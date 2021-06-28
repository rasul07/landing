<?php
	require_once "lib/start.php";
	
	$ids = array(18239099, 18239211);
	$types = array($ids[0] => "S", $ids[1] => "R");
	$from = time() - 3 * 30 * 86400;
	//$to = time() - 3 * 86400;
	$to = time() - 0 * 86400;
	
	define("ZERO_PRICE", 12);
	define("MAX_PRICE", 25);
	define("MIN_PRICE", 0.3);
	define("MIN_COUNT", 50);
	define("DIRECT_MULT", 0.1);
	
	$sc = new stdClass();
	$sc->CampaignIds = $ids;
	//$sc->States = array("ON");
	
	$params = array(
		"SelectionCriteria" => $sc,
		"FieldNames" => array("Id", "AdGroupId", "CampaignId")
	);
	
	$banners = sendRequest($params, "ads", "get");

	$banners = $banners->result->Ads;
	
	$banner_data = array();
	foreach ($banners as $banner) {
		$banner_data[$banner->Id]["count"] = 0;
		$banner_data[$banner->Id]["income"] = 0;
		$banner_data[$banner->Id]["group_id"] = $banner->AdGroupId;
		$banner_data[$banner->Id]["campaign_id"] = $banner->CampaignId;
	}
	
	$camps = getCampsYandexDirect($from, $to);

	foreach ($camps as $camp) {
		if (isset($banner_data[$camp["utm_content"]])) $banner_data[$camp["utm_content"]]["count"]++;
	}
	$orders = getOrdersForYandexDirect($from, $to);
	foreach ($orders as $order) {
		if (isset($banner_data[$order["utm_content"]])) $banner_data[$order["utm_content"]]["income"] += $order["price"];
	}
	
	$bids = array();
	
	foreach ($banner_data as $id => $value) {
		$count = $value["count"];
		$income = $value["income"];
		$bid = new stdClass();
		$bid->AdGroupId = $value["group_id"];
		$summa = 0;
		if ($count < MIN_COUNT) $summa = ZERO_PRICE;
		else {
			$summa = ($income / $count) * DIRECT_MULT;
			if ($summa > MAX_PRICE) $summa = MAX_PRICE;
			elseif ($summa < MIN_PRICE) $summa = MIN_PRICE;
		}
		$summa *= 1000000;
		if ($types[$value["campaign_id"]] == "S") $bid->Bid = $summa;
		else $bid->ContextBid = $summa;
		$bids[] = $bid;
	}

	$params = array(
		"Bids" => $bids
	);
	$result = sendRequest($params, "bids", "set");
	print_r($result);
?>