<table>
    <?php
    include("app/includes/resources/security.php");

    if (!isset($_GET['league_id']) || $_GET['league_id'] <= 0) {
        header("Location: ./league");
        exit("<script>location.href='./league'</script>");
    }

    include_once './app/classes/League.php';
    include_once './app/classes/League_award.php';
    include_once './app/classes/League_battle.php';

    $liga = new League();

    $liga->select($_GET['league_id']);

    if ($liga->getTotal_participantes() <= 16) {
        header("Location: ./tour");
        exit("<script>location.href='./tour'</script>");
    }
    ?>
    <tr>
        <td>
            <div style="text-align: center; max-width: 685px;">
                <img src="<?=$static_url?>/images/layout/liga_pokemon.png" alt="Liga Pokémon"/>
            </div>
        </td>
    </tr>
    <?php
    if ($liga->finalizada()) {
        ?>
        <tr>
            <td>
                <h3 style="width: 720px; margin-top: 15px;">Ranking</h3>
                <div style="height: 300px; max-width: 200px; overflow-y: scroll;">
                    <table id="league_ranking">
                        <tr>
                            <th>Pos.</th>
                            <th>Nome</th>
                            <th>Pontos</th>
                        </tr>
                        <?php
                        foreach ($liga->ranking() as $key => $player) {
                            ?>
                            <tr>
                                <td><?= $key + 1 ?> º</td>
                                <td><?= $player['username'] ?></td>
                                <td><?= $player['pontos'] ?></td>
                            </tr>
                            <?php
                        }
                        ?>
                    </table>
                    <script>$('#league_ranking').parent().css('max-width', $('#league_ranking').width() + 20);</script>
                </div>
            </td>
        </tr>
        <?php
    }

    if ($liga->getRound_atual() > 4) {
        ?>
        <tr>
            <td>
                <div style='width: auto; height: auto;'>
                    <h3 style="float: left; width: 720px; height: 50px; margin-top: 15px;">Mata-mata</h3>
                    <div id='gracket_<?= $liga->getId() ?>'></div>
                    <div id='gracket2_<?= $liga->getId() ?>' style='float: left; bottom: 300px; left: 730px;'></div>
                </div>
                <?= $liga->tabela_matamata() ?>
            </td>
        </tr>
        <?php
    }
    ?>
    <?php
    for ($i = ($liga->getRound_atual() < 4 ? $liga->getRound_atual() : 4); $i > 0; $i--) {
        $preeliminares = $liga->lista_preeliminares($i);
        ?>
        <tr>
            <td>
                <div class="preeliminar round<?= $i ?>">
                    <h3 style="float: left; width: 720px; height: 50px; margin-top: 15px;">Round <?= $i ?><span style="padding-left: 100px; font-size: 0.9em;">Início às <?= $liga->inicio_round($i) ?></span></h3>
                    <?php
                    foreach ($preeliminares as $campo => $batalhas) {
                        ?>
                        <div class="campo <?= $campo ?>">
                            <p style="font-weight: bold; font-size: 1.1em;text-align: center"><?php
                                switch ($campo) {
                                    case 'water':
                                        echo "Arena de água";
                                        break;
                                    case 'ice':
                                        echo "Arena de gelo";
                                        break;
                                    case 'rock':
                                        echo "Arena de pedra";
                                        break;
                                    default:
                                        echo "Arena de grama";
                                        break;
                                }
                                ?></p>
                            <ul>
                                <?php
                                foreach ($batalhas as $batalha) {
                                    ?>
                                    <li>
                                        <span class="player1" <?= ($batalha['user1_id'] == $batalha['vencedor'] ? "style=\"font-weight: bold; background-color: yellow; color: black; border-radius: 5px; padding: 3px;\"" : "") ?>><?= $batalha['user1_username'] ?> <?= $batalha['user1_pontos'] ?></span>
                                        <span class="x"> X </span>
                                        <?php if (isset($batalha['user2_pontos'])) { ?>
                                            <span class = "player2"  <?= ($batalha['user2_id'] == $batalha['vencedor'] ? "style=\"font-weight: bold; background-color: yellow; color: black; border-radius: 5px; padding: 3px;\"" : "") ?>><?= $batalha['user2_pontos'] ?> <?= $batalha['user2_username'] ?></span>
                                        <?php } else { ?>
                                            <span class = "player2">Sem oponente</span>
                                        <?php } ?>
                                    </li>
                                    <?php
                                }
                                ?>
                            </ul>
                        </div>
                        <?php
                    }
                    ?>
                </div>
            </td>
        </tr>
        <?php
    }
    ?>
</table>