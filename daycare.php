<?php
#Security laden
include('app/includes/resources/security.php');

#Je moet rank 4 zijn om deze pagina te kunnen zien
if ($gebruiker['rank'] <= 3) header("Location: ./home");
	
$eicheck_sql = DB::exQuery("SELECT * FROM `daycare` WHERE `user_id`=" . $_SESSION['id'] . " AND `ei`='1'");
$eicheck = $eicheck_sql->fetch_assoc();

echo addNPCBox(35, 'Jardim de Infância', $txt['title_text'].'<br>'.$hoeveel);
#-----------------------EI
if (isset($_POST['accept'])) {
	$hoeveelinhand = $gebruiker['in_hand'] + 1;
	$eiaantal = $eicheck_sql->num_rows;
	
	if ($eicheck['user_id'] != $_SESSION['id'])	echo '<div class="red">' . $txt['alert_not_your_pokemon'] . '</div>';
	else if ($hoeveelinhand == 7)	echo'<div class="red">' . $txt['alert_hand_full'] . '</div>';
	else if ($eiaantal == 0)	echo'<div class="red">' . $txt['alert_no_eggs'] . '</div>';
	else {
		# pokemon geven
		$query = DB::exQuery("SELECT `wild_id`,`naam`,`groei`,`attack_base`,`defence_base`,`speed_base`,`spc.attack_base`,`spc.defence_base`,`hp_base`, `ability` FROM `pokemon_wild` WHERE `naam`='" . $eicheck['naam'] . "' LIMIT 1")->fetch_assoc();

		# De willekeurige pokemon in de pokemon_speler tabel zetten
		DB::exQuery("INSERT INTO `pokemon_speler` (`wild_id`,`aanval_1`,`aanval_2`,`aanval_3`,`aanval_4`) SELECT `wild_id`,`aanval_1`,`aanval_2`,`aanval_3`,`aanval_4` FROM `pokemon_wild` WHERE `wild_id`=" . $query['wild_id'] . "");

		#id opvragen van de insert hierboven
		$pokeid	= DB::insertID();

		#Baby pokemon timer starten
		$tijd = date('Y-m-d H:i:s');

		#Karakter kiezen 
		$karakterr = DB::exQuery("SELECT * FROM `karakters` ORDER BY RAND() LIMIT 1")->fetch_assoc();
		$karakter = $karakterr['karakter_naam'];

		#Expnodig opzoeken en opslaan
		$levelpokemonnieuw = $query['level'] + 1;
		$groeipokemonnieuw = $query['groei'];
		$experience = DB::exQuery("SELECT `punten` FROM `experience` WHERE `soort`='" . $groeipokemonnieuw . "' AND `level`='" . $levelpokemonnieuw . "' LIMIT 1")->fetch_assoc();

		#Pokemon IV maken en opslaan
		#Iv willekeurig getal tussen 1,31. Ik neem 2 omdat 1 te weinig is:P
		$attack_iv		= rand(0,31);
		$defence_iv		= rand(0,31);
		$speed_iv		= rand(0,31);
		$spcattack_iv	= rand(0,31);
		$spcdefence_iv	= rand(0,31);
		$hp_iv			= rand(0,31);

		#Stats berekenen
		$attackstat		= round(((($query['attack_base']*2+$attack_iv)*$query['level']/100)+5)*1);
		$defencestat	= round(((($query['defence_base']*2+$defence_iv)*$query['level']/100)+5)*1);
		$speedstat		= round(((($query['speed_base']*2+$speed_iv)*$query['level']/100)+5)*1);
		$spcattackstat	= round(((($query['spc.attack_base']*2+$spcattack_iv)*$query['level']/100)+5)*1);
		$spcdefencestat	= round(((($query['spc.defence_base']*2+$spcdefence_iv)*$query['level']/100)+5)*1);
		#Hp bereken
		$hpstat			= round(((($query['hp_base']*2+$hp_iv)*$query['level']/100)+$query['level'])+10);
		//Ability
		$ability = explode(',', $query['ability']);
		$rand_ab = rand(0, (sizeof($ability) - 1));
		$ability = $ability[$rand_ab];

		$date = date ('Y-m-d H:i:s');

		#Heeft speler wel pokemon gekregen??
		if (is_numeric($pokeid))	DB::exQuery("UPDATE `gebruikers` SET `aantalpokemon`=`aantalpokemon`+'1' WHERE `user_id`=" . $_SESSION['id'] . " LIMIT 1");
    
		#Alle gegevens van de pokemon opslaan
		DB::exQuery("UPDATE `pokemon_speler` SET `karakter`='" . $karakter . "',`expnodig`='" . $experience['punten'] . "',`user_id`=" . $_SESSION['id'] . ",`opzak`='ja',`opzak_nummer`='" . $hoeveelinhand . "',`shiny`='" . $eicheck['levelup'] . "',`ei`='1',`ei_tijd`='" . $tijd . "',`attack_iv`='" . $attack_iv . "',`defence_iv`='" . $defence_iv . "',`speed_iv`='" . $speed_iv . "',`spc.attack_iv`='" . $spcattack_iv . "',`spc.defence_iv`='" . $spcdefence_iv . "',`hp_iv`='" . $hp_iv . "',`attack`='" . $attackstat . "',`defence`='" . $defencestat . "',`speed`='" . $speedstat . "',`spc.attack`='" . $spcattackstat . "',`spc.defence`='" . $spcdefencestat . "',`levenmax`='" . $hpstat . "',`leven`='" . $hpstat . "',`level`='5', `ability`='" . $ability . "', capture_date='" . $date . "' WHERE `id`=" . $pokeid . " LIMIT 1");
    
		#Delete From Daycare
		DB::exQuery("DELETE FROM `daycare` WHERE `user_id`=" . $_SESSION['id'] . " AND `ei`='1'");
		echo '<div class="green">' . $txt['success_egg'] . '</div>';
	}
} else if (isset($_POST['dontaccept'])) {
	$eiaantal = $eicheck_sql->num_rows;
	if ($eicheck['user_id'] != $_SESSION['id'])	echo '<div class="red">' . $txt['alert_not_your_pokemon'] . '</div>';
	else if ($eiaantal == 0)	echo '<div class="red">' . $txt['alert_no_eggs'] . '</div>';
	else	DB::exQuery("DELETE FROM `daycare` WHERE `user_id`=" . $_SESSION['id'] . " AND `ei`='1'");
} else if ($eicheck_sql->num_rows == 1)	echo '<form method="post">
	<div class="green">' . $txt['egg_text'] . '<br /><br /></div>
