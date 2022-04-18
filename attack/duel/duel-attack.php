<?php 
#Load Safety Script
include("app/includes/resources/security.php");

#Include Duel Functions
include("attack/duel/duel.inc.php");

#Include Attack Functions
include("attack/attack.inc.php");

#Load duel info
$duel_info = duel_info($_SESSION['duel']['duel_id']);

# Check if uitdager en tegenstander or valid
if (($duel_info['uitdager'] != $_SESSION['naam']) AND ($duel_info['tegenstander'] != $_SESSION['naam'])) {
  remove_duel($duel_info['id']);
  #Send back to home
  header("Location: ./home");
  #Delete Cookie
  unset($_SESSION['duel']['duel_id']);
} 

if ($duel_info['uitdager'] == $_SESSION['naam']) {
  $duel_info['you'] = "uitdager";
  $duel_info['you_duel'] = "u_klaar";  
  $duel_info['you_sex'] = $duel_info['u_character'];
  $duel_info['opponent'] = "tegenstander";
  $duel_info['opponent_duel'] = "t_klaar";
  $duel_info['opponent_sex'] = $duel_info['t_character'];
  $duel_info['opponent_name'] = $duel_info['tegenstander'];
  
  #Load All Pokemon Info
  $pokemon_info = pokemon_data($duel_info['u_pokemonid']);
  $pokemon_info['naam_klein'] = strtolower($pokemon_info['naam']);
  $pokemon_info['naam_goed'] = pokemon_naam($pokemon_info['naam'],$pokemon_info['roepnaam']);

  #Calculate Life in Procent for Pokemon         
  if ($pokemon_info['leven'] != 0) $pokemon_life_procent = round(($pokemon_info['leven']/$pokemon_info['levenmax'])*100);
  else $pokemon_life_procent = 0;

  #Calculate Exp in procent for pokemon
  if ($pokemon_info['expnodig'] == 0) $pokemon_info['expnodig'] =1;
  if ($pokemon_info['exp'] != 0) $pokemon_exp_procent = round(($pokemon_info['exp']/$pokemon_info['expnodig'])*100);
  else $pokemon_exp_procent = 0;
  
  #Shiny
  $pokemon_info['map'] = "pokemon";
  $pokemon_info['star'] = "none";
  if ($pokemon_info['shiny'] == 1) {
    $pokemon_info['map'] = "shiny";
    $pokemon_info['star'] = "block";
  }

  #Load All Opoonent Info
  $opponent_info = pokemon_data($duel_info['t_pokemonid']);
  $opponent_info['naam_klein'] = strtolower($opponent_info['naam']);
  $opponent_info['naam_goed'] = pokemon_naam($opponent_info['naam'],$opponent_info['roepnaam']);

  #Calculate Life in Procent for Pokemon         
  if ($opponent_info['leven'] != 0) $opponent_life_procent = round(($opponent_info['leven']/$opponent_info['levenmax'])*100);
  else $opponent_life_procent = 0;
  
  #Shiny
  $opponent_info['map'] = "pokemon";
  $opponent_info['star'] = "none";
  if ($opponent_info['shiny'] == 1) {
    $opponent_info['map'] = "shiny";
    $opponent_info['star'] = "block";
  }
}

else if ($duel_info['tegenstander'] == $_SESSION['naam']) {
  $duel_info['you'] = "tegenstander";
  $duel_info['you_duel'] = "t_klaar";
  $duel_info['you_sex'] = $duel_info['t_character'];
  $duel_info['opponent'] = "uitdager";
  $duel_info['opponent_duel'] = "u_klaar";
  $duel_info['opponent_sex'] = $duel_info['u_character'];
  $duel_info['opponent_name'] = $duel_info['uitdager'];

  #Load All Pokemon Info
  $pokemon_info = pokemon_data($duel_info['t_pokemonid']);
  $pokemon_info['naam_klein'] = strtolower($pokemon_info['naam']);
  $pokemon_info['naam_goed'] = pokemon_naam($pokemon_info['naam'],$pokemon_info['roepnaam']);

  #Calculate Life in Procent for Pokemon         
  if ($pokemon_info['leven'] != 0) $pokemon_life_procent = round(($pokemon_info['leven']/$pokemon_info['levenmax'])*100);
  else $pokemon_life_procent = 0;

  #Calculate Exp in procent for pokemon
  if ($pokemon_info['expnodig'] == 0) $pokemon_info['expnodig'] =1;
  if ($pokemon_info['exp'] != 0) $pokemon_exp_procent = round(($pokemon_info['exp']/$pokemon_info['expnodig'])*100);
  else $pokemon_exp_procent = 0;
  
  #Shiny
  $pokemon_info['map'] = "pokemon";
  $pokemon_info['star'] = "none";
  if ($pokemon_info['shiny'] == 1) {
    $pokemon_info['map'] = "shiny";
    $pokemon_info['star'] = "block";
  }

  #Load All Opoonent Info
  $opponent_info = pokemon_data($duel_info['u_pokemonid']);
  $opponent_info['naam_klein'] = strtolower($opponent_info['naam']);
  $opponent_info['naam_goed'] = pokemon_naam($opponent_info['naam'],$opponent_info['roepnaam']);

  #Calculate Life in Procent for Pokemon         
  if ($opponent_info['leven'] != 0) $opponent_life_procent = round(($opponent_info['leven']/$opponent_info['levenmax'])*100);
  else $opponent_life_procent = 0;
  #Shiny
  $opponent_info['map'] = "pokemon";
  $opponent_info['star'] = "none";
  if ($opponent_info['shiny'] == 1) {
    $opponent_info['map'] = "shiny";
    $opponent_info['star'] = "block";
  }
}

$time_left = strtotime(date("Y-m-d H:i:s"))-$duel_info['laatste_beurt_tijd'];
if ($time_left > 121) $time_left = 119;


