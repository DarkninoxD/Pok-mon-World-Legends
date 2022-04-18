<?php
#Script laden zodat je nooit pagina buiten de index om kan laden
require_once("app/includes/resources/security.php");

// if ($gebruiker['rank'] < 5 && $_GET['shopitem'] == 'attacks')  exit(header('LOCATION: ./market'));

if ($gebruiker['pagina'] == 'attack' && $_COOKIE['market_battle_used'] == '1') exit(header("LOCATION: ./attack/wild/wild-attack"));

#Kijken of je nog wel een itemplek overhebt
if ($gebruiker['item_over'] < 1) echo '<div class="blue">'.$txt['alert_itemplace'].'</div>';

#Ruimte vast stellen Per item.
#Vast stellen welke items nog te koop zijn.
if ($gebruiker['itembox'] == 'Black box') $ruimte['max'] = 1000;
else if ($gebruiker['itembox'] == 'Purple box') $ruimte['max'] = 500;
else if ($gebruiker['itembox'] == 'Red box') $ruimte['max'] = 250;
else if ($gebruiker['itembox'] == 'Blue box') $ruimte['max'] = 100;
else if ($gebruiker['itembox'] == 'Yellow box') $ruimte['max'] = 50;
else if ($gebruiker['itembox'] == 'Bag') $ruimte['max'] = 20;

$title = explode(' - ', $txt['pagetitle'])[1];
$att_time = 0;

