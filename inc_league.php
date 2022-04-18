<?php
include_once './app/classes/League.php';
if (isset($league_battle) && $league_battle && $page != "attack/duel/duel-attack") 
{
	$league = new League();
	$league->select($league_battle['league_id']);
?>
<script>
	var border_color = "black";
	function minimize_league_ad() {
		if ($("#league_ad > a").text() == "MINIMIZAR") {
			$("#header_league").css("display", "none");
			$("#error_league").css("display", "none");
			$("#league_ad").css({
				"height":		"65px",
				"width":		"275px",
				"top":			"8%",
				"left":			"2%",
				"margin-top":	"0",
				"margin-left":	"0",
				"border-color":	border_color
			});
			$("#league_ad > a").text("MAXIMIZAR");
		} else if ($("#league_ad > a").text() == "FECHAR")
			$("#league_ad").remove();
		else {
			$("#header_league").css("display", "block");
			$("#error_league").css("display", "block");
			$("#league_ad").css({
				"height": 		"240px",
				"width": 		"420px",
				"top": 			"50%",
				"left": 		"50%",
				"margin-top": 	"-110px",
				"margin-left": 	"-210px",
				"border-color": "black"
			});
			$("#league_ad > a").text("MINIMIZAR");
		}
	}
</script>
<div id="league_ad" style="width: 420px; height: 240px; position: fixed; top: 50%; left: 50%; z-index: 2; margin-top: -110px; margin-left: -210px; background: url('<?=$static_url;?>/images/layout/fundobox.png'); border: solid 5px black; padding: 10px;">
	<a href="#" style="background-color: black; color: white; border-radius: 5px; padding: 5px; position: relative; top: -15px; left: -15px;" onclick="minimize_league_ad(); return false;">MINIMIZAR</a>
<div>
	<div id="header_league">
		<h3 style="text-align: center;">Fase de preparação para batalha <?= ($league->getTotal_participantes() > 16 ? "na Liga" : "no Torneio") ?>.</h3>
		<p>
			Durante a fase de preparação edite seu time e vá para a região onde <?= ($league->getTotal_participantes() > 16 ? "a liga" : "o torneio") ?> está acontecendo,
			neste período você não poderá batalhar ou realizar trabalhos.
		</p>
	</div>
	<div style="text-align: center;">
		<div style="font-size: 1.3em; font-weight: bold; margin: 10px;" id="league_counter">Faltam: <span id="countdown"></span></div>
	</div>
	<script>
		function batalhar() {
			$("#league_counter").html("Criando batalha...");
			$.get("./ajax.php?act=league", function (data) {
				var request = data.split(" | ");
				$("#league_counter").html(request[1]);
				if (request[0] === '0')
					setTimeout("batalhar()", 2000);
				else if (request[0] === '1')
					location.reload();
				else if (request[0] === '2')
					location.href = './attack/duel/duel-attack';
				else if (request[0] === '3') {
					$("#league_ad > a").text("MAXIMIZAR");
					minimize_league_ad();
					$("#league_ad > a").text("FECHAR");
				} else if (request[0] === '4') {
					$("#league_ad > a").text("MAXIMIZAR");
					minimize_league_ad();
					$("#league_ad > a").text("FECHAR");
				}
			});
		}
		function att_pagina_pre_batalha() {
			if ($('#countdown').text() == "01:00")
				location.reload();
		}

		$(function () {
			$('#countdown').countdown({
				until: new Date(new Date().getTime() + <?=((strtotime($league_battle['inicio']) - time() - League::$ajuste_tempo_int) * 1000);?>),
				padZeroes: true,
				format: 'MS',
				layout: '<span class="countdown-amount">{mnn}</span>:<span class="countdown-amount">{snn}</span>',
				onTick: att_pagina_pre_batalha,
				onExpiry: batalhar
			});
			if ($('#countdown').text() == "00:00")
				batalhar();
		});
	</script>
	<div id="error_league">
		<?php if ($erros_liga = $league->erro_duelo($_SESSION['id'])) { ?>
			<script>border_color = "#ff0000";</script>
			<?php foreach ($erros_liga as $erro_liga) { ?>
				<p style="text-align: center; font-weight: bold; color: black; background-color: #ff6666; border: solid 3px #ff0000; border-radius: 5px;"><?= $erro_liga ?></p>
	<?php
			}
		} else {
	?>
			<script>border_color = "#00ff00";</script>
			<p style="text-align: center; font-weight: bold; color: black; background-color: #99ff99; border: solid 3px #00ff00; border-radius: 5px;">Tudo pronto para a batalha!</p>
	<?php } ?>
	</div>
</div>
<?php
} else if (isset($_SESSION['id']) && ($league_id = League::aberta(true)) && !isset($_SESSION['torneio_' . $league_id . '_ad'])) {
	$league = new League();
	$league->select($league_id);
	
?>
<div id="torneio_ad" style="width: 600px; height: 400px; position: fixed; top: 50%; left: 50%; z-index: 2; margin-top: -200px; margin-left: -300px;">
	<a href="#" style="background-color: black; color: white; border-radius: 5px; padding: 5px;" onclick="$.ajax({url: './ajax.php?act=remove_league_ad&torneio=<?=$league_id;?>', context: document.body}); $('#torneio_ad').remove(); return false;">FECHAR</a>
	<a href="./tour" style="text-decoration: none;">
		<img src="<?=$static_url;?>/images/layout/torneio_ad.png"/>
		<?php if (!$league->getRound_atual()) { ?>
			<div style="position: relative; z-index: 3; bottom: 75px; left: 35px; font-family: 'Lucida Console'; color: #fad000; text-shadow:2px 2px 0 #000; font-size: 35px;">
				<?= $league->getTotal_participantes() - $league->getParticipantes() ?> Vagas
			</div>
		<?php } ?>
	</a>
</div>
<?php
} else if (isset($_SESSION['id']) && !isset($_SESSION['league_ad']) && ($league_id = League::aberta())) {
	$league = new League();
	$league->select($league_id);
?>
<div id="league_ad" style="width: 600px; height: 400px; position: fixed; top: 50%; left: 50%; z-index: 2; margin-top: -200px; margin-left: -300px;">
	<a href="#" style="background-color: black; color: white; border-radius: 5px; padding: 5px;" onclick="$.ajax({url: './ajax.php?act=remove_league_ad', context: document.body}); $('#league_ad').remove(); return false;">FECHAR</a>
	<a href="./league" style="text-decoration: none;">
		<img src="<?=$static_url;?>/images/layout/league_ad.png"/>
		<?php if (!$league->getRound_atual()) { ?>
			<div style="position: relative; z-index: 3; bottom: 75px; left: 35px; font-family: 'Lucida Console'; color: #fad000; text-shadow:2px 2px 0 #000; font-size: 35px;">
				<?= $league->getTotal_participantes() - $league->getParticipantes() ?> Vagas
			</div>
		<?php } ?>
	</a>
</div>
<?php } ?>