for($inhand = 1; $player_hand = $pokemon_sql->fetch_assoc(); $inhand++) {
  #Check Wich Pokemon is infight
  if ($player_hand['id'] == $pokemon_info['id']) $infight = 1;
  else $infight = 0;
  if ($player_hand['ei'] == 1) { 
    $player_hand['naam'] = "??";
    $player_hand['wild_id'] = "??";
  }

  $battle_lifes = pokemon_data($player_hand['id']);
  ?>
  <script>
    //If div is ready
    $("div[id='change_pokemon']").ready(function() {
        //Is pokemon in fight, so yes, don't show
        if (<?= $infight; ?> == 1) {
          if (<?= $player_hand['shiny']; ?> == 1) {
            $("div[id='change_pokemon'][name='<?= $inhand; ?>']").css({ backgroundImage : "url(<?=$static_url?>/images/shiny/icon/<?= strtolower($player_hand['wild_id']); ?>.gif)" });
            $("div[id='change_pokemon'][name='<?= $inhand; ?>']").html("<?= $player_hand['naam']; ?> <div class='hp_red' style='height: 3px; width: 86%;'><div class='progress' style='width: <?= floor(($battle_lifes['leven'] * 100) / $player_hand['levenmax']); ?>%'></div></div>");
            $("div[id='change_pokemon'][name='<?= $inhand; ?>']").attr("data-original-title", "<?= $player_hand['naam']; ?> \nHP: <?= $battle_lifes['leven']; ?>/<?= $player_hand['levenmax']; ?>");
          } else{
       	    $("div[id='change_pokemon'][name='<?= $inhand; ?>']").css({ backgroundImage : "url(<?=$static_url?>/images/pokemon/icon/<?= strtolower($player_hand['wild_id']); ?>.gif)" });
            $("div[id='change_pokemon'][name='<?= $inhand; ?>']").html("<?= $player_hand['naam']; ?> <div class='hp_red' style='height: 3px; width: 86%;'><div class='progress' style='width: <?= floor(($battle_lifes['leven'] * 100) / $player_hand['levenmax']); ?>%'></div></div>");
            $("div[id='change_pokemon'][name='<?= $inhand; ?>']").attr("data-original-title", "<?= $player_hand['naam']; ?> \nHP: <?= $battle_lifes['leven']; ?>/<?= $player_hand['levenmax']; ?>");
          }      
        } else if (1 == "<?= $player_hand['ei']; ?>") {
          $("div[id='change_pokemon'][name='<?= $inhand; ?>']").css({ backgroundImage : "url(<?=$static_url?>/images/icons/egg.gif)" });
          $("div[id='change_pokemon'][name='<?= $inhand; ?>']").html("Egg Pokémon");
          $("div[id='change_pokemon'][name='<?= $inhand; ?>']").attr("data-original-title", "Egg Pokémon");
          $("div[id='change_pokemon'][name='<?= $inhand; ?>']").show();
        }
        //Pokemon is not in fight, show.
        else{
          if (<?= $player_hand['id']; ?> != "") {
            if (<?= $player_hand['shiny']; ?> == 1) {
              $("div[id='change_pokemon'][name='<?= $inhand; ?>']").css({ backgroundImage : "url(<?=$static_url?>/images/shiny/icon/<?= strtolower($player_hand['wild_id']); ?>.gif)" });
              $("div[id='change_pokemon'][name='<?= $inhand; ?>']").html("<?= $player_hand['naam']; ?> <div class='hp_red' style='height: 3px; width: 86%;'><div class='progress' style='width: <?= floor(($battle_lifes['leven'] * 100) / $player_hand['levenmax']); ?>%'></div></div>");
              $("div[id='change_pokemon'][name='<?= $inhand; ?>']").attr("data-original-title", "<?= $player_hand['naam']; ?> \nHP: <?= $battle_lifes['leven']; ?>/<?= $player_hand['levenmax']; ?>");
            }
            else{
         	    $("div[id='change_pokemon'][name='<?= $inhand; ?>']").css({ backgroundImage : "url(<?=$static_url?>/images/pokemon/icon/<?= strtolower($player_hand['wild_id']); ?>.gif)" });
              $("div[id='change_pokemon'][name='<?= $inhand; ?>']").html("<?= $player_hand['naam']; ?> <div class='hp_red' style='height: 3px; width: 86%;'><div class='progress' style='width: <?= floor(($battle_lifes['leven'] * 100) / $player_hand['levenmax']); ?>%'></div></div>");
              $("div[id='change_pokemon'][name='<?= $inhand; ?>']").attr("data-original-title", "<?= $player_hand['naam']; ?> \nHP: <?= $battle_lifes['leven']; ?>/<?= $player_hand['levenmax']; ?>");
            }
            $("div[id='change_pokemon'][name='<?= $inhand; ?>']").show()
          }
        }
      });
  </script>
<?php
}
//Player Pokemon In Hand
$pokemon_sql->data_seek(0);
?>

<script type="text/javascript" src="./attack/duel/javascript/duel.js"></script>
<script type="text/javascript" src="./attack/javascript/animation.js"></script>

<script language="javascript">
var vol = (<?=$gebruiker['volume']?>-3)/100;
if (vol < 0) vol = 0;

var sound_base = sound_base = new Howl({
      src: ['public/sounds/player.mp3'],
      autoplay: true,
      loop: true,
      html5: true,
      volume: vol
    });
    
sound_base.play();

var you_to_late
var opp_to_late
var ready_check
var attack
var wissel
var start_text
var max_time = 120
var you_time_used
var opp_time_used
var end
var you = "<?= $gebruiker['username']; ?>";
var atk = '';
var trainer_zmove = <?=$duel_info['zmove_'.str_replace('_klaar', '', $duel_info['you_duel'])]?>;

function your_to_late() {
  clearTimeout(you_to_late)
  $("#message").html("<?= $txt['too_late_lost']; ?>")
  setTimeout("show_end_screen()", 5000)
}

