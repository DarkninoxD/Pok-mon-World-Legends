<?php
//Sessie verwijderen
if (isset($_SESSION['acc_id'])) {
    DB::exQuery ("UPDATE `rekeningen` SET locked='0' WHERE `acc_id`='".$_SESSION[acc_id]."' LIMIT 1");
}
session_unset();
session_destroy();
exit(header("location: ./"));
?>