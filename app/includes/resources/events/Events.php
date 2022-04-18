<?php

class Events {
    public function importEvent ($path, $name) {
        return $path.'/'.$name.'.php';
    }

    public function getActualEvent () {
        $id = DB::exQuery("SELECT `valor` FROM `configs` WHERE `id`='5'")->fetch_assoc()['valor'];
        return DB::exQuery("SELECT * FROM `events_info` WHERE `id`='$id'")->fetch_assoc();
    }

    public function getEvent ($id) {
        return DB::exQuery("SELECT * FROM `events_info` WHERE `id`='$id'")->fetch_assoc();
    }

    public function getEvents () {
        return DB::exQuery("SELECT * FROM `events_info` WHERE `active`='1'");
    }
}