function you_check_to_late() {
  you_time_used++
  $("#time_left").html(max_time-you_time_used)
  if (you_time_used >= max_time) your_to_late()
  else you_to_late = setTimeout('you_check_to_late()', 1000)
}

function opponent_check_to_late() {
  opp_time_used++
  $("#time_left").html(max_time-opp_time_used)
  if (opp_time_used >= max_time) last_move_check()
  else opp_to_late = setTimeout('opponent_check_to_late()', 1000)
}

function do_wissel(request) {
  if (request[5] == 1) {
    $("#img_opponent").attr("src","<?= $static_url?>/images/shiny/"+request[3]+".gif")
    $("#opponent_star").show()
  }
  else{
    $("#img_opponent").attr("src","<?= $static_url?>/images/pokemon/"+request[3]+".gif")
    $("#opponent_star").hide()
  }
  wlSound('cries/'+request[3], <?=$gebruiker['volume']?>, false);
  var opponent_life_procent = Math.round((request[6]/request[7])*100)
  $("#opponent_life").width(opponent_life_procent+'%')
  $("#opponent_naam").html(request[4])
  if (request[8] != "") {
    $("#opponent_hand_"+request[8]).attr("src","<?= $static_url?>/images/icons/pokeball_black.gif")
    $("#opponent_hand_"+request[8]).attr("data-original-title","Derrotado")
  }
}

  var weather = [
    'harsh_sunlight',
    'extremely_harsh_sunlight',
    'rain',
    'heavy_rain',
    'sandstorm',
    'hail',
    'mysterious_air_current'
  ];

$("#message").ready(function() {
  if ("<?= $_SESSION['duel']['begin_zien']; ?>" == 1) {
    $("#you_text").hide()
    if (you == "<?= $duel_info['tegenstander']; ?>") {
      $("#message").html("<?= 'Você está duelando contra '.$duel_info['uitdager'].'.' ?>")
      $("#opponent_naam").html("<?= $duel_info['opponent_name']; ?>.")    
    } else if (you == "<?= $duel_info['uitdager']; ?>") {
      $("#message").html("<?= $duel_info['tegenstander'].' aceitou seu duelo.' ?>")
      $("#opponent_naam").html("<?= $duel_info['opponent_name']; ?>.")
    }
    //Set Images
    $("#img_you").attr("src","<?= $static_url?>/images/characters/<?= $gebruiker['character']; ?>/Thumb.png")
    $("#img_opponent").attr("src","<?= $static_url?>/images/characters/<?= $duel_info['opponent_sex']; ?>/Thumb.png")
    $("#opponent_life").width('100%')
    $("#pokemon_life").width('100%')
    $("#pokemon_exp").width('0%')
    
    ready_check = setTimeout("check_ready()", 1000)
  } else if ("<?= $duel_info['laatste_beurt']; ?>" == you+"_begin") {
    $("#message").html("<?= $txt['you_first_attack']; ?>")
    attack = 1
    wissel = 1
    you_time_used = <?= $time_left; ?>;
    you_check_to_late()
  } else if ("<?= $duel_info['laatste_beurt']; ?>" != you+'_begin') {
    var who = "<?= $duel_info['laatste_beurt']; ?>".split("_", 1);
    $("#message").html(who+" <?= $txt['opponent_first_attack']; ?>")
    attack = 0
    wissel = 0
    last_move_check()
    opp_time_used = <?= $time_left; ?>;
    opponent_check_to_late()
  } else if ("<?= $duel_info['volgende_zet']; ?>" == "end_screen") {
    end = show_end_screen()
  } else if (("<?= $duel_info['volgende_beurt']; ?>" == you) && ("<?= $duel_info['volgende_zet']; ?>" == "wisselen")) {
    $("#message").html("Substitua este Pokémon!")
    attack = 0
    wissel = 1
    you_time_used = <?= $time_left; ?>;
    you_check_to_late()
  } else if ("<?= $duel_info['volgende_beurt']; ?>" == you) {
    $("#message").html("<?= $txt['your_turn']; ?>")
    attack = 1
    wissel = 1
    you_time_used = <?= $time_left; ?>;
    you_check_to_late()
  } else if ("<?= $duel_info['volgende_zet']; ?>" == "wisselen") {
    $("#message").html("<?= $duel_info['tegenstander'].' '.$txt['opponent_change']; ?>")
    attack = 0
    wissel = 0
    last_move_check()
    opp_time_used = <?= $time_left; ?>;
    opponent_check_to_late()
  } else {
    $("#message").html("<?= $duel_info['tegenstander'].' '.$txt['opponents_turn']; ?>")
    attack = 0
    wissel = 0
    last_move_check()
    opp_time_used = <?= $time_left; ?>;
    opponent_check_to_late()
  }

  if (weather.indexOf('<?=$duel_info['weather']?>') != -1) {
      $('#weather').addClass('weather <?=$duel_info['weather']?>');
  } else {
      $('#weather').removeClass();
  }
});

