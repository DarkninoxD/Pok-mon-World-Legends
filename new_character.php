<?php
require_once('app/includes/resources/security-account.php');

$sql = DB::exQuery("SELECT `user_id` FROM `gebruikers` WHERE `acc_id`=" . $_SESSION['acc_id']);
if ($sql->num_rows >= 7)	exit(header("LOCATION: ./my_characters"));
else {
	if (isset($_POST['submit'])) {
		$inlognaam = $_POST['inlognaam'];
		$wereld = $_POST['wereld'];
		$ip = $_SERVER['REMOTE_ADDR'];
		$character = $_POST['character'];

		#Is er de afgelopen week al een account gemaakt?
		if (empty($inlognaam))	$alert = '<div class="red">'.$txt['alert_no_username'].'</div>';
		else if (strlen(trim($inlognaam)) < 4)	$alert = '<div class="red">O USUÁRIO DEVE CONTER AO MENOS 4 CARACTERES!</div>';
	else if (strlen(trim($inlognaam)) > 12)	$alert = '<div class="red">O USUÁRIO NÃO DEVE CONTER MAIS DE 12 CARACTERES!</div>';
		else if (!preg_match('/^([a-zA-Z0-9]+)$/is', $inlognaam))	$alert = '<div class="red">'.$txt['alert_username_incorrect_signs'].'</div>';
		else if (DB::exQuery("SELECT `username` FROM `gebruikers` WHERE `username`='" . $inlognaam . "' LIMIT 1")->num_rows != 0)	$alert = '<div class="red">'.$txt['alert_username_exists'].'</div>';
		else if (DB::exQuery("SELECT `id` FROM `characters` WHERE `naam`='" . $character . "' LIMIT 1") != 1)	$alert = '<div class="red">'.$txt['alert_character_invalid'].'</div>';
		else if (!isset($wereld))	$alert = '<div class="red">'.$txt['alert_no_beginworld'].'</div>';
		else if ($wereld != 'Kanto' && $wereld != 'Johto' && $wereld != 'Hoenn' && $wereld != 'Sinnoh' && $wereld != 'Unova' && $wereld != 'Kalos' && $wereld != 'Alola')	$alert = '<div class="red">'.$txt['alert_world_invalid'].'</div>';
		else if ($sql->num_rows >= 2 AND $rekening['gold'] < 10)	$alert = '<div class="red">Você não tem golds suficientes!</div>';
		else {
			#Gebruiker in de database
			$date_lo = date('d/m/Y');
			$date_loh = date('H:i:s');
			$unlock = $wereld.'_block';

			DB::exQuery("INSERT INTO `gebruikers` (`ultimo_login`, `ultimo_login_hour`, `acc_id`,`character`,`username`,`datum`,`aanmeld_datum`,`ip_aangemeld`,`wereld`, `$unlock`) VALUES ('".$date_lo."', '".$date_loh."', " . $rekening['acc_id'] . ",'" . $character . "','" . $inlognaam . "', NOW(), NOW(),'" . $ip . "','" . $wereld . "', '1')");
			if ($sql->num_rows >= 2) DB::exQuery("UPDATE `rekeningen` SET `gold`=`gold`-10 WHERE `acc_id`={$rekening['acc_id']}");
			
			#id opvragen van de gebruiker tabel van de gebruiker
			$user_id = DB::insertID();

			#Speler opslaan in de gebruikers_item tabel
			DB::exQuery("INSERT INTO `gebruikers_item` (`user_id`) VALUES (" . $user_id . ")");
			#Speler opslaan in de gebruikers_item tabel
			DB::exQuery("INSERT INTO `gebruikers_badges` (`user_id`) VALUES (" . $user_id . ")");
			#Speler opslaan in de gebruikers_tmhm tabel
			DB::exQuery("INSERT INTO `gebruikers_tmhm` (`user_id`) VALUES (" . $user_id . ")");

			#Bericht opstellen
			exit(header("LOCATION: ./my_characters"));
		}
	}
?>

<div class="blue">Para voltar à seleção de Personagens é só clicar <a href="./my_characters">AQUI</a>.</div>

<style>
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

<form method="post" action="./new_character" autocomplete="off">
	<?php if (!empty($alert))  echo $alert;?>
	<div class="box-content"><table class="general" width="100%">
		<thead>
			<tr><th colspan="9"><?=$txt['title_text'];?></th></tr>
			<tr><th colspan="9">Você pode criar 2 personagem gratuitamente, após será cobrado uma taxa de <img src="<?=$static_url;?>/images/icons/gold.png" width="14" /> 10 por cada novo personagem!!!</th></tr>
		</thead>
		<tbody>
			<tr>
				<td width="50px"><?=str_replace(':', '', $txt['username']);?>:</td>
				<td width="18px" align="center"><img src="<?=$static_url;?>/images/icons/user.png" width="16" height="16" class="imglower" /></td>
				<td><input name="inlognaam" type="text" value="<?=$_POST['inlognaam'];?>" style="width: 130px;" required maxlength="12" minlength="4" /></td>
				<td width="50px"><?=$txt['beginworld'];?>:</td>
				<td width="18px" align="center"><img src="<?=$static_url;?>/images/icons/map.png" width="16" height="16" class="imglower" /></td>
				<td><select name="wereld" style="width: 137px;" required>
					<option <?php if (isset($_POST['wereld']) && $_POST['wereld'] == "Kanto") { echo 'selected'; } ?>>Kanto</option>
					<option <?php if (isset($_POST['wereld']) && $_POST['wereld'] == "Johto") { echo 'selected'; } ?>>Johto</option>
					<option <?php if (isset($_POST['wereld']) && $_POST['wereld'] == "Hoenn") { echo 'selected'; } ?>>Hoenn</option>
					<option <?php if (isset($_POST['wereld']) && $_POST['wereld'] == "Sinnoh") { echo 'selected'; } ?>>Sinnoh</option>
					<option <?php if (isset($_POST['wereld']) && $_POST['wereld'] == "Unova") { echo 'selected'; } ?>>Unova</option>
					<option <?php if (isset($_POST['wereld']) && $_POST['wereld'] == "Kalos") { echo 'selected'; } ?>>Kalos</option>
					<option <?php if (isset($_POST['wereld']) && $_POST['wereld'] == "Alola") { echo 'selected'; } ?>>Alola</option>
				</select></td>
			</tr>
			<tr>
				<td class="no-padding" colspan="9">
						<script>
							var $character_array_name = [];
						</script>
						<div class="main-carousel"><?php
						$charactersql = DB::exQuery("SELECT naam FROM characters ORDER BY naam ASC");
						while($character = $charactersql->fetch_assoc()) {
							$type = '';
							echo "<div class='carousel-cell' style=\"padding-top: 30px;display:inline-block; margin:1.8px\">";
							echo "<img src=\"" . $static_url . "/images/characters/" . $character['naam'] . "/Thumb.png\" id=\"trainer_infos\" width=\"130\" height=\"130\" /><br>";
							echo "</div>";
							?>
							<script id="remove">
								$character_array_name.push("<?=$character['naam']?>");

								document.querySelector('#remove').outerHTML = '';
							</script>
							<?php
						}
					?>

					</div>
					<div style="width: 100%; background: rgba(0, 0, 0, .3); position: relative; bottom: 0; text-align: center; height: 53px; padding-top: 3px; margin-top: -35px; border-bottom-right-radius: 2px;  border-bottom-left-radius: 2px">
						<div style="width: 100%; text-align: center; font-size: 17px; margin-top: 14px">
							<h4 id="character_name" style="margin: 0; color: #eee; font-weight: bold;"></h4>
						</div>
					</div>
					</td></tr></tbody>
					<tfoot>
						<tr style="text-align: center; font-size: 13px">
							<td colspan="9">
								<input type="hidden" id="character" name="character" value="Alexa">
								<input type="submit" id="submit" name="submit" style="margin: 6px" value="CRIAR PERSONAGEM" class="button">
							</td>
						</tr>
					</tfoot>
			</table>
</div>
</form>

<script type="text/javascript">
	var $carousel = $('.main-carousel');
	var $character_name = $('#character_name');
	var $user_id = $('#character');

	var $car = $carousel.flickity({
		cellAlign: 'center',
		contain: false,
		pageDots: false,
		wrapAround: true
	});

	var flkty = $carousel.data('flickity');

	$carousel.on('select.flickity', function() {
		$character_name.text($character_array_name[flkty.selectedIndex]);
		$user_id.val($character_array_name[flkty.selectedIndex]);
	});

	$character_name.text($character_array_name[0]);
	$user_id.val($character_array_name[0]);

	$car.resize();
	
	wlSound('select-player', 30, true);
</script>
<?php } ?>