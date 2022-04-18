<?php
if (isset($_POST['login'])) {
    if (empty($_POST['username']))
        $inlog_error = $txt['alert_no_username'];
    else if (empty($_POST['password']))
        $inlog_error = $txt['alert_no_password'];
    else {
        $sql = DB::exQuery("SELECT * FROM `rekeningen` WHERE `username`='" . $_POST['username'] . "' LIMIT 1");
        if ($sql->num_rows != 1)
            $inlog_error = $txt['alert_unknown_username'];
        else {
			$rekening = $sql->fetch_assoc();
			$share = false;
			$continue = true;
			
			$typppass = password($_POST['password']);
			
			if (!empty($rekening['shared']) && $rekening['shared'] != '') {
				$shared = implode("','", explode(',', $rekening['shared']));
				$sql2 = DB::exQuery ("SELECT * FROM `rekeningen` WHERE `wachtwoord`='$typppass' AND acc_id IN ('$shared')");

				if ($sql2->num_rows > 0) {
					if ($rekening['locked'] < 1) {
						$share = true;
						$typppass = $rekening['wachtwoord'];
					} else {
						$inlog_error = 'Este usuário já está logado!';
						$continue = false;
					}
				}
			}

			if ($continue) {
				$inlog_foutx = DB::exQuery("SELECT `datum`, `ip`, `spelernaam` FROM `inlog_fout` WHERE `ip`='" . $_SERVER['REMOTE_ADDR'] . "' ORDER BY `id` DESC");
				$inlog_fout  = $inlog_foutx->fetch_assoc();
				$cntglogins  = $inlog_foutx->num_rows;
				$aftellen    = 1200 - (time() - strtotime($inlog_fout['datum']));
				
				$banned_sql = DB::exQuery("SELECT user_id FROM ban WHERE user_id = '" . $rekening['acc_id'] . "'");
				$banned     = $banned_sql->num_rows;
				if ($rekening['bloqueado_tempo'] == "0000-00-00") {
					$desblo = "Permanente";
				} else {
					$data   = implode("/", array_reverse(explode("-", $gegeven['bloqueado_tempo'])));
					$desblo = $data;
				}
				
				if (($cntglogins >= 3) AND ($inlog_fout['ip'] === $_SERVER['REMOTE_ADDR']) AND ($aftellen > 0)) {
					$inlog_error = "" . $txt['alert_time_sentence'] . " " . (round($aftellen / 60)) . "min</script>";
					$msgbuglogin = "USER_ID: " . $rekening['username'] . " = Contagem: " . $cntglogins . " - IP: " . $inlog_fout['ip'] . " === " . $_SERVER['REMOTE_ADDR'] . " - AFTELLEN: " . $aftellen . "";
				} else {
					
					if ($aftellen < 1)
						DB::exQuery("DELETE FROM `inlog_fout` WHERE `ip`='" . $_SERVER['REMOTE_ADDR'] . "'");
					
					if ($rekening['wachtwoord'] != $typppass) {
						$datum = date("Y-m-d H:i:s");
						DB::exQuery("INSERT INTO `inlog_fout` (`datum`, `ip`, `spelernaam`, `wachtwoord`) VALUES ('" . $datum . "', '" . $_SERVER['REMOTE_ADDR'] . "', '" . $rekening['username'] . "', '" . $_POST['password'] . "')");
					}
					
					//if ($rekening['wachtwoord'] != $typppass) $inlog_error = 'Senha incorreta!';
					if (($cntglogins >= 2) AND ($rekening['wachtwoord'] != $typppass))
						$inlog_error = $txt['alert_timepenalty'];
					else if (($cntglogins == 1) AND ($rekening['wachtwoord'] != $typppass))
						$inlog_error = $txt['alert_trys_left_1'];
					else if (($cntglogins == 0) AND ($rekening['wachtwoord'] != $typppass))
						$inlog_error = $txt['alert_trys_left_2'];
					else if ($rekening['bloqueado'] == "sim")
						$inlog_error = "Conta bloqueada. Desbloqueio em: $desblo";
					else if ($rekening['account_code'] == 0)
						$inlog_error = $txt['alert_account_banned'];
					else if ($banned > 0)
						$inlog_error = $txt['alert_account_banned'];
					else if ($rekening['account_code'] != 1)
						$inlog_error = $txt['alert_account_not_activated'];
					else {
						
						
						DB::exQuery("DELETE FROM `inlog_fout` WHERE `ip`='" . $_SERVER['REMOTE_ADDR'] . "'");
						
						$pa_lang = md5($_SERVER['REMOTE_ADDR']);
						
						$keylog = md5(microtime());
						
						setcookie('pa_lang', $pa_lang, (86400 * 7), '/');
						DB::exQuery("UPDATE `rekeningen` SET `ban_cookie`='{$_COOKIE['pa_lang']}',`locked`='1',`ip_ingelogd`='{$_SERVER['REMOTE_ADDR']}',`session`='{$_COOKIE['PHPSESSID']}',`last_login`=NOW(), `keylog`='" . $keylog . "' WHERE `acc_id`='{$rekening['acc_id']}' LIMIT 1");
						
						$_SESSION['share_acc'] = 0;

						if ($share) {
							DB::exQuery ("UPDATE `rekeningen` SET `locked`='0' WHERE `acc_id`='{$rekening['acc_id']}'");
							$_SESSION['share_acc'] = 1;
						}
						
						$queryloginlogs = DB::exQuery("SELECT `id` FROM `inlog_logs` WHERE `ip`='" . $_SERVER['REMOTE_ADDR'] . "' AND `speler`='" . $rekening['username'] . "' LIMIT 1");
						
						if ($queryloginlogs->num_rows == 0)
							DB::exQuery("INSERT INTO `inlog_logs` (`ip`, `datum`, `speler`) VALUES ('" . $_SERVER['REMOTE_ADDR'] . "', NOW(), '" . $rekening['username'] . "')");
						else
							DB::exQuery("UPDATE `inlog_logs` SET `datum`=NOW() WHERE `speler`='" . $rekening['username'] . "' AND `ip`='" . $_SERVER['REMOTE_ADDR'] . "' LIMIT 1");
						
						
						$_SESSION['acc_id']   = $rekening['acc_id'];
						$_SESSION['acc_naam'] = $rekening['username'];
						$_SESSION['keylog']   = $keylog;
						$_SESSION['acc_hash'] = md5($rekening['acc_id'] . "," . $rekening['username']);

						header('location: ./my_characters');
					}
				}
			}
		}
    }
}
?>