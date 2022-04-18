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
                $melding = '<font color="green">Realizado com sucesso!</font>';
                DB::exQuery("UPDATE `gebruikers` SET `silver`=`silver`+ ".$bedrag." WHERE `user_id` > 0");
            }else{
                $melding = '<font color="red">A quantia deve ser maior do que 0!</font>';
            }
        }else{
            $melding = '<font color="red">Você deve digitar um número!</font>';
        }
    }
?>
<?php echo $melding; ?>
<form method="post">
    <label>Quanto silver no banco você deseja dar aos treinadores?</label>
    <input type="text" name="bedrag" /><br/>
    <input type="submit" value="Dar banco" name="doneren" class="button">
</form><br><br>
<?php
    //Als de knop is aangeklikt
    if (isset($_POST['doneren'])) {
        $bedrag = $_POST['bedrag'];
        //Kijken of er een cijfer is ingevuld
        if (ctype_digit($bedrag)) {
            //Is het bedrag groter dan 0?
            if ($bedrag > 0) {
                $melding = '<font color="green">Realizado com sucesso!</font>';
                DB::exQuery("UPDATE `gebruikers` SET `gold`=`gold`+ ".$bedrag." WHERE `user_id` > 0");
            }else{
                $melding = '<font color="red">A quantia deve ser maior do que 0!</font>';
            }
        }else{
            $melding = '<font color="red">Você deve digitar um número!</font>';
        }
    }
?>
<?php echo $melding; ?>
<form method="post">
    <label>Quanto gold você deseja dar?</label>
    <input type="text" name="bedrag" /><br/>
    <input type="submit" value="Dar gold" name="Dar gold" class="button">
</form><br><br>
<?php
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
            $melding = '<font color="green">Realizado com sucesso!</font>';
            DB::exQuery("UPDATE `gebruikers` SET `premiumaccount`=`premiumaccount`+'$endPremium1' WHERE `premiumaccount`>'$timexx'");
            //COLOCA PRA QUEM TEM PREMIUM ATIVO
            DB::exQuery("UPDATE `gebruikers` SET `premiumaccount`='$endPremium2' WHERE `premiumaccount`<'$timexx'");
            //ATUALIZ QUEM N TEM PREMIUM
                
            }else{
                $melding = '<font color="red">A quantia deve ser maior do que 0!</font>';
            }
        }else{
            $melding = '<font color="red">Você deve digitar um número!</font>';
        }
    }
?>
<?php echo $melding; ?>
<form method="post">
    <label>Quantos dias de premium você quer dar?</label>
    <input type="text" name="bedrag" /><br/>
    <input type="submit" value="Dar premium" name="Dar premium" class="button">
</form>

