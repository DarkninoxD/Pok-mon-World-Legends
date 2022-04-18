<?php
if ((isset($_GET['duel_id'])) AND ( isset($_GET['sid']))) {
    error_reporting(0);
    //Connect With Database
    include_once("../../app/includes/resources/config.php");
    //include duel functions
    include_once("duel.inc.php");
    //Load Duel Data
    $duel_sql = DB::exQuery("SElECT `id`, `uitdager`, `tegenstander`, `u_klaar`, `t_klaar`, `u_pokemonid`, `t_pokemonid`, `laatste_beurt`, `laatste_aanval`, `datum` FROM `duel` WHERE `id`='" . $_GET['duel_id'] . "'");
    //Default text
    $ready = 0;
    //If there is no duel
    if ($duel_sql->num_rows == 1) {
        $duel = $duel_sql->fetch_assoc();

        if ((($duel['t_klaar'] == 1) && ( $duel['u_klaar'] == 1)) && ( $duel['laatste_beurt'] != "")) {
            $ready = 1;
            $_SESSION['duel']['begin_zien'] = false;
            $mes = $duel['laatste_beurt'];
            $uitdager = DB::exQuery("SELECT pw.wild_id, pw.naam, psg.levenmax, psg.leven, ps.shiny, ps.exp, ps.expnodig FROM pokemon_speler AS ps INNER JOIN pokemon_speler_gevecht AS psg ON psg.id = ps.id INNER JOIN pokemon_wild AS pw On ps.wild_id = pw.wild_id WHERE psg.id='" . $duel['u_pokemonid'] . "'")->fetch_assoc();
            $tegenstander = DB::exQuery("SELECT pw.wild_id, pw.naam, psg.levenmax, psg.leven, ps.shiny, ps.exp, ps.expnodig FROM pokemon_speler AS ps INNER JOIN pokemon_speler_gevecht AS psg ON psg.id = ps.id INNER JOIN pokemon_wild AS pw On ps.wild_id = pw.wild_id WHERE psg.id='" . $duel['t_pokemonid'] . "'")->fetch_assoc();
            if ($duel['uitdager'] == $_SESSION['naam']) {
                $you_name = $uitdager['naam'];
                $opp_name = $tegenstander['naam'];
                if ($tegenstander['shiny'] == 1) {
                    $opp_link = ''.$static_url.'/images/shiny/' . $tegenstander['wild_id'] . '.png';
                } else {
                    $opp_link = ''.$static_url.'/images/pokemon/' . $tegenstander['wild_id'] . '.png';
                }
                if ($uitdager['shiny'] == 1) {
                    $you_link = ''.$static_url.'/images/shiny/' . $uitdager['wild_id'] . '.png';
                } else {
                    $you_link = ''.$static_url.'/images/pokemon/' . $uitdager['wild_id'] . '.png';
                }
                if ($tegenstander['leven'] == 0) {
                    $opp_life = 0;
                } else {
                    $opp_life = round(($tegenstander['leven'] / $tegenstander['levenmax']) * 100);
                }
                if ($uitdager['leven'] == 0) {
                    $you_life = 0;
                } else {
                    $you_life = round(($uitdager['leven'] / $uitdager['levenmax']) * 100);
                }
                if ($uitdager['exp'] == 0) {
                    $you_exp = 0;
                } else {
                    if ($uitdager['expnodig'] != 0) {
                        $you_exp = round(($uitdager['exp'] / $uitdager['expnodig']) * 100);
                    } else {
                        $you_exp = round(($uitdager['exp']) * 100);
                    }
                }
            } else if ($duel['tegenstander'] == $_SESSION['naam']) {
                $you_name = $tegenstander['naam'];
                $opp_name = $uitdager['naam'];
                if ($tegenstander['shiny'] == 1) {
                    $you_link = ''.$static_url.'/images/shiny/' . $tegenstander['wild_id'] . '.png';
                } else {
                    $you_link = ''.$static_url.'/images/pokemon/' . $tegenstander['wild_id'] . '.png';
                }
                if ($uitdager['shiny'] == 1) {
                    $opp_link = ''.$static_url.'/images/shiny/' . $uitdager['wild_id'] . '.png';
                } else {
                    $opp_link = ''.$static_url.'/images/pokemon/' . $uitdager['wild_id'] . '.png';
                }
                if ($uitdager['leven'] == 0) {
                    $opp_life = 0;
                } else {
                    $opp_life = round(($uitdager['leven'] / $uitdager['levenmax']) * 100);
                }
                if ($tegenstander['leven'] == 0) {
                    $you_life = 0;
                } else {
                    $you_life = round(($tegenstander['leven'] / $tegenstander['levenmax']) * 100);
                }
                if ($tegenstander['exp'] == 0) {
                    $you_exp = 0;
                } else {
                    if ($tegenstander['expnodig'] != 0) {
                        $you_exp = round(($tegenstander['exp'] / $tegenstander['expnodig']) * 100);
                    } else {
                        $you_exp = round(($tegenstander['exp']) * 100);
                    }
                }
            }
            //Save Current Time
            $time = strtotime(date("Y-m-d H:i:s"));
            DB::exQuery("UPDATE `duel` SET `laatste_beurt_tijd`='" . $time . "' WHERE `id`='" . $_GET['duel_id'] . "'");
        } else if (time() > ($duel['datum'] + 180)) {
            if ($duel['u_klaar'] == 1) {
                $winner = $duel['uitdager'];
                if ($duel['uitdager'] == $_SESSION['naam']) {
                    $mes = "#1-  O seu oponente provavelmente está offline, você venceu!";
                }
            } else {
                $winner = $duel['tegenstander'];
                if ($duel['tegenstander'] == $_SESSION['naam']) {
                    $mes = "#2-  O seu oponente provavelmente está offline, você venceu!";
                }
            }
            DB::exQuery("UPDATE `duel` SET `winner`='$winner' WHERE `id`='" . $duel['id'] . "'");
            $ready = 2;
        }
    } else {
        $mes = "Foutcode: 6001";
    }

    echo $ready . " | " . $mes . " | " . $you_name . " | " . $opp_name . " | " . $opp_life . " | " . $you_link . " | " . $opp_link . " | " . $you_life . " | " . $you_exp;
}
?>