function last_move_check() {
  clearTimeout(you_to_late)
  $.get("attack/duel/last_move_check.php?duel_id="+<?= $duel_info['id']; ?>+"&sid="+Math.random(), function(data) {
    request = data.split(" | ")
    
    if (request[14] == '') {
        $("#opponent_effect").css('display', 'none');
    } else {
        $("#opponent_effect img")
            .attr('src', '<?=$static_url?>/images/effects/' + request[14] + '.png')
            .attr('alt', request[14])
            .attr('data-original-title', request[14]);
        $("#opponent_effect").css('display', 'block');
    }

    //No reaction
    if (request[0] == 0) {
      setTimeout('last_move_check()', 1500)
      attack = 0
      wissel = 0
    }
    //You can to Attack
    else if (request[0] == 1) {
      if (request[2] == "wissel") {
        do_wissel(request)
      } else{
	      document.getElementById('hit').style.display = "none";
        document.getElementById('hit2').style.display = "";
	      document.getElementById('dame').style.display = "none";
        document.getElementById('dame2').style.display = "";
        if (request[10] != '') {
        var gif_sufixo = '';
        var gif_attack = '_blank';
                                                                                              
        if (request[10] == 'Fire') {
        gif_attack = 'burn';
        } else if (request[10] == 'Water') {
        gif_attack = 'wave';
        } else if (request[10] == 'Electric') {
        gif_attack = 'electric';
        } else if (request[10] == 'Dark') {
        gif_attack = 'dark';
        } else if (request[10] == 'Steel') {
        gif_attack = 'steel';
        } else if (request[10] == 'Psychic') {
        gif_attack = 'psychic';
        } else if (request[10] == 'Poison') {
        gif_attack = 'poison';
        } else if (request[10] == 'Normal') {
        gif_attack = 'normal';
        } else if (request[10] == 'Ice') {
        gif_attack = 'ice';
        } else if (request[10] == 'Grass') {
        gif_attack = 'grass';
        } else if (request[10] == 'Ground') {
        gif_attack = 'ground';
        } else if (request[10] == 'Ghost') {
        gif_attack = 'ghost';
        } else if (request[10] == 'Flying') {
        gif_attack = 'flying';
        } else if (request[10] == 'Fighting') {
        gif_attack = 'fighting';
        } else if (request[10] == 'Fairy') {
        gif_attack = 'fairy';
        } else if (request[10] == 'Dragon') {
        gif_attack = 'dragon';
        } else if (request[10] == 'Bug') {
        gif_attack = 'bug';
        } else if (request[10] == 'Rock') {
        gif_attack = 'rock';
        }
                                                                                             
        if (gif_attack != '_blank') {                                                                                       
            if (request[10] != 'Fire') {
                $('#gif_attack img').attr('src', '<?= $static_url?>/images/attacks/' + gif_attack + gif_sufixo + '.gif');
            }
        }
       if (request[11] == 1) {
          atk = request[2].split(' ').join('_');
          $('#zmove').hide().attr('src', 'public/images/zmoves/'+atk+'.png').fadeIn(1000);

          setTimeout(() => {
            $('#zmove').fadeOut(2000);
          }, 3000);
        }
       }

        leven_verandering(request[3],'pokemon',request[4]);
        $("#dame2").html(request[7]);

        let life = Math.round((request[3] / request[4]) * 100);

        $("div[id='change_pokemon'][name='"+request[6]+"']").html(request[5]+"<div class='hp_red' style='height: 3px; width: 86%;'><div class='progress' style='width: 100%'></div></div>");
  	    $("div[id='change_pokemon'][name='"+request[6]+"'] .progress").width(life + '%');
        $("div[id='change_pokemon'][name='"+request[6]+"']").attr("data-original-title", ""+request[5]+" \nHP: "+request[3]+"/"+request[4]+"");

        if (request[13] == '') {
          $("#pokemon_effect").css('display', 'none');
        } else {
          $("#pokemon_effect img")
            .attr('src', '<?=$static_url?>/images/effects/' + request[13] + '.png')
            .attr('alt', request[13])
            .attr('data-original-title', request[13]);
          $("#pokemon_effect").css('display', 'block');
        }
      }
      clearTimeout(opp_to_late)
      $("#message").html(request[1])
      attack = 1
      wissel = 1
      you_time_used = request[9]
      you_check_to_late()
    }
    //Opponent Has to Attack
    else if (request[0] == 3) {
      if (request[2] == "wissel") {
        do_wissel(request)
      } else {
        if (request[11] == 1) {
            atk = request[12].split(' ').join('_');
            $('#zmove').hide().attr('src', 'public/images/zmoves/'+atk+'.png').fadeIn(1000);

            setTimeout(() => {
              $('#zmove').fadeOut(2000);
            }, 3000);
        }
      }
        
      clearTimeout(opp_to_late)
      $("#message").html(request[1])
      attack = 0
      wissel = 0
      opp_time_used = request[9]
      opponent_check_to_late()
      setTimeout('last_move_check()', 1500)
    }
    //Player Has To Change
    else if (request[0] == 4) {
      clearTimeout(opp_to_late)
      
      if (request[11] == 1) {
          atk = request[12].split(' ').join('_');
          $('#zmove').hide().attr('src', 'public/images/zmoves/'+atk+'.png').fadeIn(1000);

          setTimeout(() => {
            $('#zmove').fadeOut(2000);
          }, 3000);
          
      }
      
      $("#message").html(request[1])
      leven_verandering(request[3],'pokemon',request[4])

      let life = Math.round((request[3] / request[4]) * 100);

      $("div[id='change_pokemon'][name='"+request[6]+"']").html(request[5]+"<div class='hp_red' style='height: 3px; width: 86%;'><div class='progress' style='width: 100%'></div></div>");
  	  $("div[id='change_pokemon'][name='"+request[6]+"'] .progress").width(life + '%');
      $("div[id='change_pokemon'][name='"+request[6]+"']").attr("data-original-title", ""+request[5]+" \nHP: "+request[3]+"/"+request[4]+"");
      
      attack = 0
      wissel = 1
      you_time_used = request[9]
      you_check_to_late()
    }
    //Opponent Was to Late
    else if (request[0] == 2) {
      clearTimeout(opp_to_late)
      $("#message").html(request[1])
      end = setTimeout("show_end_screen();", 5000)
      $("#time_left").html("0")
      attack = 0
      wissel = 0
    }
    //Player lost
    else if (request[0] == 5) {
      clearTimeout(opp_to_late)
      $("#message").html(request[1])
      $("#time_left").html("0")
      leven_verandering(request[3],'pokemon',request[4])
      end = setTimeout("show_end_screen();", 5000)
      attack = 0
      wissel = 0
    }
  });
}

