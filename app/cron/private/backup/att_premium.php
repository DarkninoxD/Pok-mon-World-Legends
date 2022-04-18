<?php
include('../includes/resources/config.php');

$updateposicao = DB::exQuery("SELECT user_id,premiumaccount from `gebruikers`");
     
$i = 0;
while($pegaposicao = $updateposicao->fetch_assoc()) {   
   	$premiumdays = 2;
	$premium = 86400 * $premiumdays;
	if ($pegaposicao['premiumaccount'] < time()) $premium += time();
	else $premium += $pegaposicao['premiumaccount'];

	DB::exQuery("UPDATE `gebruikers` SET `premiumaccount`={$premium} WHERE `user_id`={$pegaposicao['user_id']} LIMIT 1");
   	 	
   	       	  
    $i++;
}
  
echo '<br>TOTAL DE LINHAS '.$i;
?>