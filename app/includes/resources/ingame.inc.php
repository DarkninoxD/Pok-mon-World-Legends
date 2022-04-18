<?php
$captcha_page_check = array('attack/attack_map', 'attack/gyms', 'trainer', 'attack/duel/invite', 'traders', 'race-invite', 'casino-store', 'who-is-it-quiz', 'wheel-of-fortune', 'lottery', 'fishing', 'friend-add', 'inbox', 'blocklist', 'box', 'market', 'transferlist', 'donate', 'calculator', 'juiz', 'account-options');

function GetEventLanguage() {
	return 'pt';
}

function highamount($amount) {
	return number_format(round($amount), 0, ",", ".");
}

function balance_converter ($money) {
	$max = 1000000;
	$max_2 = 1000000000;
	
	if ($money >= $max) {
		$nb = $money / $max_2;
		
		if ($nb < 1) {
			return rtrim(substr(($money / $max), 0, 4), '.').' Mi';
		} else {
			return rtrim(substr(($money / $max_2), 0, 4), '.').' Bi';
		}
	} else {
		return highamount ($money);
	}
}

function GetColorName($user_id) {
	global $static_url;

	$gebruiker = DB::exQuery("SELECT `dv`,`banned`,`premiumaccount`,`admin`,`username`,`posicaorank` FROM `gebruikers` WHERE `user_id`='" . (int)$user_id . "' LIMIT 1")->fetch_assoc();
	if ($gebruiker['admin'] == 1)					return "<span style='color: #A1FF77; text-shadow:#000 1px -1px 2px, #000 -1px 1px 2px, #000 1px 1px 2px, #000 -1px -1px 2px'><b>" . $gebruiker['username'] . "</b></span> <img src=\"" . $static_url . "/images/icons/user.png\" style=\"vertical-align:-3px;\" title=\"Moderador\" />";
	else if ($gebruiker['admin'] == 2)				return "<span style='color: #FF3030; text-shadow:#000 1px -1px 2px, #000 -1px 1px 2px, #000 1px 1px 2px, #000 -1px -1px 2px'><b>" . $gebruiker['username'] . "</b></span> <img src=\"" . $static_url . "/images/icons/user_suit.png\" style=\"vertical-align:-3px;\" title=\"Supervisor\" />";
	else if ($gebruiker['admin'] == 3)				return "<span style='color: yellow; text-shadow:#000 1px -1px 2px, #000 -1px 1px 2px, #000 1px 1px 2px, #000 -1px -1px 2px'><b>" . $gebruiker['username'] . "</b></span> <img src=\"" . $static_url . "/images/icons/user_admin.png\" style=\"vertical-align:-3px;\" title=\"Administrador\" />";
	else if ($gebruiker['dv'] == 1)					return "<span style='color: orange; text-shadow:#000 1px -1px 2px, #000 -1px 1px 2px, #000 1px 1px 2px, #000 -1px -1px 2px'><b>[DV]" . $gebruiker['username'] . "</b></span> <img src=\"" . $static_url . "/images/icons/dv.png\" style=\"vertical-align:-3px;\" title=\"Divulgador\" />";
	else if ($gebruiker['banned'] == 'Y')			return "<s>" . $gebruiker['username'] . "</s> <img src=\"" . $static_url . "/images/icons/lock.png\" style=\"vertical-align:-3px;\" />";
	else if ($gebruiker['premiumaccount'] > time())	return $gebruiker['username'] . " <img src=\"" . $static_url . "/images/icons/vip.gif\" style=\"vertical-align:-3px;\" />";
	else if ($gebruiker['posicaorank'] > 0 AND $gebruiker['posicaorank'] <= 4 AND $gebruiker['admin'] == 0)
													return '<span style="color: #fff">'.$gebruiker['username'] . "</span> <img src=\"" . $static_url . "/images/icons/elite.gif\" style=\"vertical-align:-3px;\" title=\"Elite dos 4\" />";
	else											return $gebruiker['username'];
}

function ubbcode($tekst) {
	global $static_url;

	$tekst = str_replace("<", "", $tekst);
	$tekst = str_replace(">", "", $tekst);
	$tekst = anti_langezin($tekst);
	$tekst = nl2br($tekst);

	$pad    = $static_url . "/images/emoticons/";
	$smiley = array(
		":)" => "001.png",
		":D" => "002.png",
		"xD" => "003.png",
		":P" => "004.png",
		";)" => "005.png",
		":S" => "006.png",
		":O" => "007.png",
		"8-)" => "008.png",
		":*" => "009.png",
		":(" => "010.png",
		":'(" => "011.png",
		":|" => "012.png",
		":b" => "013.png",
		"(BOO)" => "014.png",
		"(zZZ)" => "015.png",
		":v" => "016.png",
		"(GRR)" => "017.png",
		":3" => "018.png",
		"@-)" => "019.png",
		"o_O" => "020.png",
		"._." => "021.png",
		"(S2)" => "022.png"
	);

	foreach($smiley as $bb=>$img)
		$tekst = preg_replace("#" . preg_quote($bb, '#') . "#i", "<img src=\"" . $pad . $img . "\" />", $tekst);

	if (strpos($tekst, "[") === false)	return $tekst;

	$tekst = preg_replace("#\[b\](.*?)\[/b\]#si", "<strong>\\1</strong>", $tekst);
	$tekst = preg_replace("#\[i\](.*?)\[/i\]#si", "<i>\\1</i>", $tekst);
	$tekst = preg_replace("#\[u\](.*?)\[/u\]#si", "<u>\\1</u>", $tekst);
	$tekst = preg_replace("#\[s\](.*?)\[/s\]#si", "<s>\\1</s>", $tekst);
	$tekst = preg_replace("#\[marquee\](.*?)\[/marquee\]#si", "<marquee>\\1</marquee>", $tekst);
	$tekst = preg_replace("#\[center\](.*?)\[/center\]#si", "<center>\\1</center>", $tekst);
	$tekst = preg_replace("#\[quote\](.*?)\[/quote\]#si", "<div class='quote'>\\1</div>", $tekst);
	$tekst = preg_replace("#\[player\](.*?)\[/player\]#si", "<a href=\"/profile&player=\\1\">\\1</a>", $tekst);

	$tekst = preg_replace("#\[color=(\#[0-9A-F]{6}|[a-z\-]+)\](.*?)\[/color\]#si", "<font color=\"\\1\">\\2</font>", $tekst);
	$tekst = preg_replace("#\[url\][[:space:]]*(http://)?([^\\[]*)[[:space:]]*\[/url\]#si", "<a href=\"http://\\2\" target=\"_blank\">http://\\2</a>", $tekst);
	$tekst = preg_replace("#\\[img]([^\\[]*)\\[/img\\]#si", "<img src=\"\\1\" border=\"0\" OnLoad=\"if (this.width > 660) {this.width=660}\">", $tekst);
	$tekst = preg_replace('_\[youtube\].*?(v=|v/)(.+?)(&.*?|/.*?)?\[/youtube\]_is', '[youtube]$2[/youtube]', $tekst);
	$tekst = preg_replace('_\[youtube\]([a-z0-9-]+?)\[/youtube\]_is', '<object width="425" height="355"><param name="movie" value="http://www.youtube.com/v/$1"></param><param name="wmode" value="transparent"></param><embed src="http://www.youtube.com/v/$1" type="application/x-shockwave-flash" wmode="transparent" width="425" height="355"></embed></object>', $tekst);
	$tekst = preg_replace("#\[animatie\]([^\[]+)\[/animatie\]#si", "<img src=\"" . $static_url . "/images/pokemon/icon/\\1.gif\" border=\"0\">", $tekst);
	$tekst = preg_replace("#\[icon\]([^\[]+)\[/icon\]#si", "<img src=\"" . $static_url . "/images/pokemon/icon/\\1.gif\" border=\"0\">", $tekst);
	$tekst = preg_replace("#\[icon_shiny\]([^\[]+)\[/icon_shiny\]#si", "<img src=\"" . $static_url . "/images/shiny/icon/\\1.gif\" border=\"0\">", $tekst);
	$tekst = preg_replace("#\[back\]([^\[]+)\[/back\]#si", "<img src=\"" . $static_url . "/images/pokemon/back/\\1.gif\" border=\"0\">", $tekst);
	$tekst = preg_replace("#\[back_shiny\]([^\[]+)\[/back_shiny\]#si", "<img src=\"" . $static_url . "/images/shiny/back/\\1.gif\" border=\"0\">", $tekst);
	$tekst = preg_replace("#\[pokemon\]([^\[]+)\[/pokemon\]#si", "<img src=\"" . $static_url . "/images/pokemon/\\1.gif\" border=\"0\">", $tekst);
	$tekst = preg_replace("#\[shiny\]([^\[]+)\[/shiny\]#si", "<img src=\"" . $static_url . "/images/shiny/\\1.gif\" border=\"0\">", $tekst);

	return $tekst;
}

function query_cache($page, $query, $expire) {
    ini_set('memory_limit', '-1');
	$file = 'app/cache/' . $page . '.txt';
	
    if (file_exists($file) && filemtime($file) > (time() - $expire)) {
		$records = unserialize(file_get_contents($file));
	} else {
        $result = DB::exQuery($query);
        while ($record = $result->fetch_assoc()) $records[] = $record;
        $OUTPUT = serialize($records);
        $fp = fopen($file, "w");
        fputs($fp, $OUTPUT);
        fclose($fp);
    }
    return $records;
}

function update_pokedex($wild_id, $old_id, $wat) {
	$load = DB::exQuery("SELECT `pok_gezien`,`pok_bezit` FROM `gebruikers` WHERE `user_id`='" . $_SESSION['id'] . "' LIMIT 1")->fetch_assoc();
	$pokedex_bezit		= explode(",", $load['pok_bezit']);
	$pokedex_gezien		= explode(",", $load['pok_gezien']);

	$query = array();
	if ($wat == 'ei') {
		if (!in_array($wild_id, $pokedex_gezien))	$query[] = "`pok_gezien`='" . $load['pok_gezien'] . ',' . $wild_id . "'";
		if (!in_array($wild_id, $pokedex_bezit))		$query[] = "`pok_bezit`='" . $load['pok_bezit'] . ',' . $wild_id . "'";
	} else if ($wat == 'zien') {
		if (!in_array($wild_id, $pokedex_gezien))	$query[] = "`pok_gezien`='" . $load['pok_gezien'] . ',' . $wild_id . "'";
	} else if ($wat == 'vangen') {
		if (!in_array($wild_id, $pokedex_bezit))		$query[] = "`pok_bezit`='" . $load['pok_gezien'] . ',' . $wild_id . "'";
	} else if ($wat == 'buy') {
		if (!in_array($wild_id, $pokedex_gezien))	$query[] = "`pok_gezien`='" . $load['pok_gezien'] . ',' . $wild_id . "'";
		if (!in_array($wild_id, $pokedex_bezit))		$query[] = "`pok_bezit`='" . $load['pok_bezit'] . ',' . $wild_id . "'";
	} else if ($wat == 'evo') {
		if (!in_array($wild_id, $pokedex_gezien))	$query[] = "`pok_gezien`='" . $load['pok_gezien'] . ',' . $wild_id . "'";
		if (!in_array($wild_id, $pokedex_bezit))		$query[] = "`pok_bezit`='" . $load['pok_bezit'] . ',' . $wild_id . "'";
	}

	if (!empty($query))	DB::exQuery("UPDATE `gebruikers` SET " . implode(',', $query) . " WHERE `user_id`='" . $_SESSION['id'] . "' LIMIT 1");
}

function max_min_price($pokemon, $currency = 'silver') {
	if ($currency == 'silver') {
		$pokemon['zeldzaamheid'] *= 15;
		$shinywaard = 200;
		if ($pokemon['shiny'] == 0)	$shinywaard = 150;

		$waard = round($pokemon['level'] * $pokemon['zeldzaamheid'] * 40 / 100 * $shinywaard);
	} else {
		$pokemon['zeldzaamheid'] *= 2;
		$shinywaard = 20;
		if ($pokemon['shiny'] == 0)	$shinywaard = 10;

		$waard = round($pokemon['level'] * $pokemon['zeldzaamheid'] * 5 / 100 * $shinywaard);
	}

	$maxprice = round($waard * 2);

	$max_min['maxprice'] = $maxprice;
	$max_min['minimum'] = round($waard / 2);
	$max_min['waard'] = $waard;

	$max_min['minimum_mooi']  = highamount($max_min['minimum']);
	$max_min['waard_mooi']    = highamount($waard);
	$max_min['maxprice_mooi'] = highamount($maxprice);
	
	return $max_min;
}

