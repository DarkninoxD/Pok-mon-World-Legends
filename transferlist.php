<?php
#include dit script als je de pagina alleen kunt zien als je ingelogd bent.
require_once('app/includes/resources/security.php');

$inhuis = DB::exQuery("SELECT `id` FROM `pokemon_speler` WHERE `user_id`='".$_SESSION['id']."' AND (opzak = 'nee' OR opzak = 'tra')")->num_rows;

if ($gebruiker['huis'] == "doos") $over  = 2-$inhuis;
else if ($gebruiker['huis'] == "shuis") $over  = 20-$inhuis;
else if ($gebruiker['huis'] == "nhuis") $over  = 100-$inhuis;
else if ($gebruiker['huis'] == "villa") $over  = 2500-$inhuis;

$type = '';
$types = array ('private', 'auction', 'direct');
if (isset($_GET['type']) && in_array($_GET['type'], $types)) {
	$type = $_GET['type'];
} else {
	$type = 'direct';
}

$mine = 'false';

if (isset($_GET['mine']) && in_array($_GET['mine'], array('true', 'false'))) {
	$mine = $_GET['mine'];
}

if (isset($_POST['buy']) && $mine == 'false') {
	if ($over > 0) {
		$tid = base64_decode($_POST['buy']);
		$action = DB::exQuery("SELECT `t`.*, `g`.`acc_id` FROM `transferlijst` t INNER JOIN `gebruikers` g ON `g`.`user_id` = `t`.`user_id` WHERE id='$tid'");
		$buy = $action->fetch_assoc();
		
		if ($buy['user_id'] == $_SESSION['id']) {
			echo '<div class="red">Você não pode comprar seu Pokémon!</div>';
		} else if ($gebruiker['rank'] < 4) {
			echo '<div class="red">Você não tem RANK suficiente para comprar Pokémon!</div>';
		} else if ($buy['type'] == 'private' && $buy['to_user'] != $_SESSION['id']) {
			echo '<div class="red">Você não pode comprar este Pokémon!</div>';
		} else if ($buy['silver'] > $gebruiker['silver'] || $buy['gold'] > $rekening['gold']) {
			echo '<div class="red">Você não tem Silvers ou Gold suficientes para comprar este Pokémon!</div>';
		} else if ($buy['type'] == 'auction') {
			echo '<div class="red">ERROR 202</div>';
		} else if ($action->num_rows != 1) {
			echo '<div class="red">Este Pokémon já foi vendido!</div>';
		} else {
			$tl = DB::exQuery("SELECT `s`.`wild_id`, `s`.`user_id`,`s`.`icon`, `s`.`level`, `s`.`item`, `s`.`roepnaam`, `w`.`naam` FROM `pokemon_speler` s INNER JOIN `pokemon_wild` w ON `s`.`wild_id` = `w`.`wild_id` WHERE id='$buy[pokemon_id]'")->fetch_assoc();
			$tl['naam'] = pokemon_naam($tl['naam'], $tl['roepnaam'], $tl['icon']);

			DB::exQuery("UPDATE `pokemon_speler` SET `user_id`='".$_SESSION['id']."',`trade`='1.5',`opzak`='nee',`opzak_nummer`='' WHERE `id`='".$buy['pokemon_id']."'");
			
			if ($buy['type'] == 'direct') $quests->setStatus('buy_direct', $_SESSION['id']);
			if ($buy['type'] == 'private') $quests->setStatus('buy_private', $_SESSION['id']);

			DB::exQuery("UPDATE `gebruikers` SET `silver`=`silver`-'".$buy['silver']."', `aantalpokemon`=`aantalpokemon`+'1' WHERE `user_id`='".$_SESSION['id']."'");
			DB::exQuery("UPDATE `gebruikers` SET `silver`=`silver`+'".$buy['silver']."', `aantalpokemon`=`aantalpokemon`-'1' WHERE `user_id`='".$buy['user_id']."'");
			DB::exQuery("UPDATE `rekeningen` SET `gold`=`gold`-'".$buy['gold']."' WHERE `acc_id`='".$_SESSION['acc_id']."'");
			DB::exQuery("UPDATE `rekeningen` SET `gold`=`gold`+'".$buy['gold']."' WHERE `acc_id`='".$buy['acc_id']."'");

			DB::exQuery("DELETE FROM `transferlijst` WHERE `id`='".$tid."'");
			update_pokedex($tl['wild_id'], '', 'buy');

			DB::exQuery("INSERT INTO transferlist_log (date, wild_id, speler_id, level, seller, buyer, silver, gold, item) VALUES (NOW(), '".$tl['wild_id']."', '".$tl['id']."', '".$tl['level']."', '".$buy['user_id']."', '".$_SESSION['id']."', '".$buy['silver']."', '".$buy['gold']."', '".$tl['item']."')");

			$event = '<img src="' . $static_url . '/images/icons/blue.png" width="16" height="16" class="imglower" /> <a href="./profile&player='.$gebruiker['username'].'">'.$gebruiker['username'].'</a> comprou seu <a href="./pokemon-profile&id='.$buy['pokemon_id'].'">'.$tl['naam'].'</a> por: '.highamount($buy['silver']).' <img src="' . $static_url . '/images/icons/silver.png" title="Silver" width="16" height="16" /> e '.highamount($buy['gold']).'<img src="' . $static_url . '/images/icons/gold.png" title="Gold" width="16" height="16" />!';

			DB::exQuery("INSERT INTO gebeurtenis (`datum`,`ontvanger_id`,`bericht`,`gelezen`) VALUES (NOW(), '" . $buy['user_id'] . "', '" . $event . "', '0')");

			header('location: ./pokemon-profile&id='.$buy['pokemon_id']);

		}
	} else {
		echo '<div class="red">Você está com sua casa cheia! Compre uma casa maior clicando <a href="house-seller">AQUI</a>.</div>';
	}
}

