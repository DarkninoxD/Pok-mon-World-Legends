<?php
ob_start();
require_once('app/includes/resources/config.php');
require_once('language/language-general.php');
require_once('app/includes/resources/ingame.inc.php');

if (isset($_GET['act'])) {
	if (file_exists('app/ajax/' . $_GET['act'] . '.php')) {
		$page = $_GET['act'];
		require_once('language/language-ajax.php');
		require_once('app/ajax/' . $_GET['act'] . '.php');
	} else {
        echo 'Bad request!';
	}
} else {
	header('location: ./home');	
}
ob_flush();

?>