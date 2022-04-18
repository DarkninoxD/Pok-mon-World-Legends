<?php
//Admin controle
if ($gebruiker['admin'] < 3) { header('location: ./home'); exit; }
    //Als de knop is aangeklikt
    if (isset($_POST['doneren'])) {
        $bedrag = $_POST['bedrag'];
        $bedragname = $_POST['bedragname']; 
        //Kijken of er een cijfer is ingevuld
        if (ctype_digit($bedrag)) {
            //Is het bedrag groter dan 0?
            if ($bedrag > 0) {
            if (DB::exQuery("select `user_id` from `gebruikers` where `username`='".$bedragname."'")->num_rows > 0) {
            $timexx = time();
            $endPremium1 = (86400 * $bedrag);
            $endPremium2 = time() + (86400 * $bedrag);
            

                $melding = '<font color="green">'.$bedragname.' recebeu '.$bedrag.' dias de premium!</font>';
                
                DB::exQuery("UPDATE `gebruikers` SET `premiumaccount`=`premiumaccount`+'$endPremium1' WHERE `username`='$bedragname' AND `premiumaccount`>'$timexx' limit 1");
                //COLOCA PRA QUEM TEM PREMIUM ATIVO
                
                DB::exQuery("UPDATE `gebruikers` SET `premiumaccount`='$endPremium2' WHERE `username`='$bedragname' AND `premiumaccount`<'$timexx' limit 1");
                //ATUALIZ QUEM N TEM PREMIUM
               
				$event = '<img src="'.$static_url.'/images/icons/blue.png" width="16" height="16" class="imglower" /> <a href="./profile&player='.$gebruiker['username'].'">'.$gebruiker['username'].'</a> deu para '.$bedragname.' '.$bedrag.' dia(s) de premium.';
				DB::exQuery("INSERT INTO gebeurtenis (id, datum, ontvanger_id, bericht, gelezen)
	VALUES (NULL, NOW(), '".$_SESSION['id']."', '".$event."', '0')");
	} else {
	$melding = '<font color="red">Treinador não existe!</font>';
	}
            }else{
                $melding = '<font color="red">O número deve ser maior do que 0!</font>';
            }
        }else{
            $melding = '<font color="red">Digite um número.</font>';
        }
    }
?>
<?php echo $melding; ?>
<form method="post"><br>
    <label>Quantos dias você deseja dar de bônus e pra quem?</label>
   <input type="text" name="bedragname" placeholder="Treinador"/> <input type="number" name="bedrag" placeholder="Qtd dias"/><br/><br/>
    <input type="submit" value="Dar premium" name="doneren" class="button">
<br><br></form>