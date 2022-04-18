<?php
#Load Computer Data
function computer_data($computer_id) {
  #Load And Return All Computer Information
  return DB::exQuery("SELECT pokemon_wild.*, pokemon_wild_gevecht.* FROM pokemon_wild INNER JOIN pokemon_wild_gevecht ON pokemon_wild_gevecht.wildid = pokemon_wild.wild_id WHERE pokemon_wild_gevecht.id='".$computer_id."'")->fetch_assoc();
}

#Load  Pokemon Data
function pokemon_data($pokemon_id) {
  #Load And Return All Pokemon Information
  return DB::exQuery("SELECT pw.*, ps.*, psg.* FROM pokemon_wild AS pw INNER JOIN pokemon_speler AS ps ON ps.wild_id = pw.wild_id INNER JOIN pokemon_speler_gevecht AS psg ON ps.id = psg.id  WHERE psg.id='".$pokemon_id."'")->fetch_assoc();
}
  
#Load Aanval logs
function aanval_log($aanval_log_id) {
  #Load And Send Data
  return DB::exQuery("SELECT * FROM `aanval_log` WHERE `id`='".$aanval_log_id."'")->fetch_assoc();
}

#Knocked One Pokemon down
function one_pokemon_exp($aanval_log,$pokemon_info,$computer_info,$txt) {
  $ids = explode(",", $aanval_log['gebruikt_id']);
  $ret['bericht'] = "<br />";
  $aantal = 0;
  #Count all pokemon
  foreach($ids as $pokemonid) {
    if (!empty($pokemonid)) $aantal++;
  }

  foreach($ids as $pokemonid) {
    if (!empty($pokemonid)) {  
      $used_info = DB::exQuery("SELECT pokemon_wild.naam, pokemon_speler.item, pokemon_speler.roepnaam, pokemon_speler.trade, pokemon_speler.level, pokemon_speler.expnodig, pokemon_speler_gevecht.leven, pokemon_speler_gevecht.exp FROM pokemon_wild INNER JOIN pokemon_speler ON pokemon_wild.wild_id = pokemon_speler.wild_id INNER JOIN pokemon_speler_gevecht ON pokemon_speler.id = pokemon_speler_gevecht.id WHERE pokemon_speler.id='".$pokemonid."'")->fetch_assoc();
      $used_info['naam_goed'] = pokemon_naam($used_info['naam'],$used_info['roepnaam']);  
      #If pokemon is dead no exp.
      if ($used_info['leven'] > 0) {
        #If pokemon is level 100 no more exp for him
        if ($used_info['level'] < 100) {
          #Check if the user is premium
          $user = DB::exQuery("SELECT premiumaccount FROM gebruikers WHERE user_id='".$_SESSION['id']."'")->fetch_assoc();
          $valordaexp = DB::exQuery("SELECT * FROM configs WHERE config='exp'")->fetch_assoc();
          $extra_exp = 1.5;
          $extra_exp += $used_info['trade'];
          if ($user['premiumaccount'] > time()) $extra_exp += 2;
          
          #Calculate EXP, division by aantal for amount of pokemon
          $ret['exp'] = round(((($computer_info['base_exp']*$computer_info['level'])*$extra_exp)/7)/$aantal)*$valordaexp['valor'];

          if ($used_info['item'] == 'Lucky Egg') {
            $ret['exp'] = floor($ret['exp'] * 1.5);
          } else if ($used_info['item'] == 'Macho Brace') {
            $computer_info['effort_attack'] *= 2;
            $computer_info['effort_defence'] *= 2;
            $computer_info['effort_speed'] *= 2;
            $computer_info['effort_spc.attack'] *= 2;
            $computer_info['effort_spc.defence'] *= 2;
            $computer_info['effort_hp'] *= 2;
          } else if ($used_info['item'] == 'Power Weight') {
            if ($computer_info['effort_hp'] > 0) $computer_info['effort_hp'] += 4;
          } else if ($used_info['item'] == 'Power Bracer') {
            if ($computer_info['effort_attack'] > 0) $computer_info['effort_attack'] += 4;
          } else if ($used_info['item'] == 'Power Belt') {
            if ($computer_info['effort_defence'] > 0) $computer_info['effort_defence'] += 4;
          } else if ($used_info['item'] == 'Power Lens') {
            if ($computer_info['effort_spc.attack'] > 0) $computer_info['effort_spc.attack'] += 4;
          } else if ($used_info['item'] == 'Power Band') {
            if ($computer_info['effort_spc.defence'] > 0) $computer_info['effort_spc.defence'] += 4;
          } else if ($used_info['item'] == 'Power Anklet') {
            if ($computer_info['effort_speed'] > 0) $computer_info['effort_speed'] += 4;
          }

          #Add the exp and Effort points 
          DB::exQuery("UPDATE `pokemon_speler_gevecht` SET `exp`=`exp`+'".$ret['exp'] ."', `totalexp`=`totalexp`+'".$ret['exp'] ."', `attack_ev`=`attack_ev`+'".$computer_info['effort_attack']."', `defence_ev`=`defence_ev`+'".$computer_info['effort_defence']."', `speed_ev`=`speed_ev`+'".$computer_info['effort_speed']."', `spc.attack_ev`=`spc.attack_ev`+'".$computer_info['effort_spc.attack']."', `spc.defence_ev`=`spc.defence_ev`+'".$computer_info['effort_spc.defence']."', `hp_ev`=`hp_ev`+'".$computer_info['effort_hp']."' WHERE `id`='".$pokemonid."'");
	        $_SESSION['antbug'] = time();
          #Check if the Pokemon is traded
          if (($user['premiumaccount'] >= time()) && ($used_info['trade'] == "1.5")) $ret['bericht'] .= $used_info['naam_goed']." ".$txt['recieve']." ".$txt['recieve_boost_and_premium']." ".$ret['exp']." ".$txt['exp_points']."<br />";
          
          else if ($user['premiumaccount'] >= time()) $ret['bericht'] .= $used_info['naam_goed']." ".$txt['recieve_premium_boost']." ".$txt['recieve']." ".$ret['exp']." ".$txt['exp_points']."<br />";
          
          else if ($used_info['trade'] == "1.5") $ret['bericht'] .= $used_info['naam_goed']." ".$txt['recieve']." ".$ret['exp']." ".$txt['exp_points']."<br />";
          
          else $ret['bericht'] .= $used_info['naam_goed']." ".$txt['recieve']." ".$ret['exp']." ".$txt['exp_points']."<br />";

        }
      }
      else $aantal -= 1;
    }
  }
  #Empty Pokemon Used For new pokemon
  DB::exQuery("UPDATE `aanval_log` SET `gebruikt_id`=',".$pokemon_info['id'].",' WHERE `id`='".$aanval_log['id']."'");
 
  return $ret;
}

