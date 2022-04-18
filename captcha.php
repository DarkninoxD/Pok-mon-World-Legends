<?php
include("app/includes/resources/security.php");

echo addNPCBox(11, 'TESTE DE SEGURANÇA', ' Para evitar problemas de BOTS E MACROS tivemos que adotar essa medida de segurança.');

if($_POST) {

	if(empty($_SESSION['captchaincorreto'])){ $_SESSION['captchaincorreto'] = 0; }
	$chances = 3 - $_SESSION['captchaincorreto'];

	if($_POST['wild_id'] != $_SESSION['pkmon']){
		$_SESSION['captchaincorreto'] = $_SESSION['captchaincorreto']+1;
		echo '<div class="red">Incorreto, você tem mais '.$chances.' chance(s)!</div>';
	} else if(($_POST['emqual']) != $_SESSION['emqual']){
		$_SESSION['captchaincorreto'] = $_SESSION['captchaincorreto']+1;
		echo '<div class="red">Incorreto, você tem mais '.$chances.' chance(s)!</div>';
	} else {		
		$tempook = time();
		DB::exQuery("UPDATE `gebruikers` SET `captcha_time`=UNIX_TIMESTAMP() WHERE `user_id`='".$_SESSION['id']."'");
		if (strpos($_GET['page'], '/') !== false) {
		 	header("Location: ../".$_GET['page']);
		} else {
			header("Location: ./".$_GET['page']);
		}
		$_SESSION['captchaincorreto'] = 0;
		echo '<div class="green">Código correto, você pode continuar!</div>';
	}
   
}    
		 
	if($_SESSION['captchaincorreto'] > 3){
		if (strpos($_GET['page'], '/') !== false) {
		 	header("Location: ../logout");
		} else {
			header("Location: ./logout");
		}
 		$_SESSION['captchaincorreto'] = 0;
 	}
 		
		$pegapoke1 = DB::exQuery("select wild_id, naam from pokemon_wild where aparece='sim' order by rand() limit 1")->fetch_assoc();
 		$pegapoke2 = DB::exQuery("select wild_id, naam from pokemon_wild where aparece='sim' and wild_id!='".$pegapoke1['wild_id']."' order by rand() limit 1")->fetch_assoc();
 		
 		$_SESSION['pkmon'] = $pegapoke1['wild_id'];
 		
 		
 		$emqual = mt_rand(1,5);
 		
 		$_SESSION['emqual'] = $emqual;
 		
 		if($emqual == 1) {
 		$poke1 = $pegapoke1['wild_id'];
 		$poke2 = $pegapoke2['wild_id'];
 		$poke3 = $pegapoke2['wild_id'];
 		$poke4 = $pegapoke2['wild_id'];
 		$poke5 = $pegapoke2['wild_id'];
 		}
 		if($emqual == 2) {
 		$poke1 = $pegapoke2['wild_id'];
 		$poke2 = $pegapoke1['wild_id'];
 		$poke3 = $pegapoke2['wild_id'];
 		$poke4 = $pegapoke2['wild_id'];
 		$poke5 = $pegapoke2['wild_id'];
 		}
 		if($emqual == 3) {
 		$poke1 = $pegapoke2['wild_id'];
 		$poke2 = $pegapoke2['wild_id'];
 		$poke3 = $pegapoke1['wild_id'];
 		$poke4 = $pegapoke2['wild_id'];
 		$poke5 = $pegapoke2['wild_id'];
 		} 		
 		if($emqual == 4) {
 		$poke1 = $pegapoke2['wild_id'];
 		$poke2 = $pegapoke2['wild_id'];
 		$poke3 = $pegapoke2['wild_id'];
 		$poke4 = $pegapoke1['wild_id'];
 		$poke5 = $pegapoke2['wild_id'];
 		} 		 		
 		if($emqual == 5) {
 		$poke1 = $pegapoke2['wild_id'];
 		$poke2 = $pegapoke2['wild_id'];
 		$poke3 = $pegapoke2['wild_id'];
 		$poke4 = $pegapoke2['wild_id'];
 		$poke5 = $pegapoke1['wild_id'];
 		} 	 		
 		
 		
 		$rnd1 = mt_rand(1,2);
 		$rnd2 = mt_rand(1,2);
 		$rnd3 = mt_rand(1,2);
 		$rnd4 = mt_rand(1,2);	
 		$rnd5 = mt_rand(1,2);
 		
 		if($rnd1 != 1){ $front1 = "back/"; }
 		if($rnd2 != 1){ $front2 = "back/"; }
 		if($rnd3 != 1){ $front3 = "back/"; }
 		if($rnd4 != 1){ $front4 = "back/"; }
 		if($rnd5 != 1){ $front5 = "back/"; }
		 
