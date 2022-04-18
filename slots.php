<?php
include('app/includes/resources/security.php');
echo addNPCBox(36, 'Caça-Níqueis', 'Aposte 150 Tickets na Máquina de Caça Níqueis, acerte as combinações e ganhe mais Tickets! <br> Clique <a href="./casino">AQUI</a> para <b>voltar</b> ao Cassino.');

$valid = false;

if (isset($_POST['play-slots'])) {
    if ($gebruiker['tickets'] <= '149') { 
        $valid = false;
        echo '<div class="red">Você não tem Tickets suficientes! Compre na <a href="./casino-store">LOJA DO CASSINO</a> ou <a href="./casino-store">JOGUE OUTROS MINIGAMES</a>!</div>'; 
    } else {
        $valid = true;
        
        $val = array();
        for ($i = 0; $i < 3; $i++) {
            $val[$i] = rand(0, 5);
        }

        $count = array_unique($val);
        $count = array_diff_key($val, $count);
        $count_1 = count($count);
        $ticket = 0;
        if ($count_1 == 2) {
            $a = array(150, 200, 250, 400, 300, 325);
            for ($i = 0; $i < count($a); $i++) {
                if (in_array($i, $count)) {
                    $ticket = $a[$i];
                }
            }
        } else if ($count_1 == 1 && in_array(0, $count)) {
            $ticket = 100;
        } else if ($count_1 == 1 && in_array(3, $count)) {
            $ticket = 200;
        } else if ($count_1 == 1 && in_array(4, $count)) {
            $ticket = 150;
        }

        if (empty($count_1) && in_array(0, $val)) {
            $ticket += 50;
        }

        DB::exQuery("UPDATE `gebruikers` SET `tickets`=`tickets`-'150' WHERE `user_id`='$gebruiker[user_id]'");

        if ($ticket > 0) {
            echo '<div class="green" style="display: none" id="comb">Você conseguiu uma combinação e ganhou: '.$ticket.' TICKETS!</div>';
            DB::exQuery("UPDATE `gebruikers` SET `tickets`=`tickets`+'$ticket' WHERE `user_id`='$gebruiker[user_id]'");
        } else {
            echo '<div class="red" style="display: none" id="comb">Você não conseguiu nenhuma combinação.</div>';
        } 

        echo '<script>setTimeout(function (){ $("#comb").show(500) }, 5250);</script>';
    }
}

?>

<div class="box-content" style="margin-bottom: 7px"><h3 class="title" style="background: none"> Tickets no Inventário: <img src="<?=$static_url?>/images/icons/ticket.png" title="Tickets" />  <?= highamount($gebruiker['tickets']); ?></h3> </div>

<style>
    .slots {
        width: 60px;
    }
    .aux {
        margin-top: 14%;
    }
</style>
<script src="<?=$static_url?>/javascripts/jquery.roulette.min.js"></script>

<table class="general box-content" width="100%">
    <thead><tr><th>Caça-Níqueis</th></tr></thead>
    <tbody>
        <tr><td>
        <div class="row" style="text-align: center">
            <div style="width: 33.3%;" class="col">
            <div id="npc-section" style="background: url('public/images/layout/starProfile.png') no-repeat #34465f; background-position: center;height: 128px; border-top-right-radius: 0; border-bottom-right-radius: 0; border-right: 1px solid #577599;">
                <center><div class="r1" style="display:none;padding-top:14%">
                    <img src="<?=$static_url?>/images/slots/1.png" class="slots" id="slot-1-1">
                    <img src="<?=$static_url?>/images/slots/2.png" class="slots" id="slot-1-2">
                    <img src="<?=$static_url?>/images/slots/3.png" class="slots" id="slot-1-3">
                    <img src="<?=$static_url?>/images/slots/4.png" class="slots" id="slot-1-4">
                    <img src="<?=$static_url?>/images/slots/5.png" class="slots" id="slot-1-5">
                    <img src="<?=$static_url?>/images/slots/6.png" class="slots" id="slot-1-6">
                </div></center>
                <img src="<?=$static_url?>/images/slots/<?=rand(1, 6)?>.png" class="aux slots">
            </div>
            </div>
            
            <div style="width: 33.3%;" class="col">
                <div id="npc-section" style="background: url('public/images/layout/starProfile.png') no-repeat #34465f;background-position: center;height: 128px; border-top-left-radius: 0;border-bottom-left-radius: 0;border-top-right-radius: 0;border-bottom-right-radius: 0;">
                    <center><div class="r2" style="display:none;padding-top:14%">
                        <img src="<?=$static_url?>/images/slots/1.png" class="slots" id="slot-2-1">
                        <img src="<?=$static_url?>/images/slots/2.png" class="slots" id="slot-2-2">
                        <img src="<?=$static_url?>/images/slots/3.png" class="slots" id="slot-2-3">
                        <img src="<?=$static_url?>/images/slots/4.png" class="slots" id="slot-2-4">
                        <img src="<?=$static_url?>/images/slots/5.png" class="slots" id="slot-2-5">
                        <img src="<?=$static_url?>/images/slots/6.png" class="slots" id="slot-2-6">
                    </div></center>
                    <img src="<?=$static_url?>/images/slots/<?=rand(1, 6)?>.png" class="aux slots">
                </div>     	
            </div>
            
            <div style="width: 33.3%;" class="col">
                <div id="npc-section" style="background: url('public/images/layout/starProfile.png') no-repeat #34465f;background-position: center;height: 128px;border-top-left-radius: 0;border-bottom-left-radius: 0;border-left: 1px solid #577599;">
                    <center><div class="r3" style="display:none;padding-top:14%">
                        <img src="<?=$static_url?>/images/slots/1.png" class="slots" id="slot-3-1">
                        <img src="<?=$static_url?>/images/slots/2.png" class="slots" id="slot-3-2">
                        <img src="<?=$static_url?>/images/slots/3.png" class="slots" id="slot-3-3">
                        <img src="<?=$static_url?>/images/slots/4.png" class="slots" id="slot-3-4">
                        <img src="<?=$static_url?>/images/slots/5.png" class="slots" id="slot-3-5">
                        <img src="<?=$static_url?>/images/slots/6.png" class="slots" id="slot-3-6">
                    </div></center>
                    <img src="<?=$static_url?>/images/slots/<?=rand(1, 6)?>.png" class="aux slots">
                </div>
            </div>
        </div>
        </td></tr>
    </tbody>
    <tfoot>
        <tr>
            <td>
                <form method="post">
                    <center><input type="submit" value="JOGAR" name="play-slots" style="margin: 6px"></center>
                </form>
            </td>
        </tr>
    </tfoot>
