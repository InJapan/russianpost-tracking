<?php
//include the library
require_once('russianpost.lib.php');

//init the client
$client = new RussianPostAPI();

//fetch info
var_dump($client->getOperationHistory('EE004107342DE'));