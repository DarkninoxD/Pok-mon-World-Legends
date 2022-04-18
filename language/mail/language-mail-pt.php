<?php
if ($page == 'register') {
	$txt['mail_title'] = 'Cadastro efetuado! Ative sua Conta!';
	$txt['mail_body'] = '<h3 style="margin: 5px 0 5px 0; font-size:21px;">Olá ' . $inlognaam . '! Seu cadastro foi efetuado.</h3>
    	<p style="margin: 0 0 5px 0;">
    		Você está a um passo de iniciar sua Jornada no <b><u>Pokémon World Legends</u></b>, e agora você só precisa <a href="https://www.pokemonworldlegends.com/activate" target="_blank" rel="noopener noreferrer">Ativar sua Conta</a>.<br /><br />
    		<b>Seu Usuário:</b> <u style="color:#eeeeee;">' . $inlognaam . '</u><br />
    		<b>Código de Ativação:</b> <u style="color:#eeeeee;">' . $activatiecode . '</u><br />
    		<center><u>Guarde este e-mail, pois estas informações podem ser úteis futuramente!</u><br />
    		<b>Atenciosamente, Equipe Pokémon World Legends!<br /></b></center>
    	</p>';
} else if ($page == 'contact') {
	$txt['mail_body'] = '<h3 style="margin: 5px 0 5px 0;">Mensagem de contato:</h3>
	<p style="margin: 0 0 5px 0;">
		' . $bericht . '
	</p>';
} else if ($page == 'forgot') {
	$txt['mail_title'] = 'Esqueceu sua senha?';
	$txt['mail_body'] = '<h3 style="margin: 5px 0 5px 0; font-size:21px;">Olá <b>' . $gegeven['username'] . '</b>!</h3>
	<p style="margin: 0 0 5px 0;">
		Você solicitou uma nova senha, e ela agora é: <u style="color:#eeeeee;">' . $nieuwww . '</u><br /><br />
		<center><u>Lembre-se de mudá-la nas Configurações da sua Conta!</u><br />
    	<b>Atenciosamente, Equipe Pokémon World Legends!<br /></b></center>
	</p>';
} else if ($page == 'activate') {
	$txt['mail_title_activate'] = 'Seja bem vindo ao World Legends';
	$txt['mail_body_activate'] = '<h3 style="margin: 5px 0 5px 0;">Olá ' . $rekening['username'] . '!</h3>
	<p style="margin: 0 0 5px 0;">
		Você acaba de finalizar sua inscrição! Agora você pode se divertir jogando com amigos, capturando +900 espécies Pokémon, batalhando contra Treinadores e muito mais!<br /><br />
		<b>Bonus de boas-vindas:</b> ' . $activate_bonus . '<br /><br />
		<b>Atenciosamente,</b><br />
		<u>Equipe World Legends!</u>
	</p>';

	$txt['mail_title_resend'] = 'Código de Ativação';
	$txt['mail_body_resend'] = '<h3 style="margin: 5px 0 5px 0; font-size:21px;">Olá ' . $rekening['username'] . '!</h3>
	<p style="margin: 0 0 5px 0;">
		Conforme solicitado, lhe re-enviamos o Código de Ativação de sua conta.<br /><br />
		<b>Código de ativação:</b> <u style="color:#eeeeee;">' . $rekening['account_code'] . '</u><br /><br />
		<center><b>Atenciosamente, Equipe Pokémon World Legends!</b></center>
	</p>';
} else if ($page == 'donate') {
	$txt['mail_title'] = 'Pokemon Area Premium Shop';
	$txt['mail_body'] = 'Beste '.$voornaam.' '.$achternaam.',<br /><br />
		Je hebt een <b>'.$packnaam.'</b> gekocht ter waarde van &euro;'.$packkosten.'<br />
		Bewaar deze mail goed, dit geldt als een betalingsbewijs.<br /><br />
		Veel plezier hiermee!<br /><br />
		Met vriendelijke groet,<br />
		Pokemon Browser MMO Team';
}