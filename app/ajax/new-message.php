<?php
include('app/classes/Messages.php');
$static_url = '';

if (isset($_SESSION['id']) && isset($_POST['message']) && isset($_POST['id']) && isset($_POST['sender'])) {
    if (!empty($_POST['message']) && !empty($_POST['id'])) {
        $conversa = strval(strip_tags($_POST['id']));
        $message = mb_strimwidth(strval(strip_tags($_POST['message'])), 0, 1000, '');

        if ($_SESSION['id'] == $_POST['sender']) {
            $sql = DB::exQuery ("SELECT * FROM `conversas` WHERE id='$conversa' AND (trainer_1='$_SESSION[id]' OR trainer_2='$_SESSION[id]') AND trainer_1_hidden='0' AND trainer_2_hidden='0'");
            $var = new Messages ( $conversa );

            if ($sql->num_rows > 0) {
                if (!empty ($message)) {
                    $sql = $sql->fetch_assoc();
                    $reciever = $sql['trainer_1'];
                    if ($_POST['sender'] == $sql['trainer_1']) {
                        $reciever = $sql['trainer_2'];
                    }
                    
                    if (!$var->blocked($reciever)[0]) {
                        $date = date ('d/m/Y H:i:s');
                        DB::exQuery("INSERT INTO `conversas_messages` (`conversa`, `sender`, `reciever`, `message`, `date`) VALUES ('$conversa', '$_SESSION[id]', '$reciever', '$message', '$date')");
                        DB::exQuery("UPDATE `conversas` SET last_message='$date' WHERE id='$conversa' AND (trainer_1='$_SESSION[id]' OR trainer_2='$_SESSION[id]') AND trainer_1_hidden='0' AND trainer_2_hidden='0'");
                        echo $date;
                    } else {
                        echo $var->blocked_msg();
                    }
                }
            } else {
                echo 'Falha ao enviar';
            }
        } else {
            echo 'Falha ao enviar!';
        }
    }
}