<?php

class OrderCurrencyConvert {
	
	private array $currencyRates = [];
	private array $convertedOrders = [];
	
	private string $convertTo = "";
	private int $outputDataFormat;
	
	public function getConvertedOrders() {
		
		return $this->convertedOrders;
		
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
	public function convertCurrencyInOrders($orders, $convertTo, $convertService = FIXER_CONVERT_SERVICE, $outputDataFormat = 0) {
		
		// фиксируем параметры конвертации
		$this->convertTo = $convertTo;
		$this->outputDataFormat = $outputDataFormat;
		
		// получаем курсы валют
		$this->getCurrencyRates($convertService);
		
		// конвертируем валюты заказов
		$this->convertOrders($orders);
		
	}
	
	// получение курсов валют
	private function getCurrencyRates($convertService) {
		/*
		$this->currencyRates = json_decode(
			file_get_contents(
				$convertService['API_URL']."?"
				.$convertService['API_KEY_PARAM']."=".$convertService['API_KEY']
				.( $convertService['BASE_CURRENCY_PARAM'] ? "&".$convertService['BASE_CURRENCY_PARAM']."=".$this->convertTo : "" )
			), true);
		
		// если сервер ответил отказом
		if(!$this->currencyRates['success']) {
			throw new Exception("Не удалось получить курсы валют от серсвиса ${convertService['SERVICE_NAME']}". PHP_EOL . !$this->currencyRates['message']);
		} else {
			// очищаем ненужные данные
			unset($this->currencyRates['success']);
			unset($this->currencyRates['date']);
		}
		*/
		
		// 
		$this->currencyRates = [
				'timestamp' => 1709638503,
				'base' => "RUB",
				'rates' => [
					'EUR' => 0.010057,
					'USD' => 0.008607,
				]
			];
		
	}
	
	private function convertOrders($orderList) {
		
		foreach($orderList as $orderID => $orderInfo) {
			
			$this->convertedOrders[$orderID] = $this->makeOrderToConvertedList($orderID, $orderInfo);
			
		}
		
	}
	
	private function makeOrderToConvertedList($orderID, $orderInfo) {
		
		switch($this->outputDataFormat){
			case 1:
				return new OrderDTO(
						(int)$orderID,
						(float)$orderInfo['value'],
						(string)$orderInfo['currency'],
						$this->modifySum( $orderInfo['value'], $orderInfo['currency'], $this->currencyRates['rates'][$orderInfo['currency']] ),
						(string)$this->convertTo,
						(int)$this->currencyRates['timestamp'],
					);
			case 2:
			default:
				return $this->modifySum( $orderInfo['value'], $orderInfo['currency'], $this->currencyRates['rates'][$orderInfo['currency']] );
				
		}
		
	}
	
	private function modifySum($sumFrom, $currencyFrom, $rate) {
		
		// если сумма заказа нулевая
		if((int)$sumFrom == 0) {
			
			// оставляем сумму нулевой
			$sumNew = 0.00;
			
		// если валюта заказа и валюта конвертации совпадают
		} else if($currencyFrom === $this->convertTo) {
			
			// оставляем сумму заказа неизменной
			$sumNew = (float)$sumFrom;
			
		} else {
			
			// конвертируем сумму
			$sumNew = (float)$sumFrom / $rate;
			
		}
		
		// округляем итоговую сумму до 2 знаков
		return round($sumNew, 2);
		
	}
	
}