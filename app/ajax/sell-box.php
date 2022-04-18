<!DOCTYPE html>
<html lang="pt-br">
<head>
	<link rel="shortcut icon" href="favicon.ico" type="image/x-icon" />
	<title><?=$site_title;?></title>
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<meta http-equiv="X-UA-Compatible" content="IE=edge"/>

	<link rel="stylesheet" type="text/css" href="<?=$static_url;?>/stylesheets/style.css" />
	<link rel="stylesheet" type="text/css" href="<?=$static_url;?>/stylesheets/style_override.css" />

	<script src="<?=$static_url?>/javascripts/jquery-2.1.3.min.js"></script>
</head>
<body style="background: #1d2b3e">
<?php

$select = DB::exQuery("SELECT `pokemon_speler`.`id`,`pokemon_speler`.`icon`,`pokemon_speler`.`can_trade`,`pokemon_speler`.`gevongenmet`,`pokemon_speler`.`user_id`,`pokemon_speler`.`gehecht`,`pokemon_speler`.`opzak`,`pokemon_speler`.`shiny`,`pokemon_speler`.`level`,`pokemon_wild`.`wild_id`,`pokemon_wild`.`zeldzaamheid`,`pokemon_wild`.`naam`,`gebruikers`.`silver`,`gebruikers`.`premiumaccount`,`gebruikers`.`rank`,`gebruikers`.`admin`,`rekeningen`.`gold` FROM `pokemon_speler` INNER JOIN `pokemon_wild` ON `pokemon_speler`.`wild_id`=`pokemon_wild`.`wild_id` INNER JOIN `gebruikers` ON `pokemon_speler`.`user_id`=`gebruikers`.`user_id` INNER JOIN `rekeningen` ON `gebruikers`.`acc_id`=`rekeningen`.`acc_id` WHERE `pokemon_speler`.`id`='".$_GET['id']."' LIMIT 1")->fetch_assoc();

$allowed = 10;
if ($select['premiumaccount'] > time())	$allowed = 20;
if ($select['admin'] >= 3)	$allowed = 1000000000;

