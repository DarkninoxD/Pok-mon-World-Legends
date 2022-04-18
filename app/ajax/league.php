<?php
session_start();

include '../app/includes/resources/config.php';
include '../app/classes/League.php';
include '../app/classes/League_battle.php';

$result = DB::exQuery("SELECT id FROM league_battle WHERE (user_id1 = '" . $_SESSION['id'] . "' OR "
        . "user_id2 = '" . $_SESSION['id'] . "') AND ("
        . "(NOW()" . League::$ajuste_tempo_string . ") BETWEEN "
        . "(inicio - INTERVAL 5 SECOND) AND (inicio + INTERVAL 5 MINUTE))");

if ($result->num_rows>0) {
    $l_battle = new League_battle();
	$plleag = $result->fetch-assoc();
    $l_battle->select($result['id']);

    if ($l_battle->getVencedor()) {
        if ($_SESSION['id'] == $l_battle->getVencedor()) {
            echo "3 | Você ganhou!<br/>Seu oponente não estava pronto!";
        } else {
            echo "4 | Você perdeu!<br/>Você não estava pronto para a batalha!";
        }
        exit();
    }

    if ($l_battle->getDuel_id()) {
	    $chance = rand(1,2);
        $background = "duelo-".$chance."";
        $_SESSION['background'] = $background;
        $_SESSION['duel']['duel_id'] = $l_battle->getDuel_id();
        $_SESSION['duel']['begin_zien'] = true;
        echo "2 | Batalha criada!<br/>Redirecionando para batalha...";
        exit();
    }

    echo "0 | Aguarde enquanto a batalha é criada...";
    exit();
} else if (DB::exQuery("SELECT id FROM league_battle WHERE "
                . "duel_id = 0 AND termino IS NULL AND "
                . "(user_id1 = '" . $_SESSION['id'] . "' OR "
                . "user_id2 = '" . $_SESSION['id'] . "') AND ((NOW()" . League::$ajuste_tempo_string . ") BETWEEN (inicio + INTERVAL 5 MINUTE) AND "
                . "(NOW()" . League::$ajuste_tempo_string . "))")->num_rows>0) {
    $l_battle = new League_battle();
	$plleag = $result->fetch-assoc();
    $l_battle->select($result['id']);
    $l_battle->informarVencedor($l_battle->getUser_id2(), "A batalha não foi criada em 5 minutos");
    $l_battle->update();

    if ($_SESSION['id'] == $l_battle->getUser_id1()) {
        echo "4 | Você perdeu!<br/>A batalha não foi criada!";
    } else if ($_SESSION['id'] == $l_battle->getUser_id2()) {
        echo "3 | Você ganhou!<br/>A batalha não foi criada!";
    }
    exit();
}

echo "1 | Ainda não é hora para sua batalha!";
exit();
