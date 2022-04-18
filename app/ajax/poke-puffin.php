<?php

if (isset($_POST['a']) && isset($_SESSION['id'])) {
    $sql = DB::exQuery ("SELECT id, user_id FROM `pokemon_speler` WHERE id='$_POST[a]' AND user_id != '$_SESSION[id]'");
    
    if ($sql->num_rows == 1) {
        $gebruiker = DB::exQuery ("SELECT rankexp, user_id, puffins, rankexpnodig  FROM `gebruikers` WHERE user_id='$_SESSION[id]' AND puffins>0");
        if ($gebruiker->num_rows == 1) {
            $gebruiker = $gebruiker->fetch_assoc();
            DB::exQuery ("UPDATE `gebruikers` SET puffins=puffins-1 WHERE user_id='$_SESSION[id]'");
            $gebruiker['rankexp'] += 20;
            $gebruiker['rankexp'] = ($gebruiker['rankexp'] < $gebruiker['rankexpnodig']) ? $gebruiker['rankexp'] : $gebruiker['rankexpnodig'] - 10;
            DB::exQuery("UPDATE `gebruikers` SET `rankexp`={$gebruiker['rankexp']} WHERE `user_id`={$_SESSION['id']} LIMIT 1");
        }
    }
}