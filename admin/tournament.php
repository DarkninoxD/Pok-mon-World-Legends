<?php
//Script laden zodat je nooit pagina buiten de index om kan laden
include("app/includes/resources/security.php");

//Admin controle
if ($gebruiker['admin'] < 2) {
    header('location: ./home');
    exit; 
}

include_once 'app/classes/League.php';
include_once 'app/classes/League_award.php';
include_once 'app/classes/League_battle.php';

if (isset($_POST['new'])) {
    $league = new League();

    $league->setRegiao($_POST['regiao']);
    $league->setTotal_participantes($_POST['total_participantes']);
    $league->setInicio_inscricoes($_POST['inicio_inscricoes']);
    $league->setFim_inscricoes($_POST['fim_inscricoes']);
    $league->setInicio($_POST['inicio']);
    $league->setIntervalo_fase($_POST['intervalo_fase']);
    $league->setPreco_silvers($_POST['preco_silvers']);
    $league->setPreco_golds($_POST['preco_golds']);
    $league->setN_shinys($_POST['n_shinys']);
    $league->setN_lendas($_POST['n_lendas']);
    $league->setN_megas($_POST['n_megas']);
    $league->setLv_max_pokemon($_POST['lv_max_pokemon']);
    $league->setVip($_POST['vip']);
    $league->setMods($_POST['mods']);
    $league->setAdmins($_POST['adms']);
    $league->setDonos($_POST['donos']);

    if ($league->insert()) {
        ?>
        <div style="float: left; width: auto; height: auto; font-weight: bold; color: lime;">
            Torneio id:<?= $league->getId() ?> criado!
        </div>
        <?php
    } else {
        ?>
        <div style="float: left; width: auto; height: auto; font-weight: bold; color: red;">
            Erro ao criar torneio!
        </div>
        <?php
    }
} else if (isset($_POST['new_award'])) {
    $league_award = new League_award();

    $league_award->setLeague_id($_POST['league_id']);
    $league_award->setColocacao($_POST['colocacao']);
    $league_award->setSilvers($_POST['silvers']);
    $league_award->setGolds($_POST['golds']);
    $league_award->setVip($_POST['vip']);
    $league_award->setPokemon_id($_POST['pokemon_id']);
    $league_award->setLv_pokemon($_POST['lv_pokemon']);

    if ($league_award->insert()) {
        ?>
        <div style="float: left; width: auto; height: auto; font-weight: bold; color: lime;">
            Premiação criada no torneio id:<?= $league_award->getLeague_id() ?>!
        </div>
        <?php
    } else {
        ?>
        <div style="float: left; width: auto; height: auto; font-weight: bold; color: red;">
            Erro ao criar premiação!
        </div>
        <?php
    }
} else if (isset($_POST['criar_duelos'])) {
  $league = new League();
  $league->select($_POST['league_id']);

  $league->criar_duelos();

  $msg_form = "msg_form_" . $league->getId();
  $msg_form = "Duelos criados!";
  } else if (isset($_POST['passar_round'])) {
  $league = new League();
  $league->select($_POST['league_id']);

  $league->passar_round();

  $msg_form = "msg_form_" . $league->getId();
  $msg_form = "Round atualizado!";
  } else if (isset($_POST['entregar_premiacao'])) {
  $league = new League();
  $league->select($_POST['league_id']);

  $league->entregar_premiacao();

  $msg_form = "msg_form_" . $league->getId();
  $msg_form = "Premiações entregues!";
}

