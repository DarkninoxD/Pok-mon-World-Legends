<?php
//Admin controle
if ($gebruiker['admin'] < 3) { header('location: ./home'); exit; }
    //Als de knop is aangeklikt
    if (isset($_POST['doneren'])) {
        $bedrag = $_POST['bedrag'];
        //Kijken of er een cijfer is ingevuld
        if (ctype_digit($bedrag)) {
            //Is het bedrag groter dan 0?
            if ($bedrag > 0) {
            $timexx = time();
            $endPremium1 = (86400 * $bedrag);
            $endPremium2 = time() + (86400 * $bedrag);
            

                $melding = '<font color="green">Todos receberam '.$bedrag.' dias de premium!</font>';
                
                DB::exQuery("UPDATE `gebruikers` SET `premiumaccount`=`premiumaccount`+'$endPremium1' WHERE `premiumaccount`>='$timexx'");
                //COLOCA PRA QUEM TEM PREMIUM ATIVO
                
                DB::exQuery("UPDATE `gebruikers` SET `premiumaccount`='$endPremium2' WHERE `premiumaccount`<'$timexx'");
                //ATUALIZ QUEM N TEM PREMIUM
               
				$event = '<img src="'.$static_url.'/images/icons/blue.png" width="16" height="16" class="imglower" /> <a href="./profile&player='.$gebruiker['username'].'">'.$gebruiker['username'].'</a> deu para todos '.$bedrag.' dia(s) de premium.';
				DB::exQuery("INSERT INTO gebeurtenis (id, datum, ontvanger_id, bericht, gelezen)
	VALUES (NULL, NOW(), '".$_SESSION['id']."', '".$event."', '0')");
            }else{
                $melding = '<font color="red">O número deve ser maior do que 0!</font>';
            }
        }else{
            $melding = '<font color="red">Digite um número.</font>';
        }
    }
?>
<?php echo $melding; ?>
<form method="post">
    <label>Quantos dias você deseja dar de bônus?</label>
    <input type="text" name="bedrag" /><br/><br/>
    <input type="submit" value="Dar premium" name="doneren" class="button">
</form>