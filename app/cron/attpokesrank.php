<?php
require_once('../includes/resources/config.php');
	
$x = 1;
$sql = DB::exQuery("SELECT wild_id FROM `pokemon_wild`");
while($gegeven = $sql->fetch_assoc()) {

	$pgtop3 = DB::exQuery("SELECT pokemon_speler.*, pokemon_wild.wild_id, pokemon_wild.naam, pokemon_wild.type1, pokemon_wild.type2, gebruikers.username, SUM(`attack` + `defence` + `speed` + `spc.attack` + `spc.defence`) AS strongestpokemon FROM pokemon_speler INNER JOIN pokemon_wild ON pokemon_speler.wild_id = pokemon_wild.wild_id INNER JOIN gebruikers ON pokemon_speler.user_id = gebruikers.user_id WHERE gebruikers.banned = 'N' AND admin = '0' AND pokemon_wild.wild_id = '".$gegeven['wild_id']."' GROUP BY pokemon_speler.id ORDER BY strongestpokemon DESC, pokemon_speler.id ASC LIMIT 3");

	echo "#".$gegeven['wild_id']." Atualizado<br>";
			
	$i = 1;
	$top3 = '';

	DB::exQuery("UPDATE `pokemon_speler` SET `top3`='' WHERE `wild_id`='".$gegeven['wild_id']."' AND `top3`!=''");
	
	while($pgtop3x = $pgtop3->fetch_assoc()) {

		// ATUALIZA TOP 3 POKES
		DB::exQuery("UPDATE `pokemon_speler` SET `top3`='$i' WHERE `id`='".$pgtop3x['id']."' AND `wild_id`='".$gegeven['wild_id']."' AND `top3`=''");
		// ATUALIZA TOP 3 POKES
		echo ">#".$pgtop3x['id']." TOP ".$i."<br>";
	
		$i++;
	}

	$x++;
}

    echo "<br><br> ".$x." Pokémons atualizados";
    echo "<br><br>Cron executado com sucesso.";
  
?>