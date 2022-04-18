<?php
	include("app/includes/resources/security.php");

	$trainer = 1;
	$lock = false;

	echo addNPCBox(24, 'Batalhar contra NPC', 'Aqui você tem a oportunidade de procurar treinadores para desafia-los!<br /> Clicando no botão <b>PROCURAR NPC\'s</b> automaticamente um treinador aleatório será escolhido para batalhar com você.');
	
	if ($gebruiker['rank'] >= 4) {

	if (isset($_POST['submit'])) {
	
		if ($gebruiker['in_hand'] == 0)	{ 
			echo '<div class="red">' . $txt['no_pokemon'] . '</div>';
		} else {
			if ($trainer == 1) {
				$queryx = DB::exQuery("SELECT `naam` FROM `trainer` WHERE `badge`='' AND (`gebied`!='') ORDER BY RAND() LIMIT 10");
				$trainers = array();
				
				if ($queryx->num_rows > 0) {
					foreach ($queryx as $fr) {
						array_push($trainers, $fr['naam']);
						$img = '<img src="'.$static_url.'/images/trainers/'.$fr['naam'].'.png">';
						echo '<script id="remove">
							$(document).ready(function () {
								$("#trainer-content .slot").append(\''.$img.'\');
								$("#remove").remove();
							});
						</script>';
					}

					$selected[0] = rand(0, sizeof($trainers)-1);
					$selected[1] = $trainers[$selected[0]];

					$lock = true;

					$pokesvivos = DB::exQuery("SELECT `id` FROM `pokemon_speler` WHERE `user_id`='".$_SESSION['id']."' AND `opzak`='ja' AND `leven`>'0'")->num_rows;

					$query['naam'] = $selected[1];

					if ($pokesvivos > 0) { 
						include('attack/trainer/trainer-start.php');
						$pokemon_sql->data_seek(0);
						$opzak = $pokemon_sql->num_rows;
						$level = 0;

						while($pokemon = $pokemon_sql->fetch_assoc()) { 
							$level += $pokemon['level'] ;
						}
						
						$trainer_ave_level = $level / $opzak;
						//Make Fight
						$_SESSION['sec_key'] = rand(100000, 999999);
						$_SESSION['_hkey'] = createKey(4);
						$_SESSION['hkey'] = mt_rand(1000, 9999);
						$info = create_new_trainer_attack($query['naam'],$trainer_ave_level,$gebied);
						DB::exQuery("UPDATE `gebruikers` SET `pagina`='trainer-attack',`sec_key`='{$_SESSION['sec_key']}',`hkey`='{$_SESSION['hkey']}' WHERE `user_id`='".$_SESSION['id']."'");      
					} else {
						echo "<div class='red'>".$txt['alert_no_pokemon']."</div>";
						$lock = false;
					}
				}
			}
		}
	}
	} else {
		echo '<div class="red">RANK MÍNIMO PARA BATALHAR CONTRA NPCS: 4 - TRAINER. CONTINUE UPANDO PARA LIBERAR!</div>';
	}

	$trainer = ['Rival Barry', 'Scientist Chip', 'Jessie e James', 'Team Aether Sara', 'Team Rocket Butch', 'Team Skull Guzma'];
	$rand = rand(0, (sizeof($trainer)-1));
?>

<script src="<?=$static_url?>/javascripts/jquery.roulette.min.js"></script>

<style>
  .blocked {
    filter: brightness(0%)!important;
  }
	#trainer-content img {
		height: 100px
	}
</style>

<div class="box-content" style="display: inline-block; width: 100%;">
	<table class="general" width="100%">
		<thead>
			<tr><th colspan="6">NPC's</th></tr>
		</thead>
		<tbody>
			<tr>
				<td style="width: 100%; padding: 0; margin: 10px 10px;">
					<div id="trainer-content" class="swiper-border" style="width: 100%; text-align: center">
						<center><div class="slot" style="display:none; margin: 10px;"></div></center>
						<img src="<?=$static_url?>/images/trainers/<?=$trainer[$rand]?>.png" class="blocked aux"/>
					</div>
          <div style="width: 100%; background: rgba(0, 0, 0, .3); position: relative; bottom: 0; text-align: center; height: 53px; padding-top: 3px; margin-top: -35px; border-bottom-right-radius: 2px;  border-bottom-left-radius: 2px">
            <div style="width: 100%; text-align: center; font-size: 17px; margin-top: 14px">
              <h4 id="trainer_name" style="margin: 0; color: #eee; font-weight: bold;">??</h4>
            </div>
          </div>
				</td>
			</tr>
		</tbody>
		<tfoot>
      <tr style="border-top: 1px solid #577599;">
          <td>
            <form method="post">
							<?php if ($gebruiker['rank'] >= 4)  {?><center><input type='submit' name='submit' value='Procurar NPCs' class='button' style="margin: 6px"></center><?php } else { ?> <center><input type='submit' name='submit' value='SUBA DE RANK PARA DESAFIAR OS NPCS' class='button' style="margin: 6px" disabled></center> <?php } ?>
            </form>
          </td>
      </tr>
		</tfoot>
	</table>
</div>

<?php
	if ($lock) {
?>
	<script>
		function start(obj, dur, num) {
			var option = {
					speed: 7,
					duration: dur,
					stopImageNumber: num,
					startCallback : function() {
							$('input[name="submit"]').attr('value', 'Batalhar!');
					},
					stopCallback : function($stopElm) {
							$('#trainer_name').text("Batalha contra <?=$selected[1]?>");
							setTimeout(() => {
								window.location = window.location.href;
							}, 1500);
					}
			}
			$(obj).roulette(option);
			$(obj).roulette('start');

			$('.aux').hide();
		}
		$(document).ready(function () {
			start('.slot', '1', <?=$selected[0]?>);
		});
	</script>
<?php } ?>