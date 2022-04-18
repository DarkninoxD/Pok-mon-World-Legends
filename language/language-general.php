<?php
ob_start();
$language_array = array("pt");
if (isset($_GET['language']) && in_array($_GET['language'], $language_array)) {
	setcookie("pa_language", $_GET['language'], time() + (86400 * 365));
	exit(header("Location: ./" . $_GET['page']));
} else {
	if (isset($_COOKIE['pa_language']) && in_array($_COOKIE['pa_language'], $language_array))
		//Language is wat jij hebt gekozen
		$_COOKIE['pa_language'] = $_COOKIE['pa_language'];
	else
		//Default language is Engels
		$_COOKIE['pa_language'] = 'pt';
	require_once('general/language-general-' . $_COOKIE['pa_language'] . '.php');
}
ob_flush();
?>