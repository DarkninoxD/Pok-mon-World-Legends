<?php //Is all the information send
if ((isset($_GET['duel_id'])) && (isset($_GET['sid']))) {
  //Connect With Database
  include_once("../../app/includes/resources/config.php");
  //Include Default Functions
  include_once("../../app/includes/resources/ingame.inc.php");
  //Include Attack Functions
  include("../attack.inc.php");
  //Include Duel Functions
  include_once("duel.inc.php"); 
  //Load language
  include_once('../../language/language-general.php');
  //Load duel info
  $duel_info = duel_info($_GET['duel_id']);
  $league_id = 0;
        
  if ($duel_info['uitdager'] == $_SESSION['naam']) {
    $you = DB::exQuery("SELECT user_id, username FROM gebruikers WHERE username='".$_SESSION['naam']."'")->fetch_assoc();
    $other =  DB::exQuery("SELECT user_id, username FROM gebruikers WHERE username='".$duel_info['tegenstander']."'")->fetch_assoc();
    $you_ch = $duel_info['u_character'];
    $other_ch = $duel_info['t_character'];
    $other_id = $duel_info['t_used_id'];
    $dood_1 = DB::exQuery("SELECT id FROM pokemon_speler_gevecht WHERE leven='0' AND user_id='".$you['user_id']."'")->num_rows;
    $dood_2 = DB::exQuery("SELECT id FROM pokemon_speler_gevecht WHERE leven='0' AND user_id='".$other['user_id']."'")->num_rows;
  } else if ($duel_info['tegenstander'] == $_SESSION['naam']) {
    $you = DB::exQuery("SELECT user_id, username FROM gebruikers WHERE username='".$_SESSION['naam']."'")->fetch_assoc();
    $other =  DB::exQuery("SELECT user_id, username FROM gebruikers WHERE username='".$duel_info['uitdager']."'")->fetch_assoc();
    $you_ch = $duel_info['t_character'];
    $other_ch = $duel_info['u_character'];
    $other_id = $duel_info['u_used_id'];
    $dood_1 = DB::exQuery("SELECT id FROM pokemon_speler_gevecht WHERE leven='0' AND user_id='".$other['user_id']."'")->num_rows;
    $dood_2 = DB::exQuery("SELECT id FROM pokemon_speler_gevecht WHERE leven='0' AND user_id='".$you['user_id']."'")->num_rows;
  }
  //Update Hand Pokemon
  pokemon_player_hand_update();
  //Grow Pokemon
  pokemon_grow($txt);
  
  if ($_SESSION['naam'] == $duel_info['winner']) {
    //Save log
    DB::exQuery("INSERT INTO duel_logs (`datum`, `win`, `lose`)
      VALUES ( '".date("Y-m-d H:i:s")."', '".$you['user_id']."', '".$other['user_id']."')");
    
    DB::exQuery("UPDATE `gebruikers` SET `gewonnen`=`gewonnen`+'1' WHERE `user_id`='".$you['user_id']."'");
    DB::exQuery("UPDATE `gebruikers` SET `verloren`=`verloren`+'1' WHERE `user_id`='".$other['user_id']."'");

    if ($duel_info['bedrag'] > 0) {
      DB::exQuery("UPDATE `gebruikers` SET `silver`=`silver`-'".$duel_info['bedrag']."',`points`=if (`points` > 0, (`points` - 90), 0),`points_temp`=if (`points_temp` > 0, (`points_temp` - 90), 0) WHERE `user_id`='".$other['user_id']."'");
      DB::exQuery("UPDATE `gebruikers` SET `silver`=`silver`+'".$duel_info['bedrag']."',`points`=(`points`+150),`points_temp`=(`points_temp`+150) WHERE `user_id`='".$you['user_id']."'");
    }
    
    if ($duel_info['ronde_id'] != 0) {
       DB::exQuery("UPDATE toernooi_ronde SET dood_1='" . $dood_1 . "', dood_2='" . $dood_2 . "', winnaar_id='" . $you['user_id'] . "' WHERE ronde='" . $duel_info['ronde_id'] . "' ORDER BY toernooi DESC");
       DB::exQuery("UPDATE toernooi_ronde SET user_id_1='" . $you['user_id'] . "', gereed=gereed+'1' WHERE user_id_1='-" . $duel_info['ronde_id'] . "'");
       DB::exQuery("UPDATE toernooi_ronde SET user_id_2='" . $you['user_id'] . "', gereed=gereed+'1' WHERE user_id_2='-" . $duel_info['ronde_id'] . "'");
    }
    
      require_once '../../app/classes/League_battle.php';

        if ($league_battle = League_battle::select_duel($_GET['duel_id'])) {
            $league_id = $league_battle->getLeague_id();
            $league_battle->informarVencedor($you['user_id'], "Batalha finalizada");
            $league_battle->update();
        }
    
    rankerbij('duel',$txt);
    $quests->setStatus('win_duel', $_SESSION['id']);
    $text = 1;
  }
  else{
    $text = 2;
  }

  if ($duel_info['status'] == 'finish') remove_duel($duel_info['id']);
  
  DB::exQuery("UPDATE `duel` SET `status`='finish' WHERE `id`='".$_GET['duel_id']."'"); 
                             
  unset($_SESSION['duel']['duel_id']);

    echo $text . " | " . $duel_info['bedrag'] . " | " . $you . " | " . $other . " | " . $you_ch . " | " . $other_ch . " | " . $other_id. " | " . $league_id;  
}
?>