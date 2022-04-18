<?php
//Script laden zodat je nooit pagina buiten de index om kan laden
include("app/includes/resources/security.php");

//Admin controle
if ($gebruiker['admin'] < 1) { header('location: ./home'); exit; }

if (isset($_POST['geef'])) {  

 $infoxx = DB::exQuery("SELECT user_id from gebruikers where username='".$_POST['user_id']."'")->fetch_assoc();
																		
											
	//Gegevens laden van speler
	$aantal = DB::exQuery("SELECT `user_id` FROM `pokemon_speler` WHERE `user_id`='".$infoxx['user_id']."' AND `opzak`='ja'")->num_rows;

	if ($_POST['user_id'] == '') echo '<div class="red"> Digite o nome de algum treinador.</div>';
	else if ($_POST['wild_id'] == 'none') echo '<div class="red"> Selecione algum pokémon.</div>';
	else if ($aantal == 6) echo '<div class="red"> O treinador já tem 6 pokémons com ele.</div>';
	else if (DB::exQuery("SELECT user_id FROM gebruikers WHERE user_id='".$infoxx['user_id']."'")->num_rows == 0) echo '<div class="red"> O treinador não existe.</div>';
	else{
	    $opzak = $aantal+1;
	    
DB::exQuery("UPDATE pokemon_speler SET can_trade='0', user_id='".$infoxx['user_id']."',opzak_nummer='$opzak' WHERE id = '".$_POST['wild_id']."' AND user_id = '".$_SESSION['id']."'");
	
	echo '<div class="green"> Pokémon entregue com sucesso.</div>';

  }
}


?>

<form method="post">
<center>
<p>Entregue pokémons prêmio para os jogadores, lembre-se que o pokémon vai como intransferível e são mostrados apenas para enviar os pokémons que estão em seu time.</p>
<table width="350">
	<tr>
    	<td width="150">Nome do treinador:</td>
        <td width="200"><input type="text" name="user_id" class="text_long" value="<?php if ($_GET['player'] != '') echo $_GET['player']; ?>"></td>
    </tr>
    <tr>
    	<td>Pokémon:</td>
        <td><select name="wild_id" class="text_select">
				<option value="none">Escolha</option>
				<?php 
                   $allpokemonsql = DB::exQuery("SELECT pw.naam, pw.type1, pw.type2, pw.zeldzaamheid, pw.groei, pw.aanval_1, pw.aanval_2, pw.aanval_3, pw.aanval_4, ps.* FROM pokemon_wild AS pw INNER JOIN pokemon_speler AS ps ON ps.wild_id = pw.wild_id WHERE ps.user_id='".$_SESSION['id']."' AND ps.opzak='ja' ORDER BY ps.opzak_nummer ASC");
                  while($allpokemon = $allpokemonsql->fetch_assoc()) {
                    $allpokemon['naam_goed'] = computer_naam($allpokemon['naam']);
                      echo '<option value="'.$allpokemon['id'].'">'.$allpokemon['naam_goed'].'</option>';
                  }
                ?>
			</select></td>
    </tr>
    <tr>
    	<td>&nbsp;</td>
        <td><input type="submit" name="geef" value="Entregar!" class="button" /></td>
    </tr>
</table>
</center>
</form>