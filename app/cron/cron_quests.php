<?php
require_once('../includes/resources/config.php');

$all_quest = DB::exQuery("SELECT * FROM `daily_quest`");

$range = range(1, $all_quest->num_rows);

unset($range[($quest[0]-1)]);
unset($range[($quest[1]-1)]);

$range = array_values($range);
shuffle($range);

$q1 = $range[0];
$q2 = $range[1];

$quests->setQuest($q1);
$quests->setQuest($q2);

$quests->setPrize($q1);
$quests->setPrize($q2);

DB::exQuery("UPDATE `configs` SET `valor`='".$q1."' WHERE `id`='6'");
DB::exQuery("UPDATE `configs` SET `valor`='".$q2."' WHERE `id`='7'");

DB::exQuery("UPDATE `gebruikers` SET `streak`='0' WHERE `quest_1` = '0' OR `quest_2` = '0' OR `streak`='7'");
DB::exQuery("UPDATE `gebruikers` SET `quest_1`='0', `quest_2`='0',`quest_1_req`='0', `quest_2_req`='0'");

DB::exQuery("UPDATE `rekeningen` SET `quest_r_1`='0', `quest_r_2`='0',`quest_r_master`='0'");

echo 'Missões diárias atualizadas!';