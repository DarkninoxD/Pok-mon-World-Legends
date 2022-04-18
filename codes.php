<?php
require_once('app/includes/resources/config.php');

$page = 'codes';
require_once('language/language-box.php');
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<link rel="shortcut icon" href="favicon.ico" type="image/x-icon" />
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title><?=$site_title;?></title>
	<link rel="stylesheet" type="text/css" href="<?=$static_url;?>/stylesheets/style.css?<?=md5(base64_encode(time()));?>" />
</head>
<body>
<table class="general orange" style="width: 800px; margin: 0 auto;">
	<thead><tr>
		<th width="150"><?=$txt['naam'];?></th>
		<th width="450"><?=$txt['code'];?></th>
		<th width="200"><?=$txt['example'];?></th>
	</tr></thead>
	<tbody>
		<tr>
			<td width="150"><b>&raquo; <?=$txt['bold'];?></b>:</td>
			<td width="450">[b]<?=$txt['example_text'];?>[/b]</td>
			<td width="200"><b><?=$txt['example_text'];?></b></td>
		</tr>
		<tr>
			<td width="150"><b>&raquo; <?=$txt['underline'];?></b>:</td>
			<td>[u]<?=$txt['example_text'];?>[/u]</td>
			<td width="200"><u><?=$txt['example_text'];?></u></td>
		</tr>
		<tr>
			<td width="150"><b>&raquo; <?=$txt['italic'];?></b>:</td>
			<td width="450">[i]<?=$txt['example_text'];?>[/i]</td>
			<td width="200"><em><?=$txt['example_text'];?></em></td>
		</tr>
		<tr>
			<td width="150"><b>&raquo; <?=$txt['marquee'];?></b>:</td>
			<td width="450">[marquee]<?=$txt['example_text'];?>[/marquee]</td>
			<td width="200"><marquee><?=$txt['example_text'];?></marquee></td>
		</tr>
		<tr>
			<td width="150"><b>&raquo; <?=$txt['center'];?></b>:</td>
			<td width="450">[center]<?=$txt['example_text'];?>[/center]</td>
			<td width="200"><center><?=$txt['example_text'];?></center></td>
		</tr>
		<tr>
			<td width="150"><b>&raquo; <?=$txt['color'];?></b>:</td>
			<td width="450">[color=#FF000]<?=$txt['example_text'];?>[/color]</td>
			<td width="200"><span style="color:#FF0000;"><?=$txt['example_text'];?></span></td>
		</tr>
		<tr>
			<td width="150"><b>&raquo; <?=$txt['player'];?></b>:</td>
			<td width="450">[player]Treinador[/player]</td>
			<td width="200"><?=$txt['no_example'];?></td>
		</tr>
<?php
if ($_GET['category'] == 'forum') {
	echo '<tr>
		<td width="150"><b>&raquo; ' . $txt['quote'] . '</b>:</td>
		<td width="450">[quote]' . $txt['example_text'] . '[/quote]</td>
		<td width="200"><div style="padding: 5px;margin: 1px 0px 1px 0px;border: 1px dotted #000000;background: #FBF9F6;">' . $txt['example_text'] . '</div></td>
	</tr>';
}
?>
<?php
if ($_GET['category'] == 'profile') {
	echo '<tr>
		<td width="150"><b>&raquo; ' . $txt['hr'] . '</b>:</td>
		<td width="450">[HR]</td>
		<td width="200"><hr /></td>
	</tr>';
}
?>
<?php
if ($_GET['category'] == 'profile' || $_GET['category'] == 'forum') {
	$num_pokes = DB::exQuery("SELECT `wild_id` FROM `pokemon_wild`")->num_rows;
	$rand1 = mt_rand(1, $num_pokes);
	$rand2 = mt_rand(1, $num_pokes);
	$rand3 = mt_rand(1, $num_pokes);
	echo '<tr>
		<td width="150"><b>&raquo; ' . $txt['image'] . '</b>:</td>
		<td width="450">[img]URL[/img]</td>
		<td width="200"><img src="http://www.google.com/intl/en_com/images/srpr/logo1w.png" width="100" height="35" /></td>
	</tr>
	<tr>
		<td width="150"><b>&raquo; ' . $txt['video'] . '</b>:</td>
		<td width="450">[youtube]https://www.youtube.com/watch?v=-PlAg8R9TG4[/youtube]</td>
		<td width="200"><object width="200" height="160">
			<param name="movie" value="https://www.youtube.com/watch?v=-PlAg8R9TG4" />
			<param name="wmode" value="transparent" />
			<embed src="https://www.youtube.com/watch?v=-PlAg8R9TG4" type="application/x-shockwave-flash" wmode="transparent" width="200" height="160" />
		</object></td>
	</tr>
	<tr>
		<td width="150"><b>&raquo; ' . $txt['poke_image'] . '</b>:</td>
		<td width="450">[pokemon]<b>' . $rand1 . '</b>[/pokemon]</td>
		<td width="200" height="96" align="center"><img src="' . $static_url . '/images/pokemon/' . $rand1 . '.gif" /></td>
	</tr>
	<tr>
		<td width="150"><b>&raquo; ' . $txt['shiny_image'] . '</b>:</td>
		<td width="450">[shiny]<b>' . $rand1 . '</b>[/shiny]</td>
		<td width="200" height="96" align="center"><img src="' . $static_url . '/images/shiny/' . $rand1 . '.gif" /></td>
	</tr>
	<tr>
		<td width="150"><b>&raquo; ' . $txt['poke_back'] . '</b>:</td>
		<td width="450">[back]<b>' . $rand2 . '</b>[/back]</td>
		<td width="200" height="96" align="center"><img src="' . $static_url . '/images/pokemon/back/' . $rand2 . '.gif" /></td>
	</tr>
	<tr>
		<td width="150"><b>&raquo; ' . $txt['shiny_back'] . '</b>:</td>
		<td width="450">[back_shiny]<b>' . $rand2 . '</b>[/back_shiny]</td>
		<td width="200" height="96" align="center"><img src="' . $static_url . '/images/shiny/back/' . $rand2 . '.gif" /></td>
	</tr>
	<tr>
		<td width="150"><b>&raquo; ' . $txt['poke_icon'] . '</b>:</td>
		<td width="450">[icon]<b>' . $rand3 . '</b>[/icon]</td>
		<td width="200" height="32" align="center"><img src="' . $static_url . '/images/pokemon/icon/' . $rand3 . '.gif" /></td>
	</tr>
	<tr>
		<td width="150"><b>&raquo; ' . $txt['shiny_icon'] . '</b>:</td>
		<td width="450">[icon_shiny]<b>' . $rand3 . '</b>[/icon_shiny]</td>
		<td width="200" height="32" align="center"><img src="' . $static_url . '/images/shiny/icon/' . $rand3 . '.gif" /></td>
	</tr>';
} 
?>
</table>
</body>
</html>