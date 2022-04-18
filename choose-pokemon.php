<?php if ($gebruiker['sound'] == 'on') { ?>

<?php
}

#Script laden zodat je nooit pagina buiten de index om kan laden
require_once("app/includes/resources/security.php");

#Heeft de speler nog geen ei, dan pagina zien
if ($gebruiker['eigekregen'] == 0) {
	#Wil de speler een starter ei
	if (isset($_POST['submit'])) {
		$whocheck = array();
		$sql_started = DB::exQuery("SELECT `wild_id` FROM `pokemon_nieuw_starter`");
		while($started = $sql_started->fetch_assoc())	array_push($whocheck, $started['wild_id']);
		sort($whocheck);
	  
		if (!isset($_POST['who'])) echo '<div class="error">'.$txt['alert_no_pokemon'].'</div>';
		else if (!in_array($_POST['who'], $whocheck)) echo '<div class="error">'.$txt['alert_pokemon_unknown'].'</div>';
		else {
			#Willekeurige pokemon laden, en daarvan de gegevens
			$query = DB::exQuery("SELECT `pw`.`wild_id`,`pw`.`naam`,`pw`.`groei`,`pw`.`attack_base`,`pw`.`defence_base`,`pw`.`speed_base`,`pw`.`spc.attack_base`,`pw`.`spc.defence_base`,`pw`.`hp_base`,`pw`.`aanval_1`,`pw`.`aanval_2`,`pw`.`aanval_3`,`pw`.`aanval_4`,`pw`.`ability` FROM `pokemon_wild` AS `pw` WHERE `pw`.`wild_id`='".$_POST['who']."' LIMIT 1")->fetch_assoc();

			#De willekeurige pokemon in de pokemon_speler tabel zetten
			DB::exQuery("INSERT INTO `pokemon_speler` (`wild_id`,`aanval_1`,`aanval_2`,`aanval_3`,`aanval_4`) SELECT `wild_id`,`aanval_1`,`aanval_2`,`aanval_3`,`aanval_4` FROM `pokemon_wild` WHERE `wild_id`='".$query['wild_id']."'");

			#id opvragen van de insert hierboven
			$pokeid	= DB::insertID();

			#Heeft speler wel pokemon gekregen??
			if (is_numeric($pokeid)) DB::exQuery("UPDATE `gebruikers` SET `aantalpokemon`=`aantalpokemon`+'1',`eigekregen`='1' WHERE `user_id`='".$_SESSION['id']."' LIMIT 1");

			#Karakter kiezen 
			$karakter  = DB::exQuery("SELECT * FROM `karakters` ORDER BY rand() limit 1")->fetch_assoc();

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

			//Ability
			$ability = explode(',', $query['ability']);
      		$rand_ab = rand(0, (sizeof($ability) - 1));
      		$ability = $ability[$rand_ab];

			$date = date('Y-m-d H:i:s');

			#Alle gegevens van de pokemon opslaan
			DB::exQuery("UPDATE `pokemon_speler` SET `level`='5',`karakter`='".$karakter['karakter_naam']."',`expnodig`='".$experience['punten']."',`user_id`='".$_SESSION['id']."',`opzak`='ja',`opzak_nummer`='1',`gehecht`='1',`ei`='0',`ei_tijd`= NOW(),`attack_iv`='".$attack_iv."',`defence_iv`='".$defence_iv."',`speed_iv`='".$speed_iv."',`spc.attack_iv`='".$spcattack_iv."',`spc.defence_iv`='".$spcdefence_iv."',`hp_iv`='".$hp_iv."',`attack`='".$attackstat."',`defence`='".$defencestat."',`speed`='".$speedstat."',`spc.attack`='".$spcattackstat."',`spc.defence`='".$spcdefencestat."',`levenmax`='".$hpstat."',`leven`='".$hpstat."',`ability`='".$ability."',`capture_date`='".$date."' WHERE `id`='".$pokeid."' LIMIT 1");

			#Tekst laten zien
			exit(header("LOCATION: ./home"));
		}
	}
	if (isset($error)) echo $error;
	$per_line = 3;

	$avaliable_pokes = array();
	$sql_started = DB::exQuery("SELECT `pokemon_wild`.`naam`,`pokemon_wild`.`wild_id`,`pokemon_wild`.`type1`,`pokemon_wild`.`type2` FROM `pokemon_nieuw_starter` INNER JOIN `pokemon_wild` ON `pokemon_nieuw_starter`.`wild_id`=`pokemon_wild`.`wild_id` WHERE `pokemon_wild`.`wereld`='" . $gebruiker['wereld'] . "' ORDER BY `pokemon_wild`.`naam` ASC");
	while($started = $sql_started->fetch_assoc())	array_push($avaliable_pokes, $started);
	sort($avaliable_pokes);
?>

<div class="box-content">
<table class="general" width="100%">
	<thead>
		<tr><th colspan="<?=$per_line;?>"><?=$txt['starter_pokemon'];?></th></tr>
		<tr><th colspan="<?=$per_line;?>"><?=$txt['title_text'];?></th></tr>
	</thead>
	<tbody><tr>
		<script>	
			var $poke_array_id = [];
			var $poke_array_name = [];
			var $poke_array_type = [];
		</script>
		<td style="padding:0">
		<div class="main-carousel" style="height: 110px; position: relative">
	<?php
		foreach($avaliable_pokes as $key=>$value) {
		 	$value['type1'] = strtolower($value['type1']);
		  	$value['type2'] = strtolower($value['type2']);
		  	if (empty($value['type2']))	$value['type'] = '<table><tr><td><div class="type-icon type-'.$value['type1'].'">'.$value['type1'].'</div></td></tr></table>';
			else $value['type'] = '<table><tr><td><div class="type-icon type-'.$value['type1'].'">'.$value['type1'].'</div></td><td> <div class="type-icon type-'.$value['type2'].'">'.$value['type2'].'</div></td></tr></table>';
			
			?>
				<div class="carousel-cell" style="text-align: center;">
					<div style="display:table-cell; vertical-align:middle; min-width: 150px; height: 150px;">
						<img src="<?=$static_url . '/images/pokemon/' . $value['wild_id'] . '.gif'?>" />
						<script id="remove">
							$poke_array_id.push("<?=$value['wild_id']?>");
							$poke_array_name.push("<?=$value['naam']?>");
							$poke_array_type.push('<?=$value['type']?>');

							document.querySelector('#remove').outerHTML = '';
						</script>
					</div>
				</div>
				
			<?php
		}
	?></div>
	<div style="width: 100%; background: rgba(0, 0, 0, .3); position: relative; bottom: 0; text-align: center; height: 53px; padding-top: 3px; margin-top: -8px; border-bottom-right-radius: 2px;  border-bottom-left-radius: 2px">
		<div style="width: 100%; text-align: center; font-size: 17px; margin-top: 3px">
			<h4 id="poke_name" style="margin: 0; color: #eee; font-weight: bold;"></h4>
			<center><div id="poke_type"></div></center>
		</div>
	</div>
	</td></tr></tbody>
	<tfoot><tr><td align="center" colspan="3"><form action="./choose-pokemon" method="post" id="form"><input type="hidden" name="who" id="poke_id" value=""/><input type="submit" id="choose" name="submit" value="<?=$txt['button'];?>" style="margin: 6px" /></form></td></tr></tfoot>
</table></div></form>

<script>
	var $carousel = $('.main-carousel');
	var $poke_name = $('#poke_name');
	var $poke_link = $('#poke_type');
	var $poke_id = $('#poke_id');
	var $submit = $('#choose');
	var $form = $('#form');
	var frases = [
		'Parece que ele gostou de você.',
		'Que bela escolha!',
		'Uma escolha interessante.',
		'Gostei do vínculo entre vocês.',
		'Ele está meio tímido, mas parece que gostou de você!'
	];
	let f1 = frases[Math.floor(Math.random() * frases.length)];
	let f2 = frases[Math.floor(Math.random() * frases.length)];
	let f3 = frases[Math.floor(Math.random() * frases.length)];
	
	var frase = [f1,f2,f3];

	var $car = $carousel.flickity({
		cellAlign: 'center',
		contain: false,
		pageDots: false,
		wrapAround: false,
		autoPlay: false
	});

	var flkty = $carousel.data('flickity');

	$carousel.on('select.flickity', function() {
		$poke_name.text($poke_array_name[flkty.selectedIndex]);
		$poke_link.html($poke_array_type[flkty.selectedIndex]);
		$poke_id.val ($poke_array_id[flkty.selectedIndex]);
		$submit.val('Escolher '+$poke_array_name[flkty.selectedIndex]);
		$form.attr('onsubmit', "return confirm('"+frase[flkty.selectedIndex]+" Você deseja realmente iniciar sua jornada com "+$poke_array_name[flkty.selectedIndex]+"?')");
	});

	$poke_name.text($poke_array_name[0]);
	$poke_link.html($poke_array_type[0]);
	$poke_id.val ($poke_array_id[0]);
	$submit.val('Escolher '+$poke_array_name[0]);
	$form.attr('onsubmit', "return confirm('"+frase[0]+" Você deseja realmente iniciar sua jornada com "+$poke_array_name[0]+"?')");

	$car.resize();
	
	wlSound('professor-oak', <?=$gebruiker['volume']?>, true);
</script>
<?php } else	exit(header("LOCATION: ./home")); ?>