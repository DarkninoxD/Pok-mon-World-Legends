<?php
//Script laden zodat je nooit pagina buiten de index om kan laden
require_once("app/includes/resources/security.php");

// arrays , refresh button.
$traders_sql = DB::exQuery("SELECT * FROM `traders`");
										 
if ($_POST['submit'] && $gebruiker['restrict'] != '1' && $gebruiker['rank']>=4) {
	$trader = DB::exQuery("SELECT * FROM `traders` WHERE `eigenaar`='{$_POST['check']}' LIMIT 1")->fetch_assoc();
	
	if (DB::exQuery("SELECT pokemon_speler.id FROM pokemon_speler INNER JOIN pokemon_wild ON pokemon_speler.wild_id = pokemon_wild.wild_id WHERE pokemon_wild.naam='".$trader['wil']."' AND pokemon_speler.user_id='".$_SESSION['id']."' AND pokemon_speler.opzak='ja'")->num_rows == 0)
		echo '<div class="red">'.$trader['eigenaar'].': '.$txt['alert_dont_have_1'].' '.$trader['wil'].' '.$txt['alert_dont_have_2'].'</div>';
	else if (empty($trader['naam']))
		echo '<div class="red">'.$trader['eigenaar'].': '.$txt['alert_i_have_1'].' '.$trader['naam'].' '.$txt['alert_i_have_2'].'</div>';
	else {
		if ($trader['eigenaar'] == 'Wayne')
			DB::exQuery("UPDATE gebruikers SET silver = silver+'100' WHERE user_id = '".$_SESSION['id']."' LIMIT 1");
	
		echo '<div class="green">'.$trader['eigenaar'].': '.$txt['success_traders_change'].' '.$trader['naam'].'!</div>';

	$delete_info = DB::exQuery("SELECT `pokemon_speler`.`id`,`pokemon_speler`.`opzak_nummer`,`pokemon_speler`.`level`,`pokemon_wild`.`naam` FROM `pokemon_speler` INNER JOIN `pokemon_wild` ON `pokemon_speler`.`wild_id`=`pokemon_wild`.`wild_id` WHERE `pokemon_wild`.`naam`='{$trader['wil']}' AND `pokemon_speler`.`user_id`='{$_SESSION['id']}' AND `pokemon_speler`.`opzak`='ja' ORDER BY `pokemon_speler`.`opzak_nummer` ASC LIMIT 1")->fetch_assoc();

	//Pokemon die je geruild hebt deleten
	DB::exQuery("DELETE FROM `pokemon_speler` WHERE `id`={$delete_info['id']} LIMIT 1");

	//Add Pokemon
	//Load pokemon basis
	$add_sql = DB::exQuery("SELECT `wild_id`,`naam`,`aanval_1`,`aanval_2`,`aanval_3`,`aanval_4`,`ability` FROM `pokemon_wild` WHERE `naam`='{$trader['naam']}' LIMIT 1")->fetch_assoc();
    
	$add_pokemon['id']		= $add_sql['wild_id'];
	$add_pokemon['pokemon']	= $add_sql['naam'];
	$add_pokemon['aanval1']	= $add_sql['aanval_1'];
	$add_pokemon['aanval2']	= $add_sql['aanval_2'];
	$add_pokemon['aanval3']	= $add_sql['aanval_3'];
	$add_pokemon['aanval4']	= $add_sql['aanval_4'];
	$ability				= explode(',', $add_sql['ability']);
	$klaar	= false;
	$loop	= 0;
	$lastid	= 0;

	//Loop beginnen
	do {
		$teller = 0;
		++$loop;

		//Levelen gegevens laden van de pokemon
		$levelenquery = DB::exQuery("SELECT * FROM `levelen` WHERE `wild_id`='".$add_pokemon['id']."' AND `level`<='".$delete_info['level']."' ORDER BY `id` ASC ");
		while($groei = $levelenquery->fetch_assoc()) {
			//Teller met 1 verhogen
			++$teller;

			if ($delete_info['level'] >= $groei['level']) {
				if ($groei['wat'] == 'att') {
					if (empty($add_pokemon['aanval1']))		$add_pokemon['aanval1'] = $groei['aanval'];
					else if (empty($add_pokemon['aanval2']))	$add_pokemon['aanval2'] = $groei['aanval'];
					else if (empty($add_pokemon['aanval3']))	$add_pokemon['aanval3'] = $groei['aanval'];
					else if (empty($add_pokemon['aanval4']))	$add_pokemon['aanval4'] = $groei['aanval'];
					else {
						if (($add_pokemon['aanval1'] != $groei['aanval']) AND ($add_pokemon['aanval2'] != $groei['aanval']) AND ($add_pokemon['aanval3'] != $groei['aanval']) AND ($add_pokemon['aanval4'] != $groei['aanval'])) {
							$nummer = rand(1, 4);
							if ($nummer == 1)		$add_pokemon['aanval1'] = $groei['aanval'];
							else if ($nummer == 2)	$add_pokemon['aanval2'] = $groei['aanval'];
							else if ($nummer == 3)	$add_pokemon['aanval3'] = $groei['aanval'];
							else if ($nummer == 4)	$add_pokemon['aanval4'] = $groei['aanval'];
						}
					}
				} else if ($groei['wat'] == "evo") {
					$evo = DB::exQuery("SELECT * FROM `pokemon_wild` WHERE `wild_id`='".$groei['nieuw_id']."' LIMIT 1")->fetch_assoc();
					$add_pokemon['id']             = $groei['nieuw_id'];
					$add_pokemon['pokemon']        = $groei['naam'];
					$loop = 0;
					break;
				}
			} else {
				$klaar = true;
				break;
			}
		}
		if ($teller == 0) {
			break;
			$klaar = true;
		}
		if ($loop == 2) {
			break;
			$klaar = true;
		}
	} while(!$klaar);
    
    $exp['level'] = $delete_info['level'] + 1;
    $info = DB::exQuery("SELECT experience.punten, pokemon_wild.* FROM experience INNER JOIN pokemon_wild WHERE experience.soort='".$pokemon['groei']."' AND experience.level='".$exp['level']."' AND pokemon_wild.wild_id='".$add_pokemon['id']."'")->fetch_assoc();
    
    //Karakter kiezen 
    $karakter  = DB::exQuery("SELECT * FROM `karakters` ORDER BY rand() limit 1")->fetch_assoc();
      
    //Pokemon IV maken en opslaan
    //Iv willekeurig getal tussen 1,31. Ik neem 2 omdat 1 te weinig is:P
    $attack_iv       = mt_rand(2,31);
    $defence_iv      = mt_rand(2,31);
    $speed_iv        = mt_rand(2,31);
    $spcattack_iv    = mt_rand(2,31);
    $spcdefence_iv   = mt_rand(2,31);
    $hp_iv           = mt_rand(2,31);
  
    //Stats berekenen
    $add_pokemon['attackstat']     = round((((($info['attack_base']*2+$attack_iv)*$delete_info['level']/100)+5)*1)*$karakter['attack_add']);
    $add_pokemon['defencestat']    = round((((($info['defence_base']*2+$defence_iv)*$delete_info['level']/100)+5)*1)*$karakter['defence_add']);
    $add_pokemon['speedstat']      = round((((($info['speed_base']*2+$speed_iv)*$delete_info['level']/100)+5)*1)*$karakter['speed_add']);
    $add_pokemon['spcattackstat']  = round((((($info['spc.attack_base']*2+$spcattack_iv)*$delete_info['level']/100)+5)*1)*$karakter['spc.attack_add']);
    $add_pokemon['spcdefencestat'] = round((((($info['spc.defence_base']*2+$spcdefence_iv)*$delete_info['level']/100)+5)*1)*$karakter['spc.defence_add']);
    $add_pokemon['hpstat']         = round(((($info['hp_base']*2+$hp_iv)*$delete_info['level']/100)+$delete_info['level'])+10);
    
    //Iv willekeurig getal tussen 2,15
    //Normaal tussen 1,31 maar wilde pokemon moet wat minder sterk zijn
    $attack_iv       = mt_rand(2,15);
    $defence_iv      = mt_rand(2,15);
    $speed_iv        = mt_rand(2,15);
    $spcattack_iv    = mt_rand(2,15);
    $spcdefence_iv   = mt_rand(2,15);
    $hp_iv           = mt_rand(2,15);
    
    $rand_ab = rand(0, (sizeof($ability) - 1));
	$ability = $ability[$rand_ab];

	$date = date('Y-m-d H:i:s');
	
    DB::exQuery("INSERT INTO `pokemon_speler` (`wild_id`, `user_id`, `opzak`, `opzak_nummer`, `karakter`, `trade`, `level`, `levenmax`, `leven`, `expnodig`, `attack`, `defence`, `speed`, `spc.attack`, `spc.defence`, `attack_iv`, `defence_iv`, `speed_iv`, `spc.attack_iv`, `spc.defence_iv`, `hp_iv`, `aanval_1`, `aanval_2`, `aanval_3`, `aanval_4`, `gevongenmet`, `ability`, `capture_date`) 
      VALUES ('".$add_pokemon['id']."', '".$_SESSION['id']."', 'ja', '".$delete_info['opzak_nummer']."', '".$karakter['karakter_naam']."', '1.5', '".$delete_info['level']."', '".$add_pokemon['hpstat'] ."', '".$add_pokemon['hpstat']."', '".$info['punten']."', '".$add_pokemon['attackstat']."', '".$add_pokemon['defencestat']."', '".$add_pokemon['speedstat']."', '".$add_pokemon['spcattackstat']."', '".$add_pokemon['spcdefencestat']."', '".$attack_iv."', '".$defence_iv."', '".$speed_iv."', '".$spcattack_iv."', '".$spcdefence_iv."', '".$hp_iv."', '".$add_pokemon['aanval1']."', '".$add_pokemon['aanval2']."', '".$add_pokemon['aanval3']."', '".$add_pokemon['aanval4']."', 'Trader ball', '".$ability."', '".$date."')");
    
    //Remove pokemon from trader
    DB::exQuery("UPDATE `traders` SET `wil`='', `naam`=''  WHERE `eigenaar`='".$_POST['check']."'");
    
    update_pokedex($add_pokemon['id'],'','ei');
    
	}
}

#Admin functie trainer refresh:

if (isset($_POST['refresh'])) {
	DB::exQuery("UPDATE `traders` SET `wil`='',`naam`='{$pokemonnaam}'");
	echo '<div class="green">'.$txt['success_traders_refresh'].'</div>';
}

echo addNpcBox(14, 'Comerciantes', $txt['title_text']);

if ($gebruiker['rank'] < 5) {
	echo '<div class="red">RANK MÍNIMO PARA TROCAR POKÉMONS COM OS COMERCIANTES: 5 - First Coach. CONTINUE UPANDO PARA LIBERAR!</div>';
}
?>

<center>

<?php
while($traders = $traders_sql->fetch_assoc()) {
	if ($traders['eigenaar'] == 'Kayl') {
		if (empty($traders['naam']) || empty($traders['wil']))
			$text = $txt['kayl_no_pokemon'];
		else{
			$text = $txt['kayl_text_1'].$traders['wil'].$txt['kayl_text_2'].$traders['naam'].$txt['kayl_text_3'].'
					 <input type="hidden" name="check" value="'.$traders['eigenaar'].'">';
			if ($gebruiker['rank']>=5) {
			$text .= '<input type="submit" name="submit" style="margin-top:101px;" class="button" value="'.$txt['button_change'].' '.$traders['eigenaar'].'">';
			}
		}
		
		echo '<form method="post">
            <table width="100%" class="box-content general" style="margin-bottom: 7px">
    					<tr>
    						<td width="160"><center><img src="' . $static_url . '/images/Kayl.png" width="97" height="200" alt="Kayl" /></center></td>
    						<td width="340" valign="top">"'.$text.'"</td>
    					</tr>
    				</table>
          </form>';
	}
	
	if ($traders['eigenaar'] == 'Wayne') {
		if (empty($traders['naam']) || empty($traders['wil']))
			$text = $txt['wayne_no_pokemon'];
		else{
			$text = $txt['wayne_text_1'].$traders['wil'].$txt['wayne_text_2'].$traders['naam'].$txt['wayne_text_3'].'
					 <input type="hidden" name="check" value="'.$traders['eigenaar'].'">';
					 if ($gebruiker['rank']>=5) {
						$text.= '<input type="submit" name="submit" style="margin-top:81px;" class="button" value="'.$txt['button_change'].' '.$traders['eigenaar'].'">';
					 }
		}
		
		echo '<form method="post">
            <table width="100%" class="box-content general" style="margin-bottom: 7px">
    					<tr>
    						<td width="160"><center><img src="' . $static_url . '/images/Wayne.png" width="81" height="200" alt="Wayne" /></center></td>
    						<td width="340" valign="top">"'.$text.'"</td>
    					</tr>
    				</table>
          </form>';
	}
	
	if ($traders['eigenaar'] == 'Remy') {
		if (empty($traders['naam']) || empty($traders['wil']))
			$text = $txt['remy_no_pokemon'];
		else{
			$text = $txt['remy_text_1'].$traders['wil'].$txt['remy_text_2'].$traders['naam'].$txt['remy_text_3'].'
					 <input type="hidden" name="check" value="'.$traders['eigenaar'].'">';
			if ($gebruiker['rank']>=5) {
			$text .='<input type="submit" name="submit" style="margin-top:115px;" class="button" value="'.$txt['button_change'].' '.$traders['eigenaar'].'">';
			}
		}
		
		echo '<form method="post">
            <table width="100%" class="box-content general">
      				<tr>
      					<td width="160"><center><img src="' . $static_url . '/images/Remy.png" width="88" height="200" alt="Remy" /></center></td>
      					<td width="340" valign="top">"'.$text.'"</td>
      				</tr>
      			</table>
          </form>';
	}  
}
?>
<?php if ($gebruiker['admin'] != 0) { ?>

<form method="post">
<strong><?php echo $txt['refresh_pokemon']; ?></strong><br />
<input type="submit" name="refresh" class="button" value="<?php echo $txt['button_traders_refresh']; ?>" /><br />
</form>
<?php } ?>
</center>