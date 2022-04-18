<?php
set_time_limit(0);
ini_set('memory_limit', '-1');
ob_start();
require_once('../includes/resources/config.php');

DB::exQuery("UPDATE `gebruikers` SET `rang` = 0, `rang_temp` = 0");

$rang = 1;
$result = DB::exQuery("SELECT `user_id`, `points`, `rang` FROM `gebruikers` WHERE `points` != 0 AND `admin` = 0 AND `banned` = 'N' ORDER BY `points` DESC");
while ($row = $result->fetch_assoc()) {
	$contagem = DB::exQuery("SELECT `id` from `pokemon_speler` where `user_id` = '{$row['user_id']}'")->num_rows;
	echo $rang.': '.$contagem.'<br>';
	//if ($rang != $row['rang'])
		DB::exQuery("UPDATE `gebruikers` SET `rang` = '{$rang}', `aantalpokemon` = '{contagem}' WHERE `user_id` = '{$row['user_id']}'");
	++$rang;
}

$rang_temp = 1;
$result = DB::exQuery("SELECT `user_id`, `points_temp`, `rang_temp` FROM `gebruikers` WHERE `points_temp` != 0 AND `admin` = 0 AND `banned` = 'N' ORDER BY `points_temp` DESC");
while ($row = $result->fetch_assoc()) {
	$contagem = DB::exQuery("SELECT `id` from `pokemon_speler` where `user_id` = '{$row['user_id']}'")->num_rows;
	//if ($rang_temp != $row['rang_temp'])
		DB::exQuery("UPDATE `gebruikers` SET `rang_temp` = '{$rang_temp}', `aantalpokemon` = '{contagem}' WHERE `user_id` = '{$row['user_id']}'");
	++$rang_temp;
}

echo 'Rankings atualizados!';

ob_flush();