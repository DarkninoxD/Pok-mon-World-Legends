<?php 
include("app/includes/resources/security.php");

echo addNPCBox(36, "Quem é esse Pokémon?", 'Você é uma PokéDex ambulante? Para ganhar aqui tem que ser! <br>Você pode tentar acertar o nome deste Pokémon uma vez a <b>cada hora</b> por <b>50 TICKETS</b>. <br>Caso você acerte, você ganha <b>100 TICKETS!</b><br> Clique <a href="./casino">AQUI</a> para <b>voltar</b> ao Cassino.');

	$keuzessql = DB::exQuery("SELECT wild_id, naam FROM pokemon_wild ORDER BY naam ASC");
	$pass = 1;
	
	//Kijken of je dit uur alweer mag
	$lasttime	            = strtotime($gebruiker['wiequiz']);
	$current_time           = strtotime(date('Y-m-d H:i:s'));
	$countdown_time         = 3600-($current_time-$lasttime);
	
	//Is de sessie leeg en zijn je punten op?
	if (empty($_SESSION['who_is_that_img']) && ($countdown_time > 0)) {
		echo '<div class="blue">'.$txt['alert_wait'].'</div>';
		$pass = 0;
	}
	//Als de sessie leeg is maar je nog wel punten hebt, nieuwe sessie maken:
	else if (empty($_SESSION['who_is_that_img']) && ($countdown_time <= 0)) {
		
		//Updaten dat de pokemon er is.
		$datenow = date('Y-m-d H:i:s');
		DB::exQuery("UPDATE gebruikers SET wiequiz = '".$datenow."' WHERE user_id = '".$_SESSION['id']."'");
			
		//Haal een pokemon uit de database
		$pkmn = DB::exQuery("SELECT wild_id FROM pokemon_wild ORDER BY rand() limit 1")->fetch_assoc();
		$shinyrand = rand(1,2);
		if ($shinyrand == 1) $status = 'shiny';
		else $status = 'pokemon';
		//Sessie zetten voor het plaatje	
		$_SESSION['who_is_that_img'] = $pkmn['wild_id'].'/'.$status;
	}
	
	//Code splitten, zodat informatie duidelijk word
  if (!empty($_SESSION['who_is_that_img'])) list ($answer, $status) = preg_split ('[/]', $_SESSION['who_is_that_img']);
	
	//Als er op de knop word geklikt
	if (isset($_POST['submit']) && ($pass != 0)) {
		if ($_POST['who'] == '0') {
			echo '<div class="red">'.$txt['alert_choose_a_pokemon'].'</div>';
		}
		else if (empty($_SESSION['who_is_that_img'])) {
			echo '<div class="red">'.$txt['alert_no_answer'].'</div>';
		}
		else{
			$pass = 0;

			DB::exQuery("UPDATE gebruikers SET `tickets` = `tickets`-'50' WHERE user_id = '".$_SESSION['id']."'");

			//Kijken of de speler het antwoord goed heeft
			if ($_POST['who'] == $answer) {
				echo '<div class="green">'.$txt['success_win'].'</div>';
				DB::exQuery("UPDATE gebruikers SET `tickets` = `tickets`+'100' WHERE user_id = '".$_SESSION['id']."'");
				rankerbij('whoisitquiz',$txt);
			}
			else{
				$answersql = DB::exQuery("SELECT naam FROM pokemon_wild WHERE wild_id = '".$answer."'")->fetch_assoc();
				echo '<div class="red">'.$txt['success_lose_1'].' '.$answersql['naam'].'. '.$txt['success_lose_2'].'</div>';
			}
			//Sessie leegmaken
			unset($_SESSION['who_is_that_img']);
		}
	}
	if ($pass != 0) {
	
?>
<div class="box-content" style="margin-bottom: 7px;"><h3 class="title" style="background: none"> Tickets no Inventário: <img src="<?=$static_url?>/images/icons/ticket.png" title="Tickets" />  <?= highamount($gebruiker['tickets']); ?></h3> </div>

<div class="box-content">
<form method="post">
<center>
<table class="general" width="100%" style="text-align: center">
		<thead>
			<tr>
				<th>Quem é este Pokémon?</th>
			</tr>
		</thead>
    <tr>
    	<td><div style="padding:0px 0px 10px 5px;"><img src="<?=$static_url?>/images/<?php echo $status; ?>/<?php echo $answer; ?>.gif" alt="<?php echo $txt['who_is_it']; ?>"></div></td>
    </tr>
    <tr>
    	<td>
<select name="who" class="text_select">
<option value="0"><?php echo $txt['choose_a_pokemon']; ?></option>
<?php 
	while($keuzes = $keuzessql->fetch_assoc()) {
	echo '<option value="'.$keuzes['wild_id'].'">'.$keuzes['naam'].'</option>';
}
?>
</select></td>
	</tr>
    <tr>
    	<td><input type="submit" name="submit" value="Tentar advinhar" class="button" <?php echo $disable; ?>></td>
    </tr>
</table>
</center>
</form>
</div>
<?php } else { ?>

<script type="text/javascript">  	
var int3 = <?php echo $countdown_time ?>; 
function aftellen3() {  	
	var inter3 = int3;  
	var uren3 = inter3 / 3600;  	
	var uur3 = Math.floor(uren3); 
	var gehad3 = uur3 * 3600;
	var moetnog3 = inter3 - gehad3;  
	var minuten3 = moetnog3 / 60;
	var mins3 = Math.floor(minuten3);  
	var gehadmin3 = mins3 * 60;  
	var moetnogg3 = moetnog3 - gehadmin3;  
	var secs3 = moetnogg3;  
	
	if (inter3 <= 0) {  
		clearInterval(interval3);
		document.location.reload()
	} else {  
		int3 = inter3 - 1;  

		document.getElementById('uur3').innerHTML = uur3;     
		document.getElementById('minuten3').innerHTML = mins3;  
		document.getElementById('seconden3').innerHTML = secs3;  
	}  
}
	aftellen3();  
	interval3 = setInterval('aftellen3();', 1000);
</script> 

<?php } ?>