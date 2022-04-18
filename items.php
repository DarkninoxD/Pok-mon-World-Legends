<?php 
#Script laden zodat je nooit pagina buiten de index om kan laden
require_once("app/includes/resources/security.php");

if (!isset($_GET['category'])) exit(header("LOCATION: ./items&category=balls"));
echo addNPCBox(10, 'Mochila', 'Bom, aqui é a sua <b>Mochila</b>... Nela você poderá guardar vários itens e outros objetos. <br>Caso ela fique cheia você terá que comprar uma maior ou vender alguns de seus itens...<br>Lembre-se sempre de checar os espaços disponíveis em sua Mochila!');
?>
<div class="blue"><div align="center">
	<img src="<?=$static_url;?>/images/items/<?=$gebruiker['itembox'];?>.png" style="vertical-align: middle; width:24px" /> <?=$txt['title_text_1'];?> 
	<b><?=$gebruiker['item_over'];?></b> 
	<?=$txt['title_text_2'];?> 
</div></div>
<?php
	if ($gebruiker['item_over'] <= 0 && $gebruiker['itembox'] != 'Black box') {
		echo '<div class="red"><a href="./market&shopitem=items">Compre uma mochila maior AQUI.</a></div>';
	}
?>
<script type="text/javascript">
	function use_item(soort, naam, equip = false) {
		if (soort == 'stones')					var wat = 'use_stone';
		else if (soort == 'potions')				var wat = 'use_potion';
		else if (soort == 'tm' || soort == 'hm')	var wat = 'use_attack';
		else {
			if (naam == 'Rare candy') {
				var wat = 'use_rarecandy';
			} else { 
				if (equip) {
					var wat = 'use_equip_item';
				} else {
					var wat = 'use_spcitem';
				}
			}
		}
		$.colorbox({
			href: './ajax.php?act=' + wat + '&name=' + naam,
			iframe: true,
			width: '700px',
			height: '<?=(160 + (39 * $gebruiker['in_hand']));?>px'
		});
		return false;
	}
</script>
<?php
$itemData = DB::exQuery("SELECT `gebruikers_item`.*,`gebruikers_tmhm`.* FROM `gebruikers_item` INNER JOIN `gebruikers_tmhm` ON `gebruikers_item`.`user_id`=`gebruikers_tmhm`.`user_id` WHERE `gebruikers_item`.`user_id`='" . $_SESSION['id'] . "'")->fetch_assoc();
if (isset($_POST['verkoop'])) {
	$select = DB::exQuery("SELECT `naam`,`soort`,`silver`,`gold` FROM `markt` WHERE `naam`='" . $_POST['name'] . "' LIMIT 1")->fetch_assoc();
	$_POST['amount'] = (int)round($_POST['amount']);	
	
	
	if ($select['soort'] != "items") {
	if (empty($_POST['amount']))  $error = '<div class="red">A compra não pode ser vazia!</div>';
	else if (!is_numeric($_POST['amount']))  $error = '<div class="red">Houve um erro!</div>';
	else if ($_POST['amount'] <= 0)  $error = '<div class="red">Você não possui esta quantidade!</div>';
	else if ($_POST['amount'] > $itemData[$select['naam']])  $error = '<div class="red">Você não possui esta quantidade!</div>';
	else {
		//if (!empty($event_type) && $select['tickets'] != 0) {
			//$currency = 'tickets';
		//	$price = floor($_POST['amount'] * ($select[$currency] * 0.5));
		//} else {
			if ($select['gold'] != 0) {
				$currency = 'gold';
				$price = floor($_POST['amount'] * ($select[$currency] * 0.5));
				if ($select['soort'] == 'stones') $price = 0;
			} else {
				$currency = 'silver';
				$price = floor($_POST['amount'] * ($select[$currency] * 0.5));
				if ($select['soort'] == 'stones') $price = 0;
			}
		//}
		$show = '<img src="' . $static_url . '/images/icons/' . $currency . '.png" /> ' . highamount($price);
		if ($select['soort'] == "tm" || $select['soort'] == "hm")	DB::exQuery("UPDATE `gebruikers_tmhm` SET `" . $_POST['name'] . "`=`" . $_POST['name'] . "`-'" . $_POST['amount'] . "' WHERE `user_id`='" . $_SESSION['id'] . "' LIMIT 1");
		else	DB::exQuery("UPDATE `gebruikers_item` SET `" . $_POST['name'] . "`=`" . $_POST['name'] . "`-'" . $_POST['amount'] . "' WHERE `user_id`='" . $_SESSION['id'] . "' LIMIT 1");

		//if (!empty($event_type) && $select['tickets'] != 0)
			//DB::exQuery("UPDATE `rekeningen` SET `tickets`=`tickets`+'" . $price . "' WHERE `acc_id`='" . $_SESSION['acc_id']."' LIMIT 1");
		//else {
			if ($select['gold'] != 0)	DB::exQuery("UPDATE `rekeningen` SET `gold`=`gold`+'" . $price . "' WHERE `acc_id`='" . $_SESSION['acc_id']."' LIMIT 1");
			else	DB::exQuery("UPDATE `gebruikers` SET `silver`=`silver`+'" . $price . "' WHERE `user_id`='" . $_SESSION['id']."' LIMIT 1");
		//}

?>
	<script>
		window.location = window.location.href;
	</script>
<?php
	}
}

echo $error;
}

