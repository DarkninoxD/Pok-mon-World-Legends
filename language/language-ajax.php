<?php
$language_array = array('pt');
if (in_array($_COOKIE['pa_language'], $language_array))	$_COOKIE['pa_language'] = $_COOKIE['pa_language'];
else	$_COOKIE['pa_language'] = 'pt';
require_once('ajax/language-ajax-' . $_COOKIE['pa_language'] . '.php');
