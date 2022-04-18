<?php
$gebruiker_item = DB::exQuery("SELECT * FROM `gebruikers_item` WHERE `user_id`='".$_SESSION['id']."' LIMIT 1")->fetch_assoc();
$continue = false;
if ($gebruiker_item[$_GET['name']] <= 0) {
	echo '<script type="text/javascript">parent.$.colorbox.close();</script>';
} else {
	if ($_GET['name'] == "Protein")		$wat = "attack";
	else if ($_GET['name'] == "Iron")		$wat = "defence";
	else if ($_GET['name'] == "Carbos")	$wat = "speed";
	else if ($_GET['name'] == "HP up")	$wat = "hp";
	else if ($_GET['name'] == "Calcium")	$wat = "spc";

	if (isset($_POST['spcitem']) && isset($_POST['pokemonid']) && is_numeric($_POST['num_' . $_POST['pokemonid']])) {
		$num_ = round($_POST['num_' . $_POST['pokemonid']]);
		$add_points = $num_ * 3;

		$real = DB::exQuery("SELECT `pokemon_wild`.*,`pokemon_speler`.* FROM `pokemon_wild` INNER JOIN `pokemon_speler` ON `pokemon_speler`.`wild_id`=`pokemon_wild`.`wild_id` WHERE `pokemon_speler`.`id`={$_POST['pokemonid']}")->fetch_assoc();
		
	if ($num_ == "") {
	$error = "<div class='red'>Digite um valor.</div></center>";
	}
	else if (preg_match('/[A-Za-z_]+$/',$num_)) {
	$error = "<div class='red'>Digite um valor.</div></center>";
    }
  else if ($num_ < 1) {
	$error = "<div class='red'>Digite um valor.</div></center>";
  } else if (!is_numeric($num_)) {
	$error = "<div class='red'>Digite um valor.</div></center>";
	} 
 else if ($real[$wat.'_up'] >= 75) {
    $error = "<div class='red'> Você já usou 25x ".$_POST['item']." em ".$real['naam'].".</div>";
	}
  else if ($real[$wat.'_up']+$add_points > 75) {
  $valorupok = $real[$wat.'_up']/3;
  $somatoria = 25 - $valorupok;
 	$error = "<div class='red'> Máximo a ser usado de ".$somatoria."x ".$_GET['name'].".</div>";  
 }		
		else if ($num_ <= 0) { $error = '<div class="red">A quantidade deve ser maior que zero!</div>'; }
		else if ($gebruiker_item[$_POST['item']] < $num_) { $error = '<div class="red">Você não possui ' . $num_ . ' ' . $_POST['item'] . '!</div>'; }
		else {
			# Get infos
			$info = DB::exQuery("SELECT * FROM `karakters` WHERE `karakter_naam`='" . $real['karakter'] . "' LIMIT 1")->fetch_assoc();
			$real[$wat . '_up'] += $add_points;

			# Calc stats
			$attackstat		= round((((($real['attack_iv'] + 2 * $real['attack_base'] + floor($real['attack_ev'] / 4)) * $real['level'] / 100) + 5) + $real['attack_up']) * $info['attack_add']);
			$defencestat	= round((((($real['defence_iv'] + 2 * $real['defence_base'] + floor($real['defence_ev'] / 4)) * $real['level'] / 100) + 5) + $real['defence_up']) * $info['defence_add']) ;
			$speedstat		= round((((($real['speed_iv'] + 2 * $real['speed_base'] + floor($real['speed_ev'] / 4)) * $real['level'] / 100) + 5) + $real['speed_up']) * $info['speed_add']);
			$spcattackstat	= round((((($real['spc.attack_iv'] + 2 * $real['spc.attack_base'] + floor($real['spc.attack_ev'] / 4)) * $real['level'] / 100) + 5) + $real['spc_up']) * $info['spc.attack_add']);
			$spcdefencestat	= round((((($real['spc.defence_iv'] + 2 * $real['spc.defence_base'] + floor($real['spc.defence_ev'] / 4)) * $real['level'] / 100) + 5) + $real['spc_up']) * $info['spc.defence_add']);
			$hpstat			= round(((($real['hp_iv'] + 2 * $real['hp_base'] + floor($real['hp_ev'] / 4)) * $real['level'] / 100) + 10 + $real['level']) + $real['hp_up']);
			DB::exQuery("UPDATE `pokemon_speler` SET `{$wat}_up`=`{$wat}_up`+{$add_points},`levenmax`='" . $hpstat . "',`leven`='" . $hpstat . "',`attack`='" . $attackstat . "',`defence`='" . $defencestat . "',`speed`='" . $speedstat . "',`spc.attack`='" . $spcattackstat . "',`spc.defence`='" . $spcdefencestat . "' WHERE `id`='" . $real['id'] . "' LIMIT 1");

			# Remove item
			DB::exQuery("UPDATE `gebruikers_item` SET `" . $_POST['item'] . "`=`" . $_POST['item'] . "`-'".$num_."' WHERE `user_id`='" . $_SESSION['id'] . "' LIMIT 1");
			$continue = true;
		}
	}
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
	<link rel="shortcut icon" href="favicon.ico" type="image/x-icon" />
	<title><?=$site_title;?></title>
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge"/>
	<link rel="stylesheet" type="text/css" href="<?=$static_url;?>/stylesheets/style.css?<?=rand(1111, 9999);?>" />
</head>
<body>
<?php
	if ($continue) {
?>
<script type="text/javascript">
	var num = parent.$('#num_<?=str_replace(' ', '_', $_POST['item']);?>').html().replace('x', '').replace('<b>', '').replace('</b>', '');
	if ((num - <?=$num_;?>) > 0)	parent.$('#num_<?=str_replace(' ', '_', $_POST['item']);?>').html('<b>'+(num - <?=$num_;?>)+'x</b>');
	else {
		parent.$('#num_<?=str_replace(' ', '_', $_POST['item']);?>').empty().parent().remove();
		parent.$.colorbox.close();
	}
</script>
<?php
	}
?>
<form action="./ajax.php?act=<?=$_GET['act'];?>&amp;name=<?=$_GET['name'];?>" method="post">
	<div class="box-content"><table class="general" width="100%">
		<thead>
			<tr><th colspan="10">Escolha o pokémon que você vai usar <?=$_GET['name']?>.<br> (Você tem <?=$gebruiker_item[$_GET['name']]?> <?=$_GET['name']?>)</th></tr>
			<tr>
				<th width="20">#</th>
				<th width="80">Quantidade</th>
				<th width="200" colspan="2">Pokemon</th>
				<th width="50">Nível</th>
				<th width="30"><img src="<?=$static_url;?>/images/items/Protein.png" title="Protein" /></th>
				<th width="30"><img src="<?=$static_url;?>/images/items/Iron.png" title="Iron" /></th>
				<th width="30"><img src="<?=$static_url;?>/images/items/Carbos.png" title="Carbos" /></th>
				<th width="30"><img src="<?=$static_url;?>/images/items/HP up.png" title="HP up" /></th>
				<th width="30"><img src="<?=$static_url;?>/images/items/Calcium.png" title="Calcium" /></th>
			</tr>
		</thead>
		<tbody>
		<?php if (!empty($error)) { echo '<tr><td colspan="10">' . $error . '</td></tr>'; } ?>
<?php
	//Pokemon laden van de gebruiker die hij opzak heeft
	$poke = DB::exQuery("SELECT pokemon_wild.* ,pokemon_speler.* FROM pokemon_wild INNER JOIN pokemon_speler ON pokemon_speler.wild_id = pokemon_wild.wild_id WHERE user_id='".$_SESSION['id']."' AND `opzak`='ja' ORDER BY `opzak_nummer` ASC");

	//Pokemons die hij opzak heeft weergeven  
	for($teller=0;$pokemon=$poke->fetch_assoc();++$teller) {
		if ($pokemon['ei'] != 1) {
			$pokemon = pokemonei($pokemon, $txt);
			$pokemon['naam'] = pokemon_naam($pokemon['naam'],$pokemon['roepnaam'],$pokemon['icon']);
		
			echo '<tr>';
			//Als pokemon geen baby is
				if ($pokemon['ei'] != 1 && $pokemon[$wat . '_up'] < 75)	echo '<td align="center"><input type="radio" name="pokemonid" value="'.$pokemon['id'].'" /></td>
					<td align="center"><input type="text" name="num_'.$pokemon['id'].'" placeholder="Ex: 1" style="width: 50px;" /></td>';
				else	echo '<td align="center"><input type="radio" id="niet'.$i.'" name="niet" disabled /></td></td>
					<td align="center"><input type="text" name="num_'.$pokemon['id'].'" placeholder="Ex: 1" style="width: 50px;" disabled /></td>';
				echo '<td align="center" width="32"><img src="' . $static_url . '/' . $pokemon['animatie'] . '" width="32" height="32" /></td>
				<td width="168">'.$pokemon['naam'].'</td>
				<td align="center">'.$pokemon['level'].'</td>
				<td align="center">'.($pokemon['attack_up'] / 3).'</td>
				<td align="center">'.($pokemon['defence_up'] / 3).'</td>
				<td align="center">'.($pokemon['speed_up'] / 3).'</td>
				<td align="center">'.($pokemon['hp_up'] / 3).'</td>
				<td align="center">'.($pokemon['spc_up'] / 3).'</td>
			</tr>';
		}
	}
?>
		</tbody>
		<tfoot><tr>
			<td colspan="10" align="right">
				<input type="hidden" name="item" value="<?=$_GET['name'];?>" />
				<input type="submit" name="spcitem" value="Ok!" class="button" />
			</td>
		</tr></tfoot>
	</table></div>
</form>
</body>
</html>
<?php } ?>