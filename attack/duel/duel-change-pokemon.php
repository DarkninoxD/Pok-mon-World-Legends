<?php 
if ((isset($_GET['opzak_nummer'])) AND (isset($_GET['duel_id'])) AND (isset($_GET['wie'])) AND (isset($_GET['sid']))) {
    //Connect With Database
    include_once("../../app/includes/resources/config.php");
    //Include Default Functions
    include_once("../../app/includes/resources/ingame.inc.php");
    //Include Duel Functions
    include_once("duel.inc.php");
    //Include Attack Functions
    include_once("../../attack/attack.inc.php");
    //Load duel info
    $duel_info = duel_info($_GET['duel_id']);
    //Check if attack was correct, and screen has to refresh
    $good = 0;
    //Default Values        
    $pokemon_old_id = "";
    //Load language
    $page = 'attack/duel/duel-attack';
    //Goeie taal erbij laden voor de page
    include_once('../../language/language-pages.php');
    if (empty($duel_info['id']))
        $message = "Este duelo não existe.";
    if (($duel_info['u_klaar'] != 1) OR ($duel_info['t_klaar'] != 1))
        $message = $txt['opponent_not_ready'];
    else if ((strtotime(date("Y-m-d H:i:s")) - $duel_info['laatste_beurt_tijd'] > 180) AND (($duel_info['volgende_beurt'] == $_SESSION['naam']) OR (!strpos($duel_info['laaste_beurt'], $_SESSION['naam']))))
        $message = $txt['too_late_lost'];
    else if ((strtotime(date("Y-m-d H:i:s")) - $duel_info['laatste_beurt_tijd'] > 180) AND (($duel_info['volgende_beurt'] != $_SESSION['naam']) OR (!strpos($duel_info['laaste_beurt'], $_SESSION['naam']))))
        $message = $txt['opponent_too_late'];
    else if ($duel_info['volgende_beurt'] == "end_screen")
        $message = $txt['fight_over'];
    else if (($duel_info['volgende_beurt'] != $_SESSION['naam']) AND ($duel_info['volgende_zet'] == "wisselen"))
        $message = "O oponente precisa trocar de Pokémon!";
    else if (($duel_info['volgende_beurt'] != $_SESSION['naam']) AND (!empty($duel_info['volgende_beurt'])))
        $message = "O oponente deve atacar!";
    else {
        //Load New Pokemon Data
        $change_pokemon = DB::exQuery("SELECT pokemon_wild.*, pokemon_speler.*, pokemon_speler_gevecht.*, pokemon_speler.wild_id AS wildid FROM pokemon_wild INNER JOIN pokemon_speler ON pokemon_speler.wild_id = pokemon_wild.wild_id INNER JOIN pokemon_speler_gevecht ON pokemon_speler.id = pokemon_speler_gevecht.id  WHERE pokemon_speler.user_id='" . $_SESSION['id'] . "' AND pokemon_speler.opzak='ja' AND pokemon_speler.opzak_nummer='" . $_GET['opzak_nummer'] . "'")->fetch_assoc();

        //Does The Pokemon excist
        if (!empty($change_pokemon['id'])) {
            include_once '../../app/classes/League_battle.php';
            $league_battle = League_battle::select_duel($duel_info['id']);
            $pokemon_used = array();
            if ($league_battle && $duel_info['uitdager'] == $_SESSION['naam']) {
                $pokemon_used = explode(',', $league_battle->getUser_pokemon1());
            } else if ($league_battle && $duel_info['tegenstander'] == $_SESSION['naam']) {
                $pokemon_used = explode(',', $league_battle->getUser_pokemon2());
            }

            if ($change_pokemon['leven'] <= 0) {
                $message = $txt['pokemon_is_ko'];
            } else if ($league_battle && count($pokemon_used) >= $league_battle->getN_pokemons() && !in_array($change_pokemon['id'], $pokemon_used)) {
                $message = "Você não pode mais adicionar pokémon nesta batalha!";
            } else {
                if ($duel_info['uitdager'] == $_SESSION['naam']) {
                    $duel_info['you'] = "u";
                    //Load All Opoonent Info
                    $opponent_info = pokemon_data($duel_info['t_pokemonid']);
                    //Load Pokemon id
                    $pokemon_info = pokemon_data($duel_info['u_pokemonid']);
                    if ($pokemon_info['leven'] <= 0) {
                        $pokemon_old_id = $pokemon_info['id'];
                        $used_id = "," . $change_pokemon['id'] . ",";
                        if ($change_pokemon['speed'] > $opponent_info['speed'])
                            $vol_be = $duel_info['uitdager'];
                        else
                            $vol_be = $duel_info['tegenstander'];
                    }
                    else {
                        $used = explode(",", $duel_info['u_used_id']);
                        if (!in_array($change_pokemon['id'], $used))
                            $used_id = $duel_info['u_used_id'] . "," . $change_pokemon['id'] . ",";
                        $vol_be = $duel_info['tegenstander'];
                    }
                }
                else if ($duel_info['tegenstander'] == $_SESSION['naam']) {
                    $duel_info['you'] = "t";
                    //Load All Opoonent Info
                    $opponent_info = pokemon_data($duel_info['u_pokemonid']);
                    //Load Pokemon id
                    $pokemon_info = pokemon_data($duel_info['t_pokemonid']);
                    if ($pokemon_info['leven'] <= 0) {
                        $pokemon_old_id = $pokemon_info['id'];
                        $used_id = "," . $change_pokemon['id'] . ",";
                        if ($change_pokemon['speed'] >= $opponent_info['speed'])
                            $vol_be = $duel_info['tegenstander'];
                        else
                            $vol_be = $duel_info['uitdager'];
                    }
                    else {
                        $used = explode(",", $duel_info['t_used_id']);
                        if (!in_array($change_pokemon['id'], $used))
                            $used_id = $duel_info['t_used_id'] . "," . $change_pokemon['id'] . ",";
                            $vol_be = $duel_info['uitdager'];
                    }
                }

                $time = strtotime(date("Y-m-d H:i:s"));

                if ($league_battle && !in_array($change_pokemon['id'], $pokemon_used)) {
                    if ($duel_info['uitdager'] == $_SESSION['naam']) {
                        $league_battle->setUser_pokemon1($league_battle->getUser_pokemon1() . "," . $change_pokemon['id']);
                    } else if ($duel_info['tegenstander'] == $_SESSION['naam']) {
                        $league_battle->setUser_pokemon2($league_battle->getUser_pokemon2() . "," . $change_pokemon['id']);
                    }
                    $league_battle->update();
                }

                DB::exQuery("UPDATE `duel` SET `" . $duel_info['you'] . "_pokemonid`='" . $change_pokemon['id'] . "', `" . $duel_info['you'] . "_used_id`='" . $used_id . "', `laatste_beurt_tijd`='" . $time . "', `laatste_beurt`='" . $_GET['wie'] . "', `laatste_aanval`='wissel', `volgende_beurt`='" . $vol_be . "', `volgende_zet`='', `last_pokemon_id`='" . $pokemon_old_id . "' WHERE `id`='" . $_GET['duel_id'] . "'");

                $t1 = atk($change_pokemon['aanval_1'], $change_pokemon)['soort'];
                $t2 = atk($change_pokemon['aanval_2'], $change_pokemon)['soort'];
                $t3 = atk($change_pokemon['aanval_3'], $change_pokemon)['soort'];
                $t4 = atk($change_pokemon['aanval_4'], $change_pokemon)['soort'];
                
                $zmove = false;
                $tz = false;
                if (zMoves::valid($change_pokemon)[0]) {
                    $zmove = zMoves::move($change_pokemon)[0];
                    $tz = atk($zmove, $change_pokemon)['soort'];
                }

                if ($vol_be == $_SESSION['naam'])
                    $message = "Você traz " . $change_pokemon['naam'] . "<br />" . $txt['your_turn'];
                else
                    $message = "Você traz " . $change_pokemon['naam'] . "<br />" . $vol_be . " " . $txt['opponents_turn'];

                $good = 1;
            }
        } else {
            $message = "Error: 1001<br />Info: " . $change_pokemon['id'] . " - " . $_GET['opzak_nummer'] . " - " . $_SESSION['id'];
        }
    }
    echo $message . " | " . // 0
    $good . " | " . // 1
    $change_pokemon['naam'] . " | " . // 2
    $change_pokemon['level'] . " | " . // 3
    $change_pokemon['shiny'] . " | " . // 4
    $change_pokemon['leven'] . " | " . // 5
    $change_pokemon['levenmax'] . " | " . // 6
    $change_pokemon['exp'] . " | " . // 7
    $change_pokemon['expnodig'] . " | " .// 8 
    $change_pokemon['aanval_1'] . " | " . // 9
    $change_pokemon['aanval_2'] . " | " . // 10
    $change_pokemon['aanval_3'] . " | " . // 11
    $change_pokemon['aanval_4'] . " | " . // 12
    $_GET['opzak_nummer'] . " | " . // 13
    $vol_be . " | " . // 14
    $change_pokemon['wildid'] . " | " . // 15
    $change_pokemon['effect']." | ". // 16
    $t1." | " // 17
    .$t2." | " // 18
    .$t3." | " // 19
    .$t4." | " // 20
    .$zmove." | " // 21
    .$tz; // 22
}
?>