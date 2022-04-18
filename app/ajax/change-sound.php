<?php
if (isset($_SESSION['id'])) {
	$gebruiker = DB::exQuery("SELECT `sound` FROM `gebruikers` WHERE `user_id`=" . $_SESSION['id'] . " LIMIT 1");
	if ($gebruiker->num_rows == 1) {
		$gebruiker = $gebruiker->fetch_assoc();
		$gebruiker['sound'] = ($gebruiker['sound'] == 'on') ? 'off' : 'on';
		DB::exQuery("UPDATE `gebruikers` SET `sound`='" . $gebruiker['sound'] . "' WHERE `user_id`=" . $_SESSION['id'] . " LIMIT 1");
	}
}
?>