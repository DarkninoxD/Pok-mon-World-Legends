<?php
require_once('../includes/resources/config.php');

$traders_sql = DB::exQuery("SELECT * FROM `traders`");
while($trader = $traders_sql->fetch_assoc()) {
	if (empty($trader['wil'])) {
		#Willekeurige pokemon laden
		$query = DB::exQuery("SELECT naam, zeldzaamheid FROM pokemon_wild WHERE evolutie = '1' AND `zeldzaamheid` <= '5' AND aparece='sim' AND comerciantes='sim' ORDER BY rand() limit 1")->fetch_assoc();
		$wil = DB::exQuery("SELECT naam FROM pokemon_wild WHERE zeldzaamheid = '".$query['zeldzaamheid']."' AND naam != '".$query['naam']."' AND evolutie = '1' AND aparece='sim' AND comerciantes='sim' ORDER BY rand() limit 1")->fetch_assoc();

		#De willekeurige pokemon in de traders database zetten
		DB::exQuery("UPDATE `traders` SET `naam`='{$query['naam']}',`wil`='{$wil['naam']}' WHERE `eigenaar`='{$trader['eigenaar']}' LIMIT 1");
	}
}
#Tijd opslaan van wanneer deze file is uitevoerd
$tijd = date("Y-m-d H:i:s");
DB::exQuery("UPDATE `cron` SET `tijd`='{$tijd}' WHERE `soort`='trader'");
?>