echo addNPCBox(5, 'PokéMart', 'Olá, treinador! Posso lhe ajudar em algo? Bemm.. Vou adivinhar.. Está sem Poke ball?<br>
Se for, está no lugar certo! Veja nosso lindo estoque de Poké Balls... <br>Caso contrario temos vários outros <b>Itens e Pokémons</b> que irão te auxiliar em sua jornada!');
?>
<div class="orientation-bar" id="itens" style="margin-bottom: -1px">
	<a href="./market&shopitem=balls" data-orientation="balls" class="noanimate"><button type="button">Poké Balls</button></a>
	<a href="./market&shopitem=items" data-orientation="items" class="noanimate"><button type="button">Itens Chave</button></a>
	<a href="./market&shopitem=specialitems" data-orientation="specialitems" class="noanimate"><button type="button">Itens Especiais</button></a>
	<a href="./market&shopitem=potions" data-orientation="potions" class="noanimate"><button type="button">Poções</button></a>
	<a href="./market&shopitem=stones" data-orientation="stones" class="noanimate"><button type="button">Pedras</button></a>
	<a href="./market&shopitem=pokemon" data-orientation="pokemon" class="noanimate"><button type="button">Pokémons</button></a>
	<a href="./market&shopitem=attacks" data-orientation="attacks" class="noanimate"><button type="button">Ataques</button></a>
</div>
<?php
#Pagina's opbouwen
switch($_GET['shopitem']) {
	#Als er op balls geklikt word. Het volgende laten zien
	case "balls":
		$sql = "SELECT `id`,`naam`,`silver`,`gold`,`omschrijving_" . $_COOKIE['pa_language'] . "` FROM `markt` WHERE `soort`='balls' AND `beschikbaar`='1' ORDER BY silver, gold";
		$result = query_cache($_GET['page'] . '_' . $_GET['shopitem'], $sql, $att_time);
		#Als er op de knop gedrukt word
		if (isset($_POST['balls'])) {
			$gebruiker_silver = $gebruiker['silver'];
			$rekening_gold = $rekening['gold'];

			#itemruimte over berekenen
			$ruimteover = $ruimte['max'] - $gebruiker['items'];

			#Laden voor de verwerking van de informatie
			for($i=1;$i<=$_POST['teller'];++$i) {
				#Item id opvragen
				$itemid = (int)$_POST['id' . $i];

				#Aantal opvragen van het itemid
				$aantal = (int)$_POST['aantal' . $itemid];
				if ($aantal > 0) {
					#Als er geen aantal is
					if (!is_int($aantal))	$niksingevoerd = true;
					else {	#Als er wel een aantal is
						#Item gegevens laden
						$itemgegevens = DB::exQuery("SELECT `naam`,`silver`,`gold` FROM `markt` WHERE `id`='".$itemid."' AND `beschikbaar`='1'  LIMIT 1")->fetch_assoc();

						#silver of gold berekenen voor de balls
							if ($itemgegevens['gold'] != 0) {
								$goldd = $aantal * ($itemgegevens['gold'] / 1);
								$goldd *= ($gebruiker['pagina'] == 'attack') ? 1.5 : 1;
							} else {
								$silverr = $aantal * ($itemgegevens['silver'] / 1);
								$silverr *= ($gebruiker['pagina'] == 'attack') ? 1.75 : 1;
							}
						

						#Kijken als het silver er wel voor is
						if ($gebruiker_silver < $silverr || $rekening_gold < $goldd) {
							echo '<div class="red">'.$txt['alert_not_enough_money'].'</div>';
							break;
						} else if ($aantal < 0) {
							echo '<div class="red">'.$txt['alert_not_enough_money'].'</div>';
							break;
						/*} else if (!ctype_digit($aantal)) {
							echo '<div class="red">'.$txt['alert_not_enough_money'].'</div>';
							break;*/
						} else if ($ruimteover < $aantal) {	#Als speler niet genoeg ruimte heeft voor de balls
							if ($aantal > 1)	$netheid = "&#39;s";
							echo '<div class="red">'.$txt['alert_itembox_full_1'].' '.$ruimteover.' '.$itemgegevens['naam'].$netheid.' '.$txt['alert_itembox_full_2'].'</div>';
							break;
						} else {	#Opslaan
							$totalesilver += $silverr;
							$gebruiker_silver -= $silverr;
							$totalegold += $goldd;
							$rekening_gold -= $goldd;
							$ruimteover -= $aantal;
							DB::exQuery("UPDATE `gebruikers_item` SET `".$itemgegevens['naam']."`=`".$itemgegevens['naam']."`+'".$aantal."' WHERE `user_id`='".$_SESSION['id']."' LIMIT 1");
							echo '<div class="green">'.$txt['success_market'].' '.$itemgegevens['naam'].' '.$aantal.'x.</div>';
						}
						$welingevoerd = true;
					}
				}
			}
			#silver opslaan
			DB::exQuery("UPDATE `gebruikers` SET `silver`=`silver`-'".$totalesilver."' WHERE `user_id`='".$_SESSION['id']."' LIMIT 1");
			DB::exQuery("UPDATE `rekeningen` SET `gold`=`gold`-'".$totalegold."' WHERE `acc_id`='".$_SESSION['acc_id']."' LIMIT 1");
			if ($gebruiker['pagina'] == 'attack')	setcookie('market_battle_used', '1', time() + ((86400 * 30) * 12), '/');
			if (!$welingevoerd) {
				if ($niksingevoerd)	echo '<div class="red">'.$txt['alert_nothing_selected'].'</div>';
			}
		}
?>
<form method="POST" name="balls">
	<div class="box-content" style="position: relative"><table class="general" width="100%">
		<thead><tr><th><?=$title;?></th></tr></thead>
		<tbody><tr>
			<td align="center">
<?php
		$j = 1;
		foreach($result as $id=>$select) {
	
				if ($select['gold'] != 0) {
					$icon = 'gold';
					$select['gold'] *= ($gebruiker['pagina'] == 'attack') ? 1.5 : 1;
				} else {
					$icon = 'silver';
					$select['silver'] *= ($gebruiker['pagina'] == 'attack') ? 1.75 : 1;
				}
	
			$prijs = highamount($select[$icon]);
?>
				<div class="greyborder">
					<table style="width:120px">
						<tr><td align="center">
							<input type="hidden" name="teller" value="<?=$j;?>" />
							<input type="hidden" name="id<?=$j;?>" value="<?=$select['id'];?>" />
							<img src="<?=$static_url;?>/images/items/<?=$select['naam'];?>.png" class="icon-img"/>
						</td></tr>
						<tr><td align="center"><span class="smalltext"><?=$select['naam'];?> <span style="cursor:pointer;" title="<?=nl2br($select['omschrijving_'.$_COOKIE['pa_language']]);?>"><b>[?]</b></span></span></td></tr>
						<tr><td align="center"><span class="smalltext"><img src="<?=$static_url;?>/images/icons/<?=$icon;?>.png" style="margin-bottom:-3px;" /> <?=$prijs;?></span></td></tr>
						<tr><td align="center"><input type="number" min="0" maxlength="3" style="width:75px;text-align:center;" name="aantal<?=$select['id'];?>" placeholder="Ex: 0" /></td></tr>
					</table>
				</div>
<?php
			++$j;
		}
?>
			</td>
		</tr></tbody>
		<tfoot><tr><td align="center"><input type="submit" name="balls" class="button" value="<?=$txt['button_balls'];?>" style="margin: 6px"/></td></tr></tfoot>
	</table></div>
</form>
<?php
		break;
	case "potions":
		$sql = "SELECT `id`,`naam`,`silver`,`gold`,`omschrijving_" . $_COOKIE['pa_language'] . "` FROM `markt` WHERE `soort`='potions' AND `beschikbaar`='1' ORDER BY silver, gold";
		$result = query_cache($_GET['page'] . '_' . $_GET['shopitem'], $sql, $att_time);

		#Als er op de knop gedrukt word
		if (isset($_POST['potions'])) {
			$gebruiker_silver = $gebruiker['silver'];
			$rekening_gold = $rekening['gold'];

			#itemruimte over berekenen
			$ruimteover = $ruimte['max'] - $gebruiker['items'];

			#Laden voor de verwerking van de informatie
			for($i=1;$i<=$_POST['teller'];++$i) {
				#Item id opvragen
				$itemid = (int)$_POST['id' . $i];

				#Aantal opvragen van het itemid
				$aantal = (int)$_POST['aantal' . $itemid];
				if ($aantal > 0) {
					#Als er geen aantal is
					if (!is_int($aantal))	$niksingevoerd = true;
					else {	#Als er wel een aantal is
						$niksingevoerd = false;

						#Gegevens laden van de te kopen item
						$itemgegevens = DB::exQuery("SELECT `naam`,`silver`,`gold` FROM `markt` WHERE `id`='".$itemid."' AND `beschikbaar`='1' LIMIT 1")->fetch_assoc();

						#Prijs bereken voor het aantal potions.
							if ($itemgegevens['gold'] != 0)	$goldd = $aantal * ($itemgegevens['gold'] / 1);
							else	$silverr = $aantal * ($itemgegevens['silver'] / 1);
						

						#Kijken als er wel genoeg silver is.
						if ($gebruiker_silver < $silverr || $rekening_gold < $goldd) {
							echo '<div class="red">'.$txt['alert_not_enough_money'].'</div>';
							break;
						} else if ($aantal < 0) {
							echo'<div class="red">'.$txt['alert_not_enough_money'].'</div>';
							break;
						/*} else if (!ctype_digit($aantal)) {
							echo '<div class="red">'.$txt['alert_not_enough_money'].'</div>';
							break;*/
						} else if ($ruimteover < $aantal) {	#Kijken als speler nog wel ruimte heeft voor de potions
							if ($aantal > 1)	$netheid = "&#39;s";
							echo '<div class="red">'.$txt['alert_itembox_full_1'].' '.$ruimteover.' '.$itemgegevens['naam'].$netheid.' '.$txt['alert_itembox_full_2'].'</div>';
							break;
						} else {
							#Opslaan
							$totalesilver += $silverr;
							$gebruiker_silver -= $silverr;
							$totalegold += $goldd;
							$rekening_gold -= $goldd;
							$ruimteover -= $aantal;
							DB::exQuery("UPDATE `gebruikers_item` SET `".$itemgegevens['naam']."`=`".$itemgegevens['naam']."`+'".$aantal."' WHERE `user_id`='".$_SESSION['id']."' LIMIT 1");
							echo '<div class="green">'.$txt['success_market'].' '.$itemgegevens['naam'].' '.$aantal.'x.</div>';
						}
						$welingevoerd = true;
					}
				}
			}
			#silver opslaan
			DB::exQuery("UPDATE `gebruikers` SET `silver`=`silver`-'".$totalesilver."' WHERE `user_id`='".$_SESSION['id']."' LIMIT 1");
			DB::exQuery("UPDATE `rekeningen` SET `gold`=`gold`-'".$totalegold."' WHERE `acc_id`='".$_SESSION['acc_id']."' LIMIT 1");

			#Als wel ingevoerd een waarde heeft/true is
			if (!$welingevoerd) {
				if ($niksingevoerd)	echo '<div class="red">'.$txt['alert_nothing_selected'].'</div>';
			}
		}
?>
<form method="POST" name="potions">
	<div class="box-content" style="position: relative"><table class="general" width="100%">
		<thead><tr><th><?=$title;?></th></tr></thead>
		<tbody><tr>
			<td align="center">
<?php
		$j = 1;
		foreach($result as $id=>$select) {

				if ($select['gold'] != 0)	$icon = 'gold';
				else	$icon = 'silver';

			$prijs = highamount($select[$icon]);
?>
				<div class="greyborder">
					<table style="width:120px">
						<tr><td align="center">
							<input type="hidden" name="teller" value="<?=$j;?>" />
							<input type="hidden" name="id<?=$j;?>" value="<?=$select['id'];?>">
							<img src="<?=$static_url;?>/images/items/<?=$select['naam'];?>.png" class="icon-img"/>
						</td></tr>
						<tr><td align="center"><span class="smalltext"><?=$select['naam'];?> <span style="cursor:pointer;" title="<?=$select['omschrijving_'.$_COOKIE['pa_language']];?>"><b>[?]</b></span></span></td></tr>
						<tr><td align="center"><span class="smalltext"><img src="<?=$static_url;?>/images/icons/<?=$icon;?>.png" style="margin-bottom:-3px;" /> <?=$prijs;?></span></td></tr>
						<tr><td align="center"><input type="number" min="0" maxlength="2" style="width:75px;text-align:center;" name="aantal<?=$select['id'];?>" placeholder="Ex: 0" /></td></tr>
					</table>
				</div>
<?php
			++$j;
		}
?>
			</td>
		</tr></tbody>
		<tfoot><tr><td align="center"><input type="submit" name="potions" class="button" value="<?=$txt['button_potions'];?>" style="margin: 6px"/></td></tr></tfoot>
	</table></div>
</form>   
<?php
		break;
	case "items":	#Als er op items geklikt word. Het volgende laten zien 
		$sql = "SELECT `id`,`naam`,`silver`,`gold`,`omschrijving_" . $_COOKIE['pa_language'] . "` FROM `markt` WHERE `soort`='items' AND `beschikbaar`='1' AND `naam`!='Bag' AND `naam`!='Badge case' order by silver, gold";
		$result = query_cache($_GET['page'] . '_' . $_GET['shopitem'], $sql, $att_time);
		#Als er op de knop gedrukt word
		$bag = array('Bag', 'Yellow box', 'Blue box', 'Red box', 'Purple box', 'Black box');
		$b = array_search($gebruiker['itembox'], $bag) + 1;
		if ($b < 6) $bag_allowed = $bag[$b];

		if (isset($_POST['items'])) {
			#Gegevens laden van het item
			$itemgegevens = DB::exQuery("SELECT `naam`,`silver`,`gold` FROM `markt` WHERE `naam`='".$_POST['productnaam']."' AND `beschikbaar`='1' LIMIT 1")->fetch_assoc();

			#Als er niks aangvinkt is.
			if (empty($_POST['productnaam']))	$niksingevoerd = true;
			else if ($gebruiker['Pokedex'] == 0 && $itemgegevens['naam'] == 'Pokedex chip') {	#heeft speler nog geen pokedex maar wil het wel de chip kopen?
				$welingevoerd = false;
				echo '<div class="blue">'.$txt['alert_pokedex_chip'].'</div>';
			} else if ($gebruiker['silver'] < $itemgegevens['silver'] || $rekening['gold'] < $itemgegevens['gold']) {	#Heeft speler niet genoeg silver?
				$welingevoerd = false;
				echo '<div class="red">'.$txt['alert_not_enough_money'].'</div>';
			} else if ($gebruiker[$itemgegevens['naam']] >= 1) {
				$welingevoerd = false;
				echo '<div class="red">Você já comprou este ITEM CHAVE!</div>';
			} else {	#Alles is goed
				$welingevoerd = true;
				$type = explode(" ", $itemgegevens['naam']);
				#Kijken als het te kopen type een box is
				if ($type[1] == "box") { 
					if ($itemgegevens['naam'] == $bag_allowed) {
						DB::exQuery("UPDATE `gebruikers_item` SET `itembox`='".$itemgegevens['naam']."' WHERE `user_id`='".$_SESSION['id']."' LIMIT 1");
						echo '<script>window.location = window.location.href</script>';
					} else {
						$welingevoerd = false;
						echo '<div class="red">Você não pode comprar outra mochila a não ser a '.$bag_allowed.'!</div>';
					}
				}
				else {	#Het is geen box
					#Is er geen ruimte voor het te kopen item?
					if ($ruimte['max'] <= $gebruiker['items']) {
						$welingevoerd = false;
						$itemboxvol = true;
					} else	DB::exQuery("UPDATE `gebruikers_item` SET `".$itemgegevens['naam']."`='1' WHERE `user_id`='".$_SESSION['id']."' LIMIT 1");
				}
				if (!$itemboxvol) {	#Als itembox niet vol is
					#Opslaan
					DB::exQuery("UPDATE `gebruikers` SET `silver`=`silver`-'".$itemgegevens['silver']."' WHERE `user_id`='".$_SESSION['id']."' LIMIT 1");
					DB::exQuery("UPDATE `rekeningen` SET `gold`=`gold`-'".$itemgegevens['gold']."' WHERE `acc_id`='".$_SESSION['acc_id']."' LIMIT 1");
					echo '<div class="green">'.$txt['success_market'].' '.$itemgegevens['naam'].'.</div>';
				}
			}
			#Als wel ingevoerd een waarde heeft/true is
			if (!$welingevoerd) {
				if ($niksingevoerd)	echo '<div class="red">'.$txt['alert_nothing_selected'].'</div>';
				else if ($itemboxvol)	echo '<div class="red">'.$txt['alert_not_enough_place'].'</div>';
			}
		}

?>
<form method="POST" name="items">
	<div class="box-content" style="position: relative"><table class="general" width="100%">
		<thead><tr><th><?=$title;?></th></tr></thead>
		<tbody><tr>
			<td align="center">
<?php
		$j = 1;
		foreach($result as $id=>$select) {

				if ($select['gold'] != 0)	$icon = 'gold';
				else	$icon = 'silver';

			$prijs = highamount($select[$icon]);

			$type = explode(" ", $select['naam']);
			if ($type[1] == "box") { 
				if ($select['naam'] != $bag_allowed) {
					continue;
				}
			}


			if ($gebruiker[$select['naam']] >= 1 || ($itemgegevens['naam'] == $select['naam'] && $welingevoerd)) {
				continue;
			}
?>
				<div class="greyborder">
					<table style="width:120px">
						<tr><td align="center" height="24px"><img src="<?=$static_url;?>/images/items/<?=$select['naam'];?>.png" width="24" class="icon-img"/></td></tr>
						<tr><td align="center"><span class="smalltext"><?=$select['naam'];?> <span style="cursor:pointer;" title="<?=$select['omschrijving_'.$_COOKIE['pa_language']];?>"><b>[?]</b></span></span></td></tr>
						<tr><td align="center"><span class="smalltext"><img src="<?=$static_url;?>/images/icons/<?=$icon;?>.png" style="margin-bottom:-3px;" /> <?=$prijs;?></span></td>
						<tr><td align="center"><input type="radio" name="productnaam" value="<?=$select['naam'];?>"/></td></tr>
					</table>
				</div>
<?php
			++$j;
		}
if ($j == 1) echo '<div class="red">Você já comprou todos os ITENS CHAVE!</div>';
?>
			</td>
		</tr></tbody>
		<?php if ($j != 1) { ?><tfoot><tr><td align="center"><input type="submit" name="items" value="<?=$txt['button_items'];?>" class="button" style="margin: 6px"/></td></tr></tfoot><?php } ?>
	</table></div>
</form> 
<?php
		break;
	case "specialitems":
		$sql = "SELECT `id`,`naam`,`silver`,`gold`,`omschrijving_" . $_COOKIE['pa_language'] . "` FROM `markt` WHERE `soort`='special items' AND `beschikbaar`='1' ORDER BY silver,gold";
		$result = query_cache($_GET['page'] . '_' . $_GET['shopitem'], $sql, $att_time);

		#Als er op de knop gedrukt word
		if (isset($_POST['specialitems'])) {
			$gebruiker_silver = $gebruiker['silver'];
			$rekening_gold = $rekening['gold'];

			#itemruimte over berekenen
			$ruimteover = $ruimte['max']-$gebruiker['items'];

			#Laden voor de verwerking van de informatie
			for($i=1;$i<=$_POST['teller'];++$i) {
				#Item id opvragen
				$itemid = (int)$_POST['id'.$i];

				#Aantal opvragen van het itemid
				$aantal = (int)$_POST['aantal'.$itemid];
				if ($aantal > 0) {
					if (!is_int($aantal))  $niksingevoerd = true;
					else {
						#Item gegevens laden
						$itemgegevens = DB::exQuery("SELECT `naam`,`silver`,`gold` FROM `markt` WHERE `id`='".$itemid."' AND `beschikbaar`='1' LIMIT 1")->fetch_assoc();

						#silver of gold berekenen voor de balls
							if ($itemgegevens['gold'] != 0)	$goldd = $aantal * ($itemgegevens['gold'] / 1);
							else	$silverr = $aantal * ($itemgegevens['silver'] / 1);
						

						#Kijken als het silver er wel voor is
						if ($gebruiker_silver < $silverr || $rekening_gold < $goldd) {
							echo '<div class="red">'.$txt['alert_not_enough_money'].'</div>';
							break;
						} else if ($aantal < 0) {
							echo'<div class="red">'.$txt['alert_not_enough_money'].'</div>';
							break;
						/*} else if (!ctype_digit($aantal)) {
							echo '<div class="red">'.$txt['alert_not_enough_money'].'</div>';
							break;*/
						} else if ($ruimteover < $aantal) {	#Kijken als speler nog wel ruimte heeft voor de potions
							if ($aantal > 1)	$netheid = "&#39;s";
							echo '<div class="red">'.$txt['alert_itembox_full_1'].' '.$ruimteover.' '.$itemgegevens['naam'].''.$netheid.' '.$txt['alert_itembox_full_2'].'</div>';
							break;
						} else {
							#Opslaan
							$totalesilver += $silverr;
							$gebruiker_silver -= $silverr;
							$totalegold += $goldd;
							$rekening_gold -= $goldd;
							$ruimteover -= $aantal;
							DB::exQuery("UPDATE `gebruikers_item` SET `".$itemgegevens['naam']."`=`".$itemgegevens['naam']."`+'".$aantal."' WHERE `user_id`='".$_SESSION['id']."'");
							echo '<div class="green">'.$txt['success_market'].' '.$itemgegevens['naam'].' '.$aantal.'x.</div>';
						}
						$welingevoerd = true;
					}
				}
			}
			#silver opslaan
			DB::exQuery("UPDATE `gebruikers` SET `silver`=`silver`-'".$totalesilver."' WHERE `user_id`='".$_SESSION['id']."' LIMIT 1");
			DB::exQuery("UPDATE `rekeningen` SET `gold`=`gold`-'".$totalegold."' WHERE `acc_id`='".$_SESSION['acc_id']."' LIMIT 1");
			#Als wel ingevoerd een waarde heeft/true is
			if (!$welingevoerd) {
				if ($niksingevoerd)	echo '<div class="red">'.$txt['alert_nothing_selected'].'</div>';
			}
		}
?>
<form method="POST" name="specialitems">
	<div class="box-content" style="position: relative"><table class="general" width="100%">
		<thead><tr><th><?=$title;?></th></tr></thead>
		<tbody><tr>
			<td align="center">
<?php
		$j = 1;
		foreach($result as $id=>$select) {
				if ($select['gold'] != 0)	$icon = 'gold';
				else	$icon = 'silver';

			$prijs = highamount($select[$icon]);
?>
				<div class="greyborder">
					<table style="width:120px">
						<tr><td align="center">
							<input type="hidden" name="teller" value="<?=$j;?>" />
							<input type="hidden" name="id<?=$j;?>" value="<?=$select['id'];?>" />
							<img src="<?=$static_url;?>/images/items/<?=$select['naam'];?>.png" class="icon-img"/>
						</td></tr>
						<tr><td align="center"><span class="smalltext"><?=$select['naam'];?> <span style="cursor:pointer;" title="<?=$select['omschrijving_'.$_COOKIE['pa_language']];?>"><b>[?]</b></span></span></td></tr>
						<tr><td align="center"><span class="smalltext"><img src="<?=$static_url;?>/images/icons/<?=$icon;?>.png" style="margin-bottom:-3px;" /> <?=$prijs;?></span></td></tr>
						<tr><td align="center"><input type="number" min="0" maxlength="2" style="width:75px;text-align:center;" name="aantal<?=$select['id'];?>" placeholder="Ex: 0" /></td></tr>
					</table>
				</div>
<?php
			++$j;  
		}
?>
			</td>
		</tr></tbody>
		<tfoot><tr><td align="center"><input type="submit" name="specialitems" value="<?=$txt['button_spc_items'];?>" class="button" style="margin: 6px"/></td></tr> </tfoot>
	</table></div>
</form>
<?php
		break;
	case "stones":
		$sql = "SELECT `id`,`naam`,`silver`,`desconto`,`gold`,`omschrijving_" . $_COOKIE['pa_language'] . "` FROM `markt` WHERE `soort`='stones' AND `beschikbaar`='1' ORDER BY silver,gold";
		$result = query_cache($_GET['page'] . '_' . $_GET['shopitem'], $sql, $att_time);

		#Als er op de knop gedrukt word
		if (isset($_POST['stones'])) {
			$gebruiker_silver = $gebruiker['silver'];
			$rekening_gold = $rekening['gold'];

			#itemruimte over berekenen
			$ruimteover = $ruimte['max']-$gebruiker['items'];

			#Laden voor de verwerking van de informatie
			for($i=1;$i<=$_POST['teller'];++$i) {
				#Item id opvragen
				$itemid = (int)$_POST['id'.$i];

				#Aantal opvragen van het itemid
				$aantal = (int)$_POST['aantal'.$itemid];
				if ($aantal > 0) {
					#Als er geen aantal is
					if (!is_int($aantal))	$niksingevoerd = false;
					else {
						#Item gegevens laden
						$itemgegevens = DB::exQuery("SELECT `naam`,`silver`,`gold` FROM `markt` WHERE `id`='".$itemid."' AND `beschikbaar`='1' LIMIT 1")->fetch_assoc();

						#silver berekenen voor de balls
							if ($itemgegevens['gold'] != 0)	$goldd = $aantal * ($itemgegevens['gold'] / 1);
							else	$silverr = $aantal * ($itemgegevens['silver'] / 1);

						#Kijken als het silver er wel voor is           
						if ($gebruiker_silver < $silverr || $rekening_gold < $goldd) {
							echo '<div class="red">'.$txt['alert_not_enough_money'].'</div>';
							break;
						} else if ($aantal < 0) {
							echo'<div class="red">'.$txt['alert_not_enough_money'].'</div>';
							break;
						/*} else if (!ctype_digit($aantal)) {
							echo '<div class="red">'.$txt['alert_not_enough_money'].'</div>';
							break;*/
						}else if ($ruimteover < $aantal) {  #Kijken als speler nog wel ruimte heeft voor de potions
							if ($aantal > 1)	$netheid = "&#39;s";
							echo '<div class="red">'.$txt['alert_itembox_full_1'].' '.$ruimteover.' '.$itemgegevens['naam'].''.$netheid.' '.$txt['alert_itembox_full_2'].'</div>';
							break;
						} else {
							#Opslaan
							$totalesilver += $silverr;
							$totalegold += $goldd;
							$gebruiker_silver -= $silverr;
							$rekening_gold -= $goldd;
							$ruimteover -= $aantal;
							DB::exQuery("UPDATE `gebruikers_item` SET `".$itemgegevens['naam']."`=`".$itemgegevens['naam']."`+'".$aantal."' WHERE `user_id`='".$_SESSION['id']."'");
							echo '<div class="green">'.$txt['success_market'].' '.$itemgegevens['naam'].' '.$aantal.'x.</div>';
						}
						$welingevoerd = true;
					}
				}
			}
			#silver opslaan
			DB::exQuery("UPDATE `gebruikers` SET `silver`=`silver`-'".$totalesilver."' WHERE `user_id`='".$_SESSION['id']."' LIMIT 1");
			DB::exQuery("UPDATE `rekeningen` SET `gold`=`gold`-'".$totalegold."' WHERE `acc_id`='".$_SESSION['acc_id']."' LIMIT 1");
			#Als wel ingevoerd een waarde heeft/true is
			if (!$welingevoerd) {
				#Als niksingevoerd TRUE is
				if ($niksingevoerd)  echo '<div class="red">'.$txt['alert_nothing_selected'].'</div>';
			}
		}
?>
<form method="POST" name="stones">
	<div class="box-content" style="position: relative"><table class="general" width="100%">
		<thead><tr><th><?=$title;?></th></tr></thead>
		<tbody><tr>
			<td align="center">
<?php
		$j = 1;
		foreach($result as $id=>$select) {

				if ($select['gold'] != 0)	$icon = 'gold';
				else	$icon = 'silver';

			$prijs = highamount($select[$icon]);
?>
				<div class="greyborder">
					<table style="width:120px">
						<tr><td align="center">
							<input type="hidden" name="teller" value="<?=$j;?>" />
							<input type="hidden" name="id<?=$j;?>" value="<?=$select['id'];?>" />
							<img src="<?=$static_url;?>/images/items/<?=$select['naam'];?>.png" class="icon-img"/>
						</td></tr>
						<tr><td align="center"><span class="smalltext"><?=$select['naam'];?> <span style="cursor:pointer;" title="<?=$select['omschrijving_'.$_COOKIE['pa_language']];?>"><b>[?]</b></span></span></td></tr>
						<tr><td align="center"><span class="smalltext"><img src="<?=$static_url;?>/images/icons/<?=$icon;?>.png" style="margin-bottom:-3px;"> <?=$prijs;?></span> <br><font color='red'><?=$select['desconto'];?></font></td></tr>
						<tr><td align="center"><input type="number" min="0" maxlength="3" style="width:75px;text-align:center;" name="aantal<?=$select['id'];?>" placeholder="Ex: 0" /></td></tr>
					</table>
				</div>
<?php
			++$j;  
		}
?>
			</td>
		</tr></tbody>
		<tfoot><tr><td align="center"><input type="submit" name="stones" value="<?=$txt['button_stones'];?>" class="button" style="margin: 6px"/></td></tr></tfoot>
	</table></div>
</form>
<?php
	break;
	#Als er op potions geklikt word, het volgende laten zien
	case "attacks":
		if (!is_numeric($_GET['subpage']))	$subpage = 1; 
		else	$subpage = $_GET['subpage']; 

		#Max aantal pokemon per pagina
		$max = 20; 
		$aantal_attacks = 100;
		$aantal_paginas = ceil($aantal_attacks/$max); 
		if ($aantal_paginas == 0) $aantal_paginas = 1;   
		$pagina = $subpage*$max-$max; 
		$sql = "SELECT markt.id, markt.naam, markt.beschikbaar, silver, gold, omschrijving_".$_COOKIE['pa_language'].", tmhm.type1 , tmhm.type2
								FROM `markt`
								INNER JOIN tmhm
								ON markt.naam = tmhm.naam
								WHERE `soort`='tm' AND `beschikbaar` = '1' order by id
								LIMIT ".$pagina.", ".$max."";
		$result = query_cache($_GET['page'] . '_' . $_GET['shopitem'] . '_' . $subpage, $sql, $att_time);

		#Als er op de knop gedrukt word
		if (isset($_POST['tm'])) {
			$gebruiker_silver = $gebruiker['silver'];
			$rekening_gold = $rekening['gold'];

			#itemruimte over berekenen
			$ruimteover = $ruimte['max']-$gebruiker['items'];

			#Laden voor de verwerking van de informatie
			for($i=1;$i<=$_POST['teller'];++$i) {
				#Item id opvragen
				$itemid = (int)$_POST['id'.$i];

				#Aantal opvragen van het itemid
				$aantal = (int)$_POST['aantal'.$itemid];
				if ($aantal) {
					#Als er geen aantal is
					if (!is_int($aantal))  $niksingevoerd = true;
					else {
						$niksingevoerd = false;

						#Gegevens laden van de te kopen item
						$itemgegevens = DB::exQuery("SELECT `naam`,`silver`,`gold` FROM `markt` WHERE `id`='".$itemid."' AND `beschikbaar`='1' LIMIT 1")->fetch_assoc();

						#Prijs bereken voor het aantal potions.
						if ($itemgegevens['gold'] != 0)	$goldd = $aantal * ($itemgegevens['gold'] / 1);
						else	$silverr = $aantal * ($itemgegevens['silver'] / 1);

						
						#Kijken als er wel genoeg silver is.
						if ($gebruiker_silver < $silverr || $rekening_gold < $goldd) {
							echo '<div class="red">'.$txt['alert_not_enough_money'].'</div>';
							break;
						} else if ($aantal < 0) {
							echo '<div class="red">'.$txt['alert_not_enough_money'].'</div>';
							break;
						/*} else if (!ctype_digit($aantal)) {
							echo '<div class="red">'.$txt['alert_not_enough_money'].'</div>';
							break;*/
						} else if ($ruimteover < $aantal) {  #Kijken als speler nog wel ruimte heeft voor de potions
							if ($aantal > 1) $netheid = "&#39;s";
							echo '<div class="red">'.$txt['alert_itembox_full_1'].' '.$ruimteover.' '.$itemgegevens['naam'].''.$netheid.' '.$txt['alert_itembox_full_2'].'</div>';
							break;
						} else {
							#Opslaan
							$totalesilver += $silverr;
							$totalegold += $goldd;
							$gebruiker_silver -= $silverr;
							$rekening_gold -= $goldd;
							$ruimteover -= $aantal;
							DB::exQuery("UPDATE `gebruikers_tmhm` SET `".$itemgegevens['naam']."`=`".$itemgegevens['naam']."`+'".$aantal."' WHERE `user_id`='".$_SESSION['id']."' LIMIT 1");
							echo '<div class="green">'.$txt['success_market'].' '.$itemgegevens['naam'].' '.$aantal.'x.</div>';
						}
						$welingevoerd = true;
					}
				}
			}
			#silver opslaan
			DB::exQuery("UPDATE `gebruikers` SET `silver`=`silver`-'".$totalesilver."' WHERE `user_id`='".$_SESSION['id']."' LIMIT 1");
			DB::exQuery("UPDATE `rekeningen` SET `gold`=`gold`-'".$totalegold."' WHERE `acc_id`='".$_SESSION['acc_id']."' LIMIT 1");
			#Als wel ingevoerd een waarde heeft/true is
			if (!$welingevoerd) {
				if ($niksingevoerd)	echo '<div class="red">'.$txt['alert_nothing_selected'].'</div>';
			}
		}
?>
<form method="POST" name="tm">
	<div class="box-content" style="position: relative"><table class="general" width="100%">
		<thead><tr><th colspan="2"><?=$title;?></th></tr></thead>
		<tbody><tr>
			<td align="center" colspan="2">
<?php
		$j = 1;
		foreach($result as $id=>$select) {

				if ($select['gold'] != 0)	$icon = 'gold';
				else	$icon = 'silver';

			$prijs = highamount($select[$icon]);
			
		$pegadadox = DB::exQuery("select omschrijving from tmhm where naam='".$select['naam']."'")->fetch_assoc();
		$pegadado = DB::exQuery("select soort from aanval where naam='".$pegadadox['omschrijving']."'")->fetch_assoc();
 		
 		
		$type = $pegadado['soort'];
			
?>
				<div class="greyborder">
					<table style="width:120px">
						<tr><td align="center">
							<input type="hidden" name="teller" value="<?=$j;?>" />
							<input type="hidden" name="id<?=$j;?>" value="<?=$select['id'];?>" />
							<img src="<?=$static_url;?>/images/items/Attack_<?=$type;?>.png" class="icon-img"/>
						</td></tr>
						<tr><td align="center"><span class="smalltext"><?=$select['naam'];?> <span style="cursor:pointer;" title="<?=$select['omschrijving_'.$_COOKIE['pa_language']];?>"><b>[?]</b></span></span></td></tr>
						<tr><td align="center"><span class="smalltext"><img src="<?=$static_url;?>/images/icons/<?=$icon;?>.png" style="margin-bottom:-3px;"> <?=$prijs;?></span></td></tr>
						<tr><td align="center"><input type="number" min="0" maxlength="3" style="width:75px;text-align:center;" name="aantal<?=$select['id'];?>" placeholder="Ex: 0" /></td></tr>
					</table>
				</div>
<?php
			++$j;  
		}
?>
			</td>
		</tr></tbody>
		<tfoot><tr>
<?php
		if ($aantal_paginas > 1) {
			$links = false;
			$rechts = false;
			echo '<td align="center" style="width: 64%;"><div class="sabrosus" style="float: right">';
			if ($subpage == 1)	echo '<span class="disabled">&laquo;</span>';
			else {
				$back = $subpage-1;
				echo '<a href="'.$_GET['page'].'&shopitem='.$_GET['shopitem'].'&subpage='.$back.'">&laquo;</a>';
			}
			for($i=1;$i<=$aantal_paginas;++$i) {
				if (3 >= $i && $subpage == $i)	echo '<span class="current">'.$i.'</span>';
				else if (3 >= $i && $subpage != $i)	echo '<a href="'.$_GET['page'].'&shopitem='.$_GET['shopitem'].'&subpage='.$i.'">'.$i.'</a>';
				else if ($aantal_paginas-2 < $i && $subpage == $i)	echo '<span class="current">'.$i.'</span>';
				else if ($aantal_paginas-2 < $i && $subpage != $i)	echo '<a href="'.$_GET['page'].'&shopitem='.$_GET['shopitem'].'&subpage='.$i.'">'.$i.'</a>';
				else {
					$max = $subpage + 3;
					$min = $subpage -3;  
					if ($page == $i)	echo '<span class="current">'.$i.'</span>';
					else if ($min < $i && $max > $i)	echo '<a href="'.$_GET['page'].'&shopitem='.$_GET['shopitem'].'&subpage='.$i.'">'.$i.'</a>';
					else {
						if ($i < $subpage) {
							if (!$links) {
								echo '...';
								$links = true;
							}
						} else {
							if (!$rechts) {
								echo '...';
								$rechts = true;
							}
						}
					}
				}
			} 
			if ($aantal_paginas == $subpage) echo '<span class="disabled">&raquo;</span>';
			else {
				$next = $subpage+1;
				echo '<a href="'.$_GET['page'].'&shopitem='.$_GET['shopitem'].'&subpage='.$next.'">&raquo;</a>';
			}
			echo '</div></td>';
		}
?>
			<td align="center"><input type="submit" name="tm" value="<?=$txt['button_attacks'];?>" class="button" style="margin: 6px"/></td>
		</tr></tfoot>
	</table></div>
</form>
<?php
		break;
	case "pokemon":
	   // header('location: ./market');
		$sql  = "SELECT `markt`.`id`,`markt`.`pokemonid`,`markt`.`silver`,`markt`.`gold`,`markt`.`omschrijving_".$_COOKIE['pa_language']."`,`pokemon_wild`.`zeldzaamheid` FROM `markt` INNER JOIN `pokemon_wild` ON `markt`.`pokemonid`=`pokemon_wild`.`wild_id` WHERE `markt`.`soort`='pokemon' AND `markt`.`beschikbaar`='1' AND `pokemon_wild`.`wereld`='" . $gebruiker['wereld'] . "'";
		$result = query_cache($_GET['page'] . '_' . $_GET['shopitem'], $sql, $att_time);

		#Als er op de knop gedrukt word
		if (isset($_POST['pokemon'])) {
			#Gegevens laden van het item
			$itemgegevens = DB::exQuery("SELECT `id`,`pokemonid`,`silver`,`gold`,`beschikbaar` FROM `markt` WHERE `id`='".$_POST['productid']."' LIMIT 1")->fetch_assoc();

			if (empty($_POST['productid']))	echo '<div class="red">'.$txt['alert_nothing_selected'].'</div>';
			else if ($itemgegevens['beschikbaar'] != 1)	echo '<div class="red">'.$txt['alert_not_in_stock'].'</div>';
			else if ($gebruiker['silver'] < $itemgegevens['silver'] || $rekening['gold'] < $itemgegevens['gold'])	echo '<div class="red">'.$txt['alert_not_enough_money'].'</div>';
			else if ($gebruiker['in_hand'] > 5)	echo '<div class="red">'.$txt['alert_hand_full'].'</div>';
			else {	#Alles is goed
				#tijd van nu fixen
				$tijd = date('Y-m-d H:i:s');
				$opzak_nummer = $gebruiker['in_hand'] + 1;

				#Willekeurige pokemon laden, en daarvan de gegevens
				$query = DB::exQuery("SELECT `wild_id`,`naam`,`groei`,`attack_base`,`defence_base`,`speed_base`,`spc.attack_base`,`spc.defence_base`,`hp_base`,`aanval_1`,`aanval_2`,`aanval_3`,`aanval_4`,`ability` FROM `pokemon_wild` WHERE `wild_id`='" . $itemgegevens['pokemonid'] . "' LIMIT 1")->fetch_assoc();
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

				#Pokemon IV maken en opslaan
				#Iv willekeurig getal tussen 1,31. Ik neem 2 omdat 1 te weinig is:P
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
				DB::exQuery("UPDATE `pokemon_speler` SET `level`='5',`karakter`='".$karakter['karakter_naam']."',`expnodig`='".$experience['punten']."',`user_id`='".$_SESSION['id']."',`opzak`='ja',`opzak_nummer`='".$opzak_nummer."',`ei`='1',`ei_tijd`='".$tijd."',`attack_iv`='".$attack_iv."',`defence_iv`='".$defence_iv."',`speed_iv`='".$speed_iv."',`spc.attack_iv`='".$spcattack_iv."',`spc.defence_iv`='".$spcdefence_iv."',`hp_iv`='".$hp_iv."',`attack`='".$attackstat."',`defence`='".$defencestat."',`speed`='".$speedstat."',`spc.attack`='".$spcattackstat."',`spc.defence`='".$spcdefencestat."',`levenmax`='".$hpstat."',`leven`='".$hpstat."',`ability`='".$ability."',`capture_date`='".$date."' WHERE `id`='".$pokeid."' LIMIT 1");

				##################EINDE POKEMON GEVEN
				DB::exQuery("UPDATE `markt` SET `beschikbaar`='0' WHERE `id`='" . $itemgegevens['id'] . "' LIMIT 1");
				DB::exQuery("UPDATE `gebruikers` SET `silver`=`silver`-'".$itemgegevens['silver']."',`aantalpokemon`=`aantalpokemon`+'1' WHERE `user_id`='".$_SESSION['id']."' LIMIT 1");
				DB::exQuery("UPDATE `rekeningen` SET `gold`=`gold`-'".$itemgegevens['gold']."' WHERE `acc_id`='".$_SESSION['acc_id']."'");
				echo '<div class="green">'.$txt['success_market'].' '.$txt['success_bought_pokemon'].'</div>';      
			}
		}
?>
<form method="post">
	<div class="box-content" style="position: relative"><table class="general" width="100%">
		<thead><tr><th><?=$title;?></th></tr></thead>
		<tbody>
<?php
		if (@count($result) <= 0) echo '<tr><td><div class="red">'.$txt['out_of_stock_1'].' '.$gebruiker['wereld'].' '.$txt['out_of_stock_2'].'</div></td></tr></tbody></div>';
		else {
?>
			<tr><td align="center">
<?php
			$j = 1;
			foreach($result as $id=>$select) {
					if ($select['gold'] != 0)	$icon = 'gold';
					else	$icon = 'silver';
				$prijs = highamount($select[$icon]);

                if ($select['zeldzaamheid'] == 1) {
                    $name = 'Comum';
                } else if ($select['zeldzaamheid'] == 2) {
                    $name = 'Incomum';
                } else if ($select['zeldzaamheid'] == 3) {
                    $name = 'Raro';
                } else {
                    $name = 'Lendário';
                }
?>
				<div class="greyborder">
					<table style="width:120px">
						<tr><td align="center"><img src="<?=$static_url;?>/images/icons/egg.gif" class="icon-img"/></td></tr>
						<tr><td align="center"><span class="smalltext"><?=$name;?> <span style="cursor:pointer;" title="<?=$select['omschrijving_'.$_COOKIE['pa_language']];?>"><b>[?]</b></span></span></td></tr>
						<tr><td align="center"><span class="smalltext"><img src="<?=$static_url;?>/images/icons/<?=$icon;?>.png" style="margin-bottom:-3px;" /> <?=$prijs;?></span></td></tr>
						<tr><td align="center"><input type="radio" name="productid" value="<?=$select['id'];?>" /></td></tr>
					</table>
				</div>
<?php
				++$j;
			}
?>
			</td></tr>
		</tbody>
		<tfoot><tr><td align="center"><input type="submit" name="pokemon" value="<?=$txt['button_pokemon'];?>" class="button" style="margin: 6px"/></td></tr></tfoot>
<?php
		}
?>
	</table></div>
</form>
<?php
		break;
	default:	exit(header("LOCATION: ./market&shopitem=balls"));  break;
}
?>


<script>
	$('#itens').wlOrientation('<?=$_GET['shopitem']?>');
</script>