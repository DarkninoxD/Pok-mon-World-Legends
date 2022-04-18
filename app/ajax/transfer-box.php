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
$gebruiker = DB::exQuery("SELECT `huis` FROM `gebruikers` WHERE `user_id`='".$_SESSION['id']."'")->fetch_assoc();
$huis = DB::exQuery("SELECT `ruimte` FROM `huizen` WHERE `afkorting`='".$gebruiker['huis']."'")->fetch_assoc();

$select = DB::exQuery("SELECT `pokemon_speler`.`id`,`pokemon_speler`.`icon`,`pokemon_speler`.`can_trade`,`pokemon_speler`.`gevongenmet`,`pokemon_speler`.`user_id`,`pokemon_speler`.`gehecht`,`pokemon_speler`.`opzak`,`pokemon_speler`.`shiny`,`pokemon_speler`.`level`,`pokemon_wild`.`wild_id`,`pokemon_wild`.`zeldzaamheid`,`pokemon_wild`.`naam`,`gebruikers`.`silver`,`gebruikers`.`premiumaccount`,`gebruikers`.`rank`,`gebruikers`.`admin`,`rekeningen`.`gold` FROM `pokemon_speler` INNER JOIN `pokemon_wild` ON `pokemon_speler`.`wild_id`=`pokemon_wild`.`wild_id` INNER JOIN `gebruikers` ON `pokemon_speler`.`user_id`=`gebruikers`.`user_id` INNER JOIN `rekeningen` ON `gebruikers`.`acc_id`=`rekeningen`.`acc_id` WHERE `pokemon_speler`.`id`='".$_GET['id']."' LIMIT 1")->fetch_assoc();

		$calc1 = $huis['ruimte'] / 50;
		$options = '';
		if ($calc1 < 1) $calc1 = 1;
		for ($i = 1; $i <= $calc1; $i++) {
		if ($_GET['box'] == $i) $selected = 'selected';
		else $selected = '';
    		$options .= '<option value="'.$i.'">Box '.$i.'</option>';
		}


if ($select['user_id'] != $_SESSION['id'])	echo '<div class="red">' . $txt['alert_not_your_pokemon'] . '</div>';
else if ($select['opzak'] == 'ja')	echo '<div class="red">'.$txt['alert_pokeequiped'].'</div>';
else {
		$pokemonnaam = pokemon_naam($select['naam'], $select['roepnaam'],$select['icon']);
		if ($select['shiny'] == 1)	$shiny = '<img src="'. $static_url .'/images/icons/lidbetaald.png" style="vertical-align: -3px;" />'; 
		else	$shiny = '';

		if (isset($_POST['transfer'])) {


$inicio = (($_POST['newbox']*50) - 50) + 1;
$fim    = ($_POST['newbox']*50);
$success = false;


for ($x = $inicio; $x <= $fim; $x++) {

$verifyy = DB::exQuery("SELECT id from `pokemon_speler` WHERE `opzak_nummer`='".$x."' AND `user_id`='".$_SESSION['id']."' AND `opzak`='nee'")->num_rows;

if ($verifyy == 0) { 
DB::exQuery("UPDATE `pokemon_speler` SET `opzak_nummer`='".$x."' WHERE `id`='".$select['id']."' AND `user_id`='".$_SESSION['id']."' AND `opzak`='nee'");
$success = true;
break;
}

}

             

		if ($success == false) {
 		echo '<div class="red">' . $txt['alert_fail'] . '</div>';
 		} else {
		echo '<div class="green">' . $txt['alert_success'] . '</div>';
		echo '<script type="text/javascript">
		parent.$.colorbox.close();
		parent.location.reload();
		</script>';
		exit;
		}
		
		
		
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
					<tr>
				<td width="90" align="right"><b><?=$txt['box1'];?></b>:</td>
				<td width="20" align="center"><?=$_GET['box'];?></td>
			</tr>
					<tr>
				<td width="90" align="right"><b><?=$txt['box2'];?></b>:</td>
				<td width="20" align="center">
				<select name="newbox" id="newbox"><?=$options;?></select></td>
			</tr>
			
		</table></td>
		<td align="center" style="padding: 0; width: 100px; height: 80px; background: url('<?=$static_url;?>/images/<?=($select['shiny'] == 1 ? 'shiny' : 'pokemon');?>/<?=$select['wild_id'];?>.gif') center no-repeat;"></td>
	</tr></tbody>
	<tfoot><tr><td colspan="3" align="right"><input type="submit" value="<?=$txt['button'];?>" name="transfer" class="button" /></td></tr></tfoot>
</table></form></div>
<?php
	}
?>
</body>
</html>