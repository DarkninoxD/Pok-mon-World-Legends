<?php
#Script laden zodat je nooit pagina buiten de index om kan laden
require_once("app/includes/resources/security.php");

$title = 'Aprendendo novos ataques';
echo addNPCBox(11, $title, $txt['title_txt']);

#Gegevens laden van de des betreffende pokemon
$pokemoninfo  = DB::exQuery("SELECT pokemon_wild.wild_id, pokemon_wild.naam, pokemon_speler.id, pokemon_speler.aanval_1, pokemon_speler.aanval_2, pokemon_speler.aanval_3, pokemon_speler.aanval_4 FROM pokemon_wild INNER JOIN pokemon_speler ON pokemon_wild.wild_id = pokemon_speler.wild_id WHERE `id`='".$nieuweaanval['pokemonid']."'")->fetch_assoc();
$finish = false;

if (isset($_POST['annuleer'])) {
	//echo "<div class='blue'>" . sprintf($txt['annuleer'], $nieuweaanval['aanvalnaam']) . "</div>";
	$finish = true;
}
if (isset($_POST['attack'])) {
	//echo "<div class='green'>" . sprintf($txt['attack'], $nieuweaanval['aanvalnaam'], $_POST['attack']) . "</div>";

	#Nieuwe aanval opslaan
	DB::exQuery("UPDATE `pokemon_speler` SET `".$_POST['welke']."`='".$nieuweaanval['aanvalnaam']."' WHERE `id`='".$nieuweaanval['pokemonid']."'");
	$pokemoninfo[$_POST['welke']] = $nieuweaanval['aanvalnaam'];
	$finish = true;
}

if ($finish) {
	$current = array_pop($_SESSION['used']);      

	$count = 0;
	$sql = DB::exQuery("SELECT pokemon_wild.naam, pokemon_speler.id, pokemon_speler.wild_id, pokemon_speler.roepnaam, pokemon_speler.level, pokemon_speler.trade, pokemon_speler.expnodig, pokemon_speler.exp FROM pokemon_wild INNER JOIN pokemon_speler ON pokemon_wild.wild_id = pokemon_speler.wild_id WHERE pokemon_speler.id='".$current."'");
	while($select = $sql->fetch_assoc()) {
		#Change name for male and female
		$select['naam_goed'] = pokemon_naam($select['naam'],$select['roepnaam']);
		if ($select['level'] < 100) {
			#Gegevens laden van pokemon die leven groeit uit levelen tabel
			$levelensql = DB::exQuery("SELECT `id`, `level`, `trade`, `wild_id`, `wat`, `nieuw_id`, `aanval` FROM `levelen` WHERE `wild_id`='".$select['wild_id']."' AND `level`>'".$_SESSION['lvl_old']."' AND `level`<='".$select['level']."' AND aanval!='".$nieuweaanval['aanvalnaam']."' ORDER BY id ASC");
			#Voor elke actie kijken als het klopt.
			while($levelen = $levelensql->fetch_assoc()) {
				#als de actie een aanval leren is
				if ($levelen['wat'] == "att") {
					#Kent de pokemon deze aanval al
					if ($select['aanval_1'] != $levelen['aanval'] && $select['aanval_2'] != $levelen['aanval'] && $select['aanval_3'] != $levelen['aanval'] && $select['aanval_4'] != $levelen['aanval']) {
						unset($_SESSION['evolueren']);
						if ($levelen['level'] > $select['level'])	break;
						$_SESSION['aanvalnieuw'] = base64_encode($select['id']."/".$levelen['aanval']);
						++$count;
						$_SESSION['lvl_old'] = $levelen['level'];
						array_push($_SESSION['used'], $select['id']);
						break;
					}
				} else if ($levelen['wat'] == "evo") {	#Gaat de pokemon evolueren
					#Is het level groter of gelijk aan de level die benodigd is? Naar andere pagina gaan
					if ($levelen['level'] <= $select['level'] || ($levelen['trade'] == 1 && $select['trade'] == "1.5")) {
						unset($_SESSION['aanvalnieuw']);
						if ($levelen['level'] > $select['level'])	break;
						$_SESSION['evolueren'] = base64_encode($select['id']."/".$levelen['nieuw_id']);
						++$count;
						$_SESSION['lvl_old'] = $levelen['level'];
						array_push($_SESSION['used'], $select['id']);
						break;
					}
				}
			}
			if ($count != 0)	break;
		}
	}
	if ($count == 0)	unset($_SESSION['aanvalnieuw']);
	exit(header("LOCATION: ./home"));
}
?>
<div class="box-content" style="width: 100%;"><table class="general" width="100%">
	<thead>
		<tr></tr>
		<tr>
			<th colspan="3"><?=sprintf($txt['page_txt'], $pokemoninfo['naam'], $nieuweaanval['aanvalnaam'], $nieuweaanval['aanvalnaam']);?></th>
		</tr>
	</thead>
	<tbody>
		<tr><td style="width: 140px; height: 125px; background: url('<?=$static_url;?>/images/pokemon/<?=$pokemoninfo['wild_id'];?>.gif') center no-repeat;border-right: 1px solid #577599;" rowspan="3"></td></tr>
		<tr>
			<td width="145" align="center"><form method="post">
				<input type="submit" name="attack" value="<?=$pokemoninfo['aanval_1'];?>" class="button" />
				<input type="hidden" name="welke" value="aanval_1" />
			</form></td>
			<td width="145" align="center"><form method="post">
				<input type="submit" name="attack" value="<?=$pokemoninfo['aanval_2'];?>" class="button" />
				<input type="hidden" name="welke" value="aanval_2" />
			</form></td>
		</tr>
		<tr>
			<td align="center"><form method="post">
				<input type="submit" name="attack" value="<?=$pokemoninfo['aanval_3'];?>" class="button" />
				<input type="hidden" name="welke" value="aanval_3" />
			</form></td>
			<td align="center"><form method="post">
				<input type="submit" name="attack" value="<?=$pokemoninfo['aanval_4'];?>" class="button" />
				<input type="hidden" name="welke" value="aanval_4" />
			</form></td>
		</tr>
	</tbody>
	<tfoot><tr><td colspan="3" align="right"><form method="post"><input type="submit" name="annuleer" value="<?=$txt['cancel'];?>" class="button" style="margin: 7px" /></form></td></tr></tfoot>
</table></div>