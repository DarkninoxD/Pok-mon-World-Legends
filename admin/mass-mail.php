<?php
header('Content-Type: text/html; charset=utf-8'); 	
//Script laden zodat je nooit pagina buiten de index om kan laden
include("app/includes/resources/security.php");

//Admin controle
if ($gebruiker['admin'] < 3) { header('location: ./home'); exit; }


//Als er op de verstuur knop gedrukt word
if (isset($_POST['verstuur'])) {

    //Makkelijk naam toewijzen
    $bericht   = $_POST['tekst'];
    $onderwerp = $_POST['onderwerp'];
    //Als er geen bericht is ingetypt
    if (empty($bericht)) {
      echo '<div class="red">Digite algum texto.</div>';
    }
    //Als alles is ingevuld het bericht versturen
    else{
      $speler = DB::exQuery("SELECT `username`, `email` FROM `gebruikers`"); #between '1900' and '4000'
      $aantal = 0;
      while($spelers = ($speler)->fetch_assoc()) {
        $aantal++;
        //Als er geen onderwerp is ingevuld een onderwerp toewijzen
        if ($onderwerp == '' || $onderwerp == 'Onderwerp') {
          $onderwerp =  "Pokémon World Legends";
        } 
        //In de database zetten
        //Tijd opvragen.
        $datum      = date('Y-m-d H:i:s');
        $verstuurd  = date('d-m-y H:i');
        //Spaties weghalen
    ### Headers. 
    $headers = "MIME-Version: 1.0" . "\r\n";
    $headers .= "Content-Type: text/html; charset=iso-8859-1\n";
    $headers .= "From: Pokemon World Legends <automatico@simbolarena.com.br>\n"; 
    $headers .= "X-Sender: \"rot\" \n";  
    $headers .= "X-Mailer: PHP\n"; 
    $headers .= "Bcc: simbolarena.com.br\r\n"; 
  
		
		//$bericht = nl2br($bericht);
    //Mail versturen
    mail($spelers['email'],
    $onderwerp,
    '	<html>
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
</head>
<body>
<div align="center">
  <table width="70%" border="0" cellspacing="0" cellpadding="0">
    <tr>
      <td background="http://www.simbolarena.com.brimages/mail/linksboven.gif" width="11" height="11"></td>
      <td height="11" background="http://www.simbolarena.com.brimages/mail/bovenbalk.gif"></td>
      <td background="http://www.simbolarena.com.brimages/mail/rechtsboven.gif" width="11" height="11"></td>
    </tr>

    <tr>
      <td width="11" rowspan="2" background="http://www.simbolarena.com.brimages/mail/linksbalk.gif"></td>
      <td align="center" bgcolor="#D3E9F5"><img src="http://www.simbolarena.com.brimages/mail/headermail.png" width="520" height="140"></td>
      <td width="11" rowspan="2" background="http://www.simbolarena.com.brimages/mail/rechtsbalk.gif"></td>
    </tr>
    <tr>
      <td align="left" valign="top" bgcolor="#D3E9F5">Olá '.$spelers['username'].'!<br /><br />
        '.nl2br($_POST['tekst']).'
      </td>
    </tr>
    <tr>
      <td background="http://www.simbolarena.com.brimages/mail/linksonder.gif" width="11" height="11"></td>
      <td background="http://www.simbolarena.com.brimages/mail/onderbalk.gif" height="11"></td>
      <td background="http://www.simbolarena.com.brimages/mail/rechtsonder.gif" width="11" height="11"></td>
    </tr>
  </table>
  &copy; Pokémon World Legends<br>
</div>
</body>
      </html>',
      	$headers
          ); 

        }
        echo '<div class="green">Mensagem enviada '.$aantal.'x vezes!</div>'; 
  }

}

?>
<form method="post">
<table width="660" cellpadding="0" cellspacing="0">
	<tr>
        <td width="110">Assunto:</td>
        <td width="550"><input type="text" name="onderwerp" class="text_long" value="<?php if ($_POST['onderwerp'] != '') echo $_POST['onderwerp']; ?>"></td>
    </tr>
    <tr>
    	<td colspan="2"><textarea style="width:580px;" class="text_area" rows="15"  name="tekst"><?php if ($_POST['tekst'] != '') echo $_POST['tekst']; ?></textarea></td>
    </tr>
    <tr>
        <td colspan="2"><input type="submit" value="Enviar!" name="verstuur" class="button"></td>
    </tr>
</table>
</form>