if ($select['user_id'] != $_SESSION['id'])	echo '<div class="red">' . $txt['alert_not_your_pokemon'] . '</div>';
else if ($select['gehecht'] == 1)	echo '<div class="red">'.$txt['alert_beginpokemon'].'</div>';
else if ($select['can_trade'] != 1)	echo '<div class="red">Este pokémon não pode ser negociado!</div>';
else {
	$count = DB::exQuery("SELECT `id` FROM `transferlijst` WHERE `user_id`='".$_SESSION['id']."'")->num_rows;
	if ($count < $allowed) {
		$pokemonnaam = pokemon_naam($select['naam'], $select['roepnaam'], $select['icon']);
		if ($select['shiny'] == 1)	$shiny = '<img src="'. $static_url .'/images/icons/lidbetaald.png" />'; 
		else	$shiny = '';

		if (isset($_POST['sell']) && isset($_POST['method'])) {
			$method = $_POST['method'];

			if (!in_array($method, array('auction', 'direct', 'private'))) echo '<div class="red">Este método de venda não existe!</div>';
			else if ($select['rank'] <= 3)	echo '<div class="red">'.$txt['alert_too_low_rank'].'</div>';
			else if ($select['user_id'] != $_SESSION['id'])	echo '<div class="red">'.$txt['alert_not_your_pokemon'].'</div>';
			else if ($select['opzak'] == 'tra')	echo '<div class="red">'.$txt['alert_pokemon_already_for_sale'].'</div>';
			else if ($select['opzak'] == 'day') echo '<div class="red">Este pokémon está no jardim de infância.</div>';
			else {
				if ($method == 'auction') {
					if (isset($_POST['silvers']) && ctype_digit($_POST['silvers'])) {
						$silvers = $_POST['silvers'];
						if ($silvers >= 500 && $silvers <= 1000000) {
							$date = date("d/m/Y");
							$date_end = strtotime(date('Y-m-d H:i', strtotime('+48 hours')));
							
							DB::exQuery("INSERT INTO `transferlijst` (`datum`, `user_id`, `silver`, `pokemon_id`, `time_end`, `type`) VALUES ('".$date."', '".$_SESSION['id']."', '".$silvers."', '".$select['id']."', '".$date_end."', 'auction')");
							DB::exQuery("UPDATE `pokemon_speler` SET `opzak`='tra' WHERE `id`='".$select['id']."'");

							$select1 = DB::exQuery("SELECT `id`,`opzak_nummer` FROM `pokemon_speler` WHERE `user_id`='" . $_SESSION['id'] . "' AND `id`!='" . $select['id'] . "' AND `opzak`='ja' ORDER BY `opzak_nummer` ASC");
							for($i=1;$selecta=$select1->fetch_assoc();++$i) { DB::exQuery("UPDATE `pokemon_speler` SET `opzak_nummer`='" . $i . "' WHERE `id`='" . $selecta['id'] . "' LIMIT 1"); }

							echo '<div class="green">' . $txt['alert_success_sell'] . '</div>';
							echo '<script type="text/javascript">
								parent.$.colorbox.close();
								parent.location.reload();
							</script>';
						}
					}
				} else if ($method == 'direct') {
					$silvers = $_POST['silvers'];
					$golds = $_POST['golds'];

					if ($silvers >= 500 && $silvers <= 1500000) {
						if ($golds >= 0 && $golds <= 1000) {
							$negociavel = 0;
							if (isset($_POST['negociavel'])) {
								$negociavel = 1;
							}
							$date = date("d/m/Y");
								
							DB::exQuery("INSERT INTO `transferlijst` (`datum`, `user_id`, `silver`, `pokemon_id`, `gold`, `type`,`negociavel`) VALUES ('".$date."', '".$_SESSION['id']."', '".$silvers."', '".$select['id']."', '".$golds."', 'direct', '".$negociavel."')");
							DB::exQuery("UPDATE `pokemon_speler` SET `opzak`='tra' WHERE `id`='".$select['id']."' LIMIT 1");

							for($i=1;$selecta=$select1->fetch_assoc();++$i) { DB::exQuery("UPDATE `pokemon_speler` SET `opzak_nummer`='" . $i . "' WHERE `id`='" . $selecta['id'] . "' LIMIT 1"); }

							echo '<div class="green">' . $txt['alert_success_sell'] . '</div>';
							echo '<script type="text/javascript">
									parent.$.colorbox.close();
									parent.location.reload();
								</script>';
						}
					}
				} else {
					if (isset($_POST['trainer'])) {
						$silvers = $_POST['silvers'];
						$golds = $_POST['golds'];
						$trainer = DB::exQuery("SELECT `user_id` FROM `gebruikers` WHERE `username`='$_POST[trainer]' AND `user_id` != '$_SESSION[id]'");
						
						if ($trainer->num_rows == 0) {
							echo '<div class="red">Este treinador não existe ou ele é você!</div>';
						} else {
							$trainer = $trainer->fetch_assoc()['user_id'];

							if ($silvers >= 500 && $silvers <= 2000000) {
								if ($golds >= 0 && $golds <= 1000) {
									$date = date("d/m/Y");
										
									DB::exQuery("INSERT INTO `transferlijst` (`datum`, `user_id`, `silver`, `pokemon_id`, `gold`, `type`,`to_user`) VALUES ('".$date."', '".$_SESSION['id']."', '".$silvers."', '".$select['id']."', '".$golds."', 'private', '".$trainer."')");
									DB::exQuery("UPDATE `pokemon_speler` SET `opzak`='tra' WHERE `id`='".$select['id']."' LIMIT 1");

									for($i=1;$selecta=$select1->fetch_assoc();++$i) { DB::exQuery("UPDATE `pokemon_speler` SET `opzak_nummer`='" . $i . "' WHERE `id`='" . $selecta['id'] . "' LIMIT 1"); }

									echo '<div class="green">' . $txt['alert_success_sell'] . '</div>';
									echo '<script type="text/javascript">
											parent.$.colorbox.close();
											parent.location.reload();
										</script>';
								}
							}
						}
					}
				}
			}
		}
?>


<div class="box-content" style="background: #1d2b3e;">
	<div class="msg-container">
		<div class="title"><p>TEM CERTEZA QUE DESEJA VENDER ESTE <b><?=$pokemonnaam . $shiny;?> (<?=sprintf($txt['level'], $select['level']);?>)</b>?</p></div>
		<div style="background: #34465f;padding: 10px;border-bottom: 2px solid #27374e;">
			<div align="center" style="padding: 0; width: 150px; height: 120px; background: url('<?=$static_url;?>/images/<?=($select['shiny'] == 1 ? 'shiny' : 'pokemon');?>/<?=$select['wild_id'];?>.gif') center no-repeat; margin: 0 auto"></div>
		</div>
	</div>
	<div class="msg-container" style="margin-top: 10px;">                   
		<div class="title" style="border-top: 1px solid #577599;"><p>SELECIONE O MÉTODO DE VENDA</p></div>
		<div style="background: #34465f;padding: 10px;border-bottom: 2px solid #27374e;">
			<input type="radio" name="selector" id="s1" value="auction"> <label for="s1"><p style="display: inline-block; margin-bottom: 2px">Leilão</p></label> <br>
			<input type="radio" name="selector" id="s2" value="direct"> <label for="s2"><p style="display: inline-block; margin-bottom: 2px">Venda Direta</p></label> <br>
			<input type="radio" name="selector" id="s3" value="private"> <label for="s3"><p style="display: inline-block; margin-bottom: 2px">Venda Privada</p></label> 
		</div>
	</div>
	
	<div class="msg-container select" id="auction" style="margin-top: 10px;">                   
		<div class="title" style="border-top: 1px solid #577599;"><p>LEILÃO</p></div>
		<form method="post">
			<div style="background: #34465f; padding: 10px;">
				<p>Preço inicial: <input type="number" name="silvers" min="500" max="1000000" required> (entre <b>500</b> <img src="<?=$static_url?>/images/icons/silver.png" title="Silvers" style="vertical-align: sub"> até <b>1.000.000</b> <img src="<?=$static_url?>/images/icons/silver.png" title="Silvers" style="vertical-align: sub">)</p>
				<p>Esse valor poderá aumentar devido aos lançes. <br>Este Pokémon será vendido depois de até <b>48</b> horas e caso não haja algum lance, ele retornará para sua casa!</p>
				<input type="hidden" name="method" value="auction">
			</div>
			
			<div style="border-bottom: 2px solid #27374e; border-top: 1px solid #577599; background: #1d2b3e; padding: 5px; text-align: center">
				<input type="submit" name="sell" value="VENDER POKÉMON!">
			</div>
		</form>
	</div>

	<div class="msg-container select" id="direct" style="margin-top: 10px;">                   
		<div class="title" style="border-top: 1px solid #577599;"><p>VENDA DIRETA</p></div>
		<form method="post">
			<div style="background: #34465f; padding: 10px;">
				<p>Silvers: <input type="number" name="silvers" min="500" max="1500000" required> (entre <b>500</b> <img src="<?=$static_url?>/images/icons/silver.png" title="Silvers" style="vertical-align: sub"> até <b>1.500.000</b> <img src="<?=$static_url?>/images/icons/silver.png" title="Silvers" style="vertical-align: sub">)</p>
				<p>Golds: <input type="number" name="golds" min="0" max="1000"> (entre <b>0</b> <img src="<?=$static_url?>/images/icons/gold.png" title="Golds" style="vertical-align: sub"> até <b>1000</b> <img src="<?=$static_url?>/images/icons/gold.png" title="Golds" style="vertical-align: sub">)</p>
				<p>Preço Negociável: <input type="checkbox" name="negociavel"> (Marque para receber ofertas de negociação de preço)</p>
				<p>Se este Pokémon não for vendido em até <b>2</b> dias, ele retornará para sua casa!</p>
				<input type="hidden" name="method" value="direct">
			</div>
			
			<div style="border-bottom: 2px solid #27374e; border-top: 1px solid #577599; background: #1d2b3e; padding: 5px; text-align: center">
				<input type="submit" name="sell" value="VENDER POKÉMON!">
			</div>
		</form>
	</div>

	<div class="msg-container select" id="private" style="margin-top: 10px;">                   
		<div class="title" style="border-top: 1px solid #577599;"><p>VENDA PRIVADA</p></div>
		<form method="post">
			<div style="background: #34465f; padding: 10px;">
				<p>Silvers: <input type="number" name="silvers" min="500" max="2000000"> (entre <b>500</b> <img src="<?=$static_url?>/images/icons/silver.png" title="Silvers" style="vertical-align: sub"> até <b>2.000.000</b> <img src="<?=$static_url?>/images/icons/silver.png" title="Silvers" style="vertical-align: sub">)</p>
				<p>Golds: <input type="number" name="golds" min="0" max="1000"> (entre <b>0</b> <img src="<?=$static_url?>/images/icons/gold.png" title="Golds" style="vertical-align: sub"> até <b>1000</b> <img src="<?=$static_url?>/images/icons/gold.png" title="Golds" style="vertical-align: sub">)</p>
				<p>Treinador: <input type="text" name="trainer" required> (O nome do treinador que você quer vender)</p>
				<input type="hidden" name="method" value="private">
			</div>
			
			<div style="border-bottom: 2px solid #27374e; border-top: 1px solid #577599; background: #1d2b3e; padding: 5px; text-align: center">
				<input type="submit" name="sell" value="VENDER POKÉMON!">
			</div>
		</form>
	</div>
</div>

<script>
	$('.select').hide();
	
	$('input[name="selector"]').change (function () {
		let value = $(this).val();

		$('.select').hide();
		$('#'+value).show();
	});
</script>
<?php
	} else	echo '<div class="red">Você não pode colocar mais de ' . $allowed . ' Pokémons nessa venda!</div>';
}
?>
</body>
</html>