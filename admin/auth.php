<div id="auth">
	<h1>Вход в Admin-панель</h1>
	<?php if ($message) { ?><p class="message"><?=$message?></p><?php } ?>
	<form name="auth" action="index.php" method="post">
		<div>
			<label>Логин:</label> <input type="text" name="login" />
		</div>
		<div>
			<label>Пароль:</label> <input type="password" name="password" />
		</div>
		<div>
			<input type="submit" name="auth" value="Войти" />
		</div>
	</form>
</div>