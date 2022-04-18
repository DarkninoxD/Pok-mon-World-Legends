<?php

include("app/includes/resources/security.php");

echo addNPCBox(36, "Loja do Cassino", 'Aqui é a Loja do Cassino, onde você poderá comprar Tickets ou trocar seus Tickets por recompensas!<br> Clique <a href="./casino">AQUI</a> para <b>voltar</b> ao Cassino.');

if (isset($_POST['buy-tickets-quant']) && ctype_digit($_POST['buy-tickets-quant'])) {
    $quant = $_POST['buy-tickets-quant'];

    $tickets = 50 * $quant;
    $price = 2500 * $quant;
    
    if ($gebruiker['silver'] <= $price) {
        echo '<div class="red">Você não tem SILVERS suficientes!</div>';
    } else {
        DB::exQuery("UPDATE `gebruikers` SET `tickets`=`tickets`+'$tickets', `silver`=`silver`-'$price' WHERE `user_id`='$_SESSION[id]'");
        echo '<div class="green">Você comprou '.$tickets.'x TICKETS!</div>';
    }
}

if (isset($_POST['sell-tickets-quant']) && ctype_digit($_POST['sell-tickets-quant'])) {
    $quant = $_POST['sell-tickets-quant'];

    $silvers = 1250 * $quant;
    $price = 50 * $quant;
    
    if ($gebruiker['tickets'] <= $price) {
        echo '<div class="red">Você não tem TICKETS suficientes!</div>';
    } else {
        DB::exQuery("UPDATE `gebruikers` SET `tickets`=`tickets`-'$price', `silver`=`silver`+'$silvers' WHERE `user_id`='$_SESSION[id]'");
        echo '<div class="green">Você comprou '.$silvers.'x SILVERS!</div>';
    }
}

$store = DB::exQuery("SELECT * FROM `casino_store` WHERE `is_buy`='1' ORDER BY `type`,`price`");