$filter = ''; $ptotal = false; $filter_arr = array();
if (!empty($_GET['specie']) && ctype_digit($_GET['specie'])) {
	$filter = "`ps`.`wild_id`='$_GET[specie]'";
	$filter_arr[0] = $_GET['specie'];
}

if (!empty($_GET['total']) && ctype_digit($_GET['total'])) { 
	$ptotal = $_GET['total'];
	$filter_arr[1] = $_GET['total'];
}

if (!empty($_GET['shiny']) && $_GET['shiny'] == true) { 
	$filter = "`ps`.`shiny`='1'";
	$filter_arr[2] = true;
}

if (!empty($_GET['region']) && $region != 'Todas') { 
	$filter = "`pw`.`wereld`='$_GET[region]'";
	$filter_arr[3] = $_GET['region'];
}

if (!empty($_GET['price']) && ctype_digit($_GET['price']) && in_array(@$_GET['price_type'], array('silver', 'golds'))) {
	if ($_GET['price_type'] == 'silver') {
		$filter = "`t`.`silver`<='$_GET[price]'";
	} else {
		$filter = "`t`.`gold`<='$_GET[price]'";
	}

	$filter_arr[4] = $_GET['price'];
	$filter_arr[5] = $_GET['price_type'];
}

if (!empty($_GET['trainer'])) { 
	$filter = "`g`.`username`='$_GET[trainer]'";
	$filter_arr[6] = $_GET['trainer'];
}

if (!empty($_GET['level']) && ctype_digit($_GET['level']) && $_GET['level'] > 0 && $_GET['level'] <= 100 && in_array(@$_GET['level_type'], array('maior', 'menor'))) {
	if ($_GET['level_type'] == 'maior') {
		$filter = "`ps`.`level`>='$_GET[level]'";
	} else {
		$filter = "`ps`.`level`<='$_GET[level]'";
	}
	$filter_arr[7] = $_GET['level'];
	$filter_arr[8] = $_GET['level_type'];
}

