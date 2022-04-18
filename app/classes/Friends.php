<?php

class Friends {

    public function sendSolicitation ($uid, $uid2) {
        $username = $this->getInfos($uid)['username'];
        $date = date ('Y-m-d H:i:s');
        $date2 = date('Y-m-d', strtotime('+1 week'));
        
        DB::exQuery("INSERT INTO `friends` (`uid`, `uid_2`, `date`, `date_to_remove`) VALUES ('$uid', '$uid2', '$date', '$date2')");

        $event = '<img src="public/images/icons/blue.png" width="16" height="16" class="imglower" /> <a href="./profile&player='.$username.'">'.$username.'</a> deseja ser seu amigo. Visite a <a href="./friends">Página de Amigos</a> para <b>mais detalhes</b>.';

		DB::exQuery("INSERT INTO gebeurtenis (`datum`,`ontvanger_id`,`bericht`,`gelezen`) VALUES (NOW(), '" . $uid2 . "', '" . $event . "', '0')");
    }

    public function accept ($id) {
        DB::exQuery("UPDATE `friends` SET `accept`='1' WHERE `id`='$id'");
    }

    public function decline ($id) {
        DB::exQuery("DELETE FROM `friends` WHERE `id`='$id'");
    }

    public function isFriend ($uid, $uid2) {
        $is_friend = DB::exQuery("SELECT `id` FROM `friends` WHERE (`uid`='$uid' AND `uid_2`='$uid2')")->num_rows;
        $is_friend2 = DB::exQuery("SELECT `id` FROM `friends` WHERE (`uid`='$uid2' AND `uid_2`='$uid')")->num_rows;

        if ($is_friend == 0 && $is_friend2 == 0) {
            return false;
        } else {
            return true;
        }
    }

    public function isAccept ($uid, $uid2) {
        $is_friend = DB::exQuery("SELECT `id` FROM `friends` WHERE (`uid`='$uid' AND `uid_2`='$uid2') AND `accept`='1'")->num_rows;
        $is_friend2 = DB::exQuery("SELECT `id` FROM `friends` WHERE (`uid`='$uid2' AND `uid_2`='$uid') AND `accept`='1'")->num_rows;

        if ($is_friend == 0 && $is_friend2 == 0) {
            return false;
        } else {
            return true;
        }
    }

    public function queried ($id) {
        return DB::exQuery("SELECT * FROM `friends` WHERE `id`='$id'")->fetch_assoc();
    }

    public function query ($id, $instruction = '', $pagina = null, $max = '', $order = 'DESC') {
        if (!is_null($pagina)) {
            return DB::exQuery("SELECT * FROM `friends` WHERE (`uid`='$id' OR `uid_2`='$id') $instruction ORDER BY `id` $order LIMIT $pagina, $max");   
        } else {
            return DB::exQuery("SELECT * FROM `friends` WHERE (`uid`='$id' OR `uid_2`='$id') $instruction ORDER BY `id` $order");   
        }
    }

    public function getInfos ($id) {
        return DB::exQuery("SELECT `username`, `ultimo_login`, `online`,`blocklist`,`rank` FROM `gebruikers` WHERE `user_id`='$id'")->fetch_assoc();
    }
}