<?php
include('app/includes/resources/security.php');

echo addNPCBox(24, 'Criar Clã', 'Crie seu Clã e chame seu Amigos ou Outros Treinadores para serem membros e jogarem em conjunto! <br> Para criar um Clã, você deverá ter 7 dias de <b>PREMIUM</b> <img src="'.$static_url.'/images/icons/vip.gif"> e <img src="'.$static_url.'/images/icons/gold.png" title="Golds"> 100 <b>Golds</b>!');

if (!empty($gebruiker['clan'])) { 
    echo '<div class="red">Você faz parte de um CLÃ! CLIQUE <a href="./clans&action=profile&id='.$gebruiker['clan'].'">AQUI</a> PARA VÊ-LO!</div>';
} else if ($gebruiker['premiumaccount'] < (time() + 86400 * 7)) {
    echo '<div class="red"><a href="./gold-market">Compre PREMIUM CLICANDO AQUI!</div></a>';
} else {
if (isset($_POST['icon']) && isset($_POST['descr']) && isset($_POST['sigla']) && isset($_POST['name']) && isset($_POST['submit'])) {
    if (!empty($gebruiker['clan'])) echo '<div class="red">Você já faz parte de um CLÃ!</div>';
    else if ($gebruiker['rank'] < 5) echo '<div class="red">VOCÊ PRECISA TER NO MÍNIMO RANK 5 - FIRST COACH PARA TER ACESSO AOS CLÃS!</div>';
    else if ($gebruiker['premiumaccount'] < (time() + 86400 * 7)) echo '<div class="red">VOCÊ PRECISA TER PREMIUM!</div>';
    else if ($rekening['gold'] < 100) echo '<div class="red">VOCÊ PRECISA TER NO MÍNIMO <img src="'.$static_url.'/images/icons/gold.png" title="Golds"> 100 <b>Golds</b>!</div>';
    else {
        $icon = $_POST['icon'];
        $descr = $_POST['descr'];
        $sigla = strtolower($_POST['sigla']);
        $name = $_POST['name'];

        if ($icon > 14 || $icon <= 0) echo '<div class="red">ÍCONE DE CLÃ INVÁLIDO!</div>';
        else if (strlen($descr) > 500) echo '<div class="red">A APRESENTAÇÃO DO CLÃ NÃO PODE TER MAIS DE 500 CARACTERES!</div>';
        else if (strlen($name) < 5 || strlen($name) > 20) echo '<div class="red">O NOME DO CLÃ NÃO PODE CONTER MENOS QUE 5 OU MAIS QUE 20 CARACTERES!</div>';
        else if (strlen($sigla) < 5 || strlen($sigla) > 5) echo '<div class="red">A SIGLA DO CLÃ NÃO PODE CONTER MENOS OU MAIS QUE 5 CARACTERES!</div>';
        else {
            $name_verify = strtolower($name);
            $verify = DB::exQuery("SELECT `id` FROM `clans` WHERE `name`='$name_verify' OR `sigla`='$sigla'")->num_rows;

            if ($verify >= 1) {
                echo '<div class="red">JÁ EXISTE UM CLÃ COM ESTE NOME OU SIGLA!</div>';
            } else {
                $sigla = strtoupper($sigla);

                $infos = array(
                    'image' => $icon,
                    'descr' => htmlspecialchars($descr),
                    'sigla' => $sigla,
                    'name' => $name,
                    'user' => $_SESSION['id']
                );

                $clan->create($infos);
                DB::exQuery("UPDATE `rekeningen` SET `gold`=`gold`-'100' WHERE `acc_id`='".$_SESSION['acc_id']."'");
                echo '<div class="green">SEU CLÃ <a href="./clans&action=profile">'.$name.' ('.$sigla.')</a> FOI CRIADO COM SUCESSO!</div>';
            }
        }
    }
}
?>


<style>
    .carousel-cell {
        margin: 6px 13px;
        filter: grayscale(100%);
		overflow: hidden;
		transform: scale(0.8);
    }
    .carousel-cell.is-selected {
        filter: grayscale(20%);
        transition: .7s;
		transform: scale(1);
    }
</style>

<form method="post" autocomplete="off" onsubmit="return confirm('Deseja realmente criar este Clã?');">
    <input type="hidden" id="icon" name="icon" value="1">

	<div class="box-content">
        <table class="general" width="100%">
                <thead>
                    <tr><th colspan="9">Escolha o Ícone de seu Clã</th></tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="no-padding" colspan="9" style="height: 175px">
                            <div class="main-carousel">
                            <?php
                                for ($i = 1; $i <= 14; $i++) {
                                    echo "<div class='carousel-cell' style=\"width: 160px; height: 160px\">";
                                    echo "<img src=\"" . $static_url . "/images/clans_icons/" . $i . ".png\" />";
                                    echo "</div>";
                                }
                            ?>
                            </div>
                        </td>
                    </tr>
                </tbody>
        </table>
    </div>
    <div class="box-content" style="margin-top: 7px">
        <table class="general" width="100%">
                <thead>
                    <tr><th>Informações do Clã</th></tr>
                </thead>
                <tbody>
                    <tr>
                        <td class="no-padding">
                            <center>
                            <table width="37%" border="0" style="margin: 10px; text-align: center; padding: 10px">
                                <tbody><tr>
                                    <td><b style="color: #9eadcd; font-size: 12px">Nome do Clã:</b><br><input type="text" name="name" value="" id="player" class="input-blue" required minlength="5" maxlength="20" style="margin-top: 5px"></td>
                                    <td><b style="color: #9eadcd; font-size: 12px">Sigla do Clã:</b><br><input type="text" name="sigla" value="" id="send_amount" class="input-blue" required maxlength="5" minlength="5" style="margin-top: 5px; text-transform: uppercase;"></td>
                                </tr>
                                <tr>
                                    <td colspan="2">
                                        <textarea name="descr" cols="25" rows="10" style="margin-top: 7px;width: 100%" maxlength="500" placeholder="Apresentação do Clã"></textarea>
                                    </td>
                                </tr>
                                </tbody>
                            </table>
                            </center>
                        </td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr style="text-align: center; font-size: 13px">
                        <td>
                            <input type="submit" id="submit" name="submit" style="margin: 6px" value="CRIAR CLÃ" class="button">
                        </td>
                    </tr>
                </tfoot>
        </table>
    </div>
</form>

<script type="text/javascript">
	var $carousel = $('.main-carousel');
	var $icon = $('#icon');

	var $car = $carousel.flickity({
		cellAlign: 'center',
		contain: false,
		pageDots: false,
		wrapAround: false
	});

	var flkty = $carousel.data('flickity');

	$carousel.on('select.flickity', function() {
		$icon.val((flkty.selectedIndex+1));
	});

    $car.resize();
</script>

<?php } ?>