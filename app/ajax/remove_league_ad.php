<?php
session_start();

if (isset($_GET['torneio'])) {
    $_SESSION['torneio_' . $_GET['torneio'] . '_ad'] = true;
} else {
    $_SESSION['league_ad'] = true;
}

?>