</form>';
#-----------------------EINDE EI

$daycaresql = DB::exQuery("SELECT `daycare`.*,`pokemon_speler`.`wild_id`,`pokemon_speler`.`shiny` FROM `daycare` INNER JOIN `pokemon_speler` ON `daycare`.`pokemonid`=`pokemon_speler`.`id` WHERE `daycare`.`user_id`=" . $_SESSION['id'] . " AND `daycare`.`ei`='0'");
$aantal = $daycaresql->num_rows;

#Default
$kostenbegin = 250;

if ($gebruiker['premiumaccount'] < time()) {
	$hoeveel = $txt['normal_user'];
	$toegestaan = 1;
} else {
	$hoeveel = $txt['premium_user'];
	$toegestaan = 2;
}

#Things van pokemon wegbrengen:
if (isset($_POST['brengweg'])) {
	$update = DB::exQuery("SELECT `pokemon_wild`.`naam`,`pokemon_wild`.`type1`,`pokemon_speler`.`id`,`pokemon_speler`.`user_id`,`pokemon_speler`.`opzak`,`pokemon_speler`.`level` FROM `pokemon_wild` INNER JOIN `pokemon_speler` ON `pokemon_wild`.`wild_id`=`pokemon_speler`.`wild_id` WHERE `id`=" . $_POST['pokemonid'] . " LIMIT 1")->fetch_assoc();
	if ($update['user_id'] != $_SESSION['id'])	echo '<div class="red">'.$txt['alert_not_your_pokemon'].'</div>';
	else if ($gebruiker['in_hand'] <= 1) echo'<div class="red">Você não pode ficar sem nenhum pokémon em seu time.</div>';
    	else if ($update['type1'] == 'Shadow') echo' <div class="red">Pokémons do tipo Shadow não podem ficar no Jardim de infância.</div>';
	else if ($update['opzak'] == 'day')			echo '<div class="red">'.$txt['alert_already_in_daycare'].'</div>';
	else if ($update['level'] >= 100)				echo '<div class="red">'.$txt['alert_already_lvl_100'].'</div>';
	else if ($aantal >= $toegestaan)				echo '<div class="red">'.$txt['alert_daycare_full'].'</div>';
	else {
		$pokemon_sql->data_seek(0);
		$i = 0;
		while($pokemon = $pokemon_sql->fetch_assoc()) {
			if ($pokemon['id'] == $_POST['pokemonid']) {
				DB::exQuery("UPDATE pokemon_speler SET `opzak`='day', `opzak_nummer`='' WHERE id = '".$_POST['pokemonid']."'");
				DB::exQuery("INSERT INTO daycare (pokemonid, user_id, naam, level) VALUES ('".$update['id']."', '".$_SESSION['id']."', '".$update['naam']."', '".$update['level']."')");
			} else {
				++$i;
				DB::exQuery("UPDATE `pokemon_speler` SET `opzak_nummer`='".$i."' WHERE `id`='".$pokemon['id']."'");
			}
		}
		echo' <div class="green">'.$txt['success_bring'].'</div>';
	}
}