function show_start_text(begin,your,opp,opp_life,you_link,opp_link,you_life,you_exp) {
  clearTimeout(start_text)    
  $("#img_you").attr("src","<?= $static_url?>/images/<?= $pokemon_info['map']; ?>/back/<?= $pokemon_info['wild_id']; ?>.gif")
  $("#img_opponent").attr("src","<?= $static_url?>/images/<?= $opponent_info['map']; ?>/<?= $opponent_info['wild_id'] ?>.gif")
  wlSound('cries/<?=$pokemon_info['wild_id']?>', <?=$gebruiker['volume']?>, false);
  setTimeout(function () { wlSound('cries/<?=$opponent_info['wild_id']?>', <?=$gebruiker['volume']?>, false); }, 1500);

  $("#opponent_life").width(opp_life+'%')
  $("#pokemon_life").width(you_life+'%')
  $("#pokemon_exp").width(you_exp+'%')

  $("#pokemon_naam").html("<?= $pokemon_info['naam_goed']; ?>")
  $("#pokemon_level").html("<?= $pokemon_info['level']; ?> <div id='hpresta' style='margin-top: -4px;margin-left: 80px;position: absolute;'><?= "".$pokemon_info['leven']."/".$pokemon_info['levenmax']."";?></div>")
  $("#opponent_naam").html("<?= $opponent_info['naam_goed']; ?>")
  $("#you_text").show()

  if (begin == you+"_begin") {
    $("#message").html("<?= $txt['you_first_attack']; ?>")
    attack = 1
    wissel = 1
    you_time_used = 0
    you_check_to_late()
  } else {
    $("#message").html(begin.replace('_begin', '') + " começa atacando.")
    attack = 0
    wissel = 0
    last_move_check()
    opp_time_used = 0
    opponent_check_to_late()
  }
}

function check_ready() {
  $.get("attack/duel/duel-check-ready.php?duel_id="+<?= $duel_info['id']; ?>+"&sid="+Math.random(), function(data) {
    request = data.split(" | ")
    if (request[0] == 0) {
      if (request[1] != "") $("#message").html(request[1])
      else ready_check = setTimeout("check_ready()", 1000)
    }
    else if (request[0] == 1) {
      clearTimeout(ready_check)
      start_text = setTimeout("show_start_text(\'"+request[1]+"\',\'"+request[2]+"\',\'"+request[3]+"\',\'"+request[4]+"\',\'"+request[5]+"\',\'"+request[6]+"\',\'"+request[7]+"\',\'"+request[8]+"\');", 5000)
    }
    else ready_check = setTimeout("check_ready()", 1000)
  });
}

function show_end_screen() {
  clearTimeout(end)
  $.get("attack/duel/duel-finish.php?duel_id="+<?= $duel_info['id']; ?>+"&sid="+Math.random(), function(data) {
    request = data.split(" | ")
    tab_wl('');
    document.getElementById('hit').style.display = "none";
    document.getElementById('hit2').style.display = "none";
    document.getElementById('dame').style.display = "none";
    document.getElementById('dame2').style.display = "none";
    if (request[0] == 1) {
      $("#message").html("Você venceu o duelo contra <?=$duel_info['opponent_name'];?>")
      if (request[1] > 0) $("#message").append(", ganhando "+request[1]+" Silvers.") 
      
      sound_base.stop();
      wlSound('player-victory', <?=$gebruiker['volume']?>, false);
    }
    else if (request[0] == 2) {
      $("#message").html("Você foi derrotado por <?=$duel_info['opponent_name'];?>")
      if (request[1] > 0) $("#message").append(", perdendo "+request[1]+" Silvers.") 
    }
    $("#pokemon_text").hide()
    $("#trainer_naam").html(request[3])
    //Set Images
    $("#img_you").attr("src","<?= $static_url?>/images/characters/<?= $gebruiker['character']; ?>/Thumb.png")
    $("#img_opponent").attr("src","<?= $static_url?>/images/characters/<?= $duel_info['opponent_sex']; ?>/Thumb.png")
    setTimeout("location.href='./home'", 7500)
  });
}

function attack_status_2(msg) {
  request = msg.split(" | ")
  document.getElementById('hit').style.display = "none";
  document.getElementById('hit2').style.display = "none";
  document.getElementById('dame2').style.display = "none";
  document.getElementById('dame').style.display = "none";
  $("#message").html(request[0])
  if (request[1] == 1) {
    setTimeout('last_move_check()', 1500)
    opp_time_used = 0
    opponent_check_to_late() 
    attack = 0
    wissel = 0
    if (request[3] == 0) exp_change(request[6],request[7])

    var opponent_life_procent = Math.round((request[3]/request[4])*100)
    $("#opponent_life").width(opponent_life_procent+'%')
  }
  else if (request[1] == 2) {
    exp_change(request[6],request[7])
    setTimeout("show_end_screen();", 5000)
  }
}

