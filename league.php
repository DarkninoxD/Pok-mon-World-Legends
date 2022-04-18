<?php
if ($gebruiker['rank'] < 5)
    header('Location: index.php');
//Script laden zodat je nooit pagina buiten de index om kan laden
include("app/includes/resources/security.php");

$page = 'league';

require_once './app/classes/League.php';
require_once './app/classes/League_award.php';

$ligas = League::select_atuais();

//NOW() - INTERVAL 4 HOUR - INTERVAL 2 MINUTE - INTERVAL 17 SECOND
$time = time() + League::$ajuste_tempo_int;
?>
<div style="margin-bottom: 30px; text-align: center;">
    <img src="<?=$static_url?>/images/layout/liga_pokemon.png" alt="Liga Pokémon"/>
</div>

<h3>Ligas com inscrições abertas</h3>
<ul style="list-style: none;">
    <?php
    foreach ($ligas as $liga) {
        if (isset($_POST['registration']) && $_POST['league_id'] == $liga->getId()) {
            $liga->select($liga->getId());
            $liga->inscrever($gebruiker['user_id']);
        } else if (isset($_POST['undo_registration']) && $_POST['league_id'] == $liga->getId()) {
            $liga->desfazer_inscricao($gebruiker['user_id']);
        }
        ?>
        <li>
            <table style="border-bottom: solid 2px;">
                <tr>
                    <td style="font-weight: bold; font-size: 1.2em;">
                        Liga na região de <?= $liga->getRegiao() ?>
                    </td>
                </tr>
                <tr>
                    <td style="padding-right: 10px;">
                        Inscrições a partir de <?= date("d/m/Y à\s H:i:s", strtotime($liga->getInicio_inscricoes())) ?> até <?= date("d/m/Y à\s H:i:s", strtotime($liga->getFim_inscricoes())) ?>
                    </td>
                    <?php
                    if ($gebruiker['wereld'] != $liga->getRegiao()) {
                        ?>
                        <td style="font-weight: bold; font-size: 1.1em; margin-left: 10px;">
                            Você não está na região desta liga!
                        </td>
                        <?php
                    } else {
                        ?>
                        <td style="font-weight: bold; font-size: 1.1em;">
                            <?php
                            if ($liga->inscrito($gebruiker['user_id'])) {
                                ?>
                                <img src="images/icons/green.png" alt="confirm"/>
                                Você está inscrito nesta liga!
                                <?php
                            } else {
                                ?>
                                <img src="images/icons/red.png" alt="no_confirm"/>
                                Você não está participanto desta liga!
                                <?php
                            }
                            ?>
                        </td>
                        <?php
                    }
                    ?>
                </tr>
                <tr>
                    <td>Início das batalhas em <?= date("d/m/Y à\s H:i:s", strtotime($liga->getInicio())) ?></td>
                    <?php
                    if ($gebruiker['wereld'] != $liga->getRegiao()) {
                        ?>
                        <td style="text-align: center;">
                            <a href="./travel" class="button_mini" style="padding: 7px 6px; border-radius: 7px;">Viajar</a>
                        </td>
                        <?php
                    } else {
                        ?>
                        <td>
                            vagas: <?= ($liga->getTotal_participantes() - $liga->getParticipantes()) ?>
                        </td>
                        <?php
                    }
                    ?>
                </tr>
                <tr>
                    <td style="font-weight: bold; font-size: 1.1em;">Custo de inscrição:</td>
                    <?php
                    if ($gebruiker['wereld'] == $liga->getRegiao()) {
                        ?>
                        <td style="text-align: center;">
                            <?php
                            if ($time >= strtotime($liga->getInicio_inscricoes()) && $time <= strtotime($liga->getFim_inscricoes())) {
                                ?>
                                <form method="post">
                                    <input type="hidden" name="league_id" value="<?= $liga->getId() ?>"/>
                                    <?php
                                    if (!$liga->inscrito($gebruiker['user_id'])) {
                                        ?>
                                        <input type="submit" name="registration" value="Fazer inscrição" class="button" onclick="if (confirm('Deseja mesmo fazer a inscrição nessa liga?!') == false) {
                                                    return false;
                                                }"/>
                                               <?php
                                           } else {
                                               ?>
                                        <input type="submit" name="undo_registration" value="Desfazer inscrição" class="button" onclick="if (confirm('Tem certeza que deseja desfazer a  sua inscrição nessa liga?<br/>Obs.: Os custos de inscrição não serão devolvidos!') == false) {
                                                    return false;
                                                }"/>
                                               <?php
                                           }
                                           ?>
                                </form>
                                <?php
                            } else {
                                echo "As inscrições serão abertas em breve!";
                            }
                            ?>
                        </td>
                        <?php
                    }
                    ?>
                </tr>
                <tr>
                    <td>
                        <?php
                        if ($liga->getPreco_silvers()) {
                            echo '<img src="images/icons/silver.png" alt="silver"/> ';
                            echo $liga->getPreco_silvers();
                            echo " silvers";
                            $virgula = true;
                        }
                        if ($liga->getPreco_golds()) {
                            if ($virgula) {
                                echo ", ";
                            }
                            echo '<img src="images/icons/gold.png" alt="gold"/> ';
                            echo $liga->getPreco_golds();
                            echo " golds";
                            $virgula = true;
                        }
                        if ($liga->getVip()) {
                            if ($virgula) {
                                echo ", ";
                            }
                            echo '<img src="images/icons/star.png" alt="vip"/> ';
                            echo "VIP";
                        }
                        ?>
                    </td>
                </tr>
                <tr>
                    <td style="font-weight: bold; font-size: 1.1em;">Premiação:</td>
                </tr>
                <tr>
                    <td>
                        <?php
                        foreach (League_award::select_league($liga->getId()) as $premio) {
                            $virgula = false;
                            echo "<p>" . $premio->getColocacao() . "º - ";
                            if ($premio->getSilvers()) {
                                echo '<img src="images/icons/silver.png" alt="silver"/> ';
                                echo $premio->getSilvers();
                                echo " silvers";
                                $virgula = true;
                            }
                            if ($premio->getGolds()) {
                                if ($virgula) {
                                    echo ", ";
                                }
                                echo '<img src="images/icons/gold.png" alt="gold"/> ';
                                echo $premio->getGolds();
                                echo " golds";
                                $virgula = true;
                            }
                            if ($premio->getPokemon_id()) {
                                if ($virgula) {
                                    echo ", ";
                                }
                                echo '<img src="images/pokemon/icon/' . $premio->getPokemon_id() . '.gif" alt="pokemon"/> ';
                                echo (DB::exQuery("SELECT `naam` FROM `pokemon_wild` WHERE `wild_id`='" . $premio->getPokemon_id() . "'"))->fetch_assoc()['0'];
                                echo " lv " . $premio->getLv_pokemon();
                                $virgula = true;
                            }
                            if ($premio->getVip()) {
                                if ($virgula) {
                                    echo ", ";
                                }
                                echo '<img src="images/icons/star.png" alt="vip"/> ';
                                echo $premio->getVip() . "dias de VIP";
                            }
                            echo "</p>";
                        }
                        ?>
                    </td>
                </tr>
                <tr>
                    <td style="font-weight: bold; font-size: 1.1em;">Regras:</td>
                </tr>
                <tr>
                    <td>
                        Nível máximo dos pokémon: <?= $liga->getLv_max_pokemon() ?><br/>
                        Número máximo de pokémons especiais por batalha:<br/>
                        Shinys: <?= $liga->getN_shinys() ?><br/>
                        Lendários: <?= $liga->getN_lendas() ?><br/>
                        Mega evoluções: <?= $liga->getN_megas() ?>
                    </td>
                </tr>
                <tr>
                    <td colspan = "2" style = "text-align: center;">
                        <?php
                        foreach ($liga->erros as $erro) {
                            ?>
                            <div style="font-weight: bold; color: black; background-color: #ff6666; border: solid 3px red; border-radius: 5px;"><?= $erro ?></div>
                            <?php
                        }
                        ?>
                    </td>
                </tr>
            </table>
        </li>
        <?php
    }
    if (count($ligas) == 0) {
        ?>
        <div style="font-weight: bold; font-size: 1.2em;">Nenhuma liga com inscrições abertas no momento!</div>
        <?php
    }
    ?>
