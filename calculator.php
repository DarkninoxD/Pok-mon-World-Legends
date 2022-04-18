<?php
    #include dit script als je de pagina alleen kunt zien als je ingelogd bent.
    include('app/includes/resources/security.php');
     
    #Als je geen pokemon bij je hebt, terug naar index.
    if ($gebruiker['in_hand'] == 0) header('Location: index.php');

    echo addNPCBox(30, 'Calculadora Pokémon', 'Com a Calculadora Pokémon você terá a certeza ou não se o seu 
    Pokémon é forte o suficiente para se dar bem no mundo de <b>Pokémon World Legends</b>!<br><br>
    Existem dois tipos de Calculadora a <b>SIMPLES</b> e a <b>PREMIUM</b>!<br><br>
    Aproveite esta função o máximo e com isso, torne-se o melhor Mestre Pokémon!');

    function calcIV() {
        $p = func_get_args();

        if ($p[0] >= 25 && $p[0] <= 31) {
            return '25 - 31';
        }else if ($p[0] >= 20 && $p[0] <= 25) {
            return '20 - 25';
        }else if ($p[0] >= 15 && $p[0] <= 20) {
            return '15 - 20';
        }else if ($p[0] >= 10 && $p[0] <= 15) {
            return '10 - 15';
        }else if ($p[0] >= 05 && $p[0] <= 10) {
            return '05 - 10';
        }else if ($p[0] <= 05) {
            return '00 - 05';
        }
    }

    function is_premium ($premium) {
        if ($premium > time()) {
            return true;
        } else {
            return false;
        }
    }

    $custo = 0;
    if ($gebruiker['calc_limit'] <= 0) {
        // $custo = floor(($gebruiker['calc_multiplier'] * 2500) * 1.25); antiga
        $custo = floor(($gebruiker['calc_multiplier'] * 500)); //nova
    }

    if ($gebruiker['rank'] >= 4) {
    if (isset($_POST['pokemonview']) && isset($_POST['pokemonid'])) {
        if ($_POST['pokemonview'] != 1 && $_POST['pokemonview'] != 2) {
            $_POST['pokemonview'] = 1;
        }
            $pokemoninfo = DB::exQuery("SELECT pokemon_wild.wild_id,pokemon_wild.naam,pokemon_speler.*, pokemon_wild.zeldzaamheid FROM pokemon_speler INNER JOIN pokemon_wild ON pokemon_speler.wild_id = pokemon_wild.wild_id WHERE pokemon_speler.id = '".$_POST['pokemonid']."'")->fetch_assoc();
            #Is er geen pokemon gekozen?
            if (empty($_POST['pokemonid'])) echo '<div class="red">Escolha um pokémon.</div>';
            else if ($pokemoninfo['ei'] == 1) echo '<div class="red">Este pokémon ainda é um ovo.</div>';
            else if ($pokemoninfo['user_id'] != $_SESSION['id']) echo '<div class="red">Esse pokémon não é seu</div>';
            else if ($pokemoninfo['opzak'] != 'ja') echo '<div class="red">Esse pokémon não está no seu time.</div>';
            else{
                $succ = true;
                $pokemon_name = pokemon_naam($pokemoninfo['naam'], $pokemoninfo['roepnaam'], $pokemoninfo['icon']);
                if ($_POST['pokemonview'] == 1) {
                    if ($gebruiker['silver'] < $custo) {
                        echo '<div class="red">Você não tem silvers suficientes.</div>';
                        $succ = false;
                    } else {
                        if (!is_premium($gebruiker['premiumaccount'])) {
                            DB::exQuery("UPDATE `gebruikers` SET calc_limit=calc_limit-1 WHERE user_id='$_SESSION[id]' AND calc_limit>0");
                            DB::exQuery("UPDATE `gebruikers` SET calc_multiplier=calc_multiplier+1 WHERE user_id='$_SESSION[id]' AND calc_limit='0' AND calc_multiplier<20");
                        }

                        if ($gebruiker['calc_limit'] <= 0) {
                            $preco = floor(($gebruiker['calc_multiplier'] * 500));
                            DB::exQuery("UPDATE `gebruikers` SET silver=silver-'$preco' WHERE user_id='$_SESSION[id]'");
                        }
                        
                        $iv_hp = calcIV($pokemoninfo['hp_iv']);
                        $iv_atk = calcIV($pokemoninfo['attack_iv']);
                        $iv_def = calcIV($pokemoninfo['defence_iv']);
                        $iv_spatk = calcIV($pokemoninfo['spc.attack_iv']);
                        $iv_spdef = calcIV($pokemoninfo['spc.defence_iv']);
                        $iv_speed = calcIV($pokemoninfo['speed_iv']);
                    }
                } else  {
                    $custo_g = (!is_premium($gebruiker['premiumaccount']))? 10 : 5;
                    if ($rekening['gold'] < $custog) {
                        echo '<div class="red">Você não tem golds suficientes.</div>';
                        $succ = false;
                    } else {
                        if ($pokemoninfo['has_calc'] == 0) {
                            DB::exQuery("UPDATE `rekeningen` SET gold=gold-'$custo_g' WHERE acc_id='$_SESSION[acc_id]'");
                            DB::exQuery("UPDATE `pokemon_speler` SET has_calc='1' WHERE id='$pokemoninfo[id]'");
                        }

                        $iv_hp = ($pokemoninfo['hp_iv']);
                        $iv_atk = ($pokemoninfo['attack_iv']);
                        $iv_def = ($pokemoninfo['defence_iv']);
                        $iv_spatk = ($pokemoninfo['spc.attack_iv']);
                        $iv_spdef = ($pokemoninfo['spc.defence_iv']);
                        $iv_speed = ($pokemoninfo['speed_iv']);
                    }
                }

                if ($succ) $sucesso = true;
            }
        }
    } else {
        echo '<div class="red">RANK MÍNIMO PARA VER AS IVs DOS POKÉMONS: 4 - TRAINER. CONTINUE UPANDO PARA LIBERAR!</div>';
    }
  
    ?>
     
    <?php 
    if (!$sucesso) {
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
                        </script>

                        <td style="padding: 0" colspan="2">
                            <div class="main-carousel" style="height: 97px; position: relative">
                                <?php
                                    while($pokemon_profile = $pokemon_sql->fetch_assoc()) {
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
                                                    $poke_array_spe.push("<?=$pokemon_profile['naam']?>");

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
                    <td class="row">
                        <div class="col alternate" style="border-right: 1px solid #577599;">
                            <h3 class="title" style="font-size: 15px">CALCULADORA SIMPLES</h3>

                            <div style="padding: 10px; padding-bottom: 0; text-align: left; border-bottom: 1px solid #577599;">
                                <ul>
                                    <li>Com a calculadora simples, você poderá ver os valores das IV's <b>APROXIMADAS</b> de seu Pokémon.</li>
                                    <li>Por dia você terá 5 (ou <span title="Infinitos">∞</span> se for <b>CONTA PREMIUM</b>) usos <b>GRATUITOS</b>.</li>
                                    <li>Após os usos gratuitos, será cobrada uma taxa conforme o número de usos até o <b>20º uso</b>.</li>
                                    <li style="margin-top: 10px"><b>Preço: </b><img src="<?=$static_url?>/images/icons/silver.png" title="Silvers" style="vertical-align: bottom"> <?=($custo > 0)? highamount($custo) : 'Grátis'?></li>
                                    <li>Usos<b> GRÁTIS </b>restantes: <?= (!is_premium($gebruiker['premiumaccount']))? $gebruiker['calc_limit'] : '<span title="Infinitos">∞</span>' ?></li>
                                </ul>
                            </div>
                            <button class="button" style="margin: 6px" <?=($gebruiker['rank'] >= 4)? 'onclick="view_ivs(1)"' : 'disabled'?>>VER IV's SIMPLES</button>
                        </div>
                
                        <div class="col alternate">
                            <h3 class="title" style="font-size: 15px">CALCULADORA PREMIUM</h3>

                            <div style="padding: 10px; padding-bottom: 0; text-align: left; border-bottom: 1px solid #577599;">
                                <ul>
                                    <li>Com a calculadora premium, você poderá ver os valores das IV's <b>EXATAS</b> de seu Pokémon.</li>
                                    <li>O preço é <b>FIXO</b>, portanto, não há um <b>limite diário</b> para o aumento da taxa.</li>
                                    <li>Após a visualização, o gráfico ficará <b>SALVO</b> no <b>PERFIL</b> de seu <b>Pokémon</b>.</li>
                                    <li style="margin-top: 10px"><b>Preço: </b><img src="<?=$static_url?>/images/icons/gold.png" title="Golds" style="vertical-align: bottom"> <?= (!is_premium($gebruiker['premiumaccount']))? '10' : '5' ?></li>
                                </ul>
                            </div>
                            <button class="button" style="margin: 6px" <?=($gebruiker['rank'] >= 4)? 'onclick="view_ivs(2)"' : 'disabled'?>>VER IV's PREMIUM</button>
                        </div>
                    </td>
                </tr>
                </tfoot>
            </table>
        </div>

        <form method="post" action="./calculator" id="calc">
            <input type="hidden" name="pokemonid" id="poke_id" value=""/>
            <input type="hidden" name="pokemonview" id="poke_view" value="1" max="2" min="1" />
        </form>

        <script>
            var $carousel = $('.main-carousel');
            var $poke_name = $('#poke_name');
            var $poke_link = $('#poke_link');
            var $poke_id = $('#poke_id');

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

                $poke_id.val ($poke_array_iid[flkty.selectedIndex]);
            });

            $poke_link.attr('href', '/pokedex&poke='+$poke_array_id[0]);
            $poke_link.html($poke_array_name[0]);
            $poke_name.html($poke_array_spe[0]);

            $poke_id.val ($poke_array_iid[0]);

            $car.resize();

            function view_ivs ( $param = 1 ) {
                if ($param == 1 || $param == 2) {
                    $('#poke_view').val ($param);
                    $('#calc').submit();
                }
            }
        </script>

        <?php } else { ?>
        <div class="box-content" style="width: 100%">
            <table class="general" style="width: 100%; font-size: 14px">
                <thead>
                    <th colspan="2">Calculadora de IV's <?=($_POST['pokemonview'] == 1)? 'Simples' : 'Premium'?></th>
                </thead>
                <tbody>
                    <tr>
                        <td class="first"><b>HP:</b> <?=$iv_hp?> IV's</td>
                        <td class="last last-right"><b>Sp. Ataque:</b> <?=$iv_spatk?> IV's</td>
                    </tr>
                    <tr>
                        <td class="first"><b>Ataque:</b> <?=$iv_atk?> IV's</td>
                        <td class="last last-right"><b>Sp. Defesa:</b> <?=$iv_spdef?> IV's</td>
                    </tr>
                    <tr>
                        <td class="first"><b>Defesa:</b> <?=$iv_def?> IV's</td>
                        <td class="last last-right"><b>Speed:</b> <?=$iv_speed?> IV's</td>
                    </tr>
                </tbody>
                <tfooter>
                    <tr>
                        <td style="text-align: center" colspan="2">IV's de <b><a href="./pokemon-profile&id=<?=$pokemoninfo['id']?>" title="Clique para ver o Perfil do Pokémon"><?=$pokemon_name?></a></b></td>
                    </tr>
                </tfooter>
            </table>
        </div>

        <div class="box-content" style="width: 100%; margin-top: 7px">
            <script src="<?=$static_url?>/javascripts/chartjs/Chart.js"></script>
            <center><canvas id="radarChart" width="230" height="200"></canvas></center>
            <?php
                if ($_POST['pokemonview'] == 1) {
                    $iv_hp = explode (' - ', $iv_hp);    
                    $iv_atk = explode (' - ', $iv_atk);
                    $iv_def = explode (' - ', $iv_def);
                    $iv_spatk = explode (' - ', $iv_spatk);
                    $iv_spdef = explode (' - ', $iv_spdef);
                    $iv_speed = explode (' - ', $iv_speed);
                }
            ?>
            <script>
                var radarData = {
                    labels : ["HP", "Defesa", "Sp. Ataque", "Speed", "Sp. Defesa", "Ataque"],
                    datasets : [
                        <?php if ($_POST['pokemonview'] == 1) { ?>
                        {
                            fillColor: "rgba(63,169,245,.1)",
                            strokeColor: "red",
                            pointColor : "#000",
                            pointStrokeColor : "#fff",
                            data : [<?=$iv_hp[0]?>, <?=$iv_def[0]?>, <?=$iv_spatk[0]?>, <?=$iv_speed[0]?>, <?=$iv_spdef[0]?>, <?=$iv_atk[0]?>],
                        },
                        {
                            fillColor: "rgba(63,169,245,.1)",
                            strokeColor: "#0074D9",
                            pointColor : "#0b0b0b",
                            pointStrokeColor : "#fff",
                            data : [<?=$iv_hp[1]?>, <?=$iv_def[1]?>, <?=$iv_spatk[1]?>, <?=$iv_speed[1]?>, <?=$iv_spdef[1]?>, <?=$iv_atk[1]?>],
                        }
                        <?php } else { ?>
                        {
                            fillColor: "rgba(63,169,245,.1)",
                            strokeColor: "#0074D9",
                            pointColor : "#000",
                            pointStrokeColor : "#fff",
                            data : [<?=$iv_hp?>, <?=$iv_def?>, <?=$iv_spatk?>, <?=$iv_speed?>, <?=$iv_spdef?>, <?=$iv_atk?>],
                        },
                        <?php } ?>
                    ]
                };

                var ctx2 = document.getElementById("radarChart").getContext("2d");
                new Chart(ctx2).Radar(radarData);
            </script>
        </div>
            
        <center><button class="btn" style="margin-top: 7px" onclick="window.location = './calculator'">Ver mais IV's dos meus Pokémon</button></center>

        <?php } ?>