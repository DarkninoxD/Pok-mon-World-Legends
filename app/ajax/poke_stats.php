<?php
$max = 6;
$total_pokes = DB::exQuery("SELECT `wild_id` FROM `pokemon_wild`")->num_rows;
$total_pages = ceil($total_pokes/$max);
$_POST['start'] = (!is_numeric($_POST['start']) || $_POST['start'] <= 0) ? 1 : $_POST['start'];
$_POST['start'] = ($_POST['start'] > $total_pages) ? $total_pages : $_POST['start'];
$start = ceil(($_POST['start'] * $max) - $max);
// $sql = "SELECT `pw`.`wild_id`,`pw`.`naam`,(SELECT COUNT(`id`) FROM `pokemon_speler` WHERE `wild_id`=`pw`.`wild_id`) AS `count` FROM `pokemon_wild` AS `pw` ORDER BY `count` DESC,`pw`.`wild_id` ASC LIMIT " . $start . ", " . $max;
// $sql = "SELECT `pw`.`wild_id`,`pw`.`naam` FROM `pokemon_wild` AS `pw` ORDER BY `pw`.`wild_id` ASC LIMIT " . $start . ", " . $max;

$sql = "SELECT COUNT(`id`) AS `total`,`wild_id` FROM `pokemon_speler` GROUP BY `wild_id` ORDER BY `total` DESC LIMIT {$start},{$max}";
$records = query_cache($_GET['act'] . '-' . $_POST['start'], $sql, 600);
if (count($records) <= 0)	echo "<tr><td colspan=\"4\">Nenhum resultado</td></tr>";
else {
	echo "<tr>";
		$i = 1;
		foreach($records as $key => $value) {
			$info = DB::exQuery("SELECT `naam` FROM `pokemon_wild` WHERE `wild_id` = '{$value['wild_id']}'")->fetch_assoc();
			echo "<td style=\"padding:0; width:10px;\"><img src=\"" . $static_url . "/images/pokemon/icon/" . $value['wild_id'] . ".gif\" /></td>";
			echo "<td><a href=\"./pokedex&poke=" . $value['wild_id'] . "\">" . $info['naam'] . "</a><br />" . highamount($value['total']) . " capturados</td>";
			echo ($i%2 == 0) ? "</tr><tr>" : "";
			++$i;
		}
	echo "</tr>";
}
?>