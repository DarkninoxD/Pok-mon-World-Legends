<?php 
DB::exQuery("UPDATE `gebruikers` SET `pagina`='attack' WHERE `user_id`='".$_SESSION['id']."'");

#Load Safety Script
include("app/includes/resources/security.php");

#Include Attack Functions
include("attack/attack.inc.php");

$aanval_log = aanval_log($_SESSION['attack']['aanval_log_id']);

#Player in log is diffirent then loggedin
if ($aanval_log['user_id'] != $_SESSION['id'] || !isset($_SESSION['sec_key'])) {
  #End Attack
  remove_attack($aanval_log['id']);
  #Send back to home
  header("Location: ../../home");
  unset($_SESSION['attack']['duel_id']);
} else {
  #Load All Openent Info
  $computer_info = computer_data($aanval_log['tegenstanderid']);
  #Make all letters small
  $computer_info['naam_klein'] = strtolower($computer_info['naam']);
  #Change name for male and female
  $computer_info['naam_goed'] = computer_naam($computer_info['naam']);
  #Check if Player Has a Pokedex Chip
  if (($gebruiker['Pokedex chip'] == 1) AND ($gebruiker['Pokedex'] == 1)) $computer_info['level'] = $computer_info['level'];
  else $computer_info['level'] = "??";
  #Shiny
  if ($computer_info['shiny'] == 1) {
    $computer_info['map'] = "shiny";
    $computer_info['star'] = "block";
  } else {
    $computer_info['map'] = "pokemon";
    $computer_info['star'] = "none";
  }
  
  #Calculate Life in Procent for Computer         
  if ($computer_info['leven'] != 0) $computer_life_procent = round(($computer_info['leven']/$computer_info['levenmax'])*100);
  else $computer_life_procent = 0;
  
  #Load All Pokemon Info
  $pokemon_info = pokemon_data($aanval_log['pokemonid']);
  $pokemon_info['naam_klein'] = strtolower($pokemon_info['naam']);
  $pokemon_info['naam_goed'] = addslashes(pokemon_naam($pokemon_info['naam'],$pokemon_info['roepnaam'], $pokemon_info['icon']));
  
  #Calculate Life in Procent for Pokemon         
  if ($pokemon_info['levenmax'] != 0) $pokemon_life_procent = round(($pokemon_info['leven']/$pokemon_info['levenmax'])*100);
  else $pokemon_life_procent = 0;
  
  #Calculate Exp in procent for pokemon
  if ($pokemon_info['expnodig'] == 0) $pokemon_info['expnodig'] =1;
  if ($pokemon_info['expnodig'] != 0) $pokemon_exp_procent = round(($pokemon_info['exp']/$pokemon_info['expnodig'])*100);
  else $pokemon_exp_procent = 0;
  
  #Shiny
  $pokemon_info['map'] = "pokemon";
  $pokemon_info['star'] = "none";
  if ($pokemon_info['shiny'] == 1) {
    $pokemon_info['map'] = "shiny";
    $pokemon_info['star'] = "block";
  }

  #Player Pokemon In Hand
  
  for($inhand = 1; $player_hand = $pokemon_sql->fetch_assoc(); $inhand++) {
    #Check Wich Pokemon is infight
    if ($player_hand['id'] == $pokemon_info['id']) $infight = 1;
    else $infight = 0;
    if ($player_hand['ei'] == 1) $player_hand['naam'] = "??";
	if ($player_hand['ei'] == 1) $player_hand['wild_id'] = "??";
	
	if ($player_hand['ei'] != 1) $player_hand['naam'] = addslashes(pokemon_naam($player_hand['naam'], $player_hand['roepnaam'], $player_hand['icon']));

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
  $pokemon_sql->data_seek(0); 
  ?>
  <script type="text/javascript" src="./attack/javascript/attack.js"></script>
  <script type="text/javascript" src="./attack/javascript/animation.js"></script>
  <script>
  var vol = (<?=$gebruiker['volume']?>-3)/100;
  if (vol < 0) vol = 0;
  var sound_base = new Howl({
      src: ['public/sounds/wild.mp3'],
      autoplay: true,
      loop: true,
      html5: true,
      volume: vol
    });
    
    sound_base.play();
    
  var speler_attack; var timer; var next_turn_timer; var attack_timer = 0; var speler_wissel;
  var atk = ''; var trainer_zmove = <?=$aanval_log['zmove']?>; var pokeball_lock = true;
  
  <?php
        if (empty($_SESSION['map_live'])) {
      ?>
        var redirect = "location.href='./attack/<?= $gebruiker['voltaredirect']; ?>&map=<?= $gebruiker['map']; ?>'";
      <?php
        } else {
            $m = $_SESSION['map_live'];
            $_SESSION['map_live'] = '';
       ?>
       var redirect = "location.href='./safari&map=<?=$m?>'";
       <?php
        }
      ?>

  function show_end_screen(text) {
    $.get("attack/wild/wild-finish.php?aanval_log_id="+<?= $aanval_log['id']; ?>+"&_h=" + <?=$gebruiker['sec_key'];?> + "&sid="+Math.random(), function(data) {
      request = data.split(" | ");
      document.getElementById('hit').style.display = "none";
	    document.getElementById('hit2').style.display = "none";
      if (request[0] == 1) $("#message").html("<?= $txt['you_won']; ?> "+text);
      else if (request[0] == 0) {
        $("#message").html("<?= $txt['you_lost']; ?> ");
        if (request[1] > 0) $("#message").append("<?= $txt['you_lost_1']; ?>"+request[1]+" Silvers. ");
        $("#message").append("<?= $txt['you_lost_2']; ?>");
      }
      
      <?php 
        if (!empty($evento_atual)){
        if ($evento_atual['category'] == 'drop') {
      ?>
            if (request[2] != 0) {
                let msg = request[2].split(',');
                $("#message").append("<p style='margin:0'>Você conseguiu <b>"+msg[1]+"x</b> <img src='<?=$static_url?>/images/layout/events/drops/"+msg[0]+".png' title='"+msg[0]+"' style='width: 16px'/>!</p>");
            }
	<?php
		} else if ($evento_atual['name_id'] == 'pikachu_festival') {
	?>
		if (request[2] != 0) {
			let msg = request[2].split(',');
			$("#message").append("<p style='margin:0'>Você conseguiu <b>"+msg[1]+"x</b> <img src='<?=$static_url?>/images/poke_icons/pikachu_festival.png' title='"+msg[0]+"' style='width: 16px'/>!</p>");
		}
	<?php
		}
		}
      ?>
      
      
      setTimeout(redirect, 4000);
    });
  }
  
  wlSound('cries/<?=$pokemon_info['wild_id']?>', <?=$gebruiker['volume']?>, false);
  setTimeout(function () { wlSound('cries/<?=$computer_info['wild_id']?>', <?=$gebruiker['volume']?>, false); }, 1500);

  var weather = [
    'harsh_sunlight',
    'extremely_harsh_sunlight',
    'rain',
    'heavy_rain',
    'sandstorm',
    'hail',
    'mysterious_air_current'
  ];


  //If div is ready
  $("#message").ready(function() {
    //Write Start Text
    if ("<?= $aanval_log['laatste_aanval']; ?>" == "spelereersteaanval") {
      speler_attack = 1
      speler_wissel = 1
      $("#message").html("<?= $txt['you_first_attack']; ?>")
    }
    else if ("<?= $aanval_log['laatste_aanval']; ?>" == "computereersteaanval") {
      speler_attack = 0
      speler_wissel = 0
      $("#message").html("<?= $computer_info['naam_goed'].' '.$txt['opponent_first_attack']; ?>")
      setTimeout('next_turn()', 2000)
    }
    else if ("<?= $aanval_log['laatste_aanval']; ?>" == "pokemon") {
      speler_attack = 0
      next_turn()
      $("#message").html("<?= $computer_info['naam_goed'].' '.$txt['opponents_turn']; ?>")
    }
    else if ("<?= $aanval_log['laatste_aanval']; ?>" == "computer") {
      speler_attack = 1
      $("#message").html("<?= $txt['your_turn']; ?>")
    }
    else if ("<?= $aanval_log['laatste_aanval']; ?>" == "wissel") {
      speler_attack = 0
      speler_wissel = 1
      $("#message").html("<?= $pokemon_info['naam_goed']; ?> não pode entrar na batalha.")
    }
    else if ("<?= $aanval_log['laatste_aanval']; ?>" == "end_screen") {
      speler_attack = 0
      speler_wissel = 0
      show_end_screen("<?= $txt['next_time_wait']; ?>")
    }
    else if ("<?= $aanval_log['laatste_aanval']; ?>" == "klaar") {
      speler_attack = 1
      $("#message").html("<?= $txt['fight_finished']; ?>")
      setTimeout(redirect, 3000)
    }
    else if ("<?= $aanval_log['laatste_aanval']; ?>" == "gevongen") {
      speler_attack = 1
      $("#message").html("<?= $txt['success_catched_1'].' '.$computer_info['naam_goed'].' '.$txt['success_catched_2']; ?>")
      setTimeout(redirect, 2000)
    }
    else $("#message").html("Error: 0001 \n Atualize a página \n Info:<?= $aanval_log['laatste_aanval']; ?>");

    if (weather.indexOf('<?=$aanval_log['weather']?>') != -1) {
      	$('#weather').addClass('weather <?=$aanval_log['weather']?>');
    } else {
        $('#weather').removeClass();
    }
  });      

  //Change attack status
  function attack_status(msg) {
  	request = msg.split(" | ")
  	var time = 250
  	if (request[7] < 25) time = 750
  	else if (request[7] < 50) time = 800
  	else if (request[7] < 100) time = 900
  	else if (request[7] < 150) time = 1000
  	else if (request[7] < 200) time = 1200
  	else if (request[7] < 250) time = 1500
  	else if (request[7] >= 250) time = 1800

  	if (weather.indexOf(request[23]) != -1) {
  		$('#weather').addClass('weather ' + request[23]);
  	} else {
  		$('#weather').removeClass();
  	}

  	setTimeout(() => {
  		$('#zmove').fadeOut(2000);
  	}, 3000);

  	if (request[19] != '') {
  		var gif_sufixo = '';
  		var gif_attack = '_blank';

  		if (request[19] == 'Fire') {
  			gif_attack = 'burn';
  		} else if (request[19] == 'Water') {
  			gif_attack = 'wave';
  		} else if (request[19] == 'Electric') {
  			gif_attack = 'electric';
  		} else if (request[19] == 'Dark') {
  			gif_attack = 'dark';
  		} else if (request[19] == 'Steel') {
  			gif_attack = 'steel';
  		} else if (request[19] == 'Psychic') {
  			gif_attack = 'psychic';
  		} else if (request[19] == 'Poison') {
  			gif_attack = 'poison';
  		} else if (request[19] == 'Normal') {
  			gif_attack = 'normal';
  		} else if (request[19] == 'Ice') {
  			gif_attack = 'ice';
  		} else if (request[19] == 'Grass') {
  			gif_attack = 'grass';
  		} else if (request[19] == 'Ground') {
  			gif_attack = 'ground';
  		} else if (request[19] == 'Ghost') {
  			gif_attack = 'ghost';
  		} else if (request[19] == 'Flying') {
  			gif_attack = 'flying';
  		} else if (request[19] == 'Fighting') {
  			gif_attack = 'fighting';
  		} else if (request[19] == 'Fairy') {
  			gif_attack = 'fairy';
  		} else if (request[19] == 'Dragon') {
  			gif_attack = 'dragon';
  		} else if (request[19] == 'Bug') {
  			gif_attack = 'bug';
  		} else if (request[19] == 'Rock') {
  			gif_attack = 'rock';
  		}

  		if (gif_attack != '_blank' && request[4] == 'computer') {
  			gif_sufixo = '_y';
  		}
  		
  		if (request[19] != 'Fire') {
      		if (atk == 'Recover' || atk == 'Roost' && request[4] == 'computer') {
          		$('#gif_attack img').attr('src', '<?= $static_url?>/images/attacks/' + gif_attack + '.gif');
          	} else {
          		$('#gif_attack img').attr('src', '<?=$static_url?>/images/attacks/' + gif_attack + gif_sufixo + '.gif');
          	}
  		}
  	}

  	var allow_anim = true;
  	if (request[4] == 'computer') {
  		if (atk == 'Quick Attack' || atk == 'Fly') {
  			$('#img_pokemon').wlAnimate('quick_atk');
  		} else if (atk == 'Earthquake') {
  			$('#weather').wlAnimate('shake');
  		} else if (atk == 'Explode') {
  			$('#img_pokemon').wlAnimate('explode');
  		} else if (atk == 'Recover' || atk == 'Roost') {
  			allow_anim = false;
  		}

  		if (allow_anim) {
  			$('#img_pokemon_en').wlAnimate('shake');
  		}
  	}

  	if (request[4] == "computer") {
  		leven_verandering2(request[18], 'pokemon', request[15])
  		leven_verandering(request[2], 'computer', request[3])
  		document.getElementById('dame').style.display = "";
  		$("#dame").html(request[7]);

  		if (request[18] == 0) {
  			document.getElementById("hpresta").innerHTML = "";
  			speler_wissel = 1
  		} else {
  			document.getElementById("hpresta").innerHTML = "" + request[18] + "/" + request[15] + "";
  		}
  	} else {

  		leven_verandering2(request[18], 'computer', request[15])
  		leven_verandering(request[2], 'pokemon', request[3])
  		document.getElementById('dame2').style.display = "";
  		$("#dame2").html(request[7]);

  	}

  	if (request[5] == 1) leven_verandering(request[2], request[4], request[3])
  	attack_timer = setTimeout("attack_status_2('" + msg + "');", time)
  }


  function attack_status_2(msg) {
  	clearTimeout(attack_timer)
  	document.getElementById('hit').style.display = "none";
  	document.getElementById('hit2').style.display = "none";
  	document.getElementById('dame2').style.display = "none";
  	document.getElementById('dame').style.display = "none";
  	request = msg.split(" | ")

  	$("#message").html(request[0])

  	if (request[20] == '') {
  		$("#pokemon_effect").css('display', 'none');
  	} else {
  		$("#pokemon_effect img")
  			.attr('src', '<?=$static_url?>/images/effects/' + request[20] + '.png')
  			.attr('alt', request[20])
  			.attr('data-original-title', request[20]);
  		$("#pokemon_effect").css('display', 'block');
  	}

  	if (request[21] == '') {
  		$("#computer_effect").css('display', 'none');
  	} else {
  		$("#computer_effect img")
  			.attr('src', '<?=$static_url?>/images/effects/' + request[21] + '.png')
  			.attr('alt', request[21])
  			.attr('data-original-title', request[21]);
  		$("#computer_effect").css('display', 'block');
  	}


  	if (request[4] == "pokemon") {
  		life_procent = Math.round((request[2] / request[3]) * 100)
  		$("#" + request[8] + "_life").width(life_procent + '%')
  		$("#" + request[8] + "_leven").html(request[2])
  		$("#" + request[8] + "_leven_max").html(request[3])

  		$("div[id='change_pokemon'][name='" + request[9] + "']").html("<?= $pokemon_info['naam']; ?> <div class='hp_red' style='height: 3px; width: 86%;'><div class='progress' style='width: 100%'></div></div>");
  		$("div[id='change_pokemon'][name='" + request[9] + "'] .progress").width(life_procent + '%');
  		$("div[id='change_pokemon'][name='" + request[9] + "']").attr("data-original-title", "<?= $pokemon_info['naam']; ?> \nHP:" + request[2] + "/" + request[3] + "");
  		document.getElementById("hpresta").innerHTML = "" + request[2] + "/" + request[3] + "";
  	}

  	if (request[22] != '0') {
  		request_transform = request[22].split(",");

  		if (request[16] == "pokemon") {
  			document.getElementById("hpresta").innerHTML = "" + request[2] + "/" + request[3] + "";
  			leven_verandering(request[2], 'pokemon', request[3])
  			$("button:eq(0)").html(request_transform[2]);
  			$("button:eq(1)").html(request_transform[3]);
  			$("button:eq(2)").html(request_transform[4]);
  			$("button:eq(3)").html(request_transform[5]);

  			//Create image for new pokemon in fight
  			if (request_transform[5] == 1) {
  				var map = "shiny";
  			} else {
  				var map = "pokemon";
  			}
  			$("#img_pokemon").attr("src", "<?=$static_url?>/images/" + map + "/back/" + request_transform[0] + ".gif");
  		} else {
  			leven_verandering(request[2], 'computer', request[3])
  			//Create image for new pokemon in fight
  			if (request_transform[5] == 1) {
  				var map = "shiny";
  			} else {
  				var map = "pokemon";
  			}
  			$("#img_computer").attr("src", "<?=$static_url?>/images/" + map + "/" + request_transform[0] + ".gif");
  		}
  	}

  	if (request[4] == "pokemon") {
  		if (request[2] <= 0) speler_wissel = 1
  		else {
  			speler_attack = 1
  			speler_wissel = 1
  		}
  	} else {
  		speler_attack = 0
  		speler_wissel = 0
  	}

  	//Computer make next turn
  	if (request[1] == 1) next_turn()
  	else if (request[6] == 1) {
  		setTimeout("exp_change(" + request[11] + "," + request[12] + ");", 4000)
  		setTimeout("show_end_screen('" + request[10] + "');", 4000)
  		speler_attack = 0
  		speler_wissel = 0
  	}

  	if (request[18] == 0) {
  		speler_wissel = 1
  	}
  }

  //Change Pokemon Function
  function change_pokemon_status(msg) {
  	//Get php variables
  	request = msg.split(" | ")
  	//Send message
  	$("#message").html(request[0])
  	//Stop Life Change 
  	clearTimeout(timer);
  	//Change was succesfull
  	if (request[1] == 1) {
  		//Change Pokemon in fight name, level and attacks
  		$("#hpresta").show()
  		$("#pokemon_naam").html(request[3])
  		$("#pokemon_level").html("" + request[4] + "<div id='hpresta' style='margin-top: -4px;margin-left: 80px;position: absolute;'></div>")
  		document.getElementById("hpresta").innerHTML = "" + request[10] + "/" + request[11] + "";

			//Moves Buttons
			for (let i = 0; i < 4; i++) {
				let move = request[i + 5];
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
  		if (request[14] == 1) {
  			var map = "shiny"
  			$("#pokemon_star").show()
  		} else {
  			var map = "pokemon"
  			$("#pokemon_star").hide()
  		}
			
  		$("#img_pokemon").attr("src", "<?=$static_url?>/images/" + map + "/back/" + request[15] + ".gif");
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
  		$("div[id*='change_pokemon'][name*='" + request[9] + "']").hide()
  		//Change the HP Status from new pokemon in fight
  		var pokemon_life_procent = Math.round((request[10] / request[11]) * 100)
  		$("#pokemon_life").width(pokemon_life_procent + '%')
  		//Change EXP Status from new pokemon in fight
  		var exp_procent = Math.round((request[12] / request[13]) * 100)
  		$("#pokemon_exp").width(exp_procent + '%')
			tab_wl('#atacar');
  		//Computer make next turn
  		if (request[2] == 1) {
  			speler_attack = 0
  			speler_wissel = 0
  			next_turn();
  		} else {
  			speler_attack = 1
  			speler_wissel = 0
  		}
  	}
  }

  //Use item function
  function use_item_status(msg) {
  	//Get php variables
  	request = msg.split(" | ")
  	//Send message
  	$("#message").html(request[0])
    //change amount of item
    var option = $("div[data-item-name='"+request[4]+"'] .qtd")
  	//Set New Amount
  	var amount = request[2]
  	//It was a potion
  	if (request[5] == "Potion") {
      let lock = false;
      if (request[1] == 0) lock = true;

      if (!lock) {
        //The pokemon in fight life has to change
        if (request[8] == 1) leven_verandering(request[6], 'pokemon', request[7])
        //Potion screen has to go away
        $("#potion_screen").hide()
        //Calculate new life for pokemon
        var green = Math.round(request[6] / request[7] * 100);
        //Set new life for potion screen
        $("#" + request[11] + "_green").width(green + 'px')
        $("#" + request[11] + "_red").width(100 - green + 'px')
        $("#" + request[11] + "_leven").html(request[6])
        if (request[8] == 1) document.getElementById("hpresta").innerHTML = "" + request[6] + "/" + request[7] + "";
        //Change pokemon change field title

        $("div[id='change_pokemon'][name='" + request[9] + "']").html(request[10] + "<div class='hp_red' style='height: 3px; width: 86%;'><div class='progress' style='width: 100%'></div></div>");
        $("div[id='change_pokemon'][name='" + request[9] + "'] .progress").width(green + '%');
        $("div[id='change_pokemon'][name='" + request[9] + "']").attr("data-original-title", request[10] + "\nHP: " + request[6] + "/" + request[7] + "");

        //If Amount is smaller than 1, amount -> 0
        if (request[2] < 1) {
          option.parent().parent().remove();
          $('.items-carousel').width('99%');
        } else {
          option.html(amount)
        }
      }
  		//Computer make next turn
  		if (request[1] == 1) {
  			speler_attack = 0
  			speler_wissel = 0
  			next_turn()
  		}
  	} else if (request[5] == "Pokeball") {
      //If Amount is smaller than 1, amount -> 0
      if (request[2] < 1) {
        option.parent().parent().remove();
        $('.items-carousel').width('99%');
      } else {
        option.html(amount)
      }

  		speler_attack = 0
  		speler_wissel = 0
  		//Computer make next turn
  		if (request[1] == 1) {
  		    if (pokeball_lock) {
  			    $('#img_pokemon_en').wlAnimate('not-catch', '<?=$static_url?>/images/items/' + request[4] + '.png');
  			    pokeball_lock = false;
  		    }
  			next_turn();
  		}
  		//Attack finished
  		else {
			<?php
				if (!empty($evento_atual)) {
					if ($evento_atual['name_id'] == 'pikachu_festival') {
			?>
					if (request[6] != 0) {
						let msg = request[6].split(',');
						$("#message").append("<p style='margin:0'>Você conseguiu <b>"+msg[1]+"x</b> <img src='<?=$static_url?>/images/poke_icons/pikachu_festival.png' title='"+msg[0]+"' style='width: 16px'/>!</p>");
					}
			<?php
					}
				}	
			?>
  			$('#img_pokemon_en').wlAnimate('catch', '<?=$static_url?>/images/items/' + request[4] + '.png');
  			wlSound('caught', <?=$gebruiker['volume']?>, false);
  			sound_base.stop();
  			setTimeout(redirect, 4000);
  		}
  	}
  }

  //Try To Run Function
  function attack_run_status(msg) {
  	//Get php variables
  	request = msg.split(" | ")
  	//Send message	
  	$("#message").html(request[0])
  	if (request[1] == 1) setTimeout(redirect, 3000)
  	//Computer make next turn
  	if (request[1] == 0) {
  		speler_attack = 0
  		speler_wissel = 0
  		next_turn()
  	}
  }

  //Make Computer Do Attack
  function next_turn() {
  	clearTimeout(next_turn_timer)
  	next_turn_timer = setTimeout('computer_attack()', 3000)
  }

  //Player Can Do Stuff
  $(document).ready(function() {
  	//Player Do Attack
  	$("button[id='aanval']").click(function() {
  		if (speler_attack == 1) {
  			if ($(this).html() != "") {
  				speler_attack = 0
  				document.getElementById('hit2').style.display = "none";
  				$("#message").html($("#pokemon_naam").html() + " <?= $txt['did']; ?> " + $(this).html() + ".");
  				$("#potion_screen").hide();
  				atk = $(this).html();
  				document.getElementById('hit').style.display = "";
  				$.ajax({
  					type: "GET",
  					url: "attack/wild/wild-do_attack.php?attack_name=" + $(this).html() + "&wie=pokemon&aanval_log_id=" + <?= $aanval_log['id']; ?> + "&_h=" + <?=$gebruiker['sec_key'];?> + "&sid=" + Math.random(),
  					success: attack_status
  				});
  			}
  		}
  	});

  	$("button[id='use-zmove']").click(function() {
  		if (trainer_zmove == 0) {
  			if (speler_attack == 1) {
  				if ($(this).html() != "") {
  					speler_attack = 0
  					document.getElementById('hit2').style.display = "none";
  					$("#message").html($("#pokemon_naam").html() + " <?= $txt['did']; ?> " + $(this).html() + ".");
  					$("#potion_screen").hide();
  					atk = $(this).html();
  					document.getElementById('hit').style.display = "";
  					$.ajax({
  						type: "GET",
  						url: "attack/wild/wild-do_attack.php?attack_name=" + $(this).html() + "&zmove=y&wie=pokemon&aanval_log_id=" + <?= $aanval_log['id']; ?> + "&_h=" + <?=$gebruiker['sec_key'];?> + "&sid=" + Math.random(),
  						success: attack_status
  					}).done(function() {
  						atk = atk.split(' ').join('_');
  						$('#zmove').hide().attr('src', 'public/images/zmoves/' + atk + '.png').fadeIn(1000);
  						$('#use-zmove').hide('slow', function() {
  							$('#use-zmove').remove();
  						});
  						trainer_zmove = 1;
  					});
  				}
  			}
  		}
  	});

  	//Player Make Change Pokemon
  	$("div[id='change_pokemon']").click(function() {
  		if (speler_wissel == 1) {
  			if (($(this).attr("name") != "") && ($(this).attr("data-original-title")) != "Egg Pokémon") {
  				document.getElementById('hit').style.display = "none";
  				document.getElementById('hit2').style.display = "none";
  				$("#potion_screen").hide()
  				$.ajax({
  					type: "GET",
  					url: "attack/attack_change_pokemon.php?opzak_nummer=" + $(this).attr("name") + "&computer_info_name=<?= $computer_info['naam']; ?>&aanval_log_id=" + <?= $aanval_log['id']; ?> + "&_h=" + <?=$gebruiker['sec_key'];?> + "&sid=" + Math.random(),
  					success: change_pokemon_status
  				});
  			}
  		}
  	});

  	//Player Using Item
    $("div[data-item-name] img").click(function() {
  		if (speler_attack == 1) {
        let item = $(this).parent().data('item-name');
        let type = $(this).parent().data('item-type');

  			if (type == "Potion") {
  				$("#item_name").html(item)
  				$("#message").html()
  				$("#potion_screen").show()
  			} else {
  				$("#potion_screen").hide();
  				$.ajax({
  					type: "GET",
  					url: "attack/wild/wild-attack_use_pokeball.php?item=" + item + "&computer_info_name=<?= $computer_info['naam']; ?>&option_id=" + type + "&aanval_log_id=" + <?= $aanval_log['id']; ?> + "&_h=" + <?=$gebruiker['sec_key'];?> + "&sid=" + Math.random(),
  					success: use_item_status
  				});
  			}
  		}
  	});

  	$("div[id='run']").click(function() {
  		if (speler_attack == 1) {
  			$("#potion_screen").hide()
  			$.ajax({
  				type: "GET",
  				url: "attack/wild/wild-attack_run.php?computer_info_name=<?= $computer_info['naam']; ?>&aanval_log_id=<?= $aanval_log['id']; ?>&_h=" + <?=$gebruiker['sec_key'];?> + "&sid=" + Math.random(),
  				success: attack_run_status
  			});
  		}
  	});

  	//Player is Using Potion
  	$("div[id='use_potion']").click(function() {
		if (speler_attack == 1) {
			if ($(this).attr('name') == undefined) $("#message").html("<?= $txt['potion_no_pokemon_selected']; ?>")
			else {
				$.ajax({
					type: "GET",
					url: "attack/attack_use_potion.php?item=" + $("#item_name").html() + "&computer_info_name=<?= $computer_info['naam']; ?>&option_id=1&potion_pokemon_id=" + $(this).attr('name') + "&aanval_log_id=" + <?= $aanval_log['id']; ?> + "&_h=" + <?=$gebruiker['sec_key'];?> + "&sid=" + Math.random(),
					success: use_item_status
				});
				$("#potion_screen").hide()
			}
		}
	});
  });
  //Computer Do Attack
  (function($) {
  	computer_attack = function() {
  		if (speler_attack == 0) {
  			$("#message").html("<?= $txt['busy_with_attack']; ?>")
  			$("#potion_screen").hide()
  			document.getElementById('hit').style.display = "none";
  			document.getElementById('hit2').style.display = "";
  			pokeball_lock = true;

  			$.ajax({
  				type: "GET",
  				url: "attack/wild/wild-do_attack.php?attack_name=undifined&wie=computer&aanval_log_id=" + <?= $aanval_log['id']; ?> + "&_h=" + <?=$gebruiker['sec_key'];?> + "&sid=" + Math.random(),
  				success: attack_status
  			});
  		}
  	};
  })(jQuery);
   
  </script>
   <?php
    if ($computer_info['local'] == "Lavagrot") $narena = '5';
    else if ($computer_info['local'] == "Vechtschool") $narena = '6';
    else if ($computer_info['local'] == "Gras") $narena = '1';
    else if ($computer_info['local'] == "Spookhuis") $narena = '7';
    else if ($computer_info['local'] == "Grot") $narena = '4';
    else if ($computer_info['local'] == "Water") $narena = '2';
    else if ($computer_info['local'] == "Strand") $narena = '3';
    
    if ($narena == '3' || $narena == '6') {
        if ($season[1] == 2 || $season[1] == 4) {
            $narena .= '-'.$season[1];
        }
    }

    if ($computer_info['zeldzaamheid'] == 1) $raridade = "comum";
    else if ($computer_info['zeldzaamheid'] == 2) $raridade = "incomum";
    else if ($computer_info['zeldzaamheid'] == 3) $raridade = "muito raro";
    
					//$result = DB::exQuery("SELECT wild_id, vangbaarheid FROM pokemon_wild WHERE wild_id='".$computer_info['wild_id']."'");
                                        //$szamok = mysql_fetch_assoc($result);
    $egesz = 255;
    $egy = $egesz/100;
    $kerdeses_szam = $computer_info['vangbaarheid'];
    $eredmeny = $kerdeses_szam/$egy;
    number_format($eredmeny, 2, '.', '');    
    $chancecaptura = "".round($eredmeny, 2)."%";
    
    //z-index (gif_attack = 99999)
    	
    if ($computer_info['local'] != "Gras" AND $computer_info['local'] != "Water") {
   echo "<div class='box-content'><h3 class='title'>BATALHA!</h3><div id='gif_attack' style='position: absolute;float: right;margin-left: 165px;margin-top: 70px;z-index: 0;width: 700px;'><img src='".$static_url."/images/attacks/_blank.gif' style='float: left;width: 700px;height:323px'/></div><div id='weather'><img id='zmove'><table id='arena' class='battlearea".$narena."'>";
   } else {
    echo "<div class='box-content'><h3 class='title'>BATALHA!</h3><div id='gif_attack' style='position: absolute;float: right;margin-left: 165px;margin-top: 70px;z-index: 0;width: 700px;'><img src='".$static_url."/images/attacks/_blank.gif' style='float: left;width: 700px;height:323px'/></div><div id='weather'><img id='zmove'><table id='arena' class='".$gebruiker['background']."'>";
   }
  
   ?>

			<tr><td>
			<div style="padding:0px 0 100px 0px;"><div class="new_bar2">
			<div style='padding: 15px 0 0 120px;'><strong><font size='3' style='text-shadow:1px 1px 1px #fff;'><i><img src='<?=$static_url?>/images/lvl.png' style='padding:0 0 0 30px;'> <?= $computer_info['level']; ?> </i></strong></font></div>			<div style="padding:0px 0 0 43px;"><div class="hp_red"><div class="progress" id="computer_life" style="width: <?= $computer_life_procent; ?>%"></div>
			</div><div id="computer_effect" style="margin: -10px 2px 0px 151px; display: <?= $computer_effect['effect'] ? "block" : "none" ?>;"><img src="<?=$static_url?>/images/effects/<?= $computer_effect['effect'] ? $computer_effect['effect'] : 'none' ?>.png" alt="<?= $computer_effect['effect'] ?>" title="<?= $computer_effect['effect'] ?>"/>   </div></div>
			<div align="left" style="padding: 5px 0px 0px 10px;"><font style="text-shadow:1px 1px 1px #fff;" size="3">
			Um selvagem <strong><?= $computer_info['naam_goed']; ?></strong><?php if ($computer_info['star'] == "block") { ?><img src='<?=$static_url?>/images/icons/lidbetaald.png' title='Shiny'><?php } ?> <?php if (($gebruiker['Pokedex chip'] == 1) AND ($gebruiker['Pokedex'] == 1)) { ?>
			<img src="<?=$static_url;?>/images/icons/th_pokedex.png" class="tip_right-middle" title="<?=pokedex_popup($computer_info, $txt);?>" style="vertical-align: -1px;" /><?php } ?></font>
			</div>		
				<?php
					$have = explode(",", $gebruiker['pok_bezit']);
					if (in_array($computer_info['wild_id'], $have))
					echo "<div align='left' style='padding:5px 0 0 10px;'><img width='14' height='14' title='".$txt['have_already']." ".$computer_info['naam_goed']."' alt='Capturado' src='".$static_url."/images/icons/pokeball.gif'></img></div>";
				?>
				</div>
			
			</div>
			</td>
			<td>
			<div align="center" id="dame" style="display:none;"></div>
			<div class="infront" align="center" id="hit" style="display:none;"><img src="<?=$static_url?>/images/hit.png"/></div>
                <img id="img_pokemon_en" src="<?=$static_url?>/images/<?= $computer_info['map']."/".$computer_info['wild_id']; ?>.gif" style="margin: 100px 0px 0px 60%;"/>
            </td></tr>
			<tr>
			<td>
			<div align="center" id="dame2" style="display:none;"></div>
			<div class="inback" align="center" id="hit2" style="display:none;"><img src="<?=$static_url?>/images/hit.png"/></div>
                <img id="img_pokemon" src="<?=$static_url?>/images/<?= $pokemon_info['map']; ?>/back/<?= $pokemon_info['wild_id']; ?>.gif" style="margin: 40px 0 0 50%"/>
            </td>
			<td>
				<div style="padding:100px 0 0 150px;"><div class="new_bar" style="float: right;">
				<div style="padding:16px 0 0px 10px;"><strong><font size="3" style="text-shadow:1px 1px 1px #fff;"><span id="pokemon_naam" style="float:left;"><?= stripslashes($pokemon_info['naam_goed']); ?></span></strong></font> <span id="pokemon_star" style="display:<?= $pokemon_info['star']; ?>;"></div>
				<strong><font size="3" style="text-shadow:1px 1px 1px #fff;"><i><img src="<?=$static_url?>/images/lvl.png" style="padding:0 0 0 30px;"><span id="pokemon_level" style="padding:0px 0 0px 5px;"><?= $pokemon_info['level']; ?> <div id="hpresta" style="margin-top: -4px;margin-left: 80px;position: absolute;"><?= "".$pokemon_info['leven']."/".$pokemon_info['levenmax']."";?></div></span></i></strong></font>
				<div style="padding:0px 0 15px 43px;"><div class="hp_red">
				<div class="progress" id="pokemon_life" style="width: <?= $pokemon_life_procent; ?>%"></div>
				</div>
				<div id="pokemon_effect" style="margin: -10px 2px 0px 151px; display: <?= $pokemon_info['effect'] ? "block" : "none" ?>;"><img src="<?=$static_url?>/images/effects/<?= $pokemon_info['effect'] ? $pokemon_info['effect'] : 'none' ?>.png" alt="<?= $pokemon_info['effect'] ?>" title="<?= $pokemon_info['effect'] ?>"/>    </div>
				</div>
				<div style="padding:0px 0 0px 70px;"><div class="exp_blue">
				<div class="progress" id="pokemon_exp" style="width: <?= $pokemon_exp_procent; ?>%"></div>
				</div></div>
				</div></div>
			</td>
			</tr>
      <tr>
        <td style="width: 100%; background: url(public/images/layout/battle/action-content.png) no-repeat; padding: 10px 0; background-size: 100% 100%; height: 74px;" colspan="2">
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

                        if (zMoves::valid($pokemon_info)[0] && $aanval_log['zmove'] == 0) {
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
            <div id="mochila" data-tabs-wl> 
                <div class="items-carousel" style="width: 100%; margin-bottom: -34px;">
                  <?php
                      $sql2 = DB::exQuery("SELECT `naam`, `wat` FROM `items` WHERE (`wat` = 'pokeball' OR `wat` = 'potion')");

                      for($i=0; $items = $sql2->fetch_assoc(); $i++) {
                        $naamm = $items['naam'];
                        $qtd = $gebruiker[$naamm];

                        if ($qtd > 0) { 
                  ?>
                        <div class="carousel-cell"><div data-item-name="<?=$naamm?>" data-item-type="<?=ucfirst($items['wat'])?>"><img src="<?=$static_url?>/images/items/<?=$naamm?>.png" class="image"><span class="badges qtd" style="margin-left: -35px;position: relative;cursor: pointer;bottom: -12px;"><?=$qtd;?></span></div></div>
                  <?php
                        }
                      }
                      ?>

                      </div>

                      <script>
                        var $carousel = $('.items-carousel');

                        var $car = $carousel.flickity({
                            prevNextButtons: true,
                            pageDots: false,
                            draggable: true,
														cellAlign: 'left'
                        });
										</script>
            </div>
            </center>
        </td>
      </tr>
	</table></div>
  	<span id="potion_screen" style="display:none;">
        <h3 class="title">Escolha o Pokémon que irá receber <span id="item_name"></span>:</h3>
            <?php

          //Show all pokemon inhand
          while($player_hand = $pokemon_sql->fetch_assoc()) {
            //Load Right info for the pokemon in hand
            $player_hand_good = pokemonei($player_hand, $txt);
            //Als pokemon geen baby is
            if ($player_hand_good['ei'] != 1) { 
							echo '<div id="use_potion" name="'.$player_hand['id'].'" title="Usar item em '.$player_hand_good['roepnaam'].'?" style="background-image: url('.$static_url.'/'.$player_hand_good['animatie'].');" class="battle-pokemon">
												'.$player_hand_good['naam'].'
                        <div class="bar_red" style="background: #cccccc;border: #f2f2f2 1px solid;border-radius: 4px;clear: both;height: 8px;margin-left: 10px;margin-top: 1px;overflow: hidden;height: 3px; width: 86%;"><div class="progress" id="'.$player_hand['id'].'_life" style="width: '.$player_hand_good['levenprocent'].'%"></div></div>
                    </div>';           
						}
          }
          //Set Player hand query counter on 0
         $pokemon_sql->data_seek(0);
        ?>
	</span>

    <div class="text-box" style="margin-top: 7px">
      <table style="width: 100%">
          <tbody>
            <tr style="height: 150px">
							<td style="width: 41%">
								<div style="text-align: center;">
									<div onclick="tab_wl('#atacar'); $('#potion_screen').hide();" class="selector attack"></div>
									<div onclick="tab_wl('#mochila'); $('#potion_screen').hide(); $car.flickity('resize')" class="selector bag"></div>
									<div onclick="tab_wl('#pokemon'); $('#potion_screen').hide();" class="selector pokemon"></div>
									<div onclick="tab_wl(''); $('#potion_screen').hide();" id="run" class="selector run"></div>
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
}
?>