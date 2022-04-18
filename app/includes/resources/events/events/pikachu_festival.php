<?php

function find_pikachu () {
    if (rand(0, 10) > 0) return true;
    else return false;
}

function drops ($wild_id, $shiny) {
    $drop = 0;
    if ($wild_id == 25) {
        $drop = 1;
    } else if (in_array($wild_id, array('923', '965', '966', '967', '968'))) {
        $drop = 3;
    }

    if ($shiny == 1) {
        $drop += 2;
    }

    return $drop;
}