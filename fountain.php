<?php
echo addNPCBox(30, 'Bem vindo a fonte da Juventude', 'Aqui você poderá rejuvenescer seu Pokémon e após utilizar a fonte da juventude:<br>- Seu Pokémon voltará para o Level 5;<br>- Todas suas vitaminas, golpes aprendidos e EVs serão zeradas.<br><br>Será mantido apenas o humor do seu Pokémon e seus atributos de IVs, ou seja, tanto o JUIZ quanto a CALCULADORA não sofrerão alterações! <br>Se você tiver PREMIUM o custo será reduzido em 20%!');

//Script laden zodat je nooit pagina buiten de index om kan laden
include('app/includes/resources/security.php');

// if($gebruiker['rank'] < 4)	exit(header("LOCATION: ./"));

$page = 'wondertrade';
//Goeie taal erbij laden voor de page
include_once('language/language-pages.php');

echo addNPCBox(85, $txt['text_npc'], ''.$txt['text_npc1'].' '.number_format($valor).' ' .$txt['text_npc2']. '.');

if ($gebruiker['rank'] >= 4) {
#Wil de speler een starter ei
if(isset($_POST['normal'])){
	$pokeId = $_POST['who'];
	$poke = DB::exQuery("SELECT `pw`.`naam`,`pw`.`type1`,`pw`.`type2`,`pw`.`zeldzaamheid`,`pw`.`groei`,`pw`.`aanval_1`,`pw`.`aanval_2`,`pw`.`aanval_3`,`pw`.`aanval_4`,`ps`.* FROM `pokemon_wild` AS `pw` INNER JOIN `pokemon_speler` AS `ps` ON `ps`.`wild_id` = `pw`.`wild_id` WHERE `ps`.`user_id`='{$_SESSION['id']}' AND `ps`.`opzak`='ja' AND `ps`.`id` = '{$pokeId}'")->fetch_assoc();

	#Hoeveel gold nodig?
	$price = 15000;
	$price *= $poke['zeldzaamheid'];
	if ($poke['poke_reset'] == 1)
		$price *= 2;
	elseif ($poke['poke_reset'] == 2)
		$price *= 3;
		
	if ($gebruiker['premiumaccount'] >= 1)
		$price -= $price * 0.20;
	
	
	if ($poke['poke_reset'] == 3 || $poke['ei'] == 1) $price = '--';

	if (!$poke)											$error = 'Você não escolheu um pokémon válido!';
	elseif ($poke['user_id'] != $_SESSION['id'])		$error = 'Este pokémon não pertence a você!';
	elseif ($poke['ei'] == 1)							$error = 'Este pokémon ainda é um ovo.';
	elseif ($poke['poke_reset'] >= 3)					$error = 'Esse pokémon já foi rejuvenescido muitas vezes.';
	elseif ($poke['opzak'] != 'ja')						$error = 'Esse pokémon não está no seu time.';
	elseif ($poke['level'] == 5)						$error = 'Este pokémon não pode ser rejuvenescido.';
	elseif ($gebruiker['silver'] < $price)				$error = 'Você não tem silvers suficientes.';
	else {
		$update				= [];
		$update['level']	= 5;

		$base		= DB::exQuery("SELECT * FROM `pokemon_wild` WHERE `wild_id` = '{$poke['wild_id']}' LIMIT 1")->fetch_assoc();
		$character	= DB::exQuery("SELECT * FROM `karakters` WHERE `karakter_naam` = '{$poke['karakter']}' LIMIT 1")->fetch_assoc();
		$expInfo	= DB::exQuery("SELECT `punten` FROM `experience` WHERE `soort`='{$base['groei']}' AND `level`='{$update['level']}' LIMIT 1")->fetch_assoc();

		$update['exp']		= 0;
		$update['expnodig']	= $expInfo['punten'];
		$update['totalexp']	= 0;

		$update['aanval_1']	= $base['aanval_1'];
		$update['aanval_2']	= $base['aanval_2'];
		$update['aanval_3']	= $base['aanval_3'];
		$update['aanval_4']	= $base['aanval_4'];

		$update['effect']	= '';

		$update['attack_up']	= 0;
		$update['defence_up']	= 0;
		$update['speed_up']		= 0;
		$update['spc_up']		= 0;
		$update['spc_up']		= 0;
		$update['hp_up']		= 0;

		$update['attack']		= round(((($poke['attack_iv'] + 2 * $base['attack_base']) * $update['level'] / 100) + 5) * $character['attack_add']);
		$update['defence']		= round(((($poke['defence_iv'] + 2 * $base['defence_base']) * $update['level'] / 100) + 5) * $character['defence_add']);
		$update['speed']		= round(((($poke['speed_iv'] + 2 * $base['speed_base']) * $update['level'] / 100) + 5) * $character['speed_add']);
		$update['spc.attack']	= round(((($poke['spcattack_iv'] + 2 * $base['spc.attack_base']) * $update['level'] / 100) + 5) * $character['spc.attack_add']);
		$update['spc.defence']	= round(((($poke['spcdefence_iv'] + 2 * $base['spc.defence_base']) * $update['level'] / 100) + 5) * $character['spc.defence_add']);
		$update['levenmax']		= round((($poke['hp_iv'] + 2 * $base['hp_base']) * $update['level'] / 100) + 10 + $update['level']);
		$update['leven']		= $update['levenmax'];

		$update['attack_ev']		= 0;
		$update['defence_ev']		= 0;
		$update['speed_ev']			= 0;
		$update['spc.attack_ev']	= 0;
		$update['spc.defence_ev']	= 0;
		$update['hp_ev']			= 0;

		$update['poke_reset']	= $poke['poke_reset'] + 1;

		$add_sql = [];
		foreach ($update as $key => $value)
			$add_sql[] = "`{$key}`='{$value}'";
		DB::exQuery("UPDATE `pokemon_speler` SET " . implode(', ', $add_sql) . " WHERE `id` = '{$poke['id']}'") or die(mysql_error());

		DB::exQuery("UPDATE `gebruikers` SET `silver`=`silver`-'{$price}' WHERE `user_id` = '{$_SESSION['id']}'") or die(mysql_error());

		$success = 'Seu ' . $poke['naam'] . ' foi rejuvenescido!';
	}
}

$bag = true;
$total_bag = 0;
if(isset($_POST['premium'])){
	$pokeId = $_POST['who'];
	$poke = DB::exQuery("SELECT `pw`.`naam`,`pw`.`type1`,`pw`.`type2`,`pw`.`zeldzaamheid`,`pw`.`groei`,`pw`.`aanval_1`,`pw`.`aanval_2`,`pw`.`aanval_3`,`pw`.`aanval_4`,`ps`.* FROM `pokemon_wild` AS `pw` INNER JOIN `pokemon_speler` AS `ps` ON `ps`.`wild_id` = `pw`.`wild_id` WHERE `ps`.`user_id`='{$_SESSION['id']}' AND `ps`.`opzak`='ja' AND `ps`.`id` = '{$pokeId}'")->fetch_assoc();

	#Hoeveel gold nodig?
	$price = 15000;
	$price *= $poke['zeldzaamheid'];
	if ($poke['poke_reset'] == 1)
		$price *= 2;
	elseif ($poke['poke_reset'] == 2)
		$price *= 3;
	elseif ($poke['poke_reset'] == 3)
		$price *= 4;
		
	if ($gebruiker['premiumaccount'] >= 1)
		$price -= $price * 0.20;
	
	$price = $price * 3;

	if (!$poke)											$error = 'Você não escolheu um pokémon válido!';
	elseif ($poke['user_id'] != $_SESSION['id'])		$error = 'Este pokémon não pertence a você!';
	elseif ($poke['ei'] == 1)							$error = 'Este pokémon ainda é um ovo.';
	elseif ($poke['opzak'] != 'ja')						$error = 'Esse pokémon não está no seu time.';
	elseif ($poke['level'] == 5)						$error = 'Este pokémon não pode ser rejuvenescido.';
	elseif ($gebruiker['silver'] < $price)				$error = 'Você não tem silvers suficientes.';
	else {
		$update				= [];
		$update['level']	= 5;
		$text_add = "";
		#Primeira Evolução
		$evoluide = DB::exQuery("SELECT * FROM levelen where nieuw_id = '".$poke['wild_id']."' and wat='evo' limit 1");
		if($evoluide->num_rows != 0){
			$evoluidex = $evoluide->fetch_assoc();
			$poke['wild_id'] = $evoluidex['wild_id'];
			if(!empty($evoluidex['stone'])) {
				if ($gebruiker['item_over'] > 0) {
					DB::exQuery("UPDATE `gebruikers_item` SET `".$evoluidex['stone']."`=`".$evoluidex['stone']."`+'1' WHERE `user_id`='".$_SESSION['id']."'");
					$text_add = "1x <img src='".$static_url."/images/items/".$evoluidex['stone'].".png' title='".$evoluidex['stone']."' style='vertical-align: -3px;' />,";
					$item = $evoluidex['stone'];
				} else {
					$bag = false;
					$total_bag += 1;
				}
			}
			
			#Primeira Evolução
			$evoluide = DB::exQuery("SELECT * FROM levelen where nieuw_id = '".$poke['wild_id']."' and wat='evo' limit 1");
			if($evoluide->num_rows != 0){
				$evoluidex = $evoluide->fetch_assoc();
				$poke['wild_id'] = $evoluidex['wild_id'];
				
				#Terceira Evolução
				$evoluide = DB::exQuery("SELECT * FROM levelen where nieuw_id = '".$poke['wild_id']."' and wat='evo' limit 1");
				if($evoluide->num_rows != 0){
					$evoluidex = $evoluide->fetch_assoc();
					$poke['wild_id'] = $evoluidex['wild_id'];
					
					#Quarta Evolução
					$evoluide = DB::exQuery("SELECT * FROM levelen where nieuw_id = '".$poke['wild_id']."' and wat='evo' limit 1");
					if($evoluide->num_rows != 0){
						$evoluidex = $evoluide->fetch_assoc();
						$poke['wild_id'] = $evoluidex['wild_id'];
					}
				}
			}
		}
		
		$base		= DB::exQuery("SELECT * FROM `pokemon_wild` WHERE `wild_id` = '{$poke['wild_id']}' LIMIT 1")->fetch_assoc();
		$character	= DB::exQuery("SELECT * FROM `karakters` WHERE `karakter_naam` = '{$poke['karakter']}' LIMIT 1")->fetch_assoc();
		$expInfo	= DB::exQuery("SELECT `punten` FROM `experience` WHERE `soort`='{$base['groei']}' AND `level`='{$update['level']}' LIMIT 1")->fetch_assoc();

		$update['exp']		= 0;
		$update['expnodig']	= $expInfo['punten'];
		$update['totalexp']	= 0;

		$update['aanval_1']	= $base['aanval_1'];
		$update['aanval_2']	= $base['aanval_2'];
		$update['aanval_3']	= $base['aanval_3'];
		$update['aanval_4']	= $base['aanval_4'];

		$update['effect']	= '';
		
		if($poke['attack_up'] > 0){
			$wat = "Protein";
			$total = $poke['attack_up'] / 3;
			if ($gebruiker['item_over'] >= $total) {
				DB::exQuery("UPDATE `gebruikers_item` SET `" . $wat . "`=`" . $wat . "`+'".$total."' WHERE `user_id`='" . $_SESSION['id'] . "' LIMIT 1");
				$text_add .= $total."x <img src='".$static_url."/images/items/".$wat.".png' title='".$wat."' style='vertical-align: -3px;' />,";
			} else {
				$bag = false;
				$total_bag += $total;
			}
		}

		if($poke['defence_up'] > 0){
			$wat = "Iron";
			$total = $poke['defence_up'] / 3;
			if ($gebruiker['item_over'] >= $total) {
				DB::exQuery("UPDATE `gebruikers_item` SET `" . $wat . "`=`" . $wat . "`+'".$total."' WHERE `user_id`='" . $_SESSION['id'] . "' LIMIT 1");
				$text_add .= $total."x <img src='".$static_url."/images/items/".$wat.".png' title='".$wat."' style='vertical-align: -3px;' />,";
			} else {
				$bag = false;
				$total_bag += $total;
			}
		}
		
		if($poke['speed_up'] > 0){
			$wat = "Carbos";
			$total = $poke['speed_up'] / 3;
			if ($gebruiker['item_over'] >= $total) {
				DB::exQuery("UPDATE `gebruikers_item` SET `" . $wat . "`=`" . $wat . "`+'".$total."' WHERE `user_id`='" . $_SESSION['id'] . "' LIMIT 1");
				$text_add .= $total."x <img src='".$static_url."/images/items/".$wat.".png' title='".$wat."' style='vertical-align: -3px;' />,";
			} else {
				$bag = false;
				$total_bag += $total;
			}
		}
		
		if($poke['spc_up'] > 0){
			$wat = "Calcium";
			$total = $poke['spc_up'] / 3;
			if ($gebruiker['item_over'] >= $total) {
				DB::exQuery("UPDATE `gebruikers_item` SET `" . $wat . "`=`" . $wat . "`+'".$total."' WHERE `user_id`='" . $_SESSION['id'] . "' LIMIT 1");
				$text_add .= $total."x <img src='".$static_url."/images/items/".$wat.".png' title='".$wat."' style='vertical-align: -3px;' />,";
			} else {
				$bag = false;
				$total_bag += $total;
			}
		}
		
		if($poke['hp_up'] > 0){
			$wat = "HP up";
			$total = $poke['hp_up'] / 3;
			if ($gebruiker['item_over'] >= $total) {
				DB::exQuery("UPDATE `gebruikers_item` SET `" . $wat . "`=`" . $wat . "`+'".$total."' WHERE `user_id`='" . $_SESSION['id'] . "' LIMIT 1");
				$text_add .= $total."x <img src='".$static_url."/images/items/".$wat.".png' title='".$wat."' style='vertical-align: -3px;' />,";
			} else {
				$bag = false;
				$total_bag += $total;
			}
		}
		
		if ($bag) {
			$update['attack_up']	= 0;
			$update['defence_up']	= 0;
			$update['speed_up']		= 0;
			$update['spc_up']		= 0;
			$update['spc_up']		= 0;
			$update['hp_up']		= 0;

			$update['attack']		= round(((($poke['attack_iv'] + 2 * $base['attack_base']) * $update['level'] / 100) + 5) * $character['attack_add']);
			$update['defence']		= round(((($poke['defence_iv'] + 2 * $base['defence_base']) * $update['level'] / 100) + 5) * $character['defence_add']);
			$update['speed']		= round(((($poke['speed_iv'] + 2 * $base['speed_base']) * $update['level'] / 100) + 5) * $character['speed_add']);
			$update['spc.attack']	= round(((($poke['spcattack_iv'] + 2 * $base['spc.attack_base']) * $update['level'] / 100) + 5) * $character['spc.attack_add']);
			$update['spc.defence']	= round(((($poke['spcdefence_iv'] + 2 * $base['spc.defence_base']) * $update['level'] / 100) + 5) * $character['spc.defence_add']);
			$update['levenmax']		= round((($poke['hp_iv'] + 2 * $base['hp_base']) * $update['level'] / 100) + 10 + $update['level']);
			$update['leven']		= $update['levenmax'];
			$update['wild_id']		= $poke['wild_id'];

			$update['attack_ev']		= 0;
			$update['defence_ev']		= 0;
			$update['speed_ev']			= 0;
			$update['spc.attack_ev']	= 0;
			$update['spc.defence_ev']	= 0;
			$update['hp_ev']			= 0;
			$update['humor_change'] 	= 0;

			$add_sql = [];
			foreach ($update as $key => $value)
				$add_sql[] = "`{$key}`='{$value}'";
			DB::exQuery("UPDATE `pokemon_speler` SET " . implode(', ', $add_sql) . " WHERE `id` = '{$poke['id']}'") or die(mysql_error());

			DB::exQuery("UPDATE `gebruikers` SET `silver`=`silver`-'{$price}' WHERE `user_id` = '{$_SESSION['id']}'") or die(mysql_error());
			
			if(!empty($text_add)) $text_add = "Você recebeu ".$text_add." de volta.";
			if(!empty($text_add)) echo '<div class="blue">Feito, seu pokémon voltou ao normal. '.$text_add.'</div>';
			$event = 'O jogador '.$gebruiker['username'].' rejuvelheceu seu Pokémon ID: '.$poke['id'].' e recebeu um '.$item;
							DB::exQuery("INSERT INTO `fountain_logs` (`acc_id`,`user_id`,`msg`,`pkmid`) 
								VALUES ('".$rekening['acc_id']."','".$gebruiker['user_id']."','".$event."','".$poke['id']."')"); 
			
			$success = 'Seu ' . $poke['naam'] . ' foi rejuvenescido por '.$price.' Silvers!';
		} else {
			$error = 'Você não têm espaço suficiente em sua Mochila! (Necessário: '.$total_bag.'x).';
		}
	}
}
} else {
        echo '<div class="red">RANK MÍNIMO PARA VER AS IVs DOS POKÉMONS: 4 - TRAINER. CONTINUE UPANDO PARA LIBERAR!</div>';
    }
if(isset($error)) echo '<div class="red">'.$error.'</div>';
if(isset($success)) echo '<div class="green">'.$success.'</div>';

//';
//<img src="'.$static_url.'/images/icons/silver.png" title="Silver" style="margin-bottom:-3px;"> ' . $price . '
//<tfoot><tr><td align="left" colspan="1"><input type="submit" name="premium" value="Premium" title="<center>Essa restauração <b>custa 3x o valor</b> do normal, mas tem os seguintes beneficios:<br>- Devolve Mega Stone;<br>- Devolve Vitaminas;<br>- Volta a forma inicial;<br>- Reseta as mudanças de humor;<br>- Pode resetar quantas vezes quiser!" class="button" disabled /></td><td align="right" colspan="2"><input type="submit" name="normal" value="Normal" title="<center>Clique para restaurar o seu Pokémon!" class="button" disabled /></td></tr></tfoot>
?>

<style>
            .alternate:hover {
                background-color: rgba(0, 0, 0, .1);
                transition: 1s;
            }
            .carousel-cell {
                margin: 10px 10px;
                filter: grayscale(100%);
                transform: scale(0.85);
                overflow: hidden;
            }
            .carousel-cell.is-selected {
                filter: grayscale(20%) invert(8%);
                transition: 1s;
                transform: scale(1);
            }
        </style>
        <div class="box-content" style="width: 100%">
            <table width="100%" class="general">
                <thead><tr><th colspan="6">Minha equipe</th></tr></thead>
                <tbody><tr>
                        <script>
                            var $poke_array_id = [];
                            var $poke_array_iid = [];
                            var $poke_array_name = [];
                            var $poke_array_spe = [];
							var $poke_array_price = [];
                        </script>

                        <td style="padding: 0" colspan="2">
                            <div class="main-carousel" style="height: 97px; position: relative">
                                <?php
                                    while($pokemon_profile = $pokemon_sql->fetch_assoc()) {
										$price = 15000;

										$price *= $pokemon_profile['zeldzaamheid'];
										if ($pokemon_profile['poke_reset'] == 1)
											$price *= 2;
										elseif ($pokemon_profile['poke_reset'] == 2)
											$price *= 3;
										elseif ($pokemon_profile['poke_reset'] == 3)	
											$price *= 4;
										if ($gebruiker['premiumaccount'] >= 1)
											$price -= $price * 0.20;

                                        $pokemon_profile = pokemonei($pokemon_profile, $txt);
                                        $of_name = $pokemon_profile['naam'];
                                        $popup = pokemon_popup($pokemon_profile, $txt);
                                        $pokemon_profile['naam'] = pokemon_naam($pokemon_profile['naam'], $pokemon_profile['roepnaam'], $pokemon_profile['icon']);
                                ?>
                                        <div class="carousel-cell" style="text-align: center;">
                                            <div style="display:table-cell; vertical-align:middle; min-width: 150px; height: 150px;">
                                                <?='<img id="my_pokes_infos" class="tip_bottom-middle" title="'.$popup.'" src="' . $static_url . '/'.$pokemon_profile['link'].'" />';?>
                                                <script id="remove">
                                                    $poke_array_id.push("<?=$pokemon_profile['wild_id']?>");
                                                    $poke_array_iid.push("<?=$pokemon_profile['id']?>");
                                                    $poke_array_name.push("<?=$of_name?>");
                                                    $poke_array_spe.push("<?=$pokemon_profile['naam'].'<sup title=\"Quantidade de Usos da Fonte Básica\">('.$pokemon_profile['poke_reset'].'x)</sup>'?>");
													$poke_array_price.push("<?=$price?>");

                                                    document.querySelector('#remove').outerHTML = '';
                                                </script>
                                            </div>
                                        </div>
                                <?php
                                    }
                                ?>
                            </div>
                            <div style="width: 100%; background: rgba(0, 0, 0, .3); position: relative; bottom: 0; text-align: center; height: 53px; padding-top: 3px; margin-top: -8px; border-bottom-right-radius: 2px;  border-bottom-left-radius: 2px">
                                <div style="width: 100%; text-align: center; font-size: 17px; margin-top: 3px">
                                    <h4 id="poke_name" style="margin: 0; color: #eee; font-weight: bold;"></h4>
                                    <a href="./pokedex&poke=1" id="poke_link" style="color: #eee; font-size: 13px"></a>
                                </div>
                            </div>
                        </td>
                </tr></tbody>
                <tfoot>

                <tr style="text-align: center; font-size: 13px">
					<form method="post"><input type="hidden" name="who" value="" id="poke_id"/>
                            	
                    <td class="row">
                        <div class="col alternate" style="border-right: 1px solid #577599;">
                            <h3 class="title" style="font-size: 15px">FONTE BÁSICA</h3>

                            <div style="padding: 10px; padding-bottom: 0; text-align: left; border-bottom: 1px solid #577599;">
                                <ul>
                                    <li>Com a Fonte Básica, você pode restaurar seu Pokémon para o <b>Level 5</b> e <b>Zerar</b> suas EV's e Vitaminas.</li>
                                    <li>Cada Pokémon pode passar pela Fonte Básica <b>3 vezes</b>.</li>
                                    <li style="margin-top: 42px"><b>Preço: </b><img src="<?=$static_url?>/images/icons/silver.png" title="Silvers" style="vertical-align: bottom"> <span id="price-basic">0</span></li>
                                </ul>
                            </div>
							<input type="submit" class="button" name="normal" style="margin: 6px" <?=($gebruiker['rank'] >= 4)? '' : 'disabled'?> value="PASSAR PELA FONTE BÁSICA">
                        </div>
                
                        <div class="col alternate" style="width: 65%">
                            <h3 class="title" style="font-size: 15px">FONTE PREMIUM</h3>

                            <div style="padding: 10px; padding-bottom: 0; text-align: left; border-bottom: 1px solid #577599;">
                                <ul>
                                    <li>Com a Fonte Premium você terá a mesma bonificação da Básica, contudo, as <b>Vitaminas</b> e <b>Mega Stones</b> voltam para seu Inventário, além do Pokémon voltar para sua <b>Primeira Forma</b>!</li>
                                    <li>O preço é <b>3x</b> à mais que a Básica, mas pode ser usada quantas vezes quiser!</li>
                                    <li style="margin-top: 10px"><b>Preço: </b><img src="<?=$static_url?>/images/icons/silver.png" title="Silvers" style="vertical-align: bottom"> <span id="price-advanced">0</span></li>
                                </ul>
                            </div>
                            <input type="submit" class="button" name="premium" style="margin: 6px" <?=($gebruiker['rank'] >= 4)? '' : 'disabled'?> value="PASSAR PELA FONTE PREMIUM">
                        </div>
                    </td>

					</form>
                </tr>
                </tfoot>
            </table>
        </div>

<script>
            var $carousel = $('.main-carousel');
            var $poke_name = $('#poke_name');
            var $poke_link = $('#poke_link');
            var $poke_id = $('#poke_id');
			var $price_1 = $('#price-basic');
			var $price_2 = $('#price-advanced');

            var $car = $carousel.flickity({
                cellAlign: 'center',
                contain: false,
                pageDots: false,
                wrapAround: false,
                autoPlay: false
            });

            var flkty = $carousel.data('flickity');

            $carousel.on('select.flickity', function() {
                $poke_link.attr('href', './pokedex&poke='+$poke_array_id[flkty.selectedIndex]);
                $poke_link.html($poke_array_name[flkty.selectedIndex]);
                $poke_name.html($poke_array_spe[flkty.selectedIndex]);
				$price_1.html($poke_array_price[flkty.selectedIndex]);
				$price_2.html(($poke_array_price[flkty.selectedIndex] * 3));

                $poke_id.val ($poke_array_iid[flkty.selectedIndex]);
            });

            $poke_link.attr('href', '/pokedex&poke='+$poke_array_id[0]);
            $poke_link.html($poke_array_name[0]);
            $poke_name.html($poke_array_spe[0]);
			$price_1.html($poke_array_price[0]);
			$price_2.html(($poke_array_price[0] * 3));

            $poke_id.val ($poke_array_iid[0]);

            $car.resize();

            function view_ivs ( $param = 1 ) {
                if ($param == 1 || $param == 2) {
                    $('#poke_view').val ($param);
                    $('#calc').submit();
                }
            }
        </script>