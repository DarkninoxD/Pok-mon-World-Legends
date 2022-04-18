<?php
require_once('../includes/resources/config.php');
	
	$winnaar = DB::exQuery("SELECT uid FROM `loterij_kaarten` ORDER BY rand() limit 1")->fetch_assoc();
  $tijd   = date("H")+1;
  if ($tijd >= 24) $tijd-24;
  $tijd   = $tijd.":00";
  $datum  = date('d-m-y H:i');
  $time = date("Y-m-d");
	if (!empty($winnaar['uid'])) {
    $loterij = DB::exQuery("SELECT `naam`, `silver_prijs`,`golds_prijs` FROM `loterij`")->fetch_assoc();
  	$kaarten = DB::exQuery("SELECT `id` FROM `loterij_kaarten`")->num_rows;
  	$user = DB::exQuery("SELECT username FROM gebruikers WHERE user_id='".$winnaar['uid']."'")->fetch_assoc();
  	$user['land'] = 'br';
  	
	if (($user['land'] == 'Netherlands') || ($user['land'] == 'Belgium')) {
		$txt['you_won'] = 'Parabéns! Você ganhou';
		$txt['with_the'] = 'na';
		$txt['lottery'] = 'Pokémon.';
	}
	else if ($user['land'] == 'Germany') {
		$txt['you_won'] = 'Parabéns! Você ganhou';
		$txt['with_the'] = 'na';
		$txt['lottery'] = 'Pokémon.';
	}
	else if ($user['land'] == 'Spain') {
		$txt['you_won'] = 'Parabéns! Você ganhou';
		$txt['with_the'] = 'na';
		$txt['lottery'] = 'Pokémon.';
	}
	else if ($user['land'] == 'Poland') {
		$txt['you_won'] = 'Parabéns! Você ganhou';
		$txt['with_the'] = 'na';
		$txt['lottery'] = 'Pokémon.';
	}
	else{
		$txt['you_won'] = 'Parabéns! Você ganhou';
		$txt['with_the'] = 'na';
		$txt['lottery'] = 'Pokémon.';
	}
	
  	$prijs  = $loterij['silver_prijs']*$kaarten;
  	$valorgold = number_format(round($loterij['golds_prijs']),0,",",".");
  	$valoorok = number_format(round($prijs),0,",",".");
  	$bericht = '<img src="images/icons/blue.png" width="16" height="16" class="imglower" /> '.$txt['you_won'].' <img src="images/icons/silver.png" width="16" height="16" alt="Silver" class="imglower"> '.number_format(round($prijs),0,",",".").' + <img src="images/icons/gold.png" width="16" height="16" alt="Gold" class="imglower"> '.$valorgold.' '.$txt['with_the'].' '.$loterij['naam'].' '.$txt['lottery'];
  	DB::exQuery("UPDATE `gebruikers` SET `bank`=`bank`+'".$prijs."' WHERE `user_id`='".$winnaar['uid']."'");
  	$quemvai = DB::exQuery("select `acc_id` from `gebruikers` where `user_id`='".$winnaar['uid']."' limit 1")->fetch_assoc();
	DB::exQuery("UPDATE `rekeningen` SET `gold`=`gold`+'".$loterij['golds_prijs']."' WHERE `acc_id`={$quemvai['acc_id']} LIMIT 1");  	
  	DB::exQuery("DELETE FROM `loterij_kaarten`");
        DB::exQuery("INSERT INTO `gebeurtenis` (`datum` ,`ontvanger_id` ,`bericht` ,`gelezen`) VALUES ('".$datum."', '".$winnaar['uid']."', '".$bericht."', 'nee');");
  }
  DB::exQuery("UPDATE `loterij` SET `time`='".$time."', `eind_tijd`='".$tijd."', `laatste_winnaar`='".$user['username']."', `valorultima`='$valoorok', `valorgoldultima`='$valorgold', `ticketsultima`='$kaarten', `aberta`='nao'");

?>