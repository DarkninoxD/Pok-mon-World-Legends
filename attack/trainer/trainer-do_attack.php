<?php
//Is all the information send
if ((isset($_GET['attack_name'])) && (isset($_GET['wie'])) && (isset($_GET['aanval_log_id'])) && (isset($_GET['sid']))) {
    include_once("../../app/includes/resources/config.php");
    include_once("../../app/includes/resources/ingame.inc.php");
    include_once("../attack.inc.php");

    $page = 'attack/trainer/trainer-attack';
    include_once('../../language/language-pages.php');

    //Load Attack Info
    $aanval_log = aanval_log($_GET['aanval_log_id']);
    //Check if the right aanval_log is choosen
    if ($aanval_log['user_id'] != $_SESSION['id']) exit('A batalha foi encerrada por inatividade!');
    if ($_SESSION['sec_key'] != $_GET['_h']) exit;


    //Load Pokemon Info
    $pokemon_info = pokemon_data($aanval_log['pokemonid']);
    //Check if the right pokemon is choosen
    if ($pokemon_info['user_id'] != $_SESSION['id']) exit;
    //Load User Information
    $gebruiker                       = DB::exQuery("SELECT * FROM `gebruikers`, `gebruikers_item` WHERE ((`gebruikers`.`user_id`='" . $_SESSION['id'] . "') AND (`gebruikers_item`.`user_id`='" . $_SESSION['id'] . "'))")->fetch_assoc();
    //Change name for male and female
    $pokemon_info['naam_goed']       = addslashes(pokemon_naam($pokemon_info['naam'], $pokemon_info['roepnaam'], $pokemon_info['icon']));
    //Set Database Table
    $pokemon_info['table']['fight']  = "pokemon_speler_gevecht";
    //Load Computer Info
    $computer_info                   = computer_data($aanval_log['tegenstanderid']);
    //Change name for male and female
    $computer_info['naam_goed']      = computer_naam($computer_info['naam']);
    //Set Database Table
    $computer_info['table']['fight'] = "pokemon_wild_gevecht";
    $win_lose                        = 0;
    $transform                       = 0;
    //Is the new pokemon alive
    if ($pokemon_info['leven'] < 1) {
        $next_turn                  = 0;
        $levenover                  = 0;
        $attack_status['fight_end'] = 1;
        
        //Alle pokemons van de speler tellen
        $aantalpokemon = DB::exQuery("SELECT pokemon_speler_gevecht.id FROM pokemon_speler_gevecht INNER JOIN pokemon_speler ON pokemon_speler_gevecht.id = pokemon_speler.id WHERE pokemon_speler_gevecht.aanval_log_id = '" . $aanval_log['id'] . "' AND pokemon_speler_gevecht.leven > '0' AND pokemon_speler.ei = '0'")->num_rows;
        //Kan hij geen pokemon wisselen
        if (($aantalpokemon == 0) OR (empty($aantalpokemon))) {
            $attack_status['fight_end']   = 1;
            $aantalbericht                = $txt['fight_over'];
            $attack_status['last_attack'] = "end_screen";
        } else {
            $aantalbericht                = $txt['choose_another_pokemon'];
            $attack_status['fight_end']   = 0;
            $attack_status['last_attack'] = "speler_wissel";
        }
        
        $message = $pokemon_info['naam_goed'] . $txt['new_pokemon_dead'];
        $message .= "<br>" . $aantalbericht . "";
        
        
        DB::exQuery("UPDATE `aanval_log` SET `laatste_aanval`='" . $attack_status['last_attack'] . "' WHERE `id`='" . $aanval_log['id'] . "'");
        //The fight is ended
    } else if ($computer_info['leven'] < 1) {         
        $next_turn                  = 0;
        $levenover                  = 0;
        $attack_status['fight_end'] = 1;
        $attack_status['opponent']  = "computer";
        
        //Alle pokemons van de speler tellen
        $aantalpokemon = DB::exQuery("SELECT `id` FROM `pokemon_wild_gevecht` WHERE `aanval_log_id`='" . $aanval_log['id'] . "' AND `leven`>'0'")->num_rows;
        //Kan hij geen pokemon wisselen
        if (($aantalpokemon == 0) OR (empty($aantalpokemon))) {
            $aantalbericht                = $txt['fight_over'];
            $attack_status['last_attack'] = "end_screen";
        } else {
            $aantalbericht                = $aanval_log['trainer'] . " " . $txt['opponent_choose_pokemon'];
            $attack_status['fight_end']   = 0;
            $attack_status['last_attack'] = "trainer_wissel";
        }
        
        $message = $computer_info['naam_goed'] . " " . $txt['is_ko'];
        $message .= "<br>" . $aantalbericht . "";
        
        if ($aanval_log['laatste_aanval'] != "end_screen") {
            $lala = time() + 5;
            if ($lala > $_SESSION['antbug']) {
                $return = one_pokemon_exp($aanval_log, $pokemon_info, $computer_info, $txt);
            }
        }

        $message .= $return['bericht'];
        
        DB::exQuery("UPDATE `aanval_log` SET `laatste_aanval`='" . $attack_status['last_attack'] . "' WHERE `id`='" . $aanval_log['id'] . "'");
    } else if ($aanval_log['laatste_aanval'] == "klaar") {
        $message = $txt['fight_finished'];
    } else {
        switch ($_GET['wie']) {
            case "pokemon":
                //Turn Check
                if (($aanval_log['laatste_aanval'] == "pokemon") OR ($aanval_log['laatste_aanval'] == "computereersteaanval")) {
                    $message   = $computer_info['naam'] . " " . $txt['must_attack'];
                    $next_turn = 1;
                } else {
                    $attack_name                           = $_GET['attack_name'];
                    $attack_status['last_attack']          = "pokemon";
                    $next_turn                             = 1;
                    $attacker_info                         = $pokemon_info;
                    $opponent_info                         = $computer_info;
                    $attack_status['you']                  = "pokemon";
                    $attack_status['opponent']             = "computer";
                    $attack_status['table']['you_busy']    = "aanval_bezig_speler";
                    $attack_status['table']['other_busy']  = "aanval_bezig_computer";
                    $attack_status['table']['you_atack']   = "laatste_aanval_speler";
                    $attack_status['table']['other_atack'] = "laatste_aanval_computer";
                    $wissell                               = "speler_wissel";
                }
                
                break;

            case "computer":
                //Turn Check
                if (($aanval_log['laatste_aanval'] == "computer") OR ($aanval_log['laatste_aanval'] == "spelereersteaanval")) {
                    $message = $pokemon_info['naam'] . " " . $txt['must_attack'];
                } else {
                    //Check Wich Attack Computer have.
                    $computer_attack = 0;
                    if (!empty($computer_info['aanval_1']))
                        $computer_attack += 1;
                    if (!empty($computer_info['aanval_2']))
                        $computer_attack += 1;
                    if (!empty($computer_info['aanval_3']))
                        $computer_attack += 1;
                    if (!empty($computer_info['aanval_4']))
                        $computer_attack += 1;
                    $computer_attack                       = "aanval_" . rand(1, $computer_attack);
                    $attack_name                           = $computer_info[$computer_attack];
                    $attack_status['last_attack']          = "computer";
                    $next_turn                             = 0;
                    $attacker_info                         = $computer_info;
                    $opponent_info                         = $pokemon_info;
                    $attack_status['you']                  = "computer";
                    $attack_status['opponent']             = "pokemon";
                    $attack_status['table']['you_busy']    = "aanval_bezig_computer";
                    $attack_status['table']['other_busy']  = "aanval_bezig_speler";
                    $attack_status['table']['you_atack']   = "laatste_aanval_computer";
                    $attack_status['table']['other_atack'] = "laatste_aanval_speler";
                    $wissell                               = "trainer_wissel";
                }
                
                break;
            default:
                $message = "Error: 4001";
                exit;
        }
        
        //Attack Begin
        //Set Default Attack Values
        $attack_status['continu'] = 1;
        $message_add              = "";
        $stappen                  = "";
        $zmove = '';

        if (isset($_GET['zmove'])) {
            if ($_GET['zmove'] == 'y') {
                if ($aanval_log['zmove'] == 0) {
                    $zmove = zMoves::move($attacker_info)[0];
                    if ($zmove == $attack_name) {
                        DB::exQuery("UPDATE `aanval_log` SET `zmove`='1' WHERE id='" . $aanval_log['id'] . "'");
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
        } else {
            if (($attack_name != $attacker_info['aanval_1']) AND ($attack_name != $attacker_info['aanval_2']) AND ($attack_name != $attacker_info['aanval_3']) AND ($attack_name != $attacker_info['aanval_4'])) {
                if ($attacker_info['copiaid'] == 0) {
                    if (($attack_name != $opponent_info['aanval_1']) AND ($attack_name != $opponent_info['aanval_2']) AND ($attack_name != $opponent_info['aanval_3']) AND ($attack_name != $opponent_info['aanval_4'])) {
                        echo "Error: 4003<br />Info: " . $attack_name . "/" . $pokemon_info['id'];
                        exit;
                    }
                }
            }
        }
        
        //WEATHER (WL >:D)
        $weather = new Weather($aanval_log);
        if ($weather->controller) {
            echo $weather->weather_turns($attacker_info, $opponent_info);
            echo $weather->weather_text('', '<br>');
            echo $weather->weather_damage($attacker_info);
            echo $weather->weather_heal($attacker_info);
        }

        //Check For effect
        if ((!empty($attacker_info['effect'])) AND ($attacker_info['effect'] != "Burn") AND ($attacker_info['effect'] != "Poisoned")) {
            $new_attacker_info['hoelang'] = $attacker_info['hoelang'] - 1;
            $new_attacker_info['effect']  = $attacker_info['effect'];
            
            if ($attacker_info['effect'] == "Flinch") {
                $new_attacker_info['effect'] = "";
                $attack_status['continu']    = 0;
                $message                     = $attacker_info['naam_goed'] . " " . $txt['flinched'];
            } else if ($attacker_info['effect'] == "Sleep") {
                $attack_status['continu'] = 0;
                if ($new_attacker_info['hoelang'] >= 1) {
                    $message = $attacker_info['naam_goed'] . " " . $txt['sleeps'];
                } else if ($new_attacker_info['hoelang'] == 0) {
                    $message                     = $attacker_info['naam_goed'] . " " . $txt['awake'];
                    $new_attacker_info['effect'] = "";
                }
            } else if ($attacker_info['effect'] == "Freeze") {
                $attack_status['continu'] = 0;
                if ($new_attacker_info['hoelang'] >= 1) {
                    $message = $attacker_info['naam_goed'] . " " . $txt['frozen'];
                } else if ($new_attacker_info['hoelang'] == 0) {
                    $message                     = $attacker_info['naam_goed'] . " " . $txt['no_frozen'];
                    $new_attacker_info['effect'] = "";
                }
            } else if ($attacker_info['effect'] == "Paralyzed") {
                $chanceparalizado = rand(0, 100);
                if ($chanceparalizado > 25) {
                    $attack_status['continu'] = 1;
                } else {
                    $attack_status['continu'] = 0;
                    $message                  = $attacker_info['naam_goed'] . " " . $txt['paralyzed'];
                }
                $new_attacker_info['hoelang'] = $attacker_info['hoelang'] + 1;
            } else if ($attacker_info['effect'] == "Confused") {
                $attack_change = rand(1, 3);
                if ($new_attacker_info['hoelang'] == 0) {
                    $attack_status['continu']    = 0;
                    $message                     = $attacker_info['naam_goed'] . " não está mais confuso.";
                    $new_attacker_info['effect'] = "";
                } else if ($attack_change == 2) {
                    $attack_status['continu'] = 1;
                } else if ($new_attacker_info['hoelang'] >= 1) {
                    $attack_status['continu'] = 0;
                    $message                  = $attacker_info['naam_goed'] . " está confuso.";
                }
            } else if ($new_attacker_info['hoelang'] == 0) {
                $new_attacker_info['effect'] = "";
            }
            
            if ($pokemon_info['leven'] < 1) {
                $new_attacker_info['effect'] = "";
            }
            
            if ($attack_status['continu'] == 0) {
                if ($_GET['wie'] == 'computer')
                    $message .= $txt['your_attack_turn'];
                else
                    $message .= "<br />" . $opponent_info['naam_goed'] . " " . $txt['opponent_choose_attack'];
                DB::exQuery("UPDATE " . $attacker_info['table']['fight'] . " SET `effect`='" . $new_attacker_info['effect'] . "', `hoelang`='" . $new_attacker_info['hoelang'] . "' WHERE id='" . $attacker_info['id'] . "'");
                DB::exQuery("UPDATE `aanval_log` SET `laatste_aanval`='" . $attack_status['last_attack'] . "', `beurten`=`beurten`+'1' WHERE id='" . $aanval_log['id'] . "'");

                echo $message . " | " . $next_turn . " | " . $opponent_info['leven'] . " | " . $opponent_info['levenmax'] . " | " . $attack_status['opponent'] . " | 0 | 0 | 0 | " . $opponent_info['id'] . " | " . $pokemon_info['opzak_nummer'] . " | " . $new_exp . " | " . $pokemon_info['expnodig'] . " | " . $recoil_d . " | " . $rec_left . " | " . $attacker_info['levenmax'] . " | " . $attack_status['you'] . " | " . $stappen . " | " . $attacker_info['leven'] . " | " . $attack_info['soort'] . " | " . $pokemon_info['effect'] . " | " . $computer_info['effect'] . " | " . $transform . " | " . $weather->clima;
                exit;
            }
            
        }
        
        if ($attack_name == "Metronome") $attack_name = DB::exQuery("SELECT `naam` FROM `aanval` WHERE is_zmoves='0' order by rand() limit 1")->fetch_assoc()['naam'];

        $attack_info = atk($attack_name, $attacker_info);
        
        $weather->weather_create($attacker_info, $opponent_info, $attack_info);
        
        if ($attack_info['naam'] == "") {
            if ($_GET['wie'] == "computer") $next_turn = 1;
            echo "Error: 4002<br />Info: " . $attack_name;
            exit;
        }
        
        //Hit ratio down
        $htdown = $attacker_info['hit_ratio_down'] * 2;
        if ($htdown > 0) $attack_info['mis'] + $htdown;
        else if ((($attack_info['mis'] != 0) AND ($aanval_log[$attack_status['table']['you_busy']] == '') AND (rand(0, 100) <= $attack_info['mis'])) OR ($aanval_log[$attack_status['table']['other_busy']] == 'Fly') OR ($aanval_log[$attack_status['table']['other_busy']] == 'Dig') OR ($aanval_log[$attack_status['table']['other_busy']] == 'Sky Attack') OR ($aanval_log[$attack_status['table']['other_busy']] == 'Shadow Force') OR ($aanval_log[$attack_status['table']['other_busy']] == 'Phantom Force') OR ($aanval_log[$attack_status['table']['other_busy']] == 'Dive') OR ($aanval_log[$attack_status['table']['other_busy']] == 'Bounce')) {
            $message = $attacker_info['naam_goed'] . " usou " . $attack_info['naam'] . ", mas errou!";
            $message .= ($_GET['wie'] == 'computer')? $txt['your_attack_turn'] : "<br />" . $opponent_info['naam_goed'] . " " . $txt['opponent_choose_attack'];

            DB::exQuery("UPDATE `aanval_log` SET `laatste_aanval`='" . $attack_status['last_attack'] . "', `beurten`=`beurten`+'1', `" . $attack_status['table']['you_atack'] . "`='" . $attack_info['naam'] . "' WHERE id='" . $aanval_log['id'] . "'");

            echo $message . " | " . $next_turn . " | " . $opponent_info['leven'] . " | " . $opponent_info['levenmax'] . " | " . $attack_status['opponent'] . " | 0 | 0 | 0 | " . $opponent_info['id'] . " | " . $pokemon_info['opzak_nummer'] . " | " . $new_exp . " | " . $pokemon_info['expnodig'] . " | " . $recoil_d . " | " . $rec_left . " | " . $attacker_info['levenmax'] . " | " . $attack_status['you'] . " | " . $stappen . " | " . $attacker_info['leven'] . " | " . $attack_info['soort'] . " | " . $pokemon_info['effect'] . " | " . $computer_info['effect'] . " | " . $transform . " | " . $weather->clima;
            exit;
        }
        
        
        if ($attack_info['naam'] == "Fling") {
            $attack_info['sterkte'] = rand(20, 170);
        }
        if ($attack_info['naam'] == "Transform") {
            $transform = "" . $opponent_info['wild_id'] . "," . $opponent_info['shiny'] . "," . $opponent_info['aanval_1'] . "," . $opponent_info['aanval_2'] . "," . $opponent_info['aanval_3'] . "," . $opponent_info['aanval_4'] . "," . $opponent_info['leven'] . "," . $opponent_info['levenmax'] . "";
            DB::exQuery("UPDATE `" . $attacker_info['table']['fight'] . "` SET `leven`='" . $opponent_info['leven'] . "',`levenmax`='" . $opponent_info['levenmax'] . "',`copiaid`='" . $opponent_info['wild_id'] . "' WHERE `id`='" . $attacker_info['id'] . "'");
        }
        
        //Check if attack does have power
        if ($attack_info['sterkte'] != 0) {
            $life_decrease = damage_controller($attacker_info, $opponent_info, $attack_info, $weather->clima);
        } else if ($attack_info['hp_schade'] != 0) {
            $life_decrease = $attack_info['hp_schade'];
        }
        
        //If attack hits more then once
        if ($attack_info['aantalkeer'] != "1") {
            $multi_hit     = multiple_hits($attack_info, $life_decrease);
            $life_decrease = $multi_hit['damage'];
            $message_add .= $multi_hit['message'];
        }
        
       //Does the attack have Critical Hit?
        if ($attack_info['critical'] == 1 && !in_array($opponent_info['ability'], array('4', '75'))) {
            $critic_change = round(($attacker_info['speed'] * 100) / 128);
            if (rand(0, 100) <= $critic_change || in_array($attack_info['naam'], array('Frost Breath', 'Storm Throw')) || ($attacker_info['ability'] == '196' && $opponent_info['effect'] == 'Poisoned')) {
                $attack_info['sterkte'] = $attack_info['sterkte'] * 1.5;
                $message_add .= "<br />Ataque critico!";
            }
        }
        
        //Does the attack have any side effects
        if ((!empty($attack_info['effect_naam'])) AND ($attack_info['effect_kans'] != 0)) {
            if (($attack_info['effect_kans'] == 100) OR (rand(0, 100) <= ($attack_info['effect_kans']))) {
                $effect_info = DB::exQuery("SELECT * FROM effect WHERE actie='" . $attack_info['effect_naam'] . "'")->fetch_assoc();
                if (($effect_info['wat'] == "negatief_tijd") AND ($effect_info['id'] != 31) AND ($effect_info['id'] != 32) AND ($effect_info['id'] != 29) AND ($effect_info['id'] != 28) AND ($effect_info['id'] != 30) AND ($effect_info['id'] != 34) AND ($effect_info['id'] != 33)) {
                    $turns = 0;
                    //Sleep or Freeze
                    if (($effect_info['id'] == 28) OR ($effect_info['id'] == 32))
                        $turns = rand(1, 6);
                    //Confused 
                    else if ($effect_info['id'] == 33)
                        $turns = rand(1, 4);
                    //Paralyzed
                    else if ($effect_info['id'] == 29)
                        $turns = rand(1, 4);
                    //Flinch
                    else if ($effect_info['id'] == 34)
                        $turns = 1;
                    //Save to opponent
                    DB::exQuery("UPDATE " . $opponent_info['table']['fight'] . " SET effect='" . $effect_info['actie'] . "', hoelang='" . $turns . "' WHERE id='" . $opponent_info['id'] . "' AND effect=''");
                    $message = $attacker_info['naam_goed'] . " usou " . $attack_info['naam'] . ", teve efeito.";
                    if (empty($opponent_info['effect']))
                        $message .= "<br />" . $opponent_info['naam_goed'] . " agora está " . $effect_info['naam'];
                    if ($_GET['wie'] == 'computer')
                        $message .= $txt['your_attack_turn'];
                    else
                        $message .= "<br />" . $opponent_info['naam_goed'] . " " . $txt['opponent_choose_attack'];
                    DB::exQuery("UPDATE `aanval_log` SET `laatste_aanval`='" . $attack_status['last_attack'] . "', `beurten`=`beurten`+'1' WHERE id='" . $aanval_log['id'] . "'");
                    //echo $message." | ".$next_turn." | ".$opponent_info['leven']." | ".$opponent_info['levenmax']." | ".$attack_status['opponent']." | 0 | 0 | 0 | ".$opponent_info['id'] ." | ".$pokemon_info['opzak_nummer']." | ".$return['bericht']." | ".$new_exp." | ".$pokemon_info['expnodig']." | ".$recoil_d ." | ".$rec_left." | ".$attacker_info['levenmax']." | ".$attack_status['you']." | ".$stappen." | ".$attacker_info['leven']." | ".$attack_info['soort']." | ".$pokemon_info['effect']." | ".$computer_info['effect'];
                    echo $message . " | " . $next_turn . " | " . $opponent_info['leven'] . " | " . $opponent_info['levenmax'] . " | " . $attack_status['opponent'] . " | 0 | 0 | 0 | " . $opponent_info['id'] . " | " . $pokemon_info['opzak_nummer'] . " | " . $new_exp . " | " . $pokemon_info['expnodig'] . " | " . $recoil_d . " | " . $rec_left . " | " . $attacker_info['levenmax'] . " | " . $attack_status['you'] . " | " . $stappen . " | " . $attacker_info['leven'] . " | " . $attack_info['soort'] . " | " . $pokemon_info['effect'] . " | " . $computer_info['effect'] . " | " . $transform . " | " . $weather->clima;
                    exit;
                } else if ($effect_info['wat'] == "negatief") {
                    if (($effect_info['actie'] == "Defence_down") OR ($effect_info['actie'] == "Defence_down_2")) {
                        //Defence Down
                        $new_stat = round(($opponent_info['defence'] / 100) * (100 - $effect_info['kracht']));
                        $sql      = "defence='" . $new_stat . "'";
                        $text     = ' está com a Defesa diminuida.';
                    } else if (($effect_info['actie'] == "Speed_down") OR ($effect_info['actie'] == "Speed_down_2")) {
                        //Speed Down
                        $new_stat = round(($opponent_info['speed'] / 100) * (100 - $effect_info['kracht']));
                        $sql      = "speed='" . $new_stat . "'";
                        $text     = ' está com a Speed diminuida.';
                    } else if (($effect_info['actie'] == "Spc.defence_down") OR ($effect_info['actie'] == "Spc.defence_down_2")) {
                        //Special Defence Down
                        $new_stat = round(($opponent_info['spc.defence'] / 100) * (100 - $effect_info['kracht']));
                        $sql      = "`spc.defence`='" . $new_stat . "'";
                        $text     = ' está com a Sp. Defesa diminuida.';
                    } else if (($effect_info['actie'] == "Attack_down") OR ($effect_info['actie'] == "Attack_down_2")) {
                        //Attack Down
                        $new_stat = round(($opponent_info['attack'] / 100) * (100 - $effect_info['kracht']));
                        $sql      = "attack='" . $new_stat . "'";
                        $text     = ' está com o Ataque diminuido.';
                    } else if ($effect_info['actie'] == "Attack_defence_down") {
                        //Attack& Speed Down
                        $new_stat = round(($opponent_info['attack'] / 100) * (100 - $effect_info['kracht']));
                        $sql      = "attack='" . $new_stat . "'";
                        $new_stat = round(($opponent_info['defence'] / 100) * (100 - $effect_info['kracht']));
                        $sql .= ", defence='" . $new_stat . "'";
                        $text = ' está com o Ataque e Speed diminuidos.';
                    } else if ($effect_info['actie'] == "defence_spc.defence_down") {
                        //Spc.Defence & Defence Down          
                        $new_stat = round(($opponent_info['defence'] / 100) * (100 - $effect_info['kracht']));
                        $sql      = "defence='" . $new_stat . "'";
                        $new_stat = round(($opponent_info['spc.defence'] / 100) * (100 - $effect_info['kracht']));
                        $sql .= ", `spc.defence`='" . $new_stat . "'";
                        $text = ' está com a Defesa diminuida.';
                    } else if ($effect_info['actie'] == "Hit_ratio_down") {
                        //Hit Ratio Down          
                        $new_stat = $opponent_info['hit_ratio_down'] + 1;
                        $sql      = "hit_ratio_down='" . $new_stat . "'";
                        $text     = ' está com a chance de acertar diminuida.';
                    }
                    DB::exQuery("UPDATE " . $opponent_info['table']['fight'] . " SET " . $sql . " WHERE id='" . $opponent_info['id'] . "'");
                    $message_add .= "<br /> " . $opponent_info['naam_goed'] . " " . $text;
                } else if ($effect_info['wat'] == "positief") {
                    if (($effect_info['actie'] == "Defence_up") OR ($effect_info['actie'] == "Defence_up_2")) {
                        //Defence Up
                        $new_stat = round(($attacker_info['defence'] / 100) * (100 + $effect_info['kracht']));
                        $sql      = "defence='" . $new_stat . "'";
                        $text     = ' está com a Defesa aumentada.';
                    } else if (($effect_info['actie'] == "Attack_up") OR ($effect_info['actie'] == "Attack_up_2")) {
                        //Attack up
                        $new_stat = round(($attacker_info['attack'] / 100) * (100 + $effect_info['kracht']));
                        $sql      = "attack='" . $new_stat . "'";
                        $text     = ' está com o Ataque aumentado.';
                    } else if ($effect_info['actie'] == "Speed_up_2") {
                        //Speed Up
                        $new_stat = round(($attacker_info['speed'] / 100) * (100 + $effect_info['kracht']));
                        $sql      = "speed='" . $new_stat . "'";
                        $text     = ' está com a Speed aumentada .';
                    } else if ($effect_info['actie'] == "Spc.defence_up_2") {
                        //Spc. Defence Up
                        $new_stat = round(($attacker_info['spc.defence'] / 100) * (100 + $effect_info['kracht']));
                        $sql .= "`spc.defence`='" . $new_stat . "'";
                        $text = ' está com a Sp. Defesa aumentada.';
                    } else if ($effect_info['actie'] == "All_up") {
                        //All stats Up          
                        $new_stat = round(($attacker_info['attack'] / 100) * (100 + $effect_info['kracht']));
                        $sql      = "attack='" . $new_stat . "'";
                        $new_stat = round(($attacker_info['defence'] / 100) * (100 + $effect_info['kracht']));
                        $sql .= ", defence='" . $new_stat . "'";
                        $new_stat = round(($attacker_info['spc.defence'] / 100) * (100 + $effect_info['kracht']));
                        $sql .= ", `spc.defence`='" . $new_stat . "'";
                        $new_stat = round(($attacker_info['spc.attack'] / 100) * (100 + $effect_info['kracht']));
                        $sql .= ", `spc.attack`='" . $new_stat . "'";
                        $new_stat = round(($attacker_info['speed'] / 100) * (100 + $effect_info['kracht']));
                        $sql .= ", speed='" . $new_stat . "'";
                        $text = ' está com todos atributos aumentados.';
                    } else if ($effect_info['actie'] == "Attack_defence_up") {
                        //Attack & Defence Up         
                        $new_stat = round(($attacker_info['attack'] / 100) * (100 + $effect_info['kracht']));
                        $sql      = "attack='" . $new_stat . "'";
                        $new_stat = round(($attacker_info['defence'] / 100) * (100 + $effect_info['kracht']));
                        $sql .= ", defence='" . $new_stat . "'";
                        $text = ' está com o Ataque e Defesa aumentados.';
                    } else if ($effect_info['actie'] == "Defence_speed_up_2") {
                        //Defence & Speed Up         
                        $new_stat = round(($attacker_info['defence'] / 100) * (100 + $effect_info['kracht']));
                        $sql      = "defence='" . $new_stat . "'";
                        $new_stat = round(($attacker_info['speed'] / 100) * (100 + $effect_info['kracht']));
                        $sql .= ", speed='" . $new_stat . "'";
                        $text = ' está com a Defesa e Speed aumentados.';
                    } else if ($effect_info['actie'] == "spc_up") {
                        //Specials Up    
                        $new_stat = round(($attacker_info['spc.attack'] / 100) * (100 + $effect_info['kracht']));
                        $sql      = "`spc.attack`='" . $new_stat . "'";
                        $new_stat = round(($attacker_info['spc.defence'] / 100) * (100 + $effect_info['kracht']));
                        $sql .= ", `spc.defence`='" . $new_stat . "'";
                        $text = ' está com os atributos esp aumentados.';
                    } else if ($effect_info['actie'] == "defence_spc.defence_up") {
                        //Defences UP        
                        $new_stat = round(($attacker_info['defence'] / 100) * (100 + $effect_info['kracht']));
                        $sql      = "defence='" . $new_stat . "'";
                        $new_stat = round(($attacker_info['spc.defence'] / 100) * (100 + $effect_info['kracht']));
                        $sql .= ", `spc.defence`='" . $new_stat . "'";
                        $text = ' está com a Defesa aumentada.';
                    } else if ($effect_info['actie'] == "attack_speed_up") {
                        //Attack & Speed Up         
                        $new_stat = round(($attacker_info['attack'] / 100) * (100 + $effect_info['kracht']));
                        $sql      = "attack='" . $new_stat . "'";
                        $new_stat = round(($attacker_info['speed'] / 100) * (100 + $effect_info['kracht']));
                        $sql .= ", speed='" . $new_stat . "'";
                        $text = ' está com o Ataque e Speed aumentados.';
                    } else if ($effect_info['actie'] == "Spc.Attack_up_2") {
                        //Spc. Attack Up    
                        $new_stat = round(($attacker_info['spc.attack'] / 100) * (100 + $effect_info['kracht']));
                        $sql      = "`spc.attack`='" . $new_stat . "'";
                        $text     = ' está com Sp. Ataque aumentado.';
                    }
                    
                    DB::exQuery("UPDATE " . $attacker_info['table']['fight'] . " SET " . $sql . " WHERE id='" . $attacker_info['id'] . "'");
                    $message_add .= "<br /> " . $attacker_info['naam_goed'] . " " . $text;
                } else if ($effect_info['wat'] == "beide") {
                    if ($effect_info['actie'] == "attack_defence_up_speed_down") {
                        $new_stat = round(($attacker_info['attack'] / 100) * (100 + $effect_info['kracht']));
                        $sql      = "attack='" . $new_stat . "'";
                        $new_stat = round(($attacker_info['defence'] / 100) * (100 + $effect_info['kracht']));
                        $sql .= ", defence='" . $new_stat . "'";
                        $new_stat = round(($opponent_info['speed'] / 100) * (100 - $effect_info['kracht']));
                        $sql .= ", speed='" . $new_stat . "'";
                        DB::exQuery("UPDATE " . $attacker_info['table']['fight'] . " SET " . $sql . " WHERE id='" . $attacker_info['id'] . "'");
                        $message_add .= "<br /> " . $attacker_info['naam_goed'] . " está agora com Ataque e Defesa aumentados porém com Speed diminuida.";
                    } else if ($effect_info['actie'] == "speed_up_attack_down") {
                        $new_stat = round(($attacker_info['attack'] / 100) * (100 - $effect_info['kracht']));
                        $sql      = "attack='" . $new_stat . "'";
                        $new_stat = round(($opponent_info['speed'] / 100) * (100 + $effect_info['kracht']));
                        $sql .= ", speed='" . $new_stat . "'";
                        DB::exQuery("UPDATE " . $attacker_info['table']['fight'] . " SET " . $sql . " WHERE id='" . $attacker_info['id'] . "'");
                        $message_add .= "<br /> " . $attacker_info['naam_goed'] . " está agora com Speed aumentada porém com Ataque diminuido.";
                    }
                }
            }
        }
        
        //Does the attack have an extra effect
        else if (!empty($attack_info['extra'])) {
            if ($attack_info['extra'] == 'half_attack_recover') {
                if ($attacker_info['leven'] != $attacker_info['levenmax']) {
                    $rec_left = $attacker_info['leven'] + round($life_decrease / 2);
                    if ($rec_left >= $attacker_info['levenmax']) {
                        $rec_left = $attacker_info['levenmax'];
                    }
                    DB::exQuery("UPDATE `" . $attacker_info['table']['fight'] . "` SET `leven`='" . $rec_left . "' WHERE `id`='" . $attacker_info['id'] . "'");
                    $message_add .= "<br /> " . $attacker_info['naam_goed'] . " está se recuperando. ";
                    $attacker_info['leven'] = $rec_left;
                }
            } else if ($attack_info['extra'] == 'uphalfhp') {
                if ($attacker_info['leven'] != $attacker_info['levenmax']) {
                    $rec_left = $attacker_info['leven'] + round($attacker_info['levenmax'] / 2);
                    if ($rec_left >= $attacker_info['levenmax']) {
                        $rec_left = $attacker_info['levenmax'];
                    }
                    DB::exQuery("UPDATE `" . $attacker_info['table']['fight'] . "` SET `leven`='" . $rec_left . "' WHERE `id`='" . $attacker_info['id'] . "'");
                    $message_add .= "<br /> " . $attacker_info['naam_goed'] . " está se recuperando. ";
                    $attacker_info['leven'] = $rec_left;
                }
            } else if ($attack_info['extra'] == 'up75percenthp') {
                if ($attacker_info['leven'] != $attacker_info['levenmax']) {
                    $rec_left = $attacker_info['leven'] + round(($life_decrease / 100) * 75);
                    if ($rec_left >= $attacker_info['levenmax']) {
                        $rec_left = $attacker_info['levenmax'];
                    }
                    DB::exQuery("UPDATE `" . $attacker_info['table']['fight'] . "` SET `leven`='" . $rec_left . "' WHERE `id`='" . $attacker_info['id'] . "'");
                    $message_add .= "<br /> " . $attacker_info['naam_goed'] . " está se recuperando. ";
                    $attacker_info['leven'] = $rec_left;
                }
            } else if ($attack_info['extra'] == 'sleep_half_attack_recover') {
                if ($attacker_info['leven'] != $attacker_info['levenmax']) {
                    if ($opponent_info['effect'] == "Sleep") {
                        $rec_left = $attacker_info['leven'] + round($life_decrease / 2);
                        if ($rec_left >= $attacker_info['levenmax']) {
                            $rec_left = $attacker_info['levenmax'];
                        }
                        DB::exQuery("UPDATE `" . $attacker_info['table']['fight'] . "` SET `leven`='" . $rec_left . "' WHERE `id`='" . $attacker_info['id'] . "'");
                        $message_add .= "<br /> " . $attacker_info['naam_goed'] . " está se recuperando. ";
                        $attacker_info['leven'] = $rec_left;
                    }
                }
                
            }
        }
        
        //Does The attack Hits in recoil?
        else if ($attack_info['recoil'] > 1) {
            $recoil_d = round($attacker_info['levenmax'] / 20);
            $rec_left = $attacker_info['leven'] - $recoil_d;
            if ($rec_left < 1)
                $rec_left = 0;
            DB::exQuery("UPDATE `" . $attacker_info['table']['fight'] . "` SET `leven`='" . $rec_left . "' WHERE `id`='" . $attacker_info['id'] . "'");
            $message_add .= "<br /> " . $attacker_info['naam_goed'] . " atinge-se com recuo. ";
        }
        
        //Hits with burn?
        if ($attacker_info['effect'] == 'Burn') {
            $recoil_d = round($attacker_info['levenmax'] / 8);
            $rec_left = $attacker_info['leven'] - $recoil_d;
            if ($rec_left < 1)
                $rec_left = 0;
            DB::exQuery("UPDATE `" . $attacker_info['table']['fight'] . "` SET `leven`='" . $rec_left . "' WHERE `id`='" . $attacker_info['id'] . "'");
            $message_add .= "<br /> " . $attacker_info['naam_goed'] . " está queimando. ";
        }
        
        //Hits with poison?
        if ($attacker_info['effect'] == 'Poisoned') {
            $calcx    = round($attacker_info['levenmax'] / 16);
            $recoil_d = $attacker_info['poison'] * $calcx;
            $rec_left = $attacker_info['leven'] - $recoil_d;
            if ($rec_left < 1)
                $rec_left = 0;
            DB::exQuery("UPDATE `" . $attacker_info['table']['fight'] . "` SET `leven`='" . $rec_left . "',`poison`=`poison`+'1' WHERE `id`='" . $attacker_info['id'] . "'");
            $message_add .= "<br /> " . $attacker_info['naam_goed'] . " está envenenado. ";
        }
        
        //Attack Have to Steps
        if ($attack_info['stappen'] == 2) {
            //attack have to load first
            if (($attack_info['laden'] == 'voor') AND (empty($aanval_log[$attack_status['table']['you_busy']]))) {
                if ($_GET['wie'] == 'pokemon')
                    $stappen = $attack_info['naam'];
                $message = $attacker_info['naam_goed'] . " está carregando " . $attack_info['naam'];
                if ($_GET['wie'] == 'computer')
                    $message .= $txt['your_attack_turn'];
                else
                    $message .= "<br />" . $opponent_info['naam_goed'] . " " . $txt['opponent_choose_attack'];
                DB::exQuery("UPDATE `aanval_log` SET `laatste_aanval`='" . $attack_status['last_attack'] . "', `beurten`=`beurten`+'1', " . $attack_status['table']['you_busy'] . "='" . $attack_info['naam'] . "' WHERE id='" . $aanval_log['id'] . "'");
                //echo $message." | ".$next_turn." | ".$opponent_info['leven']." | ".$opponent_info['levenmax']." | ".$attack_status['opponent']." | 0 | 0 | 0 | ".$opponent_info['id'] ." | ".$pokemon_info['opzak_nummer']." | ".$return['bericht']." | ".$new_exp." | ".$pokemon_info['expnodig']." | ".$recoil_d ." | ".$rec_left." | ".$attacker_info['levenmax']." | ".$attack_status['you']." | ".$stappen." | ".$attacker_info['leven']." | ".$attack_info['soort']." | ".$pokemon_info['effect']." | ".$computer_info['effect'];
                echo $message . " | " . $next_turn . " | " . $opponent_info['leven'] . " | " . $opponent_info['levenmax'] . " | " . $attack_status['opponent'] . " | 0 | 0 | 0 | " . $opponent_info['id'] . " | " . $pokemon_info['opzak_nummer'] . " | " . $new_exp . " | " . $pokemon_info['expnodig'] . " | " . $recoil_d . " | " . $rec_left . " | " . $attacker_info['levenmax'] . " | " . $attack_status['you'] . " | " . $stappen . " | " . $attacker_info['leven'] . " | " . $attack_info['soort'] . " | " . $pokemon_info['effect'] . " | " . $computer_info['effect'] . " | " . $transform . " | " . $weather->clima;
                exit;
            } else {
                $aanval_log_sql = ",`" . $attack_status['table']['you_busy'] . "`=''";
            }
            //Attack is recharging afterwards
            if (($attack_info['laden'] == 'na') AND (!empty($aanval_log[$attack_status['table']['you_busy']]))) {
                $message = $attacker_info['naam_goed'] . " está recarregando de " . $attack_info['naam'];
                if ($_GET['wie'] == 'computer')
                    $message .= $txt['your_attack_turn'];
                else
                    $message .= "<br />" . $opponent_info['naam_goed'] . " " . $txt['opponent_choose_attack'];
                DB::exQuery("UPDATE `aanval_log` SET `laatste_aanval`='" . $attack_status['last_attack'] . "', `beurten`=`beurten`+'1', " . $attack_status['table']['you_busy'] . "='' WHERE id='" . $aanval_log['id'] . "'");

                echo $message . " | " . $next_turn . " | " . $opponent_info['leven'] . " | " . $opponent_info['levenmax'] . " | " . $attack_status['opponent'] . " | 0 | 0 | 0 | " . $opponent_info['id'] . " | " . $pokemon_info['opzak_nummer'] . " | " . $new_exp . " | " . $pokemon_info['expnodig'] . " | " . $recoil_d . " | " . $rec_left . " | " . $attacker_info['levenmax'] . " | " . $attack_status['you'] . " | " . $stappen . " | " . $attacker_info['leven'] . " | " . $attack_info['soort'] . " | " . $pokemon_info['effect'] . " | " . $computer_info['effect'] . " | " . $transform . " | " . $weather->clima;
                exit;
            } else {
                if ($_GET['wie'] == 'pokemon') $stappen = $attack_info['naam'];
                $aanval_log_sql = ",`" . $attack_status['table']['you_busy'] . "`='" . $attack_info['naam'] . "'";
            }
            
            if (($attack_info['laden'] == 'voor') AND (!empty($aanval_log[$attack_status['table']['you_busy']]))) {
                $aanval_log_sql = ",`" . $attack_status['table']['you_busy'] . "`=''";
            }
            
        }
        
        //Check burn
        if (($attack_info['effect_naam'] == 'Burn') AND (($attack_info['effect_kans'] == 100) OR (rand(0, 100) <= $attack_info['effect_kans']))) {
            //Save to opponent
            DB::exQuery("UPDATE " . $opponent_info['table']['fight'] . " SET effect='Burn', hoelang='" . $turns . "' WHERE id='" . $opponent_info['id'] . "' AND effect=''");
            $message_burn = "<br />" . $opponent_info['naam_goed'] . " está queimando.";
        }
        
        //Check freeze
        if (($attack_info['effect_naam'] == 'Freeze') AND (($attack_info['effect_kans'] == 100) OR (rand(0, 100) <= $attack_info['effect_kans']))) {
            $turns = rand(2, 8);
            //Save to opponent
            DB::exQuery("UPDATE " . $opponent_info['table']['fight'] . " SET effect='Freeze', hoelang='" . $turns . "' WHERE id='" . $opponent_info['id'] . "' AND effect=''");
            $message_burn = "<br />" . $opponent_info['naam_goed'] . " está congelado.";
        }
        
        //Check paralyzed
        if (($attack_info['effect_naam'] == 'Paralyzed') AND (($attack_info['effect_kans'] == 100) OR (rand(0, 100) <= $attack_info['effect_kans']))) {
            //Save to opponent
            //$new_statparalyzed = round(($opponent_info['speed']/100)*75);
            //$sqlparalyzed = "speed='".$new_statparalyzed."'";
            $turns = rand(5, 10);
            
            DB::exQuery("UPDATE " . $opponent_info['table']['fight'] . " SET effect='Paralyzed', hoelang='" . $turns . "' WHERE id='" . $opponent_info['id'] . "' AND effect=''");
            $message_burn = "<br />" . $opponent_info['naam_goed'] . " está paralizado.";
        }
        
        //Check poisoned
        if (($attack_info['effect_naam'] == 'Poisoned') AND (($attack_info['effect_kans'] == 100) OR (rand(0, 100) <= $attack_info['effect_kans']))) {
            //Save to opponent
            DB::exQuery("UPDATE " . $opponent_info['table']['fight'] . " SET effect='Poisoned', hoelang='" . $turns . "' WHERE id='" . $opponent_info['id'] . "' AND effect=''");
            $message_burn = "<br />" . $opponent_info['naam_goed'] . " está envenenado.";
        }
        
        //Check sleep
        if (($attack_info['effect_naam'] == 'Sleep') AND (($attack_info['effect_kans'] == 100) OR (rand(0, 100) <= $attack_info['effect_kans']))) {
            $turns = rand(1, 7);
            //Save to opponent
            DB::exQuery("UPDATE " . $opponent_info['table']['fight'] . " SET effect='Sleep', hoelang='" . $turns . "' WHERE id='" . $opponent_info['id'] . "' AND effect=''");
            $message_burn = "<br />" . $opponent_info['naam_goed'] . " está dormindo.";
        }
        
        //Check flinch
        if (($attack_info['effect_naam'] == 'Flinch') AND (($attack_info['effect_kans'] == 100) OR (rand(0, 100) <= $attack_info['effect_kans']))) {
            $turns = 1;
            //Save to opponent
            DB::exQuery("UPDATE " . $opponent_info['table']['fight'] . " SET effect='Flinch', hoelang='" . $turns . "' WHERE id='" . $opponent_info['id'] . "' AND effect=''");
            $message_burn = "<br />" . $opponent_info['naam_goed'] . " está hesitando.";
        }
        
        //Check confused
        if (($attack_info['effect_naam'] == 'Confused') AND (($attack_info['effect_kans'] == 100) OR (rand(0, 100) <= $attack_info['effect_kans']))) {
            $turns = rand(1, 4);
            //Save to opponent
            DB::exQuery("UPDATE " . $opponent_info['table']['fight'] . " SET effect='Confused', hoelang='" . $turns . "' WHERE id='" . $opponent_info['id'] . "' AND effect=''");
            $message_burn = "<br />" . $opponent_info['naam_goed'] . " está confuso.";
        }
        
        // Self-Destruct
        if ($attack_info['naam'] == "Self-Destruct") {
            //$finale = $attacker_info['levenmax']-$attacker_info['levenmax'];
            DB::exQuery("UPDATE `" . $attacker_info['table']['fight'] . "` SET `leven`='0' WHERE `id`='" . $attacker_info['id'] . "'");
            $message_add .= "<br /> " . $attacker_info['naam_goed'] . " destruiu-se!";
            $aantalbericht                = $txt['choose_another_pokemon'];
            $attack_status['fight_end']   = 0;
            $attack_status['last_attack'] = "wissel";
            $attacker_info['leven']       = 0;
        }
        
        //Explosion
        if ($attack_info['naam'] == "Explosion" || $attack_info['naam'] == "Mind Blown") {
            DB::exQuery("UPDATE `" . $attacker_info['table']['fight'] . "` SET `leven`='0' WHERE `id`='" . $attacker_info['id'] . "'");
            $message_add .= "<br /> " . $attacker_info['naam_goed'] . " explodiu-se!";
            $aantalbericht                = $txt['choose_another_pokemon'];
            $attack_status['fight_end']   = 0;
            $attack_status['last_attack'] = "wissel";
            $attacker_info['leven']       = 0;
        }
        
        //Sketch
        if ($attack_info['naam'] == "Sketch" AND $attack_status['you'] == "pokemon" AND $aanval_log['laatste_aanval_computer'] != "") {
            if (DB::exQuery("SELECT id FROM `pokemon_speler` WHERE `id`='" . $aanval_log['pokemonid'] . "' and `aanval_1`='Sketch'")->num_rows == 1) {
                DB::exQuery("UPDATE pokemon_speler SET `aanval_1`='" . $aanval_log['laatste_aanval_computer'] . "' WHERE `id`='" . $aanval_log['pokemonid'] . "' and `aanval_1`='Sketch'");
            } else if (DB::exQuery("SELECT id FROM `pokemon_speler` WHERE `id`='" . $aanval_log['pokemonid'] . "' and `aanval_2`='Sketch'")->num_rows == 1) {
                DB::exQuery("UPDATE pokemon_speler SET `aanval_2`='" . $aanval_log['laatste_aanval_computer'] . "' WHERE `id`='" . $aanval_log['pokemonid'] . "' and `aanval_2`='Sketch'");
            } else if (DB::exQuery("SELECT id FROM `pokemon_speler` WHERE `id`='" . $aanval_log['pokemonid'] . "' and `aanval_3`='Sketch'")->num_rows == 1) {
                DB::exQuery("UPDATE pokemon_speler SET `aanval_3`='" . $aanval_log['laatste_aanval_computer'] . "' WHERE `id`='" . $aanval_log['pokemonid'] . "' and `aanval_3`='Sketch'");
            } else if (DB::exQuery("SELECT id FROM `pokemon_speler` WHERE `id`='" . $aanval_log['pokemonid'] . "' and `aanval_4`='Sketch'")->num_rows == 1) {
                DB::exQuery("UPDATE pokemon_speler SET `aanval_4`='" . $aanval_log['laatste_aanval_computer'] . "' WHERE `id`='" . $aanval_log['pokemonid'] . "' and `aanval_4`='Sketch'");
            }
                
            $message_add .= "<br /> " . $attacker_info['naam_goed'] . " copiou o ataque " . $aanval_log['laatste_aanval_computer'] . "! <br />";;
             echo '<script>location.reload()</script>';  
        }
        
        //Transform
        if ($attack_info['naam'] == "Transform") {
            DB::exQuery("UPDATE `".$attacker_info['table']['fight']."` SET `copiaid`='".$opponent_info['wildid']."' WHERE `id`='".$attacker_info['id']."'");
            $message_add .= "<br /> " . $attacker_info['naam_goed'] . " copiou " . $opponent_info['naam_goed'] . ".";
        }
        
        //Attack Can Continue
        else if ($attack_status['continu'] != 0) {
            $life_decrease = damage_controller($attacker_info, $opponent_info, $attack_info, $weather->clima);
        }
        
        if ($life_decrease > 0)
            $life_off = 1;
        else
            $life_off = 0;
        
        if ($pokemon_info['leven'] < 1) {
            $life_decrease = 0;
            $life_off      = 0;
        }
        
        $levenover = $opponent_info['leven'] - $life_decrease;
        
        //False Swipe
        if ($attack_info['naam'] == "False Swipe" OR $attack_info['naam'] == "Hold Back") {
            if ($levenover <= 0) {
                $life_decrease          = 0;
                $opponent_info['leven'] = 1;
                $levenover              = 1;
            }
        }
        
        //$message .= "<br />".$levenover;
        $attack_status['fight_end'] = 0;
        if ($levenover < 1) {
            //Gevecht klaar als dit de tegenstander is
            $next_turn                  = 0;
            $levenover                  = 0;
            $attack_status['fight_end'] = 1;
            if ($attack_status['last_attack'] == "computer") {
                //Alle pokemons van de speler tellen
                $speler_pokemon = DB::exQuery("SELECT pokemon_speler_gevecht.id FROM pokemon_speler_gevecht INNER JOIN pokemon_speler ON pokemon_speler_gevecht.id = pokemon_speler.id WHERE pokemon_speler_gevecht.aanval_log_id = '" . $_GET['aanval_log_id'] . "' AND pokemon_speler_gevecht.leven > '0' AND pokemon_speler.ei = '0'")->num_rows;
                //Kan hij geen pokemon wisselen
                if (($speler_pokemon <= 1) OR (empty($speler_pokemon))) {
                    $aantalbericht                = $txt['fight_over'];
                    $attack_status['last_attack'] = "end_screen";
                } else {
                    $aantalbericht                = $txt['choose_another_pokemon'];
                    $attack_status['fight_end']   = 0;
                    $attack_status['last_attack'] = "speler_wissel";
                }
                
                $message = $computer_info['naam_goed'] . " " . $txt['use_attack_1'] . "" . $attack_info['naam'] . $txt['use_attack_2'] . $aantalbericht;
                
            } else if ($attack_status['last_attack'] == "pokemon") {
                //Alle Pokemons van trainer tellen
                $trainer_pokemon = DB::exQuery("SELECT `id` FROM `pokemon_wild_gevecht` WHERE `aanval_log_id`='" . $_GET['aanval_log_id'] . "' AND `leven`>'0'")->num_rows;
                if (($trainer_pokemon <= 1) OR (empty($trainer_pokemon))) {
                    $win_lose                     = 1;
                    $attack_status['last_attack'] = "end_screen";
                    $attack_status['fight_end']   = 1;
                } else {
                    $aantalbericht                = $aanval_log['trainer'] . " " . $txt['opponent_choose_pokemon'];
                    $attack_status['fight_end']   = 0;
                    $attack_status['last_attack'] = "trainer_wissel";
                }
                
                $message = $pokemon_info['naam_goed'] . "" . $txt['use_attack_1'] . "" . $attack_info['naam'] . "" . $txt['use_attack_2_hit'] . " " . $computer_info['naam_goed'] . " " . $txt['is_ko'] . $message_add;
                $lala    = time() + 5;
                if ($lala > $_SESSION['antbug']) {
                    $return = one_pokemon_exp($aanval_log, $pokemon_info, $computer_info, $txt);
                }
                $message .= $return['bericht'];
            }
        } else {
            $message = $attacker_info['naam_goed'] . " " . $txt['did'] . " " . $attack_info['naam'] . "" . $txt['hit!'] . $message_add . $message_burn;
            if ($_GET['wie'] == 'computer')
                $message .= $txt['your_attack_turn'];
            else
                $message .= "<br />" . $opponent_info['naam_goed'] . " " . $txt['opponent_choose_attack'];
        }
        
        //Update
        if ($pokemon_info['leven'] > 0) {
            DB::exQuery("UPDATE `" . $opponent_info['table']['fight'] . "` SET `leven`='" . $levenover . "' WHERE `id`='" . $opponent_info['id'] . "'");
        }
        
        if (!empty($aanval_log[$attack_status['table']['you_busy']])) {
            $aanval_log_sql = ",`" . $attack_status['table']['you_busy'] . "`=''";
        }
        
        //Update Aanval Log
        DB::exQuery("UPDATE `aanval_log` SET `laatste_aanval`='" . $attack_status['last_attack'] . "', `beurten`=`beurten`+'1', `" . $attack_status['table']['you_atack'] . "`='" . $attack_info['naam'] . "' " . $aanval_log_sql . " WHERE `id`='" . $aanval_log['id'] . "'");
        
        if ($win_lose == 2)
            attack_lost($gebruiker, $aanval_log['id'], $aanval_log['tegenstanderid']);
        
    }
      
    $new_exp = $pokemon_info['exp'] + $return['exp'];
    echo $message . " | " . $next_turn . " | " . $levenover . " | " . $opponent_info['levenmax'] . " | " . $attack_status['opponent'] . " | " . $life_off . " | " . $attack_status['fight_end'] . " | " . $life_decrease . " | " . $opponent_info['id'] . " | " . $pokemon_info['opzak_nummer'] . " | " . $new_exp . " | " . $pokemon_info['expnodig'] . " | " . $recoil_d . " | " . $rec_left . " | " . $attacker_info['levenmax'] . " | " . $attack_status['you'] . " | " . $stappen . " | " . $attacker_info['leven'] . " | " . $attack_info['soort'] . " | " . $pokemon_info['effect'] . " | " . $computer_info['effect'] . " | " . $transform . " | " . $weather->clima;
}