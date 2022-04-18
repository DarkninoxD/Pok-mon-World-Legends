<?php		
//Script laden zodat je nooit pagina buiten de index om kan laden
include("app/includes/resources/security.php");

//Admin controle
if ($gebruiker['admin'] < 1) { header('location: ./home'); exit; }

###################################################################

if (isset($_GET['player'])) {
	  
    //Gegevens laden van de ingevoerde gebruiker
    //GOLD E EMAIL
	  $profiel = (DB::exQuery("SELECT `username`, `datum`, `ip_aangemeld`, `ip_ingelogd`, `premiumaccount`, `silver`, `admin`, `wereld`, `online`, `voornaam`, `achternaam`, `character`, `profiel`, `teamzien`, `rank`, `aantalpokemon`, `gewonnen`, `verloren`, COUNT(DISTINCT `user_id`) AS `aantal` FROM `gebruikers` WHERE `username`='".$_GET['player']."' GROUP BY `user_id`"))->fetch_assoc();
	  
	  //is er geen player ingevuld dan terug naar home
	  if ($profiel['aantal'] != 1) header("Location: ./home");
    //Anders de pagina
    else{
		
		$plaatssql = DB::exQuery("SELECT `username` FROM `gebruikers` WHERE `account_code`='1'AND admin = '0' ORDER BY `rank` DESC, `rankexp` DESC, `username` ASC");
		
	  //Default Values
      $medaille = "";
      $plaatje = "images/icons/status_offline.png";
      $online  = "Offline";
	  
	  for($j=1; $plaats = ($plaatssql)->fetch_assoc(); $j++)
        if ($profiel['username'] == $plaats['username']) $voortgang = $j;
      
	    $voortgangplaats = $voortgang."<sup>e</sup>";
	  
      if ($voortgang == '1') {
		    $medaille = "<img src='images/icons/plaatsnummereen.png'>";
		    $voortgangplaats = $voortgang."<sup>ste</sup>";
		  }
		  else if ($voortgang == '2')
		    $medaille = "<img src='images/icons/plaatsnummertwee.png'>";
		  else if ($voortgang == '3')
		    $medaille = "<img src='images/icons/plaatsnummerdrie.png'>";
		  else if ($voortgang > '3' && $voortgang <= '10')
		    $medaille = "<img src='images/icons/gold.png'>";
		  else if ($voortgang > '10' && $voortgang <= '30')
		    $medaille = "<img src='images/icons/silver.png'>";
		  else if ($voortgang > '30' && $voortgang <= '50')
		    $medaille = "<img src='images/icons/bronze.png'>";
		else if ($voortgang =='')
			$voortgangplaats = "<b>Admin.</b>";
			
		  //Tijd voor plaatje
		  $tijd = time();
		  if (($profiel['online']+300) > $tijd) {
			$plaatje = "images/icons/status_online.png";
			$online  = "Online";
		  }
	  
        $character 	  = $_POST['character'] == ''   ? $profiel['character']   : $_POST['character'];
		$teamzien     = $_POST['teamzien'] == '' ? $profiel['teamzien'] : $_POST['teamzien'];
		$rank     = $_POST['rank'] == '' ? $profiel['rank'] : $_POST['rank'];
		
	  ##### Als er op de knop is gedrukt #####
	  
	  if (isset($_POST['change'])) {
	  	    $quem = $gebruiker['username'];
	  	    $acao = "Editou Perfil de ".$_POST['username']."";
	  	    $mensagem = "O administrador ".$gebruiker['username']." modificou o perfil do jogador ".$_POST['username'].".";
	  	    salvaLogAdmin($quem,$acao,$mensagem);
		    DB::exQuery("UPDATE `gebruikers` SET `character`='".$_POST['character']."', `username`='".$_POST['username']."', `premiumaccount`='".$_POST['premiumaccount']."', `voornaam`='".$_POST['voornaam']."', `achternaam`='".$_POST['achternaam']."', `wereld`='".$_POST['wereld']."', `datum`='".$_POST['datum']."', `rank`='".$_POST['rank']."', `aantalpokemon`='".$_POST['aantalpokemon']."', `gewonnen`='".$_POST['gewonnen']."', `verloren`='".$_POST['verloren']."', `email`='".$_POST['email']."', `ip_aangemeld`='".$_POST['ip_aangemeld']."' , `ip_ingelogd`='".$_POST['ip_ingelogd']."', `teamzien`='".$_POST['teamzien']."', `silver`='".$_POST['silver']."', `gold`='".$_POST['gold']."', `profiel`='".$_POST['profiel']."' WHERE `username`='".$_GET['player']."'");
	  
	  		echo '<div class="green">Treinador '.$_GET['player'].' atualizado!</div>';
	  }
?>
<form method="post">
    <center>
      <table width="600" border="0" cellspacing="0" cellpadding="0">
        <tr>
          <td width="180" rowspan="23" valign="top"><img src="images/avatar/<?php echo $profiel['character']; ?>.png" /><br />
      <select name="character" value="<?php if (isset($_POST ['character']) && !empty($_POST ['character'])) { echo $_POST ['character']; }?>" class="text_select">
      <?php
      $charactersql = DB::exQuery("SELECT naam FROM characters ORDER BY id ASC");
      
      if (isset($_POST['character'])) {
        $characterr = $_POST['character'];
      }
      else{
        $characterr = $profiel['character'];
      } 
  
      while($character = ($charactersql)->fetch_assoc()) {
        if ($character['naam'] == $characterr) {
          $selected = 'selected';
        }
        else{
          $selected = '';
        }
        echo '<option value="'.$character['naam'].'" '.$selected.'>'.$character['naam'].'</option>';
      }
      ?>
      </select></td>
        </tr>
        <tr>
          <td height="20"><strong>Usuário:</strong></td>
          <td><input name="username" type="text" class="text_long" value="<?php if (!isset($_POST['change'])) echo $profiel['username']; else echo $_POST['username']; ?>" maxlength="10" /></td>
        </tr>
        <tr>
          <td height="20"><strong>Dias premium:</strong></td>
          <td><input name="premiumaccount" type="text" class="text_long" value="<?php if (!isset($_POST['change'])) echo $profiel['premiumaccount']; else echo $_POST['premiumaccount']; ?>" maxlength="4" /></td>
        </tr>
        <tr>
          <td height="20"><strong>Nome:</strong></td>
          <td><input name="voornaam" type="text" class="text_long" value="<?php if (!isset($_POST['change'])) echo $profiel['voornaam']; else echo $_POST['voornaam']; ?>" maxlength="12" /></td>
        </tr>
        <tr>
          <td height="20"><strong>Sobrenome:</strong></td>
          <td><input name="achternaam" type="text" class="text_long" value="<?php if (!isset($_POST['change'])) echo $profiel['achternaam']; else echo $_POST['achternaam']; ?>" maxlength="12" /></td>
        </tr>
        <tr>
          <td height="20"><strong>Inicio:</strong></td>
          <td><input type="text" name="datum" value="<?php if (!isset($_POST['change'])) echo $profiel['datum']; else echo $_POST['datum']; ?>" class="text_long" /></td>
        </tr>
        <tr>
          <td height="20" colspan="2">&nbsp;</td>
        </tr>
        <?php
		
		##Maken dat hij goeie wereld pakt!
		//standaardwaarden
		$kantoselected = '';
		$johtoselected = '';
		$hoennselected = '';
		$sinnohselected = '';
		$kalosselected = '';
		
		if (isset($_POST['change'])) {
			if ($_POST['wereld'] == 'Kanto') $kantoselected = 'selected';
			else if ($_POST['wereld'] == 'Johto') $johtoselected = 'selected';
			else if ($_POST['wereld'] == 'Hoenn') $hoennselected = 'selected';
			else if ($_POST['wereld'] == 'Sinnoh') $sinnohselected = 'selected';
			else if ($_POST['wereld'] == 'Unova') $unovaselected = 'selected';
			else if ($_POST['wereld'] == 'Kalos') $kalosselected = 'selected';
		}
		else{
			if ($profiel['wereld'] == 'Kanto') $kantoselected = 'selected';
			else if ($profiel['wereld'] == 'Johto') $johtoselected = 'selected';
			else if ($profiel['wereld'] == 'Hoenn') $hoennselected = 'selected';
			else if ($profiel['wereld'] == 'Sinnoh') $sinnohselected = 'selected';
			else if ($profiel['wereld'] == 'Unova') $unovaselected = 'selected';
			else if ($profiel['wereld'] == 'Kalos') $kalosselected = 'selected';
		}
		
		?>
        
        <tr>
          <td height="20"><strong>Zona:</strong></td>
          <td height="20"><select name="wereld" class="text_select">
          					<option value="Kanto" <?php echo $kantoselected; ?>>Kanto</option>
                            <option value="Johto" <?php echo $johtoselected; ?>>Johto</option>
                            <option value="Hoenn" <?php echo $hoennselected; ?>>Hoenn</option>
                            <option value="Sinnoh" <?php echo $sinnohselected; ?>>Sinnoh</option>
                            <option value="Unova" <?php echo $unovaselected; ?>>Unova</option>
                            <option value="Kalos" <?php echo $kalosselected; ?>>Kalos</option>
                          </select></td>
        </tr>
        <tr>
          <td height="20"><strong>Silvers:</strong></td>
          <td height="20"><input type="text" name="silver" value="<?php if (!isset($_POST['change'])) echo $profiel['silver']; else echo $_POST['silver']; ?>" class="text_long" /></td>
        </tr>
        
        <tr>
          <td height="20" colspan="2">&nbsp;</td>
        </tr>
        <tr>
          <td height="20"><strong>Rank:</strong></td>
          <td><select name="rank" class="text_select">
    <?php
    $ranksql = DB::exQuery("SELECT naam, ranknummer FROM rank ORDER BY ranknummer DESC"); 

    if (isset($rank))
      $rankk = $rank;
    else
      $rankk = $profiel['rank'];

    while($rank = ($ranksql)->fetch_assoc()) {
      $selected = '';
      if ($rank['ranknummer'] == $rankk)
        $selected = 'selected';
        
      echo '<option value="'.$rank['ranknummer'].'" '.$selected.'>'.$rank['naam'].'</option>';
    }
    ?>
    </select></td>
        </tr>
        <tr>
          <td height="20"><strong>Rank:</strong></td>
          <td><?php echo $medaille; ?> <?php echo $voortgangplaats; ?></td>
        </tr>
        <tr>
          <td height="20"><strong>Pokemons:</strong></td>
          <td><input name="aantalpokemon" type="text" class="text_long" value="<?php if (!isset($_POST['change'])) echo $profiel['aantalpokemon']; else echo $_POST['aantalpokemon']; ?>" maxlength="3" /></td>
        </tr>
        <tr>
          <td height="20"><strong>Batalhas Ganhas:</strong></td>
          <td><input name="gewonnen" type="text" class="text_long" value="<?php if (!isset($_POST['change'])) echo $profiel['gewonnen']; else echo $_POST['gewonnen']; ?>" maxlength="9" /></td>
        </tr>
        <tr>
          <td height="20"><strong>Batalhas Perdidas:</strong></td>
          <td><input name="verloren" type="text" class="text_long" value="<?php if (!isset($_POST['change'])) echo $profiel['verloren']; else echo $_POST['verloren']; ?>" maxlength="9" /></td>
        </tr>
        <tr>
          <td height="20"><strong>Status:</strong></td>
          <td><img src="<?php echo $plaatje; ?>" /> <?php echo $online; ?></td>
        </tr>
        <tr>
          <td height="20" colspan="2">&nbsp;</td>
        </tr>
        <tr>
          <td height="20"><strong>Ip logado:</strong></td>
          <td><input name="ip_aangemeld" type="text" class="text_long" value="<?php if (!isset($_POST['change'])) echo $profiel['ip_aangemeld']; else echo $_POST['ip_aangemeld']; ?>" maxlength="15" /></td>
        </tr>
        <tr>
          <td height="20"><strong>Ip login:</strong></td>
          <td><input name="ip_ingelogd" type="text" class="text_long" value="<?php if (!isset($_POST['change'])) echo $profiel['ip_ingelogd']; else echo $_POST['ip_ingelogd']; ?>" maxlength="15" /></td>
        </tr>
	</table>
</center>

            <hr />
            <center>
              <strong>Mostrar o time:</strong><br />
			  <?php 
              if ($teamzien == 1) {
              echo'	<input type="radio" name="teamzien" value="1" id="ja" checked /><label for="ja" style="padding-right:17px"> Sim</label>
                    <input type="radio" name="teamzien" value="0" id="nee" /><label for="nee"> Não</label>';
              }
              else if ($teamzien == 0) {
              echo'	<input type="radio" name="teamzien" value="1" id="ja" /><label for="ja" style="padding-right:17px"> Sim</label>
                    <input type="radio" name="teamzien" value="0" id="nee" checked /><label for="nee"> Não</label>';
              }
              else{
              echo'	<input type="radio" name="teamzien" value="1" id="ja" /><label for="ja" style="padding-right:17px"> Sim</label>
                    <input type="radio" name="teamzien" value="0" id="nee" /><label for="nee"> Não</label>';
              }?>
</center>
            <hr />
			<textarea style="width:580px;" class="text_area" rows="15" name="profiel" ><?php if (!isset($_POST['change'])) echo $profiel['profiel']; else echo $_POST['profiel']; ?></textarea>
            <br />
			<input type="submit" name="change" value="Atualizar!" class="button" /><br />

<?php
  }
}
?>
</form>