function attack_status(msg) {
  request = msg.split(" | ")
  var time = 250
  if (request[2] < 25) time = 2000
  else if (request[2] < 50) time = 3000
  else if (request[2] < 100) time = 4000
  else if (request[2] < 150) time = 5000
  else if (request[2] < 200) time = 6000
  else if (request[2] < 250) time = 7000
  else if (request[2] >= 250) time = 8000

      if (request[20] != '') {
       var gif_sufixo = '';
       var gif_attack = '_blank';
                                                                                             
       if (request[20] == 'Fire') {
       gif_attack = 'burn';
       } else if (request[20] == 'Water') {
       gif_attack = 'wave';
       } else if (request[20] == 'Electric') {
       gif_attack = 'electric';
       } else if (request[20] == 'Dark') {
       gif_attack = 'dark';
       } else if (request[20] == 'Steel') {
       gif_attack = 'steel';
       } else if (request[20] == 'Psychic') {
       gif_attack = 'psychic';
       } else if (request[20] == 'Poison') {
       gif_attack = 'poison';
       } else if (request[20] == 'Normal') {
       gif_attack = 'normal';
       } else if (request[20] == 'Ice') {
       gif_attack = 'ice';
       } else if (request[20] == 'Grass') {
       gif_attack = 'grass';
       } else if (request[20] == 'Ground') {
       gif_attack = 'ground';
       } else if (request[20] == 'Ghost') {
       gif_attack = 'ghost';
       } else if (request[20] == 'Flying') {
       gif_attack = 'flying';
       } else if (request[20] == 'Fighting') {
       gif_attack = 'fighting';
       } else if (request[20] == 'Fairy') {
       gif_attack = 'fairy';
       } else if (request[20] == 'Dragon') {
       gif_attack = 'dragon';
       } else if (request[20] == 'Bug') {
       gif_attack = 'bug';
       } else if (request[20] == 'Rock') {
       gif_attack = 'rock';
       }                                                                                     
       
       gif_sufixo = '_y';

       if (weather.indexOf(request[23]) != -1) {
          $('#weather').addClass('weather ' + request[23]);
        } else {
          $('#weather').removeClass();
        }
       
       if (gif_attack != '_blank') { 
           if (request[20] != 'Fire') {
            $('#gif_attack img').attr('src', '<?= $static_url?>/images/attacks/' + gif_attack + gif_sufixo + '.gif');
           }
       }

        var allow_anim = true;
          if (atk == 'Quick Attack' || atk == 'Fly' || atk == 'Plasma Fists') {
            $('#img_pokemon').wlAnimate('quick_atk');
          } else if (atk == 'Earthquake') {
            $('#weather').wlAnimate('shake');
          } else if (atk == 'Explode') {
            $('#img_you').wlAnimate('explode');
          } else if (atk == 'Recover' || atk == 'Roost') {
            allow_anim = false;
          }

          if (allow_anim) {
            $('#img_opponent').wlAnimate('shake');
          }
       
      }

        if (request[22] == '') {
          $("#opponent_effect").css('display', 'none');
        } else {
          $("#opponent_effect img")
            .attr('src', '<?=$static_url?>/images/effects/' + request[22] + '.png')
            .attr('alt', request[22])
            .attr('data-original-title', request[22]);
          $("#opponent_effect").css('display', 'block');
        }

        if (request[2] > 0) { 
          leven_verandering(request[3],'opponent',request[4]);
        }

        leven_verandering(request[24],'pokemon',request[15]);
        document.getElementById('dame').style.display = "";
        $("#dame").html(request[7]);
        attack_timer = setTimeout("attack_status_2('"+msg+"');", time)
}

//Change Pokemon Function
function change_pokemon_status(msg) {
  //Get php variables
  request = msg.split(" | ")
  //Send message
  $("#message").html(request[0])
  //Change was succesfull
  if (request[1] == 1) { 
    //Change Pokemon in fight name, level and attacks
    $("#pokemon_naam").html(request[2])
    $("#pokemon_level").html(""+request[3]+"<div id='hpresta' style='margin-top: -4px;margin-left: 80px;position: absolute;'></div>")
     document.getElementById("hpresta").innerHTML = ""+request[5]+" / "+request[6]+"";
    
    //Moves Buttons
		for (let i = 0; i < 4; i++) {
				let move = request[i + 9];
				let type = request[i + 17];
				let button = $("button:eq("+ i +")");

				if (move != '') {
					button.html(move);
					button.css('background-image', "url(<?=$static_url?>/images/attack/moves/" + type + ".png)");
					button.show();
				} else {
					button.hide();
				}
    }
    
    if (trainer_zmove == 0 && request[21] != false && request[22] != false) {
  			$('#use-zmove').show();
  			$('#use-zmove').html(request[21]);
  			$('#use-zmove').css('background-image', "url(<?=$static_url?>/images/attack/moves/" + request[22] + ".png)");
  	} else {
  			$('#use-zmove').hide();
  	}

    if (request[16] == '') {
			$("#pokemon_effect").css('display', 'none');
		} else {
			$("#pokemon_effect img")
				.attr('src', '<?=$static_url?>/images/effects/' + request[16] + '.png')
				.attr('alt', request[16])
				.attr('data-original-title', request[16]);
			$("#pokemon_effect").css('display', 'block');
    }

    //Create image for new pokemon in fight
    if (request[4] == 0) {
      var map = "pokemon"
      $("#pokemon_star").hide()
    } else{
      var map = "shiny"
      $("#pokemon_star").show() 
    }

    $("#img_you").attr("src","<?= $static_url?>/images/"+ map +"/back/" + request[15] + ".gif")
    wlSound('cries/'+request[15], <?=$gebruiker['volume']?>, false);
    //Show all pokemon in your hand 
    for (let i = 1; i < 7; i++) {
			let change = "div[id*='change_pokemon'][name*='"+ i +"']";
			let query  = $(change);

			if (query.attr('data-original-title') != '') {
				query.show();
			}				
		}
    //Hide the new pokemon that is in fight
    $("div[id*='change_pokemon'][name*='"+request[13]+"']").hide()
    //Change the HP Status from new pokemon in fight
    var pokemon_life_procent = Math.round((request[5]/request[6])*100)
    $("#pokemon_life").width(pokemon_life_procent+'%')
    //Change EXP Status from new pokemon in fight
    var exp_procent = Math.round((request[7]/request[8])*100)
    $("#pokemon_exp").width(exp_procent+'%')
    tab_wl('#atacar');
    //Opponent make next turn
    wissel = 0
    if (request[14] == you) {
      attack = 1
      you_time_used = 0;
      you_check_to_late()
    }
    else{
      attack = 0
      setTimeout('last_move_check()', 1500)
      opp_time_used = 0
      opponent_check_to_late()
    }
  }
}

