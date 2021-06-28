<?php
	$mysqli = new mysqli(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME);
	$mysqli->set_charset("utf8");
	
	function sendRequest($params, $obj, $method) {
		$request = array(
			"method" => $method,
			"params" => $params
		);
		$request = json_encode($request);
		$opts = array(
			"http" => array(
				"header" => "Content-Type: application/json; charset=utf-8\r\nAuthorization: Bearer ".DIRECT_TOKEN."\r\nAccept-Language: en\r\n",
				"method" => "POST",
				"content" => $request
			)
		);
		$context = stream_context_create($opts);
		$result = json_decode(file_get_contents("https://api.direct.yandex.com/json/v5/$obj", false, $context));
		return $result;
	}
	
	function getCampsYandexDirect($from, $to) {
		$query = "SELECT * FROM `lan_camps` WHERE `utm_source` = 'YandexDirect' AND `date` > '$from' AND `date` < '$to'";
		return getTable($query);
	}
	
	function addOrder($data) {
		return addRow("lan_orders", $data);
	}
	
	function sendOrder($phone) {
		$text = "У Вас новый заказ: $phone";
		$text = urlencode($text);
		$result = file_get_contents("https://gate.smsaero.ru/send/?user=".SMS_USER."&password=".SMS_PASSWORD."&to=".SMS_PHONE."&text=$text&from=New+order");
		return strpos($result, "accepted") !== false;
	}
	
	function addCamp($data) {
		return addRow("lan_camps", $data);
	}
	
	function isAdmin($login = false, $password = false) {
		if (!$login) $login = isset($_SESSION["login"])? $_SESSION["login"] : false;
		if (!$password) $password = isset($_SESSION["password"])? $_SESSION["password"] : false;
		return mb_strtolower($login) === mb_strtolower(ADM_LOGIN) && $password === ADM_PASSWORD;
	}
	
	function login($login, $password) {
		$password = hashSecret($password);
		if (isAdmin($login, $password)) {
			$_SESSION["login"] = $login;
			$_SESSION["password"] = $password;
			return true;
		}
		return false;
	}
	
	function logout() {
		unset($_SESSION["login"]);
		unset($_SESSION["password"]);
	}
	
	function getOrder($id) {
		if (!is_numeric($id)) exit;
		global $mysqli;
		$query = "SELECT * FROM `lan_orders` WHERE `id` = '".$mysqli->real_escape_string($id)."'";
		return getRow($query);
	}
	
	function setOrder($id, $data) {
		return setRow("lan_orders", $id, $data);
	}
	
	function deleteOrder($id) {
		return deleteRow("lan_orders", $id);
	}
	
	function getCampID($data) {
		global $mysqli;
		$query = "SELECT * FROM `lan_camps` WHERE ";
		foreach ($data as $key => $value) {
			if ($value == null) $query .= "`$key` IS NULL AND ";
			else $query .= "`$key` = '".$mysqli->real_escape_string($value)."' AND ";
		}
		$query = substr($query, 0, -5);
		return getCell($query);
	}
	
	function getCountOrders($data_st = false) {
		return getDataForOrders($data_st);
	}
	
	function getCountConfirmOrders($data_st = false) {
		return getDataForOrders($data_st, true, "date_confirm");
	}
	
	function getCountPayOrders($data_st = false) {
		return getDataForOrders($data_st, true, "date_pay");
	}
	
	function getCountCancelOrders($data_st = false) {
		return getDataForOrders($data_st, true, "date_cancel");
	}
	
	function getSummaOrders($data_st = false) {
		return getDataForOrders($data_st, false);
	}
	
	function getSummaConfirmOrders($data_st = false) {
		return getDataForOrders($data_st, false, "date_confirm");
	}
	
	function getSummaPayOrders($data_st = false) {
		return getDataForOrders($data_st, false, "date_pay");
	}
	
	function getSummaCancelOrders($data_st = false) {
		return getDataForOrders($data_st, false, "date_cancel");
	}
	
	function getDataForOrders($data_st, $count = true, $field = false) {
		if ($count) $count = "COUNT(`id`)";
		else $count = "SUM(`price`)";
		$query = "SELECT $count FROM `lan_orders`";
		$where = getWhereForOrders($data_st);
		if ($field) {
			$temp = "`$field` IS NOT NULL";
			if ($where) $where .= " AND $temp";
			else $where = $temp;
		}
		if ($where) $query .= " WHERE $where";
		$result = getCell($query);
		if (!$result) return 0;
		return $result;
	}
	
	function getOrders() {
		$query = "SELECT *, `lan_orders`.`id` as `order_id` FROM `lan_orders` INNER JOIN `lan_camps` ON `lan_camps`.`id` = `lan_orders`.`camp_id` ORDER BY `date_order` DESC";
		$result = getTable($query);
		if (!$result) return array();
		return $result;
	}
	
	function getOrdersForYandexDirect($from, $to) {
		$query = "SELECT * FROM `lan_orders` INNER JOIN `lan_camps` ON `lan_camps`.`id` = `lan_orders`.`camp_id` WHERE `utm_source` = 'YandexDirect' AND `date_pay` IS NOT NULL AND `date_order` > '$from' AND `date_order` < '$to' AND `camp_id` IS NOT NULL";
		$result = getTable($query);
		if (!$result) return array();
		return $result;
	}
	
	function getTS($date) {
		$regex = "/(\d{4})\.(\d{2}).(\d{2}) (\d{2}):(\d{2}):(\d{2})/";
		preg_match($regex, $date, $matches);
		return mktime($matches[4], $matches[5], $matches[6], $matches[2], $matches[3], $matches[1]);
	}
	
	function getWhereForOrders($data_st) {
		if (!count($data_st)) return "";
		global $mysqli;
		foreach ($data_st as $key => $value) $data_st[$key] = $mysqli->real_escape_string($value);
		$log = $data_st["log"];
		
		$where = "";
		$ft = "";
		if ($data_st["from"] || $data_st["to"]) {
			if ($data_st["from"]) {
				$ft = "`date_order` > '".$data_st["from"]."'";
			}
			if ($data_st["to"]) {
				$temp = "`date_order` < '".$data_st["to"]."'";
				if ($ft) $ft .= " AND $temp";
				else $ft = $temp;
			}
		}
		
		$where_camps = "";
		$utms = array();
		$utms["utm_source"] = $data_st["utm_source"];
		$utms["utm_campaign"] = $data_st["utm_campaign"];
		$utms["utm_content"] = $data_st["utm_content"];
		$utms["utm_term"] = $data_st["utm_term"];
		foreach ($utms as $key => $value) {
			if ($value) {
				if ($where_camps) $where_camps .= " $log `$key` = '$value'";
				else $where_camps = "`$key` = '$value'";
			}
		}
		
		$sc = "";
		
		if ($data_st["split"] || $where_camps) {
			if ($data_st["split"]) {
				$sc = "`split` = '".$data_st["split"]."'";
			}
			if ($where_camps) {
				$temp = "`camp_id` IN (SELECT `id` FROM `lan_camps` WHERE $where_camps)";
				if ($sc) $sc .= " $log $temp";
				else $sc = $temp;
			}
		}
		
		if ($ft) $where .= "($ft)";
		if ($sc) {
			if ($where) $where .= " AND ($sc)";
			else $where = $sc;
		}
		
		return $where;
	}
	
	function getCell($query) {
		global $mysqli;
		$result_set = $mysqli->query($query);
		if (is_null($result_set) || !$result_set->num_rows) return false;
		$arr = array_values($result_set->fetch_assoc());
		$result_set->close();
		return $arr[0];
	}
	
	function getRow($query) {
		global $mysqli;
		$result_set = $mysqli->query($query);
		if (is_null($result_set)) return false;
		$row = $result_set->fetch_assoc();
		$result_set->close();
		return $row;
	}
	
	function getCol($query) {
		global $mysqli;
		$result_set = $mysqli->query($query);
		if (is_null($result_set)) return false;
		$row = $result_set->fetch_assoc();
		$result_set->close();
		if ($row) return array_values($row);
		return false;
	}
	
	function getTable($query) {
		global $mysqli;
		$result_set = $mysqli->query($query);
		if (is_null($result_set)) return false;
		$result = array();
		while (($row = $result_set->fetch_assoc()) != false) {
			$result[] = $row;
		}
		$result_set->close();
		return $result;
	}
	
	function addRow($table, $data) {
		global $mysqli;
		$query = "INSERT INTO `$table` (";
		foreach ($data as $key => $value) $query .= "`$key`,";
		$query = substr($query, 0, -1);
		$query .= ") VALUES (";
		foreach ($data as $value) {
			if (is_null($value)) $query .= "null,";
			else $query .= "'".$mysqli->real_escape_string($value)."',";
		}
		$query = substr($query, 0, -1);
		$query .= ")";
		$result_set = $mysqli->query($query);
		if (!$result_set) return false;
		return $mysqli->insert_id;
	}
	
	function setRow($table, $id, $data) {
		if (!is_numeric($id)) exit;
		global $mysqli;
		$query = "UPDATE `$table` SET ";
		foreach ($data as $key => $value) {
			$query .= "`$key` = ";
			if (is_null($value)) $query .= "null,";
			else $query .= "'".$mysqli->real_escape_string($value)."',";
		}
		$query = substr($query, 0, -1);
		$query .= " WHERE `id` = '$id'";
		return $mysqli->query($query);
	}
	
	function deleteRow($table, $id) {
		if (!is_numeric($id)) exit;
		global $mysqli;
		$query = "DELETE FROM `$table` WHERE `id`='$id'";
		return $mysqli->query($query);
	}
	
	function xss($data) {
		if (is_array($data)) {
			$escaped = array();
			foreach ($data as $key => $value) {
				$escaped[$key] = xss($value);
			}
			return $escaped;
		}
		return trim(htmlspecialchars($data));
	}
	
	function redirect($link) {
		header("Location: $link");
		exit;
	}
	
	function hashSecret($str) {
		return md5($str.SECRET);
	}
	
?>