#Things van pokemon ophalen:
if (isset($_POST['haalop'])) {
  $select = DB::exQuery("SELECT * FROM `daycare` WHERE `pokemonid`='".$_POST['pokemonid']."'")->fetch_assoc();
  $level = $select['level'] + $select['levelup'];
  $kostenlevelup = $select['levelup'] * 500;
  $kosten = $kostenbegin + $kostenlevelup;
	$hoeveelinhand = $gebruiker['in_hand'] + 1;
	if ($_SESSION['id'] != $select['user_id'])
    echo'<div class="red">'.$txt['alert_not_your_pokemon'].'</div>';
	else if ($hoeveelinhand == 7)
		echo'<div class="red">'.$txt['alert_hand_full'].'</div>';
	else if ($kosten > $gebruiker['silver'])
		echo'<div class="red">'.$txt['alert_not_enough_silver'].'</div>';
	else{
  	DB::exQuery("UPDATE pokemon_speler SET `opzak`='ja', `opzak_nummer`='".$hoeveelinhand."' WHERE id = '".$_POST['pokemonid']."'");
  	DB::exQuery("DELETE FROM daycare WHERE pokemonid = '".$_POST['pokemonid']."'");
  	DB::exQuery("UPDATE `gebruikers` SET `silver`=`silver`-'".$kosten."' WHERE `user_id`='".$_SESSION['id']."'");
  	
    $_SESSION['used'] = array();    
    $count = 0;
  	
    for($i=1; $i<=$select['levelup']; $i++) {
      if ($count == 0) $_SESSION['lvl_old'] = $select['level'];
      array_push($_SESSION['used'], $_POST['pokemonid']);
      $count++;
      $update = DB::exQuery("SELECT pw.*, ps.* FROM pokemon_wild AS pw INNER JOIN pokemon_speler ps ON pw.wild_id = ps.wild_id WHERE id = '".$_POST['pokemonid']."'")->fetch_assoc();
      if ($update['level'] <= 100) {
        #informatie van level
        $nieuwelevel = $update['level']+1; # Dit was 2
        $levelnieuw = $update['level']+1;
        
        #Script aanroepen dat nieuwe stats berekent
        nieuwestats($update,$nieuwelevel,$update['expnodig']);
        
        #Script aanroepen dat berekent als pokemon evolueert of een aanval leert
        if ((!$_SESSION['aanvalnieuw']) AND (!$_SESSION['evolueren']))
          $toestemming = levelgroei($levelnieuw,$update);
        
        #Gebeurtenis maken.
        $pokemonnaam = htmlspecialchars($update['naam'], ENT_QUOTES);

        DB::exQuery("INSERT INTO gebeurtenis (datum, ontvanger_id, bericht, gelezen)
	        VALUES (NOW(), '".$_SESSION['id']."', '".$pokemonnaam." subiu de nível!', '0')");
      } 
    }
	  echo' <div class="green">'.$txt['success_take'].'</div>';
	}
}

?>

		<style>
            .alternate:hover {
                background-color: rgba(0, 0, 0, .1);
                transition: 1s;
            }
            .carousel-cell {
                margin: 10px 10px;
                filter: grayscale(100%);
                transform: scale(0.85);
                overflow: hidden;
            }
            .carousel-cell.is-selected {
                filter: grayscale(20%) invert(8%);
                transition: 1s;
                transform: scale(1);
            }
        </style>
        <div class="box-content" style="width: 100%;">
            <table width="100%" class="general">
                <thead><tr><th colspan="6">Minha equipe</th></tr></thead>
                <tbody><tr>
                        <script>
                            var $poke_array_id = [];
                            var $poke_array_iid = [];
                            var $poke_array_name = [];
                            var $poke_array_spe = [];
                        </script>

                        <td style="padding: 0" colspan="2">
                            <div class="main-carousel" style="height: 97px; position: relative">
								<?php
									$pokemon_profiel_sql = DB::exQuery("SELECT `pokemon_speler`.*,`pokemon_wild`.`naam`,`pokemon_wild`.`type1`,`pokemon_wild`.`type2` FROM `pokemon_speler` INNER JOIN `pokemon_wild` ON `pokemon_speler`.`wild_id`=`pokemon_wild`.`wild_id` WHERE `user_id`='" . $_SESSION["id"] . "' AND `opzak`='ja' ORDER BY `opzak_nummer` ASC");
                                    while($pokemon_profile = $pokemon_profiel_sql->fetch_assoc()) {
                                        $pokemon_profile = pokemonei($pokemon_profile, $txt);
                                        $of_name = $pokemon_profile['naam'];
                                        $popup = pokemon_popup($pokemon_profile, $txt);
                                        $pokemon_profile['naam'] = pokemon_naam($pokemon_profile['naam'], $pokemon_profile['roepnaam'], $pokemon_profile['icon']);
                                ?>
                                        <div class="carousel-cell" style="text-align: center;">
                                            <div style="display:table-cell; vertical-align:middle; min-width: 150px; height: 150px;">
                                                <?='<img id="my_pokes_infos" class="tip_bottom-middle" title="'.$popup.'" src="' . $static_url . '/'.$pokemon_profile['link'].'" />';?>
                                                <script id="remove">
                                                    $poke_array_id.push("<?=$pokemon_profile['wild_id']?>");
                                                    $poke_array_iid.push("<?=$pokemon_profile['id']?>");
                                                    $poke_array_name.push("<?=$of_name?>");
                                                    $poke_array_spe.push("<?=$pokemon_profile['naam']?>");

                                                    document.querySelector('#remove').outerHTML = '';
                                                </script>
                                            </div>
                                        </div>
                                <?php
                                    }
                                ?>
                            </div>
                            <div style="width: 100%; background: rgba(0, 0, 0, .3); position: relative; bottom: 0; text-align: center; height: 53px; padding-top: 3px; margin-top: -8px; border-bottom-right-radius: 2px;  border-bottom-left-radius: 2px">
                                <div style="width: 100%; text-align: center; font-size: 17px; margin-top: 3px">
                                    <h4 id="poke_name" style="margin: 0; color: #eee; font-weight: bold;"></h4>
                                    <a href="./pokedex&poke=1" id="poke_link" style="color: #eee; font-size: 13px"></a>
                                </div>
                            </div>
                        </td>
                </tr></tbody>
                <tfoot>

                <tr style="text-align: center; font-size: 13px">
                    <td>
                        <button class="button" style="margin: 6px" onclick="daycare()">DEIXAR POKÉMON</button>
                    </td>
                </tr>
                </tfoot>
            </table>
        </div>

        <form method="post" action="./daycare" id="daycare">
            <input type="hidden" name="pokemonid" id="poke_id" value=""/>
            <input type="hidden" name="brengweg" id="poke_view" value="<?= $txt['button_bring']; ?>" />
        </form>

        <script>
            var $carousel = $('.main-carousel');
            var $poke_name = $('#poke_name');
            var $poke_link = $('#poke_link');
            var $poke_id = $('#poke_id');

            var $car = $carousel.flickity({
                cellAlign: 'center',
                contain: false,
                pageDots: false,
                wrapAround: false,
                autoPlay: false
            });

            var flkty = $carousel.data('flickity');

            $carousel.on('select.flickity', function() {
                $poke_link.attr('href', './pokedex&poke='+$poke_array_id[flkty.selectedIndex]);
                $poke_link.html($poke_array_name[flkty.selectedIndex]);
                $poke_name.html($poke_array_spe[flkty.selectedIndex]);

                $poke_id.val ($poke_array_iid[flkty.selectedIndex]);
            });

            $poke_link.attr('href', './pokedex&poke='+$poke_array_id[0]);
            $poke_link.html($poke_array_name[0]);
            $poke_name.html($poke_array_spe[0]);

            $poke_id.val ($poke_array_iid[0]);

            $car.resize();

			function daycare () {
				$('#daycare').submit();
			}
        </script>

		<?php 

			 if ($aantal > 0) { ?>

            <div class="box-content" style="margin-top: 7px">
				<table style="width: 100%; text-align: center" class="general" cellpadding="0" cellspacing="0">
					<thead>
						<tr> 
							<th colspan="6"><?= $txt['take_pokemon_text']; ?></th>
						</tr>
					</thead>
					<tr>
						<td><?php echo $txt['#']; ?></td>
						<td><?php echo $txt['name']; ?></td>
						<td><?php echo $txt['level']; ?></td>
						<td><?php echo $txt['levelup']; ?></td>
						<td><?php echo $txt['cost']; ?></td>
						<td><?php echo $txt['buy']; ?></td>
				</tr>
				<?php while($daycare = $daycaresql->fetch_assoc()) {
						$level = $daycare['level'] + $daycare['levelup'];
						$kostenlevelup = $daycare['levelup'] * 500;
						$kosten = $kostenbegin + $kostenlevelup;
						$map = 'pokemon';
						if ($daycare['shiny'] == '1') $map = 'shiny';
								echo'
									<tr>
										<td><img src="'.$static_url.'/images/'.$map.'/icon/'.$daycare['wild_id'].'.gif"></td>
										<td><a href="./pokemon-profile&id='.$daycare['pokemonid'].'">'.$daycare['naam'].'</a></td>
										<td>'.$level.'</td>
										<td>'.$daycare['levelup'].'</td>
										<td><img src="'.$static_url.'/images/icons/silver.png" title="Silver" style="margin-bottom:-3px;"> '.highamount($kosten).'</td>
										<td><form method="post"><input type="hidden" name="pokemonid" value="'.$daycare['pokemonid'].'">
											<input type="submit" name="haalop" value="'.$txt['button_take'].'" class="button_mini"></form></td>
								</tr>';
							}
					?>
				</table>
			</div>
<?php } ?>