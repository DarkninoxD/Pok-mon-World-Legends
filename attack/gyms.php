<?php
include('app/includes/resources/security.php');

#Kijken of je wel pokemon bij je hebt
if ($gebruiker['in_hand'] == 0) header('location: index.php');

if ($gebruiker['item_over'] < 1)
	echo '<div class="blue">'.$txt['alert_itemplace'].'</div>';

$gymsql = DB::exQuery("SELECT * FROM trainer WHERE wereld ='".$gebruiker['wereld']."' ORDER BY id ASC");
$trainer = DB::exQuery("SELECT * FROM gebruikers_badges WHERE `user_id`='".$_SESSION['id']."'")->fetch_assoc();

function possible ($rank, $act, $next) {
    if ($rank >= 3 && $act == $next) {
        return true;
    }
    
    return false;
}

if (isset($_POST['submit']) && isset($_POST['gym_leader'])) {
  if ($gebruiker['in_hand'] == 0) {
    echo '<div class="red">'.$txt['no_pokemon'].'</div>';
  } else {
    $gym_info = DB::exQuery("SELECT `rank`, `wereld`, `badge`, `progress` FROM `trainer` WHERE `naam`='".$_POST['gym_leader']."' AND `badge`!=''")->fetch_assoc();
    if (possible($gebruiker['rank'], $gebruiker[$gebruiker['wereld'].'_gym'], $gym_info['progress'])) {
      $pokesvivos = DB::exQuery("SELECT `id` FROM `pokemon_speler` WHERE `user_id`='".$_SESSION['id']."' AND `opzak`='ja' AND `leven`>'0'")->num_rows;  
      if (empty($gym_info['badge']))
        echo "<div class='red'>Isto não é um ginásio!</div>";
      else if ($gebruiker['rank'] < $gym_info['rank'])
        echo "<div class='blue'>".$txt['alert_rank_too_less']."</div>";
      else if ($gebruiker['wereld'] != $gym_info['wereld'])
        echo "<div class='red'>".$txt['alert_wrong_world']."</div>";
      else if ($trainer[$gym_info['badge']] >= 1)
        echo "<div class='blue'>".$txt['alert_gym_finished']."</div>";
      else if ($pokesvivos == 0)          
        echo "<div class='red'>".$txt['alert_no_pokemon']."</div>";
      else{
        include('attack/trainer/trainer-start.php');
        $pokemon_sql->data_seek(0);
        $opzak = $pokemon_sql->num_rows;
        $level = 0;
        while($pokemon = $pokemon_sql->fetch_assoc()) $level += $pokemon['level'];
        $trainer_ave_level = $level/$opzak;
        #Make Fight
        $info5 = create_new_trainer_attack($_POST['gym_leader'],$trainer_ave_level,$_POST['gebied']);
        if (empty($info5['bericht'])) header("Location: ./gyms");
        else echo '<div class="red"> '.$txt[$info['bericht']].'</div>';
      }
    } else {
      echo '<div class="red">ERROR 230</div>';
    }
  }
}
echo addNPCBox(11, 'Ginásios', 'Seja bem vindo, treinador! <br>Aqui você poderá desafiar líderes de ginásios de determinadas regiões e com isso, você conseguirá vantagens ao ganhar deles, como por exemplo as <b>Insígnias</b>. Treine bastante seus Pokémons, porque aqui, nenhum líder de ginásio terá piedade de você!');

if ($gebruiker['rank'] < 3) {
?>

<div class="red">RANK MÍNIMO PARA ENFRENTAR OS GINÁSIOS: 3 - COACH. CONTINUE UPANDO PARA LIBERAR!</div>

<?php } ?>
<center>
<style>
	.carousel-cell {
		margin: 10px 10px;
		transform: scale(0.85);
		overflow: hidden;
	}

  .carousel-cell img {
    filter: grayscale(100%);
  }

	.carousel-cell.is-selected {
		transition: 1s;
		transform: scale(1);
	}

  .carousel-cell.is-selected img {
    filter: grayscale(20%) invert(8%);
  }

  .blocked {
    filter: brightness(0%)!important;
  }

  .complete {
    filter: grayscale(20%) invert(8%)!important;
  }
</style>

