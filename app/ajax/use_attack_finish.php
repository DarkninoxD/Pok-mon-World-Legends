<?php
$gebruiker_item = DB::exQuery("SELECT * FROM `gebruikers_tmhm` WHERE `user_id`=" . $_SESSION['id'] . " LIMIT 1")->fetch_assoc();
if ($gebruiker_item[$_GET['name']] <= 0) echo '<script>parent.$.colorbox.close();</script>';
?>
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
#Gegevens laden van de des betreffende pokemon
$pokemoninfo  = DB::exQuery("SELECT `pokemon_wild`.`wild_id`,`pokemon_wild`.`naam`,`pokemon_wild`.`type1`,`pokemon_wild`.`type2`,`pokemon_speler`.`id`,`pokemon_speler`.`user_id`,`pokemon_speler`.`aanval_1`,`pokemon_speler`.`aanval_2`,`pokemon_speler`.`aanval_3`,`pokemon_speler`.`aanval_4` FROM `pokemon_wild` INNER JOIN `pokemon_speler` ON `pokemon_wild`.`wild_id`=`pokemon_speler`.`wild_id` WHERE `id`=" . $_GET['pokemonid'] . " LIMIT 1")->fetch_assoc();

#Naam van de aanval
$attacknaam = DB::exQuery("SELECT `omschrijving` FROM `tmhm` WHERE `naam`='" . $_GET['name'] . "' LIMIT 1")->fetch_assoc();

$check = DB::exQuery("SELECT * FROM `tmhm` WHERE `naam`='" . $_GET['name'] . "' LIMIT 1")->fetch_assoc();
//$getWilds = DB::exQuery("SELECT `wilds` FROM `tmhm_wilds` WHERE `naam`='{$_GET['name']}' LIMIT 1")->fetch_assoc();


	$check2 = DB::exQuery("SELECT * FROM tmhm_relacionados WHERE `naam`='".$_GET['name']."'")->fetch_assoc();


if ($_POST['annuleer'])	echo '<script>parent.$.colorbox.close();</script>';

$aprende = 0;
$pegaidpokes = explode(",", $check2['relacionados']);
  foreach($pegaidpokes as $pokemonid) {
    if (!empty($pokemonid)) {
  if ($pokemonid == $pokemoninfo['wild_id']) {
  	//$kan = "<img src='../images/icons/green.png' alt='Aplicável'>";
  	//$disabled = '';
  	$aprende = $aprende + 1;
  	}
  }
  }
  
  

if (empty($_GET['pokemonid']) || !is_numeric($_GET['pokemonid'])) {
	echo '<div class="red">Escolha um pokemon!</div>';
	$foutje = 1;
} else if ($aprende == 0) {
	echo '<div class="red">Seu pokémon não pode aprender este ataque!</div>';
	$foutje = 1;
} else if ($pokemoninfo['user_id'] != $_SESSION['id']) {
	echo '<div class="red">Este pokémon não é seu!</div>';
	$foutje = 1;
} else if ($pokemoninfo['aanval_1'] == $check['omschrijving'] OR $pokemoninfo['aanval_2'] == $check['omschrijving'] OR $pokemoninfo['aanval_3'] == $check['omschrijving'] OR $pokemoninfo['aanval_4'] == $check['omschrijving']) {
	echo '<div class="red">' . $pokemoninfo['naam'] . ' já aprendeu o ataque ' . $tmhm['omschrijving'] . '!</div>';
	$foutje = 1;
} else {
	if (isset($_POST['attack'])) {
		DB::exQuery("UPDATE `pokemon_speler` SET `aanval_" . $_POST['welke'] . "`='" . $check['omschrijving'] . "' WHERE `id`=" . $_GET['pokemonid'] . " LIMIT 1");
		//DB::exQuery("UPDATE `gebruikers_item` SET `items`=`items`-'1'");

		$kortenaam  = substr($_GET['name'], 0, 2);
		if ($kortenaam == 'TM') DB::exQuery("UPDATE gebruikers_tmhm SET ".$_GET['name']." = ".$_GET['name']." -'1' WHERE user_id='".$_SESSION['id']."'");
		?>
<script type="text/javascript">
	var num = parent.$('#num_<?=str_replace(' ', '_', $_GET['name']);?>').html().replace('x', '').replace('<b>', '').replace('</b>', '');
	if ((num - 1) > 0)	parent.$('#num_<?=str_replace(' ', '_', $_GET['name']);?>').html('<b>'+(num - 1)+'x</b>');
	else {
		parent.$('#num_<?=str_replace(' ', '_', $_GET['name']);?>').empty().parent().parent().remove();
		parent.$.colorbox.close();
	}
</script>
<?php
	}
}
if ($foutje == 1)	echo $error;
else {
?>
<div class="box-content"><table width="100%" class="general">
	<thead>
		<tr><th colspan="3"><?=$pokemoninfo['naam'];?> está aprendendo <?=$_GET['name'];?> - <?=$attacknaam['omschrijving'];?>.</th></tr>
	</thead>
	<tbody>
		<tr>
			<td style="width: 140px; height: 125px; background: url('<?=$static_url;?>/images/pokemon/<?=$pokemoninfo['wild_id'];?>.gif') center no-repeat;" rowspan="2"></td>
			<form method="post">
				<td width="178" align="center"><input type="submit" name="attack" value="<?=$pokemoninfo['aanval_1'];?>" class="button" /></td>
				<input type="hidden" name="welke" value="1" />
			</form>
			<form method="post">
				<td width="178" align="center"><input type="submit" name="attack" value="<?=$pokemoninfo['aanval_2'];?>" class="button" /></td>
				<input type="hidden" name="welke" value="2" />
			</form>
		</tr>
		<tr>
			<form method="post">
				<td align="center"><input type="submit" name="attack" value="<?=$pokemoninfo['aanval_3'];?>" class="button" /></td>
				<input type="hidden" name="welke" value="3" />
			</form>
			<form method="post">
				<td align="center"><input type="submit" name="attack" value="<?=$pokemoninfo['aanval_4'];?>" class="button" /></td>
				<input type="hidden" name="welke" value="4" />
			</form>
		</tr>
	</tbody>
	<tfoot><tr><td colspan="3" align="right"><form method="post"><input type="submit" name="annuleer" value="Cancelar" class="button" /></form></td></tr></tfoot>
</table></div>
<?php } ?>
</body>
</html>