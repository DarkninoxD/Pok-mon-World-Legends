<?php
include('app/includes/resources/security.php');
$title = 'Insígnias';
$text = 'Essas são suas insígnias. Elas são frutos do seu trabalho duro como <b>treinador Pokémon</b>. <br>Você pode obtê-las batalhando contra os <a href="./attack/gyms"> Líderes de Ginásio </a> em cada região.';
echo addNPCBox(19, $title, $text);

if ($gebruiker['Badge case'] == 0) {
	echo '<div class="red">Compre uma Badge Case para armazenar suas Insígnias!</div>';
}
	  
if ($gebruiker['rank'] >= 3) {
	if ($gebruiker['Badge case'] == 1) {

$kanto_arr = ['Boulder', 'Cascade', 'Thunder', 'Rainbow', 'Marsh', 'Soul', 'Volcano', 'Earth'];
$johto_arr = ['Zephyr', 'Hive', 'Plain', 'Fog', 'Storm', 'Mineral', 'Glacier', 'Rising'];
$hoenn_arr = ['Stone', 'Knuckle', 'Dynamo', 'Heat', 'Balance', 'Feather', 'Mind', 'Rain'];
$sinnoh_arr = ['Coal', 'Forest', 'Cobble', 'Fen', 'Relic', 'Mine', 'Icicle', 'Beacon'];
$unova_arr = ['Trio', 'Basic', 'Insect', 'Bolt', 'Quake', 'Jet', 'Freeze', 'Legend'];
$kalos_arr = ['Bug', 'Cliff', 'Rumble', 'Plant', 'Voltage', 'Fairy', 'Psychic', 'Iceberg'];
$alola_arr = ['Melemele Normal', 'Akala Water', 'Akala Fire', 'Akala Grass', 'Ulaula Electric', 'Ulaula Ghost', 'Poni Fairy', 'Poni Ground'];

$badge = DB::exQuery("SELECT * FROM gebruikers_badges WHERE user_id = '".$_SESSION['id']."'")->fetch_assoc();
?>

<?php

echo '<div class="box-content" style="margin-top: 7px"><table class="general" width="100%"><thead><tr><th>Insígnias</th></tr></thead>
<tr>
<td colspan="3" onclick="wlBadges(\'#kanto\')" style="cursor: pointer"><h3 style="margin: 0"><b><center>'.$txt['badges'].' Kanto:</center></b></h3></td>
</tr>
<tr id="kanto">
<td colspan="3" align="center">';
			for ( $i = 0; $i < sizeof($kanto_arr); $i++ ) {
				$name = $kanto_arr[$i];

				if ($badge[$kanto_arr[$i]] == 1) { 
					echo '<img src="'.$static_url.'/images/badges/pixel/'.$name.'.png"  alt="'.$name.' Badge" title="'.$name.' Badge adquirida!" />';	
				} else {
					echo '<img src="'.$static_url.'/images/badges/pixel/'.$name.'.png"  alt="'.$name.' Badge" title="'.$name.' Badge não adquirida!" style="filter: grayscale(100%);" />';
				}
			}
echo '</td></tr>
<tr>
<td colspan="3" onclick="wlBadges(\'#johto\')" style="cursor: pointer"><h3 style="margin: 0"><b><center>'.$txt['badges'].' Johto:</center></b></h3></td>
</tr>
<tr id="johto">
<td colspan="3" align="center">';
			for ( $i = 0; $i < sizeof($johto_arr); $i++ ) {
				$name = $johto_arr[$i];

				if ($badge[$johto_arr[$i]] == 1) { 
					echo '<img src="'.$static_url.'/images/badges/pixel/'.$name.'.png"  alt="'.$name.' Badge" title="'.$name.' Badge adquirida!" />';	
				} else {
					echo '<img src="'.$static_url.'/images/badges/pixel/'.$name.'.png"  alt="'.$name.' Badge" title="'.$name.' Badge não adquirida!" style="filter: grayscale(100%);" />';
				}
			}
echo '</td></tr>
<tr>
<td colspan="3" onclick="wlBadges(\'#hoenn\')" style="cursor: pointer"><h3 style="margin: 0"><b><center>'.$txt['badges'].' Hoenn:</center></b></h3></td>
</tr>
<tr id="hoenn">
<td colspan="3" align="center">';

			for ( $i = 0; $i < sizeof($hoenn_arr); $i++ ) {
				$name = $hoenn_arr[$i];

				if ($badge[$hoenn_arr[$i]] == 1) { 
					echo '<img src="'.$static_url.'/images/badges/pixel/'.$name.'.png"  alt="'.$name.' Badge" title="'.$name.' Badge adquirida!" />';	
				} else {
					echo '<img src="'.$static_url.'/images/badges/pixel/'.$name.'.png"  alt="'.$name.' Badge" title="'.$name.' Badge não adquirida!" style="filter: grayscale(100%);" />';
				}
			}
		

echo '</td></tr>
<tr>
<td colspan="3" onclick="wlBadges(\'#sinnoh\')" style="cursor: pointer"><h3 style="margin: 0"><b><center>'.$txt['badges'].' Sinnoh:</center></b></h3></td>
</tr>
<tr id="sinnoh">
<td colspan="3" align="center">';

		for ( $i = 0; $i < sizeof($sinnoh_arr); $i++ ) {
			$name = $sinnoh_arr[$i];

			if ($badge[$sinnoh_arr[$i]] == 1) { 
				echo '<img src="'.$static_url.'/images/badges/pixel/'.$name.'.png"  alt="'.$name.' Badge" title="'.$name.' Badge adquirida!" />';	
			} else {
				echo '<img src="'.$static_url.'/images/badges/pixel/'.$name.'.png"  alt="'.$name.' Badge" title="'.$name.' Badge não adquirida!" style="filter: grayscale(100%);" />';
			}
		}
			

echo '</td></tr>
<tr>
<td colspan="3" onclick="wlBadges(\'#unova\')" style="cursor: pointer"><h3 style="margin: 0"><b><center>'.$txt['badges'].' Unova:</center></b></h3></td>
</tr>
<tr id="unova">
<td colspan="3" align="center">';

		for ( $i = 0; $i < sizeof($unova_arr); $i++ ) {
			$name = $unova_arr[$i];

			if ($badge[$unova_arr[$i]] == 1) { 
				echo '<img src="'.$static_url.'/images/badges/pixel/'.$name.'.png"  alt="'.$name.' Badge" title="'.$name.' Badge adquirida!" />';	
			} else {
				echo '<img src="'.$static_url.'/images/badges/pixel/'.$name.'.png"  alt="'.$name.' Badge" title="'.$name.' Badge não adquirida!" style="filter: grayscale(100%);" />';
			}
		}
			
			

echo '</td></tr>
<tr>
<td colspan="3" onclick="wlBadges(\'#kalos\')" style="cursor: pointer"><h3 style="margin: 0"><b><center>'.$txt['badges'].' Kalos:</center></b></h3></td>
</tr>
<tr id="kalos">
<td colspan="3" align="center">';

		for ( $i = 0; $i < sizeof($kalos_arr); $i++ ) {
			$name = $kalos_arr[$i];

			if ($badge[$kalos_arr[$i]] == 1) { 
				echo '<img src="'.$static_url.'/images/badges/pixel/'.$name.'.png"  alt="'.$name.' Badge" title="'.$name.' Badge adquirida!" />';	
			} else {
				echo '<img src="'.$static_url.'/images/badges/pixel/'.$name.'.png"  alt="'.$name.' Badge" title="'.$name.' Badge não adquirida!" style="filter: grayscale(100%);" />';
			}
		}
			
			
echo '</td></tr>
<tr>
<td colspan="3" onclick="wlBadges(\'#alola\')" style="cursor: pointer"><h3 style="margin: 0"><b><center>'.$txt['badges'].' Alola:</center></b></h3></td>
</tr>
<tr id="alola">
<td colspan="3" align="center">';

		for ( $i = 0; $i < sizeof($alola_arr); $i++ ) {
			$name = $alola_arr[$i];

			if ($badge[$alola_arr[$i]] == 1) { 
				echo '<img src="'.$static_url.'/images/badges/pixel/'.$name.'.png"  alt="'.$name.' Badge" title="'.$name.' Badge adquirida!" />';	
			} else {
				echo '<img src="'.$static_url.'/images/badges/pixel/'.$name.'.png"  alt="'.$name.' Badge" title="'.$name.' Badge não adquirida!" style="filter: grayscale(100%);" />';
			}
		}
			
echo '</table></div>';
?>
<script>
	function wlBadges(el) {
		$(el).toggleClass('wlBadges');
	}
</script>

<?php } } else { ?>
<div class="red">RANK MÍNIMO PARA VER SUAS INSÍGNIAS: 3 - COACH. CONTINUE UPANDO PARA LIBERAR!</div>
<?php } ?>