<div class="box-content" style="display: inline-block; width: 100%;">
	<table class="general" width="100%">
		<thead>
			<tr><th colspan="6">Ginásios da Região de <?=$gebruiker['wereld']?></th></tr>
		</thead>
		<tbody>
			<tr>
				<td style="width: 100%; padding: 0;">
					<div class="swiper-border" style="width: 100%;">
            <script>
                var $trainer_array_name = [];
                var $badge_array_name = [];
            </script>
						<div class="main-carousel carousel">
            <?php 
            $descr = [];
            $i = 0;
            $next = $gebruiker[$gebruiker['wereld'].'_gym'];

            while($gym = $gymsql->fetch_assoc()) {
              $blocked = '';
              $complete = '';
              $lock = '';
              $name = $gym['naam'];
              $badge = $gym['badge'].' Badge';
              if (strpos($gym['badge'], 'Elite') !== false) { 
                $gym['descr'] = $name.' é um membro da ELITE DOS 4 de '.$gym['wereld'].'!';
                $badge = $gym['badge'];
              }

              if (!possible($gebruiker['rank'], $next, $i) && $trainer[$gym['badge']] == 0) {
                $blocked = 'class="blocked"';
                if ($i > 0) {
                  $gym['descr'] = '[GINÁSIO BLOQUEADO!] <BR> [GANHE DO ANTERIOR PARA CONSEGUIR DESAFIAR LÍDER DE GINÁSIO!]';
                } else {
                  $gym['descr'] = '[GINÁSIO BLOQUEADO!] <BR> [SUBA DE RANK PARA CONSEGUIR DESAFIAR ESTE LÍDER DE GINÁSIO!]';
                }
                $name = '???';
                $badge = '???';
                $lock = '<div style="position: absolute;z-index: 1000; line-height: 170px; text-align: center"><img src="'.$static_url.'/images/icons/avatar/lock.png" style="width: 50%"></div>';
              }

              if ($trainer[$gym['badge']] == 1) {
                $complete = 'class="complete"';
              }              

              if (empty($gym['descr'])) {
                $gym['descr'] = 'Não há descrição disponível para este treinador!';
              }

              array_push($descr, $gym['descr']);
              
              $badge_img = '';
              if (strpos($gym['badge'], 'Elite') === false) $badge_img = '<img src="'.$static_url.'/images/badges/pixel/'.$gym['badge'].'.png" '.$blocked.$complete.' style="right: 16px; width: 27px; top: 25px; position: absolute; z-index: 10" />';
                echo "<div class='carousel-cell' style=\"width: 150px\">".$badge_img.$lock;
                echo "<img src=\"" . $static_url . "/images/trainers/" . $gym['naam'] . ".png\" id=\"trainer_infos\" width=\"150\" height=\"150\" ".$blocked.$complete."/><br>";
                echo "</div>";
            ?>
              <script id="remove">
                $trainer_array_name.push("<?=$name?>");
                $badge_array_name.push("<?=$badge?>");
                document.querySelector('#remove').outerHTML = '';
              </script>
              <?php
              $i++;
            }
            ?>
						</div>
					</div>
          <div style="width: 100%; background: rgba(0, 0, 0, .3); position: relative; bottom: 0; text-align: center; height: 53px; padding-top: 3px; margin-top: -35px; border-bottom-right-radius: 2px;  border-bottom-left-radius: 2px">
            <div style="width: 100%; text-align: center; font-size: 17px; margin-top: 3px">
              <h4 id="trainer_name" style="margin: 0; color: #eee; font-weight: bold;"></h4>
              <span id="badge_name" style="color: #eee; font-size: 13px"></span>
            </div>
          </div>
				</td>
			</tr>
		</tbody>
		<tfoot>
			<tr>
				<td align="right">
          <div style="border-radius: 4px; width: 97%; padding: 12px; margin-top: 10px; text-align: justify; height: 130px; font-size: 13px">
            <h3 style="margin: 0;"><b>Descrição:</b></h3><br>
            <p id="text_descr"></p>
          </div>
				</td>
			</tr>
      <tr style="border-top: 1px solid #577599;">
          <td>
            <form method="post" action="./attack/gyms">
                <input type="hidden" id="gym_leader" name="gym_leader" value="">
                <center><input type="submit" name="submit" value="Desafiar " id="battle" style="margin: 6px;"></center>
            </form>
          </td>
      </tr>
		</tfoot>
	</table>
</div>
<script>
  var $carousel = $('.main-carousel');
  var $trainer = $('#trainer_name');
  var $badge = $('#badge_name');
  var $submit = $('#battle');
  var $gym_leader = $('#gym_leader');
  var $next = <?=$next?>;
  var $rank = <?=$gebruiker['rank']?>;

  var $desc = {
  <?php
    for ($i = 0; $i < sizeof($descr); $i++) {
      echo $i.' : '.'"'.$descr[$i].'", ';
    }
  ?>
  };

  $carousel.flickity({
    pageDots: false,
    initialIndex: $next
  });
  
  var flkty = $carousel.data('flickity');

  $carousel.on('select.flickity', function() {
    let frase = $desc[flkty.selectedIndex];
    let trainer = $trainer_array_name[flkty.selectedIndex];
    $('#text_descr').html(frase);

    $trainer.text(trainer);
    $badge.text($badge_array_name[flkty.selectedIndex]);

    if ($rank >= 3) {
      if (flkty.selectedIndex > $next) {
        $submit.attr('disabled', 'disabled');
        $submit.val('GANHE DO TREINADOR ANTERIOR PARA ENFRENTÁ-LO!');
      } else if (flkty.selectedIndex == $next) {
        $submit.removeAttr('disabled');
        $submit.val('DESAFIAR '+trainer);
        $gym_leader.val(trainer);
      } else {
        $submit.attr('disabled', 'disabled');
        $submit.val('VOCÊ JÁ ENFRENTOU '+trainer+'!');
      }
    } else {
      $submit.attr('disabled', 'disabled');
      $submit.val('SUBA DE RANK PARA ENFRENTÁ-LO!');
    }
  });

  <?php if($gebruiker['rank'] >= 3) { ?>
  if ($next <= (flkty.slides.length - 1)) {
    $submit.removeAttr('disabled');
    $submit.val('Desafiar '+$trainer_array_name[$next]);
    $gym_leader.val($trainer_array_name[$next]);
  } else {
    let next = $next - 1;
    $carousel.flickity( 'select', next );
    $submit.attr('disabled', 'disabled');
    $submit.val('VOCÊ JÁ ENFRENTOU '+$trainer_array_name[next]+'!');
  }
  
  <?php } else { ?>
  $submit.attr('disabled', 'disabled');
  $submit.val('SUBA DE RANK PARA ENFRENTÁ-LO!');
  <?php } ?>

  $('#text_descr').html($desc[$next]);
  $trainer.text($trainer_array_name[$next]);
  $badge.text($badge_array_name[$next]);
    
  wlSound('gyms', (<?=$gebruiker['volume']?>-3), true);
</script>
</center>