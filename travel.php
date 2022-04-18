<?php
#Script laden zodat je nooit pagina buiten de index om kan laden
require_once("app/includes/resources/security.php");

$reg_arr = array('kanto', 'johto', 'hoenn', 'sinnoh', 'unova', 'kalos', 'alola');
$time = array_search(strtolower($gebruiker['wereld']), $reg_arr);
$time_arr = array(60, 120, 180, 240, 300, 360, 420);

#prijzen vaststellen
$prijs['kanto'] = 50;
$prijs['kantototaal'] = $prijs['kanto'] * $gebruiker['in_hand'];
$prijs['kanto_time_total'] = max ($time_arr[0], $time_arr[$time]) - min ($time_arr[0], $time_arr[$time]);

$prijs['johto'] = 100;
$prijs['johtototaal'] = $prijs['johto'] * $gebruiker['in_hand'];
$prijs['johto_time_total'] = max ($time_arr[1], $time_arr[$time]) - min ($time_arr[1], $time_arr[$time]);

$prijs['hoenn'] = 200;
$prijs['hoenntotaal'] = $prijs['hoenn'] * $gebruiker['in_hand'];
$prijs['hoenn_time_total'] = max ($time_arr[2], $time_arr[$time]) - min ($time_arr[2], $time_arr[$time]);

$prijs['sinnoh'] = 300;
$prijs['sinnohtotaal'] = $prijs['sinnoh'] * $gebruiker['in_hand'];
$prijs['sinnoh_time_total'] = max ($time_arr[3], $time_arr[$time]) - min ($time_arr[3], $time_arr[$time]);

$prijs['unova'] = 500;
$prijs['unovatotaal'] = $prijs['unova'] * $gebruiker['in_hand'];
$prijs['unova_time_total'] = max ($time_arr[4], $time_arr[$time]) - min ($time_arr[4], $time_arr[$time]);

$prijs['kalos'] = 1000;
$prijs['kalostotaal'] = $prijs['kalos'] * $gebruiker['in_hand'];
$prijs['kalos_time_total'] = max ($time_arr[5], $time_arr[$time]) - min ($time_arr[5], $time_arr[$time]);

$prijs['alola'] = 1500;
$prijs['alolatotaal'] = $prijs['alola'] * $gebruiker['in_hand'];
$prijs['alola_time_total'] = max ($time_arr[6], $time_arr[$time]) - min ($time_arr[6], $time_arr[$time]);

if ($gebruiker['premiumaccount'] > time()) {
	for ($i = 0; $i < sizeof($reg_arr); $i++) {
		$val = $prijs[$reg_arr[$i].'totaal'];
		if ($val > 1000) {
			$prijs[$reg_arr[$i].'totaal'] = 1000;
		}
	}
}

#Als er op de knop gedrukt word
if (isset($_POST['travel'])) {
	$wereld = $_POST['wereld'];

	$count_time = $prijs[$wereld . '_time_total'];
	if ($gebruiker['admin'] > 0) $count_time = 0;
	

	$prijss = $prijs[$wereld . 'totaal'];
	$prijsmooi = highamount($prijss);

	#De eerste letter verandere in hoofdletter
	$wereld = ucfirst($wereld);
	if (empty($wereld))	$message = '<div class="red">'.$txt['alert_no_world'].'</div>';
	#Zit de speler al in deze wereld?
	else if ($gebruiker['wereld'] == $wereld)	$message = '<div class="red">'.$txt['alert_already_in_world'].'</div>';
	#Bestaat de wereld wel?
	else if ($wereld != 'Kanto' && $wereld != 'Johto' && $wereld != 'Hoenn' && $wereld != 'Sinnoh' && $wereld != 'Unova' && $wereld != 'Kalos' && $wereld != 'Alola')	$message = '<div class="red">'.$txt['alert_world_invalid'].'</div>';
	else if ($gebruiker[ucfirst($wereld).'_block'] == 0) echo '<div class="red">Você ainda não desbloqueou esta região!</div>';
	else {
		#Heeft de speler wel genoeg silver?
		if ($gebruiker['silver'] <= $prijss)	$message = '<div class="red">'.$txt['alert_not_enough_money'].'</div>';
		else { #Speler heeft genoeg silver.
			#silver minderen en nieuwe wereld opslaan
			DB::exQuery("UPDATE `gebruikers` SET `silver`=`silver` - '".$prijss."',`wereld`='" . $wereld . "',`traveltijdbegin`=NOW(),`traveltijd`='" . $count_time . "' WHERE `user_id`=" . $_SESSION['id'] . " LIMIT 1");
			exit(header("LOCATION: ./travel"));
		}
	}
}