#Let Pokemon Grow
function pokemon_grow($txt) {
  global $static_url;        
  $_SESSION['used'] = array();    
  $count = 0;
  $sql = DB::exQuery("SELECT pokemon_wild.naam, pokemon_speler.id, pokemon_speler.roepnaam, pokemon_speler.level, pokemon_speler.expnodig, pokemon_speler.exp FROM pokemon_wild INNER JOIN pokemon_speler ON pokemon_wild.wild_id = pokemon_speler.wild_id WHERE user_id='".$_SESSION['id']."' AND `exp`>=`expnodig` AND `opzak`='ja'");
  while($select = $sql->fetch_assoc()) {
    if ($count == 0) $_SESSION['lvl_old'] = $select['level'];
    array_push($_SESSION['used'], $select['id']);
    $count++;
    #Change name for male and female
    $select['naam_goed'] = pokemon_naam($select['naam'],$select['roepnaam']);
    if ($select['level'] < 100) {
      if ($select['exp'] >= $select['expnodig']) {
        do {                
          $real = DB::exQuery("SELECT pokemon_wild.*, pokemon_speler.* FROM pokemon_wild INNER JOIN pokemon_speler ON pokemon_speler.wild_id = pokemon_wild.wild_id  WHERE pokemon_speler.id='".$select['id']."'")->fetch_assoc();
                    
          #level info
          $levelnieuw = $real['level']+1;
          if ($levelnieuw > 100) break;
          
          #Call Script for Calulcalate New stats
          $expnodig = nieuwestats($real,$levelnieuw,$real['exp']);
      
          #Check if Pokemon is growing a level
          if ((!$_SESSION['aanvalnieuw']) AND (!$_SESSION['evolueren'])) $toestemming = levelgroei($levelnieuw,$real);
    
          #make Log
          $pokemonnaam = htmlspecialchars($select['naam_goed'], ENT_QUOTES);
		  
          #Event taal pack includen
          $eventlanguage = GetEventLanguage();
          require_once('../../language/events/language-events-' . $eventlanguage . '.php');
          $event = '<img src="' . $static_url . '/images/icons/blue.png" class="imglower" /> ' . sprintf($txt['event_is_level_up'], '<a href="./pokemon-profile&id='.$select['id'].'">'.$pokemonnaam.'</a>');
          
          #Melding geven aan de uitdager
          DB::exQuery("INSERT INTO gebeurtenis (id, datum, ontvanger_id, bericht, gelezen) VALUES (NULL, NOW(), '".$_SESSION['id']."', '".$event."', '0')");
		
        } while($expnodig < $real['exp'] - $real['expnodig']);
      }
    }
  }
}

#Update Pokemon PLayer Hand
function pokemon_player_hand_update() {
  #Copy Life en Effect Stats to pokemon_speler table
  $player_hand_query = DB::exQuery("SELECT `id`, `leven`, `exp`, `totalexp`, `effect`, `attack_ev`, `defence_ev`, `speed_ev`, `spc.attack_ev`, `spc.defence_ev`, `hp_ev` FROM `pokemon_speler_gevecht` WHERE `user_id`='".$_SESSION['id']."'");
  while($player_hand = $player_hand_query->fetch_assoc()) {
    DB::exQuery("UPDATE `pokemon_speler` SET `leven`='".$player_hand['leven']."', `exp`='".$player_hand['exp']."', `totalexp`='".$player_hand['totalexp']."', `effect`='".$player_hand['effect']."', `attack_ev`=`attack_ev`+'".$player_hand['attack_ev']."', `defence_ev`=`defence_ev`+'".$player_hand['defence_ev']."', `speed_ev`=`speed_ev`+'".$player_hand['speed_ev']."', `spc.attack_ev`=`spc.attack_ev`+'".$player_hand['spc.attack_ev']."', `spc.defence_ev`=`spc.defence_ev`+'".$player_hand['spc.defence_ev']."', `hp_ev`=`hp_ev`+'".$player_hand['hp_ev']."' WHERE `id`='".$player_hand['id']."'");
  }
}

