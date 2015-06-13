<?php
/**
 * Russian Post tracking API PHP library
 * @author InJapan Corp. <max@injapan.ru>
 *
 ************************************************************************
 * You MUST request usage access for this API through request mailed to *
 * fc@russianpost.ru                                                    *
 ************************************************************************
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */

class RussianPostAPI {
  /**
   * SOAP service URL
   */
  const SOAPEndpoint = 'http://voh.russianpost.ru:8080/niips-operationhistory-web/OperationHistory';

  const SOAPUser     = 'admin';
  const SOAPPassword = 'adminadmin'; //LOL

  protected $proxyHost;
  protected $proxyPort;
  protected $proxyAuthUser;
  protected $proxyAuthPassword;

  /**
   * Constructor. Pass proxy config here.
   * @param string $proxyHost
   * @param string $proxyPort
   * @param string $proxyAuthUser
   * @param string $proxyAuthPassword
   */
  public function __construct($proxyHost = "", $proxyPort = "", $proxyAuthUser = "", $proxyAuthPassword = "") {
    $russianpostRequiredExtensions = array('SimpleXML', 'curl', 'pcre');
    foreach($russianpostRequiredExtensions as $russianpostExt) {
      if (!extension_loaded($russianpostExt)) {
        throw new RussianPostSystemException('Required extension ' . $russianpostExt . ' is missing');
      }
    }

    $this->proxyHost         = $proxyHost;
    $this->proxyPort         = $proxyPort;
    $this->proxyAuthUser     = $proxyAuthUser;
    $this->proxyAuthPassword = $proxyAuthPassword;
  }

  /**
   * Returns tracking data
   * @param string $trackingNumber tracking number
   * @param string $language language for output strings
   * @return array of RussianPostTrackingRecord
   */
  public function getOperationHistory($trackingNumber, $language = 'RUS') {
    $trackingNumber = $this->checkTrackingNumber($trackingNumber);

    $message = <<<EOD
<s:Envelope xmlns:s="http://schemas.xmlsoap.org/soap/envelope/">
   <s:Header/>
   <s:Body xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
       <OperationHistoryRequest xmlns="http://russianpost.org/operationhistory/data">
           <Barcode>$trackingNumber</Barcode>
           <MessageType>0</MessageType>
           <Language>$language</Language>
       </OperationHistoryRequest>
   </s:Body>
</s:Envelope>
EOD;

    $data = $this->makeRequest($message);
    $data = $this->parseResponse($data);

    $records = $data->OperationHistoryData->historyRecord;

    if (empty($records))
      throw new RussianPostDataException("There is no tracking data in XML response");

    $out = array();
    foreach($records as $rec) {
      $outRecord = new RussianPostTrackingRecord();
      $outRecord->operationType            = (string) $rec->OperationParameters->OperType->Name;
      $outRecord->operationTypeId          = (int) $rec->OperationParameters->OperType->Id;

      $outRecord->operationAttribute       = (string) $rec->OperationParameters->OperAttr->Name;
      $outRecord->operationAttributeId     = (int) $rec->OperationParameters->OperAttr->Id;

      $outRecord->operationPlacePostalCode = (string) $rec->AddressParameters->OperationAddress->Index;
      $outRecord->operationPlaceName       = (string) $rec->AddressParameters->OperationAddress->Description;

      $outRecord->destinationPostalCode    = (string) $rec->AddressParameters->DestinationAddress->Index;
      $outRecord->destinationAddress       = (string) $rec->AddressParameters->DestinationAddress->Description;

      $outRecord->operationDate            = (string) $rec->OperationParameters->OperDate;

      $outRecord->itemWeight               = round(floatval($rec->ItemParameters->Mass) / 1000, 3);
      $outRecord->declaredValue            = floatval($rec->FinanceParameters->Value);
      $outRecord->collectOnDeliveryPrice   = floatval($rec->FinanceParameters->Payment);

      $out[] = $outRecord;
    }

    return $out;
  }

