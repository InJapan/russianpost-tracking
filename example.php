<?php
//include the library
require_once('russianpost.lib.php');

try {
  //init the client
  $client = new RussianPostAPI();

  //fetch info
  var_dump($client->getOperationHistory('EE004107342DE', 'RUS')); //Use 'ENG' for English
} catch(RussianPostException $e) {
  die('Something went wrong: ' . $e->getMessage() . "\n");
}