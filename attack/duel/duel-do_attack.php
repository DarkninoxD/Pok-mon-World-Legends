<?php
if ((isset($_GET['attack_name'])) AND ( isset($_GET['duel_id'])) AND ( isset($_GET['wie'])) AND ( isset($_GET['sid']))) {
    //Connect With Database
    include_once("../../app/includes/resources/config.php");
    //Include Default Functions
    include_once("../../app/includes/resources/ingame.inc.php");
    //Include Duel Functions
    include_once("duel.inc.php");
    //Include Attack Functions
    include_once("../attack.inc.php");
    //Load language
    $page = 'attack/duel/duel-attack';
    //Goeie taal erbij laden voor de page
    include_once('../../language/language-pages.php');
    //Load duel info
    $duel_info = duel_info($_GET['duel_id']);
    //Check if attack was correct, and screen has to refresh
    $good = 0;
    $win_lose = 0;
    $recoil_d = 0;
    $auto_turn = 0;
    $life_decrease = 0;
    $message = '';
    $pre_message = '';
    $time = strtotime(date("Y-m-d H:i:s"));
    //Default Value
    $attack_status['winner'] = "";
    if (empty($duel_info['id']))
        $message = "Algo deu errado.";
    if (($duel_info['u_klaar'] != 1) OR ( $duel_info['t_klaar'] != 1))
        $message = $txt['opponent_not_ready'];
    else if ((strtotime(date("Y-m-d H:i:s")) - $duel_info['laatste_beurt_tijd'] > 120) AND ( ($duel_info['volgende_beurt'] == $_SESSION['naam']) OR ( !strpos($duel_info['laaste_beurt'], $_SESSION['naam'])))) {
        $message = $txt['too_late_lost'];
        if ($duel_info['uitdager'] == $_SESSION['naam'])
            $winner = $duel_info['tegenstander'];
        else if ($duel_info['tegenstander'] == $_SESSION['naam'])
            $winner = $duel_info['uitdager'];
        DB::exQuery("UPDATE `duel` SET `winner`='" . $winner . "' WHERE `id`='" . $duel_info['id'] . "'");
        $good = 2;
    } else if ((strtotime(date("Y-m-d H:i:s")) - $duel_info['laatste_beurt_tijd'] > 120) AND ( ($duel_info['volgende_beurt'] != $_SESSION['naam']) OR ( !strpos($duel_info['laaste_beurt'], $_SESSION['naam'])))) {
        $message = $txt['opponent_too_late'];
        if ($duel_info['uitdager'] == $_SESSION['naam'])
            $winner = $duel_info['uitdager'];
        else if ($duel_info['tegenstander'] == $_SESSION['naam'])
            $winner = $duel_info['tegenstander'];
        DB::exQuery("UPDATE `duel` SET `winner`='" . $winner . "' WHERE `id`='" . $duel_info['id'] . "'");
        $good = 2;
    } else if ($duel_info['volgende_beurt'] == "end_screen")
        $message = $txt['fight_over'];
    else if ($_SESSION['naam'] != $_GET['wie'])
        $message = "error: 9001";
    else if (($duel_info['volgende_beurt'] != $_SESSION['naam']) AND ( $duel_info['volgende_zet'] == "wisselen"))
        $message = $txt['opponent_must_change'];
    else if (($duel_info['volgende_beurt'] != $_SESSION['naam']) AND ( !empty($duel_info['volgende_beurt'])))
        $message = $txt['opponent_must_attack'];
    else {
        $t_user_online = DB::exQuery("SELECT `online` FROM `gebruikers` WHERE `username` = '" . $duel_info['tegenstander'] . "' AND `online`+300 > UNIX_TIMESTAMP()")->num_rows;
        $u_user_online = DB::exQuery("SELECT `online` FROM `gebruikers` WHERE `username` = '" . $duel_info['uitdager'] . "' AND `online`+300 > UNIX_TIMESTAMP()")->num_rows;
		
        if ($t_user_online == 0 || $u_user_online == 0) {
            if ($t_user_online == 0) {
                if ($duel_info['uitdager'] == $_SESSION['naam']) {
                    $winner = $duel_info['uitdager'];
                    $message = "O oponente ficou inativo, você venceu!";
                } else if ($duel_info['tegenstander'] == $_SESSION['naam']) {
                    $winner = $duel_info['tegenstander'];
                    $message = "Você ficou inativo, seu oponente venceu!";
                }
            } else {
                if ($duel_info['uitdager'] == $_SESSION['naam']) {
                    $winner = $duel_info['uitdager'];
                    $message = "Você ficou inativo, seu oponente venceu!";
                } else if ($duel_info['tegenstander'] == $_SESSION['naam']) {
                    $winner = $duel_info['tegenstander'];
                    $message = "O oponente ficou inativo, você venceu!";
                }
            }
            DB::exQuery("UPDATE `duel` SET `winner`='$winner' WHERE `id`='" . $duel_info['id'] . "'");
            $good = 2;
        } else {
            if ($duel_info['uitdager'] == $_SESSION['naam']) {
                DB::exQuery("UPDATE `gebruikers` SET `online`=UNIX_TIMESTAMP() WHERE `username` =  '" . $duel_info['uitdager'] . "'");
            } else {
                DB::exQuery("UPDATE `gebruikers` SET `online`=UNIX_TIMESTAMP() WHERE `username` = '" . $duel_info['tegenstander'] . "'");
            }

            $uitdager_info = pokemon_data($duel_info['u_pokemonid']);
            $uitdager_info['naam_goed'] = pokemon_naam($uitdager_info['naam'], $uitdager_info['roepnaam']);
            $uitdager_info['username'] = $duel_info['uitdager'];
            $uitdager_info['table']['fight'] = "pokemon_speler_gevecht";

            $tegenstander_info = pokemon_data($duel_info['t_pokemonid']);
            $tegenstander_info['naam_goed'] = pokemon_naam($tegenstander_info['naam'], $tegenstander_info['roepnaam']);
            $tegenstander_info['username'] = $duel_info['tegenstander'];
            $tegenstander_info['table']['fight'] = "pokemon_speler_gevecht";

            $zmove = '';
            $zmove_table = '';

            //Check Who attacks
            if ($duel_info['uitdager'] == $_SESSION['naam']) {
                //Load All Opponent Info
                $opponent_info = &$tegenstander_info;
                //Load All Pokemon Info
                $pokemon_info = &$uitdager_info;
                //Other Check
                $attack_status['next_turn'] = $duel_info['tegenstander'];
                $attack_status['you'] = "u";
                $attack_status['opponent'] = "t";
                $attack_status['table']['you_busy'] = "aanval_bezig_u";
                $attack_status['table']['other_busy'] = "aanval_bezig_t";

                $zmove_table = 'zmove_u';
            } else if ($duel_info['tegenstander'] == $_SESSION['naam']) {
                //Load All Opoonent Info
                $opponent_info = &$uitdager_info;
                //Load All Pokemon Info
                $pokemon_info = &$tegenstander_info;
                //Other Check
                $attack_status['next_turn'] = $duel_info['uitdager'];
                $attack_status['you'] = "t";
                $attack_status['opponent'] = "u";
                $attack_status['table']['you_busy'] = "aanval_bezig_t";
                $attack_status['table']['other_busy'] = "aanval_bezig_u";

                $zmove_table = 'zmove_t';
            }

            if ($pokemon_info['leven'] <= 0) {
                $message = $pokemon_info['naam_goed'] . " foi derrotado. Troque-o agora! ";
            } else {
                if (isset($_GET['zmove'])) {
                    if ($_GET['zmove'] == 'y') {
                        if ($duel_info[$zmove_table] == 0) {
                            $zmove = zMoves::move($pokemon_info)[0];
                            if ($zmove == $_GET['attack_name']) {
                                DB::exQuery("UPDATE `duel` SET `".$zmove_table."`='1' WHERE id='" . $duel_info['id'] . "'");
                            } else {
                                echo "Error: 4005";
                                exit;
                            }
                        } else {
                            echo "Você não pode usar Z-MOVES nesta batalha!";
                            exit;
                        }
                    } else {
                        echo "Error: 4004";
                        exit;
                    }
                } else if (($_GET['attack_name'] != $pokemon_info['aanval_1']) AND ( $_GET['attack_name'] != $pokemon_info['aanval_2']) AND ( $_GET['attack_name'] != $pokemon_info['aanval_3']) AND ( $_GET['attack_name'] != $pokemon_info['aanval_4'])) {
                    echo "Error: 4003<br />Info: " . $_GET['attack_name'] . "/" . $pokemon_info['id'];
                    exit;
                }
                //Attack Begin
                //Set Default Attack Values
                $attack_status['continu'] = 1;
                $message_add = "";
                $stappen = "";

                if (!empty($duel_info[$attack_status['table']['you_busy']])) {
                    if (($duel_info[$attack_status['table']['you_busy']] != $pokemon_info['aanval_1']) AND ( $duel_info[$attack_status['table']['you_busy']] != $pokemon_info['aanval_2']) AND ( $duel_info[$attack_status['table']['you_busy']] != $pokemon_info['aanval_3']) AND ( $duel_info[$attack_status['table']['you_busy']] != $pokemon_info['aanval_4'])) {
                        DB::exQuery("UPDATE `duel` SET " . $attack_status['table']['you_busy'] . "='' WHERE id='" . $duel_info['id'] . "'");
                    } else {
                        $attack_name = $duel_info[$attack_status['table']['you_busy']];
                    }
                }
                if (!empty($duel_info[$attack_status['table']['other_busy']])) {
                    $auto_turn = $duel_info[$attack_status['table']['other_busy']];
                }

                //WEATHER (WL >:D)
                $weather = new Weather ($duel_info);
                $weather->table = 'duel';

                if ( $weather->controller ) {
                    echo $weather->weather_turns ($pokemon_info, $opponent_info);
                    echo $weather->weather_text('', '<br>');
                    echo $weather->weather_damage ($pokemon_info);
                    echo $weather->weather_heal ($pokemon_info);
                }

                //Check For effect
                if (!empty($pokemon_info['effect'])) {
                    $new_attacker_info['hoelang'] = $pokemon_info['hoelang'] - 1;
                    $new_attacker_info['effect'] = $pokemon_info['effect'];

                    if ($pokemon_info['effect'] == "Flinch") {
                        //Effect Empty
                        $new_attacker_info['effect'] = "";
                        $attack_status['continu'] = 0;
                        $attack_status['continu2'] = 0;
                        $message = $pokemon_info['naam_goed'] . " " . $txt['flinched'];
                    } else if ($pokemon_info['effect'] == "Sleep") {
                        $attack_status['continu'] = 0;
                        $attack_status['continu2'] = 0;
                        if ($new_attacker_info['hoelang'] >= 1) {
                            $message = $pokemon_info['naam_goed'] . " " . $txt['sleeps'];
                        } else {
                            $attack_status['continu2'] = 1;
                            $pre_message = $pokemon_info['naam_goed'] . " " . $txt['awake'] . "<br/>";
                            $new_attacker_info['effect'] = "";
                        }
                    } else if ($pokemon_info['effect'] == "Freeze") {
                        $attack_status['continu'] = 0;
                        $attack_status['continu2'] = 0;
                        if ($new_attacker_info['hoelang'] >= 1) {
                            $message = $pokemon_info['naam_goed'] . " " . $txt['frozen'];
                        } else {
                            $attack_status['continu2'] = 1;
                            $pre_message = $pokemon_info['naam_goed'] . " " . $txt['no_frozen'] . "<br/>";
                            $new_attacker_info['effect'] = "";
                        }
                    } else if ($pokemon_info['effect'] == "Paralyzed") {
                        if ($new_attacker_info['hoelang'] == 0) {
                            $attack_status['continu'] = 0;
                            $attack_status['continu2'] = 1;
                            $pre_message = $pokemon_info['naam_goed'] . " " . $txt['not_paralyzed'] . "<br/>";
                            $new_attacker_info['effect'] = "";
                        } else if (rand(1, 4) == 2) {
                            $attack_status['continu'] = 1;
                        } else {
                            $attack_status['continu'] = 0;
                            $attack_status['continu2'] = 0;
                            $message = $pokemon_info['naam_goed'] . " " . $txt['paralyzed'];
                        }
                    } else if ($pokemon_info['effect'] == "Confused") {
                        if ($new_attacker_info['hoelang'] == 0) {
                            $attack_status['continu'] = 0;
                            $attack_status['continu2'] = 1;
                            $pre_message = $pokemon_info['naam_goed'] . " não está mais confuso.<br/>";
                            $new_attacker_info['effect'] = "";
                        } else {
                            $attack_status['continu'] = 0;
                            $attack_status['continu2'] = 0;

                            if (rand(1, 2) == 1) {
                                $message = $pokemon_info['naam_goed'] . " está confuso e se atacou!";
                                $recoil_d = damage_controller($pokemon_info, $pokemon_info, array('sterkte' => 40, 'soort' => 'Normal', 'Special' => false));
                                $rec_left = $pokemon_info['leven'] - $recoil_d;
                                if ($rec_left < 0) {
                                    $rec_left = 0;
                                }
                                DB::exQuery("UPDATE `" . $pokemon_info['table']['fight'] . "` SET `leven`='" . $rec_left . "' WHERE `id`='" . $pokemon_info['id'] . "'");
                            } else {
                                $message = $pokemon_info['naam_goed'] . " está confuso.";
                            }
                        }
                    } else if ($new_attacker_info['hoelang'] == 0) {
                        $new_attacker_info['effect'] = "";
                    }

                    if ($attack_status['continu'] == 0) {
                        if (!isset($rec_left) || (isset($rec_left) && $rec_left > 0)) {
                            $message .= "<br />" . $opponent_info['naam_goed'] . " " . $txt['opponent_choose_attack'];
                        }
                        DB::exQuery("UPDATE " . $pokemon_info['table']['fight'] . " SET `effect`='" . $new_attacker_info['effect'] . "', `hoelang`='" . $new_attacker_info['hoelang'] . "' WHERE id='" . $pokemon_info['id'] . "'");

                        $pokemon_info['effect'] = $new_attacker_info['effect'];

                        if ($attack_status['continu2'] == 0) {
                            $good = 1;
                            if ($recoil_d > 0 && isset($rec_left) && $rec_left <= 0) {
                                include_once '../../app/classes/League_battle.php';
                                $league_battle = League_battle::select_duel($_GET['duel_id']);
                                if ($league_battle) {
                                    if ($pokemon_info['user_id'] == $league_battle->getUser_id2()) {
                                        $league_battle->setPontos_user1($league_battle->getPontos_user1() + 1);
                                    } else {
                                        $league_battle->setPontos_user2($league_battle->getPontos_user2() + 1);
                                    }
                                    $league_battle->update();
                                }
                                //Gevecht klaar als dit de tegenstander is
                                $levenover = 0;
                                //Alle pokemons van de speler tellen
                                $uitdager = DB::exQuery("SELECT psg.id FROM pokemon_speler_gevecht AS psg INNER JOIN pokemon_speler AS ps ON psg.id = ps.id WHERE ps.user_id='" . $pokemon_info['user_id'] . "' AND psg.leven>'0' AND ps.ei='0'")->num_rows;
                                //Kan hij geen pokemon wisselen
                                if ($uitdager == 1 || ($league_battle &&
                                        (($league_battle->getUser_id2() == $pokemon_info['user_id'] && $league_battle->getN_pokemons() == $league_battle->getPontos_user1()) ||
                                        ($league_battle->getUser_id1() == $pokemon_info['user_id'] && $league_battle->getN_pokemons() == $league_battle->getPontos_user2())))) {
                                    $aantalbericht = "O duelo acabou.";
                                    $attack_status['next_turn'] = "end_screen";
                                    $attack_status['winner'] = $opponent_info['username'];
                                    $good = 2;
                                } else {
                                    $aantalbericht = $pokemon_info['username'] . " está trocando de Pokémon.";
                                    $attack_status['next_move'] = "wisselen";
                                    $good = 1;
                                }

                                $message .= "<br/>" . $pre_message . $pokemon_info['naam_goed'] . " " . $txt['is_ko'] . "<br/>" . $aantalbericht;
                                $return = duel_one_dead($duel_info, $attack_status['opponent'], $opponent_info, $pokemon_info, $txt);
                                $message .= $return['bericht'];
                            }

                            $new_exp = $pokemon_info['exp'] + 0;
                            $new_exp_opponent = $opponent_info['exp'] + $return['exp'];

                            $request = $pre_message . $message . " | " .
                                    $good . " | " .
                                    $attack_status['opponent'] . " | " .
                                    $opponent_info['leven'] . " | " .
                                    $opponent_info['levenmax'] . " | " .
                                    $life_decrease . " | " .
                                    $opponent_info['id'] . " | " .
                                    $new_exp_opponent . " | " .
                                    $opponent_info['expnodig'] . " | " .
                                    $auto_turn . " | " .
                                    $attack_status['you'] . " | " .
                                    $pokemon_info['opzak_nummer'] . " | " .
                                    $new_exp . " | " .
                                    $pokemon_info['expnodig'] . " | " .
                                    $stappen . " | " .
                                    $pokemon_info['levenmax'] . " | " .
                                    $recoil_d . " | " .
                                    $rec_left . " | " .
                                    "0 | " .
                                    "0 | " .
                                    "0 | " .
                                    $pokemon_info['effect'] . " | " .
                                    $opponent_info['effect'] . " | " .
                                    $weather->clima." | ".
                                    $pokemon_info['leven']." | ";

                            DB::exQuery("UPDATE `duel` SET `winner`='" . $attack_status['winner'] . "', `laatste_beurt_tijd`='" . $time . "', `laatste_beurt`='" . $_GET['wie'] . "', `laatste_aanval`='" . $_GET['attack_name'] . "', `laatste_aanval2`='" . $_GET['attack_name'] . "', `schade`='" . $life_decrease . "', `volgende_beurt`='" . $attack_status['next_turn'] . "', `volgende_zet`='" . $attack_status['next_move'] . "', `request`='" . str_replace(" | ", "||", $request) . "', `beurten`=`beurten`+1 WHERE `id`='" . $_GET['duel_id'] . "'");

                            echo $request;
                            exit;
                        }
                    }
                }

                if ($_GET['attack_name'] == "Metronome") {
                    $attack_inforand = DB::exQuery("SELECT `naam` FROM `aanval` WHERE is_zmoves='0' order by rand() limit 1")->fetch_assoc();
                    $_GET['attack_name'] = $attack_inforand['naam'];
                }
                    
                //Load Attack Infos
                $attack_info = atk($_GET['attack_name'], $pokemon_info);

                $weather->weather_create ($pokemon_info, $opponent_info, $attack_info);

                if (empty($attack_info['naam'])) {
                    echo "Error: 4002<br />Info: " . $attack_name . " | " . $good;
                    exit;
                }

                //Hit ratio down
                $htdown = $pokemon_info['hit_ratio_down'] * 2;
                if ($htdown > 0)
                    $attack_info['mis'] += $htdown;

                //Check if attack does hit
                if ((($attack_info['mis'] != 0) AND ( $duel_info[$attack_status['table']['you_busy']] == '') AND ( rand(0, 100) <= $attack_info['mis'])) OR ( $duel_info[$attack_status['table']['other_busy']] == 'Fly') OR ( $duel_info[$attack_status['table']['other_busy']] == 'Dig') OR ( $duel_info[$attack_status['table']['other_busy']] == 'Dive') OR ( $duel_info[$attack_status['table']['other_busy']] == 'Bounce')) {
                    //OR ( $duel_info[$attack_status['table']['other_busy']] == 'Fly') OR ( $duel_info[$attack_status['table']['other_busy']] == 'Dig') OR ( $duel_info[$attack_status['table']['other_busy']] == 'Dive') OR ( $duel_info[$attack_status['table']['other_busy']] == 'Bounce')
                    $message = $pokemon_info['naam_goed'] . " usou " . $attack_info['naam'] . ", mas errou!";
                    $message .= "<br />" . $opponent_info['naam_goed'] . " " . $txt['opponent_choose_attack'];

                    $attack_info['soort'] = 'Fail';
                } else {
                    if ($attack_info['naam'] == "Fling") 
                        $attack_info['sterkte'] = rand(20, 170);

                    //Check if attack does have power
                    if ($attack_info['sterkte'] != 0) {
                        //Calculate Life Decreasing
                        $life_decrease = damage_controller($pokemon_info, $opponent_info, $attack_info, $weather->clima);
                    } else if ($attack_info['hp_schade'] != 0) {
                        $life_decrease = $attack_info['hp_schade'];
                    }

                    //If attack hits more then once
                    if ($attack_info['aantalkeer'] != "1") {
                        $multi_hit = multiple_hits($attack_info, $life_decrease);
                        $life_decrease = $multi_hit['damage'];
                        $message_add .= $multi_hit['message'];
                    }


                    //Does the attack have Critical Hit?
                    if ($attack_info['critical'] == 1 && !in_array($opponent_info['ability'], array('4', '75'))) {
                        $critic_change = round(($pokemon_info['speed'] * 100) / 128);
                        if (rand(0, 100) <= $critic_change || in_array($attack_info['naam'], array('Frost Breath', 'Storm Throw')) || ($pokemon_info['ability'] == '196' && $opponent_info['effect'] == 'Poisoned')) {
                            $attack_info['sterkte'] = $attack_info['sterkte'] * 1.5;
                            $message_add .= "<br />Ataque critico!";
                        }
                    }
                    //Attack Have to Steps
                    if ($attack_info['stappen'] == 2) {
                        //attack have to load first
                        if (($attack_info['laden'] == 'voor')) {
                            if (empty($duel_info[$attack_status['table']['you_busy']])) {
                                $life_off = 0;
                                $good = 1;
                                $life_decrease = 0;
                                $attack_info['soort'] = 'Charge';

                                $stappen = $attack_info['naam'];
                                $message = $pokemon_info['naam_goed'] . " está carregando " . $attack_info['naam'];

                                $message .= "<br />" . $opponent_info['naam_goed'] . " " . $txt['opponent_choose_attack'];
                                DB::exQuery("UPDATE `duel` SET " . $attack_status['table']['you_busy'] . "='" . $attack_info['naam'] . "' WHERE id='" . $duel_info['id'] . "'");
                            } else {
                                DB::exQuery("UPDATE `duel` SET " . $attack_status['table']['you_busy'] . "='' WHERE id='" . $duel_info['id'] . "'");
                                $attack_info['stappen'] = 1;
                            }
                            //Attack is recharging afterwards
                        } else if (($attack_info['laden'] == 'na')) {
                            if (empty($duel_info[$attack_status['table']['you_busy']])) {

                                $stappen = $attack_info['naam'];
                                DB::exQuery("UPDATE `duel` SET " . $attack_status['table']['you_busy'] . "='" . $attack_info['naam'] . "' WHERE id='" . $duel_info['id'] . "'");
                                $attack_info['stappen'] = 1;
                            } else {
                                $life_off = 0;
                                $good = 1;
                                $life_decrease = 0;
                                $attack_info['soort'] = 'Recharge';

                                $message = $pokemon_info['naam_goed'] . " está se recuperando de " . $attack_info['naam'];

                                $message .= "<br />" . $opponent_info['naam_goed'] . " " . $txt['opponent_choose_attack'];
                                DB::exQuery("UPDATE `duel` SET " . $attack_status['table']['you_busy'] . "='' WHERE id='" . $duel_info['id'] . "'");
                            }
                        }
                    }

                    if ($attack_info['stappen'] != 2) {
                        //Does the attack have any side effects
                        if ((!empty($attack_info['effect_naam'])) AND ($attack_info['effect_kans'] != 0)) {
                            if ((($attack_info['effect_kans'] == 100) || (rand(0, 100) <= ($attack_info['effect_kans'])))) {
                                $effect_info = DB::exQuery("SELECT * FROM effect WHERE actie='" . $attack_info['effect_naam'] . "'")->fetch_assoc();
                                if (($effect_info['wat'] == "negatief_tijd") &&
                                        ( $effect_info['naam'] != 'Burn') &&
                                        ($effect_info['naam'] != 'Freeze' || ($opponent_info['type1'] != 'Ice' && $opponent_info['type2'] != 'Ice')) &&
                                        ($effect_info['naam'] != 'Poisoned' || ($opponent_info['type1'] != 'Poison' && $opponent_info['type2'] != 'Poison' && $opponent_info['type1'] != 'Steel' && $opponent_info['type2'] != 'Steel')) &&
                                        ($effect_info['naam'] != 'Seeding' || ($opponent_info['type1'] != 'Grass' && $opponent_info['type2'] != 'Grass'))
                                ) {
                                    $turns = 0;
                                    //Sleep or Freeze
                                    if (($effect_info['id'] == 28) OR ( $effect_info['id'] == 32))
                                        $turns = rand(1, 6);
                                    //Confused 
                                    else if ($effect_info['id'] == 33)
                                        $turns = rand(1, 4);
                                    //Flinch
                                    else if ($effect_info['id'] == 34)
                                        $turns = 1;
                                    //Save to opponent
                                    DB::exQuery("UPDATE " . $opponent_info['table']['fight'] . " SET effect='" . $effect_info['actie'] . "', hoelang='" . $turns . "' WHERE id='" . $opponent_info['id'] . "'");
                                    $message = $pokemon_info['naam_goed'] . $txt['did'] . $attack_info['naam'] . ", teve efeito.";
                                    if (empty($opponent_info['effect']))
                                        $message .= "<br />" . $opponent_info['naam_goed'] . " agora está " . $effect_info['naam'];
                                    $message .= "<br />" . $opponent_info['naam_goed'] . " " . $txt['opponent_choose_attack'];
                                    $opponent_info['effect'] = $effect_info['naam'];
                                } else if ($effect_info['wat'] == "negatief") {
                                    if (($effect_info['actie'] == "Defence_down") OR ($effect_info['actie'] == "Defence_down_2")) {
                                        //Defence Down
                                        $new_stat = round(($opponent_info['defence'] / 100) * (100 - $effect_info['kracht']));
                                        $sql      = "`defence`='" . $new_stat . "'";
                                        $text     = ' está com a Defesa diminuida.';
                                    } else if (($effect_info['actie'] == "Speed_down") OR ($effect_info['actie'] == "Speed_down_2")) {
                                        //Speed Down
                                        $new_stat = round(($opponent_info['speed'] / 100) * (100 - $effect_info['kracht']));
                                        $sql      = "`speed`='" . $new_stat . "'";
                                        $text     = ' está com a Velocidade diminuida.';
                                    } else if (($effect_info['actie'] == "Spc.defence_down") OR ($effect_info['actie'] == "Spc.defence_down_2")) {
                                        //Special Defence Down
                                        $new_stat = round(($opponent_info['spc.defence'] / 100) * (100 - $effect_info['kracht']));
                                        $sql      = "`spc.defence`='" . $new_stat . "'";
                                        $text     = ' está com a Sp. Defesa diminuida.';
                                    } else if (($effect_info['actie'] == "Attack_down") OR ($effect_info['actie'] == "Attack_down_2")) {
                                        //Attack Down
                                        $new_stat = round(($opponent_info['attack'] / 100) * (100 - $effect_info['kracht']));
                                        $sql      = "`attack`='" . $new_stat . "'";
                                        $text     = ' está com o Ataque diminuido.';
                                    } else if ($effect_info['actie'] == "Attack_defence_down") {
                                        //Attack& Speed Down
                                        $new_stat = round(($opponent_info['attack'] / 100) * (100 - $effect_info['kracht']));
                                        $sql      = "`attack`='" . $new_stat . "'";
                                        $new_stat = round(($opponent_info['defence'] / 100) * (100 - $effect_info['kracht']));
                                        $sql .= ", `defence`='" . $new_stat . "'";
                                        $text = ' está com o Ataque e Speed diminuidos.';
                                    } else if ($effect_info['actie'] == "defence_spc.defence_down") {
                                        //Spc.Defence & Defence Down          
                                        $new_stat = round(($opponent_info['defence'] / 100) * (100 - $effect_info['kracht']));
                                        $sql      = "`defence`='" . $new_stat . "'";
                                        $new_stat = round(($opponent_info['spc.defence'] / 100) * (100 - $effect_info['kracht']));
                                        $sql .= ", `spc.defence`='" . $new_stat . "'";
                                        $text = ' está com a Defesa diminuida.';
                                    } else if ($effect_info['actie'] == "Hit_ratio_down") {
                                        //Hit Ratio Down          
                                        $new_stat = $opponent_info['hit_ratio_down'] + 1;
                                        $sql      = "`hit_ratio_down`='" . $new_stat . "'";
                                        $text     = ' está com a Chance de Acerto diminuida.';
                                    }
                                    DB::exQuery("UPDATE " . $opponent_info['table']['fight'] . " SET " . $sql . " WHERE id='" . $opponent_info['id'] . "'");
                                    $message_add .= "<br /> " . $opponent_info['naam_goed'] . " " . $text;
                                } else if ($effect_info['wat'] == "positief") {
                                    if (($effect_info['actie'] == "Defence_up") OR ($effect_info['actie'] == "Defence_up_2")) {
                                        //Defence Up
                                        $new_stat = round(($pokemon_info['defence'] / 100) * (100 + $effect_info['kracht']));
                                        $sql      = "`defence`='" . $new_stat . "'";
                                        $text     = ' está com a Defesa aumentada.';
                                    } else if (($effect_info['actie'] == "Attack_up") OR ($effect_info['actie'] == "Attack_up_2")) {
                                        //Attack up
                                        $new_stat = round(($pokemon_info['attack'] / 100) * (100 + $effect_info['kracht']));
                                        $sql      = "`attack`='" . $new_stat . "'";
                                        $text     = ' está com o Ataque aumentado.';
                                    } else if ($effect_info['actie'] == "Speed_up_2") {
                                        //Speed Up
                                        $new_stat = round(($pokemon_info['speed'] / 100) * (100 + $effect_info['kracht']));
                                        $sql      = "`speed`='" . $new_stat . "'";
                                        $text     = ' está com a Speed aumentada.';
                                    } else if ($effect_info['actie'] == "Spc.defence_up_2") {
                                        //Spc. Defence Up
                                        $new_stat = round(($pokemon_info['spc.defence'] / 100) * (100 + $effect_info['kracht']));
                                        $sql .= "`spc.defence`='" . $new_stat . "'";
                                        $text = ' está com a Sp. Defesa aumentada.';
                                    } else if ($effect_info['actie'] == "All_up") {
                                        //All stats Up          
                                        $new_stat = round(($pokemon_info['attack'] / 100) * (100 + $effect_info['kracht']));
                                        $sql      = "`attack`='" . $new_stat . "'";
                                        $new_stat = round(($pokemon_info['defence'] / 100) * (100 + $effect_info['kracht']));
                                        $sql .= ", `defence`='" . $new_stat . "'";
                                        $new_stat = round(($pokemon_info['spc.defence'] / 100) * (100 + $effect_info['kracht']));
                                        $sql .= ", `spc.defence`='" . $new_stat . "'";
                                        $new_stat = round(($pokemon_info['spc.attack'] / 100) * (100 + $effect_info['kracht']));
                                        $sql .= ", `spc.attack`='" . $new_stat . "'";
                                        $new_stat = round(($pokemon_info['speed'] / 100) * (100 + $effect_info['kracht']));
                                        $sql .= ", `speed`='" . $new_stat . "'";
                                        $text = ' está com todos atributos aumentados.';
                                    } else if ($effect_info['actie'] == "Attack_defence_up") {
                                        //Attack & Defence Up         
                                        $new_stat = round(($pokemon_info['attack'] / 100) * (100 + $effect_info['kracht']));
                                        $sql      = "`attack`='" . $new_stat . "'";
                                        $new_stat = round(($pokemon_info['defence'] / 100) * (100 + $effect_info['kracht']));
                                        $sql .= ", `defence`='" . $new_stat . "'";
                                        $text = ' está com o Ataque e Defesa aumentados.';
                                    } else if ($effect_info['actie'] == "Defence_speed_up_2") {
                                        //Defence & Speed Up         
                                        $new_stat = round(($pokemon_info['defence'] / 100) * (100 + $effect_info['kracht']));
                                        $sql      = "`defence`='" . $new_stat . "'";
                                        $new_stat = round(($pokemon_info['speed'] / 100) * (100 + $effect_info['kracht']));
                                        $sql .= ", `speed`='" . $new_stat . "'";
                                        $text = ' está com a Defesa e Speed aumentados.';
                                    } else if ($effect_info['actie'] == "spc_up") {
                                        //Specials Up    
                                        $new_stat = round(($pokemon_info['spc.attack'] / 100) * (100 + $effect_info['kracht']));
                                        $sql      = "`spc.attack`='" . $new_stat . "'";
                                        $new_stat = round(($pokemon_info['spc.defence'] / 100) * (100 + $effect_info['kracht']));
                                        $sql .= ", `spc.defence`='" . $new_stat . "'";
                                        $text = ' está com os atributos esp aumentados.';
                                    } else if ($effect_info['actie'] == "defence_spc.defence_up") {
                                        //Defences UP        
                                        $new_stat = round(($pokemon_info['defence'] / 100) * (100 + $effect_info['kracht']));
                                        $sql      = "`defence`='" . $new_stat . "'";
                                        $new_stat = round(($pokemon_info['spc.defence'] / 100) * (100 + $effect_info['kracht']));
                                        $sql .= ", `spc.defence`='" . $new_stat . "'";
                                        $text = ' está com a Defesa aumentada.';
                                    } else if ($effect_info['actie'] == "attack_speed_up") {
                                        //Attack & Speed Up         
                                        $new_stat = round(($pokemon_info['attack'] / 100) * (100 + $effect_info['kracht']));
                                        $sql      = "`attack`='" . $new_stat . "'";
                                        $new_stat = round(($pokemon_info['speed'] / 100) * (100 + $effect_info['kracht']));
                                        $sql .= ", `speed`='" . $new_stat . "'";
                                        $text = ' está com o Ataque e Speed aumentados.';
                                    } else if ($effect_info['actie'] == "Spc.Attack_up_2") {
                                        //Spc. Attack Up    
                                        $new_stat = round(($pokemon_info['spc.attack'] / 100) * (100 + $effect_info['kracht']));
                                        $sql      = "`spc.attack`='" . $new_stat . "'";
                                        $text     = ' está com Sp. Ataque aumentado.';
                                    }
                                    DB::exQuery("UPDATE " . $pokemon_info['table']['fight'] . " SET " . $sql . " WHERE id='" . $pokemon_info['id'] . "'");
                                    $message_add .= "<br /> " . $pokemon_info['naam_goed'] . " " . $text;
                                } else if ($effect_info['wat'] == "beide") {
                                    if ($effect_info['actie'] == "attack_defence_up_speed_down") {
                                        $new_stat = round(($pokemon_info['attack'] / 100) * (100 + $effect_info['kracht']));
                                        $sql = "attack='" . $new_stat . "'";
                                        $new_stat = round(($pokemon_info['defence'] / 100) * (100 + $effect_info['kracht']));
                                        $sql .= "defence='" . $new_stat . "'";
                                        $new_stat = round(($opponent_info['speed'] / 100) * (100 - $effect_info['kracht']));
                                        $sql .= "speed='" . $new_stat . "'";
                                        DB::exQuery("UPDATE " . $pokemon_info['table']['fight'] . " SET " . $sql . " WHERE id='" . $pokemon_info['id'] . "'");
                                        $message_add .= "<br /> " . $pokemon_info['naam_goed'] . " está agora com Ataque e Defesa aumentados porém com a Speed diminuida.";
                                    }
                                }
                            }
                        }

                        //Does the attack have an extra effect
                        else if (!empty($attack_info['extra'])) {
                            if ($attack_info['extra'] == 'half_attack_recover') {
                                if ($pokemon_info['leven'] != $pokemon_info['levenmax']) {
                                    $rec_left = $pokemon_info['leven'] + round($life_decrease / 2);
                                    if ($rec_left >= $pokemon_info['levenmax']) {
                                        $rec_left = $pokemon_info['levenmax'];
                                    }
                                    DB::exQuery("UPDATE `" . $pokemon_info['table']['fight'] . "` SET `leven`='" . $rec_left . "' WHERE `id`='" . $pokemon_info['id'] . "'");
                                    $message_add .= "<br /> " . $pokemon_info['naam_goed'] . " está se recuperando. ";
                                    $pokemon_info['leven'] = $rec_left;
                                }
                            } else if ($attack_info['extra'] == 'uphalfhp') {
                                if ($pokemon_info['leven'] != $pokemon_info['levenmax']) {
                                    $rec_left = $pokemon_info['leven'] + round($pokemon_info['levenmax'] / 2);
                                    if ($rec_left >= $pokemon_info['levenmax']) {
                                        $rec_left = $pokemon_info['levenmax'];
                                    }
                                    DB::exQuery("UPDATE `" . $pokemon_info['table']['fight'] . "` SET `leven`='" . $rec_left . "' WHERE `id`='" . $pokemon_info['id'] . "'");
                                    $message_add .= "<br /> " . $pokemon_info['naam_goed'] . " está se recuperando. ";
                                    $pokemon_info['leven'] = $rec_left;
                                }
                            } else if ($attack_info['extra'] == 'up75percenthp') {
                                if ($pokemon_info['leven'] != $pokemon_info['levenmax']) {
                                    $rec_left = $pokemon_info['leven'] + round(($life_decrease / 100) * 75);
                                    if ($rec_left >= $pokemon_info['levenmax']) {
                                        $rec_left = $pokemon_info['levenmax'];
                                    }
                                    DB::exQuery("UPDATE `" . $pokemon_info['table']['fight'] . "` SET `leven`='" . $rec_left . "' WHERE `id`='" . $pokemon_info['id'] . "'");
                                    $message_add .= "<br /> " . $pokemon_info['naam_goed'] . " está se recuperando. ";
                                    $pokemon_info['leven'] = $rec_left;
                                }
                            } else if ($attack_info['extra'] == 'sleep_half_attack_recover') {
                                if ($pokemon_info['leven'] != $pokemon_info['levenmax']) {
                                    if ($opponent_info['effect'] == "Sleep") {
                                        $rec_left = $pokemon_info['leven'] + round($life_decrease / 2);
                                        if ($rec_left >= $pokemon_info['levenmax']) {
                                            $rec_left = $pokemon_info['levenmax'];
                                        }
                                        DB::exQuery("UPDATE `" . $pokemon_info['table']['fight'] . "` SET `leven`='" . $rec_left . "' WHERE `id`='" . $pokemon_info['id'] . "'");
                                        $message_add .= "<br /> " . $pokemon_info['naam_goed'] . " está se recuperando. ";
                                        $pokemon_info['leven'] = $rec_left;
                                    }
                                }
                            }
                        }

                        //Does The attack Hits in recoil?
                        else if ($attack_info['recoil'] > 1) {
                            $recoil_d = round($attack_info['levenmax'] / 20);
                            $rec_left = $pokemon_info['leven'] - $recoil_d;
                            if ($rec_left < 0) {
                                $rec_left = 0;
                            }
                            DB::exQuery("UPDATE `" . $pokemon_info['table']['fight'] . "` SET `leven`='" . $rec_left . "' WHERE `id`='" . $pokemon_info['id'] . "'");
                            $message_add .= "<br /> " . $pokemon_info['naam_goed'] . " atinge-se com recuo. ";
                        }
                    }
                }

                if ($life_decrease > 0)
                    $life_off = 1;
                else
                    $life_off = 0;

                $levenover = $opponent_info['leven'] - $life_decrease;

                //False Swipe
                if ($attack_info['naam'] == "False Swipe" OR $attack_info['naam'] == "Hold Back") {
                    if ($levenover <= 0) {
                        $life_decrease          = 0;
                        $opponent_info['leven'] = 1;
                        $levenover              = 1;
                    }
                }
                

                //Hits with burn?
                if ($pokemon_info['effect'] == 'Burn') {
                    $recoil_d += round($pokemon_info['levenmax'] / 8);
                    $rec_left = $pokemon_info['leven'] - $recoil_d;
                    if ($rec_left < 0) {
                        $rec_left = 0;
                        $recoil_d = $pokemon_info['leven'];
                    }
                    DB::exQuery("UPDATE `" . $pokemon_info['table']['fight'] . "` SET `leven`='" . $rec_left . "' WHERE `id`='" . $pokemon_info['id'] . "'");
                    $message_add .= "<br /> " . $pokemon_info['naam_goed'] . " está queimando. ";
                }

                //Hits with poisoned?
                else if ($pokemon_info['effect'] == 'Poisoned') {
                    $recoil_d += round($pokemon_info['levenmax'] / 8);
                    $rec_left = $pokemon_info['leven'] - $recoil_d;
                    if ($rec_left < 0) {
                        $rec_left = 0;
                        $recoil_d = $pokemon_info['leven'];
                    }
                    DB::exQuery("UPDATE `" . $pokemon_info['table']['fight'] . "` SET `leven`='" . $rec_left . "' WHERE `id`='" . $pokemon_info['id'] . "'");
                    $message_add .= "<br /> " . $pokemon_info['naam_goed'] . " está envenenado. ";
                }

                //Hits with seeding?
                else if ($pokemon_info['effect'] == 'Seeding' && $levenover > 0) {
                    $recoil_d += round($pokemon_info['levenmax'] / 8);
                    $rec_left = $pokemon_info['leven'] - $recoil_d;
                    if ($rec_left < 0) {
                        $rec_left = 0;
                        $recoil_d = $pokemon_info['leven'];
                    }
                    DB::exQuery("UPDATE `" . $pokemon_info['table']['fight'] . "` SET `leven`='" . $rec_left . "' WHERE `id`='" . $pokemon_info['id'] . "'");
                    $message_add .= "<br /> " . $pokemon_info['naam_goed'] . " tendo sua vida roubada. ";
                }

                $good = 1;
                if ($life_decrease == 0 && $recoil_d == 0) {
                    if (!stripos($message, "teve efeito") && !stripos($message, "mas errou") && $attack_info['stappen'] != 2) {
                        $message = $pokemon_info['naam_goed'] . " " . $txt['did'] . " " . $attack_info['naam'] . $txt['hit!'] . $message_add;
                        $message .= "<br />" . $opponent_info['username'] . " " . $txt['opponent_choose_attack'];
                    }
                } else if ($levenover <= 0) {
                    include_once '../../app/classes/League_battle.php';
                    $league_battle = League_battle::select_duel($_GET['duel_id']);
                    if ($league_battle) {
                        if ($opponent_info['user_id'] == $league_battle->getUser_id2()) {
                            $league_battle->setPontos_user1($league_battle->getPontos_user1() + 1);
                        } else {
                            $league_battle->setPontos_user2($league_battle->getPontos_user2() + 1);
                        }
                        $league_battle->update();
                    }
                    //Gevecht klaar als dit de tegenstander is
                    $levenover = 0;
                    //Alle pokemons van de speler tellen
                    $tegenstander = DB::exQuery("SELECT psg.id FROM pokemon_speler_gevecht AS psg INNER JOIN pokemon_speler AS ps ON psg.id = ps.id WHERE ps.user_id='" . $opponent_info['user_id'] . "' AND psg.leven>'0' AND ps.ei='0'")->num_rows;
                    //Kan hij geen pokemon wisselen
                    if ($tegenstander == 1 || ($league_battle &&
                            (($league_battle->getUser_id2() == $opponent_info['user_id'] && $league_battle->getN_pokemons() == $league_battle->getPontos_user1()) ||
                            ($league_battle->getUser_id1() == $opponent_info['user_id'] && $league_battle->getN_pokemons() == $league_battle->getPontos_user2())))) {
                        $aantalbericht = "O duelo acabou.";
                        $attack_status['next_turn'] = "end_screen";
                        $attack_status['winner'] = $_SESSION['naam'];
                        $good = 2;
                    } else {
                        $aantalbericht = $opponent_info['username'] . " está trocando de Pokémon. ";
                        $attack_status['next_move'] = "wisselen";
                        $good = 1;
                    }
                    $message = $pokemon_info['naam_goed'] . " " . $txt['use_attack_1'] . " " . $attack_info['naam'] . $txt['use_attack_2_hit'] . " " . $opponent_info['naam_goed'] . " " . $txt['is_ko'] . $message_add . "<br />" . $aantalbericht;
                    $return = duel_one_dead($duel_info, $attack_status['you'], $pokemon_info, $opponent_info, $txt);
                    $message .= $return['bericht'];

                    $new_exp = $pokemon_info['exp'] + $return['exp'];
                    $new_exp_opponent = $opponent_info['exp'] + 0;
                } else if ($good != 2 && $recoil_d > 0 && $rec_left <= 0) {
                    include_once '../../app/classes/League_battle.php';
                    $league_battle = League_battle::select_duel($_GET['duel_id']);
                    if ($league_battle) {
                        if ($pokemon_info['user_id'] == $league_battle->getUser_id2()) {
                            $league_battle->setPontos_user1($league_battle->getPontos_user1() + 1);
                        } else {
                            $league_battle->setPontos_user2($league_battle->getPontos_user2() + 1);
                        }
                        $league_battle->update();
                    }
                    //Gevecht klaar als dit de tegenstander is
                    $levenover = 0;
                    //Alle pokemons van de speler tellen
                    $uitdager = DB::exQuery("SELECT psg.id FROM pokemon_speler_gevecht AS psg INNER JOIN pokemon_speler AS ps ON psg.id = ps.id WHERE ps.user_id='" . $pokemon_info['user_id'] . "' AND psg.leven>'0' AND ps.ei='0'")->num_rows;
                    //Kan hij geen pokemon wisselen
                    if ($uitdager == 1 || ($league_battle &&
                            (($league_battle->getUser_id2() == $pokemon_info['user_id'] && $league_battle->getN_pokemons() == $league_battle->getPontos_user1()) ||
                            ($league_battle->getUser_id1() == $pokemon_info['user_id'] && $league_battle->getN_pokemons() == $league_battle->getPontos_user2())))) {
                        $aantalbericht = "O duelo acabou.";
                        $attack_status['next_turn'] = "end_screen";
                        $attack_status['winner'] = $opponent_info['username'];
                        $good = 2;
                    } else {
                        $aantalbericht = $pokemon_info['username'] . " está trocando de Pokémon.";
                        $attack_status['next_move'] = "wisselen";
                        $good = 1;
                    }

                    $message = $pokemon_info['naam_goed'] . " " . $txt['use_attack_1'] . " " . $attack_info['naam'] . ". " . $message_add . "<br/>" . $aantalbericht;
                    $return = duel_one_dead($duel_info, $attack_status['opponent'], $opponent_info, $pokemon_info, $txt);
                    $message .= $return['bericht'];
                } else if ($attack_info['stappen'] != 2) {
                    $message = $pokemon_info['naam_goed'] . " " . $txt['did'] . " " . $attack_info['naam'] . "" . $txt['hit!'] . $message_add . $message_burn;

                    $message .= "<br />" . $opponent_info['naam_goed'] . " " . $txt['opponent_choose_attack'];
                }

                //Update Pokemon Life
                if ($pokemon_info['effect'] == 'Seeding' && $levenover > 0) {
                    $levenover_seed = $levenover + $recoil_d;
                    if ($levenover_seed > $opponent_info['levenmax']) {
                        $levenover_seed = $opponent_info['levenmax'];
                    }

                    DB::exQuery("UPDATE `" . $opponent_info['table']['fight'] . "` SET `leven`='" . $levenover_seed . "' WHERE `id`='" . $opponent_info['id'] . "'");
                } else {
                    DB::exQuery("UPDATE `" . $opponent_info['table']['fight'] . "` SET `leven`='" . $levenover . "' WHERE `id`='" . $opponent_info['id'] . "'");
                }

                $request = $pre_message . $message . " | " .
                        $good . " | " .
                        $attack_status['opponent'] . " | " .
                        $levenover . " | " .
                        $opponent_info['levenmax'] . " | " .
                        $life_decrease . " | " .
                        $opponent_info['id'] . " | " .
                        $new_exp_opponent . " | " .
                        $opponent_info['expnodig'] . " | " .
                        $auto_turn . " | " .
                        $attack_status['you'] . " | " .
                        $pokemon_info['opzak_nummer'] . " | " .
                        $new_exp . " | " .
                        $pokemon_info['expnodig'] . " | " .
                        $stappen . " | " .
                        $pokemon_info['levenmax'] . " | " .
                        $recoil_d . " | " .
                        $rec_left . " | " .
                        $recoil_u . " | " .
                        $rec_right . " | " .
                        $attack_info['soort'] . " | " .
                        $pokemon_info['effect'] . " | " .
                        $opponent_info['effect'] . " | " .
                        $weather->clima." | ".
                        $pokemon_info['leven']." | ";

                DB::exQuery("UPDATE `duel` SET `winner`='" . $attack_status['winner'] . "', `laatste_beurt_tijd`='" . $time . "', `laatste_beurt`='" . $_GET['wie'] . "', `laatste_aanval`='" . $_GET['attack_name'] . "', `laatste_aanval2`='" . $_GET['attack_name'] . "', `schade`='" . $life_decrease . "', `volgende_beurt`='" . $attack_status['next_turn'] . "', `volgende_zet`='" . $attack_status['next_move'] . "', `request`='" . str_replace(" | ", "||", $request) . "', `beurten`=`beurten`+1 WHERE `id`='" . $_GET['duel_id'] . "'");

                echo $request;
            }
        }
    }
    echo $pre_message . $message . " | " . //0
    $good . " | " . //1
    $attack_status['opponent'] . " | " . //2
    $levenover . " | " . //3
    $opponent_info['levenmax'] . " | " . //4
    $life_decrease . " | " . //5
    $opponent_info['id'] . " | " . //6
    $new_exp_opponent . " | " . //7
    $opponent_info['expnodig'] . " | " . //8
    $auto_turn . " | " . //9
    $attack_status['you'] . " | " . //10
    $pokemon_info['opzak_nummer'] . " | " . //11
    $new_exp . " | " . //12
    $pokemon_info['expnodig'] . " | " . //13
    $stappen . " | " . //14
    $pokemon_info['levenmax'] . " | " . //15
    $recoil_d . " | " . //16
    $rec_left . " | " . //17
    $recoil_u . " | " . //18
    $rec_right . " | " . //19
    $attack_info['soort'] . " | " . //20
    $pokemon_info['effect'] . " | " . //21
    $opponent_info['effect'] . " | " . //22 
    $weather->clima." | ".
    $pokemon_info['leven']." | "; // 23
}
?>