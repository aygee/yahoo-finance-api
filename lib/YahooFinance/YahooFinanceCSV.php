<?php

class YahooFinanceCSV {
	private $url = "http://ichart.finance.yahoo.com/table.csv";

	/* getCSV
	 * @params $symbol stock symbol, eg: asx.ax, bkl.ax
	 * @params $startdate optional start date. If not specified, it will return result from the earliest possible record
	 * @params $enddate optional end date. If not specified, it will return result up to the latest possible record
	 * @params $freq optional frequency. Possible values: 'd' for daily, 'm' for monthly, 'y' for yearly. Defaulted to 'd'
	 *
	 * @return csv
	 */
	public function getQuotesCSV($symbol, $startdate=NULL, $enddate=NULL, $freq='d') {
		$url = $this->url . "?s={$symbol}";
		
		if (is_string($startdate) && !empty($startdate)) {
			$startdate = new DateTime($startdate);
			$url .= "&a=" . ($startdate->format('n')-1); // start month -1
			$url .= "&b=" . $startdate->format('j');     // start day
			$url .= "&c=" . $startdate->format('y');     // start year
		}

		if (is_string($enddate) && !empty($enddate)) {
			$enddate = new DateTime($enddate);
			$url .= "&d=" . ($enddate->format('n')-1);   // end month - 1 
			$url .= "&e=" . $enddate->format('j');       // end day
			$url .= "&f=" . $enddate->format('y');       // end year
		}

		$url .= "&g=" . $freq;
		return $this->run($url);
	}

	private function run($url){
		$handle = curl_init($url);
		curl_setopt($handle, CURLOPT_RETURNTRANSFER,true);
		$response = curl_exec($handle);

		$httpCode = curl_getinfo($handle, CURLINFO_HTTP_CODE);
		if ($httpCode == 404) {
			return false;
		} else {
			curl_close($handle);
			return $response;
		}
	}
}
