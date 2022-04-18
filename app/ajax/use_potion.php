<?php
$error = "Escolha o pokémon para qual você vai usar ".$_GET['name'].".";
$gebruiker_item = DB::exQuery("SELECT * FROM `gebruikers_item` WHERE `user_id`='".$_SESSION['id']."'")->fetch_assoc();
if ($gebruiker_item[$_GET['name']] <= 0) {
?>
<script type="text/javascript">
	parent.$.colorbox.close();
</script>
<?php
} else {
?>
<!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<title><?=$site_title;?></title>
	<link rel="stylesheet" type="text/css" href="<?=$static_url;?>/stylesheets/style.css" />
</head>
<body>
<?php
	//Als er op de heal knop gedrukt word
	if (isset($_POST['use']) && isset($_POST['pokemonid'])) {
		//Gegevens laden van de potion
		$name = $_GET['name'];
		$itemgegevens = DB::exQuery("SELECT `kracht`, `naam`, `wat`, `apart`, `type1`, `type2`, `kracht2` FROM `items` WHERE `naam`='$name'")->fetch_assoc();

		//Pokemon gegevens laden
		$pokemon = DB::exQuery("SELECT pokemon_wild.* ,pokemon_speler.* FROM pokemon_wild INNER JOIN pokemon_speler ON pokemon_speler.wild_id = pokemon_wild.wild_id WHERE pokemon_speler.id='".$_POST['pokemonid']."'")->fetch_assoc();
		$pokemon = pokemonei($pokemon, $txt);
		$pokemon['naam'] = pokemon_naam($pokemon['naam'],$pokemon['roepnaam'],$pokemon['icon']);

		$life = false;
		$stauts = false;
		$finish = false;
		$newlife = $pokemon['leven'];
		$effect = $pokemon['effect'];

		//Is de potion niet apart?
		if ($itemgegevens['apart'] == 'nee')	$life = true;
		else if ($itemgegevens['apart'] == 'ja')	$status = true;

		//Als er een gewone potion gebruikt is
		if ($life) {
			if ($pokemon['leven'] == $pokemon['levenmax'])	$error = '<div class="blue">'.$pokemon['naam'].' teve a vida 100% recuperada.</div>';
			else if ($pokemon['leven'] != $pokemon['levenmax']) {	//Is het leven 0 dan heeft potions geen nut
				// Calculate new life
				$newlife = $pokemon['leven']+$itemgegevens['kracht'];

				// Check life
				if ($newlife > $pokemon['levenmax'])	$newlife = $pokemon['levenmax'];

				//Save new life
				DB::exQuery("UPDATE `pokemon_speler` SET `leven`='".$newlife."' WHERE `id`='".$_POST['pokemonid']."'");
				$finish = true;
			}else	$error = '<div class="red">Você não pode curar '.$pokemon['naam'].'.</div>';
		} else if ($status) {	//Is er een aparte potion gebruikt?
			if ($itemgegevens['naam'] == "Full heal")	$effect = '';
			else if ($pokemon['leven'] == 0) {
				if ($itemgegevens['naam'] == "Revive")	$newlife = round($pokemon['levenmax'] / 2);
				else if ($itemgegevens['naam'] == "Max revive")	$newlife = $pokemon['levenmax'];
				DB::exQuery("UPDATE `pokemon_speler` SET `leven`='".$newlife."',`effect`='".$effect."' WHERE `id`='".$_POST['pokemonid']."' LIMIT 1");
				$finish = true;
			} else	$error = '<div class="red">Somente funciona em pokémons que estão totalmente sem vida.</div>';
		}
		if ($finish)	DB::exQuery("UPDATE `gebruikers_item` SET `".$name."`=`".$name."`-'1' WHERE `user_id`='".$_SESSION['id']."' LIMIT 1");
	}
?>
<center>
<form action="./ajax.php?act=<?=$_GET['act'];?>&amp;name=<?=$_GET['name'];?>" method="post">
	<div class="box-content">
	<table class="general" style="width: 100%;">
		<thead>
			<tr><th colspan="5"><? if ($error) echo $error; else "&nbsp;"; ?></th></tr>
			<tr> 
				<th width="40">#</td>
				<th width="200" colspan="2">Pokemon:</td>
				<th width="50">Nível</td>
				<th width="300">HP</td>
			</tr>
		</thead>
		<tbody>
<?php
	//Pokemon laden van de gebruiker die hij opzak heeft
	$poke = DB::exQuery("SELECT pokemon_wild.* ,pokemon_speler.* FROM pokemon_wild INNER JOIN pokemon_speler ON pokemon_speler.wild_id = pokemon_wild.wild_id WHERE user_id='".$_SESSION['id']."' AND `opzak`='ja' ORDER BY `opzak_nummer` ASC");

	//Pokemons die hij opzak heeft weergeven  
	for($teller=0;$pokemon=$poke->fetch_assoc();++$teller) {
		//Als leven niet 0 is en er word een Revive Of Max revive gebruikt, Dan is radio gedisabled
		$disabled = '';
		if ($pokemon['leven'] != 0 && ($_GET['name'] == "Revive" || $_GET['name'] == "Max revive"))	$disabled = 'disabled';
		else if ($pokemon['leven'] >= $pokemon['levenmax'])	$disabled = 'disabled';

		//Pagina includen dat berekend als het nog een pokemon ei is.
		$pokemon = pokemonei($pokemon, $txt);
		$pokemon['naam'] = pokemon_naam($pokemon['naam'],$pokemon['roepnaam'],$pokemon['icon']);

		echo "<tr>";
			//Als pokemon geen baby is
			if ($pokemon['ei'] != 1) {
				echo '<td><center><input type="radio" name="pokemonid" value="'.$pokemon['id'].'" '.$disabled.'/></center></td>
					<input type="hidden" name="teller" value="'.$teller.'">';               
			} else echo '<td align="center"><input type="radio" id="niet'.$teller.'" name="niet" disabled /></td>';
			echo '<td align="center" width="32"><img src="' . $static_url . '/'.$pokemon['animatie'].'" width="32" height="32" /></td>
				<td width="168">'.$pokemon['naam'].'</td>
				<td align="center">'.$pokemon['level'].'</td>';
			//Als pokemon geen baby is
			//if ($pokemon['ei'] != 1) {
				echo '<td><div class="bar_red">
					<div class="progress" style="width: '.$pokemon['levenprocent'].'%"></div>
				</div></td>';
			//} else	echo '<td>HP: Inapplicable</td></tr>';
	}
	echo "</tbody>";
	if (!$finish) {
?>
			<tfoot><tr><td colspan="5" align="right">
				<input type="hidden" name="item" value="<? echo $_GET['name']; ?>">
				<input type="submit" name="use" value="Ok!" class="button" />
			</td></tr></tfoot>
<?php
	} else {
?>
<script type="text/javascript">
	var num = parent.$('#num_<?=str_replace(' ', '_', $_GET['name']);?>').html().replace('x', '').replace('<b>', '').replace('</b>', '');
	if ((num - 1) > 0)	parent.$('#num_<?=str_replace(' ', '_', $_GET['name']);?>').html('<b>'+(num - 1)+'x</b>');
	else {
		parent.$('#num_<?=str_replace(' ', '_', $_GET['name']);?>').empty().parent().remove();
		parent.$.colorbox.close();
	}
</script>


<?php } ?>
	</table>
	</div>
</form>
</center>
</body>
</html>
<?php } ?>