if (!empty($_GET['equip'])) {
	if ($_GET['equip'] == 'none') {
		$filter = "(`ps`.`item`='' OR `ps`.`item` IS NULL)";
	} else {
		$filter = "`ps`.`item`='$_GET[equip]'";
	}
	$filter_arr[9] = str_replace('_', ' ', $_GET['equip']);
}

$npctitle	= 'Mercado de pokémons';
$npctext	= 'Compre e venda Pokémons pelo melhor preço com nossos métodos de venda: <div class="badge-wip">Leilões</div> <div class="badge-wip">Diretas</div> <div class="badge-wip">Privadas</div>';
echo addNPCBox(36, $npctitle, $npctext);
if (!empty($message))	echo $message;

?>
<style>
	#npc-section {
		height: 180px;
	}
	#npc-image {
		width: 176px!important;
		margin-top: 30px!important;
	}

	.select-transferlist {
		padding: 5px 13px!important;
		border: 0!important;
	}

	.filtro b {
		margin-left: 10px
	}
</style>

<?php
$base_url = getUrl('/(&type=[a-z]+)/', '/(&subpage=[0-9]+)/');
?>

<script src="<?=$static_url?>/javascripts/timeago/jquery.timeago.js"></script>

<div class="box-content filtro" style="background: #1C3248; overflow: unset; color: #fff">
	<form method="get">
		<div class="triangle">
			<h3 class="title" style="width: 60%;font-size: 19px;margin-bottom: -5px;margin-top: 6px;">Filtrar Vendas</h3>
			<div style="text-align: left; padding-top: 17px;">
				<b>Espécie: </b><select class="select-transferlist" id="specie"><option value="">Qualquer</option>
					<?php
						$wild = DB::exQuery("SELECT `wild_id`, `naam`, `real_id` FROM `pokemon_wild` ORDER BY `real_id`");
						while ($w = $wild->fetch_assoc()) {
							$selected = '';
							if ($w['wild_id'] == $filter_arr[0]) {
								$selected = 'selected';
							}

							echo '<option value="'.$w['wild_id'].'" '.$selected.'>#'.$w['real_id'].' - '.$w['naam'].'</option>';
						}
					?>
				</select>

				<b>Poder total <span title="Maior ou igual" style="cursor: pointer">[?]</span>: </b><input type="number" min="0" placeholder="0" value="<?=$filter_arr[1]?>" class="select-transferlist" id="total">
				<b>Apenas Shiny <span title="Marcado para SIM, desmarcado para Shiny e Normal" style="cursor: pointer">[?]</span></b><input type="checkbox" class="select-transferlist" id="shiny" style="vertical-align: sub;" <?=($filter_arr[2])? 'checked' : '';?>></select>
			</div>
		</div>
		<div style="padding-top: 7px;text-align: left;padding-left: 7px;">
			<b>Região: </b><select class="select-transferlist" id="region">
				<?php
					$array = array('Todas', 'Kanto', 'Johto', 'Hoenn', 'Sinnoh', 'Unova', 'Kalos', 'Alola');
					foreach($array as $w) {
						$selected = '';
						if ($w == $filter_arr[3]) {
							$selected = 'selected';
						}

						echo '<option value="'.$w.'" '.$selected.'>'.$w.'</option>';
					}
				?>
			</select>
			<b>Preço: </b><input type="number" min="0" class="select-transferlist" id="price" placeholder="0" style="width: 70px" value="<?=$filter_arr[4]?>"><select class="select-transferlist" id="price-type"><option value="silver" <?=($filter_arr[5] == 'silver')? 'selected' : ''?>>Silvers</option><option value="golds" <?=($filter_arr[5] == 'golds')? 'selected' : ''?>>Golds</option></select>
			<b>Treinador: </b><input type="text" class="select-transferlist" id="trainer" placeholder="Qualquer" value="<?=$filter_arr[6]?>">
			<b>Level: </b><input type="number" min="0" max="100" class="select-transferlist" id="level" placeholder="0" style="width: 70px" value="<?=$filter_arr[7]?>"><select class="select-transferlist" id="level-type"><option value="maior" <?=($filter_arr[8] == 'maior')? 'selected' : ''?>>Maior</option><option value="menor" <?=($filter_arr[8] == 'menor')? 'selected' : ''?>>Menor</option></select>
			<b>Equipado: </b><select class="select-transferlist" id="equip" style="width: 100px"><option value="">Qualquer</option><option value="none" <?=($filter_arr[9] == 'none')? 'selected' : ''?>>Nenhum</option>
				<?php
					$itens = DB::exQuery("SELECT `naam` FROM `markt` WHERE `equip`='1'");
					while ($i = $itens->fetch_assoc()) {
						$selected = '';
						if ($i['naam'] == $filter_arr[9]) {
							$selected = 'selected';
						}

						echo '<option value="'.str_replace(' ', '_', $i['naam']).'" '.$selected.'>'.$i['naam'].'</option>';
					}
				?>
			</select>
		</div>
		<div style="margin-top: 11px;border-top: 1px solid #577599">
			<button type="button" style="margin: 6px" onclick="filtro()">BUSCAR</button>
		</div>
	</form>
