<?php
//Load duel info
function duel_info($duel_id) {
    //Load and return all duel data
    return DB::exQuery("SELECT * FROM `duel` WHERE id='" . $duel_id . "'")->fetch_assoc();
}

//Remove Duel
function remove_duel($duel_id) {
    DB::exQuery("UPDATE `gebruikers` SET `pagina`='duel_start' WHERE `user_id`='" . $_SESSION['id'] . "'");
    DB::exQuery("DELETE FROM `pokemon_speler_gevecht` WHERE `user_id`='" . $_SESSION['id'] . "'");
    DB::exQuery("DELETE FROM `duel` WHERE `id`='" . $duel_id . "'");
}

//Knocked One Pokemon down
function duel_one_dead($duel_info, $you, $pokemon_info, $opponent_info, $txt) {
    $ids = explode(",", $duel_info[$you . '_used_id']);
    $ret['bericht'] = "<br />";
    $aantal = 0;

    //Count all pokemon
    foreach ($ids as $pokemonid) {
        if (!empty($pokemonid))
            $aantal++;
    }
    foreach ($ids as $pokemonid) {
        if (!empty($pokemonid)) {
            $used_info = DB::exQuery("SELECT pokemon_wild.naam, pokemon_speler.roepnaam, pokemon_speler.trade, pokemon_speler.level, pokemon_speler.expnodig, pokemon_speler_gevecht.leven, pokemon_speler_gevecht.exp FROM pokemon_wild INNER JOIN pokemon_speler ON pokemon_wild.wild_id = pokemon_speler.wild_id INNER JOIN pokemon_speler_gevecht ON pokemon_speler.id = pokemon_speler_gevecht.id WHERE pokemon_speler.id='" . $pokemonid . "'")->fetch_assoc();
            $used_info['naam_goed'] = pokemon_naam($used_info['naam'], $used_info['roepnaam']);

            //If pokemon is dead no exp.
            if ($used_info['leven'] > 0) {
                //If pokemon is level 100 no more exp for him
                if ($used_info['level'] < 100) {
                    //Calculate EXP, division by aantal for amount of pokemon
                    $ret['exp'] = round(((($opponent_info['base_exp'] * $opponent_info['level']) * $used_info['trade'] * 1) / 7) / $aantal);

                    //Add the exp and Effort points 
                    DB::exQuery("UPDATE `pokemon_speler_gevecht` SET `exp`=`exp`+'" . $ret['exp'] . "', `totalexp`=`totalexp`+'" . $ret['exp'] . "', `attack_ev`=`attack_ev`+'" . $opponent_info['effort_attack'] . "', `defence_ev`=`defence_ev`+'" . $opponent_info['effort_defence'] . "', `speed_ev`=`speed_ev`+'" . $opponent_info['effort_speed'] . "', `spc.attack_ev`=`spc.attack_ev`+'" . $opponent_info['effort_spc.attack'] . "', `spc.defence_ev`=`spc.defence_ev`+'" . $opponent_info['effort_spc.defence'] . "', `hp_ev`=`hp_ev`+'" . $opponent_info['effort_hp'] . "' WHERE `id`='" . $pokemonid . "'");

                    //Check if the Pokemon is traded
                    if ($used_info['trade'] == 1)
                        $ret['bericht'] .= $used_info['naam_goed'] . " " . $txt['recieve'] . " " . $ret['exp'] . " " . $txt['exp_points'] . ".<br />";
                    else
                        $ret['bericht'] .= $used_info['naam_goed'] . " " . $txt['a_boosted'] . " " . $ret['exp'] . " " . $txt['exp_points'] . ".<br />";
                }
            } else
                $aantal -= 1;
        }
    }

    //Empty Pokemon Used For new pokemon
    DB::exQuery("UPDATE `duel` SET `" . $you . "_used_id`='," . $pokemon_info['id'] . ",' WHERE `id`='" . $duel_info['id'] . "'");

    return $ret;
}

function start_attack($duel) {
    //Check who can start
    $uitdager = DB::exQuery("SELECT pw.naam, pw.wild_id, psg.user_id, ps.id, ps.speed FROM "
            . "pokemon_speler AS ps INNER JOIN pokemon_speler_gevecht AS psg ON psg.id = ps.id INNER JOIN "
            . "pokemon_wild AS pw On ps.wild_id = pw.wild_id WHERE psg.user_id=(SELECT `user_id` FROM `gebruikers` "
            . "WHERE `username` = '" . $duel['uitdager'] . "') AND psg.leven>'0' AND ps.ei='0' "
            . "ORDER BY ps.opzak_nummer ASC LIMIT 1")->fetch_assoc();
    $tegenstander = DB::exQuery("SELECT pw.naam, pw.wild_id, psg.user_id, ps.id, ps.speed "
            . "FROM pokemon_speler AS ps INNER JOIN pokemon_speler_gevecht AS psg ON psg.id = ps.id INNER JOIN "
            . "pokemon_wild AS pw On ps.wild_id = pw.wild_id WHERE psg.user_id=(SELECT `user_id` FROM `gebruikers` "
            . "WHERE `username` = '" . $duel['tegenstander'] . "') AND psg.leven>'0' AND ps.ei='0' "
            . "ORDER BY ps.opzak_nummer ASC LIMIT 1")->fetch_assoc();
    //Check who is the fastest
    if ($uitdager['speed'] >= $tegenstander['speed']) {
        $duel_info['laatste_beurt'] = $duel['uitdager'] . "_begin";
        $duel_info['volgende_beurt'] = $duel['uitdager'];
    } else {
        $duel_info['laatste_beurt'] = $duel['tegenstander'] . "_begin";
        $duel_info['volgende_beurt'] = $duel['tegenstander'];
    }
    //Remember id's
    $duel_info['u_pokemonid'] = $uitdager['id'];
    //Save 
    DB::exQuery("UPDATE `duel` SET `u_pokemonid`='" . $duel_info['u_pokemonid'] . "', `u_used_id`='," . $duel_info['u_pokemonid'] . ",', `laatste_beurt`='" . $duel_info['laatste_beurt'] . "', `volgende_beurt`='" . $duel_info['volgende_beurt'] . "' WHERE `id`='" . $duel['id'] . "'");
    //Update Both pokedexes
    DB::exQuery("UPDATE gebruikers SET `pok_gezien`=concat(pok_gezien,'," . $uitdager['wild_id'] . "') WHERE user_id='" . $tegenstander['user_id'] . "'");
    DB::exQuery("UPDATE gebruikers SET `pok_gezien`=concat(pok_gezien,'," . $tegenstander['wild_id'] . "') WHERE user_id='" . $uitdager['user_id'] . "'");
    return;
}

?>