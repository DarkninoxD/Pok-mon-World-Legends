<?php
if (isset($_GET['duel_id'])) {
    //Connect With Database
    include_once("../../app/includes/resources/config.php");
    //include duel functions
    include_once("duel.inc.php");
    //Load Duel Data
    $duel_sql = DB::exQuery("SElECT `id`, `datum`, `uitdager`, `tegenstander`, `t_pokemonid`, `status` FROM `duel` WHERE `id`='" . $_GET['duel_id'] . "'");
    if ($duel_sql->num_rows == 1) {
        $duel = $duel_sql->fetch_assoc();
        $time = strtotime(date("Y-m-d H:i:s")) - $duel['datum'];
        if ($duel['status'] == "accept") {
            $status = 3;
            $_SESSION['duel']['duel_id'] = $_GET['duel_id'];
            $_SESSION['duel']['begin_zien'] = true;
            //start_attack($duel);
        } else if ($duel['status'] == "no_money") {
            $status = 4;
            DB::exQuery("DELETE FROM `duel` WHERE `id`='" . $_GET['duel_id'] . "'");
            //Remove Duel
            DB::exQuery("UPDATE `gebruikers` SET `pagina`='duel_start' WHERE `user_id`='" . $_SESSION['id'] . "'");
            DB::exQuery("DELETE FROM `pokemon_speler_gevecht` WHERE `duel_id`='" . $_GET['duel_id'] . "'");
        } else if ($duel['status'] == "all_dead") {
            $status = 5;
            DB::exQuery("DELETE FROM `duel` WHERE `id`='" . $_GET['duel_id'] . "'");
            //Remove Duel
            DB::exQuery("UPDATE `gebruikers` SET `pagina`='duel_start' WHERE `user_id`='" . $_SESSION['id'] . "'");
            DB::exQuery("DELETE FROM `pokemon_speler_gevecht` WHERE `duel_id`='" . $_GET['duel_id'] . "'");
        } else if ($time > 60) {
            DB::exQuery("DELETE FROM `duel` WHERE `id`='" . $_GET['duel_id'] . "'");
            //Remove Duel
            DB::exQuery("UPDATE `gebruikers` SET `pagina`='duel_start' WHERE `user_id`='" . $_SESSION['id'] . "'");
            DB::exQuery("DELETE FROM `pokemon_speler_gevecht` WHERE `duel_id`='" . $_GET['duel_id'] . "'");
            $status = 1;
        } else
            $status = 0;
    } else
        $status = 2;
    echo $status;
}
?>