#Remove All Attack Data
function remove_attack($aanval_log_id) {
  #Remove Attack
  DB::exQuery("UPDATE `gebruikers` SET `pagina`='attack_start' WHERE `user_id`='".$_SESSION['id']."'");
  DB::exQuery("DELETE FROM `pokemon_wild_gevecht` WHERE `aanval_log_id`='".$aanval_log_id."'");
  DB::exQuery("DELETE FROM `pokemon_speler_gevecht` WHERE `aanval_log_id`='".$aanval_log_id."'");
  DB::exQuery("DELETE FROM `aanval_log` WHERE `id`='".$aanval_log_id."'");
  unset($_SESSION['caught']);
}


#Vantagens (Water sobre Fire)
function attack_to_defender_advantage($soort,$defender) {
  $voordeel2 = DB::exQuery("SELECT `krachtiger` FROM `voordeel` WHERE `aanval`='".$soort."' AND `verdediger`='".$defender['type1']."'")->fetch_assoc();
  $voordeel3 = DB::exQuery("SELECT `krachtiger` FROM `voordeel` WHERE `aanval`='".$soort."' AND `verdediger`='".$defender['type2']."'")->fetch_assoc();

  if (empty($voordeel2)) $voordeel2['krachtiger'] = 1;

  if (empty($voordeel3)) $voordeel3['krachtiger'] = 1;
	
  $voordeel = $voordeel2['krachtiger'] * $voordeel3['krachtiger'];
  
  return $voordeel;
}

#Multiple Hits
function multiple_hits($attack, $damage) {
  #2-5 times?
  if ($attack['aantalkeer'] == "2-5") {
    $kans = rand(1,4);
    if ($kans != 2) {
      $times = rand(2,3);

      $multi_hit['damage'] = $damage*$times;
      $multi_hit['message'] = "<br />".$attack['naam']." atacou ".$times." vezes. ";
    } else{
      $times = rand(4,5);

      $multi_hit['damage'] = $damage*$times;
      $multi_hit['message'] = "<br />".$attack['naam']." atacou ".$times." vezes. ";
    }
  } else if ($attack['aantalkeer'] == "1-2") {
      $kans = rand(1,4);
      if ($kans == 4) {
        $times = 2;
        $multi_hit['damage'] = $damage * $times;
        $multi_hit['message'] = "<br />".$attack['naam']." atacou ".$times." vezes por causa de sua Ability, Parental Bond!";
      } else {
        $multi_hit['damage'] = $damage;
        $multi_hit['message'] = "";
      }
  }
  else if ($attack['aantalkeer'] == "1-3") {
    $times = rand(1,3);

    $multi_hit['damage'] = $damage*$times;
    $multi_hit['message'] = "<br />".$attack['naam']." atacou ".$times." vezes. ";
  }
  else if ($attack['aantalkeer'] == "gezond_opzak") {
    $times = DB::exQuery("SELECT `id` FROM `pokemon_speler_gevecht` WHERE `user_id`='".$_SESSION['id']."' AND `effect`='' AND `leven`>'0'")->num_rows;

    $multi_hit['damage'] = $damage*$times;
    $multi_hit['message'] = "<br />".$attack['naam']." atacou ".$times." vezes. ";
  }
  else{
    $multi_hit['damage'] = $damage*$attack['aantalkeer'];
    $multi_hit['message'] = "<br />".$attack['naam']." atacou ".$attack['aantalkeer']." vezes. ";
  }
  return $multi_hit;
}