//Player Can Do Stuff
$(document).ready(function() {
  //Player Do Attack
  $("button[id='aanval']").click(function() {
    if (attack == 1) {
      if ($(this).html() != "") {
        $("#message").html("<?= $txt['you_doet']; ?> "+$(this).html()+".")
        atk = $(this).html();
  			clearTimeout(you_to_late);
  			document.getElementById('hit').style.display = "";
        document.getElementById('hit2').style.display = "none";
        
			  $.ajax({
  			  type: "GET",
  			  url: "./attack/duel/duel-do_attack.php?attack_name="+$(this).html()+"&wie="+you+"&duel_id="+<?= $duel_info['id']; ?>+"&sid="+Math.random(),
  			  success: attack_status
  			}); 
  			attack = 0;
		  }
		}
  });

  $("button[id='use-zmove']").click(function() {
  		if (trainer_zmove == 0) {
        if (attack == 1) {
          if ($(this).html() != "") {
            $("#message").html("<?= $txt['you_doet']; ?> "+$(this).html()+".")
            atk = $(this).html();

            clearTimeout(you_to_late);
            document.getElementById('hit').style.display = "";
            document.getElementById('hit2').style.display = "none";
            
            $.ajax({
              type: "GET",
              url: "./attack/duel/duel-do_attack.php?attack_name="+$(this).html()+"&zmove=y&wie="+you+"&duel_id="+<?= $duel_info['id']; ?>+"&sid="+Math.random(),
              success: attack_status
            }).done(function() {
  						atk = atk.split(' ').join('_');
              $('#zmove').hide().attr('src', 'public/images/zmoves/' + atk + '.png').fadeIn(1000);
              setTimeout(() => {
                $('#zmove').fadeOut(2000);
              }, 3000);
  						$('#use-zmove').hide('slow', function() {
  						  $('#use-zmove').remove();
              });
              trainer_zmove = 1;
            });
            attack = 0;
          }
        }
  		}
    });
    
  //Player Make Change Pokemon
  $("div[id='change_pokemon']").click(function() {
    if (wissel == 1) {
      if (($(this).attr("name") != "") && (($(this).attr("data-original-title")) != "Egg") && (($(this).attr("data-original-title")) != "")) {
        clearTimeout(you_to_late)
        document.getElementById('hit').style.display = "none";
	      document.getElementById('hit2').style.display = "none";
        $.ajax({
          type: "GET",
          url: "./attack/duel/duel-change-pokemon.php?opzak_nummer="+$(this).attr("name")+"&wie="+you+"&duel_id="+<?= $duel_info['id']; ?>+"&sid="+Math.random(),
          success: change_pokemon_status
        }); 
      }
    }
  });
});

