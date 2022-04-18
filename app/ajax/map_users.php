<?php
require_once 'app/includes/resources/config.php';
require_once 'app/includes/resources/ingame.inc.php';

$map = (int) $_GET['map'];
$uid = $_SESSION['id'];
																  
$time = time();
$tenMinsAgo = $time - (60*30);
$usersQuery = DB::exQuery("SELECT * FROM `gebruikers` WHERE `map_num`='{$map}' AND `map_lastseen`>='{$tenMinsAgo}'");
$numUsersOnMap = $usersQuery->num_rows;
$usersArray = array();

$i = 0; $onMap = false;

while ($user = $usersQuery->fetch_assoc()) {
	if ($user['user_id'] == $uid) {
		$startX = $user['map_x'];
		$startY = $user['map_y'];
		$onMap = true;
		continue;
	}

	$usersArray[$i]['username'] = $user['username'];
	$usersArray[$i]['id'] = (int) $user['user_id'];
	$usersArray[$i]['x']  = (int) $user['map_x'];
	$usersArray[$i]['y']  = (int) $user['map_y'];
	$usersArray[$i]['sprite']  = (int) $user['map_sprite'];
	$usersArray[$i]['in_battle']  = (int) $user['in_battle'];
	$usersArray[$i]['map_wild']  = $user['map_wild'];
	$i++;
}

DB::exQuery("UPDATE `gebruikers` SET `map_num`='{$map}', `map_x`='{$startX}', `map_y`='{$startY}', `map_lastseen`='{$time}' WHERE `user_id`='{$uid}'");

if (!$onMap) {
	$numUsersOnMap++;
}
echo json_encode($usersArray);
?>