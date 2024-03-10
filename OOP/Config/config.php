<?php

define('DTO_OF_ORDER',   1);
define('LIST_OF_ORDERS', 2);

define('FIXER_CONVERT_SERVICE', [
				'SERVICE_NAME' => "Zixer",
				'API_KEY_PARAM' => "apikey",
				'API_KEY' => "6jwH2r99ml2U10eplj6o1Gl61kFh5RGa",
				'API_URL' => "https://api.apilayer.com/fixer/latest",
				'BASE_CURRENCY_PARAM' => "base",
			]);
define('OER_CONVERT_SERVICE', [
				'SERVICE_NAME' => "Open Exchange Rates",
				'API_KEY_PARAM' => "app_id",
				'API_KEY' => "32790e7f1b604b319ad03b4623502adf",
				'API_URL' => "https://openexchangerates.org/api/latest.json",
				'BASE_CURRENCY_PARAM' => "base",
			]);
define('CBRF_CONVERT_SERVICE', [
				'SERVICE_NAME' => "Central Bank of Russian Federation",
				'API_KEY_PARAM' => "",
				'API_KEY' => "",
				'API_URL' => "",
			]);
