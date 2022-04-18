<?php

require_once('../includes/resources/config.php');
require_once('../includes/resources/ingame.inc.php');

$date = strtotime(date('Y-m-d H:i', strtotime('+1 minutes')));
$req = DB::exQuery("SELECT * FROM `transferlijst` WHERE `type`='auction' AND `time_end` <= '$date'");

while ($buy = $req->fetch_assoc()) {
    $buyer = $buy['big_blind'];
    $tid = $buy['id'];

    $tl = DB::exQuery("SELECT `s`.`wild_id`, `s`.`icon`, `s`.`user_id`, `s`.`level`, `s`.`item`, `s`.`roepnaam`, `w`.`naam` FROM `pokemon_speler` s INNER JOIN `pokemon_wild` w ON `s`.`wild_id` = `w`.`wild_id` WHERE id='$buy[pokemon_id]'")->fetch_assoc();
    $tl['naam'] = pokemon_naam($tl['naam'], $tl['roepnaam'], $tl['icon']);

    if ($buyer > 0) {
        DB::exQuery("UPDATE `pokemon_speler` SET `user_id`='".$buyer."',`trade`='1.5',`opzak`='nee',`opzak_nummer`='' WHERE `id`='".$buy['pokemon_id']."'");

        // DB::exQuery("UPDATE `gebruikers` SET `silver`=`silver`-'".$buy['silver']."', `aantalpokemon`=`aantalpokemon`+'1' WHERE `user_id`='".$buyer."'");
        DB::exQuery("UPDATE `gebruikers` SET `aantalpokemon`=`aantalpokemon`+'1' WHERE `user_id`='".$buyer."'");
        
        DB::exQuery("UPDATE `gebruikers` SET `silver`=`silver`+'".$buy['silver']."', `aantalpokemon`=`aantalpokemon`-'1' WHERE `user_id`='".$buy['user_id']."'");

        DB::exQuery("DELETE FROM `transferlijst` WHERE `id`='".$tid."'");
        update_pokedex($tl['wild_id'], '', 'buy');

        DB::exQuery("INSERT INTO transferlist_log (date, wild_id, speler_id, level, seller, buyer, silver, gold, item) VALUES (NOW(), '".$tl['wild_id']."', '".$tl['id']."', '".$tl['level']."', '".$buy['user_id']."', '".$_SESSION['id']."', '".$buy['silver']."', '".$buy['gold']."', '".$tl['item']."')");

        $buyer = DB::exQuery("SELECT `username` FROM `gebruikers` WHERE `user_id`='$buyer'")->fetch_assoc()['username'];
        
        $event = '<img src="' . $static_url . '/images/icons/blue.png" width="16" height="16" class="imglower" /> <a href="./profile&player='.$buyer.'">'.$buyer.'</a> comprou seu <a href="./pokemon-profile&id='.$buy['pokemon_id'].'">'.$tl['naam'].'</a> por: '.highamount($buy['silver']).' <img src="' . $static_url . '/images/icons/silver.png" title="Silver" width="16" height="16" /> e '.highamount($buy['gold']).'<img src="' . $static_url . '/images/icons/gold.png" title="Gold" width="16" height="16" />!';
        DB::exQuery("INSERT INTO gebeurtenis (`datum`,`ontvanger_id`,`bericht`,`gelezen`) VALUES (NOW(), '" . $buy['user_id'] . "', '" . $event . "', '0')");

        $event = '<img src="' . $static_url . '/images/icons/blue.png" width="16" height="16" class="imglower" /> Você comprou <a href="./pokemon-profile&id='.$buy['pokemon_id'].'">'.$tl['naam'].'</a> por: '.highamount($buy['silver']).' <img src="' . $static_url . '/images/icons/silver.png" title="Silver" width="16" height="16" /> e '.highamount($buy['gold']).'<img src="' . $static_url . '/images/icons/gold.png" title="Gold" width="16" height="16" />, e ele já está em sua BOX!';
        DB::exQuery("INSERT INTO gebeurtenis (`datum`,`ontvanger_id`,`bericht`,`gelezen`) VALUES (NOW(), '" . $buy['big_blind'] . "', '" . $event . "', '0')");
    } else {
        DB::exQuery("UPDATE `pokemon_speler` SET `trade`='1.0',`opzak`='nee' WHERE `id`='".$buy['pokemon_id']."'");
        DB::exQuery("DELETE FROM `transferlijst` WHERE `id`='".$tid."'");
        
        $event = '<img src="' . $static_url . '/images/icons/blue.png" width="16" height="16" class="imglower" /> Ninguém deu lance em seu <a href="./pokemon-profile&id='.$buy['pokemon_id'].'">'.$tl['naam'].'</a>, portanto, seu Pokémon voltou para sua casa!';
        DB::exQuery("INSERT INTO gebeurtenis (`datum`,`ontvanger_id`,`bericht`,`gelezen`) VALUES (NOW(), '" . $buy['user_id'] . "', '" . $event . "', '0')");
    }
}