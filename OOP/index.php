<?php


require_once 'Config\config.php';
require_once 'Order\OrderDataTransfer.php';
require_once 'Order\OrderCurrencyConvert.php';
require_once 'Test\TestConvertCurrencyInOrders.php';







/* ВХОДНЫЕ ДАННЫЕ { */

// входные данные

$convertTo = "RUB";
$orders = [
	123123 => ['value' => 100,  'currency' => 'USD'],
	224456 => ['value' => 2005, 'currency' => 'RUB'],
	183067 => ['value' => 221,  'currency' => 'EUR'],
];

/* ВХОДНЫЕ ДАННЫЕ } */



/* ОБРАБОТКА { */

$responseDTO = new OrderCurrencyConvert();
$responseLIST = new OrderCurrencyConvert();

$responseDTO->convertCurrencyInOrders($orders, $convertTo, FIXER_CONVERT_SERVICE, DTO_OF_ORDER);
$responseLIST->convertCurrencyInOrders($orders, $convertTo, FIXER_CONVERT_SERVICE, LIST_OF_ORDERS);

echo '<pre>'; print_r($responseDTO->getConvertedOrders()); echo '</pre>';
echo '<pre>'; print_r($responseLIST->getConvertedOrders()); echo '</pre>';

/* ОБРАБОТКА } */




/* ТЕСТРОВАНИЕ { */

$Test_ConvertCurrencyInOrders = new TestConvertCurrencyInOrders();

$Test_ConvertCurrencyInOrders->makeTest();
$Test_ConvertCurrencyInOrders->showErrors();


/* ТЕСТРОВАНИЕ } */