<?php
//Als de gebruiker rank lager dan 5 is wordt hij terug gestuurd naar index.php
if ($gebruiker['rank'] < 5)
    header('Location: index.php');
//Script laden zodat je nooit pagina buiten de index om kan laden
include("app/includes/resources/security.php");

$page = 'tour';

require_once './app/classes/League.php';
require_once './app/classes/League_award.php';

$ligas = League::select_atuais(true, null, 1);
?>
<h3>Próximo torneio:</h3>
<?php
if (count($ligas)) {
    $liga = $ligas['0'];
    //NOW() - INTERVAL 4 HOUR - INTERVAL 2 MINUTE - INTERVAL 17 SECOND
    $time = time() + League::$ajuste_tempo_int;

    if (isset($_POST['registration']) && $_POST['league_id'] == $liga->getId()) {
        $liga->select($liga->getId());
        $liga->inscrever($gebruiker['user_id']);
    } else if (isset($_POST['undo_registration']) && $_POST['league_id'] == $liga->getId()) {
        $liga->desfazer_inscricao($gebruiker['user_id']);
    }
    ?>
    <table style="border-bottom: solid 2px;">
        <tr>
            <td style="font-weight: bold; font-size: 1.2em;">
                Torneio na região de <?= $liga->getRegiao() ?>
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
                    Você não está na região deste torneio!
                </td>
                <?php
            } else { 
                ?>
                <td style="font-weight: bold; font-size: 1.1em;">
                    <?php
                    if ($liga->inscrito($gebruiker['user_id'])) {
                        ?>
                        <img src="<?=$static_url?>/images/icons/green.png" alt="confirm"/>
                        Você está inscrito neste torneio!
                        <?php
                    } else {
                        ?>
                        <img src="<?=$static_url?>/images/icons/red.png" alt="no_confirm"/>
                        Você não está participanto deste torneio!
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
                                <input type="submit" name="registration" value="Fazer inscrição" class="button" onclick="if (confirm('Deseja mesmo fazer a inscrição nesse torneio?!') == false) {
                                            return false;
                                        }"/>
                                       <?php
                                   } else {
                                       ?>
                                <input type="submit" name="undo_registration" value="Desfazer inscrição" class="button" onclick="if (confirm('Tem certeza que deseja desfazer a  sua inscrição nesse torneio?<br/>Obs.: Os custos de inscrição não serão devolvidos!') == false) {
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
                    echo '<img src="'.$static_url.'/images/icons/silver.png" alt="silver"/> ';
                    echo $liga->getPreco_silvers();
                    echo " silvers";
                    $virgula = true;
                }
                if ($liga->getPreco_golds()) {
                    if ($virgula) {
                        echo ", ";
                    }
                    echo '<img src="'.$static_url.'/images/icons/gold.png" alt="gold"/> ';
                    echo $liga->getPreco_golds();
                    echo " golds";
                    $virgula = true;
                }
                if ($liga->getVip()) {
                    if ($virgula) {
                        echo ", ";
                    }
                    echo '<img src="'.$static_url.'/images/icons/star.png" alt="vip"/> ';
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
                        echo '<img src="'.$static_url.'/images/icons/silver.png" alt="silver"/> ';
                        echo $premio->getSilvers();
                        echo " silvers";
                        $virgula = true;
                    }
                    if ($premio->getGolds()) {
                        if ($virgula) {
                            echo ", ";
                        }
                        echo '<img src="'.$static_url.'/images/icons/gold.png" alt="gold"/> ';
                        echo $premio->getGolds();
                        echo " golds";
                        $virgula = true;
                    }
                    if ($premio->getPokemon_id()) {
                        if ($virgula) {
                            echo ", ";
                        }
                        echo '<img src="'.$static_url.'/images/pokemon/icon/' . $premio->getPokemon_id() . '.gif" alt="pokemon"/> ';
                        $nome = DB::exQuery("SELECT `naam` FROM `pokemon_wild` WHERE `wild_id`='" . $premio->getPokemon_id() . "'")->fetch_assoc();
						echo $nome['naam'];
                        echo " lv " . $premio->getLv_pokemon();
                        $virgula = true;
                    }
                    if ($premio->getVip) {
                        if ($virgula) {
                            echo ", ";
                        }
                        echo '<img src="'.$static_url.'/images/icons/star.png" alt="vip"/> ';
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
    <?php
} else {
    ?>
    <div style="font-weight: bold; font-size: 1.2em;">Nenhum torneio agendado!</div>
    <?php
}
?>

