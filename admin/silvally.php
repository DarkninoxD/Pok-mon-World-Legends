<?php
#Script laden zodat je nooit pagina buiten de index om kan laden
include("app/includes/resources/security.php");

// $types = array('Bug','Dark','Dragon','Electric','Fairy','Fighting','Fire','Flying','Ghost','Grass','Ground','Ice','Poison','Psychic','Rock','Steel','Water');
// $tutor = array('Draco Meteor');
// $tm = array('TM01', 'TM02', 'TM05', 'TM06', 'TM07', 'TM10', 'TM100', 'TM11', 'TM13', 'TM15', 'TM17', 'TM18', 'TM21', 'TM24', 'TM27', 'TM30', 'TM32', 'TM35', 'TM37', 'TM40', 'TM42', 'TM43', 'TM44', 'TM48', 'TM51', 'TM64', 'TM65', 'TM66', 'TM68', 'TM73', 'TM75', 'TM80', 'TM81', 'TM87', 'TM88', 'TM89', 'TM90', 'TM91', 'TM94', 'TM95');

// $a = DB::exQuery("SELECT `wild_id`, `naam` FROM `pokemon_wild`");
// $folder = 'public/images/shiny/back/';

// foreach ($a as $pw) {
//     $id = $pw['wild_id'];
//     $name_raw = str_replace('.','', str_replace(' ', '-', strtolower($pw['naam']))).'.gif';
//     $act = $id.'.gif';

//     rename($folder.$name_raw, $folder.$act);
// }

// for($i = 0; $i < sizeof($types); $i++) {
//     $type = $types[$i];
//     if ($i < 9) {
//         $ia ='00'.($i+1);
//     } else {
//         $ia = '0'.($i+1);
//     }

//     $id ='773'.$ia;
//     $item = $type.' Memory';
//     $name = 'Silvally ('.$type.')';
//     $name_raw = 'silvally-'.strtolower($type).'.png';
//     $act = $id.'.gif';

    // rename($folder.$name_raw, $folder.$act);

    // foreach($tutor as $check) {
   
    //     $pegasobre = DB::exQuery("select * from tmhm_movetutor where naam='".$check."'")->fetch_assoc();
    
    //     $rel = "".$pegasobre['relacionados'].",".$id."";
    
    //     DB::exQuery("UPDATE `tmhm_movetutor` SET `relacionados`='$rel' WHERE naam='".$check."'");
  
  
    // }
    
    // foreach($tm as $check2) {
      
    //   $pegasobre2 = DB::exQuery("select * from tmhm_relacionados where naam='".$check2."'")->fetch_assoc();
      
    //   $rel = "".$pegasobre2['relacionados'].",".$id."";
          
    //   DB::exQuery("UPDATE `tmhm_relacionados` SET `relacionados`='$rel' WHERE naam='".$check2."'");     
          
          
    // }

    // DB::exQuery("DELETE FROM `levelen` WHERE `wild_id`='$id'");
    // DB::exQuery("DELETE FROM `pokemon_wild` WHERE `wild_id`='$id'");

    // DB::exQuery("INSERT INTO `levelen` (`level`, `stone`, `trade`, `wild_id`, `wat`, `nieuw_id`, `aanval`, `gender`, `region`, `time`) VALUES 
    // (1, '', 0, $id, 'att', 0, 'Fire Fang', '', '', ''),
    // (1, '', 0, $id, 'att', 0, 'Heal Block', '', '', ''),
    // (1, '', 0, $id, 'att', 0, 'Ice Fang', '', '', ''),
    // (1, '', 0, $id, 'att', 0, 'Imprison', '', '', ''),
    // (1, '', 0, $id, 'att', 0, 'Iron Head', '', '', ''),
    // (1, '', 0, $id, 'att', 0, 'Multi-Attack', '', '', ''),
    // (1, '', 0, $id, 'att', 0, 'Poison Fang', '', '', ''),
    // (1, '', 0, $id, 'att', 0, 'Tackle', '', '', ''),
    // (1, '', 0, $id, 'att', 0, 'Thunder Fang', '', '', ''),
    // (5, '', 0, $id, 'att', 0, 'Rage', '', '', ''),
    // (10, '', 0, $id, 'att', 0, 'Pursuit', '', '', ''),
    // (15, '', 0, $id, 'att', 0, 'Bite', '', '', ''),
    // (20, '', 0, $id, 'att', 0, 'Aerial Ace', '', '', ''),
    // (25, '', 0, $id, 'att', 0, 'Crush Claw', '', '', ''),
    // (30, '', 0, $id, 'att', 0, 'Scary Face', '', '', ''),
    // (35, '', 0, $id, 'att', 0, 'X-Scissor', '', '', ''),
    // (40, '', 0, $id, 'att', 0, 'Take Down', '', '', ''),
    // (45, '', 0, $id, 'att', 0, 'Metal Sound', '', '', ''),
    // (50, '', 0, $id, 'att', 0, 'Crunch', '', '', ''),
    // (55, '', 0, $id, 'att', 0, 'Double Hit', '', '', ''),
    // (60, '', 0, $id, 'att', 0, 'Air Slash', '', '', ''),
    // (65, '', 0, $id, 'att', 0, 'Punishment', '', '', ''),
    // (70, '', 0, $id, 'att', 0, 'Razor Wind', '', '', ''),
    // (75, '', 0, $id, 'att', 0, 'Tri Attack', '', '', ''),
    // (80, '', 0, $id, 'att', 0, 'Double-Edge', '', '', ''),
    // (85, '', 0, $id, 'att', 0, 'Parting Shot', '', '', '')");

    // DB::exQuery("INSERT INTO `pokemon_wild` (`wild_id`, `wereld`, `naam`, `zeldzaamheid`, `evolutie`, `type1`, `type2`, `gebied`, `vangbaarheid`, `groei`, `base_exp`, `aanval_1`, `aanval_2`, `aanval_3`, `aanval_4`, `attack_base`, `defence_base`, `spc.attack_base`, `spc.defence_base`, `speed_base`, `hp_base`, `effort_attack`, `effort_defence`, `effort_spc.attack`, `effort_spc.defence`, `effort_speed`, `effort_hp`, `aparece`, `lendario`, `comerciantes`, `real_id`, `ability`) VALUES ($id, 'Alola', '$name', 3, 2, '$type', '', '', '3', 'Slow', 114, 'Air Slash', 'Double Hit', 'Iron Head', 'Multi-Attack', 95, 95, 95, 95, 95, 95, 0, 0, 0, 0, 0, 3, 'sim', 0, 'nao', 773, '225')");
// }
?>