</script>
 <?php
   echo "<div class='box-content'><h3 class='title'>DUELO CONTRA ".strtoupper($duel_info['opponent_name'])."! RESTAM: (<span id='time_left'></span>) SEGUNDOS!</h3><div id='gif_attack' style='position: absolute;float: right;margin-left: 165px;margin-top: 70px;z-index: 0;width: 700px;'><img src='".$static_url."/images/attacks/_blank.gif' style='float: left;width: 700px;height:323px'/></div><div id='weather'><img id='zmove'><table class='".$gebruiker['background']."'>";
   ?>
  <tr><td>
			<div style="padding:0px 0 100px 0px;"><div class="new_bar2">
			<div style='padding: 15px 0 0 120px;'><strong><font size='3' style='text-shadow:1px 1px 1px #fff;'><i><img src='<?= $static_url?>/images/lvl.png' style='padding:0 0 0 30px;'> ?? </i></strong></font></div><div style="padding:0px 0 0 43px;"><div class="hp_red"><div class="progress" id="opponent_life" style="width: <?= $opponent_life_procent; ?>%"></div>
			</div><div id="opponent_effect" style="margin: -10px 2px 0px 151px; display: <?= $opponent_info['effect'] ? "block" : "none" ?>;"><img src="<?=$static_url?>/images/effects/<?= $opponent_info['effect'] ? $opponent_info['effect'] : 'none' ?>.png" alt="<?= $opponent_info['effect'] ?>" title="<?= $opponent_info['effect'] ?>"/></div></div>
			<div align="left" style="padding: 5px 0px 0px 10px;"><font style="text-shadow:1px 1px 1px #fff;" size="3">
		  Batalhando contra <strong><span id="opponent_naam"><?= $opponent_info['naam_goed']; ?></span></strong><span id='opponent_star' style='display: <?= $opponent_info['star']; ?> ;'></span><br> <?php
        		  $opponent_pok = DB::exQuery("SELECT psg.id, psg.leven FROM gebruikers AS g INNER JOIN pokemon_speler_gevecht AS psg ON g.user_id = psg.user_id INNER JOIN pokemon_speler AS ps ON psg.id = ps.id WHERE g.username='".$duel_info['opponent_name']."' AND psg.duel_id='".$duel_info['id']."' ORDER BY ps.opzak_nummer");
              while($opponent_pokemon = $opponent_pok->fetch_assoc()) {
                if ($opponent_pokemon['leven'] > 0) echo '<img id="opponent_hand_'.$opponent_pokemon['id'].'" src="'.$static_url.'/images/icons/pokeball.gif" width="14" height="14" alt="Disposto" title="Disposto" />';
                else echo '<img id="opponent_hand_'.$opponent_pokemon['id'].'" src="'.$static_url.'/images/icons/pokeball_black.gif" width="14" height="14" "Derrotado" title="Derrotado" />';
              }
        ?></font>		
			</div>		
				</div>
			
			</div>
			</td>
			<td>
			<div align="center" id="dame" style="display:none;"></div>
			<div class="infront" align="center" id="hit" style="display:none;"><img src="<?= $static_url?>/images/hit.png"/></div>
                <img id="img_opponent" src="<?= $static_url?>/images/<?= $opponent_info['map']."/".$opponent_info['wild_id']; ?>.gif" style="margin: 100px 0px 0px 60%;"/>
            </td></tr>
			<tr>
			<td>
			<div align="center" id="dame2" style="display:none;"></div>
			<div class="inback" align="center" id="hit2" style="display:none;"><img src="<?= $static_url?>/images/hit.png"/></div>
                <img id="img_you" src="<?= $static_url?>/images/<?= $pokemon_info['map']; ?>/back/<?= $pokemon_info['wild_id']; ?>.gif" style="margin: 40px 0 0 50%;"/>
            </td>
			<td>
				<div style="padding:100px 0 0 150px;"><div class="new_bar" style="float: right;">
				<div style="padding:16px 0 0px 10px;"><strong><font size="3" style="text-shadow:1px 1px 1px #fff;"><span id="pokemon_naam" style="float:left;"><?= $pokemon_info['naam_goed']; ?></span></strong></font> <span id="pokemon_star" style="display:<?= $pokemon_info['star']; ?>;"></div>
				<strong><font size="3" style="text-shadow:1px 1px 1px #fff;"><i><img src="<?= $static_url?>/images/lvl.png" style="padding:0 0 0 30px;"><span id="pokemon_level" style="padding:0px 0 0px 5px;"><?= $pokemon_info['level']; ?> <div id="hpresta" style="margin-top: -4px;margin-left: 80px;position: absolute;"><?= "".$pokemon_info['leven']."/".$pokemon_info['levenmax']."";?></div></span></i></strong></font>
				<div style="padding:0px 0 15px 43px;"><div class="hp_red">
				<div class="progress" id="pokemon_life" style="width: <?= $pokemon_life_procent; ?>%"></div>
				</div>
        <div id="pokemon_effect" style="margin: -10px 2px 0px 151px; display: <?= $pokemon_info['effect'] ? "block" : "none" ?>;"><img src="<?=$static_url?>/images/effects/<?= $pokemon_info['effect'] ? $pokemon_info['effect'] : 'none' ?>.png" alt="<?= $pokemon_info['effect'] ?>" title="<?= $pokemon_info['effect'] ?>"/> </div></div>
				<div style="padding:0px 0 0px 70px;"><div class="exp_blue">
				<div class="progress" id="pokemon_exp" style="width: <?= $pokemon_exp_procent; ?>%"></div>
				</div></div>
				</div></div>
			</td>
			</tr>
      <tr>
        <td style="width: 100%; background: url(public/images/layout/battle/action-content.png) no-repeat; padding: 10px 0; background-size: 100% 100%; height: 74px; z-index: 1010" colspan="2">
            <center>
            <div id="atacar" data-tabs-wl>
											<?php 
												for ($i = 0; $i < 4; $i++) { 
													$move = $pokemon_info['aanval_'.($i+1)];
													$style = '';

                          if (empty($move)) $style = 'display: none;';
                          if ($i % 2 == 0) $style .= 'float: left';
													else $style .= 'float: right';
											?>
                          <button id="aanval" style="background: url(<?=$static_url?>/images/attack/moves/<?=atk($pokemon_info['aanval_'.($i+1)], $pokemon_info)['soort']?>.png) no-repeat; <?=$style?>" class="btn-type"><?=$move; ?></button>
                      <?php
                        }

                        if (zMoves::valid($pokemon_info)[0] && $duel_info['zmove_'.str_replace('_klaar', '', $duel_info['you_duel'])] == 0) {
                          $zmove = zMoves::move($pokemon_info)[0];
                          echo '<br><button class="zmove btn-type" style="background: url('.$static_url.'/images/attack/moves/'.atk($zmove, $pokemon_info)['soort'].'.png)" id="use-zmove">'.$zmove.'</button>';
                        } else {
                          echo '<br><button class="zmove btn-type" style="display: none" id="use-zmove"></button>';
                        }
                      ?>
            </div>
            <div id="pokemon" style="margin-bottom: -27px" data-tabs-wl>
									<div id="change_pokemon" name="1" title="" style="display:none; background-image: url();" class="battle-pokemon">
										<div class='hp_red' style='height: 3px; width: 86%;'><div class="progress" style="width: 100%"></div></div>
									</div>
									<div id="change_pokemon" name="2" title="" style="display:none; background-image: url();" class="battle-pokemon">
										<div class='hp_red' style='height: 3px; width: 86%;'><div class="progress" style="width: 100%"></div></div>
									</div>
									<div id="change_pokemon" name="3" title="" style="display:none; background-image: url();" class="battle-pokemon">
										<div class='hp_red' style='height: 3px; width: 86%;'><div class="progress" style="width: 100%"></div></div>
									</div>
									<div id="change_pokemon" name="4" title="" style="display:none; background-image: url();" class="battle-pokemon">
											<div class='hp_red' style='height: 3px; width: 86%;'><div class="progress" style="width: 100%"></div></div>
									</div>
									<div id="change_pokemon" name="5" title="" style="display:none; background-image: url();" class="battle-pokemon">
											<div class='hp_red' style='height: 3px; width: 86%;'><div class="progress" style="width: 100%"></div></div>
									</div>
									<div id="change_pokemon" name="6" title="" style="display:none; background-image: url();" class="battle-pokemon">
											<div class='hp_red' style='height: 3px; width: 86%;'><div class="progress" style="width: 100%"></div></div>
									</div>
						</div>
            </center>
        </td>
      </tr>
	</table></div>

   <div class="text-box" style="margin-top: 7px">
      <table style="width: 100%">
          <tbody>
            <tr style="height: 150px">
							<td style="width: 41%">
								<div style="text-align: center;">
									<div onclick="tab_wl('#atacar'); $('#potion_screen').hide();" class="selector attack"></div>
									<div class="selector bag blocked"></div>
									<div onclick="tab_wl('#pokemon'); $('#potion_screen').hide();" class="selector pokemon"></div>
									<div class="selector run blocked"></div>
								</div>
              </td>
              <td style="width: 53%; background: url(public/images/layout/battle/text-content.png) no-repeat; background-size: 100% 100%">
                <div style="width: 99%" id="message" align="center"></div> 
              </td>
            </tr>
        </tbody>
      </table>
    </div>  
</div>

<?php
//Page Completly loaded, Player Ready
DB::exQuery("UPDATE `duel` SET `".$duel_info['you_duel']."`='1' WHERE `id`='".$duel_info['id']."'");
?>