  /**
   * Returns cash-on-delivery payment data
   * @param string $trackingNumber tracking number
   * @param string $language language for output strings
   * @return array of RussianPostCODRecord
   */
  public function getCODHistory($trackingNumber, $language = 'RUS') {
    $trackingNumber = $this->checkTrackingNumber($trackingNumber);

    $user = self::SOAPUser;
    $pass = self::SOAPPassword;

    $message = <<<EOD
<s:Envelope xmlns:s="http://schemas.xmlsoap.org/soap/envelope/" xmlns:data="http://www.russianpost.org/RTM/DataExchangeESPP/Data" xmlns:data1="http://russianpost.org/operationhistory/data">
   <s:Header>
     <data1:AuthorizationHeader s:mustUnderstand="1">
       <data1:login>$user</data1:login>
       <data1:password>$pass</data1:password>
     </data1:AuthorizationHeader>
   </s:Header>
   <s:Body xmlns:xsd="http://www.w3.org/2001/XMLSchema" xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance">
       <data:PostalOrderEventsForMailInput Barcode="$trackingNumber" Language="$language" />
   </s:Body>
</s:Envelope>
EOD;

    $data = $this->makeRequest($message);
    $data = $this->parseResponse($data);

    $records = $data->PostalOrderEventsForMaiOutput;

    if (empty($records))
      throw new RussianPostDataException("There is no COD data in XML response");

    $out = array();
    foreach($records->children() as $rec) {
      $rec = $rec->attributes();

      $outRecord = new RussianPostCODRecord();

      $outRecord->paymentNumber         = (int)    $rec->Number;
      $outRecord->eventDate             = (string) $rec->EventDateTime;
      $outRecord->eventTypeId           = (int)    $rec->EventType;
      $outRecord->eventName             = (string) $rec->EventName;
      $outRecord->destinationPostalCode = (string) $rec->IndexTo;
      $outRecord->eventPostalCode       = (string) $rec->IndexEvent;
      $outRecord->paymentAmount         = round(intval($rec->SumPaymentForward) / 100, 2);
      $outRecord->destinationContryCode = (string) $rec->CountryToCode;
      $outRecord->eventCountryCode      = (string) $rec->CountryEventCode;

      $out[] = $outRecord;
    }

    return $out;
  }

  protected function checkTrackingNumber($trackingNumber) {
    $trackingNumber = trim($trackingNumber);
    if (!preg_match('/^[0-9]{14}|[A-Z]{2}[0-9]{9}[A-Z]{2}$/', $trackingNumber)) {
      throw new RussianPostArgumentException('Incorrect format of tracking number: ' . $trackingNumber);
    }

    return $trackingNumber;
  }

  protected function parseResponse($raw) {
    $xml = @simplexml_load_string($raw);

    if (!is_object($xml))
      throw new RussianPostDataException("Failed to parse XML response");

    $ns = $xml->getNamespaces(true);

    foreach($ns as $key => $dummy) {
      if (strpos($key, 'ns') === 0) {
        $nsKey = $key;
        break;
      }
    }

    if (empty($nsKey)) {
      throw new RussianPostDataException("Failed to detect correct namespace in XML response");
    }

    if (!(
      $xml->children($ns['S'])->Body &&
      $data = $xml->children($ns['S'])->Body->children($ns[$nsKey])
    ))
      throw new RussianPostDataException("There is no tracking data in XML response");

    return $data;
  }

  protected function makeRequest($message) {
    $channel = curl_init(self::SOAPEndpoint);

    curl_setopt_array($channel, array(
      CURLOPT_POST           => true,
      CURLOPT_RETURNTRANSFER => true,
      CURLOPT_CONNECTTIMEOUT => 10,
      CURLOPT_TIMEOUT        => 10,
      CURLOPT_POSTFIELDS     => $message,
      CURLOPT_HTTPHEADER     => array(
        'Content-Type: text/xml; charset=utf-8',
        'SOAPAction: ""',
      ),
    ));

    if (!empty($this->proxyHost) && !empty($this->proxyPort)) {
      curl_setopt($channel, CURLOPT_PROXY, $this->proxyHost . ':' . $this->proxyPort);
    }

    if (!empty($this->proxyAuthUser)) {
      curl_setopt($channel, CURLOPT_PROXYUSERPWD, $this->proxyAuthUser . ':' . $this->proxyAuthPassword);
    }

    $result = curl_exec($channel);
    if ($errorCode = curl_errno($channel)) {
      throw new RussianPostChannelException(curl_error($channel), $errorCode);
    }

    return $result;
  }
}

class RussianPostException         extends Exception { }
class RussianPostArgumentException extends RussianPostException { }
class RussianPostSystemException   extends RussianPostException { }
class RussianPostChannelException  extends RussianPostException { }
class RussianPostDataException     extends RussianPostException { }