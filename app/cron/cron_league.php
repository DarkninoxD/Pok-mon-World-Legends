<?php
include_once '../includes/resources/config.php';

# Liga
include_once '../classes/League.php';
include_once '../classes/League_battle.php';

$league = new League();
$league->select_atual();

if ($league->getId()) {
    if ($league->getRound_atual()) {
        if ($league->finalizada()) {
            $league->entregar_premiacao();
            echo "Premiação da liga id:" . $league->getId() . " foram entregues!" . "<br/>\n";
        } else {
            //NOW() - INTERVAL 4 HOUR - INTERVAL 2 MINUTE - INTERVAL 17 SECOND
            $time = time() + League::$ajuste_tempo_int;
            $h_batalha = strtotime($league->inicio_round($league->getRound_atual(), true));
            $h_sorteio = strtotime($league->inicio_round($league->getRound_atual(), true)) + ($league->getIntervalo_fase() - 300);

            if ($time >= $h_sorteio && $league->getRound_atual() != $league->cont_rounds()) {
                if ($league->passar_round()) {
                    $league->setEm_operacao(0); //era false
                    $league->update();

                    echo "Liga id:" . $league->getId() . " passou para o round " . $league->getRound_atual() . "<br/>\n";
                } else {
                    echo "Liga id:" . $league->getId() . " não passou de round porque o número de vagas não preenchido<br/>\n";
                }
            } else if ($time >= $h_batalha && $league->em_operacao() == 0) {
                $league->setEm_operacao(1); //era true
                $league->update();

                $league->criar_duelos();

                echo "Liga id:" . $league->getId() . " criou os duelos do round " . $league->getRound_atual() . "<br/>\n";
            }
        }
    } else {
        $league->passar_round();
        echo "Liga id:" . $league->getId() . " passou para o round " . $league->getRound_atual() . "<br/>\n";
    }
}

DB::exQuery("UPDATE `cron` SET `tijd`='" . date("Y-m-d H:i:s") . "' WHERE `soort`='league'");
echo "Tarefa cron_league executada com sucesso!";
//echo date("d/m/Y H:i:s");
?>