</div>

<script>
	function filtro () {
		let specie = $('#specie').val();
		let total = $('#total').val();
		let shiny = $('#shiny').is(':checked');
		let region = $('#region').val();
		let price = $('#price').val();
		let price_type = $('#price-type').val();
		let trainer = $('#trainer').val();
		let level = $('#level').val();
		let level_type = $('#level-type').val();
		let equip = $('#equip').val();

		let link = '';

		if (specie != '' && $.isNumeric(specie)) link = '&specie='+specie;
		if (total != '' && $.isNumeric(total) && total > 0) link += '&total='+total;
		if (shiny == true) link += '&shiny='+shiny;
		if (region != '' && region != 'Todas') link += '&region='+region;
		if (price != '' && $.isNumeric(price) && price > 0 && (price_type == 'silver' || price_type == 'golds')) link += '&price_type='+price_type+'&price='+price
		if (trainer != '') link += '&trainer='+trainer;
		if (level != '' && $.isNumeric(level) && level > 0 && level <= 100 && (level_type == 'maior' || level_type == 'menor')) link += '&level_type='+level_type+'&level='+level
		if (equip != '') link += '&equip='+equip;

		window.location = '<?=getUrl('/(&specie=[0-9]+)/', '/(&total=[0-9]+)/', '/(&shiny=[true-false]+)/', '/(&region=[A-z]+)/', '/(&price_type=[a-z]+)/', '/(&price=[0-9]+)/', '/(&trainer=[A-z]+)/', '/(&level_type=[a-z]+)/', '/(&level=[0-9]+)/', '/(&equip=[A-z_-]+)/')?>'+link;
	}
</script>

<div class="orientation-bar" id="transferlist" style="margin-top: 7px">
	<a href="<?=$base_url?>&type=auction" data-orientation="auction"><button type="button">Leilões</button></a>
	<a href="<?=$base_url?>&type=direct" data-orientation="direct"><button type="button">Diretas</button></a>
	<a href="<?=$base_url?>&type=private" data-orientation="private"><button type="button">Privadas</button></a>
	<input type="checkbox" name="mine" style="vertical-align: middle" <?=($mine == 'true')? 'checked' : '';?>> Mostrar apenas meus Pokémons
