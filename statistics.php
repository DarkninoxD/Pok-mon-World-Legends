<?php
//Script laden zodat je nooit pagina buiten de index om kan laden
//require_once("app/includes/resources/security.php");

$expire = 3600; ## TEMPO PARA ATUALIZAR EM SEGS.
?>
<div class="box-content"><table class="general" width="100%">
	<thead><tr><th colspan="6" style="text-align: center;">Pokémons mais fortes</th></tr></thead>
	<tbody><tr>
<?php
$sql = "SELECT `pokemon_speler`.*,`pokemon_wild`.`wild_id`,`pokemon_wild`.`naam`,`pokemon_wild`.`type1`,`pokemon_wild`.`type2`,`gebruikers`.`username`,SUM(`attack` + `defence` + `speed` + `spc.attack` + `spc.defence`) AS `strongestpokemon` FROM `pokemon_speler` INNER JOIN `pokemon_wild` ON `pokemon_speler`.`wild_id`=`pokemon_wild`.`wild_id` INNER JOIN `gebruikers` ON `pokemon_speler`.`user_id`=`gebruikers`.`user_id` WHERE `gebruikers`.`banned`='N' AND `gebruikers`.`admin`=0 GROUP BY `pokemon_speler`.`id` ORDER BY `strongestpokemon` DESC LIMIT 5";
$records = query_cache("toppokesfortes",$sql,$expire);
foreach ($records as $id=>$pokemon_profile ) {
	$pokemon_profile['powertotal'] = $pokemon_profile['attack'] + $pokemon_profile['defence'] + $pokemon_profile['speed'] + $pokemon_profile['spc.attack'] + $pokemon_profile['spc.defence'];
	$pokemon_profile = pokemonei($pokemon_profile, $txt);
	$popup = pokemon_popup($pokemon_profile, $txt);
	$pokemon_profile['naam'] = pokemon_naam($pokemon_profile['naam'],$pokemon_profile['roepnaam'],$pokemon_profile['icon']);
	echo '<td align="center" style="padding: 0"><table width="100%" class="general">';
	echo '<tr><td align="center"><a href="./profile&amp;player=' . $pokemon_profile['username'] . '">'.GetColorName($pokemon_profile['user_id']).'</a></th></tr>';
	echo '<tr><td class="tip_top-left" title="' . $popup . '" style="background: url(\'' . $static_url . '/'.$pokemon_profile['link'].'\') center center no-repeat; width: 149px; height: 115px;"></td></tr>';
	echo '<tr><td align="center"><b>Poder Total: '.highamount($pokemon_profile['powertotal']).'</b></th></tr></table></td>';
}
?>
	</tr></tbody>
</table></div>
<div class="separator"></div>
<div class="box-content"><table class="general" width="100%">
	<thead><tr><th colspan="6" style="text-align: center;">Pokémons mais experientes</th></tr></thead>
	<tbody><tr>
<?php
$sql = "SELECT `pokemon_speler`.*,`pokemon_wild`.`naam`,`pokemon_wild`.`type1`,`pokemon_wild`.`type2`,`gebruikers`.`username` FROM `pokemon_speler` INNER JOIN `pokemon_wild` ON `pokemon_speler`.`wild_id`=`pokemon_wild`.`wild_id` INNER JOIN `gebruikers` ON `pokemon_speler`.`user_id`=`gebruikers`.`user_id` WHERE `gebruikers`.`banned`='N' AND `gebruikers`.`admin`=0 ORDER BY `totalexp` DESC LIMIT 5";
$records = query_cache("toppokesexperientes",$sql,$expire);
foreach ($records as $id=>$pokemon_profile ) {
	$pokemon_profile = pokemonei($pokemon_profile, $txt);
	$popup = pokemon_popup($pokemon_profile, $txt);
	$pokemon_profile['naam'] = pokemon_naam($pokemon_profile['naam'],$pokemon_profile['roepnaam'],$pokemon_profile['icon']);
	echo '<td align="center" style="padding: 0"><table width="100%" class="general">';
	echo '<tr><td align="center"><a href="./profile&amp;player=' . $pokemon_profile['username'] . '">'.GetColorName($pokemon_profile['user_id']).'</a></th></tr>';
	echo '<tr><td class="tip_top-left" title="' . $popup . '" style="background: url(\'' . $static_url . '/'.$pokemon_profile['link'].'\') center center no-repeat; width: 149px; height: 115px;"></td></tr>';
	echo '<tr><td align="center"><b>' . highamount($pokemon_profile['totalexp']) . '</b></th></tr></table></td>';
}
?>
	</tr>