#########SURF
#Als er op de knop gedrukt word
if (isset($_POST['surf'])) {
	if (empty($_POST['wereld']) || !is_numeric($_POST['pokemonid']))	$surferror = '<div class="red">'.$txt['alert_not_everything_selected'].'</div>';
	else {
		#query voor alle info
		$pkmninfo = DB::exQuery("SELECT `id`,`user_id`,`level`,`aanval_1`,`aanval_2`,`aanval_3`,`aanval_4` FROM `pokemon_speler` WHERE `id`=" . (int)$_POST['pokemonid'] . " LIMIT 1")->fetch_assoc();
		#De eerste letter verandere in hoofdletter
		$wereld = ucfirst($_POST['wereld']);
		#eigenaar check
		if ($pkmninfo['user_id'] != $_SESSION['id'])	$message = ' <div class="red">'.$txt['alert_not_your_pokemon'].'</div>';
		#Bestaat de wereld wel?
		else if ($wereld != 'Kanto' && $wereld != 'Johto' && $wereld != 'Hoenn' && $wereld != 'Sinnoh' && $wereld != 'Unova' && $wereld != 'Kalos' && $wereld != 'Alola')	$message = '<div class="red">'.$txt['alert_world_invalid'].'</div>';
		#Zit de speler al in deze wereld?
		else if ($gebruiker['wereld'] == $wereld)	$message = '<div class="red">'.$txt['alert_already_in_world'].'</div>';
		#KIjken of pokemon de aanval wel heeft
		else if ($pkmninfo['aanval_1'] != 'Surf' && $pkmninfo['aanval_2'] != 'Surf' && $pkmninfo['aanval_3'] != 'Surf' && $pkmninfo['aanval_4'] != 'Surf')	$message = '<div class="red">'.$txt['alert_no_surf'].'</div>';
		else if ($gebruiker[ucfirst($wereld).'_block'] == 0) echo '<div class="red">Você ainda não desbloqueou esta região!</div>';
		#Kijken of de pokemon level 80 is
		else if ($pkmninfo['level'] < 80)	$message = '<div class="red">'.$txt['alert_not_strong_enough'].'</div>';
		#Alles goed:	
		else {
			DB::exQuery("UPDATE `gebruikers` SET `wereld`='".$wereld."' WHERE `user_id`='".$_SESSION['id']."'");
			exit(header("LOCATION: ./travel"));
		}
	}
}

##########SURF
#Als er op de knop gedrukt word
if (isset($_POST['fly'])) {
	if (empty($_POST['wereld']) || !is_numeric($_POST['pokemonid']))	$message = '<div class="red">'.$txt['alert_not_everything_selected'].'</div>';
	else {
		#query voor alle info
		$pkmninfo = DB::exQuery("SELECT `id`,`user_id`,`level`,`aanval_1`,`aanval_2`,`aanval_3`,`aanval_4` FROM `pokemon_speler` WHERE `id`=" . (int)$_POST['pokemonid'] . " LIMIT 1")->fetch_assoc();
		#De eerste letter verandere in hoofdletter
		$wereld = ucfirst($_POST['wereld']);
		#eigenaar check
		if ($pkmninfo['user_id'] != $_SESSION['id'])	$message = ' <div class="red">'.$txt['alert_not_your_pokemon'].'</div>';
		#Bestaat de wereld wel?
		else if ($wereld != 'Kanto' && $wereld != 'Johto' && $wereld != 'Hoenn' && $wereld != 'Sinnoh' && $wereld != 'Unova' && $wereld != 'Kalos' && $wereld != 'Alola')	$message = '<div class="red">'.$txt['alert_world_invalid'].'</div>';
		#Zit de speler al in deze wereld?
		else if ($gebruiker['wereld'] == $wereld)	$message = '<div class="red">'.$txt['alert_already_in_world'].'</div>';
		#KIjken of pokemon de aanval wel heeft
		else if ($pkmninfo['aanval_1'] != 'Fly' && $pkmninfo['aanval_2'] != 'Fly' && $pkmninfo['aanval_3'] != 'Fly' && $pkmninfo['aanval_4'] != 'Fly')	$message = '<div class="red">'.$txt['alert_no_fly'].'</div>';
		else if ($gebruiker[ucfirst($wereld).'_block'] == 0) echo '<div class="red">Você ainda não desbloqueou esta região!</div>';
		#Kijken of de pokemon level 80 is
		else if ($pkmninfo['level'] < 80)	$message = '<div class="red">'.$txt['alert_not_strong_enough'].'</div>';
		#Alles goed:	
		else {
			DB::exQuery("UPDATE `gebruikers` SET `wereld`='".$wereld."' WHERE `user_id`='".$_SESSION['id']."' LIMIT 1");
			exit(header("LOCATION: ./travel"));
		}
	}
}

