<?php
if (isset($_GET['id']) && isset($_SESSION['id'])) {
	if ($_GET['id'] != $_SESSION['id']) {
		$date2 = date('Y-m-d');
		$query = DB::exQuery("SELECT * FROM `honra` WHERE `u_id`='".$_GET['id']."' AND `u_honor`='".$_SESSION['id']."' AND `date_ctrl`='$date2'")->num_rows;
		if ($query == 0) { 
			DB::exQuery("INSERT INTO `honra` SET `u_id`='".$_GET['id']."', `u_honor`='".$_SESSION['id']."', `date_ctrl`='$date2', `date`=NOW()");
			$quests->setStatus('honor', $_SESSION['id']);
		}
	}
}