</ul>

<h3 style="margin-top: 30px;">Ligas com inscrições finalizadas</h3>
<ul style="list-style: none;">
    <?php
    $ligas = League::select_terminadas();

    foreach ($ligas as $liga) {
        ?>
        <li>
            <table style="border-bottom: solid 2px;">
                <tr>
                    <td style="font-weight: bold; font-size: 1.2em;">
                        Liga na região de <?= $liga->getRegiao() ?>
                    </td>
                </tr>
                <tr>
                    <td style="padding-right: 10px;">
                        Inscrições a partir de <?= date("d/m/Y à\s H:i:s", strtotime($liga->getInicio_inscricoes())) ?> até <?= date("d/m/Y à\s H:i:s", strtotime($liga->getFim_inscricoes())) ?>
                    </td>
                    <?php
                    if ($gebruiker['wereld'] != $liga->getRegiao()) {
                        ?>
                        <td style="font-weight: bold; font-size: 1.1em; margin-left: 10px;">
                            Você não está na região desta liga!
                        </td>
                        <?php
                    } else {
                        ?>
                        <td style="font-weight: bold; font-size: 1.1em;">
                            <?php
                            if ($liga->inscrito($gebruiker['user_id'])) {
                                ?>
                                <img src="images/icons/green.png" alt="confirm"/>
                                Você está inscrito nesta liga!
                                <?php
                            } else {
                                ?>
                                <img src="images/icons/red.png" alt="no_confirm"/>
                                Você não está participanto desta liga!
                                <?php
                            }
                            ?>
                        </td>
                        <?php
                    }
                    ?>
                </tr>
                <tr>
                    <td>Início das batalhas em <?= date("d/m/Y à\s H:i:s", strtotime($liga->getInicio())) ?></td>
                    <?php
                    if ($gebruiker['wereld'] != $liga->getRegiao()) {
                        ?>
                        <td style="text-align: center;">
                            <a href="./travel" class="button_mini" style="padding: 7px 6px; border-radius: 7px;">Viajar</a>
                        </td>
                        <?php
                    } else {
                        ?>
                        <td>
                        </td>
                        <?php
                    }
                    ?>
                </tr>
                <tr>
                    <td style="font-weight: bold; font-size: 1.1em;">Custo de inscrição:</td>
                    <?php
                    if ($gebruiker['wereld'] == $liga->getRegiao()) {
                        ?>
                        <td style="text-align: center;">
                            <a href="./league_status&league_id=<?= $liga->getId() ?>" class="button" style="padding: 7px 6px;">Acompanhar liga</a>
                        </td>
                        <?php
                    }
                    ?>
                </tr>
                <tr>
                    <td>
                        <?php
                        if ($liga->getPreco_silvers()) {
                            echo '<img src="images/icons/silver.png" alt="silver"/> ';
                            echo $liga->getPreco_silvers();
                            echo " silvers";
                            $virgula = true;
                        }
                        if ($liga->getPreco_golds()) {
                            if ($virgula) {
                                echo ", ";
                            }
                            echo '<img src="images/icons/gold.png" alt="gold"/> ';
                            echo $liga->getPreco_golds();
                            echo " golds";
                            $virgula = true;
                        }
                        if ($liga->getVip()) {
                            if ($virgula) {
                                echo ", ";
                            }
                            echo '<img src="images/icons/star.png" alt="vip"/> ';
                            echo "VIP";
                        }
                        ?>
                    </td>
                </tr>
                <tr>
                    <td style="font-weight: bold; font-size: 1.1em;">Premiação:</td>
                </tr>
                <tr>
                    <td>
                        <?php
                        foreach (League_award::select_league($liga->getId()) as $premio) {
                            $virgula = false;
                            echo "<p>" . $premio->getColocacao() . "º - ";
                            if ($premio->getSilvers()) {
                                echo '<img src="images/icons/silver.png" alt="silver"/> ';
                                echo $premio->getSilvers();
                                echo " silvers";
                                $virgula = true;
                            }
                            if ($premio->getGolds()) {
                                if ($virgula) {
                                    echo ", ";
                                }
                                echo '<img src="images/icons/gold.png" alt="gold"/> ';
                                echo $premio->getGolds();
                                echo " golds";
                                $virgula = true;
                            }
                            if ($premio->getPokemon_id()) {
                                if ($virgula) {
                                    echo ", ";
                                }
                                echo '<img src="images/pokemon/icon/' . $premio->getPokemon_id() . '.gif" alt="pokemon"/> ';
                                echo (DB::exQuery("SELECT `naam` FROM `pokemon_wild` WHERE `wild_id`='" . $premio->getPokemon_id() . "'"))->fetch_assoc()['0'];
                                echo " lv " . $premio->getLv_pokemon();
                                $virgula = true;
                            }
                            if ($premio->getVip()) {
                                if ($virgula) {
                                    echo ", ";
                                }
                                echo '<img src="images/icons/star.png" alt="vip"/> ';
                                echo $premio->getVip() . "dias de VIP";
                            }
                            echo "</p>";
                        }
                        ?>
                    </td>
                </tr>
                <tr>
                    <td style="font-weight: bold; font-size: 1.1em;">Regras:</td>
                </tr>
                <tr>
                    <td>
                        Nível máximo dos pokémon: <?= $liga->getLv_max_pokemon() ?><br/>
                        Número máximo de pokémons especiais por batalha:<br/>
                        Shinys: <?= $liga->getN_shinys() ?><br/>
                        Lendários: <?= $liga->getN_lendas() ?><br/>
                        Mega evoluções: <?= $liga->getN_megas() ?>
                    </td>
                </tr>
            </table>
        </li>
        <?php
    }
    if (count($ligas) == 0) {
        ?>
        <div style="font-weight: bold; font-size: 1.2em;">Nenhuma liga com inscrições finalizadas!</div>
        <?php
    }
    ?>
</ul>