function getUrl () {
	$base = str_replace('', '.', $_SERVER['REQUEST_URI']);
	$p = func_get_args();

	for($i = 0; $i < sizeof($p); $i++) {
		$base = preg_replace($p[$i], '', $base);
	}

	return $base;
}
 
function page_timer($page, $timer) {
	$zien = array('home','account-options','pokemoninfo','rankinglist','statistics','extended','items','house','pokedex','mail','events','blocklist','profile','logout','donate','information','forum-categories','forum-threads','forum-messages','promotion','inbox','official-messages','buddylist','area-messenger','search-user','area-market');
	if ($timer == 'jail')	array_push($zien, "jail");
	if (in_array($page, $zien))	return true;
	else	return false;
}

function rankerbij($soort, $txt) {
	global $static_url;

	$spelerrank = DB::exQuery("SELECT `g`.`username`,`g`.`user_id`,`g`.`rankexp`,`g`.`rankexpnodig`,`g`.`rank`,`g`.`premiumaccount` FROM `gebruikers` AS `g` INNER JOIN `rekeningen` AS `r` ON `g`.`acc_id`=`r`.`acc_id` WHERE `g`.`user_id`='" . $_SESSION['id'] . "' LIMIT 1")->fetch_assoc();

	$premiumFlag = 1; 
	if ($spelerrank['premiumaccount'] > time())	$premiumFlag += 0.5; // 50% extra voor premium

	# Kijken wat speler gedaan heeft
	if ($soort == "race") $soort = 1;
 	else if ($soort == "werken") $soort = 2;
 	else if ($soort == "whoisitquiz") $soort = 2;
  else if ($soort == "attack") $soort = 3;
 	else if ($soort == "jail") $soort = 3;
  else if ($soort == "trainer") $soort = 4;
  else if ($soort == "gym") $soort = 5;
  else if ($soort == "duel") $soort = 5;

	//Kijken als speler niet boven de max zit.
	$rank = rank($spelerrank['rank']);
	$uitkomst = round(((($rank['ranknummer'] / 0.15) * $soort) / 3) * $premiumFlag);
	DB::exQuery("UPDATE `gebruikers` SET `rankexp`=`rankexp`+'" . $uitkomst . "' WHERE `user_id`='" . $_SESSION['id'] . "' LIMIT 1");

	//Heeft speler genoeg punten om rank omhoog te gaan?
	$spelerrank['rankexp'] = $spelerrank['rankexp'] + $uitkomst;
	if ($spelerrank['rankexpnodig'] <= $spelerrank['rankexp']) {
		//Punten berekenen wat speler over heeft
		$rankexpover = $spelerrank['rankexp'] - $spelerrank['rankexpnodig'];

		//Nieuwe rank level bepalen
		$ranknieuw   = ++$spelerrank['rank'];
		if ($ranknieuw <= 33) {
			$query = DB::exQuery("SELECT `naam`,`punten` FROM `rank` WHERE `ranknummer`='" . $ranknieuw . "' LIMIT 1")->fetch_assoc();

		//Nieuwe gegevens opslaan bij de gebruiker
		if ($ranknieuw >= 33)
			DB::exQuery("UPDATE `gebruikers` SET `rank`='33', `rankexp`='1', `rankexpnodig`='170000000' WHERE `user_id`='".$_SESSION['id']."' LIMIT 1");
		else
			DB::exQuery("UPDATE `gebruikers` SET `rank`='" . $ranknieuw . "',`rankexp`='" . $rankexpover . "',`rankexpnodig`='" . $query['punten'] . "' WHERE `user_id`='" . $_SESSION['id'] . "' LIMIT 1");

		$rank_up = DB::exQuery("SELECT * FROM `rank_up` WHERE `rank`='".$ranknieuw."' LIMIT 1")->fetch_assoc();

		$gold_msg = false;
		$pokemon_msg = false;

		if (!empty($rank_up['wild_id'])) {
				#tijd van nu fixen
				$tijd = date('Y-m-d H:i:s');
				$opzak_nummer = $gebruiker['in_hand'] + 1;

				#Willekeurige pokemon laden, en daarvan de gegevens
				$query = DB::exQuery("SELECT `wild_id`,`groei`,`attack_base`,`defence_base`,`speed_base`,`spc.attack_base`,`spc.defence_base`,`hp_base`,`aanval_1`,`aanval_2`,`aanval_3`,`aanval_4`,`ability` FROM `pokemon_wild` WHERE `wild_id`='" . $rank_up['wild_id'] . "' LIMIT 1")->fetch_assoc();
				$ability = explode(',', $query['ability']);

				$date = date('Y-m-d H:i:s');

				$ability = $ability[rand(0, (sizeof($ability) - 1))];

				#De willekeurige pokemon in de pokemon_speler tabel zetten
				DB::exQuery("INSERT INTO `pokemon_speler` (`wild_id`,`aanval_1`,`aanval_2`,`aanval_3`,`aanval_4`) SELECT `wild_id`,`aanval_1`,`aanval_2`,`aanval_3`,`aanval_4` FROM `pokemon_wild` WHERE `wild_id`='" . $query['wild_id'] . "'");

				#id opvragen van de insert hierboven
				$pokeid	= DB::insertID();

				#Karakter kiezen 
				$karakter  = DB::exQuery("SELECT * FROM `karakters` ORDER BY RAND() LIMIT 1")->fetch_assoc();

				#Expnodig opzoeken en opslaan
				$experience = DB::exQuery("SELECT `punten` FROM `experience` WHERE `soort`='".$query['groei']."' AND `level`='6'")->fetch_assoc();

				$attack_iv		= mt_rand(2, 31);
				$defence_iv		= mt_rand(2, 31);
				$speed_iv		= mt_rand(2, 31);
				$spcattack_iv	= mt_rand(2, 31);
				$spcdefence_iv	= mt_rand(2, 31);
				$hp_iv			= mt_rand(2, 31);

				#Stats berekenen
				$attackstat		= round(((($attack_iv + 2 * $query['attack_base']) * 5 / 100) + 5) * $karakter['attack_add']);
				$defencestat	= round(((($defence_iv + 2 * $query['defence_base']) * 5 / 100) + 5) * $karakter['defence_add']);
				$speedstat		= round(((($speed_iv + 2 * $query['speed_base']) * 5 / 100) + 5) * $karakter['speed_add']);
				$spcattackstat	= round(((($spcattack_iv + 2 * $query['spc.attack_base']) * 5 / 100) + 5) * $karakter['spc.attack_add']);
				$spcdefencestat	= round(((($spcdefence_iv + 2 * $query['spc.defence_base']) * 5 / 100) + 5) * $karakter['spc.defence_add']);
				$hpstat			= round((($hp_iv + 2 * $query['hp_base']) * 5 / 100) + 10 + 5);

				#Alle gegevens van de pokemon opslaan
				DB::exQuery("UPDATE `pokemon_speler` SET `level`='5',`karakter`='".$karakter['karakter_naam']."',`expnodig`='".$experience['punten']."',`user_id`='".$_SESSION['id']."',`opzak`='nee',`opzak_nummer`='".$opzak_nummer."',`ei`='1',`ei_tijd`='".$tijd."',`attack_iv`='".$attack_iv."',`defence_iv`='".$defence_iv."',`speed_iv`='".$speed_iv."',`spc.attack_iv`='".$spcattack_iv."',`spc.defence_iv`='".$spcdefence_iv."',`hp_iv`='".$hp_iv."',`attack`='".$attackstat."',`defence`='".$defencestat."',`speed`='".$speedstat."',`spc.attack`='".$spcattackstat."',`spc.defence`='".$spcdefencestat."',`levenmax`='".$hpstat."',`leven`='".$hpstat."',`ability`='".$ability."',`capture_date`='".$date."' WHERE `id`='".$pokeid."' LIMIT 1");
				DB::exQuery("UPDATE `gebruikers` SET `aantalpokemon`=`aantalpokemon`+'1' WHERE `user_id`='" . $_SESSION['id'] . "' LIMIT 1");
		}

		DB::exQuery("UPDATE `gebruikers` SET `silver`=`silver`+'" . $rank_up['silvers'] . "', `points`=`points`+'" . $rank_up['extra_points'] . "' WHERE `user_id`='" . $_SESSION['id'] . "' LIMIT 1");
		DB::exQuery("UPDATE `rekeningen` SET `gold`=`gold`+'" . $rank_up['golds'] . "' WHERE `acc_id`='" . $_SESSION['acc_id'] . "' LIMIT 1");
		
		$msg = ' E de <b>Recompensa</b> ganhou: <b>'.highamount($rank_up['silvers']).'</b> <img src="'.$static_url.'/images/icons/silver.png" title="" width="16" height="16" title="Silver">';
		if ($rank_up['golds'] > 0) {
			$msg .= ' e <b>'.highamount($rank_up['golds']).'</b> <img src="'.$static_url.'/images/icons/gold.png" title="" width="16" height="16" title="Gold">';
		}

		if (!empty($rank_up['wild_id'])) {
			$msg .= ' e <b>um ovo Pokémon</b>';
		}

		if (!empty($rank_up['message'])) {
			$msg .= ' e '.$rank_up['message'];
		}

		$msg .= '!';

		$eventlanguage = GetEventLanguage();
		require_once('../../language/events/language-events-' . $eventlanguage . '.php');
		$event = '<img src="' . $static_url . '/images/icons/blue.png" class="imglower" /> ' . sprintf($txt['event_rank_up'], $query['naam']) . $msg;
		DB::exQuery("INSERT INTO `gebeurtenis` (`datum`,`ontvanger_id`,`bericht`,`gelezen`) VALUES (NOW(),'" . $_SESSION['id'] . "','" . $event . "',0)");
		}
	}
}

function rank($ranknummer) {
	$query = DB::exQuery("SELECT * FROM `rank` WHERE `ranknummer`='" . $ranknummer . "' LIMIT 1")->fetch_assoc();

	$rank['ranknummer'] = $ranknummer;
	$rank['ranknaam']   = $ranknummer.' - '.$query['naam'];

	return $rank;
}

function kans($nummer) {
	$getal = mt_rand(1, 100);

	for($i=1; $i<=$nummer; ++$i) {
		$kans = mt_rand(1, 100);
		if ($getal == $kans)  return true;
	}
	return false;
}

function isDay() {
	$date = strtotime(date('H:i:s'));
	if ($date >= strtotime('08:59:59') && $date <= strtotime('19:59:59')) {
		return 'day';
	}else if ($date >= strtotime('20:49:59') && $date <= strtotime('20:59:59')) {
		return 'dusk';
	}else{
		return 'night';
	}
}

function isSeason () {
	if (in_array(date("n"), array(1, 5, 9))) {
		$season_act = "Primavera";
		$season_number = 1;
	} else if (in_array(date("n"), array(2, 6, 10))) {
		$season_act = "Verão";
		$season_number = 2;
	} else if (in_array(date("n"), array(3, 7, 11))) {
		$season_act = "Outono";
		$season_number = 3;
	} else if (in_array(date("n"), array(4, 8, 12))) {
		$season_act = "Inverno";
		$season_number = 4;
	} 

	return array($season_act, $season_number);
}