</table></div>
<div class="separator"></div>
<script type="text/javascript">
	$(document).ready(function() {
		Tipped.create("*#pokes_infos", {
			hook: 'rightmiddle'
		});
	});
</script>
<div class="box-content"><table class="general" width="100%">
	<thead><tr><th colspan="6" style="text-align: center;">Milionários</th></tr></thead>
	<tbody><tr>
<?php
$sql = "SELECT  `user_id` ,  `username` ,  `character` , `silver` AS  `totaal` FROM  `gebruikers` WHERE  `banned` =  'N' AND  `admin` =  '0' GROUP BY user_id ORDER BY totaal DESC LIMIT 5";
$records = query_cache("milionarios",$sql,$expire);
foreach ($records as $id=>$gebruikers ) { 
	echo '<td align="center" style="padding: 0"><table width="100%" class="general">';
		echo '<tr><td align="center"><a href="./profile&amp;player=' . $gebruikers['username'] . '">'.GetColorName($gebruikers['user_id']).'</a></th></tr>';
		echo '<tr><td align="center"><img src="' . $static_url . '/images/characters/' . $gebruikers['character'] . '/Thumb.png" width="114" height="114" /></th></tr>';
		echo '<tr><td align="center" class="padding-top: 3px;"><img src="' . $static_url . '/images/icons/silver.png" width="14px" /><b>' . highamount($gebruikers['totaal']) . '</b></th></tr>';
	echo '</table></td>';
}
?>
	</tr></tbody>
</table></div>
<div class="separator"></div>
<?php /*<div class="box-content"><table class="general" width="100%">
	<thead><tr><th colspan="6" style="text-align: center;">Colecionadores de pokémons</th></tr></thead>
	<tbody><tr>
<?php
$sql = "SELECT `user_id`,`username`,`character`,`aantalpokemon` FROM `gebruikers` WHERE `banned`='N' AND `admin`='0' ORDER BY `aantalpokemon` DESC LIMIT 5";
$records = query_cache("colecionadorespokes",$sql,$expire);
foreach ($records as $id=>$gebruikers ) { 
	echo '<td align="center" style="padding: 0"><table width="100%" class="general">';
		echo '<tr><td align="center"><a href="./profile&amp;player=' . $gebruikers['username'] . '">'.GetColorName($gebruikers['user_id']).'</a></th></tr>';
		echo '<tr><td align="center"><img src="' . $static_url . '/images/characters/' . $gebruikers['character'] . '/Thumb.png" width="114" height="114" /></th></tr>';
		echo '<tr><td align="center" class="padding-top: 3px;"><b>' . highamount($gebruikers['aantalpokemon']) . '</b></th></tr>';
	echo '</table></td>';
}
?>
	</tr></tbody>
</table></div> */?>
<div class="separator"></div>
<div class="box-content"><table class="general" width="100%">
	<thead><tr><th colspan="6" style="text-align: center;">Colecionadores de pokémons no TOP 3</th></tr></thead>
	<tbody><tr>
<?php
$sql = "SELECT user_id, COUNT( id ) AS total FROM `pokemon_speler` WHERE `top3` != '' GROUP BY user_id ORDER BY `total` DESC LIMIT 5";
$records = query_cache("colecionadorestop3",$sql,$expire);
foreach ($records as $id=>$gebruikersx ) { 
$gebruikers = DB::exQuery("SELECT `user_id`, `username`, `character`, `premiumaccount` FROM `gebruikers` WHERE `user_id` = '".$gebruikersx['user_id']."'")->fetch_assoc();
	echo '<td align="center" style="padding: 0"><table width="100%" class="general">';
		echo '<tr><td align="center"><a href="./profile&amp;player=' . $gebruikers['username'] . '">'.GetColorName($gebruikers['user_id']).'</a></th></tr>';
		echo '<tr><td align="center"><img src="' . $static_url . '/images/characters/' . $gebruikers['character'] . '/Thumb.png" width="114" height="114" /></th></tr>';
		echo '<tr><td align="center" class="padding-top: 3px;"><b>' . highamount($gebruikersx['total']) . '</b></th></tr>';
	echo '</table></td>';
}
?>
	</tr></tbody>
</table></div>
<div class="separator"></div>
<div class="box-content"><table class="general" width="100%">
	<thead><tr><th colspan="6" style="text-align: center;">Colecionadores de pokémons Nv.100</th></tr></thead>
	<tbody><tr>
