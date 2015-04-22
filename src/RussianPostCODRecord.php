<?php

/**
* One record in COD history
*/
class RussianPostCODRecord {
/**
* ID of the payment
* @var int
*/
public $paymentNumber;

/**
* Event date
* @var string
*/
public $eventDate;

/**
* Code of event type
* @var int
*/
public $eventTypeId;

/**
* Literal name of the event
* @var string
*/
public $eventName;

/**
* Postal code payment sent to
* @var string
*/
public $destinationPostalCode;

/**
* Postal code event happened in
* @var string
*/
public $eventPostalCode;

/**
* Payment amount in minor units (kopecs)
* @var int
*/
public $paymentAmount;

/**
* Destination country code
* @var string
*/
public $destinationContryCode;

/**
* Country code event happened in
* @var int
*/
public $eventCountryCode;
}