function pokemon_equip ($id, $item) {
	if (in_array($item, array('Burn Drive', 'Chill Drive', 'Douse Drive', 'Shock Drive'))) {
		if ($id != '649') {
			return false;
		}
	} else if ($item == 'Dragon Scale') {
		if ($id != '117') {
			return false;
		}
	} else if ($item == 'Metal Coat') {
		if (!in_array($id, array(95, 123))) {
			return false;
		}
	} else if ($item == 'Kings Rock') {
		if (!in_array($id, array(79, 61))) {
			return false;
		}
	} else if ($item == 'Whipped Dream') {
		if ($id != '684') {
			return false;
		}
	} else if ($item == 'Dubious Disc') {
		if ($id != '233') {
			return false;
		}
	} else if ($item == 'Up-Grade') {
		if ($id != '137') {
			return false;
		}
	} else if ($item == 'Sachet') {
		if ($id != '682') {
			return false;
		}
	} else if ($item == 'Reaper Cloth') {
		if ($id != '356') {
			return false;
		}
	} else if ($item == 'Protector') {
		if ($id != '112') {
			return false;
		}
	} else if ($item == 'Electirizer') {
		if (!in_array($id, array(125, 737))) {
			return false;
		}
	} else if ($item == 'Magmarizer') {
		if ($id != '467') {
			return false;
		}
	} else if ($item == 'Razor Claw') {
		if ($id != '215') {
			return false;
		}
	} else if ($item == 'Razor Fang') {
		if ($id != '207') {
			return false;
		}
	} else if ($item == 'Light Ball') {
		if (!in_array($id, array('25', '923', '967', '968', '966', '965'))) {
			return false;
		}
	} else if ($item == 'Thick Club') {
		if (!in_array($id, array('104', '105'))) {
			return false;
		}
	} else if ($item == 'Lucky Punch') {
		if ($id != '113') {
			return false;
		}
	} else if ($item == 'Stick') {
		if ($id != '83') {
			return false;
		}
	} else if ($item == 'Soul Dew') {
		if (!in_array($id, array('381', '842', '841', '380'))) {
			return false;
		}
	} else if (strpos($item, ' Z') !== false) {
		$sql = DB::exQuery ("SELECT `pokemons` FROM `zaanval_relacionados` WHERE item='$item'");
		if ($sql->num_rows == 1) {
			$sql = $sql->fetch_assoc();
			if (!empty($sql['pokemons'])) {
				$pokes = explode(',', $sql['pokemons']);
				if (!in_array($id, $pokes)) {
					return false;
				}
			} else {
				if (in_array($id, array('902', '917', '919'))) {
					return false;
				}
			}
		}
	}

	return true;
}

//Als pokemon aanval leert of evolueert
function levelgroei($levelnieuw, $pokemon) {
	//Gegevens laden van pokemon die leven groeit uit levelen tabel
	$levelensql = DB::exQuery("SELECT * FROM `levelen` WHERE `wild_id`='" . $pokemon['wild_id'] . "'");
	//Voor elke actie kijken als het klopt.
	while($levelen = $levelensql->fetch_assoc()) {	//als de actie een aanval leren is
		if ($levelen['wat'] == "att") {	//Komt het benodigde level overeen
			if ($levelen['level'] == $levelnieuw) {	//Kent de pokemon deze aanval al
				if ($pokemon['aanval_1'] != $levelen['aanval'] && $pokemon['aanval_2'] != $levelen['aanval'] && $pokemon['aanval_3'] != $levelen['aanval'] && $pokemon['aanval_4'] != $levelen['aanval']) {	//Als er 1 plek leeg is
					if (empty($pokemon['aanval_1']) || empty($pokemon['aanval_2']) || empty($pokemon['aanval_3']) || empty($pokemon['aanval_4'])) {	//Is de eerst plek niet leeg
						if (!empty($pokemon['aanval_1'])) {	//Is de tweede plek niet leeg
							if (!empty($pokemon['aanval_2'])) {	//Is de derde plek niet leeg
								if (!empty($pokemon['aanval_3'])) {	//Is de vierde plek niet leeg, dan moet er gekozen worden, code maken die word mee gegeven
                  					if (!empty($pokemon['aanval_4'])) {
										if (!$_SESSION['aanvalnieuw'])	$_SESSION['aanvalnieuw'] = base64_encode($pokemon['id'] . "/" . $levelen['aanval']);
									} else //Als de vierde plek wel leeg is dan aanval daar opslaan
										DB::exQuery("UPDATE `pokemon_speler` SET `aanval_4`='" . $levelen['aanval'] . "' WHERE `id`='" . $pokemon['id'] . "'");
								} else  //Als de derde plek wel leeg is dan aanval daar opslaan
									DB::exQuery("UPDATE `pokemon_speler` SET `aanval_3`='" . $levelen['aanval'] . "' WHERE `id`='" . $pokemon['id'] . "'");
							} else  //Als de tweede plek wel leeg is dan aanval daar opslaan
								DB::exQuery("UPDATE `pokemon_speler` SET `aanval_2`='" . $levelen['aanval'] . "' WHERE `id`='" . $pokemon['id'] . "'");
						} else  //Als de eerste plek wel leeg is dan aanval daar opslaan
							DB::exQuery("UPDATE `pokemon_speler` SET `aanval_1`='" . $levelen['aanval'] . "' WHERE `id`='" . $pokemon['id'] . "'");
					} else  //Is alles vol, dan moet er gekozen worden
						if (!$_SESSION['aanvalnieuw'])	$_SESSION['aanvalnieuw'] = base64_encode($pokemon['id'] . "/" . $levelen['aanval']);
				}
			}
		} else if ($levelen['wat'] == "evo") {  //Gaat de pokemon evolueren
			//Is het level groter of gelijk aan de level die benodigd is? Naar andere pagina gaan
			if ($pokemon['item'] != 'Everstone') {
				if ($levelen['level'] <= $levelnieuw || ($levelen['trade'] == 1 && $pokemon['trade'] == "1.5")) {
					if (!empty($levelen['time'])) {
						if ($levelen['time'] == isDay()) {
							$code = base64_encode($pokemon['id'] . "/" . $levelen['nieuw_id']);
							if (!$_SESSION['evolueren'])	$_SESSION['evolueren'] = $code;
							else if (!$_SESSION['evolueren2'] && $_SESSION['evolueren'] != $code)	$_SESSION['evolueren2'] = $code;
							else if (!$_SESSION['evolueren3'] && $_SESSION['evolueren'] != $code && $_SESSION['evolueren2'] != $code)	$_SESSION['evolueren3'] = $code;
							else if (!$_SESSION['evolueren4'] && $_SESSION['evolueren'] != $code && $_SESSION['evolueren2'] != $code && $_SESSION['evolueren3'] != $code)	$_SESSION['evolueren4'] = $code;
							else if (!$_SESSION['evolueren5'] && $_SESSION['evolueren'] != $code && $_SESSION['evolueren2'] != $code && $_SESSION['evolueren3'] != $code && $_SESSION['evolueren4'] != $code)	$_SESSION['evolueren5'] = $code;
							else if (!$_SESSION['evolueren6'] && $_SESSION['evolueren'] != $code && $_SESSION['evolueren2'] != $code && $_SESSION['evolueren3'] != $code && $_SESSION['evolueren4'] != $code && $_SESSION['evolueren5'] != $code)	$_SESSION['evolueren6'] = $code;
						}
					} else if (($levelen['trade'] == 1 && $pokemon['trade'] == "1.5")) {
						if ( $pokemon['item'] == $levelen['item'] ) {
								$code = base64_encode($pokemon['id'] . "/" . $levelen['nieuw_id']);
								if (!$_SESSION['evolueren']) {
										$_SESSION['evolueren'] = $code;
								} else if (!$_SESSION['evolueren2'] && $_SESSION['evolueren'] != $code) {
										$_SESSION['evolueren2'] = $code;
								} else if (!$_SESSION['evolueren3'] && $_SESSION['evolueren'] != $code && $_SESSION['evolueren2'] != $code) {
										$_SESSION['evolueren3'] = $code;
								} else if (!$_SESSION['evolueren4'] && $_SESSION['evolueren'] != $code && $_SESSION['evolueren2'] != $code && $_SESSION['evolueren3'] != $code) {
										$_SESSION['evolueren4'] = $code;
								} else if (!$_SESSION['evolueren5'] && $_SESSION['evolueren'] != $code && $_SESSION['evolueren2'] != $code && $_SESSION['evolueren3'] != $code && $_SESSION['evolueren4'] != $code) {
										$_SESSION['evolueren5'] = $code;
								} else if (!$_SESSION['evolueren6'] && $_SESSION['evolueren'] != $code && $_SESSION['evolueren2'] != $code && $_SESSION['evolueren3'] != $code && $_SESSION['evolueren4'] != $code && $_SESSION['evolueren5'] != $code) {
										$_SESSION['evolueren6'] = $code;
								}
						}
					} else {
						if ($levelen['wild_id'] == '236') {
							$atk = $pokemon['attack'];
							$def = $pokemon['defence'];
							if ($atk > $def) {
								$levelen['nieuw_id'] = 106;
							} else if ($atk < $def) {
								$levelen['nieuw_id'] = 107;
							} else {
								$levelen['nieuw_id'] = 237;
							}
						} else if ($levelen['wild_id'] == '265') {
							$rand = rand(1, 2);
							$array = array('266', '268');
							$levelen['nieuw_id'] = $array[$rand-1];
						} else if ($levelen['wild_id'] == '104') {
						    if ('Alola' == $_SESSION['region']) {
						        $levelen['nieuw_id'] = '105001';
						    } else {
						        $levelen['nieuw_id'] = '105';
						    }
						}
						$code = base64_encode($pokemon['id'] . "/" . $levelen['nieuw_id']);
						if (!$_SESSION['evolueren'])	$_SESSION['evolueren'] = $code;
						else if (!$_SESSION['evolueren2'] && $_SESSION['evolueren'] != $code)	$_SESSION['evolueren2'] = $code;
						else if (!$_SESSION['evolueren3'] && $_SESSION['evolueren'] != $code && $_SESSION['evolueren2'] != $code)	$_SESSION['evolueren3'] = $code;
						else if (!$_SESSION['evolueren4'] && $_SESSION['evolueren'] != $code && $_SESSION['evolueren2'] != $code && $_SESSION['evolueren3'] != $code)	$_SESSION['evolueren4'] = $code;
						else if (!$_SESSION['evolueren5'] && $_SESSION['evolueren'] != $code && $_SESSION['evolueren2'] != $code && $_SESSION['evolueren3'] != $code && $_SESSION['evolueren4'] != $code)	$_SESSION['evolueren5'] = $code;
						else if (!$_SESSION['evolueren6'] && $_SESSION['evolueren'] != $code && $_SESSION['evolueren2'] != $code && $_SESSION['evolueren3'] != $code && $_SESSION['evolueren4'] != $code && $_SESSION['evolueren5'] != $code)	$_SESSION['evolueren6'] = $code;
					}
				}
			} else {
				return true;
			}
		} else	return true;
	}
}

//Als pokemon level groeit
function nieuwestats($pokemon, $levelnieuw, $nieuwexp) {
	//Gegevens opzoeken in de experience tabel en karakter tabel
	$explevel = $levelnieuw + 1;
	if ($explevel < 101)
		$info = DB::exQuery("SELECT `experience`.`punten`,`karakters`.* FROM `experience` INNER JOIN `karakters` WHERE `experience`.`soort`='" . $pokemon['groei'] . "' AND `experience`.`level`='" . $explevel . "' AND `karakters`.`karakter_naam`='" . $pokemon['karakter'] . "' LIMIT 1")->fetch_assoc();
	else {
		$info = DB::exQuery("SELECT * FROM `karakters` WHERE `karakter_naam`='" . $pokemon['karakter'] . "' LIMIT 1")->fetch_assoc();
		$info['punten'] = 0;
	}
	//Exp bereken dat de pokemon over gehouden heeft en mee neemt naar het volgend level.
	$expover = $nieuwexp - $pokemon['expnodig'];

	/* Nieuwe stats en hp berekenen
	 * Bron: http://www.upokecenter.com/games/rs/guides/id.html
	 * Stats berekenen
	 * Formule Stats = int((int(int(A*2+B+int(C/4))*D/100)+5)*E)
	 */

	$attackstat		= round((((($pokemon['attack_iv'] + 2 * $pokemon['attack_base'] + floor($pokemon['attack_ev'] / 4)) * $levelnieuw / 100) + 5) + $pokemon['attack_up']) * $info['attack_add']);
	$defencestat	= round((((($pokemon['defence_iv'] + 2 * $pokemon['defence_base'] + floor($pokemon['defence_ev'] / 4)) * $levelnieuw / 100) + 5) + $pokemon['defence_up']) * $info['defence_add']) ;
	$speedstat		= round((((($pokemon['speed_iv'] + 2 * $pokemon['speed_base'] + floor($pokemon['speed_ev'] / 4)) * $levelnieuw / 100) + 5) + $pokemon['speed_up']) * $info['speed_add']);
	$spcattackstat	= round((((($pokemon['spc.attack_iv'] + 2 * $pokemon['spc.attack_base'] + floor($pokemon['spc.attack_ev'] / 4)) * $levelnieuw / 100) + 5) + $pokemon['spc_up']) * $info['spc.attack_add']);
	$spcdefencestat	= round((((($pokemon['spc.defence_iv'] + 2 * $pokemon['spc.defence_base'] + floor($pokemon['spc.defence_ev'] / 4)) * $levelnieuw / 100) + 5) + $pokemon['spc_up']) * $info['spc.defence_add']);
	if ($pokemon['wild_id'] != 292) {
		$hpstat		= round(((($pokemon['hp_iv'] + 2 * $pokemon['hp_base'] + floor($pokemon['hp_ev'] / 4)) * $levelnieuw / 100) + 10 + $levelnieuw) + $pokemon['hp_up']);
	} else {
		$hpstat 	= 1;
	}

	//Stats opslaan
	DB::exQuery("UPDATE `pokemon_speler` SET `level`='" . $levelnieuw . "',`levenmax`='" . $hpstat . "',`leven`='" . $hpstat . "',`exp`='" . $expover . "',`expnodig`='" . $info['punten'] . "',`attack`='" . $attackstat . "',`defence`='" . $defencestat . "',`speed`='" . $speedstat . "', `spc.attack`='" . $spcattackstat . "', `spc.defence`='" . $spcdefencestat . "', `effect`='', `hoelang`='' WHERE `id`='" . $pokemon['id'] . "' LIMIT 1");
	return $info['punten'];
}