#Controlador de habilidades (dano) pokémon :) (World Legends)
function damage_controller($attacker_info, $opponent_info, $attack_info, $weather = '') {
  $ab1 = $attacker_info['ability'];
  $ab2 = $opponent_info['ability'];

  $ability = ability($ab1)['name'];
  $ability2 = ability($ab2)['name'];

  if ($ability2 == 'Klutz') $attacker_info['item'] = '';

  $atk_name = $attack_info['naam'];
  $based = based($atk_name);
  
  //Mold Breaker
  if (in_array($ability, array('Mold Breaker', 'Teravolt', 'Turboblaze')) && !in_array($ability2, array('Aura Break', 'Magic Guard', 'Comatose', 'Shields Down', 'Full Metal Body', 'Shadow Shield', 'Prism Armor'))) { 
    $ability2 = "";
    echo 'A Ability '.$ability.' de '.$attacker_info['naam'].' cancelou a Ability de '.$opponent_info['naam'].'! <br>';
  }

  if (in_array($atk_name, array('Moongeist Beam', 'Sunsteel Strike', 'Searing Sunraze Smash', 'Menacing Moonraze Maelstrom', 'Light That Burns the Sky'))) {
    $ability2 = "";
    echo 'O Move '.$atk_name.' cancelou a Ability de '.$opponent_info['naam'].'! <br>';
  }

  //Dano
  if ($atk == 'Eruption' || $atk == 'Water Spout') {
    $power = 150 * ($attacker_info['leven'] / $attacker_info['levenmax']);
  } else if ($atk == 'Crush Grip' || $atk == 'Wring Out') {
    $power = 120 * ($opponent_info['leven'] / $opponent_info['levenmax']);
  } else if ($atk == 'Brine' && ($opponent_info['leven'] / $opponent_info['levenmax']) <= 0.5) {
    $power = 130;
  } else if ($atk == 'Venoshock' && $opponent_info['effect'] == 'Poison') {
    $power = 130;
  } else if ($atk == 'Flail' || $atk == 'Reversal') { 
    $hp = ($opponent_info['leven'] / $opponent_info['levenmax']) * 100;
    if ($hp >= 69) {
      $power = 20;
    } else if ($hp > 35 && $hp < 69) {
      $power = 40;
    } else if ($hp > 20 && $hp < 35) {
      $power = 80;
    } else if ($hp > 10 && $hp < 20) {
      $power = 100;
    } else if ($hp > 4 && $hp < 10) {
      $power = 150;
    } else {
      $power = 200;
    }
  } else if ($atk_name == 'Magnitude') {
    $power = array(10, 30, 50, 70, 90, 110, 150)[rand(0, 6)];
  } else {
    $power = $attack_info['sterkte'];
  }
    //Tipo do dano
    $tipo = $attack_info['tipo'];

    if ($atk_name == 'Foul Play') {
      $attacker_info['attack'] = $opponent_info['attack'];
      $attacker_info['spc.attack'] = $opponent_info['spc.attack'];
    }

    if ($atk_name == 'Photon Geyser' || $atk_name == 'Light That Burns the Sky') {
      if ($attacker_info['attack'] > $attacker_info['spc.attack']) {
        if ($opponent_info['defence'] <= 0) $opponent_info['defence'] = 1;
        $def = $opponent_info['defence'];
        $atk = $attacker_info['attack'];
      } else {
        if ($opponent_info['spc.defence'] <= 0) $opponent_info['spc.defence'] = 1;
        $def = $opponent_info['spc.defence'];
        $atk = $attacker_info['spc.attack'];
      }
    } else {
      if ($tipo == 'Physical') {
        if ($opponent_info['defence'] <= 0) $opponent_info['defence'] = 1;
        $def = $opponent_info['defence'];
        $atk = $attacker_info['attack'];
      } else {
        if ($opponent_info['spc.defence'] <= 0) $opponent_info['spc.defence'] = 1;
        $def = $opponent_info['spc.defence'];
        $atk = $attacker_info['spc.attack'];
      }
    }

    if ($attacker_info['item'] == 'Light Ball' && in_array($attacker_info['wild_id'], array('25', '923', '967', '968', '966', '965'))) {
      $atk *= 1.5;
    } else if ($attacker_info['item'] == 'Thick Club' && in_array($attacker_info['wild_id'], array('104', '105')) && $tipo == 'Physical') {
      $atk *= 1.5;
    }

    //Tipo do golpe
    $golpe_type = $attack_info['soort'];

    //Abilities (#1)
    if (in_array($ability, array('Refrigerate', 'Pixilate', 'Aerilate', 'Galvanize')) && $golpe_type == 'Normal') {
      $power *= 1.2;
    } else if ($ability == 'Normalize') { 
      $power *= 1.2; 
    } else if ($ability == 'Technician' && $power <= 60) {
      $power *= 1.5;
    } else if ($ability == 'Water Bubble' && $golpe_type == 'Water') {
      $power *= 1.5;
    } else if ($ability == 'Flash Fire' && $golpe_type == 'Fire') {
      $power *= 1.5;
    } else if ($ability == 'Tough Claws' && $attack_info['makes_contact'] == 1) {
      $power = round($power+($power/3.3));
    } else if ($ability == 'Strong Jaw' && $based == 'bite') {
      $power = round($power+($power/2));
    } else if ($ability == 'Mega Launcher' && $based == 'aura, pulse') {
      $power = round($power+($power/2));
    } else if ($ability == 'Iron Fist' && $based == 'punch') {
      $power = round($power+($power/5));
    }

    //Itens buff
    $buff_i_arr = array('Hard Stone', 'Black Belt', 'Black Glasses', 'Black Sludge', 'Charcoal', 'Dragon Fang', 'Magnet', 'Miracle Seed', 'Mystic Water', 'Never-Melt Ice', 'Twisted Spoon', 'Sharp Beak', 'Silk Scarf', 'Silver Powder', 'Soft Sand', 'Spell Tag', 'Metal Powder', 'Eviolite');
    $buff_t_arr = array('Rock', 'Fighting', 'Dark', 'Poison', 'Fire', 'Dragon', 'Elecric', 'Grass', 'Water', 'Ice', 'Psychic', 'Flying', 'Normal', 'Bug', 'Ground', 'Ghost', 'Steel', 'Fairy');

    if (in_array($attacker_info['item'], $buff_i_arr)) {
      if ($golpe_type == $buff_t_arr[array_search($attacker_info['item'], $buff_i_arr)]) {
        $power = round($power+($power/5));
      }
    }

    if ($attacker_info['item'] == 'Soul Dew' && in_array($attacker_info['wild_id'], array('381', '842', '841', '380')) && in_array($golpe_type, array('Dragon', 'Psychic'))) {
      $power *= 1.45;
    }

    if ($ability2 == 'Thick Fat' && $ability != 'Full Metal Body' && $ability != 'Clear Body') {
      if ($golpe_type == 'Fire' || $golpe_type == 'Ice') {
          $atk *= 0.5;
      }
    } else if ($ability2 == 'Intimidate' && $ability != 'Full Metal Body' && $ability != 'Clear Body') {
      $atk *= 0.5;
    }

    if (($ability == 'Dark Aura' || $ability2 == 'Dark Aura') && ($ability != 'Aura Break' && $ability2 != 'Aura Break')) {
      if ($golpe_type == 'Dark') {
          $atk = round($atk+($atk/3));
      }
    }
    if (($ability == 'Fairy Aura' || $ability2 == 'Fairy Aura') && ($ability != 'Aura Break' && $ability2 != 'Aura Break')) {
        if ($golpe_type == 'Fairy') {
            $atk = round($atk+($atk/3));
        }
    }
    if (($ability == 'Aura Break' || $ability2 == 'Aura Break')) {
        if (in_array($golpe_type, array('Dark', 'Fairy'))) {
            $atk = round($atk-($atk/3));
        }
    }
    if ($ability2 == 'Fur Coat' && $tipo == 'Physical') {
      $def = $def*2;
    } else if ($ability2 == 'Marvel Scale' && !empty($opponent_info['effect'])) {
      $def *= 1.5;
    }

    if ($ability == 'Huge Power' || ($ability == 'Pure Power' && $tipo == 'Physical')) {
        $atk *= 2;
    }else if ($ability == 'Guts' && !empty($attacker_info['effect']) && $attacker_info['effect'] != 'Frozen' && $tipo == 'Physical') {
        $atk *= 2;
    }else if ($ability == 'Toxic Boost' && $attacker_info['effect'] == 'Poison' && $tipo == 'Physical') {
        $atk = round($atk+($atk/2));
    }else if ($ability == 'Flare Boost' && $attacker_info['effect'] == 'Burn' && $tipo == 'Special') {
        $atk = round($atk+($atk/2));
    }else if ($ability == 'Overgrow' && ($attacker_info['leven'] / $attacker_info['levenmax']) <= 0.5 && $golpe_type == 'Grass') {
        $power = round($power+($power/2));
    }else if ($ability == 'Blaze' && ($attacker_info['leven'] / $attacker_info['levenmax']) <= 0.5 && $golpe_type == 'Fire') {
        $power = round($power+($power/2));
    }else if ($ability == 'Torrent' && ($attacker_info['leven'] / $attacker_info['levenmax']) <= 0.5 && $golpe_type == 'Water') {
        $power = round($power+($power/2));
    }else if ($ability == 'Swarm' && ($attacker_info['leven'] / $attacker_info['levenmax']) <= 0.5 && $golpe_type == 'Bug') {
        $power = round($power+($power/2));
    } else if ($ability == 'Steelworker' && $golpe_type == 'Steel') {
        $power *= 1.5;
    } else if ($ability == 'Hustle' && $tipo == 'Physical') {
        $power *= 1.5;
    }

    if ($attacker_info['item'] == 'Expert Belt') {
        $power = round($power+($power/5));
    } else if ($attacker_info['item'] == 'Muscle Band' && $tipo == 'Physical') {
        $power = round($power+($power/10));
    } else if ($attacker_info['item'] == 'Wise Glasses' && $tipo == 'Special') {
        $power = round($power+($power/10));
    } else if ($attacker_info['item'] == 'Metronome' && $attack_info['aantalkeer'] != '1') {
        $power *= 1.3;
    }

    //Vantagem de tipo
    $attack_adv = attack_to_defender_advantage($golpe_type, $opponent_info);

    if ($ability == 'Scrappy' && ($opponent_info['type1'] == 'Ghost' || $opponent_info['type2'] == 'Ghost') && in_array($golpe_type, array('Normal', 'Fighting'))) {
      $attack_adv = 1;
    } else if ($ability == 'Neuroforce' && $attack_adv >= 2) {
      $power = round($power+($power/5));
    }

    if ($weather == 'mysterious_air_current' && ($opponent_info['type1'] == 'Flying' || $opponent_info['type2'] == 'Flying')) {
      $attack_adv = 1;
    }
    //Random
    $luck = rand(200, 256);

    //STAB = Same Type Attack Bonus
    $stab = 1;
    if ($golpe_type == $attacker_info['type1'] || (!empty($attacker_info['type2']) && $golpe_type == $attacker_info['type2'])) {
      $stab = 1.5;
      if ($ability == 'Adaptability') $stab = 2;
    }

    //WEATHER
    $w = 1;
    if (!in_array($ability, array('Air Lock', 'Cloud Nine')) || !in_array($ability2, array('Air Lock', 'Cloud Nine'))) {
      if ($weather == 'rain' || $weather == 'heavy_rain') {
          if ($golpe_type == 'Water') {
              $w = 1.5;
          }else if ($golpe_type == 'Fire') {
              $w = 0.5;
          }
      }else if ($weather == 'harsh_sunlight' || $weather == 'extremely_harsh_sunlight') {
          if ($ability == 'Flower Gift' || ($ability == 'Solar Power' && $tipo == 'Special')) {
              $atk = $atk*1.5;
          }
          if ($ability2 == 'Flower Gift') {
              $def *= 1.5;
          }
          if ($golpe_type == 'Water') {
              $w = 0.5;
          }else if ($golpe_type == 'Fire') {
              $w = 1.5;
          }
      } else if ($weather == 'sandstorm') {
          if ($ability == 'Sand Force' && in_array($golpe_type, array('Rock', 'Ground', 'Steel'))) {
              $power = round($power+($power/3));
          }
          if ($tipo == 'Special') {
            $def *= 1.5;
          }
          if ($atk_name == 'Solar Beam' || $atk_name == 'Solar Blade') {
            $power /= 2;
          }
      } else if ($weather == 'hail') {
        if ($atk_name == 'Solar Beam' || $atk_name == 'Solar Blade') {
          $power /= 2;
        }
      }
    }

    //Formula
    if ($attack_adv > 0) {
      if (in_array ($atk_name, array('Fissure', 'Guillotine', 'Horn Drill', 'Sheer Cold'))) {
        $dano = $opponent_info['leven'];
      } else if ($atk_name == 'Dragon Rage') {
        $dano = 40;
      } else if ($atk_name == 'Sonic Boom') {
        $dano = 20;
      } else if ($atk_name == 'Psywave') {
        $dano = floor(((rand(0, 10)/10) + 0.5) * 100);
      } else if ($atk_name == 'Guardian of Alola') { 
        $dano = ($opponent_info['leven'] * 0.75 > 0)? round($opponent_info['leven'] * 0.75) : 1;
      } else if (in_array($atk_name, array('Seismic Toss', 'Night Shade'))) {
        $dano = $attacker_info['level'];
      } else if (in_array($atk_name, array('Super Fang', 'Natures Madness'))) {
        $dano = ($opponent_info['leven'] * 0.5 > 0)? round($opponent_info['leven'] * 0.5) : 1;
      } else if ($atk_name == 'Endeavor') {
        $dano = $attacker_info['levenmax'] - $attacker_info['leven'];
      } else {
        if ($attack_info['sterkte'] > 0) {
          $dano = round((((((((((2 * $attacker_info['level'] / 5 + 2) * ($atk / $def) * $power)) / 50) + 2) * $stab) * $attack_adv) * $luck) * $w) / 255);
        } else {
          $dano = 0;
        }
      }
    }
    
    //Counter Formula
    if ($ability2 == 'Water Bubble' && $golpe_type == 'Fire') {
      $dano /= 2;
    } else if ($ability2 == 'Flash Fire' && $golpe_type == 'Fire') {
      $dano = 0;
    } else if (($ability2 == 'Filter' || $ability2 == 'Solid Rock' || $ability2 == 'Prism Armor') && $attack_adv >= 2) {
      $dano *= 0.75;
    } else if (($ability2 == 'Shadow Shield' || $ability2 == 'Multiscale') && ($opponent_info['leven'] / $opponent_info['levenmax']) == 1) {
      $dano /= 2;
    } else if ($ability2 == 'Wonder Guard') {
      if ($attack_adv < 2) $dano = 0;
    } else if ($ability2 == 'Levitate') {
      if ($golpe_type == 'Ground') $dano = 0;
    } else if ($ability2 == 'Damp') {
      if (in_array($atk_name, array('Self-Destruct', 'Explosion', 'Mind Blown'))) $dano = 0;
    } else if ($ability2 == 'Heatproof') {
      if ($golpe_type == 'Fire') $dano = round($dano/2);
    } else if (($ability2 == 'Sturdy' || $opponent_info['item'] == 'Focus Sash') && $opponent_info['leven'] == $opponent_info['levenmax'] && $dano >= $opponent_info['leven']) {
      $dano = $opponent_info['leven'] - 1;
    } else if ($opponent_info['item'] == 'Focus Band' && $opponent_info['leven'] == $opponent_info['levenmax'] && $dano >= $opponent_info['leven'] && rand(1, 20) <= 3) {
      $dano = $opponent_info['leven'] - 1;
    } else if ($ability2 == 'Volt Absorb' && $golpe_type == 'Electric') {
      $dano = 0;
    } else if ($ability2 == 'Water Absorb' && $golpe_type == 'Water') {
      $dano = 0;
    } else if ($ability2 == 'Disguise' && rand(1, 16) == 1) {
      echo 'Mimikyu não tomou dano por causa de sua Ability, Disguise!<br>';
      $dano = 0;
    } else if ($ability2 == 'Bulletproof' && $based == 'ball, bomb') {
      $dano = 0;
    } else if ($ability2 == 'Soundproof' && $based == 'sound') {
      $dano = 0;
    }

    if ($ability == 'Tinted Lens' && ($attack_adv > 0 && $attack_adv <= 0.5)) {
      $dano *= 2;
    }

    if ($opponent_info['item'] == 'Air Balloon' && $golpe_type == 'Ground' && rand(0, 1) == 1) {
      $dano = 0;
    }

    if ($weather == 'extremely_harsh_sunlight' && $golpe_type == 'Water') {
      echo 'Ataques tipo água evaporam por conta desse calor!<br>';
      $dano = 0;
    } else if ($weather == 'heavy_rain' && $golpe_type == 'Fire') {
      echo 'Ataques tipo fogo dispersam-se por conta dessa chuva pesada!<br>';
      $dano = 0;
    }
  
  return round($dano);
}

