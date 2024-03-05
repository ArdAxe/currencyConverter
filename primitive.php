<?php

class OrderDTO {
	
	public int    $orderID;
	public float  $cost;
	public string $currency;
	public float  $convertedCost;
	public string $convertedCurrency;
	public int    $convertDateTime;
	
	public function __construct(
		$orderID,
		$cost,
		$currency,
		$convertedCost,
		$convertedCurrency,
		$convertDateTime) {
			$this->orderID = $orderID;
			$this->cost = $cost;
			$this->currency = $currency;
			$this->convertedCost = $convertedCost;
			$this->convertedCurrency = $convertedCurrency;
			$this->convertDateTime = $convertDateTime;
	}
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
	$currencyRates = json_decode(file_get_contents("https://api.apilayer.com/fixer/latest?apikey=6jwH2r99ml2U10eplj6o1Gl61kFh5RGa&base=${convertTo}"), true);
	
	// массив ответа
	$result = [];
	
	// исходя из ожидаемого формата ответа
	switch($responseFormat){
		
		// в виде списка DTO заказов
		case 'DTO_OF_ORDER':
			foreach($orderList as $orderID => $orderInfo) {
				// добавление очередного заказа с приведением типов 
				$result[$orderID] = new OrderDTO(
						(int)$orderID,
						(float)$orderInfo['value'],
						(string)$orderInfo['currency'],
						round(
							($orderInfo['currency'] === $convertTo) ?
								(float)$orderInfo['value']
							: (float)$orderInfo['value'] / $currencyRates['rates'][$orderInfo['currency']], 2),
						(string)$convertTo,
						(int)$currencyRates['timestamp'],
					);
			}
			break;
		// в виде списка заказов с итоговыми суммами
		case 'LIST_OF_ORDERS':
		// по умолчанию
		default:
			foreach($orderList as $orderID => $orderInfo) {
				// добавление очередного заказа с приведением типов
				$result[] = round(
					// если валюта заказа та же, что и требуемая
					($orderInfo['currency'] == $convertTo) ?
						// преобразовать в число с плавающей точкой
						(float)$orderInfo['value']
						// иначе конвертировать по курсу и преобразовать в число с плавающей точкой
					: (float)$orderInfo['value'] / $currencyRates['rates'][$orderInfo['currency']], 2);
			}
	}
	
	return $result;
	
}

$orders = [
	123123 => ['value' => 100, 'currency' => 'USD'],
	224456 => ['value' => 2005, 'currency' => 'RUB'],
	183067 => ['value' => 221, 'currency' => 'EUR'],
];

$convertTo = 'RUB';

echo '<pre>'; var_dump(convertCurrencyInOrders($convertTo, $orders, 'DTO_OF_ORDER')  ); echo '</pre>';
echo '<pre>'; var_dump(convertCurrencyInOrders($convertTo, $orders, 'LIST_OF_ORDERS')); echo '</pre>';