function max_calc($pokemon, $nature) {
	$levelnieuw = 100;
	$pokemon['karakter'] = $nature;
	$info = DB::exQuery("SELECT * FROM `karakters` WHERE `karakter_naam`='" . $pokemon['karakter'] . "' LIMIT 1")->fetch_assoc();
	$info['punten'] = 0;

	/* Nieuwe stats en hp berekenen
	 * Bron: http://www.upokecenter.com/games/rs/guides/id.html
	 * Stats berekenen
	 * Formule Stats = int((int(int(A*2+B+int(C/4))*D/100)+5)*E)
	 */

	$attackstat		= round(((((31 + 2 * $pokemon['attack_base'] + floor(255 / 4)) * $levelnieuw / 100) + 5) + 75) * $info['attack_add']);
	$defencestat	= round(((((31 + 2 * $pokemon['defence_base'] + floor(255 / 4)) * $levelnieuw / 100) + 5) + 75) * $info['defence_add']) ;
	$speedstat		= round(((((31 + 2 * $pokemon['speed_base'] + floor(255 / 4)) * $levelnieuw / 100) + 5) + 75) * $info['speed_add']);
	$spcattackstat	= round(((((31 + 2 * $pokemon['spc.attack_base'] + floor(255 / 4)) * $levelnieuw / 100) + 5) + 75) * $info['spc.attack_add']);
	$spcdefencestat	= round(((((31 + 2 * $pokemon['spc.defence_base'] + floor(255 / 4)) * $levelnieuw / 100) + 5) + 75) * $info['spc.defence_add']);
	if ($pokemon['wild_id'] != '292') {
		$hpstat		= round((((31 + 2 * $pokemon['hp_base'] + floor(255 / 4)) * $levelnieuw / 100) + 10 + $levelnieuw) + 75);
	} else {
		$hpstat 	= 1;
	}

	$array = array($hpstat, $attackstat, $defencestat, $spcattackstat, $spcdefencestat, $speedstat); 

	//Stats opslaan
	return $array;
}

//Tabel welke pokemon level je tegenkomt
function rankpokemon($ranknummer) {
	if ($ranknummer == 1)		return 5;
	else if ($ranknummer == 2)	return rand(5, 10);
	else if ($ranknummer == 3)	return rand(5, 15);
	else if ($ranknummer == 4)	return rand(8, 20);
	else if ($ranknummer == 5)	return rand(10, 25);
	else if ($ranknummer == 6)	return rand(13, 30);
	else if ($ranknummer == 7)	return rand(15, 35);
	else if ($ranknummer == 8)	return rand(18, 40);
	else if ($ranknummer == 9)	return rand(20, 45);
	else if ($ranknummer == 10)	return rand(25, 50);
	else if ($ranknummer == 11)	return rand(28, 55);
	else if ($ranknummer == 12)	return rand(30, 60);
	else if ($ranknummer == 13)	return rand(33, 65);
	else if ($ranknummer == 14)	return rand(35, 70);
	else if ($ranknummer == 15)	return rand(38, 75);
	else if ($ranknummer == 16)	return rand(40, 80);
	else if ($ranknummer == 17)	return rand(43, 85);
	else if ($ranknummer == 18)	return rand(45, 90);
	else if ($ranknummer == 19)	return rand(48, 95);
	else if ($ranknummer >= 20 && $ranknummer <= 26)	return rand(50, 100);
	else if ($ranknummer == 27)	return rand(55, 100);
	else if ($ranknummer == 28)	return rand(60, 100);
	else if ($ranknummer == 29)	return rand(65, 100);
	else if ($ranknummer == 30)	return rand(70, 100);
	else if ($ranknummer == 31)	return rand(80, 100);
	else if ($ranknummer == 32)	return rand(90, 100);
	else if ($ranknummer >= 33) return rand(50, 100);
	else return 5;
}

//Zinnen splitsen als ze te lang zijn.
function anti_langezin($zin) {
	//Werkt niet goed
	//Zit er geen teken in voor ubb?
	//Beide ubb tekens vastellen en zoeken
	//Als beide tekens niet gevonden zijn is het goed
	if (strpos($zin, "[") === false) {
		if (strpos($zin, "]") === false) {
			//Zin opblasen bij een spatie
			$woord = explode(" ", $zin);
			//ELk woord apart bekijken
			for($i=0;$i<=sizeof($woord);++$i) {
				//Is het langer dan 50 tekens, afkappen
				if (strlen($woord[$i]) > 50)	$woord[$i] = wordwrap($woord[$i], 50, "\n", true);
				//Woord toevoegen
				$woorden[] = $woord[$i];
			}
			$zinnn = '';
			//Voor elke 15 woorden een zin maken
			for($ii=0;$ii<count($woorden);$ii += 15) {
				$zinnn .= $woorden[$ii] . " " . $woorden[$ii + 1] . " " . $woorden[$ii + 2] . " " . $woorden[$ii + 3] . " " . $woorden[$ii + 4] . " " . $woorden[$ii + 5] . " " . $woorden[$ii + 6] . " " . $woorden[$ii + 7] . " " . $woorden[$ii + 8] . " " . $woorden[$ii + 9] . " " . $woorden[$ii + 10] . " " . $woorden[$ii + 11] . " " . $woorden[$ii + 12] . " " . $woorden[$ii + 13] . " " . $woorden[$ii + 14];
			}
			//Te lange zinnen opsplitsen
			return wordwrap($zinnn, 50, "\n", true);
		}
	} else {
	    return $zin;
    }
}

function gebruiker_popup($gebruiker, $txt) {
    global $static_url;
    $gebruiker_rank = rank($gebruiker['rank']);
    if ($gebruiker['rankexp'] > 0)  $gebruiker_rank['procent'] = round(($gebruiker['rankexp'] / $gebruiker['rankexpnodig']) * 100);
    else  $gebruiker_rank['procent'] = 0;

    $arr = explode(",", $gebruiker['pok_bezit']);
    $result = array_unique($arr);
    $gebruiker_pokemon['procent'] = round((count($result) / DB::exQuery("SELECT `wild_id` FROM `pokemon_wild`")->num_rows) * 100);

    $pokemon_sql = DB::exQuery("SELECT pw.naam, pw.type1, pw.type2, pw.zeldzaamheid, pw.groei, pw.aanval_1, ps.humor_change, pw.aanval_2, pw.aanval_3, pw.aanval_4, ps.* FROM pokemon_wild AS pw INNER JOIN pokemon_speler AS ps ON ps.wild_id = pw.wild_id WHERE ps.user_id='".$gebruiker['user_id']."' AND ps.opzak='ja' ORDER BY ps.opzak_nummer ASC");
    $gebruiker['in_hand'] = $pokemon_sql->num_rows;

    if ($gebruiker['premiumaccount'] > time()) $premium = "<img src='".$static_url."/images/icons/vip.gif' style='vertical-align:-3px;'>";

    $return = '<div class=\'box-content\'><table class=\'general\' style=\'width:320px;\'>
		<thead><tr><th colspan=\'3\' style=\'text-align:center;\'>' . $gebruiker['username'] . ' '.$premium.'</th></tr></thead>
		<tbody>
			<tr>
				<td width=\'118\'><b>&raquo; Região</b></td>
				<td width=\'10\'><img src=\'' . $static_url . '/images/icons/wereld.png\' title=\'Região\' /></td>
				<td style=\'min-width: 192px;\'>' . $gebruiker['wereld'] . '</td>
			</tr>
			<tr>
				<td><b>&raquo; Rank</b></td>
				<td><img src=\'' . $static_url . '/images/icons/statistieken_leden.png\' /></td>
				<td><div class=\'stats-container\'>
					<div style=\'width:' . $gebruiker_rank['procent'] . '%;max-width:100%;\'><span style=\'position:absolute;\'>' . $gebruiker_rank['procent'] . '% - ' . $gebruiker_rank['ranknaam'] . '</span></div>
				</div></td>
			</tr>
			<tr>
				<td><b>&raquo; Pokédex</b></td>
				<td><img src=\'' . $static_url . '/images/icons/statistieken_online.png\' /></td>
				<td><div class=\'stats-container\'>
					<div class=\'bluepr\' style=\'width:' . $gebruiker_pokemon['procent'] . '%;max-width:100%;\'><span style=\'position:absolute;\'>' . $gebruiker_pokemon['procent'] . '%</span></div>
				</div></td>
			</tr>
			<tr>
				<td><b>&raquo; Silver</b></td>
				<td><img src=\'' . $static_url . '/images/icons/silver.png\' title=\'Silver\' /></td>
				<td>' . highamount($gebruiker['silver']) . '</td>
			</tr>
			<tr>
				<td><b>&raquo; Dias</b></td>
				<td><img src=\'' . $static_url . '/images/icons/calendar.png\' title=\'Antiguidade\' /></td>
				<td>' . ($gebruiker['antiguidade']) . ' dias</td>
			</tr>';
    if ($gebruiker['in_hand'] > 0) {
        $return .= '<tr>
					<td><b>&raquo; Time</b></td>
					<td><img src=\'' . $static_url . '/images/icons/pokeball.gif\' title=\'' . $txt['stats_bank'] . '\' /></td>
					<td style=\'padding:0;\'>';
        while($pokemon = $pokemon_sql->fetch_assoc()) {
            $pokemon = pokemonei($pokemon, $txt);
            $return .= '<div align=\'center\' style=\'display:inline-block;padding:0;\'><img src=\'' . $static_url . '/' . $pokemon['animatie'] . '\' class=\'pokemon_mini\' /></div>';
        }
        $return .= '</td>
				</tr>';
    }
    $return .= '</tbody></table></div>';
    return $return;
}

