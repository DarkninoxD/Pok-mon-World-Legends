<script src="<?=$static_url?>/javascripts/timeago/jquery.timeago.js"></script>
<?php
//DB::exQuery("UPDATE `gebruikers` SET `pokecentertijdbegin`=NOW(),`pokecentertijd`='0' WHERE `user_id`='" . $gebruiker['user_id'] . "' LIMIT 1");
#Inclua este script se você puder ver apenas a página quando estiver logado.
require_once('app/includes/resources/security.php');
#Se você não tiver um Pokemon com você, volte ao índice.
if ($gebruiker['in_hand'] == 0 ) exit ( header('LOCATION: ./') );

if (empty($_GET['id']) || !is_numeric($_GET['id'])) { 
    require_once('notfound.php'); 
} else {
    $id = (int) $_GET['id'];
    $sql = DB::exQuery("SELECT * FROM `pokemon_speler` WHERE id='$id' AND release_date='0000-00-00 00:00:00'");
    if ($sql->num_rows == 1) {
        $pokemon = $sql->fetch_assoc();
        $pokemon = pokemonei($pokemon, $txt);

        if ($pokemon['ei'] > 0) {
            header('Location: ./home');
        }

        function wild ($id) {
            return DB::exQuery("SELECT * FROM `pokemon_wild` WHERE wild_id='$id'")->fetch_assoc();
        }

        function username ($id) {
            return DB::exQuery("SELECT `username` FROM `gebruikers` WHERE user_id='$id'")->fetch_assoc()['username'];
        }

        function real_owner ($id, $admin) {
            if ($id == $_SESSION['id'] || $admin >= 3) {
                return true;
            }

            return false;
        }

        function hasCalc ($value, $admin) {
            if (empty($_SESSION['share_acc'])) {
                if ($value || $admin >= 3) {
                    return true;
                }
            }

            return false;
        }

        $sql2 = wild ($pokemon['wild_id']);
        $pokemon['naam'] = pokemon_naam($sql2['naam'], $pokemon['roepnaam'], $pokemon['icon']);
        if (rtrim(strip_tags($pokemon['naam'])) != $sql2['naam']) $pokemon['naam'] .= ' ('.$sql2['naam'].')';

        $pokemon['type'] = $sql2['type1'];

        $u_name = username($pokemon['user_id']);
        $name = $pokemon['naam'].' de <a href="./profile&player='.$u_name.'" target="_blank">'.$u_name.'</a>';

        $shiny = $pokemon['shiny'] ? 'Shiny' : 'Padrão';
        $pokemon['powertotal'] = $pokemon['attack'] + $pokemon['defence'] + $pokemon['speed'] + $pokemon['spc.attack'] + $pokemon['spc.defence'];    

        $owner = isOwner($pokemon['user_id'], $gebruiker['admin'], $pokemon['opzak']);

        if ($pokemon['opzak'] == 'tra' && $_SESSION['share_acc'] == 0 && $gebruiker['rank'] >= 4) {
            $transferlist = DB::exQuery("SELECT * FROM `transferlijst` WHERE `pokemon_id`='$pokemon[id]'")->fetch_assoc();
            if ($transferlist['type'] == 'private' && !in_array($_SESSION['id'], array($transferlist['to_user'], $transferlist['user_id']))) { 
                $owner = isOwner($pokemon['user_id'], $gebruiker['admin'], $pokemon['opzak'], 'private');
            } else {
                $buy = ($transferlist['type'] == 'auction')? 'DAR LANCE EM' : 'COMPRAR';
                
                $part = array();

                if ($transferlist['type'] == 'auction') {
                    $time_end = date('Y-m-d H:i', $transferlist['time_end']);
                    $datum = '<span><b><script id="remove">document.write(jQuery.timeago("'.$time_end.' UTC")); document.getElementById("remove").outerHTML = "";</script></b></span>';
                    $nameu = username($transferlist['big_blind']);
                    $best = (empty($nameu))? 'ninguém' : '<a href="./profile&player='.$nameu.'">'.$nameu.'</a>';

                    $part[0] = '<ul><li>Tempo restante: '.$datum.'</li><li>Treinador com maior lance: '.$best.'</li><li>Maior lance: <b>'.highamount($transferlist['silver']).'</b> <img src="'.$static_url.'/images/icons/silver.png" title="Silvers" style="vertical-align: sub"></li><li>Número de lances: <b>'.$transferlist['lances'].'</b></li></ul>';
                    if (strtotime(date('Y-m-d H:i')) <= $transferlist['time_end']) {
                        if ($transferlist['user_id'] != $_SESSION['id']) {
                            if (isset($_POST['buy'])) {
                                if (isset($_POST['price']) && ctype_digit($_POST['price'])) {
                                    $price = $_POST['price'];
                                    if ($price > $gebruiker['silver']) {
                                        echo '<div class="red">Você não tem Silvers suficientes para dar este lance!</div>';
                                    } else if ($price <= $transferlist['silver']) {
                                        echo '<div class="red">Você não pode dar um lance menor ou igual ao atual!</div>';
                                    } else {   
                                        $quests->setStatus('buy_auction', $_SESSION['id']);
                                        DB::exQuery("UPDATE `gebruikers` SET `silver`=`silver`+'$transferlist[silver]' WHERE `user_id`='$transferlist[big_blind]'");     
                                        DB::exQuery("UPDATE `gebruikers` SET `silver`=`silver`-'$price' WHERE `user_id`='$_SESSION[id]'");
                                        DB::exQuery("UPDATE `transferlijst` SET `big_blind`='$_SESSION[id]', `silver`='$price', `lances`=`lances`+'1' WHERE `id`='$transferlist[id]'");
                                        echo '<script>window.location = window.location.href</script>';
                                    }
                                }
                            }

                            $part[1] = '<center><b>Lance: </b><input type="number" name="price" min="'.($transferlist['silver']+1).'" value="'.($transferlist['silver']+1000).'"><img src="'.$static_url.'/images/icons/silver.png" title="Silvers" style="vertical-align: sub"><img src="'.$static_url.'/images/icons/arrow_refresh_small.png" onclick="window.location = window.location.href" title="Atualizar a Página" style="vertical-align: sub;cursor:pointer"><br><input type="submit" name="buy" value="Dar Lance" style="margin-top: 7px"></center>';                    
                        } else {
                            if ($transferlist['lances'] == 0) {
                                if (isset($_POST['remove'])) {
                                    DB::exQuery("UPDATE `pokemon_speler` SET `trade`='1.0',`opzak`='nee' WHERE `id`='".$pokemon['id']."'");
                                    DB::exQuery("DELETE FROM `transferlijst` WHERE `id`='".$transferlist['id']."'");
                                }

                                $part[1] = '<center><input type="submit" name="remove" value="REMOVER POKÉMON" style="margin-top: 17px"></center>';
                            } else {
                                $part[1] = '<ul><li style="margin-top: 17px">Você não pode remover este Pokémon, porque já deram lances nele!</li></ul>';
                            }
                        }
                    } else {
                        $part[1] = '<ul><li style="margin-top: 23px">Sinto muito, mas esse leilão já acabou!</li></ul>';
                    }
                } else {
                    $type = ['private' => 'Privada', 'direct' => 'Direta'];
                    $price_gd = ($transferlist['gold'] > 0)? highamount(round($transferlist['gold'])).' <img src="'.$static_url.'/images/icons/gold.png" style="vertical-align: sub">' : '';
                    $price_sl = ($transferlist['silver'] > 0)? highamount(round($transferlist['silver'])).' <img src="'.$static_url.'/images/icons/silver.png" style="vertical-align: sub">' : '';
                    $ngc = ($transferlist['negociavel'])? '<li><a href="./inbox&action=send&player='.$u_name.'&assunto='.base64_encode("Venda de ".$pokemon['naam']).'">Negociar Preço</a></li>' : '';
                    $suffix = (!empty($price_gd) && !empty($price_sl))? ' e ' : '';

                    $price = $price_sl.$suffix.$price_gd;
                    $part[0] = '<ul style="margin-top: 10px"><li>Venda: <b>'.$type[$transferlist['type']].'</b></li><li>Preço: <b>'.$price.'</b></li>'.$ngc;
                     if ($transferlist['user_id'] != $_SESSION['id']) {
                        if (isset($_POST['buy'])) {
                            $silver = $transferlist['silver'];
                            $gold = $transferlist['gold'];

                            if ($silver > $gebruiker['silver'] || $gold > $rekening['gold']) {
			                    echo '<div class="red">Você não tem Silvers ou Gold suficientes para comprar este Pokémon!</div>';
		                    } else {
                                DB::exQuery("UPDATE `pokemon_speler` SET `user_id`='".$_SESSION['id']."',`trade`='1.5',`opzak`='nee',`opzak_nummer`='' WHERE `id`='".$pokemon['id']."'");

                                if ($transferlist['type'] == 'direct') $quests->setStatus('buy_direct', $_SESSION['id']);
			                    if ($transferlist['type'] == 'private') $quests->setStatus('buy_private', $_SESSION['id']);

                                DB::exQuery("UPDATE `gebruikers` SET `silver`=`silver`-'".$silver."', `aantalpokemon`=`aantalpokemon`+'1' WHERE `user_id`='".$_SESSION['id']."'");
                                DB::exQuery("UPDATE `gebruikers` SET `silver`=`silver`+'".$silver."', `aantalpokemon`=`aantalpokemon`-'1' WHERE `user_id`='".$transferlist['user_id']."'");
                                DB::exQuery("UPDATE `rekeningen` SET `gold`=`gold`-'".$gold."' WHERE `acc_id`='".$_SESSION['acc_id']."'");

                                $acc_id = DB::exQuery("SELECT `acc_id` FROM `gebruikers` WHERE user_id='$transferlist[user_id]'")->fetch_assoc()['acc_id'];
                                DB::exQuery("UPDATE `rekeningen` SET `gold`=`gold`+'".$gold."' WHERE `acc_id`='".$acc_id."'");

                                DB::exQuery("DELETE FROM `transferlijst` WHERE `id`='".$transferlist['id']."'");
                                update_pokedex($pokemon['wild_id'], '', 'buy');

                                DB::exQuery("INSERT INTO transferlist_log (date, wild_id, speler_id, level, seller, buyer, silver, gold, item) VALUES (NOW(), '".$pokemon['wild_id']."', '".$pokemon['id']."', '".$pokemon['level']."', '".$transferlist['user_id']."', '".$_SESSION['id']."', '".$transferlist['silver']."', '".$transferlist['gold']."', '".$pokemon['item']."')");

                                $event = '<img src="' . $static_url . '/images/icons/blue.png" width="16" height="16" class="imglower" /> <a href="./profile&player='.$gebruiker['username'].'">'.$gebruiker['username'].'</a> comprou seu <a href="./pokemon-profile&id='.$pokemon['id'].'">'.$pokemon['naam'].'</a> por: '.highamount($transferlist['silver']).' <img src="' . $static_url . '/images/icons/silver.png" title="Silver" width="16" height="16" /> e '.highamount($transferlist['gold']).'<img src="' . $static_url . '/images/icons/gold.png" title="Gold" width="16" height="16" />!';

                                DB::exQuery("INSERT INTO gebeurtenis (`datum`,`ontvanger_id`,`bericht`,`gelezen`) VALUES (NOW(), '" . $transferlist['user_id'] . "', '" . $event . "', '0')");
                                echo '<script>window.location = window.location.href</script>';
                            }
                        }

                        $part[1] = '<center><input type="submit" name="buy" value="COMPRAR POKÉMON" style="margin-top: 17px"></center>';                    
                    } else {
                        if (isset($_POST['remove'])) {
                            DB::exQuery("UPDATE `pokemon_speler` SET `trade`='1.0',`opzak`='nee' WHERE `id`='".$pokemon['id']."'");
                            DB::exQuery("DELETE FROM `transferlijst` WHERE `id`='".$transferlist['id']."'");
                        }
                        $part[1] = '<center><input type="submit" name="remove" value="REMOVER POKÉMON" style="margin-top: 17px"></center>';
                    }
                }
    ?>
                <div class="box-content" style="height: 140px; margin-bottom: 7px;">
                    <h3 class="title" style="text-transform: uppercase">DESEJA <?=$buy?> <?=$name?>?</h3>
                    <div id="npc-image" style="background: url(public/images/npc/36.png)no-repeat; background-size: 100% 100%; height: 143px; width: 185px; margin-top: -38px; float: left; margin-left: 90px;"></div>
                </div>
                <div class="box-content" style="border: unset;overflow: unset;">
                    <div class="triangle" style="margin-right: 0; margin-top: -96px; color:#fff">
                        <table style="width: 100%">
                            <tr style="text-align: center; font-size: 13px">
                                <td class="row" style="height: 90px">
                                    <div style="width: 108px; border-right: 1px solid #577599; padding-top: 33px; margin-left: -6px;"><button title="Voltar as vendas" onclick="window.location = './transferlist&type=<?=$transferlist['type']?>'">&lt;&lt;</button></div>
                                    <div class="col-f" style="border-right: 1px solid #577599;">
                                        <div style="padding: 10px; padding-bottom: 0; text-align: left;">
                                            <?=$part[0];?>
                                        </div>
                                    </div>
                            
                                    <div class="col-f">
                                        <div style="padding: 10px; padding-bottom: 0; text-align: left;">
                                            <form method="post">
                                                <?=$part[1];?>
                                            </form>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        </table>                         
                    </div>
               </div>
    <?php
            }
        }
?> 
        <link rel="stylesheet" href="<?=$static_url?>/javascripts/rateyo/jquery.rateyo.css">

        <script src="<?=$static_url?>/javascripts/chartjs/Chart.js"></script>
        <script src="<?=$static_url?>/javascripts/rateyo/jquery.rateyo.js"></script>
        
        <div id="pokemon-amie" style="background-image: url('<?=$static_url?>/images/amie/<?=strtolower($pokemon['type'])?>.png'); height: 215px; margin-bottom: 6px;">
            <div class="pokemon-title"><?=$name?></div>

            <div style="text-align: center;height: 155px;vertical-align: middle;display: table-cell;">
                <?php
                    if (real_owner($pokemon['user_id'], $gebruiker['admin'])) {
                        $prev = (DB::exQuery("SELECT `id` FROM `pokemon_speler` WHERE id<$id AND `user_id`='$_SESSION[id]' ORDER BY id DESC LIMIT 1"))->fetch_assoc()['id'];

                        if (isset($prev)) {
                            echo '<a href="./pokemon-profile&id='.$prev.'"><button class="flickity-prev-next-button previous" type="button"></button></a>';
                        } else {
                            echo '<button class="flickity-prev-next-button previous" disabled type="button"></button>';
                        }
                    }
                ?>

                <img src="<?=$static_url.'/'.$pokemon['link']?>" alt="<?=$pokemon['naam']?>" id="pokemon-profile" style="position: relative;">


                <?php
                    if (real_owner($pokemon['user_id'], $gebruiker['admin'])) {
                        $next = (DB::exQuery("SELECT `id` FROM `pokemon_speler` WHERE id>$id AND `user_id`='$_SESSION[id]' ORDER BY id LIMIT 1"))->fetch_assoc()['id'];

                        if (isset($next)) {
                            echo '<a href="./pokemon-profile&id='.$next.'"><button class="flickity-prev-next-button next" type="button"><svg viewBox="0 0 100 100"><path d="M 10,50 L 60,100 L 70,90 L 30,50  L 70,10 L 60,0 Z" class="arrow" transform="translate(100, 100) rotate(180) "></path></svg></button></a>';
                        } else {
                            echo '<button class="flickity-prev-next-button next" disabled type="button"><svg viewBox="0 0 100 100"><path d="M 10,50 L 60,100 L 70,90 L 30,50  L 70,10 L 60,0 Z" class="arrow" transform="translate(100, 100) rotate(180) "></path></svg></button>';
                        }
                    }
                ?>
            </div>  
        </div>

        <div style="position: relative">
        <?php 
            if ($owner) { 
        ?>
            <div id="hp_exp" class="box-content" style="float: left; width: 49%; margin-bottom: 7px;">
                <table class="general" style="width: 100%; font-size: 14px">
                    <thead>
                        <th colspan="2">HP & EXP</th>
                    </thead>
                    <tbody>
                        <tr>
                            <td class="first" style="width: 70px">
                                <b>HP <span title="Pontos de vida" style="cursor: pointer">[?]</span></b>
                            </td>
                            <td class="last last-right">
                                <div class='bar_red' style="width: 98%; height: 13px" title="<?=highamount($pokemon['leven']).'/'.highamount($pokemon['levenmax'])?> HP">
                                    <div class='progress' style='width: <?=floor(($pokemon['leven'] * 100) / $pokemon['levenmax'])?>%;'></div>
                                </div>
                            </td>
                        </tr>
                        <tr>
                            <td class="first" style="width: 70px">
                                <b>EXP <span title="Experiência" style="cursor: pointer">[?]</span></b>
                            </td>
                            <td class="last last-right">
                                <div class='bar_blue' style="width: 98%; height: 13px" title="<?=($pokemon['level'] < 100)? highamount($pokemon['expnodig'] - $pokemon['exp']).' EXP para o próximo nível' : 'Seu Pokémon já está no nível máximo (100)!'?>">
                                    <div class="progress" style='width: <?=floor(($pokemon['exp'] / $pokemon['expnodig']) * 100)?>%;'></div>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        <?php
            }
        ?>

        <div id="caracteristicas" class="box-content" style="float: right; width: 49%; margin-bottom: 7px;">
            <table class="general" style="width: 100%; font-size: 13px">
                <thead>
                    <th colspan="2">Características</th>
                </thead>
                <tbody>
                    <tr>
                        <td class="first"><b>Adquirido </b><span class="capt_date"></span></td>
                        <td class="last last-right"><b>Cor:</b> <?=$shiny?></td>
                    </tr>
                    <tr>
                        <td class="first"><b>Negociável:</b> <?=$pokemon['can_trade'] == '0' ? 'Não Negociável' : 'Negociável'?></td>
                        <td class="last last-right"><b>Habilidade:</b> <a href="./information&category=ability-info&attack=<?=ability($pokemon['ability'])['name']?>"><?=ability($pokemon['ability'])['name']?></a></td>
                    </tr>
                    <tr>
                        <td class="first"><b>Level:</b> <?=$pokemon['level']?></td>
                        <td class="last last-right"><b>Humor:</b> <?=$pokemon['karakter']?><?=($pokemon['humor_change'] != 0 ? ' <sup>' . $pokemon['humor_change'] . '</sup>' : '')?></td>
                    </tr>
                    <tr>
                        <td class="first"><b>Poder total: </b> <?=highamount($pokemon['powertotal'])?></td>
                        <td class="last last-right"><b>Item:</b> <?=isset($pokemon['item'])? '<img src="'.$static_url.'/images/items/'.$pokemon['item'].'.png" title="Equipado com '.$pokemon['item'].'" style="vertical-align: middle">' : 'Nenhum';?></td>
                    </tr>
                    <tr>
                        <td class="first"><b>Espécie:</b> <a href="./pokedex&poke=<?=$pokemon['wild_id']?>"><?=$sql2['naam']?></a></td>
                        <td class="last last-right"><b>Pokéball:</b> <?='<img src="'.$static_url.'/images/items/'.$pokemon['gevongenmet'].'.png" title="Capturado com '.$pokemon['gevongenmet'].'" style="vertical-align: middle">'?></td>
                    </tr>
                </tbody>
            </table>
            <script>
                $('.capt_date').text(jQuery.timeago("<?=$pokemon['capture_date']?> UTC"));
            </script>
        </div>

        <div id="ataques" class="box-content" style="float: left; width: 49%; margin-bottom: 7px;">
            <table class="general" style="width: 100%; font-size: 14px">
                <thead>
                    <th>Lista de Ataques</th>
                </thead>
                <tbody>
                    <tr style="width: 100%">        
                        <td style="width: 100%; text-align: center">  
                        <?php
                            for ($i = 0; $i < 4; $i++) {
                                $aanval = $pokemon['aanval_'.($i+1)];

                                if ($aanval) {
                                    $margin = 21;

                                    if (empty($pokemon['aanval_4'])) {
                                        $margin = 18.5;
                                    }
                        ?>
                                    <a href="./information&category=attack-info&attack=<?=$aanval?>" style="font-weight: 400; margin: <?=12?>px"><button id="aanval" style="background: url(<?=$static_url?>/images/attack/moves/<?=atk($aanval, $pokemon)['soort']?>.png) no-repeat;" class="btn-type"><?=$aanval?></button></a>
                        <?php
                                }
                            }

                        ?>     
                        </td>         	
                    </tr>
                </tbody>
            </table>
        </div>

        <?php 
            if ($owner) { 
                $hp_max = max_calc ($sql2, 'Adamant')[0];
                $atk_max = max_calc ($sql2, 'Adamant')[1];
                $def_max = max_calc ($sql2, 'Bold')[2];
                $spatk_max = max_calc ($sql2, 'Mild')[3];
                $spdef_max = max_calc ($sql2, 'Sassy')[4];
                $speed_max = max_calc ($sql2, 'Timid')[5];

                $ev_tot = $pokemon['hp_ev'] + $pokemon['spc.attack_ev'] + $pokemon['attack_ev'] + $pokemon['spc.defence_ev'] + $pokemon['defence_ev'] + $pokemon['speed_ev'];
                
                $hp_poke = $pokemon['levenmax'];
                if ($pokemon['wild_id'] == '292') $hp_poke = 1;
        ?>

        <div id="tip" class="box-content" style="float: right; width: 49%; margin-bottom: 7px;">
            <table class="general" style="width: 100%; font-size: 13px">
                <thead>
                    <th colspan="2">TIP <span title="Training Indicator Percentage" style="cursor: pointer">[?]</span> <span style="float: right"><?=$ev_tot?> EV's TOTAIS</span></th>
                </thead>
                <tbody>
                    <tr>
                        <td class="first"><b>HP:</b> <?=$pokemon['hp_ev']?> EV's</td>
                        <td class="last last-right"><b>Sp. Ataque:</b> <?=$pokemon['spc.attack_ev']?> EV's</td>
                    </tr>
                    <tr>
                        <td class="first"><b>Ataque:</b> <?=$pokemon['attack_ev']?> EV's</td>
                        <td class="last last-right"><b>Sp. Defesa:</b> <?=$pokemon['spc.defence_ev']?> EV's</td>
                    </tr>
                    <tr>
                        <td class="first"><b>Defesa:</b> <?=$pokemon['defence_ev']?> EV's</td>
                        <td class="last last-right"><b>Speed:</b> <?=$pokemon['speed_ev']?> EV's</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div id="status" class="box-content" style="float: left; width: 49%; margin-bottom: 7px;">
            <table class="general" style="width: 100%; font-size: 14px">
                <thead>
                    <tr><th colspan="3">Status <span title="Status máximos são baseados em IV's max. (31), EV's max. (255), Vitaminas max. (25) e Natures béneficas." style="cursor: pointer">[?]</span><span style="float: right"><?= $pokemon['powertotal']; ?> TOTAIS</span></th></tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="first" style="width: 70px"><b>HP:</b></td>
                        <td>
                            <div id="starHP" style="margin: 0 auto"  data-rateyo-max-value="<?=$hp_max?>" data-rateyo-num-stars="10" data-rateyo-read-only="true" data-rateyo-star-width="20px" title="HP Máximo: <?=$hp_max?>"></div>
                            <script id="status_del">
                                $('#starHP').rateYo({ rating: "<?=($hp_poke/$hp_max)*100?>%", multiColor: { "startColor": "#FF0000", "endColor"  : "#F39C12" } });
                            </script>
                        </td>
                        <td class="last last-right">
                            <b><?=$hp_poke.' (+'.$pokemon['hp_up'].'<img src="'.$static_url.'/images/items/HP up.png" style="vertical-align: middle; width: 20px" title="HP UP">)'?></b>
                        </td>
                    </tr>
                    <tr>
                        <td class="first"><b>Ataque: </b></td>
                        <td>
                            <div id="starATK" style="margin: 0 auto"  data-rateyo-max-value="<?=$atk_max?>" data-rateyo-num-stars="10" data-rateyo-read-only="true" data-rateyo-star-width="20px" title="Ataque Máximo: <?=$atk_max?>"></div>
                            <script id="status_del">
                                $('#starATK').rateYo({ rating: "<?=($pokemon['attack']/$atk_max)*100?>%", multiColor: { "startColor": "#FF0000", "endColor"  : "#F39C12" } });
                            </script>
                        </td>
                        <td class="last last-right">
                            <b><?=$pokemon['attack'].' (+'.$pokemon['attack_up'].'<img src="'.$static_url.'/images/items/Protein.png" style="vertical-align: middle; width: 20px" title="Protein">)'?></b>
                        </td>
                    </tr>
                    <tr>
                        <td class="first"><b>Defesa: </b></td>
                        <td>
                            <div id="starDEF" style="margin: 0 auto"  data-rateyo-max-value="<?=$def_max?>" data-rateyo-num-stars="10" data-rateyo-read-only="true" data-rateyo-star-width="20px" title="Defesa Máxima: <?=$def_max?>"></div>
                            <script id="status_del">
                                $('#starDEF').rateYo({ rating: "<?=($pokemon['defence']/$def_max)*100?>%", multiColor: { "startColor": "#FF0000", "endColor"  : "#F39C12" } });
                            </script>
                        </td>
                        <td class="last last-right">
                            <b><?=$pokemon['defence'].' (+'.$pokemon['defence_up'].'<img src="'.$static_url.'/images/items/Iron.png" style="vertical-align: middle; width: 20px" title="Iron">)'?></b>
                        </td>
                    </tr>
                    <tr>
                        <td class="first" style="width: 100px"><b>Sp. Ataque: </b></td>
                        <td>
                            <div id="starSPATK" style="margin: 0 auto"  data-rateyo-max-value="<?=$spatk_max?>" data-rateyo-num-stars="10" data-rateyo-read-only="true" data-rateyo-star-width="20px" title="Sp. Ataque Máximo: <?=$spatk_max?>"></div>
                            <script id="status_del">
                                $('#starSPATK').rateYo({ rating: "<?=($pokemon['spc.attack']/$spatk_max)*100?>%", multiColor: { "startColor": "#FF0000", "endColor"  : "#F39C12" } });
                            </script>
                        </td>
                        <td class="last last-right">
                            <b><?=$pokemon['spc.attack'].' (+'.$pokemon['spc_up'].'<img src="'.$static_url.'/images/items/Calcium.png" style="vertical-align: middle; width: 20px" title="Calcium">)'?></b>
                        </td>
                    </tr>
                    <tr>
                        <td class="first"><b>Sp. Defesa: </b></td>
                        <td>
                            <div id="starSPDEF" style="margin: 0 auto"  data-rateyo-max-value="<?=$spdef_max?>" data-rateyo-num-stars="10" data-rateyo-read-only="true" data-rateyo-star-width="20px" title="Sp. Defesa Máxima: <?=$spdef_max?>"></div>
                            <script id="status_del">
                                $('#starSPDEF').rateYo({ rating: "<?=($pokemon['spc.defence']/$spdef_max)*100?>%", multiColor: { "startColor": "#FF0000", "endColor"  : "#F39C12" } });
                            </script>
                        </td>
                        <td class="last last-right">
                            <b><?=$pokemon['spc.defence'].' (+'.$pokemon['spc_up'].'<img src="'.$static_url.'/images/items/Calcium.png" style="vertical-align: middle; width: 20px" title="Calcium">)'?></b>
                        </td>
                    </tr>
                    <tr>
                        <td class="first"><b>Speed: </b></td>
                        <td>
                            <div id="starSPD" style="margin: 0 auto" data-rateyo-max-value="<?=$speed_max?>" data-rateyo-num-stars="10" data-rateyo-read-only="true" data-rateyo-star-width="20px" title="Speed Máxima: <?=$speed_max?>"></div>
                            <script id="status_del">
                                $('#starSPD').rateYo({ rating: "<?=($pokemon['speed']/$speed_max)*100?>%", multiColor: { "startColor": "#FF0000", "endColor"  : "#F39C12" } });
                                $('#status_del').remove();
                            </script>
                        </td>
                        <td class="last last-right">
                            <b><?=$pokemon['speed'].' (+'.$pokemon['speed_up'].'<img src="'.$static_url.'/images/items/Carbos.png" style="vertical-align: middle; width: 20px" title="Carbos">)'?></b>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div id="iv" class="box-content" style="float: right;width: 49%; margin-bottom: 7px;">
            <table class="general" style="width: 100%; font-size: 14px">
                <thead>
                    <th>IV's <span title="Individual Values" style="cursor: pointer">[?]</span><span style="float: right"><?php $iv_total = $pokemon['hp_iv'] + $pokemon['defence_iv'] + $pokemon['spc.attack_iv'] + $pokemon['speed_iv'] + $pokemon['spc.defence_iv'] + $pokemon['attack_iv']; if (!hasCalc($pokemon['has_calc'], $gebruiker['admin'])) $iv_total = '??'; echo $iv_total;?> IV's TOTAL</span></th>
                </thead>
                <tbody>
                    <tr>
                        <td style=" height: 200px;">                            
                            <center>
                                <?php if (!hasCalc($pokemon['has_calc'], $gebruiker['admin'])) { ?>
                                    <div style="border-radius: 4px;width: 47%; background: rgba(255, 255, 255, .4); height: 205px; position: absolute; line-height: 184px" title="Este Pokémon não tem suas IV's calculadas."><img src="<?=$static_url?>/images/icons/avatar/lock.png" style="width: 17%"></div>
                                <?php } ?>
                                <canvas id="radarChart" width="230" height="200"></canvas>
                            </center>

                            <script>
                                <?php if (hasCalc($pokemon['has_calc'], $gebruiker['admin'])) { ?>
                                let data_ivs = [<?=$pokemon['hp_iv']?>, <?=$pokemon['defence_iv']?>, <?=$pokemon['spc.attack_iv']?>, <?=$pokemon['speed_iv']?>, <?=$pokemon['spc.defence_iv']?>, <?=$pokemon['attack_iv']?>];
                                <?php } else { ?>
                                let data_ivs = [0, 0, 0, 0, 0, 0];
                                <?php } ?>

                                var radarData = {
                                    labels : ["HP", "Defesa", "Sp. Ataque", "Speed", "Sp. Defesa", "Ataque"],
                                    datasets : [{
                                        defaultFontColor: "#fff",
                                        fillColor: "transparent",
                                        strokeColor: "#fff",
                                        pointColor : "#fff",
                                        pointStrokeColor : "#000",
                                        data : data_ivs
                                    }]
                                };

                                var ctx2 = document.getElementById("radarChart").getContext("2d");
                                new Chart(ctx2).Radar(radarData);
                            </script>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>


        <?php } ?>

        </div>
<?php
    } else {
        require_once('notfound.php');
    }
}
?>