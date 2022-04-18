<?php

class Clans extends Gebruikers {

    public function get ($id) {
        $clan = DB::exQuery("SELECT * FROM `clans` WHERE `id`='$id'")->fetch_assoc();
        if (!empty($clan)) {
            $clan['num_members'] = $this->getNumMembers($id);
            $clan['max_members'] = 4 + $clan['level'];
            $clan['rank_name'] = $clan['ranking'];
            if ($clan['ranking'] == 0) $clan['rank_name'] = '--';
        }

        return $clan;
    }

    public function getMembers ($id) {
        return DB::exQuery("SELECT * FROM `clans_member` WHERE `cla`='$id'");
    }
    
    public function getNumMembers ($id) {
        return DB::exQuery("SELECT * FROM `clans_member` WHERE `cla`='$id'")->num_rows;
    }

    public function getInvites ($id) {
        return DB::exQuery("SELECT * FROM `clans_invites` WHERE `id`='$id'")->fetch_assoc();
    }

    public function create ($infos) {
        $date = date('d/m/Y');
        DB::exQuery("INSERT INTO `clans` (`name`, `sigla`, `descr`, `silvers`, `golds`, `level`, `exp`, `ranking`, `owner`, `image`, `date`) VALUES ('$infos[name]', '$infos[sigla]', '$infos[descr]', 0, 0, 1, 0, 0, '$infos[user]', '$infos[image]', '$date');");  
        $clan_id = DB::insertID();
        DB::exQuery("INSERT INTO `clans_member`(`user_id`, `silvers_contribuicao`, `golds_contribuicao`, `prioridade`, `cla`, `date`) VALUES ('$infos[user]', 0, 0, 0, '$clan_id', NOW());");   
    }

    public function getUserClan ($user_id) {
        return DB::exQuery("SELECT `cla` FROM `clans_member` WHERE `user_id`='$user_id'")->fetch_assoc()['cla'];
    }

}