if (isset($_POST['buy']) && ctype_digit($_POST['buy'])) {
    $id = $_POST['buy'];
    $verify = DB::exQuery("SELECT * FROM `casino_store` WHERE `is_buy`='1' AND `id`='$id'");

    if ($verify->num_rows == 1) {
        $verify = $verify->fetch_assoc();

        if ($gebruiker['tickets'] <= $verify['price']) {
            echo '<div class="red">Você não tem TICKETS suficientes!</div>';
        } else {
            if ($verify['type'] == 0) {
                if ($gebruiker['in_hand'] >= 6) {
                    echo '<div class="red">VOCÊ JA POSSUI 6 POKÉMONS COM VOCÊ!</div>';
                } else {
                    $opzak_nummer = $gebruiker['in_hand'] + 1;

                    #Willekeurige pokemon laden, en daarvan de gegevens
                    $query = DB::exQuery("SELECT `wild_id`,`groei`,`attack_base`,`defence_base`,`speed_base`,`spc.attack_base`,`spc.defence_base`,`hp_base`,`aanval_1`,`aanval_2`,`aanval_3`,`aanval_4`,`ability` FROM `pokemon_wild` WHERE `wild_id`='" . $verify['type_val'] . "' LIMIT 1")->fetch_assoc();
                    $ability = explode(',', $query['ability']);

                    $date = date('Y-m-d H:i:s');

                    $ability = $ability[rand(0, (sizeof($ability) - 1))];

                    #De willekeurige pokemon in de pokemon_speler tabel zetten
                    DB::exQuery("INSERT INTO `pokemon_speler` (`wild_id`,`aanval_1`,`aanval_2`,`aanval_3`,`aanval_4`) SELECT `wild_id`,`aanval_1`,`aanval_2`,`aanval_3`,`aanval_4` FROM `pokemon_wild` WHERE `wild_id`='" . $query['wild_id'] . "'");

                    #id opvragen van de insert hierboven
                    $pokeid	= DB::insertID();

                    #Karakter kiezen 
                    $karakter  = DB::exQuery("SELECT * FROM `karakters` ORDER BY RAND() LIMIT 1")->fetch_assoc();

                    #Expnodig opzoeken en opslaan
                    $experience = DB::exQuery("SELECT `punten` FROM `experience` WHERE `soort`='".$query['groei']."' AND `level`='6'")->fetch_assoc();

                    $attack_iv		= mt_rand(13, 21);
                    $defence_iv		= mt_rand(13, 21);
                    $speed_iv		= mt_rand(13, 21);
                    $spcattack_iv	= mt_rand(13, 21);
                    $spcdefence_iv	= mt_rand(13, 21);
                    $hp_iv			= mt_rand(13, 21);

                    #Stats berekenen
                    $attackstat		= round(((($attack_iv + 2 * $query['attack_base']) * 5 / 100) + 5) * $karakter['attack_add']);
                    $defencestat	= round(((($defence_iv + 2 * $query['defence_base']) * 5 / 100) + 5) * $karakter['defence_add']);
                    $speedstat		= round(((($speed_iv + 2 * $query['speed_base']) * 5 / 100) + 5) * $karakter['speed_add']);
                    $spcattackstat	= round(((($spcattack_iv + 2 * $query['spc.attack_base']) * 5 / 100) + 5) * $karakter['spc.attack_add']);
                    $spcdefencestat	= round(((($spcdefence_iv + 2 * $query['spc.defence_base']) * 5 / 100) + 5) * $karakter['spc.defence_add']);
                    $hpstat			= round((($hp_iv + 2 * $query['hp_base']) * 5 / 100) + 10 + 5);

                    #Alle gegevens van de pokemon opslaan
                DB::exQuery("UPDATE `pokemon_speler` SET `level`='5',`karakter`='".$karakter['karakter_naam']."',`expnodig`='".$experience['punten']."',`user_id`='".$_SESSION['id']."',`opzak`='ja',`opzak_nummer`='".$opzak_nummer."',`attack_iv`='".$attack_iv."',`defence_iv`='".$defence_iv."',`speed_iv`='".$speed_iv."',`spc.attack_iv`='".$spcattack_iv."',`spc.defence_iv`='".$spcdefence_iv."',`hp_iv`='".$hp_iv."',`attack`='".$attackstat."',`defence`='".$defencestat."',`speed`='".$speedstat."',`spc.attack`='".$spcattackstat."',`spc.defence`='".$spcdefencestat."',`levenmax`='".$hpstat."',`leven`='".$hpstat."',`ability`='".$ability."',`capture_date`='".$date."',`icon`='1' WHERE `id`='".$pokeid."' LIMIT 1");
                    DB::exQuery("UPDATE `gebruikers` SET `aantalpokemon`=`aantalpokemon`+'1',`tickets`=`tickets`-'$verify[price]' WHERE `user_id`='" . $_SESSION['id'] . "' LIMIT 1");
                    echo '<div class="green">Você comprou 1x '.$verify['name'].'!</div>';
                }
            } else {
                if ($gebruiker['item_over'] < 1) {
                    echo '<div class="red">VOCÊ NÃO TEM ESPAÇOS DISPONÍVEIS NA SUA MOCHILA!</div>';
                } else {
                    DB::exQuery("UPDATE `gebruikers_tmhm` SET `".$verify['name']."`=`".$verify['name']."`+'1' WHERE `user_id`='".$_SESSION['id']."' LIMIT 1");
                    DB::exQuery("UPDATE `gebruikers` SET `tickets`=`tickets`-'$verify[price]' WHERE `user_id`='" . $_SESSION['id'] . "' LIMIT 1");
                    echo '<div class="green">Você comprou 1x '.$verify['name'].'!</div>';
                }
            }
        }
    }
}

?>

<div class="box-content" style="margin-bottom: 7px"><h3 class="title" style="background: none"> Tickets no Inventário: <img src="<?=$static_url?>/images/icons/ticket.png" title="Tickets" />  <?= highamount($gebruiker['tickets']); ?></h3> </div>

