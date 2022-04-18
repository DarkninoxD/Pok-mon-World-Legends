<?php
session_name('__'.sha1(md5('secure'.$_SERVER['REMOTE_ADDR'].$_SERVER['HTTP_USER_AGENT'])));
session_start();

ob_start();
error_reporting(E_ALL ^ E_NOTICE);
// date_default_timezone_set('America/Sao_Paulo');
ini_set('default_charset', 'UTF-8');

define('PATH', str_replace(PATH_SEPARATOR, '/', dirname(dirname(__FILE__))));

# Informações para conexão ao MySQL
$dbhost		= "localhost";
$dblogin	= "u200265274_bkwl";
$dbpassword	= "[2207023]";
$dbdatabase	= "u200265274_pkwl";

require_once(PATH.'/DB/DB.php');

foreach($_GET as $key=>$value) if (!is_array($value)) $_GET[$key] = DB::real_escape_string($value);
foreach($_POST as $key=> $value) if (!is_array($value)) $_POST[$key] = DB::real_escape_string($value);
foreach($_COOKIE as $key=>$value) if (!is_array($value)) $_COOKIE[$key] = DB::real_escape_string($value);

require_once(PATH.'/PHPMailer/class.phpmailer.php');
require_once(PATH.'/PHPMailer/class.smtp.php');

require_once(PATH.'/resources/site_names.php');
$static_url = 'public'; # sem "/" no final da pasta/url

$smtp = [
	'host'	=> 'smtp.hostinger.com.br',
	'port'	=> '587',
	'mail'	=> 'noreply@pokemonworldlegends.com',
	'pass'	=> '[2207023]'
];

#CONFIGURAÇÕES NEW YEAR SHOP
$shop_newyear = false;
// NORMALMENTE INICIA DIA 31/12 E VAI ATE DIA 05/01 OU 10/01.

#CONFIGURAÇÕES DE TRIPLE EXP NO FDS
# EXP NOS FINAIS DE SEMANA? 
# INICIA NA SEXTA 00:00 E FINALIZA NO DOMINGO 00:00
################ CONFIGURAÇÕES DE TRIPLE EXP NO FDS
$tripleexpfds = true;
$doublesilverativo = true;
$doublesilverdia = 6; //0=Dom,1=Seg,2=Ter,3=Qua,4=Qui,5=Sex,6=Sab

require(PATH.'/resources/events/Events.php');
include(PATH.'/resources/Quests.php');

$evento = new Events();
$evento_atual = $evento->getActualEvent();

if (!empty($evento_atual)) {
	include($evento->importEvent(PATH.'/resources/events/events', $evento_atual['name_id']));
}

$quests = new Quests();
$quest = $quests->getActualQuests();
$quest_1 = $quests->getQuest($quest[0])->fetch_assoc();
$quest_2 = $quests->getQuest($quest[1])->fetch_assoc();

//Dias do Safari (Terça, Quinta e Sábado) 2, 4, 6

$Saffari = '';

$date = strtotime(date('H:i'));
$aberto_1 = strtotime('14:00');
$fechado_1 = strtotime('16:00');
$aberto_2 = strtotime('21:00');
$fechado_2 = strtotime('23:00');
$aberto_3 = strtotime('03:00');
$fechado_3 = strtotime('05:00');

if (in_array(date('w'), array('2', '4', '6'))) {
    if ($date >= $aberto_1 && $date <= $fechado_1) {
        $Saffari = "Aberto";
    } else {
        if ($date >= $aberto_2 && $date <= $fechado_2) {
            $Saffari = "Aberto";
        } else {
            if ($date >= $aberto_3 && $date <= $fechado_3) $Saffari = "Aberto";
        }
    }
}