?>
<div class="orientation-bar" id="itens" style="margin-top: 7px; margin-bottom: -1px">
	<a href="./items&category=balls" data-orientation="balls" class="noanimate"><button type="button">Poké Balls</button></a>
	<a href="./items&category=items" data-orientation="items" class="noanimate"><button type="button">Itens Chave</button></a>
	<a href="./items&category=spc_items" data-orientation="special items" class="noanimate"><button type="button">Itens Especiais</button></a>
	<a href="./items&category=potions" data-orientation="potions" class="noanimate"><button type="button">Poções</button></a>
	<a href="./items&category=stones" data-orientation="stones" class="noanimate"><button type="button">Pedras</button></a>
	<a href="./items&category=hm" data-orientation="hm" class="noanimate"><button type="button">HM's</button></a>
	<a href="./items&category=tm" data-orientation="tm" class="noanimate"><button type="button">TM's</button></a>
</div>
<?php

$_GET['category'] = $_GET['category'] == 'spc_items' ? 'special items' : $_GET['category'];

$arrayItems = array();
$getItems = DB::exQuery("SELECT * FROM `markt` WHERE `soort`='" . $_GET['category'] . "' ORDER BY `soort` ASC, `id` ASC");
while($item = $getItems->fetch_assoc()) {
	if ($itemData[$item['naam']] > 0)
		$arrayItems[$item['soort']][] = $item;
}
if (count($arrayItems) > 0) {
	foreach($arrayItems as $key=>$value) {
		if (count($value) > 0) {
			if ($key == 'balls') {
				echo '<div class="box-content" style="position: relative"><table class="general" id="example" style="text-align: center">
				<thead>
					<tr><th colspan="5">'.$txt[$key].'</th></tr>
					<tr>
						<td width="120"><strong>'.$txt['name'].'</strong></td>
						<td width="75"><strong>'.$txt['number'].'</strong></td>
						<td width="115"><strong>'.$txt['sellprice'].' UNIT.</strong></td>
						<td width="112" class="no-sort"><strong>'.$txt['sell'].'</strong></td>
					</tr>
				</thead>
				<tbody>';
			} else if ($key == 'items') {
				echo '<div class="box-content" style="position: relative"><table class="general" id="example" style="text-align: center">
				<thead>
					<tr><th colspan="5">'.$txt[$key].'</th></tr>
					<tr>
						<td width="120"><strong>'.$txt['name'].'</strong></td>
						<td width="75"><strong>'.$txt['number'].'</strong></td>
						<td width="115" class="no-sort"><strong>'.$txt['sellprice'].' UNIT.</strong></td>
						<td width="70" class="no-sort"><strong>'.$txt['sell'].'</strong></td>
					</tr>
				</thead>
				<tbody>';
			} else if (in_array($key, array('stones','special items','potions','tm','hm'))) {
				echo '<div class="box-content" style="position: relative"><table class="general" id="example" style="text-align: center">
				<thead>
					<tr><th colspan="6">'.$txt[str_replace('special items', 'spc_items', $key)].'</th></tr>
					<tr>
						<td width="120"><strong>'.$txt['name'].'</strong></td>
						<td width="75"><strong>'.$txt['number'].'</strong></td>
						<td width="115"><strong>'.$txt['sellprice'].' UNIT.</strong></td>
						<td width="140" class="no-sort"><strong>'.$txt['sell'].'</strong></td>
						<td width="70" class="no-sort"><strong>'.$txt['use'].'</strong></td>
					</tr>
				</thead>
				<tbody>';
			}
			foreach($value as $key2=>$value2) {
				if (!empty($event_type) && $value2['tickets'] != 0) {
					$munt = 'tickets';
					$price = highamount(floor($value2[$munt] * 0.5));
				} else {
					if ($value2['gold'] != 0) {
						$munt = 'gold';
						$price = highamount(floor($value2[$munt] * 0.5));
						if ($_GET['category'] == 'stones') $price = 0;
					} else {
						$munt = 'silver';
						$price = highamount(floor($value2[$munt] * 0.5));
						if ($_GET['category'] == 'stones') $price = 0;
					}
				}
		
				if ($key == 'balls') {
					echo '<tr><form method="post">
						<td style="text-align: left; padding-left: 27px;"><img src="' . $static_url . '/images/items/'.$value2['naam'].'.png" title="'.nl2br($value2['omschrijving_' . $_COOKIE['pa_language']]).'" class="elipse"/><b>'.$value2['naam'].'</b></td>
						<td align="center"><b>'.$itemData[$value2['naam']].'x</b></td>
						<td><img src="' . $static_url . '/images/icons/'.$munt.'.png" style="margin-bottom:-3px;" /> '.$price.'</td>
						<input type="hidden" name="name" value="'.$value2['naam'].'" />
						<td align="center"><input type="number" name="amount" style="width:60px;" min="1" max="'.$itemData[$value2['naam']].'" class="input-blue"/> <input type="submit" name="verkoop" value="OK!" class="button" /></td>
					</form></tr>';
				} else if ($key == 'items') {
					echo '<tr>
						<td style="text-align: left; padding-left: 27px;"><img src="' . $static_url . '/images/items/'.$value2['naam'].'.png" title="'.nl2br($value2['omschrijving_' . $_COOKIE['pa_language']]).'" class="elipse"/><b>'.$value2['naam'].'</b></td>
						<td align="center"><b>1x</b></td>
						<td align="center"><b>--</b></td>
						<td align="center"><b>--</b></td>
					</tr>';
				} else if (in_array($key, array('stones','special items','potions'))) {
					echo '<tr><form method="post">
						<td style="text-align: left; padding-left: 27px;"><img src="' . $static_url . '/images/items/'.$value2['naam'].'.png" title="'.nl2br($value2['omschrijving_' . $_COOKIE['pa_language']]).'" class="elipse"/><b>'.$value2['naam'].'</b></td>
						<td align="center" id="num_' . str_replace(' ', '_', $value2['naam']) . '"><b>'.$itemData[$value2['naam']].'x</b></td>
						<td><img src="' . $static_url . '/images/icons/' . $munt . '.png" style="margin-bottom:-3px;" /> '.$price.'</b></td>
						<input type="hidden" name="wat" value="use_potion" />
						<input type="hidden" name="name" value="'.$value2['naam'].'">
						<td align="center"><input type="number" name="amount" style="width:60px;" min="1" max="'.$itemData[$value2['naam']].'" class="input-blue" /> <input type="submit" name="verkoop" value="OK!" class="button" /></td>';
						
						$use_item = "use_item('".$value2['soort']."', '".$value2['naam']."')";
						if ($value2['equip'] == 1) {
							$use_item = "use_item('".$value2['soort']."', '".$value2['naam']."', true)";
						}
						echo '<td align="center"><button type="button" onclick="'.$use_item.'" class="button">'.$txt['button_use'].'</button></td>';

					echo '</form></tr>';
				} else if (in_array($key, array('tm','hm'))) {
					$inaam = $value2['naam'];

					if ($key == 'hm') {
				
		if ($inaam == 'HM01') $type = 'Grass';
		else if ($inaam == 'HM02') $type = 'Flying';
		else if ($inaam == 'HM03' || $inaam == 'HM07' || $inaam == 'HM08') $type = 'Water';
		else if ($inaam == 'HM04' || $inaam == 'HM06') $type = 'Fighting';
		else $type = 'Electric';
						
					} else if ($key == 'tm') {
		$pegadadox = DB::exQuery("select omschrijving from tmhm where naam='".$inaam."'")->fetch_assoc();
		$pegadado = DB::exQuery("select soort from aanval where naam='".$pegadadox['omschrijving']."'")->fetch_assoc();
 		
		$type = $pegadado['soort'];
		
	
					}
					echo '<tr><form method="post">
						<td style="text-align: left; padding-left: 27px;"><img src="' . $static_url . '/images/items/Attack_'.$type.'.png" title="'.$inaam.' ('.$pegadadox['omschrijving'].')" class="elipse"/><b>'.$inaam.' ('.$pegadadox['omschrijving'].')</b></td>
						<td align="center"><b id="num_' . str_replace(' ', '_', $value2['naam']) . '">'.$itemData[$value2['naam']].'x</b></td>
						<td><img src="' . $static_url . '/images/icons/' . $munt . '.png" style="margin-bottom:-3px;" /> '.$price.'</td>
						<input type="hidden" name="name" value="'.$inaam.'" />
						<td align="center"><input type="number" name="amount" style="width:60px;" min="1" max="'.$itemData[$value2['naam']].'" class="input-blue" /> <input type="submit" name="verkoop" value="OK!" class="button" /></td>
						<td align="center"><button type="button" onclick="use_item(\'' . $value2['soort'] . '\', \'' . $value2['naam'] . '\');" class="button">'.$txt['button_use'].'</button></td>
					</form></tr>';
				}
			}
			echo '</tbody></table></div>';
		}
	}
} else echo '<div class="red">' . $txt['no_item'] . '</div>';


?>

<script>
	$('#itens').wlOrientation('<?=$_GET['category']?>');
</script>