if (!empty($message))	echo $message;
?>
<style>
    .carousel-cell {
        margin: 10px 10px;
        filter: grayscale(100%);
		overflow: hidden;
		transform: scale(0.9);
    }
    .carousel-cell.is-selected {
        filter: grayscale(20%);
        transition: .5s;
		box-shadow: 0 0 15px #0e0d0d66;
		border-radius: 6px;
		transform: scale(1);
    }
</style>

<div class="box-content" style="display: inline-block; width: 100%;">
	<form method="post"><table class="general bordered" width="100%">
		<thead>
			<tr><th colspan="6"><?=$txt['title_text'];?></th></tr>
		</thead>
		<tbody>
			<tr>
				<td style="width: 100%; padding: 0">
					<div style="width: 100%; height: 217px;">
						<script>
							$lock = [];
						</script>
						<div class="main-carousel carousel">
							<?php
								for ($i = 0; $i < count($reg_arr); $i++) {
									$lock = $gebruiker[ucfirst($reg_arr[$i]).'_block'];
							?>
							<div class="carousel-cell" style="text-align: center; width: 300px;">
								<div>
									<?= (strtolower($gebruiker['wereld']) == $reg_arr[$i])? '<div style="border-radius: 4px;width: 100%;background: rgba(255, 255, 255, .4);height: 197px;position: absolute;z-index: 1000; line-height: 170px;" title="Você já está nesta região!"><img src="'.$static_url.'/images/icons/avatar/lock.png" style="width: 27%"></div>' : ''; ?>
									<?= (($lock) == 0)? '<div style="border-radius: 4px;width: 100%;background: rgba(255, 255, 255, .4);height: 197px;position: absolute;z-index: 1000; line-height: 170px;" title="Você ainda não conseguiu todas as Insígnias da Região anterior!"><img src="'.$static_url.'/images/icons/avatar/lock.png" style="width: 27%"></div>' : ''; ?>

									<h1 style="position: absolute; text-align: center; width: 100%; color: #fff; font-weight: bold; margin-top: 50px"><?=strtoupper($reg_arr[$i])?></h1>
									<p style="position: absolute; text-align: center; width: 100%; color: #fff; font-weight: 700; margin-top: 90px; font-size: 21px">Custo: <img src="<?=$static_url?>/images/icons/silver.png" title="Silvers"><?=$prijs[$reg_arr[$i].'totaal']?></p>
									<img src="<?=$static_url?>/images/Regioes/<?=$reg_arr[$i]?>.png" style="width: 100%; border-radius: 6px; height: 195px;">
								</div>
							</div>
							<script id="remove">
								$lock.push("<?=$lock?>");
								document.querySelector('#remove').outerHTML = '';
							</script>
							<?php
								}
							?>
						</div>
					</div>
				</td>
			</tr>
		</tbody>
		<tfoot>
			<tr>
				<td>
				<?php $rand = rand(0, 6); while ($rand == $time) { $rand = rand(0, 6); } ?>
				<div style="width: 97%; padding: 12px; margin-top: 10px; text-align: justify; height: 130px; font-size: 13px; border-bottom: 1px solid #577599;">
					<h5 style="margin: 0;"><b>Duração: </b></h5> <span id="duration_travel"><?=formatTime($prijs[$reg_arr[$rand].'_time_total']);?></span></p>
					<h5 style="margin: 0;"><b>Descrição:</b></h5><p id="text_travel"></p>
				</div>
					<input type="radio" name="wereld" value="<?=strtolower($reg_arr[$rand]);?>" checked="checked" style="display: none">
					<center><input type="submit" name="travel" value="Viajar para <?=($reg_arr[$rand]);?>" class="button" style="margin: 10px;"/></center>
				</td>
			</tr>
		</tfoot>
	</table>
	</form>
