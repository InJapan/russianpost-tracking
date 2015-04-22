<?php
require 'vendor/autoload.php';

try {
  //init the client
  $client = new RussianPostAPI();

  //fetch tracking info
  var_dump($client->getOperationHistory('42382396002056', 'RUS')); //Use 'ENG' for English

  //fetch COD payment info
  var_dump($client->getCODHistory('42382396002056', 'RUS'));
} catch(RussianPostException $e) {
  die('Something went wrong: ' . $e->getMessage() . "\n");
}