<!DOCTYPE html>
<html lang="pt-br">
<head>
	<link rel="shortcut icon" href="favicon.ico" type="image/x-icon" />
	<title><?=$site_title;?></title>
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge"/>
	<link rel="stylesheet" type="text/css" href="<?=$static_url;?>/stylesheets/style.css" />
</head>
<body>
<?php
$select = DB::exQuery("SELECT `pokemon_speler`.`id`,`pokemon_speler`.`icon`,`pokemon_speler`.`can_trade`,`pokemon_speler`.`gevongenmet`,`pokemon_speler`.`user_id`,`pokemon_speler`.`gehecht`,`pokemon_speler`.`opzak`,`pokemon_speler`.`shiny`,`pokemon_speler`.`level`,`pokemon_wild`.`wild_id`,`pokemon_wild`.`zeldzaamheid`,`pokemon_wild`.`naam`,`gebruikers`.`silver`,`gebruikers`.`premiumaccount`,`gebruikers`.`rank`,`gebruikers`.`admin`,`rekeningen`.`gold` FROM `pokemon_speler` INNER JOIN `pokemon_wild` ON `pokemon_speler`.`wild_id`=`pokemon_wild`.`wild_id` INNER JOIN `gebruikers` ON `pokemon_speler`.`user_id`=`gebruikers`.`user_id` INNER JOIN `rekeningen` ON `gebruikers`.`acc_id`=`rekeningen`.`acc_id` WHERE `pokemon_speler`.`id`='".$_GET['id']."' LIMIT 1")->fetch_assoc();


if ($select['user_id'] != $_SESSION['id'])	echo '<div class="red">' . $txt['alert_not_your_pokemon'] . '</div>';
else if ($select['gehecht'] == 1)	echo '<div class="red">'.$txt['alert_beginpokemon'].'</div>';
else {
		$pokemonnaam = pokemon_naam($select['naam'], $select['roepnaam'],$select['icon']);
		if ($select['shiny'] == 1)	$shiny = '<img src="'. $static_url .'/images/icons/lidbetaald.png" style="vertical-align: -3px;" />'; 
		else	$shiny = '';

		if (isset($_POST['release'])) {


		
	 DB::exQuery("UPDATE gebruikers_item SET `".$select['gevongenmet']."`=`".$select['gevongenmet']."`+'1' WHERE `user_id`='".$_SESSION['id']."'");

	  	 if (DB::exQuery("SELECT id FROM pokemon_speler WHERE wild_id='".$select['wild_id']."'")->num_rows == 1) update_pokedex($select['wild_id'],'','release');

		
		$select1 = DB::exQuery("SELECT `id`,`opzak_nummer` FROM `pokemon_speler` WHERE `user_id`='" . $_SESSION['id'] . "' AND `id`!='" . $select['id'] . "' AND `opzak`='ja' ORDER BY `opzak_nummer` ASC");
		for($i=1;$selectx=$select1->fetch_assoc();++$i) {
			#Alle opzak_nummers ééntje lager maken van alle pokemons die over blijven
			DB::exQuery("UPDATE `pokemon_speler` SET `opzak_nummer`='" . $i . "' WHERE `id`='" . $selectx['id'] . "' LIMIT 1");
		}
		
		$date = date("Y-m-d H:i:s");
		DB::exQuery("INSERT INTO release_log (date, user_id, poke_id, wild_id, pokeball) VALUES (NOW(), '".$_SESSION['id']."', '".$select['id']."', '".$select['wild_id']."', '".$select['gevongenmet']."')");
		

		//DB::exQuery("DELETE FROM pokemon_speler WHERE id = '".$id."'");
		DB::exQuery("UPDATE pokemon_speler SET user_id = '0', release_user = '".$_SESSION['id']."', release_date = NOW() WHERE id = '".$select['id']."'");

		DB::exQuery("DELETE FROM transferlijst WHERE id = '".$select['id']."'");

		DB::exQuery("UPDATE `gebruikers` SET `aantalpokemon`=`aantalpokemon`-'1' WHERE `user_id` = '".$_SESSION['id']."'");

		$quests->setStatus('release', $_SESSION['id']);
 		
		
		echo '<div class="green">' . $txt['alert_success_release'] . '</div>';
		echo '<script type="text/javascript">
		parent.$.colorbox.close();
		parent.location.reload();
		</script>';
		exit;
		
		
		
		
		}
?>

<div class="box-content">
<form method="post"><table class="general" style="width: 100%;">
	<thead>
		<tr><th colspan="3"><?=sprintf($txt['pagetitle'], $pokemonnaam . $shiny);?></th></tr>
		<tr>
			<th colspan="3" width="370"><?=$txt['information'];?></th>
		</tr>
	</thead>
	<tbody><tr>
		<td valign="top" style="padding: 0;"><table class="general" width="100%">
			<tr>
				<td width="90" align="right"><b><?=$txt['pokemon'];?></b>:</td>
				<td width="20" align="center"><img src="<?=$static_url;?>/images/items/<?=$select['gevongenmet']?>.png" /></td>
				<td align="left"><?=$pokemonnaam . $shiny;?> (<?=sprintf($txt['level'], $select['level']);?>)</td>
			</tr>
		</table></td>
		<td align="center" style="padding: 0; width: 100px; height: 80px; background: url('<?=$static_url;?>/images/<?=($select['shiny'] == 1 ? 'shiny' : 'pokemon');?>/<?=$select['wild_id'];?>.gif') center no-repeat;"></td>
	</tr></tbody>
	<tfoot><tr><td colspan="3" align="right"><b><?=$txt['irreversivel']?></b> <input type="submit" value="<?=$txt['button'];?>" name="release" class="button" /></td></tr></tfoot>
</table></form></div>
<?php
	}
?>
</body>
</html>