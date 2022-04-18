<?php
require_once('../includes/resources/config.php');

// DB::exQuery("UPDATE markt SET beschikbaar = '0' WHERE soort = 'pokemon'");

 $sql = DB::exQuery("SELECT markt.id, pokemon_wild.wereld
					FROM markt
					INNER JOIN pokemon_wild
					ON markt.pokemonid = pokemon_wild.wild_id
					WHERE markt.soort = 'pokemon' 
					AND markt.beschikbaar = '0'");
 while($select = $sql->fetch_assoc()) {
  $newinfo = DB::exQuery("SELECT wild_id, naam, type1, zeldzaamheid FROM pokemon_wild WHERE wereld = '".$select['wereld']."' AND evolutie = '1' AND aparece='sim' AND `egg`='1' AND zeldzaamheid <= 5 ORDER BY rand() LIMIT 1")->fetch_assoc();

	if ($newinfo['zeldzaamheid'] == 1) {
		$silver_price = rand(1250, 3500);
		$gold_price = 0;
		$omschrijving_nl = 'Um ovo de Pokémon Comum. Este é um Pokémon tipo '.$newinfo['type1'].'.';
		$omschrijving_en = 'Um ovo de Pokémon Comum. Este é um Pokémon tipo '.$newinfo['type1'].'.';
		$omschrijving_es = 'Um ovo de Pokémon Comum. Este é um Pokémon tipo '.$newinfo['type1'].'.';
		$omschrijving_de = 'Um ovo de Pokémon Comum. Este é um Pokémon tipo '.$newinfo['type1'].'.';
		$omschrijving_pl = 'Um ovo de Pokémon Comum. Este é um Pokémon tipo '.$newinfo['type1'].'.';
		$omschrijving_pt = 'Um ovo de Pokémon Comum. Este é um Pokémon tipo '.$newinfo['type1'].'.';
	}
	else if ($newinfo['zeldzaamheid'] == 2) {
		$silver_price = rand(4000, 7300);
		$gold_price = 0;
		$omschrijving_nl = 'Um ovo de Pokémon Incomum. Aparenta ser um Pokémon tipo '.$newinfo['type1'].'.';
		$omschrijving_en = 'Um ovo de Pokémon Incomum. Aparenta ser um Pokémon tipo '.$newinfo['type1'].'.';
		$omschrijving_es = 'Um ovo de Pokémon Incomum. Aparenta ser um Pokémon tipo '.$newinfo['type1'].'.';
		$omschrijving_de = 'Um ovo de Pokémon Incomum. Aparenta ser um Pokémon tipo '.$newinfo['type1'].'.';
		$omschrijving_pl = 'Um ovo de Pokémon Incomum. Aparenta ser um Pokémon tipo '.$newinfo['type1'].'.';
		$omschrijving_pt = 'Um ovo de Pokémon Incomum. Aparenta ser um Pokémon tipo '.$newinfo['type1'].'.';
	}
	else if ($newinfo['zeldzaamheid'] == 3) {
		$silver_price = rand(7500, 11000);
		$gold_price = 0;
		$omschrijving_nl = 'Um ovo de Pokémon Raro. Tem altas chances de ser um Pokémon tipo '.$newinfo['type1'].'.';
		$omschrijving_en = 'Um ovo de Pokémon Raro. Tem altas chances de ser um Pokémon tipo '.$newinfo['type1'].'.';
		$omschrijving_es = 'Um ovo de Pokémon Raro. Tem altas chances de ser um Pokémon tipo '.$newinfo['type1'].'.';
		$omschrijving_de = 'Um ovo de Pokémon Raro. Tem altas chances de ser um Pokémon tipo '.$newinfo['type1'].'.';
		$omschrijving_pl = 'Um ovo de Pokémon Raro. Tem altas chances de ser um Pokémon tipo '.$newinfo['type1'].'.';
		$omschrijving_pt = 'Um ovo de Pokémon Raro. Tem altas chances de ser um Pokémon tipo '.$newinfo['type1'].'.';
	}
	else{
		$silver_price = 0;
		$gold_price = rand(200, 423);
		$omschrijving_nl = 'Um ovo de Pokémon Lendário ou será que é de um Inicial? Cientistas acham que é um ovo de Pokémon tipo '.$newinfo['type1'].'.';
		$omschrijving_en = 'Um ovo de Pokémon Lendário ou será que é de um Inicial? Cientistas acham que é um ovo de Pokémon tipo '.$newinfo['type1'].'.';
		$omschrijving_es = 'Um ovo de Pokémon Lendário ou será que é de um Inicial? Cientistas acham que é um ovo de Pokémon tipo '.$newinfo['type1'].'.';
		$omschrijving_de = 'Um ovo de Pokémon Lendário ou será que é de um Inicial? Cientistas acham que é um ovo de Pokémon tipo '.$newinfo['type1'].'.';
		$omschrijving_pl = 'Um ovo de Pokémon Lendário ou será que é de um Inicial? Cientistas acham que é um ovo de Pokémon tipo '.$newinfo['type1'].'.';
		$omschrijving_pt = 'Um ovo de Pokémon Lendário ou será que é de um Inicial? Cientistas acham que é um ovo de Pokémon tipo '.$newinfo['type1'].'.';
	}
	
	#Product opslaan in database
	DB::exQuery("UPDATE markt SET beschikbaar = '1', pokemonid = '".$newinfo['wild_id']."', naam = '".$newinfo['naam']."', silver = '".$silver_price."', gold = '".$gold_price."', omschrijving_nl = '".$omschrijving_nl."', omschrijving_en = '".$omschrijving_en."', omschrijving_es = '".$omschrijving_es."', omschrijving_de = '".$omschrijving_de."', omschrijving_pl = '".$omschrijving_pl."', omschrijving_pt = '".$omschrijving_pt."' WHERE id = '".$select['id']."'");
 }
  
  #Tijd opslaan van wanneer deze file is uitgevoerd
  $tijd = date("Y-m-d H:i:s");
  DB::exQuery("UPDATE `cron` SET `tijd`='".$tijd."' WHERE `soort`='markt'");
  echo "Cron executado com sucesso.";
?>