// class Heal {
  
//   public $h = '';
//   private $move = '';
//   private $damage = 0;

//   public function __construct ( $aanval_log, $move, $damage ) {
//       $this->h = $aanval_log;
//       $this->move = $move;
//       $this->damage = $damage;
//   }

//   public function heal_controller ( $attacker_info ) {
//       $trigger = false;
  
//       if ($this->move == '') {

//       }


//       if ($trigger) {


//       } else {
//         return '';
//       }
//   }

//   private function heal_damage ( $attacker_info ) {

//   }

// }

class Weather {

  public $w = '';
  public $clima = '';
  public $table = 'aanval_log';
  public $controller = false;
  private $txt_controller = false;
  private $list = [
    'harsh_sunlight',
    'extremely_harsh_sunlight',
    'rain',
    'heavy_rain',
    'sandstorm',
    'hail',
    'mysterious_air_current'
  ];

  public function __construct ( $aanval_log ) {
      $this->w = $aanval_log;
      $this->clima = $this->w['weather'];

      $this->weather_controller ();
  }
  
  public function weather_controller () {
      if (isset($this->clima) && in_array($this->clima, $this->list)) {
          $this->controller = true;
      } else {
          $this->controller = false;
      }
  }

  public function weather_text ( $txt = '', $suffix = '', $priority = false ) {
      if ( !$this->txt_controller || $priority ) {
        $text = array ();

        $text['weather_harsh_sunlight'] = 'Os raios de sol estão fortes.';
        $text['weather_harsh_sunlight_ended'] = 'Os fortes raios solares desapareceram.';
        
        $text['weather_extremely_harsh_sunlight'] = 'Os raios de sol estão extremamente fortes!';
        $text['weather_extremely_harsh_sunlight_negative'] = 'Ataques tipo Água evaporam por conta desse calor!';
        $text['weather_extremely_harsh_sunlight_ended'] = 'Os extremos raios de sol desapareceram!';

        $text['weather_rain'] = 'Está chovendo.';
        $text['weather_rain_ended'] = 'A chuva parou.';

        $text['weather_heavy_rain'] = 'A chuva começou a ficar densa!';
        $text['weather_heavy_rain_negative'] = 'Ataques tipo Fogo dispersam-se por conta dessa chuva pesada!';
        $text['weather_heavy_rain_ended'] = 'A chuva pesada foi suprimida!';

        $text['weather_sandstorm'] = 'Está acontecendo uma tempestade de areia.';
        $text['weather_sandstorm_negative'] = '%pokemon foi golpeado pela tempestade de areia!';
        $text['weather_sandstorm_ended'] = 'A tempestade de areia diminuiu a intensidade.';

        $text['weather_hail'] = 'Está chovendo granizo.';
        $text['weather_hail_negative'] = '%pokemon foi golpeado pelo granizo!';
        $text['weather_hail_ended'] = 'A chuva de granizo parou.';

        $text['weather_mysterious_air_current'] = 'Os ventos misteriosos estão causando turbulências!';
        $text['weather_mysterious_air_current_negative'] = 'Os ventos misteriosos enfraqueceram o golpe!';
        $text['weather_mysterious_air_current_ended'] = 'Os ventos misteriosos foram dissipados!';

        return $text['weather_'.$this->clima.$txt].$suffix;
      } else {
        return '';
      }
  }

