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
                $melding = '<font color="green">Todos receberam golds!</font>';
               DB::exQuery("UPDATE `rekeningen` SET `gold`=`gold`+ ".$bedrag." WHERE `user_id` > 0");
            }else{
                $melding = '<font color="red">A quantia deve ser maior do que 0!</font>';
            }
        }else{
            $melding = '<font color="red">Digite uma quantia.</font>';
        }
    }
	//Als de knop is aangeklikt
    if (isset($_POST['doneren2'])) {
        $bedrag = $_POST['bedrag'];
        //Kijken of er een cijfer is ingevuld
        if (ctype_digit($bedrag)) {
            //Is het bedrag groter dan 0?
            if ($bedrag > 0) {
                $melding2 = '<font color="green">Todos receberam silvers!</font>';
                DB::exQuery("UPDATE `gebruikers` SET `silver`=`silver`+ ".$bedrag." WHERE `user_id` > 0");
            }else{
                $melding2 = '<font color="red">A quantia deve ser maior do que 0!</font>';
            }
        }else{
            $melding2 = '<font color="red">Digite uma quantia.</font>';
        }
    }
?>
<?php echo $melding; ?>
<form method="post">
    <label>Quanto golds você quer dar?</label>
    <input type="text" name="bedrag" /><br/><br/>
    <input type="submit" value="Dar!" name="doneren" class="button">
</form>
<hr>
<?php echo $melding2; ?>
<form method="post">
    <label>Quantos silver você quer dar?</label>
    <input type="text" name="bedrag" /><br/><br/>
    <input type="submit" value="Dar!" name="doneren2" class="button">
</form>