function pokedex_popup($pokemon, $txt) {
	global $static_url;

	$pokemon['powertotal'] = $pokemon['attack'] + $pokemon['defence'] + $pokemon['speed'] + $pokemon['spc.attack'] + $pokemon['spc.defence'];
	$pokemon['real_id'] = real_id($pokemon);

	$ability = ability($pokemon['ability']);

	$shiny = "";
	if ($pokemon['shiny'] == 1)	$shiny = "<img src='" . $static_url . "/images/icons/lidbetaald.png' />";

	if (empty($pokemon['aanval_1']))	$aanval1 = "";
	else	$aanval1 = "<a href='./information&amp;category=attack-info&amp;attack=" . $pokemon['aanval_1'] . "'>" . $pokemon['aanval_1'] . "</a>";
	if (empty($pokemon['aanval_2']))	$aanval2 = "";
	else	$aanval2 = " | <a href='./information&amp;category=attack-info&amp;attack=" . $pokemon['aanval_2'] . "'>" . $pokemon['aanval_2'] . "</a>";
	if (empty($pokemon['aanval_3']))	$aanval3 = "";
	else	$aanval3 = "<br /><a href='./information&amp;category=attack-info&amp;attack=" . $pokemon['aanval_3'] . "'>" . $pokemon['aanval_3'] . "</a>";
	if (empty($pokemon['aanval_4']))	$aanval4 = "";
	else	$aanval4 = " | <a href='./information&amp;category=attack-info&amp;attack=" . $pokemon['aanval_4'] . "'>" . $pokemon['aanval_4'] . "</a>";

	if (empty($pokemon['type2']))	$pokemon['type'] = '<table><tr><td><div class=\'type-icon type-' . strtolower($pokemon['type1']) . '\'>' . $pokemon['type1'] . '</div></td></tr></table>';
	else	$pokemon['type'] = '<table><tr><td><div class=\'type-icon type-' . strtolower($pokemon['type1']) . '\'>' . $pokemon['type1'] . '</div></td><td> <div class=\'type-icon type-' . strtolower($pokemon['type2']) . '\'>' . $pokemon['type2'] . '</div></td></tr></table>';

    $raridade = DB::exQuery("SELECT `nome` FROM `zeldzaamheid` WHERE `id`='".$pokemon['zeldzaamheid']."'")->fetch_assoc()['nome'];
	$eredmeny = ceil($pokemon['vangbaarheid'] / (255 / 100));
	
	$top3 = ''; 
	if ($pokemon['top3'] != '') $top3 = '<img src=\'' . $static_url . '/images/icons/medal' . $pokemon['top3'] . '.png\' title=\'Top ' . $pokemon['top3'] . ' Pokémon\' />';
	
	$return = '<div class=\'box-content\' style=\'width: 350px;\'><table width=\'100%\' class=\'general\'>
		<thead>
			<tr><th colspan=\'3\' style=\'text-align: left; padding: 5px 0;\'>
				#' . $pokemon['real_id'] . ' - ' . $pokemon['naam'] . '
				<span style=\'float: right;\'>'. $shiny .'(Nv. ' . $pokemon['level'] . ')</span>
			</th></tr>
		</thead>
		<tr>
			<td rowspan=\'5\' class=\'no-padding\' style=\'background: url(' . $static_url . '/images/' . $pokemon['map'] . '/' . $pokemon['wild_id'] . '.gif) center center no-repeat; width: 120px;\' align=\'center\' valign=\'top\'></td>
			<td class=\'no-padding\'><b>Chance de captura:</b></td>
			<td class=\'no-padding\' style=\'text-align: center\'>' . $eredmeny . '%</td>
		</tr>
		<tr>
			<td class=\'no-padding\'><b>Ability:</b></td>
			<td class=\'no-padding\' style=\'text-align: center\'><div title=\''.$ability['descr'].'\'>' . $ability['name'] . '</div></td>
		</tr>
		<tr>
			<td class=\'no-padding\'><b>Poder total:</b></td>
			<td class=\'no-padding\' style=\'text-align: center\'>' . highamount($pokemon['powertotal']) . '</td>
		</tr>
		<tr>
			<td colspan=\'2\' style=\'padding: 0;\'><table width=\'100%\' class=\'general\'>
				<tr>
					<td align=\'center\' width=\'16.6%\' class=\'no-padding\'><img src=\'' . $static_url . '/images/icons/stats/stat_hp.png\' title=\'HP\' width=\'16\' height=\'16\' /></td>
					<td align=\'center\' width=\'16.6%\' class=\'no-padding\'><img src=\'' . $static_url . '/images/icons/stats/stat_at.png\' title=\'Ataque\' width=\'16\' height=\'16\' /></td>
					<td align=\'center\' width=\'16.6%\' class=\'no-padding\'><img src=\'' . $static_url . '/images/icons/stats/stat_de.png\' title=\'Defesa\' width=\'16\' height=\'16\' /></td>
					<td align=\'center\' width=\'16.6%\' class=\'no-padding\'><img src=\'' . $static_url . '/images/icons/stats/stat_sa.png\' title=\'Esp. Ataque\' width=\'16\' height=\'16\' /></td>
					<td align=\'center\' width=\'16.6%\' class=\'no-padding\'><img src=\'' . $static_url . '/images/icons/stats/stat_sd.png\' title=\'Esp. Defesa\' width=\'16\' height=\'16\' /></td>
					<td align=\'center\' width=\'16.6%\' class=\'no-padding\'><img src=\'' . $static_url . '/images/icons/stats/stat_sp.png\' title=\'Speed\' width=\'16\' height=\'16\' /></td>
				</tr>
				<tr>
					<td align=\'center\' class=\'no-padding\'>' . $pokemon['levenmax'] . '<sup>'.$pokemon['effort_hp'].'</sup></td>
					<td align=\'center\' class=\'no-padding\'>' . $pokemon['attack'] . '<sup>'.$pokemon['effort_attack'].'</sup></td>
					<td align=\'center\' class=\'no-padding\'>' . $pokemon['defence'] . '<sup>'.$pokemon['effort_defence'].'</sup></td>
					<td align=\'center\' class=\'no-padding\'>' . $pokemon['spc.attack'] . '<sup>'.$pokemon['effort_spc.attack'].'</sup></td>
					<td align=\'center\' class=\'no-padding\'>' . $pokemon['spc.defence'] . '<sup>'.$pokemon['effort_spc.defence'].'</sup></td>
					<td align=\'center\' class=\'no-padding\'>' . $pokemon['speed'] . '<sup>'.$pokemon['effort_speed'].'</sup></td>
				</tr>
			</table></td>
		</tr>
		<tr><td class=\'no-padding\' colspan=\'2\' align=\'center\'><b>Este pokémon é <u>' . $raridade . '</u>.</b></td></tr>
		<tr>
			<td class=\'no-padding\' align=\'center\'>' . $pokemon['type'] . '</td>
			<td align=\'center\' colspan=\'2\' style=\'font-size: x-small;\' class=\'no-padding\'>' . $aanval1 . $aanval2 . $aanval3 . $aanval4 . '</td>
		</tr>
	</table></div>';
	return $return;
}

function isOwner ($id, $admin, $opzak, $method = 'direct') {
	if (($opzak == 'tra' && $method != 'private') || $id == $_SESSION['id'] || $admin >= 3) {
		return true;
	}

	return false;
}

