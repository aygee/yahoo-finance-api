<?php


class YahooFinance {
	private $yqlUrl  = "http://query.yahooapis.com/v1/public/yql";
	private $options = array("env" => "http://datatables.org/alltables.env",				// need this env to query yahoo finance
							);
	private $format;

	public function __construct($format='json') {
		if (isset($format)) {
			switch ($format) {
				case 'json':
					$this->options['format'] = 'json';
					break;
			}
		}
	}

	public function getHistoricalData($symbol, $startDate, $endDate) {
		if (is_object($startDate) && get_class($startDate) == 'DateTime') {
			$startDate = $this->dateToDBString($startDate);
		}
		if (is_object($endDate) && get_class($endDate) == 'DateTime') {
			$endDate = $this->dateToDBString($endDate);
		}

		$options = $this->options;
		$options['q'] = "select * from yahoo.finance.historicaldata where startDate='{$startDate}' and endDate='{$endDate}' and symbol='{$symbol}'";
		
		return $this->execQuery($options);
	}

	public function getQuotes($symbols) {
		if (is_string($symbols)) {
			$symbols = array($symbols);
		}

		$options = $this->options;
		$options['q'] = "select * from yahoo.finance.quotes where symbol in ('" . implode("','", $symbols) . "')";
		
		return $this->execQuery($options);
	}

	public function getQuotesList($symbols) {
		if (is_string($symbols)) {
			$symbols = array($symbols);
		}

		$options = $this->options;
		$options['q'] = "select * from yahoo.finance.quoteslist where symbol in ('" . implode("','", $symbols) . "')";
		
		return $this->execQuery($options);
	}

	private function execQuery($options) {
		$yql_query_url = $this->getUrl($options);
		$session = curl_init($yql_query_url);  
		curl_setopt($session, CURLOPT_RETURNTRANSFER,true);      
		return curl_exec($session);    		
	}

	private function getUrl($options) {
		$url = $this->yqlUrl;
		$i=0;
		foreach ($options as $k => $qstring) {
			if ($i==0) {
				$url .= '?';
			} else {
				$url .= '&';
			}
			$url .= "$k=" . urlencode($qstring);
			$i++;
		}
		return $url;
	}

	private function dateToDBString($date) {
		assert('is_object($date) && get_class($date) == "DateTime"');

		return $date->format('Y-m-d');
	}


		

	}