  public function weather_turns ( $attacker_info, $opponent_info ) {
      if ($this->w['beurten'] >= $this->w['weather_turns']) {
        if (!in_array($this->clima, array($this->list[1], $this->list[3], $this->list[6]))) {
          DB::exQuery ("UPDATE `".$this->table."` SET `weather`='NULL', `weather_turns`='0' WHERE `id`='".$this->w['id']."'");

          $this->txt_controller = true;
          $this->controller = false;
          return $this->weather_text ( '_ended' , '<br/>', true );
        } else {
          $ability = DB::exQuery("SELECT * FROM `abilities` WHERE id='$attacker_info[ability]'")->fetch_assoc()['name'];
          $ability2 = DB::exQuery("SELECT * FROM `abilities` WHERE id='$opponent_info[ability]'")->fetch_assoc()['name'];
          
          if (($ability == 'Desolate Land' || $ability2 == 'Desolate Land') && $this->clima == $this->list[1]) {
            
          } else if (($ability == 'Primordial Sea' || $ability2 == 'Primordial Sea') && $this->clima == $this->list[3]) {
          
          } else if (($ability == 'Delta Stream' || $ability2 == 'Delta Stream') && $this->clima == $this->list[6]) {
          
          } else {
            DB::exQuery ("UPDATE `".$this->table."` SET `weather`='NULL', `weather_turns`='0' WHERE `id`='".$this->w['id']."'");

            $this->txt_controller = true;
            $this->controller = false;
            return $this->weather_text ( '_ended' , '<br/>', true );
          }

        }
      }
  }

