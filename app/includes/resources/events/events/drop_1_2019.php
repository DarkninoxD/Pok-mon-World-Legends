<?php

// Evento de DROP (MAIO 2019)

function getDrop ($where = '') {
    return DB::exQuery("SELECT * FROM `events_drop_1_2019` WHERE `world`='$where'")->fetch_assoc();
}

function drop ($drop = '', $chance = '1') {
    if ($drop != '4') {
        if ($chance == '1') {
            $perc = 20;
        } else if ($chance == '2') {
            $perc = 35;
        } else {
            $perc = 45;
        }
    } else {
        if ($chance == '1') {
            $perc = 10;
        } else if ($chance == '2') {
            $perc = 20;
        } else {
            $perc = 35;
        }
    }

    $rand = rand(1, 100);

    if ($rand <= $perc) {
        return true;
    } else {
        return false;
    }
    
}

function drops ($drop = '') {
    if ($drop != '4') {
        $rand = rand(1, 100);

        if ($rand <= 50) {
            return 1;
        } else if ($rand <= 80) {
            return 2;
        } else {
            return 3;
        }
    } else {
        return rand(1, 2);
    }
}