?>
<?php if($gebruiker['premiumaccount'] < time()){ ?>
<div class="red">Você não é premium. Seja Premium clicando <a href="./gold-market">AQUI</a> e tenha vantagens.</div>
<?php } ?>
<div class="blue">Clique no <b><font color="#d25757"><?=$pegapoke1['naam']?></font></b> abaixo.</div>
<div class="row">
    <div style="width: 20%;" class="col">
		<div id="npc-section" style="height: 185px; border-top-right-radius: 0; border-bottom-right-radius: 0; border-right: 1px solid #577599;">
			<form method="post" id="poke1">
				<div style="height: 185px; vertical-align: middle; display: table-cell">
					<img src="<?=$static_url?>/images/pokemon/<?php echo $front1; echo $poke1; ?>.gif" onclick="$('#poke1').submit()" style="cursor: pointer">
				</div>
				<input type="hidden" value="<?php echo $poke1; ?>" id="wild_id" name="wild_id">
				<input type="hidden" value="1" id="emqual" name="emqual">
			</form>
		</div>
    </div>

	<div style="width: 20%;" class="col">
		<div id="npc-section" style="height: 185px;border-top-right-radius: 0;border-bottom-right-radius: 0;border-top-left-radius: 0;border-bottom-left-radius: 0; border-right: 1px solid #577599;">
			<form method="post" id="poke2">
				<div style="height: 185px; vertical-align: middle; display: table-cell">
					<img src="<?=$static_url?>/images/pokemon/<?php echo $front2; echo $poke2; ?>.gif" onclick="$('#poke2').submit()" style="cursor: pointer">
				</div>
				<input type="hidden" value="<?php echo $poke2; ?>" id="wild_id" name="wild_id">
				<input type="hidden" value="2" id="emqual" name="emqual">
			</form>
		</div>
	</div>
    
    <div style="width: 20%;" class="col">
		<div id="npc-section" style="height: 185px;border-top-right-radius: 0;border-bottom-right-radius: 0;border-top-left-radius: 0;border-bottom-left-radius: 0; border-right: 1px solid #577599;">
			<form method="post" id="poke3">
				<div style="height: 185px; vertical-align: middle; display: table-cell">
					<img src="<?=$static_url?>/images/pokemon/<?php echo $front3; echo $poke3; ?>.gif" onclick="$('#poke3').submit()" style="cursor: pointer">
				</div>
				<input type="hidden" value="<?php echo $poke3; ?>" id="wild_id" name="wild_id">
				<input type="hidden" value="3" id="emqual" name="emqual">
			</form>
		</div>
	</div>
	
	<div style="width: 20%;" class="col">
		<div id="npc-section" style="height: 185px;border-top-right-radius: 0;border-bottom-right-radius: 0;border-bottom-right-radius: 0;border-top-left-radius: 0;border-bottom-left-radius: 0;    border-right: 1px solid #577599;">
			<form method="post" id="poke4">
				<div style="height: 185px; vertical-align: middle; display: table-cell">
					<img src="<?=$static_url?>/images/pokemon/<?php echo $front4; echo $poke4; ?>.gif" onclick="$('#poke4').submit()" style="cursor: pointer">
				</div>
				<input type="hidden" value="<?php echo $poke4; ?>" id="wild_id" name="wild_id">
				<input type="hidden" value="4" id="emqual" name="emqual">
			</form>
		</div>
	</div>

	<div style="width: 20%;" class="col">
		<div id="npc-section" style="height: 185px;border-top-left-radius: 0;border-bottom-left-radius: 0;">
			<form method="post" id="poke5">
				<div style="height: 185px; vertical-align: middle; display: table-cell">
					<img src="<?=$static_url?>/images/pokemon/<?php echo $front5; echo $poke5; ?>.gif" onclick="$('#poke5').submit()" style="cursor: pointer">
				</div>
				<input type="hidden" value="<?php echo $poke5; ?>" id="wild_id" name="wild_id">
				<input type="hidden" value="5" id="emqual" name="emqual">
			</form>
		</div>
	</div>
</div>

