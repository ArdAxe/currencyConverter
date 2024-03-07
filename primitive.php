<?php

class OrderDTO {
	
	public int    $orderID;
	public float  $cost;
	public string $currency;
	public float  $convertedCost;
	public string $convertedCurrency;
	public int    $convertDateTime;
	
	public function __construct(
		int    $orderID,
		float  $cost,
		string $currency,
		float  $convertedCost,
		string $convertedCurrency,
		int    $convertDateTime) {
			$this->orderID = $orderID;
			$this->cost = $cost;
			$this->currency = $currency;
			$this->convertedCost = $convertedCost;
			$this->convertedCurrency = $convertedCurrency;
			$this->convertDateTime = $convertDateTime;
	}
}

function modifySum($sumFrom, $currencyFrom, $currencyTo, $rate){
	
	// если сумма заказа нулевая
	if((int)$sumFrom == 0) {
		
		// оставляем сумму нулевой
		$sumNew = 0.00;
		
	// если валюта заказа и валюта конвертации совпадают
	} else if($currencyFrom === $currencyTo) {
		
		// оставляем сумму заказа неизменной
		$sumNew = (float)$sumFrom;
		
	} else {
		
		// конвертируем сумму
		$sumNew = (float)$sumFrom / $rate;
		
	}
	
	// округляем итоговую сумму до 2 знаков
	return round($sumNew, 2);
}

/** Convert currencies in orders to any single currency (Конвертация валют в заказах к одной общей)
 *  
 *  @string $convertTo - 3-letter code of currency that you need convert to (код валюты, в которую нужно конвертировать)
 *  @array $orderList - array of orders (список заказов)
 *  @string $responseFormat - response format (флаг, указывающий формат возвращзаемого значения)
 *  
 *  return
 *    @array - list of orders ids with converted sums (список заказов с конвертированными суммами)
 *    @array - list of DTO of orders with converted sums (список заказов с конвертированными суммами)
 *  
 */
function convertCurrencyInOrders($convertTo, $orderList, $responseFormat = null) {
	
	// получение курсов валют
	//$currencyRates = json_decode(file_get_contents("https://api.apilayer.com/fixer/latest?apikey=6jwH2r99ml2U10eplj6o1Gl61kFh5RGa&base=${convertTo}"), true);
	// имитация ответа сервиса конвертации
	$currencyRates = [
			'success' => true,
			'timestamp' => 1709638503,
			'base' => "RUB",
			'date' => "2024-03-05",
			'rates' => [
				'EUR' => 0.010057,
				'USD' => 0.008607,
			]
		];
	
	$result = [];
	
	foreach($orderList as $orderID => $orderInfo) {
		switch($responseFormat){
			case 'DTO_OF_ORDER':
				$result[$orderID] = new OrderDTO(
						(int)$orderID,
						(float)$orderInfo['value'],
						(string)$orderInfo['currency'],
						modifySum(
								$orderInfo['value'],
								$orderInfo['currency'],
								$convertTo,
								$currencyRates['rates'][$orderInfo['currency']]
							),
						(string)$convertTo,
						(int)$currencyRates['timestamp'],
					);
				break;
			case 'LIST_OF_ORDERS':
			default:
				$result[$orderID] = modifySum(
						$orderInfo['value'],
						$orderInfo['currency'],
						$convertTo,
						$currencyRates['rates'][$orderInfo['currency']],
					);
		}
	}
	
	return $result;
	
}




function Test_convertCurrencyInOrders() {
	
	// имитация ответа сервиса конвертации
	$currencyRates = [
			'success' => true,
			'timestamp' => 1709638503,
			'base' => "RUB",
			'date' => "2024-03-05",
			'rates' => [
				'EUR' => 0.010057,
				'USD' => 0.008607,
			]
		];
	
	// индикатор ошибки
	$errors = [];
	$test = [];
	
	// контрольные значения
	$convertTo = 'RUB';
	$test[123123] = [ 'converted_value' => 11618.45, 'currency' => "USD", 'value' => 100  ];
	$test[224456] = [ 'converted_value' => 2005.00,  'currency' => "RUB", 'value' => 2005 ];
	$test[183067] = [ 'converted_value' => 21974.74, 'currency' => "EUR", 'value' => 221  ];
	$test[183068] = [ 'converted_value' => 0.00,     'currency' => "EUR", 'value' => 0    ];
	$test[183069] = [ 'converted_value' => 0.00,     'currency' => "RUB", 'value' => 0    ];
	
	// выполнение конвертации
	$DTO_OF_ORDER   = convertCurrencyInOrders($convertTo, $test, 'DTO_OF_ORDER');
	$LIST_OF_ORDERS = convertCurrencyInOrders($convertTo, $test, 'LIST_OF_ORDERS');
	
	// определяет ли функция совпадение валют
	if(!(abs($LIST_OF_ORDERS[224456] - $test[224456]['value']) < 0.001)) {
		$errors[] = "LIST_OF_ORDERS не определяет совпадение валют"; 
	}
	
	// определяет ли функция нулевую сумму
	if(!(abs($test[183068]['converted_value'] - $DTO_OF_ORDER[183068]->convertedCost) < 0.001))
	{ $errors[] = "DTO_OF_ORDER не определяет нулевую сумму"; }
	
	// корректны ли выходные данные
	if(!(gettype($DTO_OF_ORDER) === "array"
	&&   gettype($DTO_OF_ORDER[123123]) === "object"
	&&   gettype($DTO_OF_ORDER[123123]->orderID) === "integer"
	&&   gettype($DTO_OF_ORDER[123123]->cost) === "double"
	&&   gettype($DTO_OF_ORDER[123123]->currency) === "string"
	&&   gettype($DTO_OF_ORDER[123123]->convertedCost) === "double"
	&&   gettype($DTO_OF_ORDER[123123]->convertedCurrency) === "string"
	&&   gettype($DTO_OF_ORDER[123123]->convertDateTime) === "integer"
	)) { $errors[] = "DTO_OF_ORDER неверный тип выходных данных"; }
	
	echo "Ошибок: ".count($errors);
	echo '<pre>'; print_r($errors); echo '</pre>';
	
}



$orders = [
	123123 => ['value' => 100,  'currency' => 'USD'],
	224456 => ['value' => 2005, 'currency' => 'RUB'],
	183067 => ['value' => 221,  'currency' => 'EUR'],
];

$convertTo = 'RUB';


echo '<pre>'; print_r(convertCurrencyInOrders($convertTo, $orders, 'DTO_OF_ORDER')  ); echo '</pre>';
echo '<pre>'; print_r(convertCurrencyInOrders($convertTo, $orders, 'LIST_OF_ORDERS')); echo '</pre>';

//Test_convertCurrencyInOrders();
