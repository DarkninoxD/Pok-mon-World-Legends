<?php
require_once 'app/includes/resources/ingame.inc.php';

$txt['event_is_level_up'] = '<b>%s</b> passou de nível!';
$gebruiker_item = DB::exQuery("SELECT * FROM `gebruikers_item` WHERE `user_id`='".$_SESSION['id']."'")->fetch_assoc();
$continue = false;
if ($gebruiker_item[$_GET['name']] <= 0) {
?>
<script type="text/javascript">
	parent.$.fn.colorbox.close();
</script>
<?php
} else {
	if (isset($_POST['spcitem']) && isset($_POST['pokemonid']) && is_numeric($_POST['pokemonid']) && isset($_POST['item'])) {
		$pokemon = DB::exQuery("SELECT * FROM pokemon_speler WHERE id='".$_POST['pokemonid']."' AND user_id='$_SESSION[id]'")->fetch_assoc();
		$item = $_POST['item'];

		if ($gebruiker_item[$item] < 0) {
			$error = "<div class='red'>Você não possui este item!</div>";
		} else if (!pokemon_equip($pokemon['wild_id'], $item)) {
			$error = "<div class='red'>Este item não pode ser equipado neste Pokémon!</div>";
		} else if ($_POST['spcitem'] == $pokemon['item']) {
			$error = "<div class='red'>O Pokémon já tem este item equipado!</div>";
		} else if ($pokemon['ei'] != 0) {
			$error = "<div class='red'>Este item não pode ser equipado em Ovo Pokémon!</div>";
		} else {
			DB::exQuery("UPDATE `gebruikers_item` SET `$item`=`$item`-1 WHERE `user_id`='".$_SESSION['id']."'");
			DB::exQuery("UPDATE `pokemon_speler` SET `item`='$item' WHERE id='$_POST[pokemonid]'");
			
			//Sucesso!
			$continue = true;
			$error = "<div class='green'>O item ".$item." foi equipado com sucesso!</div>";
		}
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
	var num = parent.$('#num_<?=str_replace(' ', '_', $_GET['name']);?>').html().replace('x', '').replace('<b>', '').replace('</b>', '');
	if ((num - 1) > 0)	parent.$('#num_<?=str_replace(' ', '_', $_GET['name']);?>').html('<b>'+(num - 1)+'x</b>');
	else {
		parent.$('#num_<?=str_replace(' ', '_', $_GET['name']);?>').empty().parent().remove();
		parent.$.colorbox.close();
	}
</script>
<?php
	}
?>
<form action="./ajax.php?act=<?=$_GET['act'];?>&amp;name=<?=$_GET['name'];?>" method="post">
	<div class="box-content"><table class="general" width="100%">
		<thead>
			<tr><th colspan="5">Escolha qual pokémon irá receber o item <?=$_GET['name'];?>.</th></tr>
			<tr>
				<th style="max-width: 10px;">#</th>
				<th colspan="2">Pokémon</th>
				<th style="max-width: 50px;">Equipado</th>
			</tr>
			<tr><th colspan="5">Se este Pokémon já tiver um item equipado, o item antigo será destruído!</th></tr>
		</thead>
		<tbody>
		<?php if (!empty($error)) { echo '<tr><td colspan="10">' . $error . '</td></tr>'; } ?>
<?php
	//Pokemon laden van de gebruiker die hij opzak heeft
	$poke = DB::exQuery("SELECT pokemon_wild.* ,pokemon_speler.* FROM pokemon_wild INNER JOIN pokemon_speler ON pokemon_speler.wild_id = pokemon_wild.wild_id WHERE user_id='".$_SESSION['id']."' AND `opzak`='ja' ORDER BY `opzak_nummer` ASC");

	//Pokemons die hij opzak heeft weergeven  
	for($teller=0;$pokemon=$poke->fetch_assoc();++$teller) {
		$pokemon = pokemonei($pokemon, $txt);
		$pokemon['naam'] = pokemon_naam($pokemon['naam'],$pokemon['roepnaam'],$pokemon['icon']);
		echo '<tr>';
			if ($pokemon['ei'] == 0 && pokemon_equip($pokemon['wild_id'], $_GET['name']) && $_GET['name'] != $pokemon['item'])	echo '<td align="center"><input type="radio" name="pokemonid" value="'.$pokemon['id'].'" /></td>';
			else	echo '<td align="center"><input type="radio" name="pokemonid" value="'.$pokemon['id'].'" disabled /></td></td>';
			
			$icon = '';
			if (isset($pokemon['item']))
				$icon = '<img src="'.$static_url.'/images/icons/blue.png" title="Equipado com '.$pokemon['item'].'"> + <img src="'.$static_url.'/images/items/'.$pokemon['item'].'.png" title="'.$pokemon['item'].'">';
			else
				$icon = '<img src="'.$static_url.'/images/icons/green.png" title="Não equipado">';
									
			echo '<td align="center" width="32"><img src="' . $static_url . '/' . $pokemon['animatie'] . '" width="32" height="32" /></td>
				<td width="168">'.$pokemon['naam'].'</td>
				<td align="center">'.$icon.'</td>
		</tr>';
	}
?>
		</tbody>
		<tfoot><tr>
			<td colspan="5" align="right">
				<input type="hidden" name="item" value="<?=$_GET['name'];?>" />
				<input type="submit" name="spcitem" value="Equipar" class="button blue" />
			</td>
		</tr></tfoot>
	</table></div>
</form>
</body>
</html>