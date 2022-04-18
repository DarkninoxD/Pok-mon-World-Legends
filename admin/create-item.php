<?php
#Script laden zodat je nooit pagina buiten de index om kan laden
include("app/includes/resources/security.php");

#Admin controle
if ($gebruiker['admin'] < 3) { header('location: ./home'); exit; }

if (isset($_POST['create'])) {
    $naam = $_POST['naam'];
    $soort = str_replace('_', ' ', $_POST['soort']);
    $beschikbaar = empty($_POST['beschikbaar'])? 0 : 1;
    $silver = $_POST['silver'];
    $gold = $_POST['gold'];
    $roleta = empty($_POST['roleta'])? 'nao' : 'sim';
    $equip = empty($_POST['equip'])? 0 : 1;
    $descr = $_POST['descr'];
    $foto = $_FILES['sprite'];

    // if (!empty($foto['name'])) {
    //     if (!preg_match("/^image\/(pjpeg|jpeg|png|gif|bmp)$/", $foto["type"])) {
    //         echo "Isso não é uma imagem.";
    //         exit ();
   	//  	} else {
    //         preg_match("/\.(gif|bmp|png|jpg|jpeg) {1}$/i", $foto["name"], $ext);

    //     	$nome_imagem = $naam.".".$ext[1];

    //     	$caminho_imagem = "public/images/items/" . $nome_imagem;

	// 		move_uploaded_file($foto["tmp_name"], $caminho_imagem);        
    //     }
    // } else {
    //     $dir = scandir ('cron/atualizador_db/itens/Itens/');
    //     $name = $naam.'.png';
    //     if (in_array($name, $dir)) {
    //         copy ("cron/atualizador_db/itens/Itens/".$name, "public/images/items/".$name);
    //     } else {
    //         exit ();
    //     }
    // }

    DB::exQuery("ALTER TABLE `gebruikers_item` ADD `$naam` INT(255) NOT NULL DEFAULT 0");
    DB::exQuery("INSERT INTO `markt` (`naam`, `soort`, `beschikbaar`, `silver`, `gold`, `roleta`, `equip`, `omschrijving_nl`, `omschrijving_en`, `omschrijving_pt`) 
    VALUES ('$naam', '$soort', '$beschikbaar', '$silver', '$gold', '$roleta', '$equip', '$descr', '$descr', '$descr')"); 

    header("refresh: 0;");
}

?>

<form method="post" enctype="multipart/form-data">
<center>
<p>Criar itens!</p>
<table width="600">
	<tr>
    	<td width="150">Nome:</td>
        <td width="200"><input type="text" name="naam" class="text_long"></td>
    </tr>
    <!-- <tr>
        <td>Sprite:</td>
        <td><input type="file" name="sprite" id="sprite"></td>
    </tr> -->
    <tr>
    	<td>Tipo:</td>
        <td>
            <select name="soort" class="text_select" required>
				<option value="none">Escolha</option>
				<option value="balls">Pokéball</option>
                <option value="hm">HM</option>
                <option value="items">Itens</option>
                <option value="potions">Poções</option>
                <option value="special_items">Itens Especiais</option>
                <option value="stones" selected>Pedras Evolutivas</option>
                <option value="tm">TM</option>
			</select>
        </td>
    </tr>
    <tr>
    	<td>Ativo?</td>
        <td><input type="checkbox" value="1" name="beschikbaar" class="text_long"/> (Marcado para sim, desmarcado para não)</td>
    </tr>
    <tr>
    	<td>Preço:</td>
        <td>Silver: &nbsp;<input type="number" name="silver" min="0" value="0"><br>Gold: &nbsp;&nbsp;&nbsp;<input type="number" name="gold" min="0" value="0"></td>
    </tr>
    <tr>
      <td>Aparece na Roleta?</td>
      <td><input type="checkbox" value="sim" name="roleta" class="text_long"/> (Marcado para sim, desmarcado para não)</td>
    </tr>
    <tr>
      <td>É Equipável?</td>
      <td><input type="checkbox" value="1" name="equip" class="text_long"/> (Marcado para sim, desmarcado para não)</td>
    </tr>
    <tr>
      <td>Descrição:</td>
      <td><textarea name="descr" id="" cols="30" rows="5"></textarea></td>
    </tr>
    <tr>
    	<td>&nbsp;</td>
        <td><input type="submit" name="create" value="Criar!" class="button" style="width: 60%"/></td>
    </tr>
</table>
</center>
</form>