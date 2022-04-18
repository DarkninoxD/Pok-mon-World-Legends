<?php
if (isset($_SESSION['id']))	exit(header('LOCATION: ./'));
if (isset($_POST['registreer'])) {
	$inlognaam = $_POST['username'];
	$wachtwoord = $_POST['wachtwoord'];
	$wachtwoord_nogmaals = $_POST['wachtwoord_nogmaals'];
	$wachtwoordmd5 = password($wachtwoord);
	$email = $_POST['email'];
	$others = $_POST['others'];
	$ip = $_SERVER['REMOTE_ADDR'];
	$referer = $_POST['refferal'];
	$black_list = array('yopmail.com');
	$_yapMail = explode('@', $email);

	$check = DB::exQuery("SELECT `ip_aangemeld`,`aanmeld_datum` FROM `rekeningen` WHERE `ip_aangemeld`='".$ip."' ORDER BY `acc_id` DESC LIMIT 1")->fetch_assoc();
	$registerdate = strtotime($check['aanmeld_datum']);
	$countdown_time = 43200 - (time() - $registerdate);
  
	//if ($check['ip_aangemeld'] == $ip && $countdown_time > 0)	$message = '<div class="red">'.$txt['alert_already_this_ip'].'</div>';
	if (!isset($others))	$message = '<div class="red">'.$txt['alert_accept_others'].'</div>';
	else if (empty($inlognaam))	$message = '<div class="red">'.$txt['alert_no_username'].'</div>';
	else if (strlen(trim($inlognaam)) < 3)	$message = '<div class="red">'.$txt['alert_username_too_short'].'</div>';
	else if (strlen(trim($inlognaam)) > 10)	$message = '<div class="red">'.$txt['alert_username_too_long'].'</div>';
	else if (!preg_match('/^([a-zA-Z0-9]+)$/is', $inlognaam))	$message = '<div class="red">'.$txt['alert_username_incorrect_signs'].'</div>';
	else if (DB::exQuery("SELECT `username` FROM `rekeningen` WHERE `username`='" . $inlognaam . "' LIMIT 1")->num_rows != 0)	$message = '<div class="red">'.$txt['alert_username_exists'].'</div>';
	else if (empty($wachtwoord))	$message = '<div class="red">'.$txt['alert_no_password'].'</div>';
	else if (strlen(trim($wachtwoord)) < 6)	$message = '<div class="red">'.$txt['alert_password_too_short'].'</div>';
	else if ($wachtwoord != $wachtwoord_nogmaals)	$message = '<div class="red">'.$txt['alert_passwords_dont_match'].'</div>';
	else if (empty($email) || in_array($_yapMail[1], $black_list))	$message = '<div class="red">'.$txt['alert_no_email'].'</div>';
	else if (!preg_match("/^[A-Z0-9._%-]+@[A-Z0-9][A-Z0-9.-]{0,61}[A-Z0-9]\.[A-Z]{2,6}$/i", $email))	$message = '<div class="red">'.$txt['alert_email_incorrect_signs'].'</div>';
	else if (DB::exQuery("SELECT `email` FROM `rekeningen` WHERE `email`='" . $email . "' LIMIT 1")->num_rows != 0)	$message = '<div class="red">'.$txt['alert_email_exists'].'</div>';
	else {
		#Genereer activatiecode
		$activatiecode = mt_rand(100000, 999999);

		$page = 'register';
		require_once('language/language-mail.php');
		
		$mail = new PHPMailer();
		$mail->CharSet = 'utf-8';
		$mail->IsSMTP();
		$mail->Host	= $smtp['host'];
		$mail->SMTPAuth	= true;
		$mail->Port	= $smtp['port'];
		$mail->Username	= $smtp['mail'];
		$mail->Password	= $smtp['pass'];
		$mail->setFrom($smtp['mail'], "Pokémon World Legends");
		$mail->AddAddress($email, $inlognaam);
		$mail->Subject = $txt['mail_title'];
		$mail->msgHTML('<div style="color: #9eadcd;padding: 0;background-color: #34465f;border-bottom: 2px solid #27374e;border-right: 1px solid #27374e;border-radius: 4px;margin-bottom: 7px;overflow:hidden;font-size:600;font-size:15px;">
			<table width="100%" cellspacing="0" cellpadding="0" align="center">
				<tr><td><div style="background-color: #2e3d53;padding: 1px 5px; font-size:12px; font-family: Arial, Helvetica, sans-serif;">
					' . $txt['mail_body'] . '
				</div></td></tr>
				<tr><td align="center"><div style="padding: 5px; font-size: 12px; font-family: Arial, Helvetica, sans-serif;border-top: 1px solid #577599;">
					<b style="color: #eeeeee;">&copy;' . date("Y"). ' - Pokémon World Legends | Todos os direitos reservados.</b>
				</div></td></tr>
			</table>
		</div>');
		if ($mail->Send()) {
			$mail->ClearAllRecipients();
			$mail->ClearAttachments();

			#Gebruiker in de database
			DB::exQuery("INSERT INTO `rekeningen` (`account_code`,`username`,`datum`,`aanmeld_datum`,`wachtwoord`,`email`,`ip_aangemeld`) VALUES (".$activatiecode.",'".$inlognaam."',NOW(),NOW(),'".$wachtwoordmd5."','".$email."','".$ip."')");

			$id = DB::insertID();

			if (DB::exQuery("SELECT `username` FROM `gebruikers` WHERE `username`='".$referer."'")->num_rows > 0) {
				
				DB::exQuery("UPDATE gebruikers SET silver = silver +200, referidos = referidos +1 WHERE username = '".$referer."'");
				
				$pegaid = DB::exQuery("select `user_id` from `gebruikers` where `username`='".$referer."'")->fetch_assoc();
				
				DB::exQuery("UPDATE rekeningen SET refferal='".$pegaid['user_id']."' WHERE acc_id = '".$id."'");
				
			}



			#Bericht opstellen
			$_SESSION['user'] = $inlognaam;
            $_SESSION['act_msg'] = '<div class="green">Seu cadastro foi efetuado! <a href="./activate">Ative sua conta</a> com o código enviado em seu e-mail!</div>';
			header('location: ./activate');
		} else	$message = '<div class="red">' . $mail->ErrorInfo . '</div>';
	}
}
echo addNPCBox(1, $txt['titlenpc'], $txt['textnpc']);
if (!empty($message))	echo $message
?>

<center>
	<form method="post" autocomplete="off" style="padding: 10px; width: 520px; z-index: 10">
		<table width="70%" cellspacing="0" celpadding="0" border="0">
			<tr>
				<td colspan="2">
					<input type="text" name="username" placeholder="<?=$txt['login_username'];?>:" style="width:99%; height: 40px; margin-bottom: 5px; font-size: 14px" value="<?=$_SESSION['user'];?>" maxlength="10" minlength="3" required />
					<input type="email" name="email" placeholder="Email:" value="<?=$_POST['email'];?>" style="width:99%; height: 40px; margin-bottom: 5px; font-size: 14px" required />
					<input type="password" placeholder="<?=$txt['login_password'];?>:" name="wachtwoord" style="width:49%; height: 40px; margin-bottom: 5px; font-size: 14px" value="<?=$_POST['wachtwoord'];?>" required />
					<input type="password" placeholder="Repita a Senha:" name="wachtwoord_nogmaals" value="<?=$_POST['wachtwoord_nogmaals'];?>" style="width:49%; height: 40px; margin-bottom: 5px; font-size: 14px" required />
					<input type="text" name="refferal" value="<?=$refferal;?>" style="padding-left: 10px;width:99%; height: 40px; margin-bottom: 5px; font-size: 14px" placeholder="Quem convidou você ao Pokémon World Legends?">
					<center><input type="checkbox" name="others" id="others" required /> Declaro que li e concordo com a <a href="ajax.php?act=privacy" class="colorbox-privacy">Política de Privacidade</a>, <a href="ajax.php?act=terms" class="colorbox-terms">Termos de Serviço</a> e  com as <a href="ajax.php?act=rules" class="colorbox-rules">Regras e Punições</a> </center>
				</td>
			</tr>
			<tr style="font-style: italic;">
				<td style="padding-left: 5px; padding-top: 5px; width: 50%">
					<a href="./activate"><img src="<?=$static_url?>/images/layout/seta1.png" style="margin-right: 3px; vertical-align: 1px;">Ative sua Conta</a>
				</td>
				<td style="width: 50%; text-align: right; padding-top: 5px; padding-right: 10px">
					<a href="./forgot"><img src="<?=$static_url?>/images/layout/seta1.png" style="margin-right: 3px; vertical-align: 1px">Recuperar Conta</a>
				</td>
			</tr>
			<tr>
				<td colspan="2" style="padding-top: 10px">
					<button class="button-rounded ripple" name="registreer" type="submit" value="register">PARTICIPAR DA AVENTURA!</button>
				</td>
			</tr>
			<tr>
				<td colspan="2" style="font-style: italic; text-align: center; padding-top: 5px">
					Já tem uma conta? <a href="./" style="color: #6ac7ee; font-weight: bold">LOGUE-SE</a> agora mesmo!
				</td>
			</tr>
		</table>
	</form>
</center>