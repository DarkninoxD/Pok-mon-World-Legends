<?php
if (isset($_SESSION['acc_id'])) header ('Location: ./notfound');
if (isset($_POST['submit'])) {
	$inlognaam = $_POST['inlognaam'];
	$email = $_POST['email'];
  
	#Gegevens laden
	//`username`='" . $inlognaam . "' OR
	$gegeven = DB::exQuery("SELECT `acc_id`,`username`,`email` FROM `rekeningen` WHERE `email`='" . $email . "' LIMIT 1")->fetch_assoc();

	//if (empty($inlognaam))	$message = '<div class="red">'.$txt['alert_no_username'].'</div>';
	//else if (strlen(trim($inlognaam)) < 3)	$message = '<div class="red">'.$txt['alert_username_too_short'].'</div>';
	//else if (strlen(trim($inlognaam)) > 10)	$message = '<div class="red">'.$txt['alert_username_too_long'].'.</div>';
	if (empty($email))	$message = '<div class="red">'.$txt['alert_no_email'].'</div>';
    else if (!is_numeric($gegeven['acc_id']))	$message = '<div class="red">Não existe nenhum usuário com este e-email.</div>';
	//else if (strtolower($gegeven['username']) != strtolower($inlognaam))	$message = '<div class="red">O usuário não pertence a esse e-mail.</div>';
	//else if (strtolower($gegeven['email']) != strtolower($email))	$message = '<div class="red">O e-mail não pertence a esse usuário.</div>';
	else {
		# Wachtwoord lengte
		$length = rand(6, 10);

		# Password generation 
		$conso = array("b","c","d","f","g","h","j","k","l","m","n","p","q","r","s","t","v","w","x","y","z");
		$vocal = array("a","e","i","o","u");
		$numbers = array("1","2","3","4","5","6","7","8","9","0");
		$array1 = array('conso','vocal','numbers');

		$nieuwww = "";
		for($i=1;$i<=$length;++$i) {
			# Wachtwoord
			$toArray = $array1[mt_rand(0, count($array1) - 1)];
			$nieuwww .= (rand() % 3 == 0) ? strtoupper($toArray[rand(0, count($toArray) - 1)]) : $toArray[rand(0, count($toArray) - 1)];
		}

		#Md5 versie van het wachtwoord
		$nieuwwwmd5 = password($nieuwww); 

		$page = 'forgot';
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
		$mail->AddAddress($gegeven['email'], $gegeven['username']);
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

			DB::exQuery("INSERT INTO `wwvergeten` (`naam`,`ip`,`email`) VALUES ('" . $inlognaam . "','" . $_SERVER['REMOTE_ADDR'] . "','" . $email . "')");
			DB::exQuery("UPDATE `rekeningen` SET `wachtwoord`='" . $nieuwwwmd5 . "' WHERE `acc_id`='" . $gegeven['acc_id'] . "' LIMIT 1");

			$message = '<div class="green">' . $txt['success_forgot'] . '</div>';
		} else	$message = '<div class="red">' . $mail->ErrorInfo . '</div>';
	}
}
echo addNPCBox(6, $txt['titlenpc'], $txt['textnpc']);
if (!empty($message))	echo $message;
?>
<center>
	<form method="post" autocomplete="off" style="padding: 10px; width: 520px; z-index: 10">
		<table width="70%" cellspacing="0" celpadding="0" border="0">
			<tr>
				<td colspan="2">
					<input type="email" name="email" placeholder="Email:" value="<?=$_POST['email'];?>" style="width:99%; height: 40px; margin-bottom: 5px; font-size: 14px" required />
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
					<button class="button-rounded ripple" name="submit" type="submit" value="forgot">RECUPERAR SUA CONTA!</button>
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
