<div class="orientation-bar" id="orientation" style="margin-bottom: -1px">
	<a href="./information&category=game-info" data-orientation="game-info" class="noanimate"><button type="button">F.A.Q</button></a>
	<a href="./information&category=attack-info" data-orientation="attack-info" class="noanimate"><button type="button">Info. Ataques</button></a>
	<a href="./information&category=mood-info" data-orientation="mood-info" class="noanimate"><button type="button">Info. Humores</button></a>
	<a href="./information&category=ability-info" data-orientation="ability-info" class="noanimate"><button type="button">Info. Habilidades</button></a>
	<a href="./information&category=items-info" data-orientation="items-info" class="noanimate"><button type="button">Info. Itens</button></a>
</div>
<?php
switch($_GET['category']) {
	case "game-info":
		echo $txt['informationpage'];
	break;

	case 'mood-info':
?>
<style type="text/css">
	.title {
		background-color: #F5F5F5;
		border-right: 1px solid #d8d8d8;
		font-family: dosismedium, sans-serif;
		font-size: 14px;
		font-weight: 400;
		text-align: center;
	}
	.green {
		background-color: #A9FA46;
		color: #4B800A;
	}
	.red {
		background-color: #FA4D59;
		color: #73030B;
	}
</style>
<div class="box-content" style="position: relative"><table class="general" width="100%">
	<thead>
		<tr><th colspan="6">Humor por status</th></tr>
		<tr>
			<th>#</th>
			<th>- Attack</th>
			<th>- Defense</th>
			<th>- Sp. Atk</th>
			<th>- Sp. Def</th>
			<th>- Speed</th>
		</tr>
	</thead>
	<tbody>
		<tr>
			<td>+ Attack</td>
			<td align="center">Hardy</td>
			<td align="center" class="green">Lonely</td>
			<td align="center" class="green">Adamant</td>
			<td align="center" class="green">Naughty</td>
			<td align="center" class="green">Brave</td>
		</tr>
		<tr>
			<td>+ Defense</td>
			<td align="center" class="red">Bold</td>
			<td align="center">Docile</td>
			<td align="center" class="green">Impish</td>
			<td align="center" class="green">Lax</td>
			<td align="center" class="green">Relaxed</td>
		<tr>
			<td>+ Sp. Atk</td>
			<td align="center" class="red">Modest</td>
			<td align="center" class="red">Mild</td>
			<td align="center">Bashful</td>
			<td align="center" class="green">Rash</td>
			<td align="center" class="green">Quiet</td>
		</tr>
		<tr>
			<td>+ Sp. Def</td>
			<td align="center" class="red">Calm</td>
			<td align="center" class="red">Gentle</td>
			<td align="center" class="red">Careful</td>
			<td align="center">Quirky</td>
			<td align="center" class="green">Sassy</td>
		</tr>
		<tr>
			<td>+ Speed</td>
			<td align="center" class="red">Timid</td>
			<td align="center" class="red">Hasty</td>
			<td align="center" class="red">Jolly</td>
			<td align="center" class="red">Naive</td>
			<td align="center">Serious</td>
		</tr>
	</tbody>
</table></div>
<div class="separator"></div>
<div class="box-content" style="position: relative"><table class="general" width="100%">
	<thead>
		<tr><th colspan="6">Humor por nome</th></tr>
		<tr>
			<th width="16.6%">#</th>
			<th width="16.6%">Ataque</th>
			<th width="16.6%">Defesa</th>
			<th width="16.6%">Speed</th>
			<th width="16.6%">Esp. Ataque</th>
			<th width="16.6%">Esp. Defesa</th>
		</tr>
	</thead>
	<tbody><?php
		$getNatures = DB::exQuery("SELECT * FROM `karakters` ORDER BY `karakter_naam` ASC");
		while($nature = $getNatures->fetch_assoc()) {
			echo '<tr>
				<td>' . ucfirst($nature['karakter_naam']) . '</td>
				<td align="center" class="' . (($nature['attack_add'] == '1.1') ? 'green' : (($nature['attack_add'] == '0.9') ? 'red' : '')) . '">' . (($nature['attack_add'] == '1.1') ? 'Aumenta' : (($nature['attack_add'] == '0.9') ? 'Diminui' : 'Neutro')) . '</td>
				<td align="center" class="' . (($nature['defence_add'] == '1.1') ? 'green' : (($nature['defence_add'] == '0.9') ? 'red' : '')) . '">' . (($nature['defence_add'] == '1.1') ? 'Aumenta' : (($nature['defence_add'] == '0.9') ? 'Diminui' : 'Neutro')) . '</td>
				<td align="center" class="' . (($nature['speed_add'] == '1.1') ? 'green' : (($nature['speed_add'] == '0.9') ? 'red' : '')) . '">' . (($nature['speed_add'] == '1.1') ? 'Aumenta' : (($nature['speed_add'] == '0.9') ? 'Diminui' : 'Neutro')) . '</td>
				<td align="center" class="' . (($nature['spc.attack_add'] == '1.1') ? 'green' : (($nature['spc.attack_add'] == '0.9') ? 'red' : '')) . '">' . (($nature['spc.attack_add'] == '1.1') ? 'Aumenta' : (($nature['spc.attack_add'] == '0.9') ? 'Diminui' : 'Neutro')) . '</td>
				<td align="center" class="' . (($nature['spc.defence_add'] == '1.1') ? 'green' : (($nature['spc.defence_add'] == '0.9') ? 'red' : '')) . '">' . (($nature['spc.defence_add'] == '1.1') ? 'Aumenta' : (($nature['spc.defence_add'] == '0.9') ? 'Diminui' : 'Neutro')) . '</td>
			</tr>';
		}
	?></tbody>
</table></div>
<?php
	break;
    case "attack-info":
?>
<div class="box-content" style="position: relative"><table class="general" width="100%">
	<thead><tr>
		<th width="30" style="text-align: center;"><?php echo $txt['#']; ?></th>
		<th width="160"><?php echo $txt['name']; ?></th>
		<th width="120" style="text-align: center;"><?php echo $txt['category']; ?></th>
		<th width="120" style="text-align: center;"><?php echo $txt['type']; ?></th>
		<th width="70" style="text-align: center;"><?php echo $txt['att']; ?></th>
		<th width="70" style="text-align: center;"><?php echo $txt['acc']; ?></th>
		<th width="130" style="text-align: center;"><?php echo $txt['effect']; ?></th>
		<th width="60" style="text-align: center;">Contato</th>
		<th width="60" style="text-align: center;"><?php echo $txt['ready']; ?></th>
	</tr></thead>
	<tbody>
<?php
if (!is_numeric($_GET['subpage']))	$subpage = 1;
else	$subpage = (int)$_GET['subpage'];

#Max aantal leden per pagina
$max = 20;
#Aantal attacks
if ((isset($_POST['search_att']) && strlen(trim($_POST['attack'])) != 0) || (isset($_GET['attack']) && strlen(trim($_GET['attack'])) != 0))	$search = "SELECT `id` FROM `aanval` WHERE `naam` REGEXP '" . (isset($_GET['attack']) && strlen(trim($_GET['attack'])) != 0 ? $_GET['attack'] : $_POST['attack']) . "'";
else	$search = "SELECT `id` FROM `aanval`";
$aantal_attacks = DB::exQuery($search)->num_rows;

$aantal_paginas = ceil($aantal_attacks/$max);
$pagina = $subpage * $max - $max; 

if ((isset($_POST['search_att']) && !is_array($_POST['search_att']) && strlen(trim($_POST['attack'])) != 0) || (isset($_GET['attack']) && strlen(trim($_GET['attack'])) != 0))	$search2 = "SELECT * FROM aanval WHERE `naam` REGEXP '" . (isset($_GET['attack']) && !is_array($_GET['attack']) && strlen(trim($_GET['attack'])) != 0 ? $_GET['attack'] : $_POST['attack']) . "' ORDER BY naam ASC LIMIT ".$pagina.", ".$max;
else	$search2 = "SELECT * FROM aanval ORDER BY naam ASC LIMIT " . $pagina . "," . $max;
$attackquery = DB::exQuery($search2);
for($number=1;$attack=$attackquery->fetch_assoc();++$number) {
	$type = strtolower($attack['soort']);
	$typeattack      = strtolower($attack['tipo']);
	if ($attack['effect_kans'] == '0' || empty($attack['effect_kans']) || ($attack['effect_naam'] != 'Sleep' && $attack['effect_naam'] != 'Paralyzed' && $attack['effect_naam'] != 'Poisoned' && $attack['effect_naam'] != 'Flinch' && $attack['effect_naam'] != 'Burn' && $attack['effect_naam'] != 'Freeze' && $attack['effect_naam'] != 'Confued'))	$effect = ' -- ';
	else	$effect = $attack['effect_kans'] . '% ' . $attack['effect_naam'];

	if ($attack['klaar'] == 'ja')	$klaar = '<img src="' . $static_url . '/images/icons/green.png" title="Aplica efeito">';
	else	$klaar = '<img src="' . $static_url . '/images/icons/red.png" title="Não aplica efeito">';

	$accuracy = 100 - $attack['mis'];
	$rank = $number + $pagina;
	$contact = $attack['makes_contact'];

	if ($contact) {
		$contact = '<img src="' . $static_url . '/images/icons/green.png" title="Faz contato">';
	} else {
		$contact = '<img src="' . $static_url . '/images/icons/red.png" title="Não faz contato">';
	}

	echo '<tr>
		<td align="center">'.$rank.'.</td>
		<td>'.$attack['naam'].'</td>
		<td align="center"><table><tr><td><div class="type-icon type-'.$typeattack.'">'.$typeattack.'</div></td></tr></table></td>
		<td align="center"><table><tr><td><div class="type-icon type-'.$type.'">'.$type.'</div></td></tr></table></td>
		<td align="center">'.$attack['sterkte'].'</td>
		<td align="center">'.$accuracy.'</td>
		<td align="center">'.$effect.'</td>
		<td align="center">'.$contact.'</td>
		<td align="center">'.$klaar.'</td>
	</tr>';
}
?>
	</tbody>
	<tfoot><tr>
		<td colspan="<?=($aantal_paginas > 1 ? '3' : '7');?>"><form action="./information&category=attack-info" method="post">
			<input type="text" name="attack" value="<?=(empty($_GET['attack']) ? $_POST['attack'] : $_GET['attack']);?>" placeholder="Buscar:" required />
			<input type="submit" name="search_att" value="Ok" class="button" />
		</form></td>
<?php
if ($aantal_paginas > 1) {
	#Pagina systeem
	$links = false;
	$rechts = false;
	echo '<td colspan="4" align="center"><div class="sabrosus">';
	if ($subpage == 1)	echo '<span class="disabled">&laquo;</span>';
	else {
		$back = $subpage-1;
		echo '<a href="./' . $_GET['page'] . '&amp;category='.$_GET['category'].'&amp;subpage=' . $back . ((isset($_GET['attack']) && strlen(trim($_GET['attack'])) != 0) ? '&amp;attack=' . $_GET['attack'] : ((isset($_POST['search_att']) && strlen(trim($_POST['attack'])) != 0) ? '&amp;attack=' . $_POST['attack'] : '')) . '">&laquo;</a>';
	}
	for($i = 1; $i <= $aantal_paginas; $i++) { 
		if ((2 >= $i) && ($subpage == $i))	echo '<span class="current">'.$i.'</span>';
		else if ((2 >= $i) && ($subpage != $i))	echo '<a href="./' . $_GET['page'] . '&amp;category='.$_GET['category'].'&amp;subpage=' . $i . ((isset($_GET['attack']) && strlen(trim($_GET['attack'])) != 0) ? '&amp;attack=' . $_GET['attack'] : ((isset($_POST['search_att']) && strlen(trim($_POST['attack'])) != 0) ? '&amp;attack=' . $_POST['attack'] : '')) . '">'.$i.'</a>';
		else if (($aantal_paginas-2 < $i) && ($subpage == $i))	echo '<span class="current">'.$i.'</span>';
		else if (($aantal_paginas-2 < $i) && ($subpage != $i))	echo '<a href="./' . $_GET['page'] . '&amp;category='.$_GET['category'].'&amp;subpage=' . $i . ((isset($_GET['attack']) && strlen(trim($_GET['attack'])) != 0) ? '&amp;attack=' . $_GET['attack'] : ((isset($_POST['search_att']) && strlen(trim($_POST['attack'])) != 0) ? '&amp;attack=' . $_POST['attack'] : '')) . '">'.$i.'</a>';
		else {
			$max = $subpage+3;
			$min = $subpage-3;  
			if ($subpage == $i)	echo '<span class="current">'.$i.'</span>';
			else if (($min < $i) && ($max > $i))	echo '<a href="./' . $_GET['page'] . '&amp;category='.$_GET['category'].'&amp;subpage=' . $i . ((isset($_GET['attack']) && strlen(trim($_GET['attack'])) != 0) ? '&amp;attack=' . $_GET['attack'] : ((isset($_POST['search_att']) && strlen(trim($_POST['attack'])) != 0) ? '&amp;attack=' . $_POST['attack'] : '')) . '">'.$i.'</a>';
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
	if ($aantal_paginas == $subpage)	echo '<span class="disabled">&raquo;</span>';
	else {
		$next = $subpage+1;
		echo '<a href="./' . $_GET['page'] . '&amp;category='.$_GET['category'].'&amp;subpage=' . $next . ((isset($_GET['attack']) && strlen(trim($_GET['attack'])) != 0) ? '&amp;attack=' . $_GET['attack'] : ((isset($_POST['search_att']) && strlen(trim($_POST['attack'])) != 0) ? '&amp;attack=' . $_POST['attack'] : '')) . '">&raquo;</a>';
	}
	echo "</div></td>";
}
?>
	</tr></tfoot>
</table></div>
<?php
	break;
    case "ability-info":
?>
<style>
	.ability-table {
		font-size: 12.5px
	}
</style>
<div class="box-content" style="position: relative">
	<table class="general" width="100%">
		<thead>
			<tr>
				<th width="10%">#</th>
				<th width="30%">Nome</th>
				<th width="60%" colspan="2">Descrição</th>
			</tr>
		</thead>
		<tbody class="ability-table">
		<?php
if (!is_numeric($_GET['subpage']))	$subpage = 1;
else	$subpage = (int)$_GET['subpage'];

#Max aantal leden per pagina
$max = 20;
#Aantal attacks
if ((isset($_POST['search_att']) && strlen(trim($_POST['attack'])) != 0) || (isset($_GET['attack']) && strlen(trim($_GET['attack'])) != 0))	$search = "SELECT `id` FROM `abilities` WHERE `name` REGEXP '" . (isset($_GET['attack']) && strlen(trim($_GET['attack'])) != 0 ? $_GET['attack'] : $_POST['attack']) . "'";
else	$search = "SELECT `id` FROM `abilities`";
$aantal_attacks = DB::exQuery($search)->num_rows;

$aantal_paginas = ceil($aantal_attacks/$max);
$pagina = $subpage * $max - $max; 

if ((isset($_POST['search_att']) && !is_array($_POST['search_att']) && strlen(trim($_POST['attack'])) != 0) || (isset($_GET['attack']) && strlen(trim($_GET['attack'])) != 0))	$search2 = "SELECT * FROM `abilities` WHERE `name` REGEXP '" . (isset($_GET['attack']) && !is_array($_GET['attack']) && strlen(trim($_GET['attack'])) != 0 ? $_GET['attack'] : $_POST['attack']) . "' ORDER BY name ASC LIMIT ".$pagina.", ".$max;
else	$search2 = "SELECT * FROM `abilities` ORDER BY `name` ASC LIMIT " . $pagina . "," . $max;
$attackquery = DB::exQuery($search2);
for($number=1;$attack=$attackquery->fetch_assoc();++$number) {
	$rank = $number + $pagina;
	echo '<tr>
		<td align="center">'.$rank.'.</td>
		<td>'.$attack['name'].'</td>
		<td colspan="2">'.$attack['descr'].'</td>
	</tr>';
}
?>
	</tbody>
	<tfoot><tr>
		<td colspan="<?=($aantal_paginas > 1 ? '3' : '3');?>"><form action="./information&category=ability-info" method="post">
			<input type="text" name="attack" value="<?=(empty($_GET['attack']) ? $_POST['attack'] : $_GET['attack']);?>" placeholder="Buscar:" required />
			<input type="submit" name="search_att" value="Ok" class="button" />
		</form></td>
<?php
if ($aantal_paginas > 1) {
	#Pagina systeem
	$links = false;
	$rechts = false;
	echo '<td colspan="2" align="center"><div class="sabrosus">';
	if ($subpage == 1)	echo '<span class="disabled">&laquo;</span>';
	else {
		$back = $subpage-1;
		echo '<a href="./' . $_GET['page'] . '&amp;category='.$_GET['category'].'&amp;subpage=' . $back . ((isset($_GET['attack']) && strlen(trim($_GET['attack'])) != 0) ? '&amp;attack=' . $_GET['attack'] : ((isset($_POST['search_att']) && strlen(trim($_POST['attack'])) != 0) ? '&amp;attack=' . $_POST['attack'] : '')) . '">&laquo;</a>';
	}
	for($i = 1; $i <= $aantal_paginas; $i++) { 
		if ((2 >= $i) && ($subpage == $i))	echo '<span class="current">'.$i.'</span>';
		else if ((2 >= $i) && ($subpage != $i))	echo '<a href="./' . $_GET['page'] . '&amp;category='.$_GET['category'].'&amp;subpage=' . $i . ((isset($_GET['attack']) && strlen(trim($_GET['attack'])) != 0) ? '&amp;attack=' . $_GET['attack'] : ((isset($_POST['search_att']) && strlen(trim($_POST['attack'])) != 0) ? '&amp;attack=' . $_POST['attack'] : '')) . '">'.$i.'</a>';
		else if (($aantal_paginas-2 < $i) && ($subpage == $i))	echo '<span class="current">'.$i.'</span>';
		else if (($aantal_paginas-2 < $i) && ($subpage != $i))	echo '<a href="./' . $_GET['page'] . '&amp;category='.$_GET['category'].'&amp;subpage=' . $i . ((isset($_GET['attack']) && strlen(trim($_GET['attack'])) != 0) ? '&amp;attack=' . $_GET['attack'] : ((isset($_POST['search_att']) && strlen(trim($_POST['attack'])) != 0) ? '&amp;attack=' . $_POST['attack'] : '')) . '">'.$i.'</a>';
		else {
			$max = $subpage+3;
			$min = $subpage-3;  
			if ($subpage == $i)	echo '<span class="current">'.$i.'</span>';
			else if (($min < $i) && ($max > $i))	echo '<a href="./' . $_GET['page'] . '&amp;category='.$_GET['category'].'&amp;subpage=' . $i . ((isset($_GET['attack']) && strlen(trim($_GET['attack'])) != 0) ? '&amp;attack=' . $_GET['attack'] : ((isset($_POST['search_att']) && strlen(trim($_POST['attack'])) != 0) ? '&amp;attack=' . $_POST['attack'] : '')) . '">'.$i.'</a>';
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
	if ($aantal_paginas == $subpage)	echo '<span class="disabled">&raquo;</span>';
	else {
		$next = $subpage+1;
		echo '<a href="./' . $_GET['page'] . '&amp;category='.$_GET['category'].'&amp;subpage=' . $next . ((isset($_GET['attack']) && strlen(trim($_GET['attack'])) != 0) ? '&amp;attack=' . $_GET['attack'] : ((isset($_POST['search_att']) && strlen(trim($_POST['attack'])) != 0) ? '&amp;attack=' . $_POST['attack'] : '')) . '">&raquo;</a>';
	}
	echo "</div></td>";
}
?>
	</tr></tfoot>
	</table>
</div>
<?php
	break;
    case "items-info":
?>
<style>
	.item-table {
		font-size: 12.5px;
		text-align: center
	}
</style>
<div class="box-content" style="position: relative">
	<table class="general" id="example" width="100%">
		<thead>
			<tr><th colspan="7">Lista de Itens</th></tr>
			<tr style="text-align: center">
				<td width="5%"><strong>#</strong></td>
				<td width="20%"><strong>Nome</strong></td>
				<td width="40%" class="no-sort"><strong>Descrição</strong></td>
				<td width="20%"><strong>Categoria</strong></td>
				<td width="5%"><strong>Tem no PokéMarket?</strong></td>
				<td width="5%"><strong>Roda da Fortuna?</strong></td>
				<td width="5%"><strong>Equipável?</strong></td>
			</tr>
		</thead>
		<tbody class="item-table">
		<?php
if (!is_numeric($_GET['subpage']))	$subpage = 1;
else	$subpage = (int)$_GET['subpage'];

#Max aantal leden per pagina
$max = 20;
#Aantal attacks
if ((isset($_POST['search_att']) && strlen(trim($_POST['attack'])) != 0) || (isset($_GET['attack']) && strlen(trim($_GET['attack'])) != 0))	$search = "SELECT `id` FROM `markt` WHERE `soort`!='pokemon' AND `soort`!='tm' AND `soort`!='hm' AND `naam` REGEXP '" . (isset($_GET['attack']) && strlen(trim($_GET['attack'])) != 0 ? $_GET['attack'] : $_POST['attack']) . "'";
else	$search = "SELECT `id` FROM `markt` WHERE `soort`!='pokemon' AND `soort`!='tm' AND `soort`!='hm'";
$aantal_attacks = DB::exQuery($search)->num_rows;

$aantal_paginas = ceil($aantal_attacks/$max);
$pagina = $subpage * $max - $max; 

if ((isset($_POST['search_att']) && !is_array($_POST['search_att']) && strlen(trim($_POST['attack'])) != 0) || (isset($_GET['attack']) && strlen(trim($_GET['attack'])) != 0))	$search2 = "SELECT * FROM `markt` WHERE `soort`!='pokemon' AND `soort`!='tm' AND `soort`!='hm' AND `naam` REGEXP '" . (isset($_GET['attack']) && !is_array($_GET['attack']) && strlen(trim($_GET['attack'])) != 0 ? $_GET['attack'] : $_POST['attack']) . "' ORDER BY naam ASC LIMIT ".$pagina.", ".$max;
else	$search2 = "SELECT * FROM `markt` WHERE `soort`!='pokemon' AND `soort`!='tm' AND `soort`!='hm' ORDER BY `soort` ASC LIMIT " . $pagina . "," . $max;
$attackquery = DB::exQuery($search2);
for($number=1;$attack=$attackquery->fetch_assoc();++$number) {
	$rank = $number + $pagina;
	$type_item = array(
		'balls' => 'Poké Balls',
		'items' => 'Itens Chave',
		'potions' => 'Poções',
		'stones' => 'Pedras',
		'special items' => 'Itens Especiais'
	);
	
	$equip = $attack['equip'];
	if ($attack['equip']) {
		$attack['equip'] = '<img src="' . $static_url . '/images/icons/green.png" title="Este item é equipável em um Pokémon!">';
	} else {
		$attack['equip'] = '<img src="' . $static_url . '/images/icons/red.png" title="Este item não é equipável em um Pokémon!">';
	}

	$roleta = $attack['roleta'];
	if ($attack['roleta'] == 'sim') {
		$attack['roleta'] = '<img src="' . $static_url . '/images/icons/green.png" title="Este item está disponível na Roda da Fortuna!">';
	} else {
		$attack['roleta'] = '<img src="' . $static_url . '/images/icons/red.png" title="Este item não está disponível na Roda da Fortuna!">';
	}

	$beschikbaar = $attack['beschikbaar'];
	if ($attack['beschikbaar']) {
		$attack['beschikbaar'] = '<img src="' . $static_url . '/images/icons/green.png" title="Este item está disponível no PokéMarket!">';
	} else {
		$attack['beschikbaar'] = '<img src="' . $static_url . '/images/icons/red.png" title="Este item não está disponível no PokéMarket!">';
	}

	echo '<tr>
		<td align="center">'.$rank.'.</td>
		<td align="left"><img src="'.$static_url.'/images/items/'.$attack['naam'].'.png" class="elipse" width="24" height="24">'.$attack['naam'].'</td>
		<td align="center">'.$attack['omschrijving_pt'].'</td>
		<td align="center"><b>'.$type_item[$attack['soort']].'</b></td>
		<td align="center" data-order="'.$beschikbaar.'"><b>'.$attack['beschikbaar'].'</b></td>
		<td align="center" data-order="'.$roleta.'"><b>'.$attack['roleta'].'</b></td>
		<td align="center" data-order="'.$equip.'"><b>'.$attack['equip'].'</b></td>
	</tr>';
}
?>
	</tbody>
	<tfoot><tr>
		<td colspan="<?=($aantal_paginas > 1 ? '3' : '3');?>"><form action="./information&category=items-info" method="post">
			<input type="text" name="attack" value="<?=(empty($_GET['attack']) ? $_POST['attack'] : $_GET['attack']);?>" placeholder="Buscar:" required />
			<input type="submit" name="search_att" value="Ok" class="button" />
		</form></td>
<?php
if ($aantal_paginas > 1) {
	#Pagina systeem
	$links = false;
	$rechts = false;
	echo '<td colspan="4" align="center"><div class="sabrosus">';
	if ($subpage == 1)	echo '<span class="disabled">&laquo;</span>';
	else {
		$back = $subpage-1;
		echo '<a href="./' . $_GET['page'] . '&amp;category='.$_GET['category'].'&amp;subpage=' . $back . ((isset($_GET['attack']) && strlen(trim($_GET['attack'])) != 0) ? '&amp;attack=' . $_GET['attack'] : ((isset($_POST['search_att']) && strlen(trim($_POST['attack'])) != 0) ? '&amp;attack=' . $_POST['attack'] : '')) . '">&laquo;</a>';
	}
	for($i = 1; $i <= $aantal_paginas; $i++) { 
		if ((2 >= $i) && ($subpage == $i))	echo '<span class="current">'.$i.'</span>';
		else if ((2 >= $i) && ($subpage != $i))	echo '<a href="./' . $_GET['page'] . '&amp;category='.$_GET['category'].'&amp;subpage=' . $i . ((isset($_GET['attack']) && strlen(trim($_GET['attack'])) != 0) ? '&amp;attack=' . $_GET['attack'] : ((isset($_POST['search_att']) && strlen(trim($_POST['attack'])) != 0) ? '&amp;attack=' . $_POST['attack'] : '')) . '">'.$i.'</a>';
		else if (($aantal_paginas-2 < $i) && ($subpage == $i))	echo '<span class="current">'.$i.'</span>';
		else if (($aantal_paginas-2 < $i) && ($subpage != $i))	echo '<a href="./' . $_GET['page'] . '&amp;category='.$_GET['category'].'&amp;subpage=' . $i . ((isset($_GET['attack']) && strlen(trim($_GET['attack'])) != 0) ? '&amp;attack=' . $_GET['attack'] : ((isset($_POST['search_att']) && strlen(trim($_POST['attack'])) != 0) ? '&amp;attack=' . $_POST['attack'] : '')) . '">'.$i.'</a>';
		else {
			$max = $subpage+3;
			$min = $subpage-3;  
			if ($subpage == $i)	echo '<span class="current">'.$i.'</span>';
			else if (($min < $i) && ($max > $i))	echo '<a href="./' . $_GET['page'] . '&amp;category='.$_GET['category'].'&amp;subpage=' . $i . ((isset($_GET['attack']) && strlen(trim($_GET['attack'])) != 0) ? '&amp;attack=' . $_GET['attack'] : ((isset($_POST['search_att']) && strlen(trim($_POST['attack'])) != 0) ? '&amp;attack=' . $_POST['attack'] : '')) . '">'.$i.'</a>';
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
	if ($aantal_paginas == $subpage)	echo '<span class="disabled">&raquo;</span>';
	else {
		$next = $subpage+1;
		echo '<a href="./' . $_GET['page'] . '&amp;category='.$_GET['category'].'&amp;subpage=' . $next . ((isset($_GET['attack']) && strlen(trim($_GET['attack'])) != 0) ? '&amp;attack=' . $_GET['attack'] : ((isset($_POST['search_att']) && strlen(trim($_POST['attack'])) != 0) ? '&amp;attack=' . $_POST['attack'] : '')) . '">&raquo;</a>';
	}
	echo "</div></td>";
}
?>
	</tr></tfoot>
	</table>
</div>
<?php
	break;
	default:	exit(header("LOCATION: ./information&category=attack-info"));	break;
}
?>

<script>
	$('#orientation').wlOrientation('<?=$_GET['category']?>');
</script>