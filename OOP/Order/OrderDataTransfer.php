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