</div>
<script>
	var $carousel = $('.main-carousel');
    var regiao = ['Kanto', 'Johto', 'Hoenn', 'Sinnoh', 'Unova', 'Kalos', 'Alola'];
    var regiao_text = {
        0 : 'A região de Kanto é o lar de muitos Pokémon e tem uma rica história de criação de Pokémon com tecnologia. Os pesquisadores do Kanto foram excelentes em seu trabalho. Um pesquisador que trabalha para a organização maligna Team Rocket clonou Mew através de seus genes e criou o Pokémon Genético Mewtwo. Porygon, o Pokémon virtual, também foi criado por seres humanos em Kanto.',
        1 : '1500 anos antes dos tempos modernos, o lugar agora conhecido como Ruínas de Alfa foi construído em Johto, e o mais antigo sistema conhecido de escrita - o alfabeto latino moderno - foi desenvolvido. Embora ninguém saiba exatamente quem construiu as ruínas de Alph, as pesquisas indicam que as pessoas em questão eram uma civilização antiga que desejava coexistir com o enigmático Unown que tem grande semelhança com as letras do alfabeto. Não só essa tribo transmitia mensagens nas paredes das ruínas descrevendo o Unown, mas também ergueram uma estátua de Pokémon por uma razão desconhecida.',
        2 : 'Hoenn foi criado depois que Primal Groudon e Primal Kyogre foram formados. Primal Groudon levantou as massas terrestres e Primal Kyogre encheu os mares que mais tarde se tornariam Hoenn. O encontro destes dois criou uma grande batalha pela supremacia até que foi reprimida por Mega Rayquaza, enviando Primal Groudon e Primal Kyogre para cavernas onde descansaram.',
        3 : 'De acordo com vários mitos de Sinnoh, esta região foi a primeira de todas as regiões do mundo dos Pokémon a terem sido criadas. Em um vazio de nada, surgiu um único ovo, que então entrou em Arceus, o primeiro Pokémon a existir.',
        4 : 'Na mitologia, a Unova foi criada unindo os povos guerreiros da terra por heróis gêmeos. Eles usaram um único dragão há mais de 2500 anos atrás. Os irmãos começaram a argumentar sobre suas crenças; O irmão mais velho buscava a verdade e o irmão mais novo buscava ideais. Seus argumentos dividiram o dragão único em Reshiram, que compareceu com o irmão mais velho, e Zekrom, que compareceu com o irmão mais novo. Uma vez que ambos nasceram do mesmo dragão, nenhum deles poderia derrotar o outro e os irmãos declararam que não havia um lado direito.',
        5 : 'Há 3000 anos, uma guerra aconteceu em Kalos, e com isso, foi criada a Ultimate Weapon, uma arma que concedia a imortalidade. 2200 anos após o fim da guerra o Pokémon lendário Xerneas liberou energia vital em toda a região ao chegar ao fim de seu ciclo de vida. O pokémon lendário Yvetal, por sua vez, absorveu a energia vital e ambos entraram em estado de hibernação profunda, trazendo um estado de equilíbrio para a região.',
        6 : 'Alola é formada por quatro ilhas naturais e uma artificial, dizem as lendas que os guardiões das ilhas, os Tapu, enfrentaram Solgaleo e Lunala, expulsando-os. Muitos anos se passaram e apareceu um misterioso portal ligado à uma dimensão, onde nele surgiram algumas criaturas novas que foram apelidadas de Ultra Beasts.'
    };

    $('#text_travel').text(regiao_text[<?=$rand?>]);
	var $time = ["<?=formatTime($prijs['kanto_time_total'])?>", "<?=formatTime($prijs['johto_time_total'])?>", "<?=formatTime($prijs['hoenn_time_total'])?>", "<?=formatTime($prijs['sinnoh_time_total'])?>", "<?=formatTime($prijs['unova_time_total'])?>", "<?=formatTime($prijs['kalos_time_total'])?>", "<?=formatTime($prijs['alola_time_total'])?>"];

    $carousel.flickity({
        pageDots: false,
        initialIndex: <?=$rand?>
    });
	var flkty = $carousel.data('flickity');
	var $input = $('input[name="travel"]');

    $carousel.on('select.flickity', function() {
        $('input[name="wereld"]').val(regiao[flkty.selectedIndex].toLowerCase());

        $input.removeAttr('disabled');
        $('#text_travel').text(regiao_text[flkty.selectedIndex]);

        $('#duration_travel').text($time[flkty.selectedIndex]);
		if ($lock[flkty.selectedIndex] == 1) {
			if (regiao[flkty.selectedIndex] !== "<?=$gebruiker['wereld']?>") {
				$input.val('Viajar para '+regiao[flkty.selectedIndex]);
			} else {
				$input.val('Você já está em '+regiao[flkty.selectedIndex]+'!');
				$input.attr('disabled', 'true');
			}
		} else {
			$input.val('Você ainda não desbloqueou '+regiao[flkty.selectedIndex]+'!');
        	$input.attr('disabled', 'true');
		}
	});
	
	if ($lock[<?=$rand?>] == 0) {
		$input.val('Você ainda não desbloqueou '+regiao[flkty.selectedIndex]+'!');
        $input.attr('disabled', 'true');
	}
