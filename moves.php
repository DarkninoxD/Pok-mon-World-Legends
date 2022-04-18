<?php
    #include dit script als je de pagina alleen kunt zien als je ingelogd bent.
    include('app/includes/resources/security.php');

    #Als je geen pokemon bij je hebt, terug naar index.
    if ($gebruiker['in_hand'] == 0) header('Location: index.php');

    echo addNPCBox(30, 'Especialista em Ataques', 'Olá <b>Jovem Treinador</b>, como vai?<br><br>
    Que <b>belos Pokémons</b> você possui! <br>Gostaria que eu mostrasse os <b>Ataques</b> Especiais que posso lhes <b>ensinar</b>? <br>Ou até mesmo <b>relembrá-lo</b> de algum <b>Ataque</b>?<br><br>
    Escolha um <b>Pokémon</b> e te mostrarei.');

    if (isset($_POST['pokemonview']) && isset($_POST['pokemonid'])) {
        if ($_POST['pokemonview'] != 1 && $_POST['pokemonview'] != 2) {
            $_POST['pokemonview'] = 1;
        }
            $pokemoninfo = DB::exQuery("SELECT pokemon_wild.wild_id,pokemon_wild.type1,pokemon_wild.type2,pokemon_wild.naam,pokemon_speler.*, pokemon_wild.zeldzaamheid FROM pokemon_speler INNER JOIN pokemon_wild ON pokemon_speler.wild_id = pokemon_wild.wild_id WHERE pokemon_speler.id = '".$_POST['pokemonid']."'")->fetch_assoc();
            if (empty($_POST['pokemonid'])) echo '<div class="red">Escolha um pokémon.</div>';
            else if ($pokemoninfo['ei'] == 1) echo '<div class="red">Este pokémon ainda é um ovo.</div>';
            else if ($pokemoninfo['user_id'] != $_SESSION['id']) echo '<div class="red">Esse pokémon não é seu</div>';
            else if ($pokemoninfo['opzak'] != 'ja') echo '<div class="red">Esse pokémon não está no seu time.</div>';
            else{
                $succ = true;
                $pokemonid = $_POST['pokemonid'];
                $category = '';
                $pokemon_name = pokemon_naam($pokemoninfo['naam'], $pokemoninfo['roepnaam'], $pokemoninfo['icon']);

                if ($_POST['pokemonview'] == 1) {
                    if (!isset($_POST['ataque'])) {
                        $category = 'moves-tutor-show';
                    }
                } else  {
                    if (!isset($_POST['ataque'])) {
                        $category = 'moves-reminder-show';
                    }
                }

                if ($succ) $sucesso = true;
            }
    }

    if (isset($_POST['ataque']) && isset($_POST['pokemonid'])) {
        if (empty($_POST['method']) || $_POST['method'] > 2) {
            $_POST['method'] = 1;
        }

        $method = $_POST['method'];
        $succ = false;

        if ($method == 1) {
            $verify = 0;
            $sql = DB::exQuery("SELECT * FROM tmhm_movetutor WHERE `naam`='$_POST[ataque]'");

            if ($sql->num_rows > 0) {
                $sql = $sql->fetch_assoc();

                if ($gebruiker['silver'] >= $sql['silver']) {
                    if ($rekening['gold'] >= $sql['gold']) {
                        $money = $sql['silver'].','.$sql['gold'];

                        $sql = DB::exQuery("SELECT id FROM `pokemon_speler` WHERE aanval_1 != '$_POST[ataque]' AND aanval_2 != '$_POST[ataque]' AND aanval_3 != '$_POST[ataque]' AND aanval_4 != '$_POST[ataque]' AND id='$_POST[pokemonid]'")->num_rows;

                        if ($sql > 0) {
                            $succ = true;
                        } else {
                            echo '<div class="red">Seu Pokémon já tem esse golpe!</div>';
                        }
                    } else {
                        echo '<div class="red">Você não tem Golds suficientes!</div>';
                    }
                } else {
                    echo '<div class="red">Você não tem Silvers suficientes!</div>';
                }

            } else {
                header ('Location: ./moves');
            }
        } else {
            $verify = 0;
            $pokemoninfo = DB::exQuery("SELECT pokemon_wild.wild_id,pokemon_wild.type1,pokemon_wild.type2,pokemon_wild.naam,pokemon_speler.*, pokemon_wild.zeldzaamheid FROM pokemon_speler INNER JOIN pokemon_wild ON pokemon_speler.wild_id = pokemon_wild.wild_id WHERE pokemon_speler.id = '".$_POST['pokemonid']."'")->fetch_assoc();
            $sql = DB::exQuery("select * from levelen where wild_id='".$pokemoninfo['wild_id']."' and level<='".$pokemoninfo['level']."' and aanval='".$_POST['ataque']."' order by level asc");

            if ($sql->num_rows > 0) {
                $sql = $sql->fetch_assoc();
                $dadosataque = atk($sql['aanval'], $pokemoninfo);

                if ($dadosataque['tipo'] == "Status") {
                    $ataque['silver'] = "50000";
                } else if ($dadosataque['sterkte'] <= 70) {
                    $ataque['silver'] = "25000";
                } else if ($dadosataque['sterkte'] >= 70 AND $dadosataque['sterkte'] <= 100) {
                    $ataque['silver'] = "75000";
                } else if ($dadosataque['sterkte'] >= 100) {
                    $ataque['gold'] = "3";
                }

                if ($ataque['gold'] == "") {
                    $ataque['gold'] = 0;
                }
                if ($ataque['silver'] == "") {
                    $ataque['silver'] = 0;
                }

                if ($dadosataque['naam'] == "Sketch") {
                    header ('Location: ./moves');
                }

                if ($gebruiker['silver'] >= $ataque['silver']) {
                    if ($rekening['gold'] >= $ataque['gold']) {
                        $sql = DB::exQuery("SELECT id FROM `pokemon_speler` WHERE aanval_1 != '$_POST[ataque]' AND aanval_2 != '$_POST[ataque]' AND aanval_3 != '$_POST[ataque]' AND aanval_4 != '$_POST[ataque]' AND id='$_POST[pokemonid]'")->num_rows;

                        if ($sql > 0) {
                            $money = $ataque['silver'].','.$ataque['gold'];
                            $succ = true;
                        } else {
                            echo '<div class="red">Seu Pokémon já tem esse golpe!</div>';
                        }
                    } else {
                        echo '<div class="red">Você não tem Golds suficientes!</div>';
                    }
                } else {
                    echo '<div class="red">Você não tem Silvers suficientes!</div>';
                }

            } else {
                header ('Location: ./moves');
            }
        }

        if ($succ) {
            $sucesso = true;
            $pokemoninfo = DB::exQuery("SELECT pokemon_wild.wild_id,pokemon_wild.type1,pokemon_wild.type2,pokemon_wild.naam,pokemon_speler.*, pokemon_wild.zeldzaamheid FROM pokemon_speler INNER JOIN pokemon_wild ON pokemon_speler.wild_id = pokemon_wild.wild_id WHERE pokemon_speler.id = '".$_POST['pokemonid']."'")->fetch_assoc();
            $pokemon_name = pokemon_naam($pokemoninfo['naam'], $pokemoninfo['roepnaam'], $pokemoninfo['icon']);
            $ataqueinfo = atk($_POST['ataque']);

            if (!empty($pokemoninfo['aanval_1']) && !empty($pokemoninfo['aanval_2']) && !empty($pokemoninfo['aanval_3']) && !empty($pokemoninfo['aanval_4'])) {
                $category = 'moves-learn';

                if (isset($_POST['welke'])) {
                    if (in_array($_POST['welke'], array('aanval_1', 'aanval_2', 'aanval_3', 'aanval_4'))) {
                        $money = explode (',', $money);
                        $aanval = $_POST['welke'];

                        DB::exQuery ("UPDATE `gebruikers` SET `silver`=`silver`-'$money[0]' WHERE user_id='$_SESSION[id]'");
                        DB::exQuery ("UPDATE `rekeningen` SET `gold`=`gold`-'$money[1]' WHERE acc_id='$_SESSION[id]'");
                        DB::exQuery ("UPDATE `pokemon_speler` SET $aanval='$_POST[ataque]' WHERE id='$_POST[pokemonid]'");

                        $pokemoninfo = DB::exQuery("SELECT pokemon_wild.wild_id,pokemon_wild.type1,pokemon_wild.type2,pokemon_wild.naam,pokemon_speler.*, pokemon_wild.zeldzaamheid FROM pokemon_speler INNER JOIN pokemon_wild ON pokemon_speler.wild_id = pokemon_wild.wild_id WHERE pokemon_speler.id = '".$_POST['pokemonid']."'")->fetch_assoc();

                        $category = 'moves-learn-nomove';
                    } else {
                        header ('Location: ./moves');
                    }
                }
            } else {
                $category = 'moves-learn-nomove';
                if (empty($pokemoninfo['aanval_1'])) {
                    $aanval = '_1';
                } else if (empty($pokemoninfo['aanval_2'])) {
                    $aanval = '_2';
                } else if (empty($pokemoninfo['aanval_3'])) {
                    $aanval = '_3';
                } else {
                    $aanval = '_4';
                }

                $aanval = 'aanval'.$aanval;

                $money = explode (',', $money);

                DB::exQuery ("UPDATE `gebruikers` SET `silver`=`silver`-'$money[0]' WHERE user_id='$_SESSION[id]'");
                DB::exQuery ("UPDATE `rekeningen` SET `gold`=`gold`-'$money[1]' WHERE acc_id='$_SESSION[id]'");
                DB::exQuery ("UPDATE `pokemon_speler` SET $aanval='$_POST[ataque]' WHERE id='$_POST[pokemonid]'");

                $pokemoninfo = DB::exQuery("SELECT pokemon_wild.wild_id,pokemon_wild.type1,pokemon_wild.type2,pokemon_wild.naam,pokemon_speler.*, pokemon_wild.zeldzaamheid FROM pokemon_speler INNER JOIN pokemon_wild ON pokemon_speler.wild_id = pokemon_wild.wild_id WHERE pokemon_speler.id = '".$_POST['pokemonid']."'")->fetch_assoc();
            }
        }
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
        <div class="box-content" style="width: 100%;">
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
                                    $pokemon_profiel_sql = DB::exQuery("SELECT `pokemon_speler`.*,`pokemon_wild`.`naam`,`pokemon_wild`.`type1`,`pokemon_wild`.`type2` FROM `pokemon_speler` INNER JOIN `pokemon_wild` ON `pokemon_speler`.`wild_id`=`pokemon_wild`.`wild_id` WHERE `user_id`='" . $_SESSION["id"] . "' AND `opzak`='ja' ORDER BY `opzak_nummer` ASC");
                                    while($pokemon_profile = $pokemon_profiel_sql->fetch_assoc()) {
                                        $pokemon_profile = pokemonei($pokemon_profile, $txt);
                                        $of_name = $pokemon_profile['naam'];
                                        $popup = pokemon_popup($pokemon_profile, $txt);
                                        $pokemon_profile['naam'] = pokemon_naam($pokemon_profile['naam'], $pokemon_profile['roepnaam'],$pokemon_profile['icon']);
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
                </tr>
                </tbody>
                <tfoot>

                    <tr style="text-align: center; font-size: 13px">
                        <td class="row">
                            <div class="col alternate" style="border-right: 1px solid #577599;">
                                <h3 class="title" style="font-size: 15px">MOVE TUTOR</h3>

                                <div style="padding: 10px; padding-bottom: 0; text-align: left; border-bottom: 1px solid #577599;">
                                    <ul>
                                        <li>Aqui você pode <b>ensinar</b> golpes ao seu Pokémon por uma quantia de <b>Silvers</b> ou <b>Gold</b>.</li>
                                    </ul>
                                </div>
                                <button class="button" style="margin: 6px" onclick="view_ivs(1)">ENSINAR GOLPES</button>
                            </div>
                    
                            <div class="col alternate">
                                <h3 class="title" style="font-size: 15px">MOVE REMINDER</h3>

                                <div style="padding: 10px; padding-bottom: 0; text-align: left; border-bottom: 1px solid #577599;">
                                    <ul>
                                        <li>Aqui você pode <b>lembrar</b> golpes de seu Pokémon por uma quantia de <b>Silvers</b> ou <b>Gold</b>.</li>
                                    </ul>
                                </div>
                                <button class="button" style="margin: 6px" onclick="view_ivs(2)">LEMBRAR GOLPES</button>
                            </div>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <form method="post" action="./moves" id="move">
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
                $poke_link.attr('href', '/pokedex&poke='+$poke_array_id[flkty.selectedIndex]);
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
                    $('#move').attr('action', './moves');
                    $('#move').submit();
                }
            }
        </script>

        <?php
            } else {
                if ($category == 'moves-tutor-show') {
                    $pokemon = pokemonei($pokemoninfo, $txt);
                    $pokemon['naam'] = $pokemon_name;
        ?>
        <div id="pokemon-amie" style="background-image: url('<?=$static_url?>/images/amie/<?=strtolower($pokemon['type1'])?>.png'); min-height: 215px; margin-bottom: 6px">
            <div class="pokemon-title"><?="<a href='./pokemon-profile&id=".$pokemon['id']."' style='vertical-align: middle' class='noanimate' target='_blank'><img src='".$static_url." /images/icons/info.png' title='Ver Perfil do Pokémon'></a> ".$pokemon['naam']?></div>

            <div style="text-align: center">
                <img src="<?=$static_url.'/'.$pokemon['link']?>" alt="<?=$pokemon['naam']?>" id="pokemon-profile" style="position: relative; margin-top: 4%;">
            </div>  
        </div>
                        <style>
                            .btn-type-selected {
                                opacity: 1;
                                transition: 1s;
                                box-shadow: 0 0 15px #0e0d0d66;
                                filter: brightness(115%);
                            }
                            .btn-type:hover {
                                box-shadow: none;
                                opacity: 0.8;
                            }
                            .btn-type-selected:hover {
                                opacity: 1;
                                transition: 1s;
                                box-shadow: 0 0 15px #0e0d0d66;
                                filter: brightness(115%);
                            }
                            .moves p {
                                margin-top: 0;
                                font-size: 12px;
                                font-weight: unset;
                            }
                        </style>


                    <div style="margin-top: 3px" class="box-content moves">
                    <center>
                    <form action="./moves" method="post">
        <?php

                    $ataque_sql = DB::exQuery("SELECT * FROM tmhm_movetutor");
                    $pos = 1;
                    while ($ataque = $ataque_sql->fetch_assoc()) {
                        $pegaidpokes = explode(",", $ataque['relacionados']);
                        foreach ($pegaidpokes as $pokemonid) {
                            if (!empty($pokemonid)) {
                                if ($pokemonid == $pokemoninfo['wild_id']) {
                                    if (!in_array ($ataque['naam'], array($pokemoninfo['aanval_1'], $pokemoninfo['aanval_2'], $pokemoninfo['aanval_3'], $pokemoninfo['aanval_4']))) {
                                        $price = '0';
                                        if ($ataque['silver'] == 0) {
                                            $price = '<img src="'.$static_url.'/images/icons/gold.png" style="vertical-align: bottom"> '.highamount($ataque['gold']);
                                        } else if ($ataque['gold'] == 0) {
                                            $price = '<img src="'.$static_url.'/images/icons/silver.png" style="vertical-align: bottom"> '.highamount($ataque['silver']);
                                        } else {
                                            $price = '<img src="'.$static_url.'/images/icons/silver.png" style="vertical-align: bottom"> '.highamount($ataque['silver']).' <img src="'.$static_url.'/images/icons/gold.png" style="vertical-align: bottom"> '.highamount($ataque['gold']);
                                        }
                                        $pos++;
                                        ?>
                                        <input type="radio" name="ataque" id="atk-<?=$ataque['id']?>" value="<?=$ataque['naam']?>" style="display: none"/>
                                        <button type="button" style="background: url(<?=$static_url?>/images/attack/moves/<?=atk($ataque['naam'], $pokemon)['soort']?>.png) no-repeat;" class="btn-type" onclick="select_btn(this, '#atk-<?=$ataque['id']?>');"><label for="atk-<?=$ataque['id']?>"><p style="width: 100%; padding-top: 14px;"><?=$ataque['naam']?><br><?=$price?></p></label></button>
                                        <?php
                                    }
                                }
                            }
                        }
                    }
                    if ($pos > 0) {
                    ?>
                        <script>
                            function select_btn ($param, $id) {
                                let obj = $param;
                                $('.btn-type-selected').removeClass('btn-type-selected');
                                $(obj).addClass('btn-type-selected');
                                let move = $($id).attr('value');
                                $('#subm').val('Ensinar o golpe ' + move + '?');
                            }
                        </script>
                        <input type="hidden" value="<?=$_POST['pokemonid']?>" name="pokemonid">
                        <input type="hidden" value="1" name="method">

                        <div style="border-top: 1px solid #577599;"><input type="submit" value="Ensinar golpe" id="subm" class="button" style="margin: 6px">
                            &nbsp;&nbsp;<button type="button" class="button" style="background-color: #d25757" onclick="window.location = window.location.href">Voltar</button>
                        </div>
                        </form>
                        <?php
                            } else {
                                echo '<div class="red">Infelizmente seu pokémon não pode aprender nenhum ataque comigo.</div>';
                            }
                        ?>
                        </center>
                        </div>
                    <?php
                } else if ($category == 'moves-learn') {
                    $pokemon = pokemonei($pokemoninfo, $txt);
                    $pokemon['naam'] = $pokemon_name;

                    ?>

                    <div id="pokemon-amie" style="background-image: url('<?=$static_url?>/images/amie/<?=$pokemon['type1']?>.png'); min-height: 215px; margin-bottom: 6px">
                        <div class="pokemon-title"><?="<a href='./pokemon-profile&id=".$pokemon['id']."' style='vertical-align: middle' class='noanimate' target='_blank'><img src='".$static_url." /images/icons/info.png' title='Ver Perfil do Pokémon'></a> ".$pokemon['naam']?></div>

                        <div style="text-align: center">
                            <img src="<?=$static_url.'/'.$pokemon['link']?>" alt="<?=$pokemon['naam']?>" id="pokemon-profile" style="position: relative; margin-top: 4%;">
                        </div>  
                    </div>
                    <center>

                    <div class="blue">Qual golpe você deseja substituir para que <?=$pokemon['naam']?> aprenda o ataque: <?=$ataqueinfo['naam']?>?</div>
                    <div class="box-content" style="width:100%">
                    <table>
                    <tr>
                    <td>
                        <form method="post">
                            <input type="hidden" name="pokemonid" value="<?=$_POST['pokemonid']?>">
                            <input type="hidden" name="ataque" value="<?=$_POST['ataque']?>">
                            <input type="hidden" value="<?=$_POST['method']?>" name="method">
                            <button style="background: url(<?=$static_url?>/images/attack/moves/<?=atk($pokemoninfo['aanval_1'], $pokemoninfo)['soort']?>.png) no-repeat;" class="btn-type"><?=atk($pokemoninfo['aanval_1'], $pokemoninfo)['naam']?></button>
                            <input type="hidden" name="welke" value="aanval_1">
                        </form>
                    </td>
                    <td>
                        <form method="post">
                            <input type="hidden" name="pokemonid" value="<?=$_POST['pokemonid']?>">
                            <input type="hidden" name="ataque" value="<?=$_POST['ataque']?>">
                            <input type="hidden" value="<?=$_POST['method']?>" name="method">
                            <button style="background: url(<?=$static_url?>/images/attack/moves/<?=atk($pokemoninfo['aanval_2'], $pokemoninfo)['soort']?>.png) no-repeat;" class="btn-type"><?=atk($pokemoninfo['aanval_2'], $pokemoninfo)['naam']?></button>
                            <input type="hidden" name="welke" value="aanval_2">
                        </form>
                    </td>
                    <td>
                        <form method="post">
                            <input type="hidden" name="pokemonid" value="<?=$_POST['pokemonid']?>">
                            <input type="hidden" name="ataque" value="<?=$_POST['ataque']?>">
                            <input type="hidden" value="<?=$_POST['method']?>" name="method">
                            <button style="background: url(<?=$static_url?>/images/attack/moves/<?=atk($pokemoninfo['aanval_3'], $pokemoninfo)['soort']?>.png) no-repeat;" class="btn-type"><?=atk($pokemoninfo['aanval_3'], $pokemoninfo)['naam']?></button>
                            <input type="hidden" name="welke" value="aanval_3">
                        </form>
                    </td>
                    <td>
                        <form method="post">
                            <input type="hidden" name="pokemonid" value="<?=$_POST['pokemonid']?>">
                            <input type="hidden" name="ataque" value="<?=$_POST['ataque']?>">
                            <input type="hidden" value="<?=$_POST['method']?>" name="method">
                            <button style="background: url(<?=$static_url?>/images/attack/moves/<?=atk($pokemoninfo['aanval_4'], $pokemoninfo)['soort']?>.png) no-repeat;" class="btn-type"><?=atk($pokemoninfo['aanval_4'], $pokemoninfo)['naam']?></button>
                            <input type="hidden" name="welke" value="aanval_4">
                        </form>
                    </td>
                    </tr>
                    </table>
                    <div style="border-top: 1px solid #577599;">
                    <button type="button" class="button" style="background-color: #d25757; margin: 6px" onclick="window.location = window.location.href">Voltar</button>
                    </div>
                    </div>
                    </center>
                    <?php
                } else if ($category == 'moves-reminder-show') {
                    $pokemon = pokemonei($pokemoninfo, $txt);
                    $pokemon['naam'] = $pokemon_name;
        ?>
        <div id="pokemon-amie" style="background-image: url('<?=$static_url?>/images/amie/<?=$pokemon['type1']?>.png'); min-height: 215px; margin-bottom: 6px">
            <div class="pokemon-title"><?="<a href='./pokemon-profile&id=".$pokemon['id']."' style='vertical-align: middle' class='noanimate' target='_blank'><img src='".$static_url." /images/icons/info.png' title='Ver Perfil do Pokémon'></a> ".$pokemon['naam']?></div>

            <div style="text-align: center">
                <img src="<?=$static_url.'/'.$pokemon['link']?>" alt="<?=$pokemon['naam']?>" id="pokemon-profile" style="position: relative; margin-top: 4%;">
            </div>  
        </div>
                        <style>
                            .btn-type-selected {
                                opacity: 1;
                                transition: 1s;
                                box-shadow: 0 0 15px #0e0d0d66;
                                filter: brightness(115%);
                            }
                            .btn-type:hover {
                                box-shadow: none;
                                opacity: 0.8;
                            }
                            .btn-type-selected:hover {
                                opacity: 1;
                                transition: 1s;
                                box-shadow: 0 0 15px #0e0d0d66;
                                filter: brightness(115%);
                            }
                            .moves p {
                                margin-top: 0;
                                font-size: 12px;
                                font-weight: unset;
                            }
                        </style>
  
                    <div style="margin-top: 3px" class="box-content moves">
                    <center>
                    <form action="./moves" method="post">
        <?php

                    $ataque_sql = DB::exQuery("select * from levelen where wild_id='".$pokemoninfo['wild_id']."' and level<='".$pokemoninfo['level']."' order by level asc");
                    $pos = 1;
                    while ($ataque = $ataque_sql->fetch_assoc()) {
                        $dadosataque = DB::exQuery("select * from aanval where naam='".$ataque['aanval']."'")->fetch_assoc();

                        if ($dadosataque['tipo'] == "Status") {
                        $ataque['silver'] = "50000";
                        }
                        else if ($dadosataque['sterkte'] <= 70) {
                        $ataque['silver'] = "25000";
                        }
                        else if ($dadosataque['sterkte'] >= 70 AND $dadosataque['sterkte'] <= 100) {
                        $ataque['silver'] = "75000";
                        }
                        else if ($dadosataque['sterkte'] >= 100) {
                        $ataque['gold'] = "3";
                        }
                        if ($ataque['gold'] == "") {
                        $ataque['gold'] = 0;
                        }
                        if ($ataque['silver'] == "") {
                        $ataque['silver'] = 0;
                        }
                        if ($dadosataque['naam'] == "Sketch") {
                        continue;
                        }

                         if (in_array ($dadosataque['naam'], array($pokemoninfo['aanval_1'], $pokemoninfo['aanval_2'], $pokemoninfo['aanval_3'], $pokemoninfo['aanval_4']))) {
                            continue;
                         }
                                    $price = '0';
                                    if ($ataque['silver'] == 0) {
                                        $price = '<img src="'.$static_url.'/images/icons/gold.png" style="vertical-align: bottom"> '.highamount($ataque['gold']);
                                    } else if ($ataque['gold'] == 0) {
                                        $price = '<img src="'.$static_url.'/images/icons/silver.png" style="vertical-align: bottom"> '.highamount($ataque['silver']);
                                    } else {
                                        $price = '<img src="'.$static_url.'/images/icons/silver.png" style="vertical-align: bottom"> '.highamount($ataque['silver']).' <img src="'.$static_url.'/images/icons/gold.png" style="vertical-align: bottom"> '.highamount($ataque['gold']);
                                    }
                                    $pos++;
                                    ?>
                                    <input type="radio" name="ataque" id="atk-<?=$ataque['id']?>" value="<?=$dadosataque['naam']?>" style="display: none"/>
                                    <button type="button" style="background: url(<?=$static_url?>/images/attack/moves/<?=$dadosataque['soort']?>.png) no-repeat;" class="btn-type" onclick="select_btn(this, '#atk-<?=$ataque['id']?>');"><label for="atk-<?=$ataque['id']?>"><p style="width: 100%; padding-top: 14px;"><?=$dadosataque['naam']?><br><?=$price?></p></label></button>
                                    <?php
                    }
                    if ($pos > 0) {
                    ?>
                        <script>
                            function select_btn ($param, $id) {
                                let obj = $param;
                                $('.btn-type-selected').removeClass('btn-type-selected');
                                $(obj).addClass('btn-type-selected');
                                let move = $($id).attr('value');
                                $('#subm').val('Relembrar o golpe ' + move + '?');
                            }
                        </script>
                        <input type="hidden" value="<?=$_POST['pokemonid']?>" name="pokemonid">
                        <input type="hidden" value="2" name="method">
                        <div style="border-top: 1px solid #577599;">
                        <input type="submit" value="Relembrar golpe" id="subm" class="button">
                        &nbsp;&nbsp;<button type="button" class="button" style="background-color: #d25757; margin: 6px" onclick="window.location = window.location.href">Voltar</button></div>
                        </form>
                        <?php } else {
                            echo '<div class="red">Infelizmente seu pokémon não pode relembrar nenhum ataque comigo.</div>';
                        } ?>
                        </center>
                        </div>
                    <?php
                } else if ($category == 'moves-learn-nomove') {
                    $pokemon = pokemonei($pokemoninfo, $txt);
                    $pokemon['naam'] = $pokemon_name;

                    if ($_POST['method'] == 1) {
                        echo '<div class="green">Seu '.$pokemon['naam'].' aprendeu o ataque '.$ataqueinfo['naam'].' com sucesso!</div>';
                    } else if ($_POST['method'] == 2) {
                        echo '<div class="green">Seu '.$pokemon['naam'].' relembrou o ataque '.$ataqueinfo['naam'].' com sucesso!</div>';
                    }
                ?>
<div id="pokemon-amie" style="background-image: url('<?=$static_url?>/images/amie/<?=$pokemon['type1']?>.png'); min-height: 215px; margin-bottom: 6px">
            <div class="pokemon-title"><?="<a href='./pokemon-profile&id=".$pokemon['id']."' style='vertical-align: middle' class='noanimate' target='_blank'><img src='".$static_url." /images/icons/info.png' title='Ver Perfil do Pokémon'></a> ".$pokemon['naam']?></div>

            <div style="text-align: center">
                <img src="<?=$static_url.'/'.$pokemon['link']?>" alt="<?=$pokemon['naam']?>" id="pokemon-profile" style="position: relative; margin-top: 4%;">
            </div>  
        </div>
                <?php
                    echo '<center><button type="button" class="button" style="background-color: #d25757" onclick="window.location = window.location.href">Voltar para a seleção</button></center>';
                } else {
                    header ('Location: ./moves');
                }
        ?>

        <?php } ?>