function pokemon_popup($pokemon, $txt) {
	global $static_url;

	if ( $pokemon['attack'] == '??' ) {
		$pokemon['powertotal'] = '??';
	} else {
		$pokemon['powertotal'] = $pokemon['attack'] + $pokemon['defence'] + $pokemon['speed'] + $pokemon['spc.attack'] + $pokemon['spc.defence'];	
	}

	$pokemon['real_id'] = real_id($pokemon);

	$shiny = "";
	if ($pokemon['shiny'] == 1)	$shiny = "<img src='" . $static_url . "/images/icons/lidbetaald.png' />";

	$gehecht = '';
	if ($pokemon['gehecht'] == 1) $gehecht = '<img src=\'' . $static_url . '/images/icons/friend.png\' title=\'Inicial\' />';

	if (empty($pokemon['aanval_1']))	$aanval1 = "";
	else	$aanval1 = "<a href='./information&amp;category=attack-info&amp;attack=" . $pokemon['aanval_1'] . "'>" . $pokemon['aanval_1'] . "</a>";
	if (empty($pokemon['aanval_2']))	$aanval2 = "";
	else	$aanval2 = " | <a href='./information&amp;category=attack-info&amp;attack=" . $pokemon['aanval_2'] . "'>" . $pokemon['aanval_2'] . "</a>";
	if (empty($pokemon['aanval_3']))	$aanval3 = "";
	else	$aanval3 = "<br /><a href='./information&amp;category=attack-info&amp;attack=" . $pokemon['aanval_3'] . "'>" . $pokemon['aanval_3'] . "</a>";
	if (empty($pokemon['aanval_4']))	$aanval4 = "";
	else	$aanval4 = " | <a href='./information&amp;category=attack-info&amp;attack=" . $pokemon['aanval_4'] . "'>" . $pokemon['aanval_4'] . "</a>";

	$top3 = '';
	if ($pokemon['top3'] != '') { 
		$top3 = '<img src=\'' . $static_url . '/images/icons/medal' . $pokemon['top3'] . '.png\' title=\'Top ' . $pokemon['top3'] . ' Pokémon\' />';

		if ($pokemon['top3'] == 1) {
			$color = ['FFEB3B', 'FFEB39'];	
		} else if ($pokemon['top3'] == 2) {
			$color = ['9BBDDA', 'E9F6FD'];
		} else {
			$color = ['D5772A', 'FAC388'];	
		}

		$pokemon['def_naam'] = "<span style='text-shadow: 0 0 3px #".$color[0].", 0 0 5px #".$color[1]."; font-weight: 600;'>".$pokemon['def_naam']."</span>"; 
	}

	$item = '';
	if (!empty($pokemon['item'])) $item = '<span style=\'float: left; margin-top: 155px;\'><img src=\'' . $static_url . '/images/items/' . $pokemon['item'] . '.png\' title=\'' . $pokemon['item'] . '\' /></span>';

	$profile = '';
	if (isset($_SESSION['id'])) $profile = "<a href='./pokemon-profile&id=".$pokemon['id']."' style='vertical-align: middle'><img src='".$static_url."/images/icons/info.png' title='Ver Perfil do Pokémon'></a>";

	$return = '<div class=\'box-content\' style=\'width: 415px;\'><table width=\'100%\' class=\'general\'>
		<thead>
			<tr><th colspan=\'8\' style=\'padding: 5px 0; vertical-align: middle\'>
				'.$profile.' #' . $pokemon['real_id'] . ' - ' . pokemon_naam($pokemon['naam'], '', $pokemon['icon']) . '<span>'. $shiny . $top3 .' (Nv. ' . $pokemon['level'] . ')</span>
			</th></tr>
		</thead>
		<tbody>
			<tr>
				<td colspan=\'8\' class=\'no-padding\' align=\'center\' style=\'background-color: #34465f\'>' . $pokemon['type'] . '</td>
			</tr>
			<tr>
				<td class=\'no-padding\'><b>ID: '.$pokemon['id'].'</b></td>
				<td rowspan=\'5\' class=\'no-padding\' style=\'background: url(' . $static_url . '/'.$pokemon['link'].') center center no-repeat; width: 130px; border: 1px solid #577599;\' align=\'center\' valign=\'top\'>
					' . $gehecht . '
					<span style=\'float: right; margin-top: 155px;\'><img src=\'' . $static_url . '/images/items/' . $pokemon['gevongenmet'] . '.png\' title=\'' . $pokemon['gevongenmet'] . '\' /></span>
					'.$item.'
				</td>
				<td align=\'center\' class=\'no-padding bordered\' width=\'30\'><img src=\'' . $static_url . '/images/icons/stats/stat_hp.png\' title=\'HP\' width=\'16\' height=\'16\' /></td>
				<td class=\'no-bordered\' width=\'5\'></td>
				<td align=\'center\' class=\'no-padding bordered\' width=\'30\'><img src=\'' . $static_url . '/images/icons/stats/stat_at.png\' title=\'Ataque\' width=\'16\' height=\'16\' /></td>
				<td class=\'no-bordered\' width=\'5\'></td>
				<td align=\'center\' class=\'no-padding bordered\' width=\'30\'><img src=\'' . $static_url . '/images/icons/stats/stat_de.png\' title=\'Defesa\' width=\'16\' height=\'16\' /></td>
			</tr>
			<tr>
				<td class=\'no-padding\'><b>Apelido: '.$pokemon['roepnaam'].'</b></td>
				<td align=\'center\' class=\'no-padding bordered\'>' . (!isOwner($pokemon['user_id'], 1, $pokemon['opzak']) ? '??' : $pokemon['levenmax']) . '<sup>' . (!isOwner($pokemon['user_id'], 1, $pokemon['opzak']) ? '?' : ($pokemon['hp_ev'] != 0 ? $pokemon['hp_ev'] : '')) . '</sup></td>
				<td class=\'no-bordered\'></td>
				<td align=\'center\' class=\'no-padding bordered\'>' . (!isOwner($pokemon['user_id'], 1, $pokemon['opzak']) ? '??' : $pokemon['attack']) . '<sup>' . (!isOwner($pokemon['user_id'], 1, $pokemon['opzak']) ? '?' : ($pokemon['attack_ev'] != 0 ? $pokemon['attack_ev'] : '')) . '</sup></td>
				<td class=\'no-bordered\'></td>
				<td align=\'center\' class=\'no-padding bordered\'>' . (!isOwner($pokemon['user_id'], 1, $pokemon['opzak']) ? '??' : $pokemon['defence']) . '<sup>' . (!isOwner($pokemon['user_id'], 1, $pokemon['opzak']) ? '?' : ($pokemon['defence_ev'] != 0 ? $pokemon['defence_ev'] : '')) . '</sup></td>
			</tr>
			<tr>
				<td class=\'no-padding\'><b>Humor: '. $pokemon['karakter'] . ($pokemon['humor_change'] != 0 ? ' <sup>' . $pokemon['humor_change'] . '</sup>' : '') .'</b></td>
				<td align=\'center\' class=\'no-padding bordered\'><img src=\'' . $static_url . '/images/icons/stats/stat_sa.png\' title=\'Esp. Ataque\' width=\'16\' height=\'16\' /></td>
				<td class=\'no-bordered\'></td>
				<td align=\'center\' class=\'no-padding bordered\'><img src=\'' . $static_url . '/images/icons/stats/stat_sd.png\' title=\'Esp. Defesa\' width=\'16\' height=\'16\' /></td>
				<td class=\'no-bordered\'></td>
				<td align=\'center\' class=\'no-padding bordered\'><img src=\'' . $static_url . '/images/icons/stats/stat_sp.png\' title=\'Speed\' width=\'16\' height=\'16\' /></td>
			</tr>
			<tr>
				<td class=\'no-padding\'><b>Negociavel: '. ($pokemon['can_trade'] == '0' ? 'Não negociavel' : 'Negociavel') .'</b></td>
				<td align=\'center\' class=\'no-padding bordered\'>' . (!isOwner($pokemon['user_id'], 1, $pokemon['opzak']) ? '??' : $pokemon['spc.attack']) . '<sup>' . (!isOwner($pokemon['user_id'], 1, $pokemon['opzak']) ? '?' : ($pokemon['spc.attack_ev'] != 0 ? $pokemon['spc.attack_ev'] : '')) . '</sup></td>
				<td class=\'no-bordered\'></td>
				<td align=\'center\' class=\'no-padding bordered\'>' . (!isOwner($pokemon['user_id'], 1, $pokemon['opzak']) ? '??' : $pokemon['spc.defence']) . '<sup>' . (!isOwner($pokemon['user_id'], 1, $pokemon['opzak']) ? '?' : ($pokemon['spc.defence_ev'] != 0 ? $pokemon['spc.defence_ev'] : '')) . '</sup></td>
				<td class=\'no-bordered\'></td>
				<td align=\'center\' class=\'no-padding bordered\'>' . (!isOwner($pokemon['user_id'], 1, $pokemon['opzak']) ? '??' : $pokemon['speed']) . '<sup>' . (!isOwner($pokemon['user_id'], 1, $pokemon['opzak']) ? '?' : ($pokemon['speed_ev'] != 0 ? $pokemon['speed_ev'] : '')) . '</sup></td>
			</tr>
			<tr>
				<td class=\'no-padding\'><b>Poder total: '. highamount($pokemon['powertotal']) .'</b></td>
				<td align=\'center\' class=\'no-padding bordered\'><img src=\'' . $static_url . '/images/items/Protein.png\' title=\'Protein\' width=\'16\' height=\'16\' /></td>
				<td align=\'center\' class=\'no-padding bordered\'><img src=\'' . $static_url . '/images/items/Iron.png\' title=\'Iron\' width=\'16\' height=\'16\' /></td>
				<td align=\'center\' class=\'no-padding bordered\'><img src=\'' . $static_url . '/images/items/Carbos.png\' title=\'Carbos\' width=\'16\' height=\'16\' /></td>
				<td align=\'center\' class=\'no-padding bordered\'><img src=\'' . $static_url . '/images/items/HP up.png\' title=\'HP up\' width=\'16\' height=\'16\' /></td>
				<td align=\'center\' class=\'no-padding bordered\'><img src=\'' . $static_url . '/images/items/Calcium.png\' title=\'Calcium\' width=\'16\' height=\'16\' /></td>
			</tr>
			<tr>
				<td align=\'center\' colspan=\'2\' style=\'font-size: x-small; height: 40px\' class=\'no-padding\'>' . $aanval1 . $aanval2 . $aanval3 . $aanval4 . '</td>
				<td align=\'center\' class=\'no-padding bordered\'>' . (!isOwner($pokemon['user_id'], 1, $pokemon['opzak']) ? '??' : ($pokemon['attack_up'] / 3)) . '</td>
				<td align=\'center\' class=\'no-padding bordered\'>' . (!isOwner($pokemon['user_id'], 1, $pokemon['opzak']) ? '??' : ($pokemon['defence_up'] / 3)) . '</td>
				<td align=\'center\' class=\'no-padding bordered\'>' . (!isOwner($pokemon['user_id'], 1, $pokemon['opzak']) ? '??' : ($pokemon['speed_up'] / 3)) . '</td>
				<td align=\'center\' class=\'no-padding bordered\'>' . (!isOwner($pokemon['user_id'], 1, $pokemon['opzak']) ? '??' : ($pokemon['hp_up'] / 3)) . '</td>
				<td align=\'center\' class=\'no-padding bordered\'>' . (!isOwner($pokemon['user_id'], 1, $pokemon['opzak']) ? '??' : ($pokemon['spc_up'] / 3)) . '</td>
			</tr>
		</tbody>
	</table></div>';
	
	if (isOwner($pokemon['user_id'], 1, $pokemon['opzak'])) {
		$return .= '<div class=\'box-content\' style=\'width: 415px; margin-top: 7px\'><table width=\'100%\' class=\'general\'>';
		if ($pokemon['ei'] == 1)	{ 
			$return .= '<script>$(\'.timer-'.$pokemon['id'].'\').startTimer();</script><tr><td colspan=\'2\' align=\'center\'><b>O ovo chocará em: <span class=\'timer-'.$pokemon['id'].'\' data-seconds-left=\''.(strtotime('+10 minutes', strtotime($pokemon['ei_tijd'])) - strtotime(date('Y-m-d H:i:s'))).'\'></span></b></td></tr>';
		} else {
			$return .= '<tr>
				<td width=\'34\'><b>HP:</b></td>
				<td width=\'266\'><div class=\'bar_red\'>
					<div class=\'progress\' style=\'width: ' . $pokemon['levenprocent'] . '%;\'></div>
				</div></td>
			</tr>
			<tr>
				<td><b>Exp:</b></td>
				<td><div class=\'bar_blue\'>
					<div class=\'progress\' style=\'width: ' . $pokemon['expprocent'] . '%;\'></div>
				</div></td>
			</tr>';
		}

		$return .= '</table></div>';
	}

	return $return;
}
function real_id ($pokemon) {
	$id = $pokemon['wild_id'];
	$real_id = DB::exQuery ("SELECT `real_id` FROM `pokemon_wild` WHERE wild_id='$id'")->fetch_assoc()['real_id'];
	if ($real_id < 10) {
		$real_id = '00'.$real_id;
	} else if ($real_id >= 10 && $real_id <= 99) {
		$real_id = '0'.$real_id;
	}
	return $real_id;
}
function poke_icons ($icon) {
	$q = DB::exQuery("SELECT * FROM `pokemon_icons` WHERE id='$icon'")->fetch_assoc();
	return $q;
}
//Maak pokemon naam goed ivm roepnaam & male/female
function pokemon_naam($oud, $roepnaam, $icon = '') {
	global $static_url;
	$new_name = $oud;
	if (!empty($roepnaam))	$new_name = $roepnaam;
	else if (preg_match('/ /', $oud)) {
		$pokemon = explode(" ", $oud);
		if ($pokemon[1] == "f")	$new_name = $pokemon[0] . " &#9792;";
		else if ($pokemon[1] == "m")	$new_name = $pokemon[0] . " &#9794;";
		else	$new_name = $oud;
	}

	if (!empty($icon)) {
		$icon = poke_icons ($icon);
		$icon = ' <img src=\'' . $static_url . '/images/poke_icons/' . $icon['img'] . '.png\' title=\'' . $icon['name'] . '\' />';
		$new_name .= $icon;
	}
	return $new_name;
}

//Maak Computer naam goed ivm male/female
function computer_naam($old) {
	//Staat er een f/m achter de naam Male/Female Character maken
	if (preg_match('/ /', $old)) {
		$pokemon = explode(" ", $old);
		if ($pokemon[1] == "f")	return $pokemon[0] . " &#9792;";
		else if ($pokemon[1] == "m")	return $pokemon[0] . " &#9794;";
		else	return $old;
	} else	return $old;
}

