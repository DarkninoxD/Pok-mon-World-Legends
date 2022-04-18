<?php
if ($_POST OR $_GET) {

$tipo = $_GET['option'];


if ($tipo == "more-info") {
	$page = 'extended';
	include_once('./language/language-pages.php');
	

	$qryzita =  DB::exQuery("SELECT pw.naam, pw.type1, pw.type2, pw.zeldzaamheid, pw.groei, pw.aanval_1, pw.aanval_2, pw.aanval_3, pw.aanval_4, ps.* FROM pokemon_wild AS pw INNER JOIN pokemon_speler AS ps ON ps.wild_id = pw.wild_id WHERE ps.user_id='".$_SESSION['id']."' AND ps.id='".$_POST['id']."'");
	$pokemon = $qryzita->fetch_assoc();
	#Carregar dados corretamente para o Pokemon
	$pokemon = pokemonei($pokemon, $txt);
	#Mude o nome se for masculino ou feminino.
	$pokemon['naam'] = pokemon_naam($pokemon['naam'], $pokemon['roepnaam']);

	echo pokemon_popup($pokemon, $txt);

}

else if ($tipo == "order") {

$array = $_POST['pkm'];

$count = 1;
    foreach ($array as $idval) {
	 if ($count <= 6) DB::exQuery("UPDATE `pokemon_speler` SET `opzak_nummer`='".$count."',`opzak`='ja' WHERE `id`='".$idval."' and `user_id`='".$_SESSION['id']."'");
        $count++;
    }

 if ($count <= 6) echo 1;
 else echo 0;

}

else if ($tipo == "bringaway") {

    $slot = substr($_POST['target'], 5);
    $idval = substr($_POST['item'], 4);

    DB::exQuery("UPDATE `pokemon_speler` SET `opzak_nummer`='".$slot."', `opzak`='nee' WHERE `id`='".$idval."' and `user_id`='".$_SESSION['id']."'");


    $pokesnamao = DB::exQuery("SELECT id FROM `pokemon_speler` where `opzak`='ja' AND `user_id`='".$_SESSION['id']."'")->num_rows;
    if ($pokesnamao == 0) { 
        echo 0; 
        DB::exQuery("UPDATE `pokemon_speler` SET `opzak_nummer`='1', `opzak`='ja' WHERE `id`='".$idval."' and `user_id`='".$_SESSION['id']."'");
    }else { 
        echo 1; 
    }
}

else if ($tipo == "equip") { 
   require('use_item_equip.php');
}

else if ($tipo == "options") { 
$verifybox = DB::exQuery("SELECT * FROM `boxes` WHERE `user_id`='".$_SESSION['id']."' AND `box_id`='".$_POST['box']."' limit 1");

if ($verifybox->num_rows > 0) $verifyboxx = $verifybox->fetch_assoc();


if (isset($_POST['save_box'])) {


if ($_POST['box-bg'] == '') $nmrfundo = 1;
else if ($_POST['box-bg'] == 'simple2') $nmrfundo = 2;
else if ($_POST['box-bg'] == 'forest') $nmrfundo = 3;
else if ($_POST['box-bg'] == 'forest2') $nmrfundo = 4;
else if ($_POST['box-bg'] == 'city') $nmrfundo = 5;
else if ($_POST['box-bg'] == 'city2') $nmrfundo = 6;
else if ($_POST['box-bg'] == 'desert') $nmrfundo = 7;
else if ($_POST['box-bg'] == 'desert2') $nmrfundo = 8;
else if ($_POST['box-bg'] == 'savanna') $nmrfundo = 9;
else if ($_POST['box-bg'] == 'savanna2') $nmrfundo = 10;
else if ($_POST['box-bg'] == 'crag') $nmrfundo = 11;
else if ($_POST['box-bg'] == 'crag2') $nmrfundo = 12;
else if ($_POST['box-bg'] == 'volcano') $nmrfundo = 13;
else if ($_POST['box-bg'] == 'volcano2') $nmrfundo = 14;
else if ($_POST['box-bg'] == 'snow') $nmrfundo = 15;
else if ($_POST['box-bg'] == 'snow2') $nmrfundo = 16;
else if ($_POST['box-bg'] == 'cave') $nmrfundo = 17;
else if ($_POST['box-bg'] == 'cave2') $nmrfundo = 18;
else if ($_POST['box-bg'] == 'beach') $nmrfundo = 19;
else if ($_POST['box-bg'] == 'beach2') $nmrfundo = 20;
else if ($_POST['box-bg'] == 'seafloor') $nmrfundo = 21;
else if ($_POST['box-bg'] == 'seafloor2') $nmrfundo = 22;
else if ($_POST['box-bg'] == 'river') $nmrfundo = 23;
else if ($_POST['box-bg'] == 'river2') $nmrfundo = 24;
else if ($_POST['box-bg'] == 'sky') $nmrfundo = 25;
else $nmrfundo = 0;

if ($nmrfundo == 0) exit;

    if ($verifybox->num_rows > 0) {
        #UPDATE BOX
        DB::exQuery("UPDATE `boxes` SET `fundo`='".$_POST['box-bg']."',`nome`='".$_POST['box_name']."' WHERE `id`='".$verifyboxx['id']."'");
        exit(header("Location: ./box&box=".$_POST['box'].""));
    } else {
        #INSERT BOX
        DB::exQuery("INSERT INTO `boxes`(`user_id`, `box_id`, `fundo`, `nome`) VALUES ('".$_SESSION['id']."','".$_POST['box']."','".$_POST['box-bg']."','".$_POST['box_name']."')");
        exit(header("Location: ./box&box=".$_POST['box'].""));
    }
}





if ($verifybox->num_rows > 0) {
    $nomebox = $verifyboxx['nome'];
    $fundo = $verifyboxx['fundo'];

    if ($fundo == '') $nmrfundo = 1;
    else if ($fundo == 'simple2') $nmrfundo = 2;
    else if ($fundo == 'forest') $nmrfundo = 3;
    else if ($fundo == 'forest2') $nmrfundo = 4;
    else if ($fundo == 'city') $nmrfundo = 5;
    else if ($fundo == 'city2') $nmrfundo = 6;
    else if ($fundo == 'desert') $nmrfundo = 7;
    else if ($fundo == 'desert2') $nmrfundo = 8;
    else if ($fundo == 'savanna') $nmrfundo = 9;
    else if ($fundo == 'savanna2') $nmrfundo = 10;
    else if ($fundo == 'crag') $nmrfundo = 11;
    else if ($fundo == 'crag2') $nmrfundo = 12;
    else if ($fundo == 'volcano') $nmrfundo = 13;
    else if ($fundo == 'volcano2') $nmrfundo = 14;
    else if ($fundo == 'snow') $nmrfundo = 15;
    else if ($fundo == 'snow2') $nmrfundo = 16;
    else if ($fundo == 'cave') $nmrfundo = 17;
    else if ($fundo == 'cave2') $nmrfundo = 18;
    else if ($fundo == 'beach') $nmrfundo = 19;
    else if ($fundo == 'beach2') $nmrfundo = 20;
    else if ($fundo == 'seafloor') $nmrfundo = 21;
    else if ($fundo == 'seafloor2') $nmrfundo = 22;
    else if ($fundo == 'river') $nmrfundo = 23;
    else if ($fundo == 'river2') $nmrfundo = 24;
    else if ($fundo == 'sky') $nmrfundo = 25;

    $fundonum = $nmrfundo;
} else {
        $nomebox = "Box ".$_POST['box']."";
        $fundo = "";
        $fundonum = 1;
}
?>
<style>
.ui-widget input, .ui-widget select, .ui-widget textarea, .ui-widget button {
    font-family: Lucida Grande,Lucida Sans,Arial,sans-serif;
    font-size: 1em;
}
.b-blue {
    color: #d9eef7;
    border: 1px solid #0076a3;
    background: #007ead;
    background: -webkit-gradient(linear,left top,left bottom,from(#0095cc),to(#00678e));
    background: -moz-linear-gradient(top,#0095cc,#00678e);
    filter: progid:DXImageTransform.Microsoft.gradient(startColorstr='#0095cc', endColorstr='#00678e');
}
.b-small {
    font-size: 11px;
    padding: .2em 1em .275em;
}
.b-button {
    display: inline-block;
    zoom: 1;
    vertical-align: baseline;
    margin: 0 2px;
    outline: 0;
    cursor: pointer;
    text-align: center;
    font: 14px/100% Arial,Helvetica,sans-serif;
    padding: .5em 2em .55em;
    text-shadow: 0 1px 1px rgba(0,0,0,.3);
    -webkit-border-radius: .5em;
    -moz-border-radius: .5em;
    border-radius: .5em;
    -webkit-box-shadow: 0 1px 2px rgba(0,0,0,.2);
    -moz-box-shadow: 0 1px 2px rgba(0,0,0,.2);
    box-shadow: 0 1px 2px rgba(0,0,0,.2);
}
.b-button, .b-button:hover {
    text-decoration: none;
}
</style>
                <form action="ajax.php?act=box&option=options" method="post">
                    <table>
                        <tr>
                            <td><label for="box_name">Nome:</label></td>
                            <td><input type="text" id="box_name" class="text_long" name="box_name" maxlength="20" value="<?=$nomebox?>"/></td>
                        </tr>
                        <tr>
                            <td colspan="2">
                                <div style="margin-bottom: 5px;">
                                    <label>Plano de fundo:</label> <span id="label-name" style="font-weight: bold;"></span>
                                </div>
                                <input type="hidden" id="box-bg" name="box-bg" value="<?=$fundo?>"/>
                                <input type="hidden" id="box" name="box" value="<?=$_POST['box']?>"/>
                                <img src="<?=$static_url?>/images/icons/arrow_left_25.png" style="margin-bottom: 30px;" onclick="previousDivs()"/>
                                <div class="box-bg-img" style="display: none; width: 200px; height: 100px; background-image: url('<?=$static_url?>/images/box/Simple.png'); background-size: 200px;" name="Simples" value=""></div><div class="box-bg-img" style="display: none; width: 200px; height: 100px; background-image: url('<?=$static_url?>/images/box/Simple2.png'); background-size: 200px;" name="Simples 2" value="simple2"></div><div class="box-bg-img" style="display: none; width: 200px; height: 100px; background-image: url('<?=$static_url?>/images/box/Forest.png'); background-size: 200px;" name="Floresta" value="forest"></div><div class="box-bg-img" style="display: none; width: 200px; height: 100px; background-image: url('<?=$static_url?>/images/box/Forest2.png'); background-size: 200px;" name="Floresta 2" value="forest2"></div><div class="box-bg-img" style="display: none; width: 200px; height: 100px; background-image: url('<?=$static_url?>/images/box/City.png'); background-size: 200px;" name="Cidade" value="city"></div><div class="box-bg-img" style="display: none; width: 200px; height: 100px; background-image: url('<?=$static_url?>/images/box/City2.png'); background-size: 200px;" name="Cidade 2" value="city2"></div><div class="box-bg-img" style="display: none; width: 200px; height: 100px; background-image: url('<?=$static_url?>/images/box/Desert.png'); background-size: 200px;" name="Deserto" value="desert"></div><div class="box-bg-img" style="display: none; width: 200px; height: 100px; background-image: url('<?=$static_url?>/images/box/Desert2.png'); background-size: 200px;" name="Deserto 2" value="desert2"></div><div class="box-bg-img" style="display: none; width: 200px; height: 100px; background-image: url('<?=$static_url?>/images/box/Savanna.png'); background-size: 200px;" name="Savana" value="savanna"></div><div class="box-bg-img" style="display: none; width: 200px; height: 100px; background-image: url('<?=$static_url?>/images/box/Savanna2.png'); background-size: 200px;" name="Savana 2" value="savanna2"></div><div class="box-bg-img" style="display: none; width: 200px; height: 100px; background-image: url('<?=$static_url?>/images/box/Crag.png'); background-size: 200px;" name="Penhasco" value="crag"></div><div class="box-bg-img" style="display: none; width: 200px; height: 100px; background-image: url('<?=$static_url?>/images/box/Crag2.png'); background-size: 200px;" name="Penhasco 2" value="crag2"></div><div class="box-bg-img" style="display: none; width: 200px; height: 100px; background-image: url('<?=$static_url?>/images/box/Volcano.png'); background-size: 200px;" name="Vulcão" value="volcano"></div><div class="box-bg-img" style="display: none; width: 200px; height: 100px; background-image: url('<?=$static_url?>/images/box/Volcano2.png'); background-size: 200px;" name="Vulcão 2" value="volcano2"></div><div class="box-bg-img" style="display: none; width: 200px; height: 100px; background-image: url('<?=$static_url?>/images/box/Snow.png'); background-size: 200px;" name="Neve" value="snow"></div><div class="box-bg-img" style="display: none; width: 200px; height: 100px; background-image: url('<?=$static_url?>/images/box/Snow2.png'); background-size: 200px;" name="Neve 2" value="snow2"></div><div class="box-bg-img" style="display: none; width: 200px; height: 100px; background-image: url('<?=$static_url?>/images/box/Cave.png'); background-size: 200px;" name="Caverna" value="cave"></div><div class="box-bg-img" style="display: none; width: 200px; height: 100px; background-image: url('<?=$static_url?>/images/box/Cave2.png'); background-size: 200px;" name="Caverna 2" value="cave2"></div><div class="box-bg-img" style="display: none; width: 200px; height: 100px; background-image: url('<?=$static_url?>/images/box/Beach.png'); background-size: 200px;" name="Praia" value="beach"></div><div class="box-bg-img" style="display: none; width: 200px; height: 100px; background-image: url('<?=$static_url?>/images/box/Beach2.png'); background-size: 200px;" name="Praia 2" value="beach2"></div><div class="box-bg-img" style="display: none; width: 200px; height: 100px; background-image: url('<?=$static_url?>/images/box/Seafloor.png'); background-size: 200px;" name="Fundo do Mar" value="seafloor"></div><div class="box-bg-img" style="display: none; width: 200px; height: 100px; background-image: url('<?=$static_url?>/images/box/Seafloor2.png'); background-size: 200px;" name="Fundo do Mar 2" value="seafloor2"></div><div class="box-bg-img" style="display: none; width: 200px; height: 100px; background-image: url('<?=$static_url?>/images/box/River.png'); background-size: 200px;" name="Rio" value="river"></div><div class="box-bg-img" style="display: none; width: 200px; height: 100px; background-image: url('<?=$static_url?>/images/box/River2.png'); background-size: 200px;" name="Rio 2" value="river2"></div><div class="box-bg-img" style="display: none; width: 200px; height: 100px; background-image: url('<?=$static_url?>/images/box/Sky.png'); background-size: 200px;" name="Céu" value="sky"></div>                                <img src="<?=$static_url?>/images/icons/arrow_right_25.png" style="margin-bottom: 30px;" onclick="nextDivs()"/>
                            </td>
                        </tr>
                        <tr>
                            <th colspan="2">
                                <input type="submit" name="save_box" value="Salvar"/>
                            </th>
                        </tr>
                    </table>
                </form>
                <script>
                    var slideIndex = <?=$fundonum?>;
                    var bg_imgs = document.getElementsByClassName("box-bg-img");
                    var label_name = document.getElementById("label-name");
                    var box_bg = document.getElementById("box-bg");
                    showDivs(slideIndex);

                    function nextDivs() {
                        showDivs(++slideIndex);
                    }
                    function previousDivs() {
                        showDivs(--slideIndex);
                    }

                    function showDivs(n) {
                        var i;
                        if (n > bg_imgs.length) {
                            slideIndex = 1;
                        }
                        if (n < 1) {
                            slideIndex = bg_imgs.length;
                        }

                        for (i = 0; i < bg_imgs.length; i++) {
                            bg_imgs[i].style.display = "none";
                        }
                        bg_imgs[slideIndex - 1].style.display = "inline-block";
                        box_bg.value = bg_imgs[slideIndex - 1].getAttribute("value");
                        label_name.innerHTML = bg_imgs[slideIndex - 1].getAttribute("name");
                    }
                </script>
                        
                
<?php
}

}

?>