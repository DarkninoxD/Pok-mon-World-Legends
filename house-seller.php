<?php
#Script laden zodat je nooit pagina buiten de index om kan laden
include("app/includes/resources/security.php");

  #Naam van huis wat je nu hebt:
  if ($gebruiker['huis'] == "doos") $huusnu = $txt['house1'];
  else if ($gebruiker['huis'] == "shuis") $huusnu = $txt['house2'];
  else if ($gebruiker['huis'] == "nhuis") $huusnu = $txt['house3'];
  else if ($gebruiker['huis'] == "villa") $huusnu = $txt['house4'];
	 
#Als er op de Buy knop gedrukt word
if (isset($_POST['koop'])) {
  #Naamopbouwen
  if ($_POST['huis'] == "doos") $huus = $txt['house1'];
  else if ($_POST['huis'] == "shuis") $huus = $txt['house2'];
  else if ($_POST['huis'] == "nhuis") $huus = $txt['house3'];
  else if ($_POST['huis'] == "villa") $huus = $txt['house4'];
  
  #Gegevens laden van het huis
  $gegevenhuis = DB::exQuery("SELECT `kosten` FROM `huizen` WHERE `afkorting`='".$_POST['huis']."'")->fetch_assoc();
  
  #Heeft de speler dit huis al?
  if (empty($_POST['huis'])) echo '<div class="red">'.$txt['alert_nothing_selected'].'</div>';
  #Heeft de speler dit huis al?
  else if ($_POST['huis'] == $gebruiker['huis']) echo '<div class="red">'.$txt['alert_you_own_this_house'].'</div>';
  #heeft de speler wel genoeg silver?
  else if ($gebruiker['silver'] < $gegevenhuis['kosten']) echo '<div class="red">'.$txt['alert_not_enough_silver'].'</div>';
  #Heeft de speler al een villa?
  else if ($gebruiker['huis'] == "villa") echo '<div class="red">'.$txt['alert_already_have_villa'].'</div>';
  #Heeft de speler een nhuis en wil hij/zij iets anders kopen dan een villa?
  else if (($gebruiker['huis'] == "nhuis") AND ($_POST['huis'] != "villa")) echo '<div class="red">'.$txt['alert_you_have_better_now'].'</div>';
  #Heeft de speler een klein huis en wil hij/zij een doos kopen?
  else if (($gebruiker['huis'] == "shuis") AND ($_POST['huis'] == "doos")) echo '<div class="red">'.$txt['alert_you_have_better_now'].'</div>';
  #Is alles goed dan dit uitvoeren
  else{
    #Er is een error en bericht opstellen
    echo '<div class="green">'.$txt['success_house_1'].' '.$huus.' '.$txt['success_house_2'].'</div>';
    #Opslaan
    DB::exQuery("UPDATE `gebruikers` SET `silver`=`silver`-'".$gegevenhuis['kosten']."', `huis`='".$_POST['huis']."' WHERE `user_id`='".$_SESSION['id']."'");
  }
}

$keet['1'] = 'disabled';
$keet['2'] = '';
$keet['3'] = '';
$keet['4'] = '';
$button = '';

if ($gebruiker['huis'] == "doos") {
}
else if ($gebruiker['huis'] == "shuis") {
  $keet['2'] = 'disabled';
}
else if ($gebruiker['huis'] == "nhuis") {
  $keet['2'] = 'disabled';
  $keet['3'] = 'disabled';
}
else if ($gebruiker['huis'] == "villa") {
  $keet['1'] = 'disabled';
  $keet['2'] = 'disabled';
  $keet['3'] = 'disabled';
  $keet['4'] = 'disabled';
  $button = 'disabled';
}

$sql = DB::exQuery("SELECT * FROM `huizen`");

echo addNPCBox(5, 'VENDEDOR DE CASAS', $txt['title_text'].' <b>'.$huusnu.'</b>.');
?>
<form method="post" class="box-content">
  <table class="general" cellpadding="0" cellspacing="0">
      <tr>
        <td width="70" class="top_td"><center>#</center></td>
        <td width="140" class="top_td"><center><?php echo $txt['house']; ?></center></td>
        <td width="90" class="top_td"><?php echo $txt['price']; ?></td>
        <td width="360" class="top_td"><?php echo $txt['description']; ?></td>
      </tr>
      <?php
      for($j=1; $select = $sql->fetch_assoc(); $j++) {
        $prijs = number_format(round($select['kosten']),0,",",".");
        echo '
          <tr>
            <td class="normal_td"><center><input type="radio" name="huis" value="'.$select['afkorting'].'" '.$keet[$j].'/></center></td>
            <td class="normal_td" height="80"><center><img src="'.$static_url.'/'.$select['link'].'" /></center></td>
            <td class="normal_td"><img src="'.$static_url.'/images/icons/silver.png" title="Silver" style="margin-bottom:-3px;" /> '.$prijs.'</td>
            <td class="normal_td">'.$select['omschrijving_en'].'</td>
          </tr>';
      }
      ?>
      <tr>
        <td colspan="4"><center><input type="submit" name="koop" class="button_mini" value="COMPRAR!"></center></td>
      </tr>
  </table>
</form>