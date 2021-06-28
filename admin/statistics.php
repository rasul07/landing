<?php
	require_once "../lib/start.php";
	require_once "top.php";
	if (isAdmin()) { ?>
	<div id="statistics">
		<h1>Статистика</h1>
		<form name="statistics" action="statistics.php" method="post">
			<div>
				<label>От:</label> <input type="text" name="from" value="<?php if (isset($request["from"]))  { ?><?=$request["from"]?><?php } else { ?><?=date(FORMAT_DATE, time() - 30 * 86400)?><?php } ?>" />
			</div>
			<div>
				<label>До:</label> <input type="text" name="to" value="<?php if (isset($request["to"]))  { ?><?=$request["to"]?><?php } else { ?><?=date(FORMAT_DATE)?><?php } ?>" />
			</div>
			<div>
				<label>UTM-Source:</label> <input type="text" name="utm_source" value="<?php if (isset($request["utm_source"])) { ?><?=$request["utm_source"]?><?php } ?>" />
			</div>
			<div>
				<label>UTM-Campaign:</label> <input type="text" name="utm_campaign" value="<?php if (isset($request["utm_campaign"])) { ?><?=$request["utm_campaign"]?><?php } ?>" />
			</div>
			<div>
				<label>UTM-Content:</label> <input type="text" name="utm_content" value="<?php if (isset($request["utm_content"])) { ?><?=$request["utm_content"]?><?php } ?>" />
			</div>
			<div>
				<label>UTM-Term:</label> <input type="text" name="utm_term" value="<?php if (isset($request["utm_term"])) { ?><?=$request["utm_term"]?><?php } ?>" />
			</div>
			<div>
				<label>Split:</label> <input type="text" name="split" value="<?php if (isset($request["split"])) { ?><?=$request["split"]?><?php } ?>" />
			</div>
			<div>
				<label>И:</label> <input type="radio" name="log" value="AND" <?php if (isset($request["log"]) && $request["log"] == "AND") { ?>checked="checked"<?php } ?> />
			</div>
			<div>
				<label>ИЛИ:</label> <input type="radio" name="log" value="OR" <?php if (isset($request["log"]) && $request["log"] == "OR") { ?>checked="checked"<?php } ?> />
			</div>
			<div>
				<input type="submit" name="statistics" value="Вывести" />
			</div>
		</form>
	</div>
	
<?php require_once "data.php"; }
	else require_once "auth.php";
	require_once "footer.php";
?>