<?php
	require_once "../lib/start.php";
	require_once "top.php";
	if (isAdmin()) { $orders = getOrders();
	if (isset($request["func"]) && $request["func"] == "edit") {
		$fd = getOrder($request["id"]);
	}
	else $fd = $request; ?>
<div id="form_order">
	<h1><?php if (isset($fd["date_order"])) { ?>Редактировать<?php } else { ?>Добавить<?php } ?> заказ</h1>
	<form name="form_order" action="orders.php<?php if (isset($fd["date_order"])) { ?>?func=edit&amp;id=<?=$fd["id"]?><?php } ?>" method="post">
		<div>
			<label>Цена:</label> <input type="text" name="price" value="<?php if (isset($fd["price"]) && $fd["price"]) { ?><?=$fd["price"]?><?php } ?>" />
		</div>
		<div>
			<label>Телефон:</label> <input type="text" name="phone" value="<?php if (isset($fd["phone"]) && $fd["phone"]) { ?><?=$fd["phone"]?><?php } ?>" />
		</div>
		<div>
			<label>Подтверждён:</label> <input type="checkbox" name="confirm" <?php if (isset($fd["confirm"]) || isset($fd["date_confirm"])) { ?>checked="checked"<?php }  ?> />
		</div>
		<div>
			<label>Оплачен:</label> <input type="checkbox" name="pay" <?php if (isset($fd["pay"]) || isset($fd["date_pay"])) { ?>checked="checked"<?php }  ?> />
		</div>
		<div>
			<label>Аннулирован:</label> <input type="checkbox" name="cancel" <?php if (isset($fd["cancel"]) || isset($fd["date_cancel"])) { ?>checked="checked"<?php }  ?> />
		</div>
		<div>
			<input type="hidden" name="id" value="<?php if (isset($fd["id"])) { ?><?=$fd["id"]?><?php } ?>" />
			<input type="submit" name="<?php if (isset($fd["date_order"])) { ?>edit<?php } else { ?>add<?php } ?>" value="<?php if (isset($fd["date_order"])) { ?>Редактировать<?php } else { ?>Добавить<?php } ?>" />
		</div>
	</form>
</div>
<h1>Заказы</h1>
<?php if ($message) { ?><p class="message"><?=$message?></p><?php } ?>
<table>
	<tr>
		<td>ID</td>
		<td>Цена</td>
		<td>Телефон</td>
		<td>Дата заказа</td>
		<td>Дата подтверждения</td>
		<td>Дата оплаты</td>
		<td>Дата аннулирования</td>
		<td>Кампания</td>
		<td>Функции</td>
	</tr>
	<?php foreach ($orders as $order) { ?>
		<tr>
			<td><?=$order["order_id"]?></td>
			<td><?php if ($order["price"]) { ?><?=$order["price"]?> рублей<?php } else { ?>Нет<?php } ?></td>
			<td><?=$order["phone"]?></td>
			<td><?php if ($order["date_order"]) { ?><?=date(FORMAT_DATE, $order["date_order"])?><?php } else { ?>Нет<?php } ?></td>
			<td><?php if ($order["date_confirm"]) { ?><?=date(FORMAT_DATE, $order["date_confirm"])?><?php } else { ?>Нет<?php } ?></td>
			<td><?php if ($order["date_pay"]) { ?><?=date(FORMAT_DATE, $order["date_pay"])?><?php } else { ?>Нет<?php } ?></td>
			<td><?php if ($order["date_cancel"]) { ?><?=date(FORMAT_DATE, $order["date_cancel"])?><?php } else { ?>Нет<?php } ?></td>
			<td>
				<p>UTM Source: <?=$order["utm_source"]?></p>
				<p>UTM Campaign: <?=$order["utm_campaign"]?></p>
				<p>UTM Content: <?=$order["utm_content"]?></p>
				<p>UTM Term: <?=$order["utm_term"]?></p>
				<p>Split: <?=$order["split"]?></p>
			</td>
			<td>
				<a href="/admin/orders.php?func=edit&amp;id=<?=$order["order_id"]?>">Редактировать</a>
				<br />
				<a href="/admin/orders.php?func=delete&amp;id=<?=$order["order_id"]?>">Удалить</a>
			</td>
		</tr>
	<?php } ?>
</table>
<?php } else require_once "auth.php"; ?>
<?php
	require_once "footer.php";
?>