//Pokemonei function
function pokemonei($geg, $txt) {
	global $static_url;

	if ($geg['ei'] == 1) {
		$ei = true;

		//Beide tijden opvragen, en strtotime van maken
		$tijdtoen = strtotime($geg['ei_tijd']);
		$new['ei_tijd'] = $geg['ei_tijd'];
		$tijdnu = time();
		$egg_array = [1, 4, 7, 144, 145, 146, 150, 151, 152, 155, 158, 243, 244, 245, 249, 250, 251, 252, 255, 258, 377, 378, 379, 380, 381, 382, 383, 384, 385, 386, 387, 390, 393, 479, 480, 481, 482, 483, 484, 485, 486, 487, 488, 489, 490, 491, 492, 493, 494, 495, 498, 501, 638, 639, 640, 641, 642, 643, 644, 645, 646, 647, 648, 649, 650, 653, 646, 716, 717, 718];

		$new['user_id'] = $geg['user_id'];
		//Is er geen tijd dus niet goed geactieveerd, geen pokemon
		if (empty($tijdtoen)) {
			//Link maken voor het plaatje van de pokemon
			$new['animatie'] = "/images/icons/egg.gif";
			$new['little'] = "/images/icons/egg_big.gif";
			if (in_array($geg['wild_id'], $egg_array)) {
				$new['link'] = "/images/pokemon/egg/".$geg['wild_id']."egg.gif";
			} else {
				$new['link'] = "/images/icons/egg_big.gif";
			}

			//Geen leven opgeven
			$new['levenproc'] = "";

			//Andere naam voor de pokemon en de level
			$new['naam'] = "";
			$new['level'] = "";
			$new['ei'] = 1;
		} else if ($tijdnu - $tijdtoen < 300) {	//Als het verschil minder dan 600 sec is, dan hele ei
			//Bereken hoeveel tijd er nog over is
			$new['tijdover'] = 600 - ($tijdnu - $tijdtoen);
			//$new['afteltijd'] = strftime("%M:%S", $new['tijdover']);
			$new['afteltijd'] = formatTime($new['tijdover']);

			//Link maken voor het plaatje van de pokemon
			$new['animatie'] = "/images/icons/egg.gif";
			$new['little'] = "/images/icons/egg_big.gif";
			if (in_array($geg['wild_id'], $egg_array)) {
				$new['link'] = "/images/pokemon/egg/".$geg['wild_id']."egg.gif";
			} else {
				$new['link'] = "/images/icons/egg_big.gif";
			}

			//Geen leven opgeven
			$new['levenproc']   = sprintf($txt['pokemonei_egg'], $new['afteltijd']);

			//Alles andere naam toewijzen
			$new['ei'] = 1;
			$new['wild_id'] = '??';
			$new['naam'] = "??";
			$new['def_naam'] = "??";
			$new['roepnaam'] = "??";
			$new['id'] = $geg['id'];
			$new['attack'] = "??";
			$new['leven'] = "??";
			$new['levenmax'] = "??";
			$new['defence'] = "??";
			$new['type1'] = "??";
			$new['type2'] = "??";
			$new['speed'] = "??";
			$new['level'] = "??";
			$new['exp'] = "??";
			$new['totalexp'] = "??";
			$new['expnodig'] = "??";
			$new['spc.attack'] = "??";
			$new['spc.defence']  = "??";
			$new['lvl_hook'] = "(Nv. ??)";
			$new['level_1'] = "-";
			$new['type'] = "<div style='padding-left:2px'>??</div>";
			$new['gevongenmet'] = 'Poke ball';
			$new['karakter'] = "??";
			$new['aanval_1'] = "??";
			$new['aanval_2'] = "??";
			$new['aanval_3'] = "??";
			$new['aanval_4'] = "??";
		} else if ($tijdnu - $tijdtoen < 600) {  //Als het verschil meer dan 600 sec is maar minder dan 900 dan halve ei
			//Bereken hoeveel tijd er nog over is
			$new['tijdover'] = 600 - ($tijdnu - $tijdtoen);
			//$new['afteltijd'] = strftime("%M:%S", $new['tijdover']);
			$new['afteltijd'] = formatTime($new['tijdover']);

			//Link maken voor het plaatje van de pokemon
			if (in_array($geg['wild_id'], $egg_array)) {
				$new['link'] = "/images/pokemon/egg/".$geg['wild_id']."egg.gif";
			} else {
				$new['link'] = "/images/icons/egg_big.gif";
			}
			$new['little'] = "/images/icons/egg_big.gif";
			$new['animatie'] = "/images/icons/egg_hatching.gif";

			//Geen leven opgeven
			$new['levenproc'] = sprintf($txt['pokemonei_egg'], $new['afteltijd']);

			//Alles andere naam toewijzen
			$new['ei'] = 1;
			$new['wild_id'] = '??';
			$new['naam'] = "??";
			$new['def_naam'] = "??";
			$new['roepnaam'] = "??";
			$new['shiny'] = 0;
			$new['id'] = $geg['id'];
			$new['attack'] = "??";
			$new['leven'] = "??";
			$new['levenmax'] = "??";
			$new['defence'] = "??";
			$new['type1'] = "??";
			$new['type2'] = "??";
			$new['speed'] = "??";
			$new['level'] = "??";
			$new['exp'] = "??";
			$new['totalexp'] = "??";
			$new['expnodig'] = "??";
			$new['spc.attack'] = "??";
			$new['spc.defence'] = "??";
			$new['lvl_hook'] = "(Nv. ??)";
			$new['lvl_stripe'] = "-";
			$new['type'] = "<div style='padding-left:2px'>??</div>";
			$new['gevongenmet'] = 'Poke ball';
			$new['karakter'] = "??";
			$new['aanval_1'] = "??";
			$new['aanval_2'] = "??";
			$new['aanval_3'] = "??";
			$new['aanval_4'] = "??";
		} else	$ei = false;
	} else	$ei = false;
	if (!$ei) {
		//Link maken voor het plaatje van de pokemon \
		$new = array();
		foreach($geg as $key=>$value)
			if (!is_numeric($key))  $new[$key] = $value;

		$new['ei']        = 0;
		$new['naamklein'] = strtolower($geg['naam']);

		if ($new['wild_id'] == '649') {
			if (in_array($geg['item'], array('Burn Drive', 'Chill Drive', 'Douse Drive', 'Shock Drive'))) {
				$suffix = '-'.(array_search($geg['item'], array('Burn Drive', 'Chill Drive', 'Douse Drive', 'Shock Drive')) + 1);
			}
		} else if (in_array($new['wild_id'], array('585', '586'))) {
			$season = isSeason()[1];
			$suffix = '-'.$season;
		}
    
		##**##
		if ($geg['shiny'] == 1) {
			$new['link']     = "/images/shiny/" . $new['wild_id'] .$suffix. ".gif";
			$new['animatie'] = "/images/shiny/icon/" . $new['wild_id'] . ".gif";
		} else {
			$new['link']     = "/images/pokemon/" . $new['wild_id'] .$suffix. ".gif";
			$new['animatie'] = "/images/pokemon/icon/" . $new['wild_id'] . ".gif";
		}

		#Andere naam voor de pokemon en de level
		#Alles andere naam toewijzen   
		$new['karakter'] = ucfirst($geg['karakter']);
		$new['def_naam'] = $geg['naam'];

		if (empty($geg['roepnaam']))	$new['roepnaam'] = $geg['naam'];
		else {
			$new['roepnaam'] = $geg['roepnaam'];
			$new['naam']     = $geg['naam'];
		}

		if ($geg['leven'] > 0)	$new['levenprocent'] = round(($geg['leven'] / $geg['levenmax']) * 100);
		else	$new['levenprocent'] = 0;
		if ($geg['expnodig'] > 0)	$new['expprocent'] = round(($geg['exp'] / $geg['expnodig']) * 100);
		else	$new['expprocent'] = 0;

		$new['levenmin100'] = 100 - $new['levenprocent'];
		$new['type1'] = strtolower($geg['type1']);
		$new['type2'] = strtolower($geg['type2']);

		//Heeft de pokemon twee types?
		if (empty($new['type2']))	$new['type'] = '<table><tr><td><div class=\'type-icon type-' . $new['type1'] . '\'>' . $new['type1'] . '</div></td></tr></table>';
		else	$new['type'] = '<table><tr><td><div class=\'type-icon type-' . $new['type1'] . '\'>' . $new['type1'] . '</div></td><td> <div class=\'type-icon type-' . $new['type2'] . '\'>' . $new['type2'] . '</div></td></tr></table>';

		$new['lvl_hook']   = "(" . $txt['pokemonei_level'] . " " . $geg['level'] . ")";
		$new['level_1']    = $geg['level'];
		$new['expmin100']  = 100 - $new['expprocent'];
		$new['spcattack']  = $geg['spc.attack'];
		$new['spcdefence'] = $geg['spc.defence'];
	}

	return $new;
}
function checkIPConnection($from_user, $to_user) {
	$from_logins = array();
	$result = DB::exQuery("SELECT * FROM `inlog_logs` WHERE `speler`='" . $from_user['username'] . "'");
	while($row = $result->fetch_assoc()) {
		if (!in_array($row['ip'], $from_logins) && strtotime($row['datum']) >= (time() - (86400 * 15)))	$from_logins[] = $row['ip'];
	}

	$to_logins = array();
	$result2 = DB::exQuery("SELECT * FROM `inlog_logs` WHERE `speler`='" . $to_user['username'] . "'");
	while($row2 = $result2->fetch_assoc()) {
		if (!in_array($row2['ip'], $to_logins) && strtotime($row2['datum']) >= (time() - (86400 * 15)))	$to_logins[] = $row2['ip'];
	}

	$two_arrays = array_merge($from_logins, $to_logins);
	$unique_array = array_unique($two_arrays);
	if (count($two_arrays) != count($unique_array))	return true;
	else	return false;
}
function addNPCBox($image = false, $title = '', $text = '') {
	global $static_url, $page;
	$image = $image != true ? mt_rand(1, 31) : $image;
	if (!empty($title) && !empty($text)) {
		$return = '<div id="npc-section" data-npc="' . $page . '">';
			$return .= '<div id="npc-image" style="background: url(\'' . $static_url . '/images/npc/' . $image . '.png\') center center no-repeat; background-size: 100% 100%;"></div>';
			$return .= '<div id="npc-content">';
				$return .= '<h3>' . $title . '</h3>';
				$return .= '<p style="margin-bottom: 0;">' . $text . '</p>';
			$return .= '</div>';
		$return .= '</div>';
		return $return;
	}
	return false;
}
function formatTime($seconds) {
	$hour = 3600;
	$minutes = 60;

	$count_min = $count_hour = 0;

	while($hour <= $seconds) {
		$seconds -= $hour;
		++$count_hour;
	}
	while($minutes <= $seconds) {
		$seconds -= $minutes;
		++$count_min;
	}
	return sprintf('%01s', $count_hour) . ':' . sprintf('%02s', $count_min) . ':' . sprintf('%02s', $seconds);
}
function createKey($size = 6) {
	$letters = "abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
	$key = "";
	for($i=1;$i<=$size;++$i) {
		$key .= $letters{rand(0, 61)};
	}
	return $key;
}

$keyzitapass = "SENHAENCRYPTSIMBOLSPASSWORD2016";

function password($password) {
	$salt = md5(strrev($keyzitapass));

	return hash('sha1', crypt($password, md5(strrev($keyzitapass))));
}

function salvaLogAdmin($quem,$acao,$mensagem) {
	$ip = $_SERVER['REMOTE_ADDR']; // Salva o IP do visitante
	$hora = date('Y-m-d H:i:s'); // Salva a data e hora atual (formato MySQL)

	// Monta a query para inserir o log no sistema
	$sql = "INSERT INTO `logs` VALUES (NULL, '".$quem."', '".$hora."', '".$ip."', '".$acao."', '".$mensagem."')";

	DB::exQuery($sql);

	// mail('vinicius@pokemonsimbol.com', $acao, $mensagem);

}

function freeSlots () {
	$slots = 0;
	$slots += DB::exQuery("SELECT SUM(`Poke ball`+`Great ball`+`Ultra ball`+`Premier ball`+`Net ball`+`Dive ball`+`Nest ball`+`Repeat ball`+`Timer ball`+`Master ball`+`Moon ball`+`Dusk ball`+`Dream ball`+`Luxury ball`+`Rocket ball`+`DNA ball`+`Cherish ball`+`Antique ball`+`Black ball`+`Frozen ball`+`Santa ball`+`GS ball`+`Potion`+`Super potion`+`Hyper potion`+`Full heal`+`Revive`+`Max revive`+`Pokedex`+`Pokedex chip`+`Fishing rod`+`Cave suit`+`Bike`+`Protein`+`Iron`+`Carbos`+`Calcium`+`HP up`+`Rare candy`+`Duskstone`+`Firestone`+`Leafstone`+`Moonstone`+`Ovalstone`+`Shinystone`+`Sunstone`+`Thunderstone`+`Waterstone`+`Dawnstone`+`Garchompite`+`Absolite`+`Banettite`+`Manectite`+`Medichamite`+`Aggronite`+`Gardevoirite`+`Tyranitarite`+`Houndoominite`+`Heracronite`+`Ampharosite`+`Scizorite`+`Gyaradosite`+`Pinsirite`+`Kangaskhanite`+`Gengarite`+`Alakazite`+`Blastoisinite`+`Charizardite Y`+`Charizardite X`+`Venusaurite`+`Mawilite`+`Aerodactylite`+`Lucarionite`+`Abomasite`+`Mewtwonite X`+`Mewtwonite Y`+`Blazikenite`+`Latiasite`+`Latiosite`+`Diancienite`+`Nightmare Unbound Orb`+`Omega Primal Stone`+`Alpha Primal Stone`+`Pidgeotite`+`Glalietite`+`Altarianite`+`Audinotite`+`Beedrillnite`+`Cameruptite`+`Galladerite`+`Lopunnynite`+`Metagrossite`+`Sableyetite`+`Salamencenite`+`Sceptilenite`+`Sharpedorite`+`Slowbronite`+`Steelixite`+`Swampertite`+`Rayquazanite`+`Trader ball`+`Ecology ball`+`Ice Stone`+`Soothe Bell`+`Yellow Nectar`+`Pink Nectar`+`Red Nectar`+`Purple Nectar`+`Everstone`+`Burn Drive`+`Chill Drive`+`Douse Drive`+`Shock Drive`+`Black Belt`+`Black Glasses`+`Black Sludge`+`Charcoal`+`Dragon Fang`+`Hard Stone`+`Magnet`+`Miracle Seed`+`Mystic Water`+`Never-Melt Ice`+`Twisted Spoon`+`Sharp Beak`+`Silk Scarf`+`Silver Powder`+`Soft Sand`+`Spell Tag`+`Metal Powder`+`Eviolite`+`Dragon Scale`+`Metal Coat`+`Kings Rock`+`Whipped Dream`+`Dubious Disc`+`Up-Grade`+`Sachet`+`Reaper Cloth`+`Protector`+`Electirizer`+`Magmarizer`+`Expert Belt`+`Muscle Band`+`Wise Glasses`+`Focus Sash`+`Razor Claw`+`Razor Fang`+`Lucky Egg`+`Air Balloon`+`Safety Goggles`+`Macho Brace`+`Power Weight`+`Power Bracer`+`Power Belt`+`Power Lens`+`Power Band`+`Power Anklet`+`Moomoo Milk`+`Fresh Water`+`Soda Pop`+`Lemonade`+`Scope Lens`+`Focus Band`+`Metronome`+`Damp Rock`+`Heat Rock`+`Icy Rock`+`Smooth Rock`+`Light Ball`+`Thick Club`+`Lucky Punch`+`Stick`+`Soul Dew`+`Wide Lens`+`Buginium Z`+`Darkinium Z`+`Dragonium Z`+`Electrium Z`+`Fairium Z`+`Fightinium Z`+`Firium Z`+`Flyinium Z`+`Ghostium Z`+`Grassium Z`+`Groundium Z`+`Icium Z`+`Normalium Z`+`Poisonium Z`+`Psychium Z`+`Rockium Z`+`Steelium Z`+`Waterium Z`+`Pikashunium Z`+`Pikanium Z`+`Kommonium Z`+`Eevium Z`+`Mewnium Z`+`Tapunium Z`+`Mimikium Z`+`Ultranecrozium Z`+`Incinium Z`+`Lunalium Z`+`Primarium Z`+`Snorlium Z`+`Solganium Z`+`Decidium Z`+`Marshadium Z`+`Lycanium Z`+`Draco Plate`+`Dread Plate`+`Iron Plate`+`Spooky Plate`+`Mind Plate`+`Insect Plate`+`Toxic Plate`+`Sky Plate`+`Stone Plate`+`Earth Plate`+`Fist Plate`+`Icicle Plate`+`Zap Plate`+`Meadow Plate`+`Flame Plate`+`Splash Plate`+`Bug Memory`+`Dark Memory`+`Dragon Memory`+` Electric Memory`+`Fairy Memory`+`Fighting Memory`+`Fire Memory`+` Flying Memory`+`Ghost Memory`+`Grass Memory`+`Ground Memory`+`Ice Memory`+`Poison Memory`+`Psychic Memory`+`Rock Memory`+`Steel Memory`+`Water Memory`) AS `items` FROM `gebruikers_item` WHERE `user_id` = '{$_SESSION['id']}'")->fetch_object()->items;
	$slots += DB::exQuery("SELECT SUM(`TM01` + `TM02` + `TM03` + `TM04` + `TM05` + `TM06` + `TM07` + `TM08` + `TM09` + `TM10` + `TM11` + `TM12` + `TM13` + `TM14` + `TM15` + `TM16` + `TM17` + `TM18` + `TM19` + `TM20` + `TM21` + `TM22` + `TM23` + `TM24` + `TM25` + `TM26` + `TM27` + `TM28` + `TM29` + `TM30` + `TM31` + `TM32` + `TM33` + `TM34` + `TM35` + `TM36` + `TM37` + `TM38` + `TM39` + `TM40` + `TM41` + `TM42` + `TM43` + `TM44` + `TM45` + `TM46` + `TM47` + `TM48` + `TM49` + `TM50` + `TM51` + `TM52` + `TM53` + `TM54` + `TM55` + `TM56` + `TM57` + `TM58` + `TM59` + `TM60` + `TM61` + `TM62` + `TM63` + `TM64` + `TM65` + `TM66` + `TM67` + `TM68` + `TM69` + `TM70` + `TM71` + `TM72` + `TM73` + `TM74` + `TM75` + `TM76` + `TM77` + `TM78` + `TM79` + `TM80` + `TM81` + `TM82` + `TM83` + `TM84` + `TM85` + `TM86` + `TM87` + `TM88` + `TM89` + `TM90` + `TM91` + `TM92` + `HM01` + `HM02` + `HM03` + `HM04` + `HM05` + `HM06` + `HM07` + `HM08` + `TM93` + `TM94` + `TM95` + `TM96` + `TM97` + `TM98` + `TM99` + `TM100`) AS `items` FROM `gebruikers_tmhm` WHERE `user_id` = '{$_SESSION['id']}'")->fetch_object()->items;

	return $slots;
}

