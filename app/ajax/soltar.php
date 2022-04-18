<?php
$count = 0;

$arr = explode(",", $_GET['id']);

foreach ($arr as $id) {

  if (is_numeric($id)) {
  
  
  
  $update = DB::exQuery("SELECT id, wild_id, user_id, gehecht, gevongenmet FROM pokemon_speler WHERE id = '".$id."'")->fetch_assoc();

	
	if ($update['user_id'] != $_SESSION['id']) {

    	

	}else if ($update['gehecht'] == 1) {

	

	}else{

		 DB::exQuery("UPDATE gebruikers_item SET `".$update['gevongenmet']."`=`".$update['gevongenmet']."`+'1' WHERE `user_id`='".$_SESSION['id']."'");

	  	 if (DB::exQuery("SELECT id FROM pokemon_speler WHERE wild_id='".$update['wild_id']."'")->num_rows == 1) update_pokedex($pokemon['wild_id'],'','release');

		
		$select1 = DB::exQuery("SELECT `id`,`opzak_nummer` FROM `pokemon_speler` WHERE `user_id`='" . $_SESSION['id'] . "' AND `id`!='" . $id . "' AND `opzak`='ja' ORDER BY `opzak_nummer` ASC");
		for($i=1;$select=$select1->fetch_assoc();++$i) {
			#Alle opzak_nummers ééntje lager maken van alle pokemons die over blijven
			DB::exQuery("UPDATE `pokemon_speler` SET `opzak_nummer`='" . $i . "' WHERE `id`='" . $select['id'] . "' LIMIT 1");
		}
		
		$date = date("Y-m-d H:i:s");
		DB::exQuery("INSERT INTO release_log (date, user_id, poke_id, wild_id, pokeball) VALUES (NOW(), '".$_SESSION['id']."', '".$update['id']."', '".$update['wild_id']."', '".$update['gevongenmet']."')");
		

		//DB::exQuery("DELETE FROM pokemon_speler WHERE id = '".$id."'");
		DB::exQuery("UPDATE pokemon_speler SET user_id = '0', release_user = '".$_SESSION['id']."', release_date = NOW() WHERE id = '".$id."'");

		DB::exQuery("DELETE FROM transferlijst WHERE id = '".$id."'");

		DB::exQuery("UPDATE `gebruikers` SET `aantalpokemon`=`aantalpokemon`-'1' WHERE `user_id` = '".$_SESSION['id']."'");
 		

		
	$count++;

	}
	
	
	

  }

}

if ($count == 0) echo "fail";

else echo "succes";

?>