<?php
$sql = "SELECT user_id, COUNT( id ) AS total FROM `pokemon_speler` WHERE `level` = '100' GROUP BY user_id ORDER BY `total` DESC LIMIT 5";
$records = query_cache("colecionadoresnv100",$sql,$expire);
foreach ($records as $id=>$gebruikersx ) { 
$gebruikers = DB::exQuery("SELECT `user_id`, `username`, `character`, `premiumaccount` FROM `gebruikers` WHERE `user_id` = '".$gebruikersx['user_id']."'")->fetch_assoc();
	echo '<td align="center" style="padding: 0"><table width="100%" class="general">';
		echo '<tr><td align="center"><a href="./profile&amp;player=' . $gebruikers['username'] . '">'.GetColorName($gebruikers['user_id']).'</a></th></tr>';
		echo '<tr><td align="center"><img src="' . $static_url . '/images/characters/' . $gebruikers['character'] . '/Thumb.png" width="114" height="114" /></th></tr>';
		echo '<tr><td align="center" class="padding-top: 3px;"><b>' . highamount($gebruikersx['total']) . '</b></th></tr>';
	echo '</table></td>';
}
?>
	</tr></tbody>
</table></div>
<div class="separator"></div>
<div class="box-content"><table class="general" width="100%">
	<thead><tr><th colspan="6" style="text-align: center;">Duelistas</th></tr></thead>
	<tbody><tr>
<?php
$sql = "SELECT `user_id`, `username`, `character`, `premiumaccount`, SUM(`gewonnen` - `verloren`) AS `gevechten` FROM `gebruikers` WHERE `admin` = '0' AND `banned`='N' GROUP BY `user_id` ORDER BY `gevechten` DESC LIMIT 5";
$records = query_cache("topduelistas",$sql,$expire);
foreach ($records as $id=>$gebruikers) { 		
	echo '<td align="center" style="padding: 0"><table width="100%" class="general">';
		echo '<tr><td align="center"><a href="./profile&amp;player=' . $gebruikers['username'] . '">'.GetColorName($gebruikers['user_id']).'</a></th></tr>';
		echo '<tr><td align="center"><img src="' . $static_url . '/images/characters/' . $gebruikers['character'] . '/Thumb.png" width="114" height="114" /></th></tr>';
		echo '<tr><td align="center" class="padding-top: 3px;"><b>' . $gebruikers['gevechten'] . '</b></th></tr>';
	echo '</table></td>';
}
?>
	</tr></tbody>
</table></div>
<?php # Top stats #
/*
$gettotal = DB::exQuery("SELECT SUM(silver + bank) AS rubytotal,
										SUM(gewonnen + verloren) AS matchestotal,
										SUM(aantalpokemon) AS pokemontotal,
										COUNT(user_id) AS userstotal 
										FROM gebruikers WHERE banned='N' AND admin = '0'");
	$total = $gettotal->fetch_assoc();								
										
$total['userstotal'] = number_format(round($total['userstotal']),0,",",".");
$total['rubytotal'] = number_format(round($total['rubytotal']),0,",",".");
$total['pokemontotal'] = number_format(round($total['pokemontotal']),0,",",".");
$total['matchestotal'] = number_format(round($total['matchestotal']),0,",",".");
?>
<div class="separator"></div>
<div class="box-content"><table class="general" width="100%">
	<thead><tr><th colspan="6" style="text-align: center;">Informações</th></tr></thead>
	<tbody><tr>


        <tr>
        	<td><?php echo $txt['users_total']; ?></td>
            <td><img src="media/images/icons/lid.png" style="margin-bottom:-3px;" /> <strong><?php echo $total['userstotal']; ?></strong></td>
        </tr>
        <tr>
        	<td><?php echo $txt['silver_in_game']; ?></td>
            <td><img src="media/images/icons/silver.png" title="Silver" style="margin-bottom:-3px;" /> <strong><?php echo $total['rubytotal']; ?></strong></td>
        </tr>
        <tr>
        	<td><?php echo $txt['pokemon_total']; ?></td>
            <td><img src="media/images/icons/ball.gif" style="margin-bottom:-3px;" /> <strong><?php echo $total['pokemontotal']; ?></strong></td>
        </tr>
        <tr>
        	<td><?php echo $txt['matches_played']; ?></td>
            <td><img src="media/images/icons/tegenstander.png" style="margin-bottom:-3px;" /> <strong><?php echo $total['matchestotal']; ?></strong></td>
        </tr>
        
        
	</tr></tbody>
</table></div>



*/