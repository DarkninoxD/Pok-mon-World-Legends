<?php
if (isset($_SESSION['id'])) {
	function addSilvers($min = 500, $max = 4999) {
		global $static_url;
		$silvers = rand($min, $max);
		DB::exQuery("UPDATE `gebruikers` SET `silver`=`silver`+{$silvers},`daily_bonus`=UNIX_TIMESTAMP() WHERE `user_id`={$_SESSION['id']} LIMIT 1");
		return 'success | Parabéns, você ganhou <img src="' . $static_url . '/images/icons/silver.png" style="vertical-align: middle"/> <b>' . highamount($silvers) .'</b>!';
	}

	$gebruiker = DB::exQuery("SELECT `daily_bonus`,`premiumaccount`,`rank`,`rankexp`,`rankexpnodig` FROM `gebruikers` WHERE `user_id`={$_SESSION['id']} LIMIT 1")->fetch_assoc();
	if ($gebruiker['daily_bonus']+86400 > time())	echo 'error | Você já recebeu seu premio diario hoje!';
	else {
		$random = rand(1, 7);
		switch($random) {
			case 1:	// Ganhou silvers
				echo addSilvers(5000, 100000);
				break;
			case 2:
				$soort = rand(1, 2);
				if ($soort == 1) {
				// Ganhou premium
				$premiumdays = 1; //rand(1, 7);
				$premium = 86400 * $premiumdays;
				if ($gebruiker['premiumaccount'] < time())	$premium += time();
				else	$premium += $gebruiker['premiumaccount'];

				DB::exQuery("UPDATE `gebruikers` SET `premiumaccount`={$premium},`daily_bonus`=UNIX_TIMESTAMP() WHERE `user_id`={$_SESSION['id']} LIMIT 1");
				echo 'success | Parabéns, você ganhou <img src="' . $static_url . '/images/icons/vip.gif" style="vertical-align: middle"/> <b>' . highamount($premiumdays) .'</b> dia!';
				} else echo addSilvers();
				break;
			case 3:	// Ganhou uma stone
				$soort = rand(1, 2500);
				if ($soort <= 2490) {	// Stones comuns
					$getStone = DB::exQuery("SELECT * FROM `markt` WHERE `soort`='stones' AND `roleta`='sim' AND `beschikbaar`='1' AND (`id`>='131' AND `id`<='140') ORDER BY RAND() LIMIT 1")->fetch_assoc();
					DB::exQuery("UPDATE `gebruikers_item` SET `{$getStone['naam']}`=`{$getStone['naam']}`+'1' WHERE `user_id`={$_SESSION['id']} LIMIT 1");
					echo 'success | Parabéns, você ganhou <img src="' . $static_url . '/images/items/' . $getStone['naam'] . '.png" style="vertical-align: middle"/> <b>1</b>!';
				} else	echo addSilvers();
				DB::exQuery("UPDATE `gebruikers` SET `daily_bonus`=UNIX_TIMESTAMP() WHERE `user_id`={$_SESSION['id']} LIMIT 1");
				break;
			case 4:	// Ganhou uma pokebola
				$soort = rand(1, 2500);
				if ($soort >= 1000) {
					$balls = 1;
					$getBall = DB::exQuery("SELECT * FROM `markt` WHERE `soort`='balls' AND `gold`='0' AND `beschikbaar`='1' AND `roleta`='sim' LIMIT 1")->fetch_assoc();
					DB::exQuery("UPDATE `gebruikers_item` SET `{$getBall['naam']}`=`{$getBall['naam']}`+{$balls} WHERE `user_id`={$_SESSION['id']} LIMIT 1");
				DB::exQuery("UPDATE `gebruikers` SET `daily_bonus`=UNIX_TIMESTAMP() WHERE `user_id`={$_SESSION['id']} LIMIT 1");
				echo 'success | Parabéns, você ganhou <img src="' . $static_url . '/images/items/' . $getBall['naam'] . '.png" style="vertical-align: middle"/> <b>' . $balls . '</b>!';
				break;
				} else	{ echo addSilvers(); break; }
			case 5:
				$add_exp = rand(100, 1000) * $gebruiker['rank'];
				$gebruiker['rankexp'] += $add_exp;
				$gebruiker['rankexp'] = ($gebruiker['rankexp'] < $gebruiker['rankexpnodig']) ? $gebruiker['rankexp'] : $gebruiker['rankexpnodig'] - 10;
				DB::exQuery("UPDATE `gebruikers` SET `rankexp`={$gebruiker['rankexp']},`daily_bonus`=UNIX_TIMESTAMP() WHERE `user_id`={$_SESSION['id']} LIMIT 1");
				echo 'success | Parabéns, você ganhou <b>' . highamount($add_exp) . '</b> pontos de experiência!';
				break;
			default:	 echo addSilvers();	break;
		}
	}
}
?>