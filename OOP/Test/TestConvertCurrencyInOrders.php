<?php

class TestConvertCurrencyInOrders {
	
	private array $errors = [];
	private array $controlData = [];
	private OrderCurrencyConvert $DTO_OF_ORDER;
	private OrderCurrencyConvert $LIST_OF_ORDERS;
	
	public function __construct() {
		
		$this->controlData['convertTo'] = 'RUB';
		$this->controlData['orders'] = [
				123123 => [ 'currency' => "USD", 'value' => 100,  'converted_value' => 11618.45, ],
				224456 => [ 'currency' => "RUB", 'value' => 2005, 'converted_value' => 2005.00,  ],
				183067 => [ 'currency' => "EUR", 'value' => 221,  'converted_value' => 21974.74, ],
				183068 => [ 'currency' => "EUR", 'value' => 0,    'converted_value' => 0.00,     ],
				183069 => [ 'currency' => "RUB", 'value' => 0,    'converted_value' => 0.00,     ],
			];
		$this->controlData['currencyRates'] = [
				'success' => true,
				'timestamp' => 1709638503,
				'base' => $convertTo,
				'date' => "2024-03-05",
				'rates' => ['EUR' => 0.010057, 'USD' => 0.008607,],
			];
		
		$this->DTO_OF_ORDER   = new OrderCurrencyConvert();
		$this->LIST_OF_ORDERS = new OrderCurrencyConvert();
		
	}
	
	public function makeTest() {
		
		// выполнение конвертации
		
		$this->DTO_OF_ORDER
		     ->convertCurrencyInOrders($this->controlData['orders'], $this->controlData['convertTo'], FIXER_CONVERT_SERVICE, DTO_OF_ORDER);
		
		$this->LIST_OF_ORDERS
		     ->convertCurrencyInOrders($this->controlData['orders'], $this->controlData['convertTo'], FIXER_CONVERT_SERVICE, LIST_OF_ORDERS);
		
		$this->checkOutputFormat();
		$this->checkMatchedInputOutputCurrencies();
		$this->checkNullenOrderSum();
		
	}
	
	public function showErrors() {
		
		echo "Ошибок: ".count($this->errors);
		echo '<pre>'; print_r($this->errors); echo '</pre>';
		
	}
	
	private function isFloatAreMatched($floatOne, $floatTwo, $accuracy) {
		
		return !(abs($floatOne - $floatTwo) < $accuracy);
		
	}
	
	private function checkOutputFormat() {
		
		// корректны ли выходные данные
		if(!(gettype($this->DTO_OF_ORDER->getConvertedOrders()) === "array"
		&&   gettype($this->DTO_OF_ORDER->getConvertedOrders()[array_key_first($this->DTO_OF_ORDER->getConvertedOrders())]) === "object"
		&&   gettype($this->DTO_OF_ORDER->getConvertedOrders()[array_key_first($this->DTO_OF_ORDER->getConvertedOrders())]->orderID) === "integer"
		&&   gettype($this->DTO_OF_ORDER->getConvertedOrders()[array_key_first($this->DTO_OF_ORDER->getConvertedOrders())]->cost) === "double"
		&&   gettype($this->DTO_OF_ORDER->getConvertedOrders()[array_key_first($this->DTO_OF_ORDER->getConvertedOrders())]->currency) === "string"
		&&   gettype($this->DTO_OF_ORDER->getConvertedOrders()[array_key_first($this->DTO_OF_ORDER->getConvertedOrders())]->convertedCost) === "double"
		&&   gettype($this->DTO_OF_ORDER->getConvertedOrders()[array_key_first($this->DTO_OF_ORDER->getConvertedOrders())]->convertedCurrency) === "string"
		&&   gettype($this->DTO_OF_ORDER->getConvertedOrders()[array_key_first($this->DTO_OF_ORDER->getConvertedOrders())]->convertDateTime) === "integer"
		)) { $this->catchedError("DTO_OF_ORDER неверный тип выходных данных"); }
		
		// корректны ли выходные данные
		if(!(gettype($this->LIST_OF_ORDERS->getConvertedOrders()) === "array"
		&&   gettype($this->LIST_OF_ORDERS->getConvertedOrders()[array_key_first($this->LIST_OF_ORDERS->getConvertedOrders())]) === "double"
		)) { $this->catchedError("LIST_OF_ORDERS неверный тип выходных данных"); }
		
	}
	
	private function checkMatchedInputOutputCurrencies() {
		
		if($this->isFloatAreMatched( $this->controlData['orders'][224456]['value'], $this->LIST_OF_ORDERS->getConvertedOrders()[224456], 0.01)) {
			$this->catchedError("LIST_OF_ORDERS не определяет совпадение валют");
		}
		
		if($this->isFloatAreMatched( $this->controlData['orders'][224456]['value'], $this->DTO_OF_ORDER->getConvertedOrders()[224456]->convertedCost, 0.01)) {
			$this->catchedError("DTO_OF_ORDER не определяет совпадение валют");
		}
		
	}
	
	private function checkNullenOrderSum() {
		
		if($this->isFloatAreMatched($this->controlData['orders'][183068]['converted_value'], $this->LIST_OF_ORDERS->getConvertedOrders()[183068], 0.01))
		{ $this->catchedError("LIST_OF_ORDERS не определяет нулевую сумму"); }
		
		if($this->isFloatAreMatched($this->controlData['orders'][183068]['converted_value'], $this->DTO_OF_ORDER->getConvertedOrders()[183068]->convertedCost, 0.01))
		{ $this->catchedError("DTO_OF_ORDER не определяет нулевую сумму"); }
		
	}
	
	private function catchedError(string $description) {
		
		$this->errors[] = $description;
		
	}
	
}