</script>
<?php if ($gebruiker['rank'] >= 5 && $gebruiker['in_hand'] != 0) { ?>
<div class="box-content" style="float: right; margin-top: 7px;width: 49%;display: inline-block;">
	<form method="post"><table class="general" width="100%">
		<thead>
			<tr><th colspan="4">Surfar <span style="cursor: pointer" title="Você só pode usar Pokémon acima do Level 80!">[?]</span></th></tr>
			<tr><th colspan="4" style="font-size: 12px;">Seu pokémon possui o golpe SURF? Você pode viajar de graça!</th></tr>
			<tr><th colspan="4"><img src="<?=$static_url?>/images/surf.gif"></th></tr>
		</thead>
		<tbody>
			<tr>
				<td><?=$txt['world'];?>:</td>
				<td><select name="wereld">
					<?php
						for ($i = 0; $i < count($reg_arr); $i++) {
							$wereld = ucfirst($reg_arr[$i]);
							$lock = $gebruiker[ucfirst($reg_arr[$i]).'_block'];
							$disabled = '';

							if ($lock == 0 || strtolower($gebruiker['wereld']) == $reg_arr[$i]) $disabled = ' disabled';
							
					?>
						<option value="<?=$reg_arr[$i]?>"<?=$disabled?>><?=$wereld?></option>
					<?php
						}
					?>
				</select></td>
				<td><?=$txt['pokemon'];?>:</td>
				<td><select name="pokemonid"><?php
					#Pokemon query ophalen
					while($pokemon = $pokemon_sql->fetch_assoc()) {
						$pokemon = pokemonei($pokemon, $txt);
						$pokemon['naam'] = pokemon_naam($pokemon['naam'],$pokemon['roepnaam']);
						if ($pokemon['baby'] != "Ja")
							echo "<option value=\"" . $pokemon['id'] . "\">" . $pokemon['naam'] . "</option>";
					}
					$pokemon_sql->data_seek(0);
				?></select></td>
			</tr>
		</tbody>
		<tfoot><tr><td align="right" colspan="4"><input type="submit" name="surf" value="<?=$txt['button_surf'];?>" class="button" style="margin: 5px"/></td></tr></tfoot>
	</table></form>
</div>
<div class="box-content" style="float: left; margin-top: 7px;width: 49%;display: inline-block;">
	<form method="post"><table class="general" width="100%">
		<thead>
			<tr><th colspan="4">Voar <span style="cursor: pointer" title="Você só pode usar Pokémon acima do Level 80!">[?]</span></th></tr>
			<tr><th colspan="4"  style="font-size: 12px;">Seu pokémon possui o golpe FLY? Você pode viajar de graça!</th></tr>
			<tr><th colspan="4"><img src="<?=$static_url?>/images/fly.gif"></th></tr>
		</thead>
		<tbody>
			<tr>
				<td><?=$txt['world'];?>:</td>
				<td><select name="wereld">
					<?php
						for ($i = 0; $i < count($reg_arr); $i++) {
							$wereld = ucfirst($reg_arr[$i]);
							$lock = $gebruiker[ucfirst($reg_arr[$i]).'_block'];
							$disabled = '';

							if ($lock == 0 || strtolower($gebruiker['wereld']) == $reg_arr[$i]) $disabled = ' disabled';
							
					?>
						<option value="<?=$reg_arr[$i]?>"<?=$disabled?>><?=$wereld?></option>
					<?php
						}
					?>
				</select></td>
				<td><?=$txt['pokemon'];?>:</td>
				<td><select name="pokemonid"><?php
					#Pokemon query ophalen
					while($pokemon = $pokemon_sql->fetch_assoc()) {
						$pokemon = pokemonei($pokemon, $txt);
						$pokemon['naam'] = pokemon_naam($pokemon['naam'],$pokemon['roepnaam']);
						if ($pokemon['baby'] != "Ja")
							echo "<option value=\"" . $pokemon['id'] . "\">" . $pokemon['naam'] . "</option>";
					}
					$pokemon_sql->data_seek(0);
				?></select></td>
			</tr>
		</tbody>
		<tfoot><tr><td align="right" colspan="4"><input type="submit" name="fly" value="<?=$txt['button_fly'];?>" class="button" style="margin: 5px"/></td></tr></tfoot>
	</table></form>
</div>
<div class="separator"></div>
<?php } ?>