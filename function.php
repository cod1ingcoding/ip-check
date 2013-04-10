<?php

function ip_check($ip) {
	$need = ip2long($ip);

	$strIPs = file_get_contents('ip_list.txt');
	$arrRules = explode(',', $strIPs);
	$arrIPs = array();

	foreach ($arrRules as $k => $v) {
		$arr = explode('-', $v);
		if (count($arr) == 1) $arr[1] = $arr[0];

		$arrIPs[] = $arr[0];
		$arrIPs[] = $arr[1];
	}

	foreach($arrIPs as $k => $ip) {
		$arrIPs[$k] = ip2long($ip);
	}
	sort($arrIPs);

	$key = in_scope($arrIPs, $need);

	return $key;
}

function in_scope(&$arrIPs, $need){
	$begin = 0;
	$end = count($arrIPs) - 1;
	$key =  false;

	while($begin <= $end) {
		$mid = floor(($begin + $end) / 2);
		// echo $mid, "\n";
		if ($arrIPs[$mid] > $need) {
			$end = $mid - 1;
		} else if ($arrIPs[$mid] < $need) {
			$begin = $mid + 1;
		} else {
			$key = $mid;
			break;
		}
	}

	if ($key !== false) {
		return true;
	} else {
		$nearkey = $mid;
		if ($need > $arrIPs[$nearkey]) {
			$nearbegin = $nearkey + $nearkey % 2;
			$nearend = $nearbegin + 1;
		} else {
			$nearend = $nearkey - (1 - $nearkey % 2);
			$nearbegin = $nearend - 1;
		}
		// echo $arrIPs[$nearbegin], "\n" . $need . "\n", $arrIPs[$nearend], "\n";
		// echo long2ip($arrIPs[$nearbegin]), "\n" . long2ip($need) . "\n", long2ip($arrIPs[$nearend]), "\n";
		if ($nearbegin < 0 || $nearend > count($arrIPs)) {
			return false;
		}

		if (!($need > $arrIPs[$nearbegin] && $need < $arrIPs[$nearend])) {
			return false;
		}

		return true;
	}
}