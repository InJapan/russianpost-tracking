<?php

/**
 * One record in tracking history
 */
class RussianPostTrackingRecord {
  /**
   * Operation type, e.g. Импорт, Экспорт and so on
   * @var string
   */
  public $operationType;

  /**
   * Operation type ID
   * @var int
   */
  public $operationTypeId;

  /**
   * Operation attribute, e.g. Выпущено таможней
   * @var string
   */
  public $operationAttribute;

  /**
   * Operation attribute ID
   * @var int
   */
  public $operationAttributeId;

  /**
   * ZIP code of the postal office where operation took place
   * @var string
   */
  public $operationPlacePostalCode;

  /**
   * Name of the postal office where operation took place
   * @var [type]
   */
  public $operationPlaceName;

  /**
   * Operation date in ISO 8601 format
   * @var string
   */
  public $operationDate;

  /**
   * Item wight (kg)
   * @var float
   */
  public $itemWeight;

  /**
   * Declared value of the item in rubles
   * @var float
   */
  public $declaredValue;

  /**
   * COD price of the item in rubles
   * @var float
   */
  public $collectOnDeliveryPrice;

  /**
   * Postal code of the place item addressed to
   * @var string
   */
  public $destinationPostalCode;

  /**
   * Destination address of the place item addressed to
   * @var string
   */
  public $destinationAddress;
}