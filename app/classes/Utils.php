<?php

class Gebruikers {

    public function getInfos ($id, $get = '*') {
        if (ctype_digit($id)) {
            return DB::exQuery("SELECT $get FROM `gebruikers` WHERE user_id='$id'");
        } else {
            return DB::exQuery("SELECT $get FROM `gebruikers` WHERE username='$id'");
        }
    }

}

class PokemonSpeler {

    public function getInfos ($user_id) {
        return DB::exQuery("SELECT `pw`.`naam`,`pw`.`type1`,`pw`.`type2`,`pw`.`zeldzaamheid`,`pw`.`groei`,`pw`.`aanval_1`,`ps`.`humor_change`,`pw`.`aanval_2`,`pw`.`aanval_3`,`pw`.`aanval_4`,`ps`.* FROM `pokemon_wild` AS `pw` INNER JOIN `pokemon_speler` AS `ps` ON `ps`.`wild_id`=`pw`.`wild_id` WHERE `ps`.`user_id`='" . $user_id . "' AND `ps`.`opzak`='ja' ORDER BY `ps`.`opzak_nummer` ASC");
    }

}