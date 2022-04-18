<?php
$pokemonid	= (int)$_GET["pokemonid"];

#Verwijderen uit transferlijst tabel
DB::exQuery("DELETE FROM `transferlijst` WHERE `pokemon_id`='".$pokemonid."' and `user_id`='".$_SESSION['id']."' LIMIT 1");
#pokemon opslaan als zijne van transferlijst
DB::exQuery("UPDATE `pokemon_speler` SET `opzak`='nee' WHERE `id`='".$pokemonid."' and `user_id`='".$_SESSION['id']."' LIMIT 1");
?>