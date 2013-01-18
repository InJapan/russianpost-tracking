<?php
//include the library
require_once('russianpost.lib.php');

$try_count = 0;
$TRACKNUM = '10203940006133';

a:
try
{
    $client = new RussianPostAPI();	//init the client

	$data = $client->getOperationHistory($TRACKNUM);	//fetch info
	
	foreach ($data as $t1) {
		$tmp_arr = NULL;
		foreach ($t1 as $key => $t2) {
			$tmp_arr[$key] = $t2;
		}
		$data_a[] = $tmp_arr;
	}
}
catch(RussianPostException $e) 
{
	if ($try_count < 10) {
		sleep (1);
		goto a;
	}
	else {
		echo ('Problem with connectivity: (' . $e->getMessage() . ') \n We already tried ' . $try_count . " times, but still unsuccessful :(\n");
		die();
	}
}
print_r ($data_a);

/*
RESULT OUTPUT WILL BE LIKE THIS:

Array
(
    [0] => Array
        (
            [operationType] => Приём
            [operationTypeId] => 1
            [operationAttribute] => Партионный
            [operationAttributeId] => 2
            [operationPlacePostalCode] => 102001
            [operationPlaceName] => Москва-Казанский вокзал ПЖДП-1
            [operationDate] => 2011-07-02T00:00:00.000+04:00
            [itemWeight] => 1.6
            [declaredValue] => 30.41
            [collectOnDeliveryPrice] => 30.41
            [destinationPostalCode] => 385009
            [destinationAddress] => Майкоп, Адыгея респ.
        )

    [1] => Array
        (
            [operationType] => Обработка
            [operationTypeId] => 8
            [operationAttribute] => Сортировка
            [operationAttributeId] => 0
            [operationPlacePostalCode] => 140983
            [operationPlaceName] => Московский Асц цех Посылок
            [operationDate] => 2011-07-04T02:46:00.000+04:00
            [itemWeight] => 0
            [declaredValue] => 0
            [collectOnDeliveryPrice] => 0
            [destinationPostalCode] =>
            [destinationAddress] =>
        )

    [2] => Array
        (
            [operationType] => Обработка
            [operationTypeId] => 8
            [operationAttribute] => Покинуло сортировочный центр
            [operationAttributeId] => 4
            [operationPlacePostalCode] => 385058
            [operationPlaceName] => Майкоп УОПП
            [operationDate] => 2011-07-08T00:00:00.000+04:00
            [itemWeight] => 0
            [declaredValue] => 0
            [collectOnDeliveryPrice] => 0
            [destinationPostalCode] =>
            [destinationAddress] =>
        )

    [3] => Array
        (
            [operationType] => Вручение
            [operationTypeId] => 2
            [operationAttribute] => Вручение адресату
            [operationAttributeId] => 1
            [operationPlacePostalCode] => 385009
            [operationPlaceName] => Майкоп 9
            [operationDate] => 2011-07-11T00:00:00.000+04:00
            [itemWeight] => 1.6
            [declaredValue] => 30.41
            [collectOnDeliveryPrice] => 0
            [destinationPostalCode] =>
            [destinationAddress] =>
        )

)
*/

?>