  public function weather_damage ( $attacker_info ) {
    $trigger = false;

    $attacker_info['ability'] = DB::exQuery("SELECT * FROM `abilities` WHERE id='$attacker_info[ability]'")->fetch_assoc()['name'];

    if ($this->clima == 'hail' && !in_array($attacker_info['ability'], array('Ice Body', 'Snow Cloak', 'Magic Guard', 'Overcoat', 'Slush Rush')) && ($attacker_info['type1'] != 'Ice' && $attacker_info['type2'] != 'Ice')) {
        $trigger = true;
    } else if ($this->clima == 'sandstorm' && !in_array($attacker_info['ability'], array('Sand Force', 'Sand Rush', 'Sand Veil', 'Magic Guard', 'Overcoat')) && ( !in_array ($attacker_info['type1'], array('Rock', 'Steel', 'Ground')) && !in_array ($attacker_info['type2'], array('Rock', 'Steel', 'Ground')))) {
        $trigger = true;
    }
    
    if ($attacker_info['item'] == 'Safety Goggles') {
      $trigger = false;
    }

    if ($trigger) {
      $damage = $attacker_info['leven'] - round ($attacker_info['levenmax'] / 16);
      if ($damage <= 0) {
        $damage = 0;
      }
  
      DB::exQuery("UPDATE `" . $attacker_info['table']['fight'] . "` SET `leven`='" . $damage . "' WHERE `id`='" . $attacker_info['id'] . "'");
      $text_modify = str_replace('%pokemon', $attacker_info['naam_goed'], $this->weather_text ( '_negative' , '<br/>' ));
      return $text_modify;
    } else {
      return '';
    }
  }