<h3 style="margin-top: 30px;">Últimos torneios:</h3>
<ul style="list-style: none;">
    <?php
    $ligas = League::select_terminadas(true, null, 4);

    foreach ($ligas as $liga) {
        ?>
        <li>
            <table style="border-bottom: solid 2px;">
                <tr>
                    <td style="font-weight: bold; font-size: 1.2em;">
                        Torneio na região de <?= $liga->getRegiao() ?>
                    </td>
                </tr>
                <tr>
                    <td style="padding-right: 10px;">
                        <p>
                            Inscrições a partir de <?= date("d/m/Y à\s H:i:s", strtotime($liga->getInicio_inscricoes())) ?> até <?= date("d/m/Y à\s H:i:s", strtotime($liga->getFim_inscricoes())) ?>
                        </p>
                        <p>
                            Início das batalhas em <?= date("d/m/Y à\s H:i:s", strtotime($liga->getInicio())) ?>
                        </p>
                        <p>
                            Intervalo entre os rounds: <?= number_format($liga->getIntervalo_fase() / 60, 0) ?> minutos
                        </p>
                        <p>
                            <span style="font-weight: bold; font-size: 1.1em;">Regras:</span><br/>
                            Nível máximo dos pokémon: <?= $liga->getLv_max_pokemon() ?><br/>
                            Número máximo de pokémons especiais por batalha:<br/>
                            Shinys: <?= $liga->getN_shinys() ?><br/>
                            Lendários: <?= $liga->getN_lendas() ?><br/>
                            Mega evoluções: <?= $liga->getN_megas() ?>
                        </p>
                    </td>
                    <td>
                        <p>
                            <span style="font-weight: bold; font-size: 1.1em;">Custo de inscrição:</span><br/>
                            <?php
                            if ($liga->getPreco_silvers()) {
                                echo '<img src="'.$static_url.'/images/icons/silver.png" alt="silver"/> ';
                                echo $liga->getPreco_silvers();
                                echo " silvers";
                                $virgula = true;
                            }
                            if ($liga->getPreco_golds()) {
                                if ($virgula) {
                                    echo ", ";
                                }
                                echo '<img src="'.$static_url.'/images/icons/gold.png" alt="gold"/> ';
                                echo $liga->getPreco_golds();
                                echo " golds";
                                $virgula = true;
                            }
                            if ($liga->getVip()) {
                                if ($virgula) {
                                    echo ", ";
                                }
                                echo '<img src="'.$static_url.'/images/icons/star.png" alt="vip"/> ';
                                echo "VIP";
                            }
                            ?>
                        </p>
                        <p>
                            <span style="font-weight: bold; font-size: 1.1em;">Premiação:</span><br/>
                            <?php
                            foreach (League_award::select_league($liga->getId()) as $premio) {
                                $virgula = false;
                                echo "<p>" . $premio->getColocacao() . "º - ";
                                if ($premio->getSilvers()) {
                                    echo '<img src="'.$static_url.'/images/icons/silver.png" alt="silver"/> ';
                                    echo $premio->getSilvers();
                                    echo " silvers";
                                    $virgula = true;
                                }
                                if ($premio->getGolds()) {
                                    if ($virgula) {
                                        echo ", ";
                                    }
                                    echo '<img src="'.$static_url.'/images/icons/gold.png" alt="gold"/> ';
                                    echo $premio->getGolds();
                                    echo " golds";
                                    $virgula = true;
                                }
                                if ($premio->getPokemon_id()) {
                                    if ($virgula) {
                                        echo ", ";
                                    }
                                    echo '<img src="'.$static_url.'/images/pokemon/icon/' . $premio->getPokemon_id() . '.gif" alt="pokemon"/> ';
                                    $nome = DB::exQuery("SELECT `naam` FROM `pokemon_wild` WHERE `wild_id`='" . $premio->getPokemon_id() . "'")->fetch_assoc();
									echo $nome['naam'];
                                    echo " lv " . $premio->getLv_pokemon();
                                    $virgula = true;
                                }
                                if ($premio->getVip) {
                                    if ($virgula) {
                                        echo ", ";
                                    }
                                    echo '<img src="'.$static_url.'/images/icons/star.png" alt="vip"/> ';
                                    echo $premio->getVip() . "dias de VIP";
                                }
                                echo "</p>";
                            }
                            ?>
                        </p>
                    </td>
                </tr>
                <tr>
                    <td>
                        <div style="width: 400px; height: auto;">
                            <div id='gracket_<?= $liga->getId() ?>'></div>
                            <div id='gracket2_<?= $liga->getId() ?>' style='float: left; bottom: 300px; left: 730px;'></div>
                            <?php
                            if ($liga->getRound_atual()) {
                                echo $liga->tabela_matamata();
                            }
                            ?>
                        </div>
                    </td>
                </tr>
            </table>
        </li>
        <?php
    }
    if (count($ligas) == 0) {
        ?>
        <div style="font-weight: bold; font-size: 1.2em;">Nenhum histórico de torneio!</div>
        <?php
    }
    ?>
</ul>