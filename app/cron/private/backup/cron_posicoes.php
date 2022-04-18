<?php
set_time_limit(9999);
ini_set('memory_limit', '-1');
ob_start();
require_once('../includes/resources/config.php');

  	    //ATUALIZA AS POSIÇÕES NO RANK
            $updateposicao = DB::exQuery("SELECT gb.user_id, gb.username, gb.rank, gb.badges,
            (gb.gewonnen + gb.verloren) AS victories, SUM(ps.`level`) AS lv_sum, AVG(ps.`level`) AS lv_average,
            (((gb.gewonnen + gb.verloren)/10) + (AVG(ps.`level`) * 10) + (SUM(ps.`level`) / 100) + (gb.badges * 10)) AS total_points
            FROM gebruikers AS gb JOIN pokemon_speler AS ps ON gb.user_id = ps.user_id
            WHERE gb.banned='N' AND gb.admin = '0' GROUP BY gb.user_id
            ORDER BY total_points DESC, gb.rank DESC, gb.rankexp DESC, gb.username ASC");
            
            $numero = 1;
            
	    while($pegaposicao = $updateposicao->fetch_assoc()) {
   	    
   	    $contagem = DB::exQuery("SELECT id from pokemon_speler where user_id = '".$pegaposicao['user_id']."'")->num_rows;
            
            
            DB::exQuery("UPDATE gebruikers SET posicaorank = '".$numero."',aantalpokemon = '".$contagem."' WHERE user_id = '".$pegaposicao['user_id']."' limit 1");
            
            $numero = $numero + 1;
   	    }
  
  echo "Cron executado com sucesso.";
  
  ob_flush();
?>