<?php
include('app/includes/resources/security.php');

if (!empty($evento_atual)) {
    if ($_GET['actual'] == $evento_atual['link']) {
        include($event_page);
    } else {
        header('location: ./home');
    }
} else {
    header('location: ./home');
}