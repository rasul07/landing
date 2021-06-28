<?php
	$request = xss($_REQUEST);
	if (isset($request["message"])) $message = $request["message"];
	if (isset($request["order"])) {
		$data = array();
		$data["phone"] = $request["phone"];
		$data["date_order"] = time();
		$data["camp_id"] = (isset($_SESSION["camp_id"]) && $_SESSION["camp_id"])? $_SESSION["camp_id"] : null;
		$data["split"] = (isset($_SESSION["split"]) && $_SESSION["split"])? $_SESSION["split"] : null;
		if (addOrder($data)) {
			sendOrder($request["phone"]);
			redirect("/order.php");
		}
		else $message = "Произошла ошибка при отправке формы! Повторите попытку или обратитесь к администрации.";
	}
	elseif (isset($request["auth"])) {
		if (login($request["login"], $request["password"])) redirect("/admin");
		else $message = "Неверные имя пользователя и/или пароль!";
	}
	if (isAdmin()) {
		$data_st = array();
		if (isset($request["logout"])) {
			logout();
			redirect("/admin");
		}
		elseif (isset($request["add"])) {
			$data = array();
			$data["price"] = ($request["price"])? $request["price"] : null;
			$data["phone"] = $request["phone"];
			$data["date_order"] = time();
			if (isset($request["confirm"])) $data["date_confirm"] = time();
			if (isset($request["pay"])) $data["date_pay"] = time();
			if (isset($request["cancel"])) $data["date_cancel"] = time();
			if ($data["phone"]) {
				if (addOrder($data)) {
					$message = "Заказ успешно добавлен!";
					redirect("/admin/orders.php?message=".urlencode($message));
				}
				else $message = "Ошибка при добавлении заказа!";
			}
			else $message = "Вы не указали телефон!";
		}
		elseif (isset($request["edit"])) {
			$order = getOrder($request["id"]);
			$data = array();
			$data["price"] = ($request["price"])? $request["price"] : null;
			$data["phone"] = $request["phone"];
			if (isset($request["confirm"]) xor $order["date_confirm"]) $data["date_confirm"] = isset($request["confirm"])? time() : null;
			if (isset($request["pay"]) xor $order["date_pay"]) $data["date_pay"] = isset($request["pay"])? time() : null;
			if (isset($request["cancel"]) xor $order["date_cancel"]) $data["date_cancel"] = isset($request["cancel"])? time() : null;
			if ($data["phone"]) {
				if (setOrder($request["id"], $data)) {
					$message = "Заказ успешно отредактирован!";
					redirect("/admin/orders.php?message=".urlencode($message));
				}
				else $message = "Ошибка при редактировании заказа!";
			}	
			else $message = "Вы не указали телефон!";
		}
		elseif (isset($request["func"]) && $request["func"] == "delete") {
			if (deleteOrder($request["id"])) {
				$message = "Заказ успешно удалён!";
				redirect("/admin/orders.php?message=".urlencode($message));
			}
			else $message = "Ошибка при удалении заказа!";
		}
		elseif (isset($request["statistics"])) {
			$data_st["from"] = getTS($request["from"]);
			$data_st["to"] = getTS($request["to"]);
			$data_st["utm_source"] = $request["utm_source"];
			$data_st["utm_campaign"] = $request["utm_campaign"];
			$data_st["utm_content"] = $request["utm_content"];
			$data_st["utm_term"] = $request["utm_term"];
			$data_st["split"] = $request["split"];
			$data_st["log"] = $request["log"];
		}
	}
	
?>