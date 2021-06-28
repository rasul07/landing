<!DOCTYPE html>
<html>
<head>
	<title>Admin-панель</title>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<meta name="description" content="Admin-панель." />
	<meta name="keywords" content="admin панель, управление сайтом, управление лендингом" />
	<link type="text/css" rel="stylesheet" href="/styles/admin.css" />
</head>
<body>
<?php if (isAdmin()) { ?>
	<div id="logout">
		<a href="/admin?logout=1">Выход</a>
	</div>
	<div id="menu">
		<a href="/admin">Главная</a>
		<a href="/admin/orders.php">Заказы</a>
		<a href="/admin/statistics.php">Статистика</a>
	</div>
<?php } ?>