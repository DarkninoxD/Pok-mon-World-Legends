<?php

class Sharing_account {
    public $id;

    function __construct () {
        $this->id = $_SESSION['id'];
    }

    public function add ($add) {
        $shared = $this->getShared();

        if (!in_array($add, $shared) && sizeof($shared) < 2) {
            return true;
        }

        return false;
    }

    public function remove ($add) {
        $shared = $this->getShared();

        if (in_array($add, $shared)) {
            return true;
        }

        return false;
    }

    public function getShared () {
        $user = $this->user()->fetch_assoc();
        
        return explode(',', $user['shared']);
    }

    public function username ($id) {
        return DB::exQuery("SELECT `username` FROM `gebruikers` WHERE `user_id`='$id'")->fetch_assoc()['username'];
    }

    protected function user () {
        return DB::exQuery("SELECT `r`.`shared`, `g`.`blocklist` FROM `gebruikers` AS `g` INNER JOIN `rekeningen` AS `r` ON `g`.`acc_id`=`r`.`acc_id` WHERE user_id='$this->id'");
    }

}