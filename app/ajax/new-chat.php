<?php
if (isset($_SESSION['id']) && isset($_POST['message'])) {
    $message = mb_strimwidth(strval(strip_tags($_POST['message'])), 0, 255, '');

    if (!empty ($message)) {
        $nick = DB::exQuery("SELECT `username` FROM `gebruikers` WHERE `user_id` = '$_SESSION[id]'")->fetch_assoc()['username'];
        $verify = DB::exQuery("SELECT * FROM `chat` WHERE `sender`='".$_SESSION['id']."' ORDER BY `id` DESC LIMIT 1")->fetch_assoc();
        if (!empty($verify['hour'])) {
            $dateval = str_replace('/', '-', $verify['date']).' '.$verify['hour'];
            $vdate_full = strtotime('+5 seconds', strtotime($dateval));
        } else {
            $vdate_full = strtotime(date('d-m-Y H:i:s'));
        }

        $date_full = strtotime(date('d-m-Y H:i:s'));

        if ($date_full >= $vdate_full) {
            $hour = date('H:i:s');
            $date = date ('d/m/Y');
            DB::exQuery("INSERT INTO `chat` (`sender`, `message`, `date`, `hour`, `nickname`) VALUES ('$_SESSION[id]', '$message', '$date', '$hour', '$nick')");

            echo 'success | '.$message;
        } else {
            echo 'error | Falha ao enviar!';   
        }
    }
}