<?php
session_start();
if (isset($_SESSION['acc_id'])) {
    DB::exQuery ("UPDATE `rekeningen` SET locked='0' WHERE `acc_id`='".$_SESSION[acc_id]."' LIMIT 1");
}

session_unset();
session_destroy();

if ($manutencao == 0) {
    echo '<script>window.location = "./";</script>';
}

echo addNPCBox(11, 'EM MANUNTENÇÃO!', 'POKÉMON WORLD LEGENDS ESTÁ EM MANUNTENÇÃO, VOLTAREMOS ÀS 00:00!');

?>

<audio src="<?=$static_url?>/sounds/select-player.mp3" autoplay loop volume="0.2" style="display: none"></audio>

<style>
    #logo_login {
        margin-top: 45px;
    }
</style>
