<?php
		
require_once('app/includes/resources/config.php');
require_once('language/language-general.php');
require_once('app/includes/resources/ingame.inc.php');

#Load Page
$page = $_GET['page'];

$mostraanuncios = false; //MOSTRA GOOGLE ADS ?
$events_count = 0;

$ip = $_SERVER['REMOTE_ADDR'];

// if (!in_array($ip, array())) {
// 	$page = 'app/includes/resources/pages/aguardem';
// }

$manutencao = 0; //0 = NAO 1 = SIM 2 = PASSANDO POR UPGRADE 3 = ANTI FLOOD
if (($manutencao == 1 || $manutencao == 2)) {
    if (!in_array($ip, array('187.19.176.193'))) {
    	$page = 'app/includes/resources/pages/manuntencao';
    }
}

$chat_block = false;

#Ingame dingen
if (isset($_POST['login']) && empty($_SESSION['acc_id'])) {
	require_once("app/includes/resources/login.php");
} else if (isset($_SESSION['acc_id'])) {
	$md5hash_acc  = md5($_SESSION['acc_id'] . "," . $_SESSION['acc_naam']);
	if ($_SESSION['acc_hash'] != $md5hash_acc) { require_once('logout.php'); exit(); }

	$result = DB::exQuery("SELECT * FROM `rekeningen` WHERE `acc_id`='" . $_SESSION['acc_id'] . "' GROUP BY `acc_id` LIMIT 1");
	$rekening = $result->fetch_assoc();
    if (!is_array($rekening) || $_SESSION['keylog'] != $rekening['keylog']) require_once('logout.php');

	#Als account_code 0 is, verbannen!
	if ($rekening['account_code'] == 0 OR $gebruiker['bloqueado'] == "sim") {
		session_unset();
		session_destroy();
		exit(header('Location: ./banned.php'));
	}

	$gold = highamount($rekening['gold']);
	$season = isSeason();

	if (isset($_SESSION['id'])) {
		#Hash maken
		//$md5hash  = md5($_SERVER['REMOTE_ADDR'] . "," . $_SESSION['naam']);
		$md5hash  = md5($_SESSION['id'] . "," . $_SESSION['naam']);

		#Controleren van de hash.
		#Is de has niet goed dan uitloggen en inloggen opnieuw laden
		if ($_SESSION['hash'] != $md5hash) {
			unset($_SESSION['hash'], $_SESSION['id'], $_SESSION['naam']);
			exit(header("Location: ./my_characters"));
		}

		#Load User Information
		$result = DB::exQuery("SELECT g.*, gi.* FROM `gebruikers` AS `g` INNER JOIN `gebruikers_item` AS `gi` ON `g`.`user_id` = `gi`.`user_id` WHERE `g`.`user_id`='{$_SESSION['id']}' AND `g`.`acc_id`='{$_SESSION['acc_id']}' GROUP BY `g`.`user_id` LIMIT 1");
		$gebruiker = $result->fetch_assoc();

		# ITEMS
		$gebruiker['items'] = 0;
		$gebruiker['items'] += freeSlots ();

		if (!is_array($gebruiker) || $gebruiker['acc_id'] != $rekening['acc_id'] || $gebruiker['session'] != $_COOKIE['PHPSESSID'] || $gebruiker['banned'] == 'Y') {
			unset($_SESSION['hash'], $_SESSION['id'], $_SESSION['naam']);
			exit(header("Location: ./my_characters"));
		}

		$pagesAllowerGlobal = array('logout','my_characters','new_character','information','privacy','terms','rules');

		//$gebruiker['rang'] = rank_player($gebruiker['user_id']);
		
		require('app/classes/Utils.php');
        require('app/classes/Clans.php');
        
        $clan = new Clans();
        $gebruiker['clan'] = $clan->getUserClan($_SESSION['id']);

		if (($gebruiker['online'] + 300) < time())
			DB::exQuery("UPDATE `gebruikers` SET `online`=UNIX_TIMESTAMP() WHERE `user_id`='" . $gebruiker['user_id'] . "' LIMIT 1");

		$silver = ($gebruiker['silver'] <= 0) ? '--' : highamount($gebruiker['silver']);
		$tickets = ($gebruiker['moedapromocional'] <= 0) ? '--' : highamount($gebruiker['moedapromocional']);

		$vvloginaberto	= DB::exQuery("SELECT valor FROM configs WHERE config='login_aberto' LIMIT 1")->fetch_assoc();
		$fecharlogin	= $vvloginaberto['valor'];	# 1 para fechado 0 para aberto
		if ($gebruiker['admin'] == 0 && $fecharlogin == 1) {
			echo "<script>alert('Fechado para manutencao, aguarde!');window.location.href='./';</SCRIPT>";
			require_once('logout.php');
			exit;
		}

    include_once 'app/classes/League.php';

    $result_league = DB::exQuery("SELECT duel_id FROM league_battle WHERE termino = '0000-00-00 00:00:00' AND "
            . "(user_id1 = '" . $_SESSION['id'] . "' OR user_id2 = '" . $_SESSION['id'] . "')");

    if ($result_league->num_rows == 1 && $page != 'attack/duel/duel-attack') {

		$verifyleague = $result_league->fetch_assoc();

        $_SESSION['duel']['duel_id'] = $verifyleague['duel_id'];
        $_SESSION['duel']['begin_zien'] = true;
        //$gebruiker['pagina'] = 'duel';
        //$page = "attack/duel/duel-attack";
        header("Location: ./attack/duel/duel-attack");
        exit();
    } else if (DB::exQuery("SELECT * FROM league_battle WHERE "
                    . "(user_id1 = '" . $_SESSION['id'] . "' OR user_id2 = '" . $_SESSION['id'] . "') AND "
                    . "((NOW()" . League::$ajuste_tempo_string . ") BETWEEN "
                    . "(inicio - INTERVAL 5 MINUTE - INTERVAL 5 SECOND) AND "
                    . "(inicio + INTERVAL 1 MINUTE + INTERVAL 10 SECOND))")->num_rows > 0) {

		$result_leaguex = DB::exQuery("SELECT * FROM league_battle WHERE "
                    . "(user_id1 = '" . $_SESSION['id'] . "' OR user_id2 = '" . $_SESSION['id'] . "') AND "
                    . "((NOW()" . League::$ajuste_tempo_string . ") BETWEEN "
                    . "(inicio - INTERVAL 5 MINUTE - INTERVAL 5 SECOND) AND "
                    . "(inicio + INTERVAL 1 MINUTE + INTERVAL 10 SECOND))")->fetch_assoc();

        $league_battle = $result_leaguex;

        if ($page == "attack/attack_map" || $page == "trainer" || $page == "attack/gyms" || $page == "attack/duel/invite") {
            header("Location: index.php/league");
            exit();
        }
    }

    if (($gebruiker['pagina'] != 'duel') && ( $page != 'pokemoncenter') && !(isset($_SESSION['duel']['duel_id']) && $_SESSION['duel']['duel_id'])) {
        $tour_sql = DB::exQuery("SELECT * FROM toernooi WHERE deelnemers!='' AND no_1='0' ORDER BY toernooi DESC LIMIT 1");
        if ($tour_sql->num_rows > 0) {
            $tour_info = $tour_sql->fetch_assoc();
            $round_sql = DB::exQuery("SELECT * FROM `toernooi_ronde` WHERE toernooi='" . $tour_info['toernooi'] . "' AND winnaar_id = '0' AND (user_id_1 = '" . $_SESSION['id'] . "' OR user_id_2 = '" . $_SESSION['id'] . "')");
            if ($round_sql->num_rows > 0) {
                $round_info = $round_sql->fetch_assoc();
                $tour_over = strtotime($tour_info['tijd']) - strtotime(date("H:i:s"));
                if ($tour_over < 300 AND $tour_over > 0) {
                    if (!$_SESSION['toernooi_sent']) {
                        $_SESSION['toernooi_sent'] = TRUE;
                        $time = floor($tour_over / 60);
                        DB::exQuery("INSERT INTO `gebeurtenis` (`datum` ,`ontvanger_id` ,`bericht`)
              VALUES ('" . date('Y-m-d H:i:s') . "', '" . $_SESSION['id'] . "', 'Sua batalha no torneio irá começar em &plusmn;" . $time . " minutos. Certifique-se que seus pokémons estão prontos.');");
                    }
                    header("refresh: " . $tour_over . "; url=attack/tour_fight");
                } else if (($tour_over > -90 AND $tour_over < 0) AND ( $_GET['page'] != "attack/tour_fight") AND ( $_GET['page'] != "attack/duel/duel-attack")) {
                    $_SESSION['toernooi_sent'] = FALSE;
                    $page = 'attack/tour_fight';
                }
            } else
                $_SESSION['toernooi_sent'] = FALSE;
        }
    }

		if ($gebruiker['admin'] > 0 AND $_SESSION['equipe'] != 1)
			$page = 'app/includes/resources/pages/equipe-check';

		$gebruiker_rank = rank($gebruiker['rank']);
		if ($gebruiker['rankexp'] > 0)
				$gebruiker_rank['procent'] = round(($gebruiker['rankexp'] / $gebruiker['rankexpnodig']) * 100);
		else	$gebruiker_rank['procent'] = 0;

		if ($gebruiker['itembox'] == 'Bag')				$gebruiker['item_over'] = 20   - $gebruiker['items'];
		else if ($gebruiker['itembox'] == 'Yellow box')	$gebruiker['item_over'] = 50   - $gebruiker['items'];
		else if ($gebruiker['itembox'] == 'Blue box')	$gebruiker['item_over'] = 100  - $gebruiker['items'];
		else if ($gebruiker['itembox'] == 'Red box')	$gebruiker['item_over'] = 250  - $gebruiker['items'];
		else if ($gebruiker['itembox'] == 'Purple box')	$gebruiker['item_over'] = 500  - $gebruiker['items'];
		else if ($gebruiker['itembox'] == 'Black box')	$gebruiker['item_over'] = 1000 - $gebruiker['items'];

		$arr = explode(",", $gebruiker['pok_bezit']);
		$result = array_unique($arr);
		$num_pokes = DB::exQuery("SELECT `wild_id` FROM `pokemon_wild`")->num_rows;
		$gebruiker_pokemon['procent'] = round((count($result) / $num_pokes) * 100);

		if ($page != 'choose-pokemon' && $gebruiker['eigekregen'] == 0) {
			if (!in_array($_GET['page'], $pagesAllowerGlobal))
				exit(header("LOCATION: ./choose-pokemon"));
		}

		#Load User Pokemon
		$user_id = $_SESSION['id'];
		$pokemon_sql = DB::exQuery("SELECT `pw`.`naam`,`pw`.`type1`,`pw`.`type2`,`pw`.`zeldzaamheid`,`pw`.`groei`,`pw`.`aanval_1`,`ps`.`humor_change`,`pw`.`aanval_2`,`pw`.`aanval_3`,`pw`.`aanval_4`,`ps`.* FROM `pokemon_wild` AS `pw` INNER JOIN `pokemon_speler` AS `ps` ON `ps`.`wild_id`=`pw`.`wild_id` WHERE `ps`.`user_id`='" . $user_id . "' AND `ps`.`opzak`='ja' ORDER BY `ps`.`opzak_nummer` ASC");
		$gebruiker['in_hand'] = $pokemon_sql->num_rows;
		if ($gebruiker['in_hand'] == 0 AND $page != "beginning" AND $page != "choose-pokemon"  AND $page != "box")
			exit(header("Location: ./box"));

		# Check new Mails
		
		#CORRAÇÃO DE BUG NOTIFICAÇÕES (SEXTA)
	    $mails_count	= DB::exQuery("SELECT * FROM `conversas` WHERE `trainer_2_hidden`='0' AND `id` = ANY (SELECT DISTINCT (`conversa`) FROM `conversas_messages` WHERE `reciever`='".$_SESSION['id']."' AND `seen`='0')")->num_rows;
		$official_count	= DB::exQuery("SELECT `id` FROM `official_message` WHERE `hidden`='0' AND `id` NOT IN (SELECT `id_msg` FROM `official_message_read` WHERE `id_user`='$user_id') ")->num_rows;
		$mails_txt		= ($mails_count > 1) ? 'Você tem ' . $mails_count . ' novas mensagens!' : 'Você tem ' . $mails_count . ' nova mensagem!';
		$general_count  = $mails_count + $official_count;

        $_SESSION['region'] = $gebruiker['wereld'];
		#Load User Events
		$events_count = DB::exQuery("SELECT `id` FROM `gebeurtenis` WHERE `ontvanger_id`='" . $_SESSION['id'] . "' AND `gelezen`='0'")->num_rows;
		$events_txt = ($events_count > 1) ? 'Você tem ' . $events_count . ' novas notificações!' : 'Você tem ' . $events_count . ' nova notificação!';
		
		if (!empty($evento_atual)) {
		    $event_page = 'app/includes/resources/events/pages/'.$evento_atual['link'].'.php';
			$div_ballon = '<a class="noanimate" href="./eventos&actual='.$evento_atual['link'].'"><div class="event_ballon">
				<div id="e-icon-'.$evento_atual['id'].'"></div>
				<div id="e-ballon">
					<h3 class="title">'.$evento_atual['nome'].'</h3>
					<p class="text"><img src="'.$static_url.'/images/icons/time.png" style="vertical-align: text-bottom"> '.$evento_atual['date'].'</p>
				</div>
			</div></a>';
		}
		
		if ($Saffari == 'Aberto') {
		    $safari_horario = '11:00 ATÉ 13:00';
		    $date = strtotime(date('H:i'));
		    if ($date >= $aberto_2 && $date <= $fechado_2) {
		        $safari_horario = '18:00 ATÉ 20:00';
		    } else if ($date >= $aberto_3 && $date <= $fechado_3) {
		        $safari_horario = '00:00 até 02:00';
		    } else {
		        $safari_horario = '11:00 ATÉ 13:00';
		    }
		    $div_ballon = '<a class="noanimate" href="./safari"><div class="event_ballon">
				<div id="e-icon-2"></div>
				<div id="e-ballon">
					<h3 class="title">ZONA DO SAFARI</h3>
					<p class="text"><img src="'.$static_url.'/images/icons/time.png" style="vertical-align: text-bottom"> HOJE ÀS '.$safari_horario.'!</p>
				</div>
			</div></a>';
		}
	}
}

if (!in_array($page, array('my_characters', 'new_character', 'logout')) && empty($_SESSION['id']) && isset($_SESSION['acc_id'])) {
	$characters = DB::exQuery("SELECT `user_id` FROM `gebruikers` WHERE `acc_id`='$_SESSION[acc_id]'")->num_rows;
	if ($characters > 0) {
		echo("<script>window.location = './my_characters';</script>");
	} else {
		echo("<script>window.location = './new_character';</script>");
	}
}

#Check if you're asked for a duel MOET OOK ANDERS -> Event! ;)
$duel_sql = DB::exQuery("SELECT * FROM `duel` WHERE `tegenstander`='" . $gebruiker['username'] . "' AND (`status`='wait') ORDER BY `id` DESC LIMIT 1");

$captcha_time = ($gebruiker['premiumaccount'] > time()) ? 1200 : 600;

if (empty($page))	$page = 'home';
else if (!file_exists($page . '.php')) $page = 'notfound';
else if (empty($_SESSION['id']))	$page = $page;
else if (in_array($page, $pagesAllowerGlobal)) $page = $_GET['page'];
else if (($gebruiker['captcha_time'] + $captcha_time) < time() && in_array($_GET['page'], $captcha_page_check))	$page = 'captcha';
else if ($gebruiker['pagina'] == 'trainer-attack') $page = 'attack/trainer/trainer-attack';
else if ($gebruiker['pagina'] == 'attack') $page = 'attack/wild/wild-attack';
else if (DB::exQuery("SELECT * FROM `duel` WHERE `uitdager`='" . $gebruiker['username'] . "' AND (`status`='wait') ORDER BY `id` DESC LIMIT 1")->num_rows == 1) $page = 'attack/duel/invite';
else {
	$duel_test = DB::exQuery("SELECT `id` FROM `duel` WHERE `status`='wait' AND `uitdager`='" . $_SESSION['naam'] . "'");
	if (!empty($_SESSION['aanvalnieuw']) && $page != 'information') {
		list($nieuweaanval['pokemonid'], $nieuweaanval['aanvalnaam']) = explode('/', base64_decode($_SESSION['aanvalnieuw']));
		$page = "app/includes/resources/poke-newattack";
	} else if (!empty($_SESSION['evolueren']) && $page != 'information') {
		list($evolueren['pokemonid'], $evolueren['nieuw_id']) = explode('/', base64_decode($_SESSION['evolueren']));
		$page = "app/includes/resources/poke-evolve";
	} else if ($gebruiker['pagina'] == 'attack') {
			$page = "attack/wild/wild-attack";
			$res = DB::exQuery("SELECT `id` FROM `aanval_log` WHERE `user_id`={$gebruiker['user_id']} AND `trainer`='' AND `laatste_aanval`!='end_screen' LIMIT 1")->fetch_assoc();
			$_SESSION['attack']['aanval_log_id'] = $res['id'];
	} else if ($gebruiker['pagina'] == 'trainer-attack') {
		$page = "attack/trainer/trainer-attack";
		$res = DB::exQuery("SELECT `id` FROM `aanval_log` WHERE `user_id`={$gebruiker['user_id']} AND `trainer`!='' AND `laatste_aanval`!='end_screen' LIMIT 1")->fetch_assoc();
		$_SESSION['attack']['aanval_log_id'] = $res['id'];
	} else if ($gebruiker['pagina'] == 'duel' && $duel_test->num_rows > 0)	$page = $_GET['page'];
	else if ($gebruiker['pagina'] == 'duel') {
		$page = "attack/duel/duel-attack";
		$res = DB::exQuery("SELECT `id` FROM `duel` WHERE (`tegenstander`='{$gebruiker['username']}' OR `uitdager`='{$gebruiker['username']}') AND `status`='accept' LIMIT 1")->fetch_assoc();
		$_SESSION['duel']['duel_id'] = $res['id'];
	} else if ($duel_sql->num_rows == 1) {
		$page = "attack/duel/invited";
	}
}

$compartilhamento = array('captcha','pokemoncenter', 'home', 'rankinglist', 'information', 'statistics', 'my_characters', 'box', 'badges', 'profile', 'pokemon-profile', 'pokedex', 'attack/attack_map', 'trainer', 'attack/gyms', 'town', 'logout', 'attack/wild/wild-attack', 'attack/trainer/trainer-attack', 'app/includes/pages/equip-check');

if ($_SESSION['share_acc'] == 1) {
	if (!in_array($page, $compartilhamento)) {
		$page = 'notfound';
	}
	$chat_block = true;
}

if (in_array($page, array('notfound', 'error', 'captcha', 'app/includes/pages/equip-check', 'app/includes/resources/poke-newattack', 'app/includes/resources/poke-evolve', 'config', 'my_characters', 'new_character', 'inbox', 'blocklist', 'official-messages', 'ajax'))) {
	$chat_block = true;
}

if ($gebruiker['admin'] >= 3 && (isset($_GET['sair']) && $_GET['sair'] == 'y')) {
	DB::exQuery("UPDATE `gebruikers` SET `pagina`='duel_start' WHERE `user_id`='" . $_SESSION['id'] . "'");
    DB::exQuery("DELETE FROM `pokemon_speler_gevecht` WHERE `user_id`='" . $_SESSION['id'] . "'");
    DB::exQuery("DELETE FROM `duel` WHERE `uitdager`='" . $_SESSION['naam'] . "' OR `tegenstander`='" . $_SESSION['naam'] . "'");
}

$pokecen_tijd = (strtotime($gebruiker['pokecentertijdbegin']) + $gebruiker['pokecentertijd']) - time();
$travel_tijd = (strtotime($gebruiker['traveltijdbegin']) + $gebruiker['traveltijd']) - time();

if ($pokecen_tijd > 0) {
	#Tijd die overblijft
	$wait_time = $pokecen_tijd;
	if ($wait_time >= 0) {
		$type_timer = 'pokecenter';
		if (!page_timer($page, 'jail') && !in_array($page, $pagesAllowerGlobal))
			$page = 'app/includes/resources/wait';
	}
} else if ($travel_tijd > 0) {
	#Tijd die overblijft
	$wait_time = $travel_tijd;
	if ($wait_time >= 0) {
		$type_timer = 'travel';
		if (!page_timer($page, 'jail') && !in_array($page, $pagesAllowerGlobal))
			$page = 'app/includes/resources/wait';
	}
}
?>
<!DOCTYPE html>
<noscript>
    <meta http-equiv="refresh" runat="server" content="0;url=https://<?=$_SERVER['SERVER_NAME']?>/noscript.php" />
</noscript>
<html lang="pt-br">
	<head>
	    <meta http-equiv="content-type" content="text/html; charset=utf-8" />
	    
		<link rel="shortcut icon" href="favicon.ico" type="image/x-icon" />
		<title><?=$site_title;?></title>
		
		<meta name="description" content="<?=$site_description;?>" />
		<meta name="keywords" content="<?=$site_keywords;?>" />
		<meta name="copyright" content="<?=$site_copyright;?>" />
		<meta name="robots" content="index, follow">
		
		<meta property="og:title" content="<?=$site_title;?>" />
        <meta property="og:description" content="<?=$site_description;?>" />
        <meta property="og:image" content="https://www.pokemonworldlegends.com/public/images/layout/logo_footer.png" />
        
        <meta http-equiv="pragma" content="no-cache" />
        <meta http-equiv="expires" content ="-1" />
        <meta http-equiv="Content-Security-Policy" content="upgrade-insecure-requests"> 

		<base href="https://<?=$_SERVER['SERVER_NAME']?>">

		<link rel="stylesheet" type="text/css" href="<?=$static_url;?>/stylesheets/jquery-ui.css" />
		<link rel="stylesheet" type="text/css" href="<?=$static_url;?>/stylesheets/colorbox.css" />
		<link rel="stylesheet" type="text/css" href="<?=$static_url;?>/stylesheets/tipped.css" />
		<link rel="stylesheet" type="text/css" href="<?=$static_url?>/stylesheets/bootstrap.css">
		<link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.10.12/css/jquery.dataTables.min.css" />
		<link rel="stylesheet" type="text/css" href="https://fonts.googleapis.com/icon?family=Material+Icons">
		<link rel="stylesheet" type="text/css" href="<?=$static_url;?>/stylesheets/style.css" />
		<link rel="stylesheet" type="text/css" href="<?=$static_url;?>/stylesheets/style_override.css" />
		<link rel="stylesheet" type="text/css" href="<?=$static_url?>/javascripts/flickity/flickity.min.css">

		<script type="text/javascript" src="<?=$static_url;?>/javascripts/jquery-2.1.3.min.js"></script>
		<script type="text/javascript" src="<?=$static_url;?>/javascripts/jquery-ui.min.js"></script>
		<script type="text/javascript" src="<?=$static_url;?>/javascripts/jquery.ui.touch-punch-improved.min.js"></script>
		<script type="text/javascript" src="<?=$static_url;?>/javascripts/jquery.cookie.min.js"></script>
		<script type="text/javascript" src="<?=$static_url;?>/javascripts/jquery.colorbox.js"></script>
		<script type="text/javascript" src="<?=$static_url;?>/javascripts/tipped/tipped.js"></script>
		<script type="text/javascript" src="<?=$static_url;?>/javascripts/bootstrap.min.js"></script>
		<script type="text/javascript" src="<?=$static_url?>/javascripts/flickity/flickity.pkgd.min.js"></script>
		<script type="text/javascript" src="https://cdn.datatables.net/1.10.12/js/jquery.dataTables.min.js"></script>
		<script type="text/javascript" src="<?=$static_url;?>/javascripts/jq-timer.js"></script>
		<script type="text/javascript" src="<?=$static_url;?>/javascripts/jquery.pokemon.js"></script>
		<script type="text/javascript" src="<?=$static_url?>/javascripts/wl.orientation.js"></script>
		<script type="text/javascript" src="<?=$static_url;?>/javascripts/howler.min.js"></script>
		<!--<script type="text/javascript" src="<?=$static_url?>/javascripts/socket.io.js"></script>-->
        
		<script type="text/javascript" src="<?=$static_url?>/plugins/icheck/icheck.min.js"></script>
		<link rel="stylesheet" type="text/css" href="<?=$static_url?>/plugins/icheck/square/blue.css" rel="stylesheet">

		<?php if (isset($league_battle) && $league_battle && $page != "attack/duel/duel-attack") { echo 'a'; }?>

		<?php if ($page == "league_status" || $page == "tour") { echo '<script type="text/javascript" src="'.$static_url.'/javascripts/jquery.gracket.js-master/jquery.gracket.min.js"></script> <link type="text/css" rel="stylesheet" href="'.$static_url.'/stylesheets/gracket.css"/>'; } ?>
	</head>
	<body <?=(isset($_SESSION['acc_id']))? 'style="background: url('.$static_url.'/images/layout/background-ingame.png) no-repeat center; background-size: cover;"' : ''?>>

	<?php
	if ( isset($_SESSION['acc_id']) ) {
		include("inc_league.php");
// 		if (isset($_SESSION['id'])) include('inc_promo.php');
		?>
		<div id="wrapper">
			<div id="container">
				<?php if (isset($_SESSION['id'])) include('app/includes/resources/poke-ovni.php'); ?>
				<div id="header">
					<div class="hub" style="z-index: 10">
						<ul class="hub-hud">
							<li class="hub-hud-line" style="width: 600px">
								<a href="./" class="noanimate" style="padding-top: 150px;">
									<div id="logo" class="logo_<?=rand(1, 5)?>"></div>
								</a>
							</li>
							<?php $ab = (empty($_SESSION['id']) || !empty($_SESSION['share_acc']))? 'block' : 'add'; ?>
							<li class="hub-hud-line" style="padding-right: 5px;"><div id="silvers" class="bright-low" <?=($gebruiker['silver'] >= 1000000)? 'title="'.highamount($gebruiker['silver']).'"' : ''?>"><div class="<?=$ab?>"></div><p><?=balance_converter($gebruiker['silver'])?></p></div></li>
							<li class="hub-hud-line">
								<?php if ($ab != 'block') { ?> <a href="./donate" class="noanimate"><div id="golds" class="bright-low"><div class="<?=$ab?>"></div><p><?=highamount($rekening['gold'])?></p></div><span class="badges" style="float: right; margin-left: -20px; margin-top: -2px; z-index: 100; position: relative;">+</span></a> <?php } else { ?> <div id="golds" class="bright-low" title="<?=highamount($rekening['gold'])?>"><div class="<?=$ab?>"></div><p><?=balance_converter($rekening['gold'])?></p></div> <?php } ?>
							</li>
							<li class="hub-hud-line"><a href="./inbox" class="noanimate" style="float: right"><?=($general_count > 0)? '<span class="badges" style="float: right; margin-left: -20px; margin-top: -15px; z-index: 100; position: relative;">'.$general_count.'</span>' : ''; ?><img src="<?=$static_url?>/images/layout/mensagens.png" style="margin-top: -7px;" class="bright"></a></li>
							<li class="hub-hud-line"><?=($events_count > 0)? '<span class="badges" style="float: right; margin-left: -20px; margin-top: -15px; z-index: 100; position: relative;">'.$events_count.'</span>' : ''; ?><img src="<?=$static_url?>/images/layout/perfil.png" style="margin-top: -10px; float: right" class="bright tip_bottom-right" title="<div class='user_hover'><a href='./profile&player=<?=$gebruiker['username']?>' class='noanimate'><div><i class='material-icons'>account_circle</i><?=isset($gebruiker['username'])? $gebruiker['username'] : 'Usuário'?></div></a><a href='./my_characters' class='noanimate'><div><i class='material-icons'>group</i>Meus Personagens</div></a><a href='./events' class='noanimate'><div><i class='material-icons'>notifications_active</i>Notificações (<?=$events_count?>)</div></a><a href='./account-options' class='noanimate'><div><i class='material-icons'>settings</i>Configurações</div></a><a href='./logout' class='noanimate'><div><i class='material-icons'>close</i>Deslogar</div></a></div>"></li>
						</ul>
					</div>
					<div class="hub" style="margin-top: -165px; z-index: 10">
						<ul class="hub-hud">
							<li class="hub-hud-line" style="width: 100%">
								<div style="background: url('<?=$static_url?>/images/characters/<?=$gebruiker['character']?>/bar.png') no-repeat; border-radius: 5px; float: right">
									<?php if ($gebruiker['premiumaccount'] > time()) { ?><img src="<?=$static_url?>/images/icons/avatar/clock.png" title="Sua conta premium acaba em: <?=date('d/m/y H:i', $gebruiker['premiumaccount']);?>" style="width: 35px;margin-top: 58px;position: absolute;background: url(public/images/layout/eventos.png) no-repeat;margin-left: 485px;background-size: 50px 50px;border-radius: 5px;cursor: pointer;"><?php } ?>
									<div style="background: url('<?=$static_url?>/images/layout/player.png') no-repeat; width: 520px; height: 93px">
										<ul style="list-style: none; padding-top: 16px; color: #fff">
											<li style="padding-left: 103px; width: 124px; text-align: center"><a href="./profile&amp;player=<?=$gebruiker['username'];?>"><?=GetColorName($gebruiker['user_id']);?><?=(!empty($gebruiker['clan']))? '<a href="./clans&action=profile&id='.$gebruiker['clan'].'"> - <b>'.$clan->get($gebruiker['clan'])['sigla'].'</b></a>' : '';?></a></li>
											<li style="padding-left: 103px; width: 124px; text-align: center; padding-top: 3px"><?=(isset($gebruiker['wereld']))? $gebruiker['wereld'] : 'Lobby' ;?></li>
											<li style="padding-left: 85px; width: 171px; text-align: center; padding-top: 6px"><?= str_replace(" ", "&nbsp;", $gebruiker_rank['ranknaam']); ?> (<?=$gebruiker_rank['procent'];?>%)</li>
											<li style="padding-left: 85px; width: 171px; text-align: center; padding-top: 6px"><?=$gebruiker_pokemon['procent'];?>% de todos os Pokémons</li>
										</ul>
									</div>
								</div>
							</li>
						</ul>
					</div>
					<div class="hub" style="padding-top: 10px">
						<ul class="hub-hud">
							<li class="hub-hud-line" style="width: 45%;"></li>
							<li class="hub-hud-line" style="width:160px">
								<div id="events">
									<img src="<?=$static_url?>/images/icons/avatar/<?=$season[1]?>-season.png" title="Durante este mês estamos na Estação: <?=$season[0]?>!" style="width: 49px;margin-top: -3px;margin-right: -3px;">
									<?php if (($gebruiker['quest_1']+$gebruiker['quest_2']) < 2) { ?><span class="badges" style="float: right; margin-left: -20px; margin-top: 23px; z-index: 100; position: relative; cursor:pointer; width: 13px; height:13px; line-height: 14px; font-size:8px;" onclick="window.location = './daily_quests'"><?=(2-($gebruiker['quest_1']+$gebruiker['quest_2']))?></span><?php } ?><a href="./daily_quests" class="noanimate" style="display:block"><img src="<?=$static_url?>/images/icons/avatar/quests.png" title="Clique aqui para ver suas Missões Diárias." style="margin-right: -3px;"></a>
									<?php if ($gebruiker['daily_bonus']+86400 < time() && isset($_SESSION['id'])) { ?><img src="<?=$static_url?>/images/icons/avatar/pokeball.png" title="Clique aqui para receber seu bônus diário." onclick="Game.getDailyBonus(this);"><?php } ?>
									<?php
										$bonus_exp = DB::exQuery("SELECT `id`, `config`, `valor` FROM configs WHERE config='exp'")->fetch_assoc()['valor'];
										$bonus_sil = DB::exQuery("SELECT `id`, `config`, `valor` FROM configs WHERE config='silver'")->fetch_assoc()['valor'];
										$exp_sil_conf = array ('Double', 'Triple', 'Quadruple');

										if ($bonus_exp > 1 && $bonus_exp < 5) { ?><img src="<?=$static_url?>/images/icons/avatar/<?=$bonus_exp?>x-exp.png" title="Campanha <?=$exp_sil_conf[$bonus_exp-2]?> EXP em ANDAMENTO!"><?php }
										if ($bonus_sil > 1 && $bonus_sil < 5) { ?><img src="<?=$static_url?>/images/icons/avatar/<?=$bonus_sil?>x-silver.png" title="Campanha <?=$exp_sil_conf[$bonus_sil-2]?> SILVERS em ANDAMENTO!"><?php } ?>
								</div>
							</li>
							<li class="hub-hud-line" style="width: 200px">
								<div class="my_pokemons" style="float: right">
								<?php if ($gebruiker['in_hand'] > 0) { 
										$pkm_count = 6;
										while($pokemon = $pokemon_sql->fetch_assoc()) {
											$date = date('Y-m-d H:i:s', time() - 600);

											if (($pokemon['ei'] == 1) && ($pokemon['ei_tijd'] < $date)) {
												update_pokedex($pokemon['wild_id'], '', 'ei');
												DB::exQuery("UPDATE `pokemon_speler` SET `ei`='0' WHERE `id`=" . $pokemon['id'] . " LIMIT 1");
												$event = '<img src="'.$static_url.'/images/icons/blue.png" width="16" height="16" class="imglower" /> Seu Ovo Pokémon chocou! É um <a href="./pokemon-profile&id='.$pokemon['id'].'" title="Clique aqui para ver o Perfil deste Pokémon!">'.$pokemon['naam'].'</a>!';
												DB::exQuery("INSERT INTO `gebeurtenis` (`datum`,`ontvanger_id`,`bericht`,`gelezen`) VALUES (NOW(), '".$_SESSION['id']."', '".$event."', '0')");
											}

											$pokemon = pokemonei($pokemon, $txt);
											$popup = pokemon_popup($pokemon, $txt);
											$pokemon['naam'] = pokemon_naam($pokemon['naam'], $pokemon['roepnaam']);

											echo '<div class="icon"><div style="background-image: url(\'' . $static_url . '/' . $pokemon['animatie'] . '\');" class="tip_bottom-left' . ($pokemon['leven'] < 1 ? ' dead' : '') . '" title="' . $popup . '"></div></div>';
				
											$pkm_count--;
										}

										for ($i = 0; $i < $pkm_count; $i++) echo '<div class="icon"></div>';
										$pokemon_sql->data_seek(0);
									} else { 
										for ($i = 0; $i < 6; $i++) echo '<div class="icon"></div>';
									}
									?>
								</div>
							</li>
						</ul>
					</div>
				</div>
				<div id="navbar">
					<div class="menu tip_bottom-middle" title="<?php include 'app/includes/resources/menu/menu_hover.php'; ?>"><p>MENU</p></div>
					<div class="content">
						<center><ul>
							<?php if ($_SESSION['share_acc'] == 0){ ?><li><a href="./gold-market" class="noanimate">Gold Market &bull;</a></li><?php } ?>
							<li><a href="./town" class="noanimate">Cidade &bull;</a></li>
							<li><a href="./rankinglist" class="noanimate">Classificação &bull;</a></li>
							<li><a href="./box" class="noanimate">Box Pokémon &bull;</a></li>
							<?php if ($_SESSION['share_acc'] == 0){ ?><li><a href="./items" class="noanimate">Mochila &bull;</a></li><?php } ?>
							<li><a href="./attack/attack_map" class="noanimate">Mapa &bull;</a></li>
							<li><a href="./trainer" class="noanimate">NPC's &bull;</a></li>
						</ul></center>
					</div>
				</div>
				<?=$div_ballon;?>
				<div id="content">
					<div class="output">
						<div class="right">
							<?php require_once('language/language-pages.php'); ?>
							<?php $pageTitle = explode(' - ', $txt['pagetitle']); ?>
							<div id="main-content">
								<div class="content">
									<center>
										<?php
											if (isset($_SESSION['id'])) {
												if ($_SESSION['share_acc'] == 1) {
													echo '<div class="sharing_account">Você está na conta de '.$gebruiker['username'].' via compartilhamento. <i class="material-icons" style="color: #fff; display: inline-block; vertical-align:middle; font-size:15px">lock</i></div>';
												}
											}
										?>

										<?php //if (isset($_SESSION['user'])) echo '<div class="green">' . sprintf($txt['after_register'], $_SESSION['user']) . '</div>'; ?>
										<?php require_once($page . '.php'); ?>
										<?php if (isset($_SESSION['id']) && !$chat_block && $gebruiker['rank'] >= 3 && $gebruiker['admin'] >= 3 && $gebruiker['chat'] == '1') //require_once('app/includes/resources/chat.php'); ?>
										<?php if (isset($_SESSION['id'])) { ?>
										<div id="players_on">
										<?php require_once('online.php'); ?>
										</div>
										<?php } ?>
										<div class="box-content" style="float: left; width: 100%; margin-top: 7px;margin-bottom: 10px">
										    <h3 class="title">PUBLICIDADE:</h3>
										    <a href="https://www.pokeshop.com.br/" class="noanimate"><img src="<?=$static_url?>/images/layout/banner/pokeshop.png" style="padding: 5px"></a>
										</div>
									</center>
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<?php
		} else {
		?>
		
		<?php require_once('language/language-pages.php'); ?>
		
		<div id="wrap">
			<div id="container_wrap">
				<div id="container_login" <?= ($page != 'register')? 'style="height: 600px"': '';?>>
					<a href="./" class="noanimate">
						<div id="logo_login" class="logo_<?=rand(1, 5)?>"></div>
					</a>
					<?php require_once($page . '.php'); ?>
				</div>
				<div id="container_menu">
					<ul>
						<li>
							<a href="./">
								<img src="<?=$static_url?>/images/layout/menu/inicio.png"> Início
							</a>
						</li>
						<li>
							<a href="./register">
								<img src="<?=$static_url?>/images/layout/menu/registro.png"> Registro
							</a>
						</li>
						<li>
							<a href="./forgot">
								<img src="<?=$static_url?>/images/layout/menu/faq.png"> Recuperar
							</a>
						</li>
					</ul>
				</div>
			</div>
		</div>

		<?php } ?>

		<div id="social_menu">
			<ul>
				<li>
					<a href="https://www.facebook.com/pkworldlegends/" class="noanimate" target="_blank">
						<img src="<?=$static_url?>/images/layout/menu/social/facebook.png">
					</a>
				</li>
				<li>
					<a href="https://twitter.com/pkworldlegends/" class="noanimate" target="_blank">
						<img src="<?=$static_url?>/images/layout/menu/social/twitter.png">
					</a>
				</li>
				<li>
					<a href="https://www.instagram.com/pkworldlegends/" class="noanimate" target="_blank">
						<img src="<?=$static_url?>/images/layout/menu/social/instagram.png">
					</a>
				</li>
				<li>
					<a href="#" class="noanimate">
						<img src="<?=$static_url?>/images/layout/menu/social/youtube.png">
					</a>
				</li>
			</ul>
		</div>
		<div id="footer-pokes"></div>
		<div id="footer">
			<div id="footer-container">
				<center>
					<table>
						<tr>
							<td id="footer-left">
								<a href="./"><img src="<?=$static_url?>/images/layout/logo_footer.png" alt="Logo Pokémon World Legends"></a>
							</td>
							<td id="footer-right">
								<b>Pokémon</b> é uma marca registrada da <b>Nintendo</b>. Sua utilização é de caráter exclusivo ao <b>fã game</b>. <br>
								<p style="font-size: 13px">Nós não somos afiliados da <b>Nintendo</b>, da <b>Pokémon Company Creatures Inc.</b> ou da <b>Game Freak</b>.</p>

								<p style="font-size: 13px">Não há intenção de violação de direitos autorais ou marcas registradas. | <br><a href="ajax.php?act=privacy" class="colorbox-privacy">Política de Privacidade</a> / <a href="ajax.php?act=terms" class="colorbox-terms">Termos de Serviço</a> / <a href="ajax.php?act=rules" class="colorbox-rules">Regras e Punições</a></p>
								
								Todos os horários do jogo são baseados em UTC (0:00), exceto anúncios de eventos, alerta de manuntenção (-3:00).
							    
								Para notícias, eventos e atualizações, siga-nos no <a href="https://www.facebook.com/pkworldlegends/" target="_blank">Facebook</a> / <a href="https://twitter.com/pkworldlegends/" target="_blank">Twitter</a>.
							</td>
						</tr>
					</table>
				</center>
			</div>
			<div id="to_top"></div>
		</div>

		<script src="<?=$static_url?>/javascripts/wl.tabs.js"></script>
		<script src="<?=$static_url?>/javascripts/app.js"></script>
		<script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
        <script>
          (adsbygoogle = window.adsbygoogle || []).push({
            google_ad_client: "ca-pub-8887891179905445",
            enable_page_level_ads: true
          });
        </script>
	</body>
</html>
<?php ob_flush(); ?>