  public function weather_heal ( $attacker_info ) {
    $trigger = false;

    $attacker_info['ability'] = DB::exQuery("SELECT * FROM `abilities` WHERE id='$attacker_info[ability]'")->fetch_assoc()['name'];

    if ($this->clima == 'rain' && $attacker_info['ability'] == 'Rain Dish') {
      $trigger = true;
    } else if ($this->clima == 'hail' && $attacker_info['ability'] == 'Ice Body') {
      $trigger = true;
    }

    if ($trigger) {
      $heal = $attacker_info['leven'] + round ($attacker_info['levenmax'] / 16);
      if ($heal > $attacker_info['levenmax']) {
        $heal = $attacker_info['levenmax'];
      }

      DB::exQuery("UPDATE `" . $attacker_info['table']['fight'] . "` SET `leven`='" . $heal . "' WHERE `id`='" . $attacker_info['id'] . "'");
      $text_modify = str_replace('%pokemon', $attacker_info['naam_goed'], '%pokemon foi curado por causa de sua Ability. <br>');
      return $text_modify;
    } else {
      return '';
    }
  }

  public function weather_create ($attacker_info, $opponent_info, $attack_info) {
      if (!$this->controller) {
        $ability = DB::exQuery("SELECT * FROM `abilities` WHERE id='$attacker_info[ability]'")->fetch_assoc()['name'];
        $ability2 = DB::exQuery("SELECT * FROM `abilities` WHERE id='$opponent_info[ability]'")->fetch_assoc()['name'];
        $atk_name = $attack_info['naam'];
        $turns = $this->w['beurten'];
        $weather = '';

        if ($ability == 'Desolate Land') {
          $weather = 'extremely_harsh_sunlight';
          $turns += 0;

          DB::exQuery ("UPDATE `".$this->table."` SET `weather`='$weather', `weather_turns`='$turns' WHERE `id`='".$this->w['id']."'");
        } else if ($ability == 'Primordial Sea') {
          $weather = 'heavy_rain';
          $turns += 0;

          DB::exQuery ("UPDATE `".$this->table."` SET `weather`='$weather', `weather_turns`='$turns' WHERE `id`='".$this->w['id']."'");
        } else if ($ability == 'Delta Stream') {
          $weather = 'mysterious_air_current';
          $turns += 0;

          DB::exQuery ("UPDATE `".$this->table."` SET `weather`='$weather', `weather_turns`='$turns' WHERE `id`='".$this->w['id']."'");
        }

        if (!in_array($ability2, array('Air Lock', 'Cloud Nine'))) {
          if ($atk_name == 'Sunny Day') {
            $weather = 'harsh_sunlight';
            $turns += 5;
            if ($attacker_info['item'] == 'Heat Rock') {
              $turns += 3;
            }

            DB::exQuery ("UPDATE `".$this->table."` SET `weather`='$weather', `weather_turns`='$turns' WHERE `id`='".$this->w['id']."'");
          } else if ($atk_name == 'Rain Dance') {
            $weather = 'rain';
            $turns += 5;
            if ($attacker_info['item'] == 'Damp Rock') {
              $turns += 3;
            }

            DB::exQuery ("UPDATE `".$this->table."` SET `weather`='$weather', `weather_turns`='$turns' WHERE `id`='".$this->w['id']."'");
          } else if ($atk_name == 'Sandstorm') {
            $weather = 'sandstorm';
            $turns += 5;
            if ($attacker_info['item'] == 'Smooth Rock') {
              $turns += 3;
            }

            DB::exQuery ("UPDATE `".$this->table."` SET `weather`='$weather', `weather_turns`='$turns' WHERE `id`='".$this->w['id']."'");
          } else if ($atk_name == 'Hail') {
            $weather = 'hail';
            $turns += 5;
            if ($attacker_info['item'] == 'Icy Rock') {
              $turns += 3;
            }

            DB::exQuery ("UPDATE `".$this->table."` SET `weather`='$weather', `weather_turns`='$turns' WHERE `id`='".$this->w['id']."'");
          }
        }
        
      }
  }

}
?>