<?php
if ($gebruiker['in_hand'] == 0) header('Location: index.php');
	
    include_once './app/classes/League.php';
    $button = '';
    $lock = false;
    
    if (DB::exQuery("SELECT * FROM `duel` WHERE `uitdager`='" . $gebruiker['username'] . "' AND (`status`='wait') ORDER BY `id` DESC LIMIT 1")->num_rows == 1) {
        $lock = true;
    }

    $getname = $_GET['player'];
    if ((isset($_POST['duel']))) {

        if (!empty($_POST['naam']))
            $getname = $_POST['naam'];

        if ($_SESSION['naam'] == $_POST['naam'])
            echo '<div class="red">' . $txt['alert_not_yourself'] . '</div>';

        else if ($_POST['bedrag'] < 0)
            echo '<div class="red">' . $txt['alert_unknown_amount'] . '</div>';

        else if (!ctype_digit($_POST['bedrag']))
            echo '<div class="red">' . $txt['alert_unknown_amount'] . '</div>';

        else if ($gebruiker['rank'] < 4) echo '<div class="red">Você não tem RANK SUFICIENTE PARA DUELAR!</div>';

        else if ($gebruiker['silver'] < $_POST['bedrag'])
            echo '<div class="red">' . $txt['alert_not_enough_silver'] . '</div>';

        else if (DB::exQuery("SELECT `id` FROM `pokemon_speler` WHERE `leven`>'0' AND `user_id`='" . $_SESSION['id'] . "' AND opzak='ja'")->num_rows == 0)
            echo '<div class="red">' . $txt['alert_all_pokemon_ko'] . '</div>';

        else {
            $sql = DB::exQuery("SELECT user_id, username, wereld, rank, premiumaccount, `character`, dueluitnodiging, pagina, `online`,`blocklist` FROM gebruikers WHERE username='" . $_POST['naam'] . "'");

            if ($sql->num_rows == 1) {

                $select = $sql->fetch_assoc();
                
                $blocklist_1 = explode(',', $gebruiker['blocklist']);
                $blocklist_2 = explode(',', $select['blocklist']);

                if ($select['wereld'] != $gebruiker['wereld'])
                    echo '<div class="red">' . $_POST['naam'] . ' ' . $txt['alert_opponent_not_in'] . ' ' . $gebruiker['wereld'] . '.</div>';

                else if ($select['rank'] < 4) echo '<div class="red">' . $_POST['naam'] . ' ' . ' não tem RANK SUFICIENTE!</div>';

                else if ($select['dueluitnodiging'] == 0)
                    echo '<div class="red">' . $_POST['naam'] . ' ' . $txt['alert_opponent_duelevent_off'] . '</div>';

                else if (($select['pagina'] == "attack") || ($select['pagina'] == "attack-trainer") || ($select['pagina'] == "duel"))
                    echo '<div class="red">' . $_POST['naam'] . ' ' . $txt['alert_opponent_already_fighting'] . '</div>';
                else if (DB::exQuery("SELECT * FROM league_battle WHERE (user_id1 = '" . $select['user_id'] . "' OR user_id2 = '" . $select['user_id'] . "') AND ((NOW()" . League::$ajuste_tempo_string . ") BETWEEN (inicio - INTERVAL 5 MINUTE - INTERVAL 5 SECOND) AND inicio)")->num_rows >0)
                    echo '<div class="red">' . $_POST['naam'] . ' Seu oponete está se preparando para uma batalha na liga pokémon</div>';
                else if (($select['online'] + 900) <= time()) 
                    echo '<div class="red">' . $_POST['naam'] . ' está <b>OFFLINE</b>!</div>';
                else if ($lock)
                    echo '<div class="red">Você já desafiou algum treinador! Por favor, aguarde a resposta!</div>';
                else if (in_array($select['user_id'], $blocklist_1))
                    echo '<div class="red">Você bloqueou este treinador!</div>';
                else if (in_array($_SESSION['id'], $blocklist_2))
                    echo '<div class="red">Você foi bloqueado por este treinador!</div>';
                else {
                    $date = strtotime(date("Y-m-d H:i:s"));
                    DB::exQuery("INSERT INTO duel (datum, uitdager, tegenstander, u_character, t_character, bedrag, status, laatste_beurt_tijd, laatste_beurt)
                                 VALUES ('" . $date . "', '" . $_SESSION['naam'] . "', '" . $select['username'] . "', '" . $gebruiker['character'] . "', '" . $select['character'] . "', '" . $_POST['bedrag'] . "', 'wait', '" . $date . "', '" . $_SESSION['naam'] . "')");

                    $duel_id = DB::insertID();
                    $_SESSION['duel']['duel_id'] = $duel_id;

                    #Include Duel Functions
                    include_once('duel-start.php');
                    #Start Duel
                    start_duel($duel_id, 'uitdager');
                    
                    $chance = rand(1,2);
                    $background = "duelo-".$chance."";
                    $_SESSION['background'] = $background;
                    DB::exQuery("UPDATE `gebruikers` SET `background`='$background' WHERE `user_id`='".$_SESSION['id']."'"); 
                    $lock = true;                
                }
            } else
                echo '<div class="red">' . $txt['alert_user_unknown'] . '</div>';
        }
    }

    if ($gebruiker['rank'] < 4) {
        echo '<div class="red">RANK MÍNIMO PARA DESAFIAR OUTROS TREINADORES: 4 - TRAINER. CONTINUE UPANDO PARA LIBERAR!</div>';
    }

    if (!$lock) {
    ?>
            <div class="blue"><img src="<?=$static_url?>/images/icons/duel.png" style="vertical-align: bottom;"> <strong>Desafie um treinador para um duelo.</strong> <img src="<?=$static_url?>/images/icons/duel.png" style="vertical-align: bottom;"><br>
                O Treinador deve estar online.</div>
                <div class="box-content">
            <h3 class="title">DESAFIAR</h3>
            <form method="post" onsubmit="return confirm('Deseja realmente desafiar este Treinador?');">
                <table width="37%" border="0" style="margin: 10px; text-align: center; padding: 10px">
                <tr>
                    <td><b style="color: #9eadcd; font-size: 12px">Treinador:</b><br><input type="text" name="naam" value="<?php echo $getname; ?>" id="player" class="input-blue" required style="margin-top: 5px"/></td>
                    <td><b style="color: #9eadcd; font-size: 12px">Valor:</b><br><input type="number" name="bedrag" value="<?php
                        if (!empty($_POST['bedrag']))
                            echo $_POST['bedrag'];
                        else
                            echo 0;
                        ?>" class="input-blue" min="0" style="margin-top: 5px"/></td>
                </tr>
                </table>
                <div style="border-top: 1px solid #577599;"><input type="submit" name="duel" value="<?php echo $txt['button_duel']; ?>" class="button" <?php echo $button; ?> <?=($lock)? 'disabled' : ''?> style="margin: 6px"></div>
            </form>
            </div>
<?php } else {     
    $duel2_sql = DB::exQuery("SELECT * FROM `duel` WHERE `uitdager`='" . $gebruiker['username'] . "' AND (`status`='wait') ORDER BY `id` DESC LIMIT 1");
    $duel2 = $duel2_sql->fetch_assoc();
    
    // include("app/classes/Utils.php");

    $gb = new Gebruikers();
    $ps = new PokemonSpeler();

    $infos = $gb->getInfos($duel2['tegenstander'], '`user_id`, `exibepokes`')->fetch_assoc();

    echo '<div class="blue">' . $duel2['tegenstander'] . ' ' . $txt['waiting_for_accept'] . '<br /><br />
                            Status: <span id="status">Esperando..</span></div>';
                    ?>

                    <script type="text/javascript">
                        var t
                        function status_check() {
                            $.get("attack/duel/status_check.php?duel_id=" +<?php echo $duel2['id']; ?> + "&sid=" + Math.random(), function(data) {
                                if (data == 0) {
                                    $("#status").html("Esperando<span class='dots'></span>")
                                    t = setTimeout('status_check()', 5000);
                                }
                                else if (data == 1) {
                                    $("#status").html("Expirado.")
                                    clearTimeout(t)
                                }
                                else if (data == 2) {
                                    $("#status").html("Recusado.")
                                    clearTimeout(t)
                                }
                                else if (data == 3) {
                                    $("#status").html("Aceito.")
                                    clearTimeout(t)
                                    setTimeout("location.href='./attack/duel/duel-attack'", 0)
                                }
                                else if (data == 4) {
                                    $("#status").html("<?php echo $txt['alert_opponent_no_silver']; ?>")
                                    clearTimeout(t)
                                }
                                else if (data == 5) {
                                    $("#status").html("<?php echo $txt['alert_opponent_no_health']; ?>")
                                    clearTimeout(t)
                                }
                            });
                        }
                        status_check();
                        
                        setInterval(() => {
                            let text = $('.dots').text()+'.';
                            $('.dots').text(text);
                        }, 1666);
                    </script>
                    <div class="row">
    <div style="width: 27%;" class="col">
      <div id="npc-section" style="background: url('public/images/layout/starProfile.png') no-repeat #34465f; background-position: center;height: 185px; border-top-right-radius: 0; border-bottom-right-radius: 0; border-right: 1px solid #577599;">
        <div id="npc-image" style="background: url('public/images/characters/<?=$gebruiker['character']?>/npc.png') center center no-repeat; background-size: 100% 100%; height: 180px;width: 160px;margin-top: 5px;"></div>
      </div>
    </div>
    
		<div style="width: 46%;" class="col">
				<div id="npc-section" class="row" style="height: 185px;border-radius: 0;border-right: unset;">
          <div class="col" style="margin: 18px 0px 0px 13px; width: 146px;">
            <h3 class="title" style="padding: 5px;background: url(public/images/layout/line.png) no-repeat;background-position: bottom; background-size: 70%; font-size: 21px; margin: 5px 0 0; font-weight: bold; text-transform: uppercase; color: #9eadcd;"><?=$gebruiker['username']?></h3>
            <div class="duel-pokemon" style="float: right">
                <?php if ($gebruiker['in_hand'] > 0) { 
                        $pkm_count = 6;
                        while($pokemon = $pokemon_sql->fetch_assoc()) {
                            $pokemon = pokemonei($pokemon, $txt);
                            $popup = pokemon_popup($pokemon, $txt);
                            $pokemon['naam'] = pokemon_naam($pokemon['naam'], $pokemon['roepnaam'], $pokemon['icon']);

                            echo '<div class="icon"><div style="background-image: url(\'' . $static_url . '/' . $pokemon['animatie'] . '\');" class="tip_bottom-left' . ($pokemon['leven'] < 1 ? ' dead' : '') . '" title="' . $popup . '"></div></div>';

                            $pkm_count--;
                        }

                        for ($i = 0; $i < $pkm_count; $i++) echo '<div class="icon"></div>';
                        $pokemon_sql->data_seek(0);
                    } else { 
                        for ($i = 0; $i < 6; $i++) echo '<div class="icon"></div>';
                    }
                    ?>
                </div>
          </div>   
          <div class="col" style="width: 121px;">
			      <img src="public/images/icons/avatar/vs.png" style="width: 119%;margin-top: 28px;">		
          </div>
          <div class="col" style="margin: 18px 0px 0px 13px;width: 146px;">
              <h3 class="title" style="padding: 5px;background: url(public/images/layout/line.png) no-repeat;background-position: bottom; background-size: 70%; font-size: 21px; margin: 5px 0 0; font-weight: bold; text-transform: uppercase; color: #9eadcd;"><?=$duel2['tegenstander']?></h3>
              <div class="duel-pokemon" style="float: right">
              <?php if ($infos['exibepokes'] == 'sim') { 
                        $pkm_count = 6;
                        $pinfos = $ps->getInfos($infos['user_id']);

                        while($pokemon = $pinfos->fetch_assoc()) {
                            $pokemon = pokemonei($pokemon, $txt);
                            $popup = pokemon_popup($pokemon, $txt);
                            $pokemon['naam'] = pokemon_naam($pokemon['naam'], $pokemon['roepnaam'], $pokemon['icon']);

                            echo '<div class="icon"><div style="background-image: url(\'' . $static_url . '/' . $pokemon['animatie'] . '\');" class="tip_bottom-left' . ($pokemon['leven'] < 1 ? ' dead' : '') . '" title="' . $popup . '"></div></div>';

                            $pkm_count--;
                        }

                        for ($i = 0; $i < $pkm_count; $i++) echo '<div class="icon"></div>';
                    } else { 
                        for ($i = 0; $i < 6; $i++) echo '<div class="icon"></div>';
                    }
                    ?>
              </div>
          </div>  
        </div>     	
    </div>
    
    <div style="width: 27%;" class="col">
				<div id="npc-section" style="background: url('public/images/layout/starProfile.png') no-repeat #34465f;background-position: center;height: 185px;border-top-left-radius: 0;border-bottom-left-radius: 0;border-left: 1px solid #577599;">
					<div id="npc-image" style="background: url('public/images/characters/<?=$duel2['t_character']?>/npc.png') center center no-repeat;background-size: 100% 100%;height: 180px;width: 160px;margin-top: 5px;"></div>
				</div>
		</div>
</div>
     
<?php } ?>