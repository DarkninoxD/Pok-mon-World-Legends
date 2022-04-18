<?php

echo addNPCBox(24, 'DROPS ELEMENTAIS - DISPONÍVEL ATÉ 19/05/2019!', 'O EVENTO <b>DROPS ELEMENTAIS</b> ESTÁ DISPONÍVEL!
<br>• Nesse atual evento contamos com 7 tipos de DROPS, dentre eles: <img src="'.$static_url.'/images/layout/events/drops/Fire.png" title="DROP FIRE" style="width: 16px"/>, <img src="'.$static_url.'/images/layout/events/drops/Fighting.png" title="DROP FIGHTING" style="width: 16px"/>, <img src="'.$static_url.'/images/layout/events/drops/Grass.png" title="DROP GRASS" style="width: 16px"/>, <img src="'.$static_url.'/images/layout/events/drops/Ice.png" title="DROP ICE" style="width: 16px"/>, <img src="'.$static_url.'/images/layout/events/drops/Ground.png" title="DROP GROUND" style="width: 16px"/>, <img src="'.$static_url.'/images/layout/events/drops/Water.png" title="DROP WATER" style="width: 16px"/> e o mais <b>difícil</b> e <b>valioso</b>: <img src="'.$static_url.'/images/layout/events/drops/Ghost.png" title="DROP GHOST" style="width: 16px"/>!
<br>• Consiga eles em suas determinadas áreas <a href="./eventos&actual=drops_elementais#encontrar"><b>[?]</b></a>;
<br>• Haverão dias em que alguns DROPS terão mais chances de se obter <a href="./eventos&actual=drops_elementais#dias"><b>[?]</b></a>;
<br>• Eles podem variar a quantidade, entre 1x à 3x DROPS!');

?>

<?php
    $unlock = true;
    if ($unlock) {
        if (isset($_POST['buy-store'])) {
            $id = $_POST['buy-store'];
            
            $query = DB::exQuery("SELECT * FROM `events_drop_1_2019_store` WHERE `id`='$id' AND `active`='1'")->fetch_assoc();
            if (isset($query)) {
                $lock = 'none';
                $type = ['', 'Fire', 'Fighting', 'Grass', 'Ghost', 'Ground', 'Water', 'Ice'];
                
                for ($i = 1; $i < 8; $i++) {
                    $drop = $query[$i.'_drop'];
                    $g_drop = $gebruiker[$i.'_drop'];
                    if ($drop > 0) {
	                    if ($g_drop >= $drop) {
	                        if ($lock != 'false') $lock = 'true';
	                    } else {
	                        $lock = 'false';
	                        echo '<div class="red">Faltam '.($drop - $g_drop).'x DROP '.$type[$i].' <img src="'.$static_url.'/images/layout/events/drops/'.$type[$i].'.png" style="margin-right:0;width:16px"/>! </div>';
	                    }
	                }
	            }
	            
	            if ($lock == 'true') {
	                if ($query['type'] == '0') {
	                    //ITENS
                        if ($gebruiker['item_over'] < 1) {
                            echo '<div class="red">VOCÊ NÃO TEM ESPAÇOS DISPONÍVEIS NA SUA MOCHILA!</div>';
                        } else {
                            DB::exQuery("UPDATE `gebruikers_item` SET `".$query['name']."` = `".$query['name']."` + '1' WHERE `user_id` = '$_SESSION[id]';");
                            echo '<div class="green">Você comprou 1x '.$query['name'].'!</div>';
                            DB::exQuery("UPDATE `gebruikers` SET `1_drop`=`1_drop`-'".$query['1_drop']."', `2_drop`=`2_drop`-'".$query['2_drop']."', `3_drop`=`3_drop`-'".$query['3_drop']."', `4_drop`=`4_drop`-'".$query['4_drop']."', `5_drop`=`5_drop`-'".$query['5_drop']."', `6_drop`=`6_drop`-'".$query['6_drop']."', `7_drop`=`7_drop`-'".$query['7_drop']."' WHERE `user_id`='$_SESSION[id]'");
                        }
                    } else if ($query['type'] == '1') {
                        //POKEMON
                        if ($gebruiker['in_hand'] >= 6) {
                            echo '<div class="red">VOCÊ JA POSSUI 6 POKÉMONS COM VOCÊ!</div>';
                        } else {
                            $opzak_nummer = $gebruiker['in_hand'] + 1;
        
                            #Willekeurige pokemon laden, en daarvan de gegevens
                            $pkm = DB::exQuery("SELECT `wild_id`,`groei`,`attack_base`,`defence_base`,`speed_base`,`spc.attack_base`,`spc.defence_base`,`hp_base`,`aanval_1`,`aanval_2`,`aanval_3`,`aanval_4`,`ability` FROM `pokemon_wild` WHERE `wild_id`='" . $query['type_val'] . "' LIMIT 1")->fetch_assoc();
                            $ability = explode(',', $pkm['ability']);
        
                            $date = date('Y-m-d H:i:s');
        
                            $ability = $ability[rand(0, (sizeof($ability) - 1))];
        
                            #De willekeurige pokemon in de pokemon_speler tabel zetten
                            DB::exQuery("INSERT INTO `pokemon_speler` (`wild_id`,`aanval_1`,`aanval_2`,`aanval_3`,`aanval_4`) SELECT `wild_id`,`aanval_1`,`aanval_2`,`aanval_3`,`aanval_4` FROM `pokemon_wild` WHERE `wild_id`='" . $pkm['wild_id'] . "'");
        
                            #id opvragen van de insert hierboven
                            $pokeid	= DB::insertID();
        
                            #Karakter kiezen 
                            $karakter  = DB::exQuery("SELECT * FROM `karakters` ORDER BY RAND() LIMIT 1")->fetch_assoc();
        
                            #Expnodig opzoeken en opslaan
                            $experience = DB::exQuery("SELECT `punten` FROM `experience` WHERE `soort`='".$pkm['groei']."' AND `level`='6'")->fetch_assoc();
        
                            $attack_iv		= mt_rand(15, 31);
                            $defence_iv		= mt_rand(15, 31);
                            $speed_iv		= mt_rand(15, 31);
                            $spcattack_iv	= mt_rand(15, 31);
                            $spcdefence_iv	= mt_rand(15, 31);
                            $hp_iv			= mt_rand(15, 31);
        
                            #Stats berekenen
                            $attackstat		= round(((($attack_iv + 2 * $pkm['attack_base']) * 5 / 100) + 5) * $karakter['attack_add']);
                            $defencestat	= round(((($defence_iv + 2 * $pkm['defence_base']) * 5 / 100) + 5) * $karakter['defence_add']);
                            $speedstat		= round(((($speed_iv + 2 * $pkm['speed_base']) * 5 / 100) + 5) * $karakter['speed_add']);
                            $spcattackstat	= round(((($spcattack_iv + 2 * $pkm['spc.attack_base']) * 5 / 100) + 5) * $karakter['spc.attack_add']);
                            $spcdefencestat	= round(((($spcdefence_iv + 2 * $pkm['spc.defence_base']) * 5 / 100) + 5) * $karakter['spc.defence_add']);
                            $hpstat			= round((($hp_iv + 2 * $pkm['hp_base']) * 5 / 100) + 10 + 5);
        
                            #Alle gegevens van de pokemon opslaan
                        DB::exQuery("UPDATE `pokemon_speler` SET `level`='5',`karakter`='".$karakter['karakter_naam']."',`expnodig`='".$experience['punten']."',`user_id`='".$_SESSION['id']."',`opzak`='ja',`opzak_nummer`='".$opzak_nummer."',`attack_iv`='".$attack_iv."',`defence_iv`='".$defence_iv."',`speed_iv`='".$speed_iv."',`spc.attack_iv`='".$spcattack_iv."',`spc.defence_iv`='".$spcdefence_iv."',`hp_iv`='".$hp_iv."',`attack`='".$attackstat."',`defence`='".$defencestat."',`speed`='".$speedstat."',`spc.attack`='".$spcattackstat."',`spc.defence`='".$spcdefencestat."',`levenmax`='".$hpstat."',`leven`='".$hpstat."',`ability`='".$ability."',`capture_date`='".$date."',`icon`='4' WHERE `id`='".$pokeid."' LIMIT 1");
                        
                        echo '<div class="green">Você comprou 1x '.$query['name'].'!</div>';
                        DB::exQuery("UPDATE `gebruikers` SET `1_drop`=`1_drop`-'".$query['1_drop']."', `2_drop`=`2_drop`-'".$query['2_drop']."', `3_drop`=`3_drop`-'".$query['3_drop']."', `4_drop`=`4_drop`-'".$query['4_drop']."', `5_drop`=`5_drop`-'".$query['5_drop']."', `6_drop`=`6_drop`-'".$query['6_drop']."', `7_drop`=`7_drop`-'".$query['7_drop']."' WHERE `user_id`='$_SESSION[id]'");
                        }
                    } else if ($query['type'] == '3') {
                        //VIP
                        $premium = 86400 * $query['type_val'];
                        if ($gebruiker['premiumaccount'] < time()) $premium += time();
                        else $premium += $gebruiker['premiumaccount'];
    
                        DB::exQuery("UPDATE `gebruikers` SET `premiumaccount`={$premium} WHERE `user_id`={$_SESSION['id']} LIMIT 1");
                        echo '<div class="green">Você comprou '.$query['name'].'!</div>';
                        DB::exQuery("UPDATE `gebruikers` SET `1_drop`=`1_drop`-'".$query['1_drop']."', `2_drop`=`2_drop`-'".$query['2_drop']."', `3_drop`=`3_drop`-'".$query['3_drop']."', `4_drop`=`4_drop`-'".$query['4_drop']."', `5_drop`=`5_drop`-'".$query['5_drop']."', `6_drop`=`6_drop`-'".$query['6_drop']."', `7_drop`=`7_drop`-'".$query['7_drop']."' WHERE `user_id`='$_SESSION[id]'");
                    }
	            }
	            
            } else {
                echo '<div class="red">Essa opção não existe!</div>';
            }
        }
?>
<?php
    if (true) {
        if (isset($_POST['conjunto_evento'])) {
            if ($rekening['gold'] < 50) {
                echo '<div class="red">Você não tem Golds suficientes!</div>';
            } else {
                $arr = array('Fire', 'Fighting', 'Grass', 'Ground', 'Water', 'Ice');
                $choosen = array();
                for ($i = 0; $i < 3; $i++) {
                    $rand = rand(0, (sizeof($arr)-1));
                    array_push($choosen, $arr[$rand]);
                    array_splice($arr, $rand, 1);
                }
                
                $rand_ghost = rand(0, 9);
                
                if ($rand_ghost >= 4) {
                    array_push($choosen, 'Ghost');
                }
                
                $drops_name = [
                    'Fire' => '1_drop',
                    'Fighting' => '2_drop',
                    'Grass' => '3_drop',
                    'Ghost' => '4_drop',
                    'Ground' => '5_drop',
                    'Water' => '6_drop',
                    'Ice' => '7_drop'
                ];
                
                $drops_qnt = ['40', '120', '200', '320', '640', '1200'];
                $drops_g_qnt = ['10', '25', '45', '135', '405', '700'];
                $choosen_qnt = array();
                
                for ($i = 0; $i < sizeof($choosen); $i++) {
                    $rand = rand(1, 100);
                    
                    if ($rand <= 50) {
                        $get = 0;
                    } else if ($rand <= 70) {
                        $get = 1;
                    } else if ($rand <= 85) {
                        $get = 2;
                    } else if ($rand <= 95) {
                        $get = 3;
                    } else if ($rand <= 99) {
                        $get = 4;
                    } else {
                        $get = 5;
                    }
                    
                    if ($i == 3) {
                        // if ($rand <= 48) {
                        //     $get = 0;
                        // } else if ($rand <= 68) {
                        //     $get = 1;
                        // } else if ($rand <= 83) {
                        //     $get = 2;
                        // } else if ($rand <= 93) {
                        //     $get = 3;
                        // } else if ($rand <= 98) {
                        //     $get = 4;
                        // } else {
                        //     $get = 5;
                        // }
                        
                        array_push($choosen_qnt, $drops_g_qnt[$get]);
                    } else {
                        array_push($choosen_qnt, $drops_qnt[$get]);
                    }
                }
                
                $drops = ''; $insql = '';
                for ($i = 0; $i < sizeof($choosen); $i++) {
                    $img = '<img src="'.$static_url.'/images/layout/events/drops/'.$choosen[$i].'.png" title="DROP '.strtoupper($choosen[$i]).'" style="width: 16px; vertical-align: bottom;"/>';
                    $drops .= $choosen_qnt[$i].'x '.$img.', ';
                    $namezada = '`'.$drops_name[$choosen[$i]].'`';
                    $insql .= $namezada.' = '.$namezada.' + '."'".$choosen_qnt[$i]."'".', ';
                }
                
                $insql = rtrim($insql, ', ');

                DB::exQuery("UPDATE `gebruikers` SET ".$insql." WHERE `user_id`='".$_SESSION['id']."'");
                DB::exQuery("UPDATE `rekeningen` SET `gold`=`gold`-'50' WHERE `acc_id`='".$_SESSION['acc_id']."'");
                echo '<div class="green">Você recebeu: '.rtrim($drops, ', ').'!</div>';
            }
        }
?>
    <div class="row">
    <div class="box-content col" style="margin-bottom: 7px; width: 50%; margin-right: 7px">
    <h3 class="title" style="font-size: 13px;padding: 10px;padding-left: 4px;font-weight: bold;text-transform: uppercase;color: #9eadcd;margin:0">QUANTIDADE DE DROPS NO INVENTÁRIO:</h3>
<?php 
    $arrays = array('Fire', 'Fighting', 'Grass', 'Ghost', 'Ground', 'Water', 'Ice');
    
    for($i = 0; $i < count($arrays); $i++) {
        $name = $arrays[$i];
?>

<div style="border-radius: 4px;display: inline-block;margin: 3px;border: 1px solid #577599;width: 100px;box-shadow: 0 2px 0 0 #0f1a2a;overflow: hidden;">
	<table class="general" width="100%">
		<thead>
			<tr></tr>
			<tr><th style="border-radius: 5px 5px 0 0;"><?=strtoupper($name)?></th></tr>
		</thead>
		<tbody><tr><td style="border-radius: 0 0 5px 5px;" align="center" title="DROP <?=strtoupper($name)?>"><img src="<?=$static_url?>/images/layout/events/drops/<?=$name?>.png" class="elipse" style="margin-right:0;"/><br><span style="color:#9eadcd;font-weight:bold;font-size:12px"><?=$gebruiker[($i+1).'_drop']?>x</span></td></tr></tbody>
	</table>
</div>

<?php } ?>

</div>
    <div class="col" style="margin-bottom: 7px; width:50%">
        <div class="box-content row" style="height: 125px;">
            <div class="col" style="border-right: 1px solid #577599;"> 
                <img src="<?=$static_url?>/images/layout/events/store/Drops.png" style="padding:10px;margin-top:10px">
            </div>
            <div class="col" style="margin-left: 10px">
                <h3 class="title" style="font-size: 13px;padding: 10px;padding-left: 4px;font-weight: bold;text-transform: uppercase;color: #9eadcd;margin:0">CONJUNTO DE DROPS!</h3>
                <p style="color: #fff;font-weight: bold;font-size:13px; margin:0; text-align: left">Até dia 19/05, você poderá comprar por apenas <b><img src="<?=$static_url?>/images/icons/gold.png"> 50 GOLDS</b>, <br>um Conjunto que vêm 3 DROPS aleatórios, podendo vir um 4º Ghost!<br>
                <center><form method="post" onsubmit="return confirm('Deseja realmente comprar o Conjunto de DROPS por 50 Golds?')"><button value="conjunto_evento" name="conjunto_evento" type="submit" style="margin-top: 5px">COMPRAR CONJUNTO?</button></form></center></p>
            </div>
        </div>
        <div class="box-content" style="margin-top: 3px; height: 151px">
            <table class="bordered general u-infos" width="100%">
                <thead>
                    <tr><th colspan="9">Quantidade de DROPS que podem vir no Conjunto <span style="cursor:pointer" title="Os valores de cima correspondem aos DROPS normais, enquanto os de baixo correspondem apenas aos DROPS GHOST!">[?]</span></th></tr>
                </thead>
                <tbody style="color:#9eadcd;font-weight:bold;font-size:12px">
                    <tr></tr>
                    <tr>
                        <td rowspan="2"><img src="<?=$static_url?>/images/layout/events/store/Drops.png" style="padding:9px;"></td>
                        <td>4500</td>
                        <td>2800</td>
                        <td>1200</td>
                        <td>640</td>
                        <td>320</td>
                        <td>200</td>
                        <td>120</td>
                        <td>40</td>
                    </tr>
                    <tr>
                        <td>2500 <span style="cursor:pointer" title="Valor correspondente aos DROPS GHOST!">[?]</span></td>
                        <td>1215 <span style="cursor:pointer" title="Valor correspondente aos DROPS GHOST!">[?]</span></td>
                        <td>700 <span style="cursor:pointer" title="Valor correspondente aos DROPS GHOST!">[?]</span></td>
                        <td>405 <span style="cursor:pointer" title="Valor correspondente aos DROPS GHOST!">[?]</span></td>
                        <td>135 <span style="cursor:pointer" title="Valor correspondente aos DROPS GHOST!">[?]</span></td>
                        <td>45 <span style="cursor:pointer" title="Valor correspondente aos DROPS GHOST!">[?]</span></td>
                        <td>25 <span style="cursor:pointer" title="Valor correspondente aos DROPS GHOST!">[?]</span></td>
                        <td>10 <span style="cursor:pointer" title="Valor correspondente aos DROPS GHOST!">[?]</span></td>
                    </tr>
                    <tr></tr>
                </tbody>
            </table>
        </div>
    </div>
    </div>
<?php } ?>

<div class="box-content" style="background:#2e3d53"><h3 class="title" style="font-size: 13px;padding: 10px;padding-left: 4px;font-weight: bold;text-transform: uppercase;color: #9eadcd;margin:0;background: none;border-bottom: 2px solid #6ac7ee;">LOJA DO EVENTO:</h3>
<div id="store_biggest" class="box-content">
    <h3 class="title" style="font-size: 13px;padding: 10px;padding-left: 4px;font-weight: bold;text-transform: uppercase;color: #9eadcd;margin:0">PRÊMIO MAIOR <span style="cursor: pointer; font-weight:bold" title="Spooky Plate">[?]</span>:</h3>
    <div id="container-biggest" style="margin: 10px;">
        <div style="display:inline-block">
            <div style="background: url(<?=$static_url?>/images/layout/events/store/Fire.png) no-repeat; background-size: 100% 100%; width: 76px; height: 82px; margin-right: 29px"><p style="color: #9eadcd;font-weight: bold;font-size:14px; margin:0; padding-top:50px;">1.800</p></div>
            <div style="background: url(<?=$static_url?>/images/layout/events/store/Fighting.png) no-repeat; background-size: 100% 100%; width: 76px; height: 82px; margin-right: 60px; margin-top: 10px"><p style="color: #9eadcd;font-weight: bold;font-size:14px; margin:0; padding-top:50px">1.500</p></div>
            <div style="background: url(<?=$static_url?>/images/layout/events/store/Grass.png) no-repeat; background-size: 100% 100%; width: 76px; height: 82px; margin-top: 10px"><p style="color: #9eadcd;font-weight: bold;font-size:14px; margin:0; padding-top:50px">2.000</p></div>
        </div>
        
        <div style="background: url(<?=$static_url?>/images/layout/events/store/BG-Plate.png) no-repeat; background-size: 100% 100%; width: 140px; height: 155px; display:inline-block; margin: 3px">
            <img src="<?=$static_url?>/images/layout/events/store/Biggest.png" title="Spooky Plate" style="margin-top: 30px;"/>
            <div style="background: url(<?=$static_url?>/images/layout/events/store/Ghost.png) no-repeat; background-size: 100% 100%; width: 76px; height: 82px; display:inline-block;margin-top:53px"><p style="color: #9eadcd;font-weight: bold;font-size:14px; margin:0; padding-top:50px">1.000</p></div>
        </div>
        
        <div style="display:inline-block">
            <div style="background: url(<?=$static_url?>/images/layout/events/store/Ground.png) no-repeat; background-size: 100% 100%; width: 76px; height: 82px; margin-left: 29px"><p style="color: #9eadcd;font-weight: bold;font-size:14px; margin:0; padding-top:50px">1.700</p></div>
            <div style="background: url(<?=$static_url?>/images/layout/events/store/Water.png) no-repeat; background-size: 100% 100%; width: 76px; height: 82px; margin-left: 60px; margin-top: 10px"><p style="color: #9eadcd;font-weight: bold;font-size:14px; margin:0; padding-top:50px">2.200</p></div>
            <div style="background: url(<?=$static_url?>/images/layout/events/store/Ice.png) no-repeat; background-size: 100% 100%; width: 76px; height: 82px; margin-top: 10px"><p style="color: #9eadcd;font-weight: bold;font-size:14px; margin:0; padding-top:50px;">1.900</p></div>
        </div>
    </div>
</div>

<div class="box-content" style="padding: 10px;"><form method="post" onsubmit="return confirm('Deseja realmente realizar esta compra?')"><input type="hidden" name="buy-store" value="1"><button type="submit">COMPRAR SPOOKY PLATE?</button></form></div>

<style>
.carousel-cell {
    margin: 10px 10px;
	overflow: hidden;
	transform: scale(0.8);
}
.carousel-cell.is-selected {
    transition: .5s;
	transform: scale(1);
}
</style>

<div class="box-content" style="margin-top:7px;display: inline-block; width: 100%;">
    <table class="general bordered" width="100%">
		<thead>
			<tr><th colspan="6">Itens</th></tr>
		</thead>
		<tbody>
			<tr>
				<td style="width: 100%; padding: 0">
					<div style="width: 100%; height: 200px;">
					    <script>
					        var $links = []; var $names = [];
					    </script>
					    <div class="main-carousel-item carousel">
					    <?php
					        $sql = DB::exQuery("SELECT * FROM `events_drop_1_2019_store` WHERE `type`='0' AND `active`='1' AND `biggest`='0'");
					        
					        while($sql1 = $sql->fetch_assoc()) {
					    ?>
							<div class="carousel-cell" style="text-align: center;">
							    <div style="background: url(<?=$static_url?>/images/layout/events/store/prizes.png) no-repeat; background-size: 100% 100%; width: 170px; height: 180px">
							        <table width="99%">
                                        <tbody><tr><th colspan="2"><h3 class="title" style="text-align: center; margin: 10px 0 0;"><?=ucwords($sql1['name'])?></h3></th></tr>
                                        <tr><td colspan="2"><img src="public/images/items/<?=$sql1['name']?>.png" class="elipse" style="margin-top: 13px;width: 36px;margin-right:0;"></td></tr>
                                        <tr>
                                        <td style="padding-top: 7px;">
                                            <script id="remove">
                                                $links.push("<?=$sql1['id']?>");
                    					        $names.push("<?=$sql1['name']?>");
                    					        $('#remove').remove();
                    					    </script>
                                            <?php
                                                for ($i = 1; $i < 8; $i++) {
                                                    $drop = $sql1[$i.'_drop'];
                                                    $type = ['', 'Fire', 'Fighting', 'Grass', 'Ghost', 'Ground', 'Water', 'Ice'];
                                                    if ($drop > 0) {
                					                    echo '<img src="'.$static_url.'/images/layout/events/drops/'.$type[$i].'.png" title="DROP '.strtoupper($type[$i]).'" style="width: 16px"/> <span style="color:#9eadcd;font-weight:bold;font-size:12px">'.$drop.'x</span><br>';
                					                }
                					            }
                                            ?>
                                        </td>
                                        </tr>
                                        </tbody></table>
							    </div>
							</div>
						<?php } ?>
						</div>
					</div>
				</td>
			</tr>
		</tbody>
		<tfoot>
			<tr>
				<td>
					<center><form method="post" onsubmit="return confirm('Deseja realmente realizar esta compra?')"><input type="hidden" id="item-store" name="buy-store" value="0"><button type="submit" style="margin: 10px;">COMPRAR <span id="item-name">item</span></button></form></center>
				</td>
			</tr>
		</tfoot>
	</table>
</div>

<script>
	var $carousel = $('.main-carousel-item');

    $carousel.flickity({
        pageDots: false,
        initialIndex: 0
    });

	var flkty = $carousel.data('flickity');
	var $input = $('#item-name');
	var $link = $('#item-store');

    $carousel.on('select.flickity', function() {
		$link.val($links[flkty.selectedIndex]);
		$input.text($names[flkty.selectedIndex]);
	});
	
    $input.text($names[flkty.selectedIndex]);
	$link.val($links[flkty.selectedIndex]);
</script>

<div class="box-content" style="margin-top:7px;display: inline-block; width: 100%;">
	<table class="general bordered" width="100%">
		<thead>
			<tr><th colspan="6">Pokémons</th></tr>
		</thead>
		<tbody>
			<tr>
				<td style="width: 100%; padding: 0">
					<div style="width: 100%; height: 200px;">
					    <script>
					        var $links2 = []; var $names2 = [];
					    </script>
					    <div class="main-carousel-pokemon carousel">
					    <?php
					        $sql = DB::exQuery("SELECT * FROM `events_drop_1_2019_store` WHERE `type`='1' AND `active`='1' AND `biggest`='0'");
					        
					        while($sql1 = $sql->fetch_assoc()) {
					    ?>
							<div class="carousel-cell" style="text-align: center;">
							    <div style="background: url(<?=$static_url?>/images/layout/events/store/prizes.png) no-repeat; background-size: 100% 100%; width: 170px; height: 180px">
							        <table width="99%">
                                        <tbody><tr><th colspan="2"><h3 class="title" style="text-align: center; margin: 10px 0 0;"><?=ucwords($sql1['name'])?></h3></th></tr>
                                        <tr><td colspan="2"><img src="public/images/pokemon/icon/<?=$sql1['type_val']?>.gif" class="elipse" style="margin-top: 13px;width: 36px;margin-right:0;"></td></tr>
                                        <tr>
                                        <td style="padding-top: 7px;">
                                            <script id="remove">
                                                $links2.push("<?=$sql1['id']?>");
                    					        $names2.push("<?=$sql1['name']?>");
                    					        $('#remove').remove();
                    					    </script>
                                            <?php
                                                for ($i = 1; $i < 8; $i++) {
                                                    $drop = $sql1[$i.'_drop'];
                                                    $type = ['', 'Fire', 'Fighting', 'Grass', 'Ghost', 'Ground', 'Water', 'Ice'];
                                                    if ($drop > 0) {
                					                    echo '<img src="'.$static_url.'/images/layout/events/drops/'.$type[$i].'.png" title="DROP '.strtoupper($type[$i]).'" style="width: 16px"/> <span style="color:#9eadcd;font-weight:bold;font-size:12px">'.$drop.'x</span><br>';
                					                }
                					            }
                                            ?>
                                        </td>
                                        </tr>
                                        </tbody></table>
							    </div>
							</div>
						<?php } ?>
						</div>
					</div>
				</td>
			</tr>
		</tbody>
		<tfoot>
			<tr>
				<td>
					<center><form method="post" onsubmit="return confirm('Deseja realmente realizar esta compra?')"><input type="hidden" id="pokemon-store" name="buy-store" value="0"><button type="submit" style="margin: 10px;">COMPRAR <span id="pokemon-name">pokémon</span></button></form></center>
				</td>
			</tr>
		</tfoot>
	</table>
</div>


<script>
	var $carousel2 = $('.main-carousel-pokemon');
    
    $carousel2.flickity({
        pageDots: false,
        initialIndex: 0
    });

	var flkty2 = $carousel2.data('flickity');
	var $input2 = $('#pokemon-name');
	var $link2 = $('#pokemon-store');

    $carousel2.on('select.flickity', function() {
		$link2.val($links2[flkty2.selectedIndex]);
		$input2.text($names2[flkty2.selectedIndex]);
	});
	
    $input2.text($names2[flkty2.selectedIndex]);
	$link2.val($links2[flkty2.selectedIndex]);
</script>

<div class="box-content" style="margin-top:7px;display: inline-block; width: 100%;">
	<table class="general bordered" width="100%">
		<thead>
			<tr><th colspan="6">VIP</th></tr>
		</thead>
		<tbody>
			<tr>
				<td style="width: 100%; padding: 0">
					<div style="width: 100%; height: 200px;">
					    <script>
					        var $links3 = []; var $names3 = [];
					    </script>
					    <div class="main-carousel-vip carousel">
					    <?php
					        $sql = DB::exQuery("SELECT * FROM `events_drop_1_2019_store` WHERE `type`='3' AND `active`='1' AND `biggest`='0'");
					        
					        while($sql1 = $sql->fetch_assoc()) {
					    ?>
							<div class="carousel-cell" style="text-align: center;">
							    <div style="background: url(<?=$static_url?>/images/layout/events/store/prizes.png) no-repeat; background-size: 100% 100%; width: 170px; height: 180px">
							        <table width="99%">
                                        <tbody><tr><th colspan="2"><h3 class="title" style="text-align: center; margin: 10px 0 0;"><?=ucwords($sql1['name'])?></h3></th></tr>
                                        <tr><td colspan="2"><img src="public/images/icons/gold-vip1.png" class="elipse" style="margin-top: 13px;width: 36px;margin-right:0;"></td></tr>
                                        <tr>
                                        <td style="padding-top: 7px;">
                                            <script id="remove">
                                                $links3.push("<?=$sql1['id']?>");
                    					        $names3.push("<?=$sql1['name']?>");
                    					        $('#remove').remove();
                    					    </script>
                                            <?php
                                                for ($i = 1; $i < 8; $i++) {
                                                    $drop = $sql1[$i.'_drop'];
                                                    $type = ['', 'Fire', 'Fighting', 'Grass', 'Ghost', 'Ground', 'Water', 'Ice'];
                                                    if ($drop > 0) {
                					                    echo '<img src="'.$static_url.'/images/layout/events/drops/'.$type[$i].'.png" title="DROP '.strtoupper($type[$i]).'" style="width: 16px"/> <span style="color:#9eadcd;font-weight:bold;font-size:12px">'.$drop.'x</span><br>';
                					                }
                					            }
                                            ?>
                                        </td>
                                        </tr>
                                        </tbody></table>
							    </div>
							</div>
						<?php } ?>
						</div>
					</div>
				</td>
			</tr>
		</tbody>
		<tfoot>
			<tr>
				<td>
					<center><form method="post" onsubmit="return confirm('Deseja realmente realizar esta compra?')"><input type="hidden" id="vip-store" name="buy-store" value="0"><button type="submit" style="margin: 10px;">COMPRAR <span id="vip-name">pokémon</span></button></form></center>
				</td>
			</tr>
		</tfoot>
	</table>
</div>


<script>
	var $carousel3 = $('.main-carousel-vip');
    
    $carousel3.flickity({
        pageDots: false,
        initialIndex: 0
    });

	var flkty3 = $carousel3.data('flickity');
	var $input3 = $('#vip-name');
	var $link3 = $('#vip-store');

    $carousel3.on('select.flickity', function() {
		$link3.val($links3[flkty3.selectedIndex]);
		$input3.text($names3[flkty3.selectedIndex]);
	});
	
    $input3.text($names3[flkty3.selectedIndex]);
	$link3.val($links3[flkty3.selectedIndex]);
</script>

</div>

<?php } ?>

<div id="encontrar" class="box-content" style="margin-top: 7px">
    <table class="bordered general u-infos" width="100%">
        <thead><tr><th colspan="4">ONDE ENCONTRAR?</th></tr></thead>
        <tbody style="font-weight: bold;text-align:center;font-size: 13px; text-transform:uppercase">
            <tr>
                <td colspan="2">
                    Torre Fantasma:
                </td>
                <td colspan="2">
                    <img src="<?=$static_url?>/images/layout/events/drops/Ghost.png" title="DROP GHOST" style="width: 24px"/>
                </td>
            </tr>
            <tr>
                <td>
                    Água:
                </td>
                <td>
                    <img src="<?=$static_url?>/images/layout/events/drops/Water.png" title="DROP WATER" style="width: 24px"/>
                </td>
                <td>
                    Praia:
                </td>
                <td>
                    <img src="<?=$static_url?>/images/layout/events/drops/Ice.png" title="DROP ICE" style="width: 24px"/>
                </td>
            </tr>
            <tr>
                <td>
                    Grama:
                </td>
                <td>
                    <img src="<?=$static_url?>/images/layout/events/drops/Grass.png" title="DROP GRASS" style="width: 24px"/>
                </td>
                <td>
                    Dojô:
                </td>
                <td>
                    <img src="<?=$static_url?>/images/layout/events/drops/Fighting.png" title="DROP FIGHTING" style="width: 24px"/>
                </td>
            </tr>
            <tr>
                <td>
                    Gruta:
                </td>
                <td>
                    <img src="<?=$static_url?>/images/layout/events/drops/Ground.png" title="DROP GROUND" style="width: 24px"/>
                </td>
                <td>
                    Lava:
                </td>
                <td>
                    <img src="<?=$static_url?>/images/layout/events/drops/Fire.png" title="DROP FIRE" style="width: 24px"/>
                </td>
            </tr>
            <tr></tr>
        </tbody>
    </table>
</div>

<div id="dias" class="box-content" style="margin-top: 7px">
    <h3 class="title" style="font-size: 13px;
    padding: 10px;
    padding-left: 4px;
    font-weight: bold;
    text-transform: uppercase;
    color: #9eadcd;margin:0">INCIDÊNCIA</h3>
    <table class="bordered general u-infos" width="100%">
        <thead><tr><th>DROP</th>
        <?php
                $days = array('12/05', '13/05', '14/05', '15/05', '16/05', '17/05', '18/05', '19/05');
                for($i = 0; $i < count($days); $i++) {
                    if (date('d/m') == $days[$i]) {
                        echo '<th>*'.$days[$i].'*</th>';
                    } else {
                        echo '<th>'.$days[$i].'</th>';
                    }
                }
        ?>
        </tr></thead>
        <tbody style="font-weight: bold;text-align:center;font-size: 13px; text-transform:uppercase">
            <tr></tr>
            <tr>
                <td>
                    <img src="<?=$static_url?>/images/layout/events/drops/Ghost.png" title="DROP GHOST" style="width: 24px"/>
                </td>
                <?php
                    $ghost_arr = array(1, 2, 2, 2, 2, 3, 3, 3);
                    for($i = 0; $i < count($ghost_arr); $i++) {
                        echo '<td><img src="'.$static_url.'/images/layout/events/drops/'.$ghost_arr[$i].'.png" style="width: 24px"/></td>';
                    }
                ?>
            </tr>
            <tr>
                <td>
                    <img src="<?=$static_url?>/images/layout/events/drops/Water.png" title="DROP WATER" style="width: 24px"/>
                </td>
                <?php
                    $ghost_arr = array(3, 1, 2, 2, 3, 1, 3, 3);
                    for($i = 0; $i < count($ghost_arr); $i++) {
                        echo '<td><img src="'.$static_url.'/images/layout/events/drops/'.$ghost_arr[$i].'.png" style="width: 24px"/></td>';
                    }
                ?>
            </tr>
            <tr>
                <td>
                    <img src="<?=$static_url?>/images/layout/events/drops/Ice.png" title="DROP ICE" style="width: 24px"/>
                </td>
                <?php
                    $ghost_arr = array(2, 1, 2, 1, 3, 2, 2, 3);
                    for($i = 0; $i < count($ghost_arr); $i++) {
                        echo '<td><img src="'.$static_url.'/images/layout/events/drops/'.$ghost_arr[$i].'.png" style="width: 24px"/></td>';
                    }
                ?>
            </tr>
            <tr>
                <td>
                    <img src="<?=$static_url?>/images/layout/events/drops/Grass.png" title="DROP GRASS" style="width: 24px"/>
                </td>
                <?php
                    $ghost_arr = array(3, 1, 1, 3, 2, 1, 3, 3);
                    for($i = 0; $i < count($ghost_arr); $i++) {
                        echo '<td><img src="'.$static_url.'/images/layout/events/drops/'.$ghost_arr[$i].'.png" style="width: 24px"/></td>';
                    }
                ?>
            </tr>
            <tr>
                <td>
                    <img src="<?=$static_url?>/images/layout/events/drops/Fighting.png" title="DROP FIGHTING" style="width: 24px"/>
                </td>
                <?php
                    $ghost_arr = array(1, 2, 3, 2, 1, 3, 2, 3);
                    for($i = 0; $i < count($ghost_arr); $i++) {
                        echo '<td><img src="'.$static_url.'/images/layout/events/drops/'.$ghost_arr[$i].'.png" style="width: 24px"/></td>';
                    }
                ?>
            </tr>
            <tr>
                <td>
                    <img src="<?=$static_url?>/images/layout/events/drops/Ground.png" title="DROP GROUND" style="width: 24px"/>
                </td>
                <?php
                    $ghost_arr = array(1, 2, 3, 3, 1, 1, 3, 3);
                    for($i = 0; $i < count($ghost_arr); $i++) {
                        echo '<td><img src="'.$static_url.'/images/layout/events/drops/'.$ghost_arr[$i].'.png" style="width: 24px"/></td>';
                    }
                ?>
            </tr>
            <tr>
                <td>
                    <img src="<?=$static_url?>/images/layout/events/drops/Fire.png" title="DROP FIRE" style="width: 24px"/>
                </td>
                <?php
                    $ghost_arr = array(1, 3, 1, 2, 1, 2, 2, 3);
                    for($i = 0; $i < count($ghost_arr); $i++) {
                        echo '<td><img src="'.$static_url.'/images/layout/events/drops/'.$ghost_arr[$i].'.png" style="width: 24px"/></td>';
                    }
                ?>
            </tr>
            <tr></tr>
        </tbody>
    </table>
</div>