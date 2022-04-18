<?php
header('Content-Type: text/html; charset=utf-8'); 	
//Script laden zodat je nooit pagina buiten de index om kan laden
include("app/includes/resources/security.php");

//Admin controle
if ($gebruiker['admin'] < 3) { header('location: ./home'); exit; }

if (isset($_POST['text']) && isset($_POST['title'])) {
	$text = htmlspecialchars($_POST['text']);
	$title = $_POST['title'];
	$date = date ('d/m/Y'); 
	DB::exQuery("INSERT INTO `official_message` SET `title`='$title', `message`='$text', `admin`='$_SESSION[acc_id]', `date`='$date'");
	echo 'Mensagem enviada!';
}
?>
<script src="<?=$static_url?>/plugins/tinymce/tinymce.min.js"></script>

<style>
	.mce-tinymce {
		border-radius: 5px;
		overflow: hidden;
	}

	.mce-tinymce img {
		vertical-align: bottom;
	}
</style>

<form method="post">
    <input type="text" name="title" style="width: 100%; padding: 5px; margin: 7px 0" placeholder="Assunto da Mensagem" required>
	<textarea id="text" name="text"></textarea>
	<input type="submit" value="Mandar Mensagem" class="button" style="margin: 7px 0;">

	<script>
		$(document).ready(function() {
			tinymce.init({ 
				selector:'#text',
				branding: false,
				language: 'pt_BR',
				menubar: false,
				statusbar: false,
				height: 300,
				plugins: "emoticons lists table image",
				toolbar: 'undo redo | styleselect | bold italic underline | alignleft aligncenter alignright | bullist numlist | outdent indent | table emoticons image',
			});
		});
	</script>
</form>