</div>
<div class="box-content" style="margin-top: -1px; text-align: center">
<table class="general" id="example">
	<thead>
		<tr>
            <td><strong><?php echo $txt['pokemon']; ?></strong></td>
			<td class="no-sort"><strong>Características</strong></td>
            <td><strong>Poder Total</strong></td>
			<td><strong>Item</strong></td>
			<td><strong>Data</strong></td>
			<td width="130"><strong>Preço</strong></td>
			<td class="no-sort" width="30"></td>
		</tr>
	</thead>
	<tbody><?php
	if ($over > 0) {
	if (!is_numeric($_GET['subpage']))	$subpage = 1; 
	else	$subpage = $_GET['subpage']; 

	if ($type == 'private') {
		if ($mine == 'true') {
			$aantal_pokemon = DB::exQuery("SELECT * FROM `transferlijst` WHERE `type`='private' AND `user_id`='$_SESSION[id]'")->num_rows;
		} else {
			$aantal_pokemon = DB::exQuery("SELECT * FROM `transferlijst` WHERE `type`='private' AND `to_user`='$_SESSION[id]'")->num_rows;
		}
	} else {
		if ($mine == 'true') {
			$aantal_pokemon = DB::exQuery("SELECT * FROM `transferlijst` WHERE `type`='$type' AND `user_id`='$_SESSION[id]'")->num_rows;
		} else {
			$aantal_pokemon = DB::exQuery("SELECT * FROM `transferlijst` WHERE `type`='$type' AND `user_id`!='$_SESSION[id]'")->num_rows;
		}
	}

	if (!empty($filter)) $filter = 'AND '.$filter;

	$max = 20;
	$aantal_paginas = ceil($aantal_pokemon/$max); 
	if ($aantal_paginas == 0)	$aantal_paginas = 1;   
	$pagina = $subpage * $max - $max; 

	#Gegevens laden voor de lijst 
	if ($gebruiker['rank'] >= 4) {
		if ($type == $types[0]) {
			if ($mine == 'true') {
				$mine = "`t`.`user_id`='".$_SESSION['id']."'";
			} else {
				$mine = "`t`.`to_user`='".$_SESSION['id']."'";
			}

			$tl_sql = DB::exQuery("SELECT `pw`.`naam`,`pw`.`type1`,`pw`.`type2`,`pw`.`wereld`,`ps`.*,`t`.`id` AS `tid`,`t`.`silver`,`t`.`gold`, `t`.`datum`, `t`.`negociavel`, `g`.`username` AS `owner` FROM `pokemon_wild` AS `pw` INNER JOIN `pokemon_speler` AS `ps` ON `pw`.`wild_id`=`ps`.`wild_id` INNER JOIN `transferlijst` AS `t` ON `t`.`pokemon_id`=`ps`.`id` INNER JOIN `gebruikers` AS `g` ON `ps`.`user_id`=`g`.`user_id` WHERE (`t`.`type`='private' AND ".$mine.") $filter ORDER BY `t`.`id` DESC LIMIT " . $pagina . "," . $max);
		} else {
			if ($mine == 'true') {
				$mine = "AND `t`.`user_id`='".$_SESSION['id']."'";
			} else {
				$mine = "AND `t`.`user_id`!='".$_SESSION['id']."'";
			}

			if ($type == 'auction') {
				$date = strtotime(date('d-m-Y H:i'));
				$a = " AND `t`.`time_end` > '".$date."'";
			}

			$tl_sql = DB::exQuery("SELECT `pw`.`naam`,`pw`.`type1`,`pw`.`type2`,`pw`.`wereld`,`ps`.*, `t`.`id` AS `tid`,`t`.`silver`,`t`.`gold`, `t`.`datum`, `t`.`negociavel`, `t`.`time_end`, `t`.`lances`, `g`.`username` AS `owner` FROM `pokemon_wild` AS `pw` INNER JOIN `pokemon_speler` AS `ps` ON `pw`.`wild_id`=`ps`.`wild_id` INNER JOIN `transferlijst` AS `t` ON `t`.`pokemon_id`=`ps`.`id` INNER JOIN `gebruikers` AS `g` ON `ps`.`user_id`=`g`.`user_id` WHERE (`t`.`type`='".$type."' ".$mine.$a.") $filter ORDER BY `t`.`id` DESC LIMIT " . $pagina . "," . $max);
		}

		for($j=$pagina+1;$tl=$tl_sql->fetch_assoc();++$j) {		
			$decodedid = base64_encode($tl['tid']);
			$tid = $tl['tid'];
			$ngc = $tl['negociavel'];
			$time_end = $tl['time_end'];
			$lances = $tl['lances'];
			
			if ($type == 'auction') {
				$time_end = date('Y-m-d H:i:s', $time_end);
				$datum = '<span><b><script id="remove">document.write(jQuery.timeago("'.$time_end.' UTC")); document.getElementById("remove").outerHTML = "";</script></b></span>';
			} else {
				$datum = $tl['datum'];
			}
			
			$tl = pokemonei($tl, $txt);
			$popup = pokemon_popup($tl, $txt);
			$tl['naam'] = pokemon_naam($tl['naam'], $tl['roepnaam'], $tl['icon']);

			$shinystar = '';
			$pokemontype = $tl['type1'];

			#Heeft pokemon meerdere types
			if (!empty($tl['type2']))	$pokemontype = $tl['type1'] . " - " . $tl['type2'];

			if ($tl['shiny'] == 1) {
				$shinystar = '<img src="'.$static_url.'/images/icons/lidbetaald.png" width="16" height="16" />';
			}

			$tl['ability'] = ability($tl['ability'])['name'];

			$price_gd = ($tl['gold'] > 0)? highamount(round($tl['gold'])).' <img src="'.$static_url.'/images/icons/gold.png">' : '';
			$price_sl = ($tl['silver'] > 0)? highamount(round($tl['silver'])).' <img src="'.$static_url.'/images/icons/silver.png">' : '';
			$ngc = ($ngc)? '<p style="margin: 0; color: #d25757; font-size: 12px">NEGOCIÁVEL</p>' : '';
			$suffix = (!empty($price_gd) && !empty($price_sl))? ' e ' : '';
			
			if ($type == 'auction') {
				$ngc = '<p style="margin: 0; color: #d25757; font-size: 12px">'.$lances.' LANCES</p>';
			}

			$price = $price_sl.$suffix.$price_gd.$ngc;

			$item = (isset($tl['item']))? '<img src=\'' . $static_url . '/images/items/' . $tl['item'] . '.png\' title=\'' . $tl['item'] . '\' />' : 'Nenhum';

			$tl['powertotal'] = $tl['attack'] + $tl['defence'] + $tl['speed'] + $tl['spc.attack'] + $tl['spc.defence'];

			if ($ptotal != false && $tl['powertotal'] <= $ptotal) continue;

			$remove = '<button type="button" onclick="delete_from(\'' . $tl['id'] . '\',\'' . $tid . '\');">REMOVER</button>';
			if ($type == 'auction') {
				if ($lances > 0) {
					$remove = '<button type="button" onclick="window.location = \'./pokemon-profile&id='.$tl['id'].'\'">VISUALIZAR</button>';
				}
				$btn = '<button type="button" onclick="window.location = \'./pokemon-profile&id='.$tl['id'].'\'">DAR LANCE</button>';
			} else {
				$btn = '<button type="button" class="buy-pokemon" data-buy="'.base64_encode($tid).'">Comprar</button>';
			}
			
			$buy = (($_SESSION['id'] == $tl['user_id']) ? $remove : '<div class="alternate" style="font-weight: 600;"><span>'.$price.'</span>'.$btn.'</div>');

			echo '<tr id="' . $tl['id'] . '">
				<td data-sort="'.$tl['naam'].'" style="text-align: left; padding-left: 27px;"><img src="'.$static_url.'/'.$tl['animatie'].'" class="tip_top-middle elipse" title="' . $popup . '" width="32" height="32"/><b>'. $tl['naam'] . $shinystar . '</b></td>
				<td style="text-align: left"><b>Nível: </b>' . $tl['level'] . '<br><b>Humor: </b>'.$tl['karakter'].'<br><b>Habilidade: </b><a href="./information&category=ability-info&attack='.$tl['ability'].'">'.$tl['ability'].'</a></td>
				<td data-sort="'.$tl['powertotal'].'">'. highamount($tl['powertotal']) .'</td>
				<td data-sort="'.$tl['item'].'">'.$item.'</td>
				<td>'.$datum.'</td>
				<td data-sort="'.$tl['silver'].'" align="center">' . $buy . '</td>
				<td><a href="./pokemon-profile&id='.$tl['id'].'"><div class="lupa"></div></a></td></tr>';
		}
	} else {
		echo '<tr><td colspan="7"><div class="red" style="margin-top: 5px">RANK MÍNIMO PARA COMPRAR OU VENDER POKÉMONS: 4 - TRAINER. CONTINUE UPANDO PARA LIBERAR!</div></td></tr>';
	}
	?></tbody>
	<?php
		$base_url = getUrl('/&subpage=[0-9]/');
		if ($aantal_paginas > 1) {
			$links = false;
			$rechts = false;
			echo '<tfoot>';
			echo '<td align="center" colspan="6"><div class="sabrosus">';
			if ($subpage == 1)	echo '<span class="disabled">&laquo;</span>';
			else {
				$back = $subpage-1;
				echo '<a href="'.$base_url.'&subpage='.$back.'">&laquo;</a>';
			}
			for($i=1;$i<=$aantal_paginas;++$i) {
				if (3 >= $i && $subpage == $i)	echo '<span class="current">'.$i.'</span>';
				else if (3 >= $i && $subpage != $i)	echo '<a href="'.$base_url.'&subpage='.$i.'">'.$i.'</a>';
				else if ($aantal_paginas-2 < $i && $subpage == $i)	echo '<span class="current">'.$i.'</span>';
				else if ($aantal_paginas-2 < $i && $subpage != $i)	echo '<a href="'.$base_url.'&subpage='.$i.'">'.$i.'</a>';
				else {
					$max = $subpage + 3;
					$min = $subpage -3;  
					if ($page == $i)	echo '<span class="current">'.$i.'</span>';
					else if ($min < $i && $max > $i)	echo '<a href="'.$base_url.'&subpage='.$i.'">'.$i.'</a>';
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
				echo '<a href="'.$base_url.'&subpage='.$next.'">&raquo;</a>';
			}
			echo '</div></td></tfoot>';
		}
?>
</table>
</div>
<form method="post" id="form_buy"><input type="hidden" name="buy"></form>
<script type='text/javascript'>
	function IsNumeric(sText) {
		var ValidChars = "0123456789",
			IsNumber = true,
			Char;

		for(i=0;i<sText.length && IsNumber==true;++i) {
			Char = sText.charAt(i);
			if (ValidChars.indexOf(Char) == -1)	IsNumber = false;
		}
		return IsNumber;
	}

	function delete_from(pokemonid, tid) {
		if (IsNumeric(pokemonid)) {
			$("#" + pokemonid).hide();
			$.ajax({
				type: "GET",
				url: "./ajax.php?act=transferlist-remove&pokemonid=" + pokemonid
			});
		}
	}

	$('input[name="mine"]').change(function () {
		let check = 'false';

		if (this.checked) check = 'true';

		window.location = '<?=getUrl('/(&mine=[true-false]+)/')?>&mine='+check;
	});

	$('.buy-pokemon').click (function () {
		let buy = $(this).data('buy');
		$('input[name="buy"]').val(buy);
		$('#form_buy').submit();
	});

	$('#transferlist').wlOrientation('<?=$type?>');

	$('.alternate').children('button').hide();

	$('.alternate').mouseover(function () {
		$(this).children('button').show();
		$(this).children('span').hide();
	});

	$('.alternate').mouseout(function () {
		$(this).children('span').show();
		$(this).children('button').hide();
	});
</script>

<?php } else { echo '<tr><td colspan="7"><div class="red">Você está com sua casa cheia! Compre uma casa maior clicando <a href="house-seller">AQUI</a>.</div></tr></td></tbody></table></div>';} ?>