if (isset($_GET['new'])) {
    ?>
    <div style="float: left; width: auto; height: auto;">
        <hr/>
        <form method="post" action="./admin/tournament">
            <h2>Criar novo torneio:</h2><br/>
            <label>Região: 
                <select name="regiao">
                    <option value="Kanto">Kanto</option>
                </select>
            </label>
            <br/><br/>
            <label>Total de participantes: 
                <select name="total_participantes">
                    <option value="8">8</option>
                    <option value="16">16</option>
                </select>
            </label>
            <br/><br/>
            <label>Início das inscrições: 
                <input type="text" name="inicio_inscricoes" class="text_long" value="<?= date("Y-m-d H:i:s", time() + League::$ajuste_tempo_int) ?>" maxlength="19"/>
                <br/>
                <small>Modelo: Y-m-d H:i:s, Exemplo: 2014-01-04 14:30:00</small>
            </label>
            <br/><br/>
            <label>Fim das inscrições: 
                <input type="text" name="fim_inscricoes" class="text_long" value="<?= date("Y-m-d H:i:s", time() + League::$ajuste_tempo_int + 600) ?>" maxlength="19"/>
                <br/>
                <small>Modelo: Y-m-d H:i:s, Exemplo: 2014-01-04 14:30:00</small>
            </label>
            <br/><br/>
            <label>Início das batalhas: 
                <input type="text" name="inicio" class="text_long" value="<?= date("Y-m-d H:i:s", time() + League::$ajuste_tempo_int + 600 + 300) ?>" maxlength="19"/>
                <br/>
                <small>Modelo: Y-m-d H:i:s, Exemplo: 2014-01-04 14:30:00</small>
            </label>
            <br/><br/>
            <label>Intervalo de tempo entre as fases: 
                <input type="text" name="intervalo_fase" class="text_long" value="900"/>
                <br/>
                <small>em segundos, Exemplo: 900 | 900 segundos = 15 minutos</small>
            </label>
            <br/><br/>
            <label>Preço de inscrição em silvers: 
                <input type="text" name="preco_silvers" class="text_long" value="0"/>
            </label>
            <br/><br/>
            <label>Preço de inscrição em golds: 
                <input type="text" name="preco_golds" class="text_long" value="0"/>
            </label>
            <br/><br/>
            <label>VIP: &nbsp;
                <input type="radio" name="vip" value="1"> Sim&nbsp;
                <input type="radio" name="vip" value="0" checked="true"> Não
            </label>
            <br/>
            <label>Moderadores podem participar: &nbsp;
                <input type="radio" name="mods" value="1"> Sim&nbsp;
                <input type="radio" name="mods" value="0" checked="true"> Não
            </label>
            <br/>
            <label>Administradores podem participar: &nbsp;
                <input type="radio" name="adms" value="1"> Sim&nbsp;
                <input type="radio" name="adms" value="0" checked="true"> Não
            </label>
            <br/>
            <label>Donos podem participar: &nbsp;
                <input type="radio" name="donos" value="1"> Sim&nbsp;
                <input type="radio" name="donos" value="0" checked="true"> Não
            </label>
            <br/><br/>
            <label>Número máximo de pokémon shinys por batalha: 
                <input type="text" name="n_shinys" class="text_long" value="6"/>
            </label>
            <br/><br/>
            <label>Número máximo de pokémon lendários por batalha: 
                <input type="text" name="n_lendas" class="text_long" value="6"/>
            </label>
            <br/><br/>
            <label>Número máximo de pokémon mega evoluidos por batalha: 
                <input type="text" name="n_megas" class="text_long" value="6"/>
            </label>
            <br/><br/>
            <label>Nível maxímo dos pokémon: 
                <input type="text" name="lv_max_pokemon" class="text_long" value="100"/>
            </label>
            <br/><br/>

            <input type="submit" name="new" value="Criar torneio" class="button">
            <input type="button" value="Cancelar" onclick="location.href = '/admin/tournament'" class="button"/>
        </form>
    </div>
    <?php
} else if (isset($_GET['new_award'])) {
    ?>
    <div style="float: left; width: auto; height: auto;">
        <hr/>
        <form method="post">
            <h2>Criar nova premiação:</h2><br/>

            <label>Id do torneio: 
                <input type="text" name="league_id" class="text_long" value="<?= (isset($_GET['league_id']) ? $_GET['league_id'] : "1")?>"/>
            </label>
            <br/><br/>
            <label>Colocação: 
                <input type="text" name="colocacao" class="text_long" value="1"/>
            </label>
            <br/><br/>
            <label>Silvers: 
                <input type="text" name="silvers" class="text_long" value="0"/>
            </label>
            <br/><br/>
            <label>Golds: 
                <input type="text" name="golds" class="text_long" value="0"/>
            </label>
            <br/><br/>
            <label>VIP: 
                <input type="text" name="vip" class="text_long" value="0"/>
            </label>
            <br/><br/>
            <label>Id do pokémon: 
                <input type="text" name="pokemon_id" class="text_long" value="0"/>
            </label>
            <br/><br/>
            <label>Lv do pokémon: 
                <input type="text" name="lv_pokemon" class="text_long" value="1"/>
            </label>
            <br/><br/>

            <input type="submit" name="new_award" value="Criar premiaçao" class="button">
            <input type="button" value="Cancelar" onclick="location.href = '/admin/tournament'" class="button"/>
        </form>
    </div>
    <?php
} else {
    $ligas_atuais = League::select_atuais(true);
    $ligas_terminadas = League::select_terminadas(true, null, 8);
    ?>
    <div style="float: left; width: auto; height: auto; margin: 10px;">
        <input type="button" value="Criar novo torneio" onclick="location.href = '/admin/tournament&new'" class="button"/> &nbsp;
        <input type="button" value="Criar premiação" onclick="location.href = '/admin/tournament&new_award'" class="button"/>
    </div>
    <div style="float: left; width: auto; height: auto;">
        <h2>Torneioss com inscrições abertas:</h2><br/>
        <ul style="list-style-type: none;">
            <?php
            foreach ($ligas_atuais as $liga) {
                ?>
                <li>
                    <div style="float: left; width: auto; height: auto;">
                        <div style="float: left; width: auto; height: auto;">
                            Id: <?= $liga->getId() ?><br/>
                            Região: <?= $liga->getRegiao() ?><br/>
                            Total de participantes: <?= $liga->getTotal_participantes() ?><br/>
                            Atual número de participantes: <?= $liga->getParticipantes() ?><br/>
                            Inicio das inscrições: <?= $liga->getInicio_inscricoes() ?><br/>
                            Fim das inscricões: <?= $liga->getFim_inscricoes() ?><br/>
                            Início das batalhas: <?= $liga->getInicio() ?><br/>
                            Intervalo entre as fases: <?= $liga->getIntervalo_fase() ?><br/>
                            Round atual: <?= $liga->getRound_atual() ?><br/>
                            Premiação entrengue: <?= ($liga->getPremiacao_entregue() ? "sim" : "não") ?><br/>
                            <br/>
                            Regras de participação:<br/>
                            Preço em silvers: <?= $liga->getPreco_silvers() ?><br/>
                            Preço em golds: <?= $liga->getPreco_golds() ?><br/>
                            Moderadores participando: <?= ($liga->getMods() ? "sim" : "não") ?><br/>
                            Administradores participando: <?= ($liga->getAdmins() ? "sim" : "não") ?><br/>
                            Donos participando: <?= ($liga->getDonos() ? "sim" : "não") ?><br/>
                            Nescessário ser VIP: <?= ($liga->getVip() ? "sim" : "não") ?><br/>
                        </div>
                        <div style="float: left; width: auto; height: auto; margin-left: 50px;">
                            Regras:<br/>
                            Número de lendas: <?= $liga->getN_lendas() ?><br/>
                            Número de shinys: <?= $liga->getN_shinys() ?><br/>
                            Número de megas: <?= $liga->getN_megas() ?><br/>
                            Lv maxímo dos pokémon: <?= $liga->getLv_max_pokemon() ?><br/>
                            <br/>
                            Premiações: <input type="button" value="Criar" onclick="location.href = '/admin/tournament&new_award&league_id=<?= $liga->getId() ?>'" class="button_mini"/>
                            <ul>
                                <?php
                                foreach (League_award::select_league($liga->getId()) as $premio) {
                                    $virgula = false;
                                    echo "<li>" . $premio->getColocacao() . "º - ";
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
                                        
                                        $pkfind = DB::exQuery("SELECT `naam` FROM `pokemon_wild` WHERE `wild_id`='" . $premio->getPokemon_id() . "'")->fetch_assoc();
                                        echo $pkfind['naam'];
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
                                    echo "</li>";
                                }
                                ?>
                            </ul>
                        </div>
                        <div style="float: left; width: 500px; height: auto;"><hr/></div>
                    </div>
                </li>
                <?php
            }
            ?>
        </ul>
        <br/>
    </div>
    <div style="float: left; width: auto; height: auto;">
        <hr/>
        <h2>Torneios com inscrições encerradas:</h2><br/>
        <div style="width: auto; height: auto;">
            <ul style="list-style-type: none;">
                <?php
                foreach ($ligas_terminadas as $liga) {
                    ?>
                    <li>
                        <div style="float: left; width: auto; height: auto;">
                            <!--div style="width: auto; height: auto;"-->
                            <div style="float: left; width: auto; height: auto;">
                                Id: <?= $liga->getId() ?><br/>
                                Região: <?= $liga->getRegiao() ?><br/>
                                Total de participantes: <?= $liga->getTotal_participantes() ?><br/>
                                Atual número de participantes: <?= $liga->getParticipantes() ?><br/>
                                Inicio das inscrições: <?= $liga->getInicio_inscricoes() ?><br/>
                                Fim das inscricões: <?= $liga->getFim_inscricoes() ?><br/>
                                Início das batalhas: <?= $liga->getInicio() ?><br/>
                                Intervalo entre as fases: <?= $liga->getIntervalo_fase() ?><br/>
                                Round atual: <?= $liga->getRound_atual() ?><br/>
                                Premiação entrengue: <?= ($liga->getPremiacao_entregue() ? "sim" : "não") ?><br/>
                                <br/>
                                Regras de participação:<br/>
                                Preço em silvers: <?= $liga->getPreco_silvers() ?><br/>
                                Preço em golds: <?= $liga->getPreco_golds() ?><br/>
                                Moderadores participando: <?= ($liga->getMods() ? "sim" : "não") ?><br/>
                                Administradores participando: <?= ($liga->getAdmins() ? "sim" : "não") ?><br/>
                                Donos participando: <?= ($liga->getDonos() ? "sim" : "não") ?><br/>
                            </div>
                            <div style="float: left; width: auto; height: auto; margin-left: 50px;">
                                Regras de batalha:<br/>
                                Número de lendas: <?= $liga->getN_lendas() ?><br/>
                                Número de shinys: <?= $liga->getN_shinys() ?><br/>
                                Número de megas: <?= $liga->getN_megas() ?><br/>
                                Lv maxímo dos pokémon: <?= $liga->getLv_max_pokemon() ?><br/>
                                <br/>
                                Premiações: <input type="button" value="Criar" onclick="location.href = '/admin/tournament&new_award&league_id=<?= $liga->getId() ?>'" class="button_mini"/>
                                <ul>
                                    <?php
                                    foreach (League_award::select_league($liga->getId()) as $premio) {
                                        $virgula = false;
                                        echo "<li>" . $premio->getColocacao() . "º - ";
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

                                            $pkfind = DB::exQuery("SELECT `naam` FROM `pokemon_wild` WHERE `wild_id`='" . $premio->getPokemon_id() . "'")->fetch_assoc();
                                            echo $pkfind['naam'];
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
                                        echo "</li>";
                                    }
                                    ?>
                                </ul>
                            </div>
                            <div style="float: left; width: 500px; height: auto;"><hr/></div>
                        </div>
                        <?php /*
                          <div style="float: left; margin-left: 50px;">
                          <form method="post">
                          <input type="hidden" name="league_id" value="<?= $liga->getId() ?>"/>
                          <?php
                          if ($liga->finalizada() && !$liga->getPremiacao_entregue()) {
                          ?>
                          <input type="submit" class="button" name="entregar_premiacao" value="Entregar Premiação"/><br/>
                          <?php
                          } else if (!$liga->finalizada()) {
                          if ($liga->batalhas_criadas()) {
                          ?>
                          <input type="submit" class="button" name="criar_duelos" value="Criar Duelos"/><br/>
                          <?php
                          } else {
                          ?>
                          <input type="submit" class="button" name="passar_round" value="Passar round"/><br/>
                          <?php
                          }
                          }
                          $msg_form = "msg_form_" . $liga->getId();
                          if (isset($$msg_form)) {
                          echo "<p>" . $$msg_form . "</p>";
                          }
                          ?>
                          </form>
                          </div>
                         */ ?>
                    </li>
                    <?php
                }
                ?>
            </ul>
        </div><br/>
    </div>
    <div style="float: left; width: auto; height: auto; margin: 10px;">
        <input type="button" value="Criar novo torneio" onclick="location.href = './admin/tournament&new'" class="button"/> &nbsp;
        <input type="button" value="Criar premiação" onclick="location.href = './admin/tournament&new_award'" class="button"/>
    </div>
    <?php
}
?>