function date_show($date) {
	$dt = explode('-', $date);
	$arr = [
		'01' => 'Janeiro',
		'02' => 'Fevereiro',
		'03' => 'Março',
		'04' => 'Abril',
		'05' => 'Maio',
		'06' => 'Junho',
		'07' => 'Julho',
		'08' => 'Agosto',
		'09' => 'Setembro',
		'10' => 'Outubro',
		'11' => 'Novembro',
		'12' => 'Dezembro'
	];

	$string = $dt[2].' de '.$arr[$dt[1]].' de '.$dt[0];
	return $string;
}

function based ($atk) {
	$a = "normal";
	if (in_array($atk, array('Bite', 'Crunch', 'Fire Fang', 'Hyper Fang', 'Ice Fang', 'Poison Fang', 'Psychic Fangs', 'Thunder Fang'))) {
		$a = "bite";
	}else if (in_array($atk, array('Aura Sphere', 'Dark Pulse', 'Heal Pulse', 'Origin Pulse', 'Water Pulse'))) {
		$a = "aura, pulse";
	}else if (in_array($atk, array('Acid Spray', 'Aura Sphere', 'Barrage', 'Beak Blast', 'Bullet Seed', 'Egg Bomb', 'Electro Ball', 'Energy Ball', 'Focus Blast', 'Gyro Ball', 'Ice Ball', 'Magnet Bomb', 'Mist Ball', 'Mud Bomb', 'Octazooka', 'Pollen Puff', 'Rock Blast', 'Rock Wrecker', 'Searing Shot', 'Seed Bomb', 'Shadow Ball', 'Sludge Bomb', 'Weather Ball', 'Zap Cannon'))) {
		$a = "ball, bomb";
	}else if (in_array($atk, array('Dragon Dance', 'Feather Dance', 'Fiery Dance', 'Lunar Dance', 'Petal Dance', 'Quiver Dance', 'Revelation Dance', 'Swords Dance', 'Teeter Dance'))) {
		$a = "dance";
	}else if (in_array($atk, array('Cotton Spore', 'Poison Powder', 'Powder', 'Rage Powder', 'Sleep Powder', 'Spore', 'Stun Spore'))) {
		$a = "powder, spore";
	}else if (in_array($atk, array('Bullet Punch', 'Comet Punch', 'Dizzy Punch', 'Drain Punch', 'Dynamic Punch', 'Fire Punch', 'Focus Punch', 'Hammer Arm', 'Ice Hammer', 'Ice Punch', 'Mach Punch', 'Mega Punch', 'Meteor Mash', 'Power-Up Punch', 'Shadow Punch', 'Sky Uppercut', 'Thunder Punch'))) {
		$a = "punch";
	}else if (in_array($atk, array('Boomburst', 'Bug Buzz', 'Chatter', 'Clanging Scales', 'Confide', 'Disarming Voice', 'Echoed Voice', 'Grass Whistle', 'Growl', 'Heal Bell', 'Hyper Voice', 'Metal Sound', 'Noble Roar', 'Parting Shot', 'Perish Song', 'Relic Song', 'Roar', 'Round', 'Screech', 'Shadow Panic', 'Sing', 'Snarl', 'Snore', 'Sparkling Aria', 'Supersonic', 'Uproar', 'Clangorous Soulblaze'))) {
		$a = "sound";
	}
  
	return $a; // Based Moves para algumas Habilidades
}
  
function hidden_Power() {
	$p = func_get_args();
  
	$h = round(((($p[0] + $p[1] * 2 + $p[2] * 4 + $p[3] * 8 + $p[4] * 16 + $p[5] * 32) * 15) / 63) / 31.5);
	$typeArr = array('Fighting', 'Flying', 'Poison', 'Ground', 'Rock', 'Bug', 'Ghost', 'Steel', 'Fire', 'Water', 'Grass', 'Electric', 'Psychic', 'Ice', 'Dragon', 'Dark');
  
	return $typeArr[$h];
}
  
function atk ($atk, $poke = '') {
	$arr = DB::exQuery("SELECT * FROM `aanval` WHERE naam='$atk'")->fetch_assoc();
	if (!empty($poke)) {
	  $poke['ability'] = ability($poke['ability'])['name'];
	  $poke['type1'] = DB::exQuery("SELECT * FROM `pokemon_wild` WHERE wild_id='$poke[wild_id]'")->fetch_assoc()['type1'];

	  if ($arr['is_zmoves'] == 1) {
			$zinfo = zMoves::move ($poke);
			if (is_array($zinfo) && sizeof($zinfo) == 3) {
				$arr['sterkte'] = $zinfo[1];
				$arr['soort'] = $zinfo[2];
			}
	  }
	  
	  if ($arr['soort'] == 'Normal') {
			switch ($poke['ability']) {
				case 'Refrigerate':
				$arr['soort'] = 'Ice';
				break;
				case 'Pixilate':
				$arr['soort'] = 'Fairy';
				break;
				case 'Aerilate':
				$arr['soort'] = 'Flying';
				break;
				case 'Galvanize':
				$arr['soort'] = 'Electric';
				break;
			}
	  }
  
	  if ($poke['ability'] == 'Normalize') {
		$arr['soort'] = 'Normal';
	  } else if ($poke['ability'] == 'Liquid Voice' && based($atk) == 'sound') {
		$arr['soort'] = 'Water';
	  } else if ($poke['ability'] == 'Parental Bond') {
		$arr['aantalkeer'] = '1-2';
	  } else if ($poke['ability'] == 'Speed Boost') {
		$arr['effect_kans'] = '100';
		$arr['effect_naam'] = 'Speed_up_2';
	  } else if ($poke['ability'] == 'Poison Touch' && $arr['makes_contact'] == 1) {
		$arr['effect_kans'] = '30';
		$arr['effect_naam'] = 'Poison';
	  } else if ($poke['ability'] == 'Skill Link' && $arr['aantalkeer'] != '1') {
		$arr['aantalkeer'] = '2-5';
	  }

	  if ($atk == 'Hidden Power') {
		$arr['soort'] = hidden_Power ($poke['hp_iv'], $poke['attack_iv'], $poke['defence_iv'], $poke['speed_iv'], $poke['spc.attack_iv'], $poke['spc.defence_iv']);
	  } else if (in_array($atk, array('Judgment', 'Multi-Attack', 'Revelation Dance'))) {
		$arr['soort'] = $poke['type1'];
	  } else if ($atk == 'Techno Blast') {
		$a = array('Fire', 'Ice', 'Water', 'Electric');
		$b = array('Burn Drive', 'Chill Drive', 'Douse Drive', 'Shock Drive');
	
		if (in_array($poke['item'], $b)) {
		  $arr['soort'] = $a[array_search($poke['item'], $b)];
		}	
	  }

	  if ($poke['item'] == 'Scope Lens') {
		if ($arr['critical'] == 0) {
			if (rand(0, 6) == 6) 
				$arr['critical'] = 1;
		}
	  } else if ($poke['item'] == 'Wide Lens') {
		$percent = floor($arr['mis'] * 1.15);
		$arr['mis'] -= $percent;
	  } else if ($poke['item'] == 'Lucky Punch' && $poke['wild_id'] == '113') {
		if ($arr['critical'] == 0) {
			if (rand(0, 4) == 4) 
				$arr['critical'] = 1;
		}
	  } else if ($poke['item'] == 'Stick' && $poke['wild_id'] == '83') {
		if ($arr['critical'] == 0) {
			if (rand(0, 4) == 4) 
				$arr['critical'] = 1;
		}
	  }
	}
  
	return $arr;
}

function ability ($id) {
	  return DB::exQuery("SELECT * FROM `abilities` WHERE id='$id'")->fetch_assoc();
}

class zMoves {
  
	public $z = '';

	public static function valid ($poke) {
		if (!empty($poke)) {
			$item = $poke['item'];
			$valid = pokemon_equip ($poke['wild_id'], $item);

			if ($valid) {
				$query = DB::exQuery ("SELECT * FROM `zaanval_relacionados` WHERE item='$item'");

				if ($query->num_rows > 0) { 
					$query = $query->fetch_assoc();

					$atk1 = atk ($poke['aanval_1']);
					$atk2 = atk ($poke['aanval_2']);
					$atk3 = atk ($poke['aanval_3']);
					$atk4 = atk ($poke['aanval_4']);

					if ($query['typed'] == 1) {
						$atk  = atk ($query['naam'])['soort'];
						$atk_arr = array($atk1['soort'], $atk2['soort'], $atk3['soort'], $atk4['soort']);

						if (in_array($atk, $atk_arr)) {
							$key = array_search($atk, $atk_arr);
							$atk_arr2 = array($atk1['naam'], $atk2['naam'], $atk3['naam'], $atk4['naam']);

							return array(true, $query['naam'], $atk_arr2[$key]);
						}
					} else {
						$atk_arr = array($atk1['naam'], $atk2['naam'], $atk3['naam'], $atk4['naam']);

						if (in_array($query['required_move'], $atk_arr)) {
							return array (true, $query['naam']);
						}
					}
				}
			}
		}

		return array(false);
	}

	public static function move ($poke) {
		$valid = self::valid ($poke);

		if ($valid[0]) {
			$name = $valid[1];

			if (sizeof($valid) == 3) {
				$based = atk($valid[2]);
				$power = $based['sterkte'];
				$pow = '';

				if ($power >= 0 && $power <= 55) {
					$pow = 100;
				} else if ($power >= 60 && $power <= 65) {
					$pow = 120;
				} else if ($power >= 70 && $power <= 75) {
					$pow = 140;
				} else if ($power >= 80 && $power <= 85) {
					$pow = 160;
				} else if ($power >= 90 && $power <= 95) {
					$pow = 175;
				} else if ($power == 100) {
					$pow = 180;
				} else if ($power == 110) {
					$pow = 185;
				} else if ($power >= 120 && $power <= 125) {
					$pow = 190;
				} else if ($power == 130) {
					$pow = 195;
				} else {
					$pow = 200;
				}

				$type = $based['soort'];
				return array($name, $pow, $type);
			} else {
				return array ($name);
			}
		}
	}

}