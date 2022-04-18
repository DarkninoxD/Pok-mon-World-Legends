<?php
$page = 'information';
$_GET['category'] = 'pokemon-info';
require_once('language/language-pages.php');

if (isset($_GET['pokemon']) && is_numeric($_GET['pokemon'])) {
	$result = DB::exQuery("SELECT `pokemon_wild`.`wild_id`,`pokemon_wild`.`evolutie`,`pokemon_wild`.`egg`,`pokemon_wild`.`ability`,`real_id`,`effort_hp`,`effort_attack`,`effort_defence`,`effort_spc.attack`,`effort_spc.defence`,`effort_speed`,`vangbaarheid`,`naam`,`zeldzaamheid`,`type1`,`type2`,`gebied`,`wereld`,`pokemon_wild`.`aanval_1`,`pokemon_wild`.`aanval_2`,`pokemon_wild`.`aanval_3`,`pokemon_wild`.`aanval_4`,`attack_base`,`defence_base`,`spc.attack_base`,`spc.defence_base`,`speed_base`,`hp_base`,COUNT(`pokemon_speler`.`wild_id`) AS `hoeveelingame` FROM `pokemon_wild` LEFT JOIN `pokemon_speler` ON `pokemon_wild`.`wild_id`=`pokemon_speler`.`wild_id` WHERE `pokemon_wild`.`wild_id`='" . (int)$_GET['pokemon'] . "' AND `pokemon_wild`.`aparece` = 'sim' GROUP BY `pokemon_wild`.`real_id`");
	if ($result->num_rows != 0) {
		$info = $result->fetch_assoc();
		$levelensql = DB::exQuery("SELECT * FROM `levelen` WHERE `wild_id`='" . (int)$_GET['pokemon'] . "' ORDER BY `level` ASC");
		$aantallevelen = $levelensql->num_rows;
  		$contcem = DB::exQuery("SELECT id FROM pokemon_speler WHERE wild_id = '".(int)$_GET['pokemon']."' and level='100'")->num_rows;
		if ($info['naam'] == "") {
			echo "Escolha um pokémon.";
			exit();
		}

		$abilities = explode(',', $info['ability']);
		$abilities_2 = '';

		$info['naam'] = computer_naam($info['naam']);

		$zeldzaam = DB::exQuery("SELECT `nome` FROM `zeldzaamheid` WHERE `id`='".$info['zeldzaamheid']."'")->fetch_assoc()['nome'];

		if ($info['gebied'] == "Gras")				$info['gebied'] = "Grama";
		else if ($info['gebied'] == "Lavagrot")		$info['gebied'] = "Lava";
		else if ($info['gebied'] == "Water")			$info['gebied'] = "Agua";
		else if ($info['gebied'] == "Grot")			$info['gebied'] = "Gruta";
		else if ($info['gebied'] == "Strand")			$info['gebied'] = "Praia";
		else if ($info['gebied'] == "Vechtschool")	$info['gebied'] = "Dojo";
		else if ($info['gebied'] == "Spookhuis")		$info['gebied'] = "Torre";

		if (empty($info['gebied'])) $gebied = $txt['not_a_favorite_place'];
  		else if ($info['gebied'] == "Mega" OR $info['gebied'] == "Primal" OR $info['wereld'] == "Promo") $gebied = $txt['not_a_favorite_place'];
  		else $gebied = sprintf($txt['is_his_favorite_place'], $info['gebied']);

		$info['wereld'] = empty($info['wereld']) ? $txt['unknow'] : $info['wereld'];
  
		$info['type1'] = strtolower($info['type1']);
		$info['type2'] = strtolower($info['type2']);

		foreach ($abilities as $abil) {
			$sql = DB::exQuery("SELECT * FROM `abilities` WHERE id='$abil'")->fetch_assoc();
			$abilities_2 .= '<a href="./information&category=ability-info&attack='.$sql['name'].'">'.$sql['name'].'</a>, ';
		}

		if (empty($info['type2']))	$info['type'] = '<table><tr><td><div class="type-icon type-'.$info['type1'].'">'.$info['type1'].'</div></td></tr></table>';
		else	$info['type'] = '<table><tr><td><div class="type-icon type-'.$info['type1'].'">'.$info['type1'].'</div></td><td> <div class="type-icon type-'.$info['type2'].'">'.$info['type2'].'</div></td></tr></table>';

		$eredmeny = ceil($info['vangbaarheid'] / (255 / 100));
		echo '<table width="100%" celspacing="0" celpadding="0" border="0"><tr>
			<td valign="top" width="365">
				<div class="box-content"><table class="general" style="width:365px;">
					<thead><tr><th colspan="2" align="left"># '.$info['real_id'].' - ' . $info['naam'] . '<span style="float: right;">(' . $zeldzaam . ')</span></th></tr></thead>
					<tbody>
						<tr>
							<td width="182.5px" align="center" style="padding: 0; margin: 0;">
								<div style="width: 100%; height: 130px; background: url(\'' . $static_url . '/images/pokemon/'.$info['wild_id'].'.gif\') center center no-repeat;"></div>
								<!--img src="' . $static_url . '/images/pokemon/'.$info['wild_id'].'.gif" title="'.$info['naam'].'" class="pokemon" /-->
							</td>
							<td width="182.5px" align="center" style="padding: 0; margin: 0;">
								<div style="width: 100%; height: 130px; background: url(\'' . $static_url . '/images/shiny/'.$info['wild_id'].'.gif\') center center no-repeat;"></div>
								<!--img src="' . $static_url . '/images/shiny/'.$info['wild_id'].'.gif" title="'.$info['naam'].'" class="pokemon" /-->
							</td>
						</tr>';
						
						if ($info['wereld'] != "Mega" AND $info['wereld'] != "Primal" AND $info['wereld'] != "Promo") {
						echo '<tr>
							<td><b>&raquo; ' . $txt['lives_in'] . ':</b></td>
							<td align="center">' . $info['wereld'] . '</td>
						</tr>';
						}
						
$pgtop3 = DB::exQuery("SELECT `pokemon_speler`.*, pokemon_wild.wild_id, pokemon_wild.naam, pokemon_wild.type1, pokemon_wild.type2, gebruikers.username, SUM(`attack` + `defence` + `speed` + `spc.attack` + `spc.defence`) AS strongestpokemon FROM pokemon_speler INNER JOIN pokemon_wild ON pokemon_speler.wild_id = pokemon_wild.wild_id INNER JOIN gebruikers ON pokemon_speler.user_id = gebruikers.user_id WHERE gebruikers.banned = 'N' AND admin = '0' AND pokemon_wild.wild_id = '".(int)$_GET['pokemon']."' GROUP BY pokemon_speler.id ORDER BY strongestpokemon DESC, pokemon_speler.id ASC LIMIT 3");
						
$i = 1;
$top3 = '';

DB::exQuery("UPDATE `pokemon_speler` SET `top3`='' WHERE `wild_id`='".(int)$_GET['pokemon']."' AND `top3`!=''");
while($pgtop3x = $pgtop3->fetch_assoc()) {
	$pokemon_profile = pokemonei($pgtop3x, $txt);
	$popup = pokemon_popup($pokemon_profile, $txt);
	$pokemon_profile['naam'] = pokemon_naam($pgtop3x['naam'],$pgtop3x['roepnaam'],$pgtop3x['icon']);
	
	// ATUALIZA TOP 3 POKES
	DB::exQuery("UPDATE `pokemon_speler` SET `top3`='$i' WHERE `id`='".$pgtop3x['id']."' AND `wild_id`='".(int)$_GET['pokemon']."' AND `top3`=''");
	// ATUALIZA TOP 3 POKES
	
	$pokemon_profile['powertotal'] = $pokemon_profile['attack'] + $pokemon_profile['defence'] + $pokemon_profile['speed'] + $pokemon_profile['spc.attack'] + $pokemon_profile['spc.defence'];
	
	/*if ($i == 1) $top3 .= '<tr><td align="center">';
	if ($i == 2) $top3 .= '<tr><td align="center">';*/
	if ($i == 1) $rnk = "plaatsnummereen";
	else if ($i == 2) $rnk = "plaatsnummertwee";
	else if ($i == 3) $rnk = "plaatsnummerdrie";
	$top3 .=  '
	<td width="33.33%" align="center">
	<div class="tip_top-middle" title="'.$popup.'" style="width: 100%; height: 130px; background: url(\'' . $static_url . '/'.$pokemon_profile['link'].'\') center center no-repeat;"></div>
	Poder Total: <b>'. highamount($pokemon_profile['powertotal']).'</b> 
	<br>
	<img src="'.$static_url.'/images/icons/'.$rnk.'.png"> <a href="./profile&amp;player=' . $pokemon_profile['username'] . '">'.GetColorName($pokemon_profile['user_id']).'</a>
	</td>
	';
	
	
	
	/*if ($i == 1) $top3 .= '</td></tr>';
	if ($i == 3) $top3 .= '</td></tr>';*/
	
	$i++;

	
}
if ($i == 1) $top3 = '<td><div class="red">Não há Pokémons dessa espécie!</div></td>';
	$evolui_de = '-';
	$evoluide = DB::exQuery("SELECT * FROM levelen where nieuw_id = '".(int)$_GET['pokemon']."' and wat='evo' limit 1");
	if ($evoluide->num_rows != 0) {
		$evoluidex = $evoluide->fetch_assoc();
		if ($evoluidex['level'] <= 100) {
			$dequem = DB::exQuery("SELECT wild_id, naam FROM pokemon_wild where wild_id = '".$evoluidex['wild_id']."' limit 1")->fetch_assoc();
			$evolui_de = '<a href="javascript:void(0)" onClick="show_info(\''.$dequem['wild_id'].'\')"><img src="' . $static_url . '/images/pokemon/icon/'.$dequem['wild_id'].'.gif" class="pokemon_mini" title="' . $dequem['naam'] . '" style="cursor: pointer;" /> '.$dequem['naam'].'</a>';
		} else if (!empty($evoluidex['stone'])) {
			$dequem = DB::exQuery("SELECT wild_id, naam FROM pokemon_wild where wild_id = '".$evoluidex['wild_id']."' limit 1")->fetch_assoc();
			$evolui_de = '<a href="javascript:void(0)" onClick="show_info(\''.$dequem['wild_id'].'\')"><img src="' . $static_url . '/images/pokemon/icon/'.$dequem['wild_id'].'.gif" class="pokemon_mini" title="' . $dequem['naam'] . '" style="cursor: pointer;" /> '.$dequem['naam'].'</a> + <img src="' . $static_url . '/images/items/'.$evoluidex['stone'].'.png" title="' . $evoluidex['stone'] . '"/>';
		} else if ($evoluidex['trade'] == 1) {
			$dequem = DB::exQuery("SELECT wild_id, naam FROM pokemon_wild where wild_id = '".$evoluidex['wild_id']."' limit 1")->fetch_assoc();
			$evolui_de = '<a href="javascript:void(0)" onClick="show_info(\''.$dequem['wild_id'].'\')"><img src="' . $static_url . '/images/pokemon/icon/'.$dequem['wild_id'].'.gif" class="pokemon_mini" title="' . $dequem['naam'] . '" style="cursor: pointer;" /> '.$dequem['naam'].'</a> + <img src="' . $static_url . '/images/icons/trade.png" title="' . $txt['trade'] . '"/>';
		}
	}
	
	$egg = $info['egg'];
	$egg_rar = array('Comum', 'Incomum', 'Raro', 'Lendário/Inicial');
	if ($info['zeldzaamheid'] >= 5) $info['zeldzaamheid'] = 4;
	
	if ($egg == '1' && $info['evolutie'] == '1') {
	    $egg = '<b>Sim ['.$egg_rar[($info['zeldzaamheid']-1)].']</b>';
	} else {
	    $egg = '<b>Não</b>';
	}
	

						echo '
						<tr>
							<td><b>&raquo; ' . $txt['type'] . ':</b></td>
							<td align="center">' . $info['type'] . '</td>
						</tr>
												<tr>
							<td><b>&raquo; ' . $txt['evolui_de'] . ':</b></td>
							<td align="center">' . $evolui_de. '</td>
						</tr>
						<tr>
							<td><b>&raquo; ' . $txt['favorite_place'] . ':</b></td>
							<td align="center">' . $gebied . '</td>
						</tr>
						<tr>
							<td><b>&raquo; Possíveis Habilidade(s):</b></td>
							<td align="center">' . substr($abilities_2, 0, -2) . '</td>
						</tr>
						<tr>
							<td><b>&raquo; ' . $txt['capture_chance'] . ':</b></td>
							<td align="center">' . $eredmeny . '%</td>
						</tr>
						<tr>
							<td><b>&raquo; Pode vir no PokéMart?</b></td>
							<td align="center">'.$egg.'</td>
						</tr>
						<tr><td align="center" colspan="2">' . sprintf($txt['how_much'], highamount($info['hoeveelingame']), $info['naam']) . '   <br>Existem <b>'.highamount($contcem).'</b> no level 100. </td></tr>
					</tbody>
				</table></div>
				<div class="box-content" style="margin-top: 3px"><table class="general" style="width:365px;">
					<thead>
						<tr><th colspan="6">Base stats</th></tr>
						<tr>
							<th style="width: 60px;" align="center"><img src="' . $static_url . '/images/icons/stats/stat_hp.png" title="HP" /></th>
							<th style="width: 61px;" align="center"><img src="' . $static_url . '/images/icons/stats/stat_at.png" title="Attack" /></th>
							<th style="width: 61px;" align="center"><img src="' . $static_url . '/images/icons/stats/stat_de.png" title="Defense" /></th>
							<th style="width: 61px;" align="center"><img src="' . $static_url . '/images/icons/stats/stat_sa.png" title="Special Attack" /></th>
							<th style="width: 61px;" align="center"><img src="' . $static_url . '/images/icons/stats/stat_sd.png" title="Special Defense" /></th>
							<th style="width: 61px;" align="center"><img src="' . $static_url . '/images/icons/stats/stat_sp.png" title="Speed" /></th>
						</tr>
					</thead>
					<tbody><tr>
						<td align="center">' . $info['hp_base'] . '</td>
						<td align="center">' . $info['attack_base'] . '</td>
						<td align="center">' . $info['defence_base'] . '</td>
						<td align="center">' . $info['spc.attack_base'] . '</td>
						<td align="center">' . $info['spc.defence_base'] . '</td>
						<td align="center">' . $info['speed_base'] . '</td>
					</tr></tbody>
				</table></div>
				<div class="box-content" style="margin-top: 3px"><table class="general" style="width:365px;">
					<thead>
						<tr><th colspan="4">Ataques</th></tr>
						<tr>
							<th width="25%">Ataque 1</th>
							<th width="25%">Ataque 2</th>
							<th width="25%">Ataque 3</th>
							<th width="25%">Ataque 4</th>
						</tr>
					</thead>
					<tbody><tr>
						<td align="center">' . (empty($info['aanval_1']) ? '--' : $info['aanval_1']) . '</td>
						<td align="center">' . (empty($info['aanval_2']) ? '--' : $info['aanval_2']) . '</td>
						<td align="center">' . (empty($info['aanval_3']) ? '--' : $info['aanval_3']) . '</td>
						<td align="center">' . (empty($info['aanval_4']) ? '--' : $info['aanval_4']) . '</td>
					</tr></tbody>
				</table></div>
				<div class="box-content" style="margin-top: 3px"><table class="general" style="width:365px;">
					<thead>
						<tr><th colspan="4">TOP 3 Melhores da Espécie</th></tr>
					</thead>
					<tbody><tr>
				
					'.$top3.'
						
			
					</tr></tbody>
				</table></div>
			</td>
			<td valign="top" width="365" style="float: right;">
				<div class="box-content" style="max-height: 350px; overflow-y: auto"><table class="general" style="width:365px;">
					<thead>
						<tr><th colspan="2" style="text-align: center;">'.$txt['attack&evolution'].'</th></tr>';
					if ($aantallevelen == 0)	echo '<tbody><tr><td colspan="2"><div class="red">'.$txt['no_attack_or_evolve'].'</div></td></tr>';
					else if ($aantallevelen > 0) {
						echo '<tr>
							<th width="50">' . $txt['level'] . '</th>
							<th>' . $txt['evolution'] . '</th>
						</tr></thead><tbody>';
						while($levelen = $levelensql->fetch_assoc()) {
							if ($levelen['wat'] == 'att') {
								echo '<tr>
									<td align="center">'.$levelen['level'].'</td>
									<td>'.$levelen['aanval'].'</td>
								</tr>';
							} else	$evolutie = DB::exQuery("SELECT `wild_id`,`naam` FROM `pokemon_wild` WHERE `wild_id`='".$levelen['nieuw_id']."' LIMIT 1")->fetch_assoc();
							
							$method = '';
							if ($levelen['wat'] == 'evo') {
								echo '<tr>';
								if ($levelen['level'] <= 100) {
									$method = $levelen['level'];
								} else if (!empty($levelen['stone'])) {
									$method = '<img src="' . $static_url . '/images/items/'.$levelen['stone'].'.png" title="' . $levelen['stone'] . '"/>';
								} else if ($levelen['trade'] == 1) {
									$method = '<img src="' . $static_url . '/images/icons/trade.png" title="' . $txt['trade'] . '"/>';
								}

								if (!empty($levelen['time'])) {
									if ($levelen['time'] == 'day') {
										$method .= ' + <img src="' . $static_url . '/images/icons/'.$levelen['time'].'.png" title="' . ucfirst($levelen['time']) . '" width="18"/>';
									} else {
										$method .= ' + <img src="' . $static_url . '/images/icons/'.$levelen['time'].'.png" title="' . ucfirst($levelen['time']) . '" width="16"/>';
									}
								}

								if (!empty($levelen['region'])) {
									$method .= ' + '.$levelen['region'];
								}

								if ($levelen['nieuw_id'] == 106) {
									$method .= ' + Attack > Defense';
								} else if ($levelen['nieuw_id'] == 107) {
									$method .= ' + Attack < Defense';
								} else if ($levelen['nieuw_id'] == 237) {
									$method .= ' + Attack = Defense';
								}
 
								if (!empty($levelen['item']) && $levelen['trade'] == 1) {
									$method .= ' + <img src="' . $static_url . '/images/items/'.$levelen['item'].'.png" title="Segurando o item ' . $levelen['item'] . '" width="16"/>';
								}

								echo '<td align="center">'.$method.'</td>';
								echo '<td><img src="' . $static_url . '/images/pokemon/icon/'.$evolutie['wild_id'].'.gif" class="pokemon_mini" onClick="show_info(\''.$evolutie['wild_id'].'\')" title="' . $evolutie['naam'] . '" style="cursor: pointer;" /></td>';
								echo '</tr>';
							}
						}
					}
				echo '</tbody></table></div>';
				echo '<div class="box-content" style="margin-top: 3px"><table class="general" style="width:365px;">
					<thead>
						<tr><th colspan="6">Ganho de EVs</th></tr>
						<tr>
							<th style="width: 60px;" align="center"><img src="' . $static_url . '/images/icons/stats/stat_hp.png" title="EV HP" /></th>
							<th style="width: 61px;" align="center"><img src="' . $static_url . '/images/icons/stats/stat_at.png" title="EV Attack" /></th>
							<th style="width: 61px;" align="center"><img src="' . $static_url . '/images/icons/stats/stat_de.png" title="EV Defense" /></th>
							<th style="width: 61px;" align="center"><img src="' . $static_url . '/images/icons/stats/stat_sa.png" title="EV Special Attack" /></th>
							<th style="width: 61px;" align="center"><img src="' . $static_url . '/images/icons/stats/stat_sd.png" title="EV Special Defense" /></th>
							<th style="width: 61px;" align="center"><img src="' . $static_url . '/images/icons/stats/stat_sp.png" title="EV Speed" /></th>
						</tr>
					</thead>
					<tbody><tr>
						<td align="center">' . $info['effort_hp'] . '</td>
						<td align="center">' . $info['effort_attack'] . '</td>
						<td align="center">' . $info['effort_defence'] . '</td>
						<td align="center">' . $info['effort_spc.attack'] . '</td>
						<td align="center">' . $info['effort_spc.defence'] . '</td>
						<td align="center">' . $info['effort_speed'] . '</td>
					</tr></tbody>
				</table></div>';
				$getTmHm = DB::exQuery("SELECT `tmhm`.*,`tmhm_relacionados`.`relacionados` FROM `tmhm` LEFT JOIN `tmhm_relacionados` ON `tmhm`.`naam`=`tmhm_relacionados`.`naam` ORDER BY `tmhm`.`naam` ASC");
				if ($getTmHm->num_rows != 0) {
					echo '<div class="box-content" style="margin-top: 3px;max-height: 300px; overflow-y: auto"><table class="general" width="100%">
						<thead><tr><th>TM / HM</th></tr></thead>
						<tbody><tr><td align="center">';
					while($tmhm = $getTmHm->fetch_assoc()) {
						$wilds_id = explode(',', $tmhm['relacionados']);
						if (in_array($info['wild_id'], $wilds_id)) {
							echo '<div style="border-radius: 4px;display: inline-block;margin: 3px;border: 1px solid #577599;width: 50px;box-shadow: 0 2px 0 0 #0f1a2a;overflow: hidden;">
								<table class="general" width="100%">
									<thead>
										<tr></tr>
										<tr><th style="border-radius: 5px 5px 0 0;">' . $tmhm['naam'] . '</th></tr>
									</thead>
									<tbody><tr><td style="border-radius: 0 0 5px 5px;" align="center" title="' . $tmhm['omschrijving'] . '"><img src="' . $static_url . '/images/items/Attack_' . $tmhm['type1'] . '.png" class="elipse" style="margin-right:0;"/></td></tr></tbody>
								</table>
							</div>';
						}
					}
					echo '</td></tr></tbody>
					</table></div>';
				}
				
				?>
				<style>
.tipo{
	display: inline-block;
	width: 32px;
    height: 32px;
    line-height: 32px;
	border: 1px solid #aaa;
    border-radius: 2px;
    color: #fff;
    font-size: 10px;
    text-align: center;
    text-shadow: 1px 1px 1px #333;
	text-transform: uppercase;
	border-radius: 4px;
    display: inline-block;
    margin: 1px;
    border: 1px solid #577599;
    box-shadow: 0 2px 0 0 #0f1a2a;
    overflow: hidden;
}
.typee{
	display: inline-block;
	width: 32px;
    height: 32px;
    line-height: 32px;
	border: 1px solid #aaa;
    border-radius: 2px;
    color: #fff;
    font-size: 10px;
    text-align: center;
	text-transform: uppercase;
	border-radius: 4px;
    display: inline-block;
    margin: 1px;
    border: 1px solid #577599;
    box-shadow: 0 2px 0 0 #0f1a2a;
    overflow: hidden;
}
.Normalt{
    background: #8a8a59;
    background-image: -webkit-linear-gradient(#a8a878,#8a8a59);
    background-image: linear-gradient(#a8a878,#8a8a59);
    border-color: #79794e;
}
.Firet{
	background: #f08030;
    background-image: -webkit-linear-gradient(#f08030,#dd6610);
    background-image: linear-gradient(#f08030,#dd6610);
    border-color: #b4530d;
}
.Watert{background: #6890f0;
    background-image: -webkit-linear-gradient(#6890f0,#386ceb);
    background-image: linear-gradient(#6890f0,#386ceb);
    border-color: #1753e3;
}
.Electrict{    background: #f8d030;
    background-image: -webkit-linear-gradient(#f8d030,#f0c108);
    background-image: linear-gradient(#f8d030,#f0c108);
    border-color: #c19b07;}
.Grasst{background: #78c850;
    background-image: -webkit-linear-gradient(#78c850,#5ca935);
    background-image: linear-gradient(#78c850,#5ca935);
    border-color: #4a892b;}
.Icet{    background: #98d8d8;
    background-image: -webkit-linear-gradient(#98d8d8,#69c6c6);
    background-image: linear-gradient(#98d8d8,#69c6c6);
    border-color: #45b6b6;}
.Fightingt{    background: #c03028;
    background-image: -webkit-linear-gradient(#c03028,#9d2721);
    background-image: linear-gradient(#c03028,#9d2721);
    border-color: #82211b;}
.Poisont{    background: #a040a0;
    background-image: -webkit-linear-gradient(#a040a0,#803380);
    background-image: linear-gradient(#a040a0,#803380);
    border-color: #662966;}
.Groundt{    background: #e0c068;
    background-image: -webkit-linear-gradient(#e0c068,#d4a82f);
    background-image: linear-gradient(#e0c068,#d4a82f);
    border-color: #aa8623;}
.Flyingt{background:#a890f0;
	background-image:-webkit-linear-gradient(#a890f0,#9180c4);
	background-image:linear-gradient(#a890f0,#9180c4);
	border-color:#7762b6}
.eff1{padding: 0;
    border: 1px solid #ececec;
	background:#fff;
	color:black;}
.eff2{padding: 0;
    border: 1px solid #ececec;
	background:#4e9a06;}
.eff0{padding: 0;
    border: 1px solid #ececec;
	background:#2e3436;}
.eff4{padding: 0;
    border: 1px solid #ececec;
	background:#73d216;}
.eff12{padding: 0;
    border: 1px solid #ececec;
	background:#a40000;}
.eff14{padding: 0;
    border: 1px solid #ececec;
	background:#7c0000;}
.Psychict{    background: #f85888;
    background-image: -webkit-linear-gradient(#f85888,#f61c5d);
    background-image: linear-gradient(#f85888,#f61c5d);
    border-color: #d60945;}
.Bugt{background: #a8b820;
    background-image: -webkit-linear-gradient(#a8b820,#8d9a1b);
    background-image: linear-gradient(#a8b820,#8d9a1b);
    border-color: #616b13;}
.Ghostt{    background: #705898;
    background-image: -webkit-linear-gradient(#705898,#554374);
    background-image: linear-gradient(#705898,#554374);
    border-color: #413359;}
.Rockt{background: #b8a038;
    background-image: -webkit-linear-gradient(#b8a038,#93802d);
    background-image: linear-gradient(#b8a038,#93802d);
    border-color: #746523;}
.Dragont{background:#7038f8;
background-image:-webkit-linear-gradient(#7038f8,#4c08ef);
background-image:linear-gradient(#7038f8,#4c08ef);
border-color:#3d07c0;}
.Darkt{    background: #705848;
    background-image: -webkit-linear-gradient(#705848,#513f34);
    background-image: linear-gradient(#705848,#513f34);
    border-color: #362a23;}
.Stellt{background: #b8b8d0;
    background-image: -webkit-linear-gradient(#b8b8d0,#9797ba);
    background-image: linear-gradient(#b8b8d0,#9797ba);
    border-color: #7a7aa7;}
.Fairyt{background:#e898e8;
background-image:-webkit-linear-gradient(#e898e8,#de6ede);
background-image:linear-gradient(#e898e8,#de6ede);
border-color:#d547d5}
</style>
<?php
class Retornoo
{
    public $vlr;
    public $vlr2;
    public $vlr3;
}
#Vantagens (Water sobre Fire)
function attack_to_defender_advantage($soort,$defender) {
  $ret = new Retornoo();
  $voordeel2 = DB::exQuery("SELECT `krachtiger` FROM `voordeel` WHERE `aanval`='".$soort."' AND `verdediger`='".ucfirst($defender['type1'])."'")->fetch_assoc();
	$voordeel3 = DB::exQuery("SELECT `krachtiger` FROM `voordeel` WHERE `aanval`='".$soort."' AND `verdediger`='".ucfirst($defender['type2'])."'")->fetch_assoc();

	if (empty($voordeel2)) $voordeel2['krachtiger'] = 1;

	if (empty($voordeel3)) $voordeel3['krachtiger'] = 1;
	
	$voordeel = $voordeel2['krachtiger'] * $voordeel3['krachtiger'];

	$ret->vlr = $voordeel;
  
  if ($ret->vlr == 0) {$ret->vlr2 = "eff0"; $ret->vlr3 = "Sem efeito."; }
  else if ($ret->vlr == 1) {$ret->vlr2 = "eff1"; $ret->vlr3 = "Eficácia normal."; }
  else if ($ret->vlr == 1.38) {$ret->vlr2 = "eff1"; $ret->vlr3 = "Eficácia normal."; }
  else if ($ret->vlr == 2.00) {$ret->vlr2 = "eff2"; $ret->vlr3 = "Super efetivo."; }
  else if ($ret->vlr == 4.00) {$ret->vlr2 = "eff4"; $ret->vlr3 = "Super efetivo."; }
  else if ($ret->vlr == 2.76) {$ret->vlr2 = "eff2"; $ret->vlr3 = "Super efetivo."; }
  else if ($ret->vlr == 0.50) {$ret->vlr2 = "eff12"; $ret->vlr3 = "Não muito efetivo."; }
  else if ($ret->vlr == 0.25) {$ret->vlr2 = "eff14"; $ret->vlr3 = "Não muito efetivo."; }
  
  return $ret;
}
if ($info['type2'] == "") $nm = ucfirst($info['type1']);
else $nm = ''.ucfirst($info['type1']).'/'.ucfirst($info['type2']).'';

				
					echo '<div class="box-content" style="margin-top: 3px"><table class="general" width="100%">
						<thead><tr><th>Vantagens e desvantagens</th></tr></thead>
						<tbody><tr><td align="center">';
					?>
					

<div style="width:355px;">
<table>
<tr>
<td>
<div class="tipo Normalt">NOR</div>
</td>
<td>
<div class="tipo Firet">FIR</div>
</td>
<td>
<div class="tipo Watert">WAT</div>
</td>
<td>
<div class="tipo Electrict">ELE</div>
</td>
<td>
<div class="tipo Grasst">GRA</div>
</td>
<td>
<div class="tipo Icet">ICE</div>
</td>
<td>
<div class="tipo Fightingt">FIG</div>
</td>
<td>
<div class="tipo Poisont">POI</div>
</td>
<td>
<div class="tipo Groundt">GRO</div>
</td>
</tr>
<tr>
<td>
<?php $inff = attack_to_defender_advantage('Normal',$info); ?>
<div class="typee <?=$inff->vlr2?>" title="Ataque Normal → <?php echo $nm; ?> = <?=$inff->vlr3?>"><?=$inff->vlr?>x</div>
</td>
<td>
<?php $inff2 = attack_to_defender_advantage('Fire',$info); ?>
<div class="typee <?=$inff2->vlr2?>" title="Ataque Fire → <?php echo $nm; ?> = <?=$inff2->vlr3?>"><?=$inff2->vlr?>x</div>
</td>
<td>
<?php $inff3 = attack_to_defender_advantage('Water',$info); ?>
<div class="typee <?=$inff3->vlr2?>" title="Ataque Water → <?php echo $nm; ?> = <?=$inff3->vlr3?>"><?=$inff3->vlr?>x</div>
</td>
<td>
<?php $inff4 = attack_to_defender_advantage('Electric',$info); ?>
<div class="typee <?=$inff4->vlr2?>" title="Ataque Electric → <?php echo $nm; ?> = <?=$inff4->vlr3?>"><?=$inff4->vlr?>x</div>
</td>
<td>
<?php $inff5 = attack_to_defender_advantage('Grass',$info); ?>
<div class="typee <?=$inff5->vlr2?>" title="Ataque Grass → <?php echo $nm; ?> = <?=$inff5->vlr3?>"><?=$inff5->vlr?>x</div>
</td>
<td>
<?php $inff6 = attack_to_defender_advantage('Ice',$info); ?>
<div class="typee <?=$inff6->vlr2?>" title="Ataque Ice → <?php echo $nm; ?> = <?=$inff6->vlr3?>"><?=$inff6->vlr?>x</div>
</td>
<td>
<?php $inff7 = attack_to_defender_advantage('Fighting',$info); ?>
<div class="typee <?=$inff7->vlr2?>" title="Ataque Fighting → <?php echo $nm; ?> = <?=$inff7->vlr3?>"><?=$inff7->vlr?>x</div>
</td>
<td>
<?php $inff8 = attack_to_defender_advantage('Poison',$info); ?>
<div class="typee <?=$inff8->vlr2?>" title="Ataque Poison → <?php echo $nm; ?> = <?=$inff8->vlr3?>"><?=$inff8->vlr?>x</div>
</td>
<td>
<?php $inff9 = attack_to_defender_advantage('Ground',$info); ?>
<div class="typee <?=$inff9->vlr2?>" title="Ataque Ground → <?php echo $nm; ?> = Sem efeito."><?=$inff9->vlr?>x</div>
</td>
<td>
</tr>
<tr style="height:10px;"></tr>



<tr>
<td>
<div class="tipo Psychict">PSY</div>
</td>
<td>
<div class="tipo Bugt">BUG</div>
</td>
<td>
<div class="tipo Rockt">ROC</div>
</td>
<td>
<div class="tipo Ghostt">GHO</div>
</td>
<td>
<div class="tipo Dragont">DRA</div>
</td>
<td>
<div class="tipo Darkt">DAR</div>
</td>
<td>
<div class="tipo Stellt">STE</div>
</td>
<td>
<div class="tipo Fairyt">FAI</div>
</td>
<td>
<div class="tipo Flyingt">FLY</div>
</td>
</tr>

<tr>
<td>
<?php $inff11 = attack_to_defender_advantage('Psychic',$info); ?>
<div class="typee <?=$inff11->vlr2?>" title="Ataque Psychic → <?php echo $nm; ?> = <?=$inff11->vlr3?>"><?=$inff11->vlr?>x</div>
</td>
<td>
<?php $inff12 = attack_to_defender_advantage('Bug',$info); ?>
<div class="typee <?=$inff12->vlr2?>" title="Ataque Bug → <?php echo $nm; ?> = <?=$inff12->vlr3?>"><?=$inff12->vlr?>x</div>
</td>
<td>
<?php $inff13 = attack_to_defender_advantage('Rock',$info); ?>
<div class="typee <?=$inff13->vlr2?>" title="Ataque Rock → <?php echo $nm; ?> = <?=$inff13->vlr3?>"><?=$inff13->vlr?>x</div>
</td>
<td>
<?php $inff14 = attack_to_defender_advantage('Ghost',$info); ?>
<div class="typee <?=$inff14->vlr2?>" title="Ataque Ghost → <?php echo $nm; ?> = <?=$inff14->vlr3?>"><?=$inff14->vlr?>x</div>
</td>
<td>
<?php $inff15 = attack_to_defender_advantage('Dragon',$info); ?>
<div class="typee <?=$inff15->vlr2?>" title="Ataque Dragon → <?php echo $nm; ?> = <?=$inff15->vlr3?>"><?=$inff15->vlr?>x</div>
</td>
<td>
<?php $inff16 = attack_to_defender_advantage('Dark',$info); ?>
<div class="typee <?=$inff16->vlr2?>" title="Ataque Dark → <?php echo $nm; ?> = <?=$inff16->vlr3?>"><?=$inff16->vlr?>x</div>
</td>
<td>
<?php $inff17 = attack_to_defender_advantage('Stell',$info); ?>
<div class="typee <?=$inff17->vlr2?>" title="Ataque Stell → <?php echo $nm; ?> = <?=$inff17->vlr3?>"><?=$inff17->vlr?>x</div>
</td>
<td>
<?php $inff18 = attack_to_defender_advantage('Fairy',$info); ?>
<div class="typee <?=$inff18->vlr2?>" title="Ataque Fairy → <?php echo $nm; ?> = <?=$inff18->vlr3?>"><?=$inff18->vlr?>x</div>
</td>
<td>
<?php $inff10 = attack_to_defender_advantage('Flying',$info); ?>
<div class="typee <?=$inff10->vlr2?>" title="Ataque Flying → <?php echo $nm; ?> = <?=$inff10->vlr3?>"><?=$inff10->vlr?>x</div>
</td>
</tr>

</table>
</div>


<?php					
					echo '</td></tr></tbody>
					</table></div>';				
				echo '</td>';
		echo '</tr></table>';
		echo '</div></td>';
	}
}
?>

<script>
Tipped.create("*.tip_top-middle", {
        hook: {
            target: 'topmiddle',
            tooltip: 'bottommiddle'
        },
        radius: 3
    });
</script>