</table>

<div class="box-content" style="margin-top: 7px; text-align: center">
    <table class="general" width="100%"><thead><tr onclick="wlBadges('#combinacao')" style="cursor: pointer"><th><b id="comb-text">Combinações (Clique para comprimir):</b></th></tr></thead>
        <tr id="combinacao">
            <td style="padding:0">
                <table class="general" style="width: 100%; font-size: 13px; font-weight: 600">
                        <tbody>
                            <tr>
                                <td>Combinações:</td>
                                <td>Prêmio:</td>
                            </tr>
                            <tr>
                                <td><img src="<?=$static_url?>/images/slots/4.png"> <img src="<?=$static_url?>/images/slots/4.png"> <img src="<?=$static_url?>/images/slots/4.png"></td>
                                <td>400</td>
                            </tr>
                            <tr>
                                <td><img src="<?=$static_url?>/images/slots/6.png"> <img src="<?=$static_url?>/images/slots/6.png"> <img src="<?=$static_url?>/images/slots/6.png"></td>
                                <td>325</td>
                            </tr>
                            <tr>
                                <td><img src="<?=$static_url?>/images/slots/5.png"> <img src="<?=$static_url?>/images/slots/5.png"> <img src="<?=$static_url?>/images/slots/5.png"></td>
                                <td>300</td>
                            </tr>
                            <tr>
                                <td><img src="<?=$static_url?>/images/slots/3.png"> <img src="<?=$static_url?>/images/slots/3.png"> <img src="<?=$static_url?>/images/slots/3.png"></td>
                                <td>250</td>
                            </tr>
                            <tr>
                                <td><img src="<?=$static_url?>/images/slots/4.png"> <img src="<?=$static_url?>/images/slots/4.png"></td>
                                <td>200</td>
                            </tr>
                            <tr>
                                <td><img src="<?=$static_url?>/images/slots/2.png"> <img src="<?=$static_url?>/images/slots/2.png"> <img src="<?=$static_url?>/images/slots/2.png"></td>
                                <td>200</td>
                            </tr>
                            <tr>
                                <td><img src="<?=$static_url?>/images/slots/5.png"> <img src="<?=$static_url?>/images/slots/5.png"></td>
                                <td>150</td>
                            </tr>
                            <tr>
                                <td><img src="<?=$static_url?>/images/slots/1.png"> <img src="<?=$static_url?>/images/slots/1.png"> <img src="<?=$static_url?>/images/slots/1.png"></td>
                                <td>150</td>
                            </tr>
                            <tr>
                                <td><img src="<?=$static_url?>/images/slots/1.png"> <img src="<?=$static_url?>/images/slots/1.png"></td>
                                <td>100</td>
                            </tr>
                            <tr>
                                <td><img src="<?=$static_url?>/images/slots/1.png"></td>
                                <td>50</td>
                            </tr>
                        </tbody>
                    </table>
            </td>
        </tr>
    </table>
</div>

<script>
    var open = true;
	function wlBadges( el ) {
		$(el).toggleClass('wlBadges');
        if (!open) {
            $('#comb-text').text('Combinações (Clique para comprimir):');
            open = true;
        } else {
            $('#comb-text').text('Combinações (Clique para expandir):');
            open = false;
        }
	}
</script>

<?php
    if ($valid) {
?>
<script>
var i = 1;
function start(obj, dur, num) {
    var option = {
        speed : 10,
        duration : dur,
        stopImageNumber : num,
        startCallback : function() {
            $('input[name="play-slots"]').attr('disabled', 'true');
        },
        stopCallback : function($stopElm) {
            $('input[name="play-slots"]').removeAttr('disabled');
            $('.r'+i+' img').css('opacity', '0');
            $stopElm.css('opacity', '1');
            console.log($stopElm);
            i++;
        }
    }
    $(obj).roulette(option);
    $(obj).roulette('start');

    $('.aux').hide();
    $('input[name="play-slots"]').val('Jogar novamente');
}

start('.r1', '2', '<?=$val[0]?>'); start('.r2', '3', '<?=$val[1]?>'); start('.r3', '4', '<?=$val[2]?>');
</script>
<?php } ?>