<?php
if (isset($_SESSION['acc_id'])) header ('Location: ./notfound');

if (isset($_POST['activate'])) {
	$inlognaam	=	$_POST['username'];
	$activatie	=	$_POST['activatie'];
  
	#Gegevens laden van uit de database
	$getRekening = DB::exQuery("SELECT `acc_id`,`username`,`account_code`,`email` FROM `rekeningen` WHERE `username`='" . $inlognaam . "' AND `account_code`='" . $activatie . "' LIMIT 1");
	if ($getRekening->num_rows != 0) {
		$rekening  = $getRekening->fetch_assoc();

		if (empty($inlognaam)) $message = '<div class="red">'.$txt['alert_no_username'].'</div>';
		else if (empty($activatie)) $message = '<div class="red">'.$txt['alert_no_activatecode'].'</div>';
		else if (strlen(trim($activatie)) < 1)  $message = '<div class="red">'.$txt['alert_activatecode_too_short'].'</div>';
		else if (strlen(trim($activatie)) > 6)  $message = '<div class="red">'.$txt['alert_activatecode_too_long'].'</div>';
		else if ($rekening['username'] != $inlognaam)  $message = '<div class="red">Esta conta não existe!</div>';
		else if ($rekening['account_code'] != $activatie)  $message = '<div class="red">'.$txt['alert_activatecode_dont_exist'].'</div>';
		else if ($rekening['account_code'] == 1) $message = '<div class="red">Esta conta já está ativa!</div>';
		else if ($rekening['account_code'] == 0) $message = '<div class="red">Esta conta já está ativa!</div>';
		else {
			$page = 'activate';
			require_once('language/language-mail.php');

			// $mail = new PHPMailer();
			// $mail->charSet = "UTF-8";
			// $mail->IsSMTP();
			// $mail->Host	= $smtp['host'];
			// $mail->SMTPAuth	= true;
			// $mail->Port	= $smtp['port'];
			// $mail->Username	= $smtp['mail'];
			// $mail->Password	= $smtp['pass'];
			// $mail->setFrom($smtp['mail'], "Pokémon World Legends");
			// $mail->AddAddress($rekening['email'], $rekening['username']);
			// $mail->Subject = $txt['mail_title_activate'];
			// $mail->msgHTML('<div style="margin: 0 auto; background: #F5F5F5; border-radius: 5px; width: 520px; border: 1px dotted #D8D8D8; border-left: 4px solid #CE3233; border-right: 4px solid #CE3233;">
			// 	<table width="100%" cellspacing="0" cellpadding="0" align="center">
			// 		<tr><td><div style="padding: 1px 5px; font-size:12px; font-family: Arial, Helvetica, sans-serif;">
			// 			' . $txt['mail_body_activate'] . '
			// 		</div></td></tr>
			// 		<tr><td align="center"><div style="background: rgba(0, 0, 0, .5); margin-top: 10px; padding: 5px; font-size: 12px; font-family: Arial, Helvetica, sans-serif;">
			// 			<b style="color: #FFF;">&copy;' . date("Y"). ' - Pokémon World Legends | Todos os direitos reservados.</b>
			// 		</div></td></tr>
			// 	</table>
			// </div>');
			//$mail->Send()
			if (true) {
				// $mail->ClearAllRecipients();
				// $mail->ClearAttachments();

				DB::exQuery("UPDATE `rekeningen` SET `account_code`='1' WHERE `acc_id`='" . $rekening['acc_id'] . "' LIMIT 1");
				if (!empty($_SESSION['act_msg'])) {
				    $_SESSION['act_msg'] = '';
				}
				$message = '<div class="green">Sua conta foi ativada com sucesso!</div>';
			} else	$message = '<div class="red">' . $mail->ErrorInfo . '</div>';
		}
	} else {
	    $message = '<div class="red">Usuário ou Código de Ativação Incorreto(s)!</div>';
	}
}
if (isset($_POST['resend'])) {
	$inlognaam	= $_POST['username'];
	$email		= $_POST['email'];

	if (empty($inlognaam)) $message = '<div class="red">'.$txt['alert_no_username'].'</div>';
	else if (strlen(trim($inlognaam)) < 3)  $message = '<div class="red">'.$txt['alert_username_too_short'].'</div>';
	else if (strlen(trim($inlognaam)) > 10) $message = '<div class="red">'.$txt['alert_username_too_long'].'</div>';
	else if (empty($email))	$alert = '<div class="error">'.$txt['alert_no_email'].'</div>';
	else if (!preg_match("/^[A-Z0-9._%-]+@[A-Z0-9][A-Z0-9.-]{0,61}[A-Z0-9]\.[A-Z]{2,6}$/i", $email))	$alert = '<div class="red">'.$txt['alert_email_incorrect_signs'].'</div>';
	else {
		$getRekening = DB::exQuery("SELECT `acc_id`,`username`,`account_code`,`email` FROM `rekeningen` WHERE `username`='" . $inlognaam . "' AND `email`='" . $email . "' LIMIT 1");
		if ($getRekening->num_rows != 1)	$message = '<div class="red">Não encontramos nenhuma conta vinculado à este e-mail!</div>';
		else {
			$rekening = $getRekening->fetch_assoc();
			if ($rekening['account_code'] == 1)	$message = '<div class="red">'.$txt['alert_already_activated'].'</div>';   
			else if ($rekening['account_code'] == 0)	$message = '<div class="red">'.$txt['alert_already_activated'].'</div>';    
			else{
				$page = 'activate';
				require_once('language/language-mail.php');

				$mail = new PHPMailer();
                $mail->charSet = "utf-8";
        		$mail->IsSMTP();
        		$mail->Host	= $smtp['host'];
        		$mail->SMTPAuth	= true;
        		$mail->Port	= $smtp['port'];
        		$mail->Username	= $smtp['mail'];
        		$mail->Password	= $smtp['pass'];
        		$mail->setFrom($smtp['mail'], "Pokémon World Legends");
				$mail->AddAddress($rekening['email'], $rekening['username']);
				$mail->Subject = $txt['mail_title_resend'];
				$mail->msgHTML('<div style="color: #9eadcd;padding: 0;background-color: #34465f;border-bottom: 2px solid #27374e;border-right: 1px solid #27374e;border-radius: 4px;margin-bottom: 7px;overflow:hidden;font-size:600;font-size:15px;">
			<table width="100%" cellspacing="0" cellpadding="0" align="center">
				<tr><td><div style="background-color: #2e3d53;padding: 1px 5px; font-size:12px; font-family: Arial, Helvetica, sans-serif;">
					' . $txt['mail_body_resend'] . '
				</div></td></tr>
				<tr><td align="center"><div style="padding: 5px; font-size: 12px; font-family: Arial, Helvetica, sans-serif;border-top: 1px solid #577599;">
					<b style="color: #eeeeee;">&copy;' . date("Y"). ' - Pokémon World Legends | Todos os direitos reservados.</b>
				</div></td></tr>
			</table>
		</div>');
				if ($mail->Send()) {
					$mail->ClearAllRecipients();
					$mail->ClearAttachments();

					$message = '<div class="green">' . $txt['success_resend'] . '</div>';
				} else	$message = '<div class="red">' . $mail->ErrorInfo . '</div>';
			}
		}
	}
}
echo addNPCBox(9, $txt['titlenpc'], $txt['textnpc']);
if (!empty($message))	echo $message;
?>
<center>
	<form method="post" autocomplete="off" style="padding: 10px; width: 520px; z-index: 10">
		<table width="70%" cellspacing="0" celpadding="0" border="0">
			<?php if (isset($_GET['method']) && $_GET['method'] == 'resend') { ?>
			<tr>
				<td colspan="2">
					<input type="text" name="username" value="<?=$_POST['inlognaam'];?>"  placeholder="<?=$txt['login_username'];?>:" style="width:99%; height: 40px; margin-bottom: 5px; font-size: 14px" required />
					<input name="email" type="email" value="<?=$_POST['email'];?>" placeholder="Email:" style="width:99%; height: 40px; margin-bottom: 5px; font-size: 14px" required />
				</td>
			</tr>
			<tr style="font-style: italic;">
				<td style="padding-left: 5px; padding-top: 5px; width: 50%">
					<a href="./activate"><img src="<?=$static_url?>/images/layout/seta1.png" style="margin-right: 3px; vertical-align: 1px;">Ativar Conta</a>
				</td>
				<td style="width: 50%; text-align: right; padding-top: 5px; padding-right: 10px">
					<a href="./forgot"><img src="<?=$static_url?>/images/layout/seta1.png" style="margin-right: 3px; vertical-align: 1px">Recuperar Conta</a>
				</td>
			</tr>
			<tr>
				<td colspan="2" style="padding-top: 10px">
					<button class="button-rounded ripple" name="resend" type="submit" value="resend">REENVIAR CÓDIGO DE ATIVAÇÃO!</button>
				</td>
			</tr>
			<tr>
				<td colspan="2" style="font-style: italic; text-align: center; padding-top: 5px">
					Já tem uma conta? <a href="./" style="color: #6ac7ee; font-weight: bold">LOGUE-SE</a> agora mesmo!
				</td>
			</tr>
			<?php } else { 
			    if (!empty($_SESSION['act_msg'])) {
			        echo $_SESSION['act_msg'];
			    }
			?>
			<tr>
				<td colspan="2">
					<input type="text" name="username" value="<?=$inlognaam;?>" placeholder="<?=$txt['login_username'];?>:" style="width:99%; height: 40px; margin-bottom: 5px; font-size: 14px" required />
					<input type="text" name="activatie" value="<?=$activatie;?>" placeholder="Código de Ativação:" style="width:99%; padding-left: 10px;height: 40px; margin-bottom: 5px; font-size: 14px" required />
				</td>
			</tr>
			<tr style="font-style: italic;">
				<td style="padding-left: 5px; padding-top: 5px; width: 50%">
					<a href="./activate&method=resend"><img src="<?=$static_url?>/images/layout/seta1.png" style="margin-right: 3px; vertical-align: 1px;">Esqueceu o código?</a>
				</td>
				<td style="width: 50%; text-align: right; padding-top: 5px; padding-right: 10px">
					<a href="./forgot"><img src="<?=$static_url?>/images/layout/seta1.png" style="margin-right: 3px; vertical-align: 1px">Recuperar Conta</a>
				</td>
			</tr>
			<tr>
				<td colspan="2" style="padding-top: 10px">
					<button class="button-rounded ripple" name="activate" type="submit" value="activate">ATIVAR CONTA!</button>
				</td>
			</tr>
			<tr>
				<td colspan="2" style="font-style: italic; text-align: center; padding-top: 5px">
					Já tem uma conta? <a href="./" style="color: #6ac7ee; font-weight: bold">LOGUE-SE</a> agora mesmo!
				</td>
			</tr>
			<?php } ?>
		</table>
	</form>
</center>