<div class="box-content" style="margin-bottom: 7px">
    <table class="general" width="100%">
        <thead>
            <tr><th>Compre tickets ou troque-os por Silvers!</th></tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    <center>
                        <div class="greyborder">
                            <table style="width: 125px; height: 101px;">
                                <tbody><tr><td align="center">
                                    <img src="<?=$static_url?>/images/icons/ticket.png" title="Tickets" class="icon-img"/>
                                </td></tr>
                                <tr><td align="center"><span id="buy-silvers">50</span>x Tickets <span style="cursor:pointer;" title="Troque seus Silvers por Tickets!"><b>[?]</b></span></td></tr>
                                <tr><td align="center"><img src="public/images/icons/silver.png" style="margin-bottom:-3px;" title="Silvers">  <span id="buy-tickets">2.500</span></td></tr>
                                <tr><td align="center"><form method="post"><input type="number" min="1" max="1000" style="width:60px;" name="buy-tickets-quant" class="input-blue"><input type="submit" value="OK" name="buy-tickets"></form></td></tr>
                            </tbody></table>
                        </div>
                        <div class="greyborder">
                            <table style="width: 125px; height: 101px;">
                                <tbody><tr><td align="center" style="height: 31px">
                                    <img src="<?=$static_url?>/images/icons/silver.png" title="Silvers" class="icon-img"/>
                                </td></tr>
                                <tr><td align="center"><span id="sell-silvers">1.250</span> Silvers <span style="cursor:pointer;" title="Troque seus Tickets por Silvers!"><b>[?]</b></span></td></tr>
                                <tr><td align="center"><img src="public/images/icons/ticket.png" style="margin-bottom:-3px;" title="Tickets"> <span id="sell-tickets">50</span></td></tr>
                                <tr><td align="center"><form method="post"><input type="number" min="1" max="1000" style="width:60px;" name="sell-tickets-quant" class="input-blue"><input type="submit" value="OK" name="sell-tickets"></form></td></tr>
                            </tbody></table>
                        </div>
                    </center>
                </td>
            </tr>
        </tbody>
    </table>
</div>

<div class="box-content">
    <table class="general" width="100%">
        <thead>
            <tr><th>TROQUE SEUS TICKETS POR ESSAS RECOMPENSAS!</th></tr>
        </thead>
        <tbody>
            <tr>
                <td>
                    <center>
                        <?php
                            while ($st = $store->fetch_assoc()) {
                                if ($st['type'] == 0) {
                                    $img = '<img src="'.$static_url.'/images/pokemon/icon/'.$st['type_val'].'.gif" title="'.$st['name'].'" class="icon-img"/>';
                                    $desc = 'Troque seus Tickets por este belíssimo '.$st['name'].'!';
                                } else {
                                    $pegadadox = DB::exQuery("select omschrijving from tmhm where naam='".$st['name']."'")->fetch_assoc();
                                    $pegadado = DB::exQuery("select soort from aanval where naam='".$pegadadox['omschrijving']."'")->fetch_assoc();
                                    $select = DB::exQuery("select `omschrijving_pt` from `markt` where naam='".$st['name']."'")->fetch_assoc();

                                    $type = $pegadado['soort'];
                                    $img = '<img src="'.$static_url.'/images/items/Attack_'.$type.'.png" class="icon-img"/>';
                                    $desc = 'Troque seus Tickets por este fabuloso '.$st['name'].': '.$select['omschrijving_pt'];
                                }
                        ?>

                        <div class="greyborder">
                            <table style="width: 125px; height: 101px;">
                                <tbody><tr><td align="center">
                                   <?=$img?>
                                </td></tr>
                                <tr><td align="center"><?=$st['name']?> <span style="cursor:pointer;" title="<?=$desc?>"><b>[?]</b></span></td></tr>
                                <tr><td align="center"><img src="public/images/icons/ticket.png" style="margin-bottom:-3px;" title="Tickets"> <span><?=highamount($st['price'])?></span></td></tr>
                                <tr><td align="center"><form method="post" onsubmit="return confirm('Deseja realmente realizar esta compra?')"><input type="hidden" name="buy" value="<?=$st['id']?>"><input type="submit" value="COMPRAR" name="buy-casino"></form></td></tr>
                            </tbody></table>
                        </div>

                        <?php } ?>
                    </center>
                </td>
            </tr>
        </tbody>
    </table>
</div>

<script>
    $("input[name='sell-tickets-quant']").bind('keyup input', function(e){
        let val = $(this).val();
        if ($.isNumeric(val) || val == '') {
            let utickets = 50;
            let usilver = 1250;

            if (val == '') val = 1;
            
            $('#sell-silvers').text(formatNumber(usilver*val));
            $('#sell-tickets').text(formatNumber(utickets*val));
        }
    });

    $("input[name='buy-tickets-quant']").bind('keyup input', function(e){
        let val = $(this).val();
        if ($.isNumeric(val) || val == '') {
            let utickets = 50;
            let usilver = 2500;

            if (val == '') val = 1;
            
            $('#buy-silvers').text(formatNumber(utickets*val));
            $('#buy-tickets').text(formatNumber(usilver*val));
        }
    });

    function formatNumber (num) {
        return num.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, "$1,").replace(',', '.');
    }
    
    wlSound('casino', <?=$gebruiker['volume']?>, true);
</script>