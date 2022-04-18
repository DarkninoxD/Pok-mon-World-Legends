<?php
require_once 'app/includes/resources/ingame.inc.php';

    if (isset($_GET['id'])) {
        $qryzita =  DB::exQuery("SELECT pw.naam, pw.type1, pw.type2, pw.zeldzaamheid, pw.groei, pw.aanval_1, pw.aanval_2, pw.aanval_3, pw.aanval_4, ps.* FROM pokemon_wild AS pw INNER JOIN pokemon_speler AS ps ON ps.wild_id = pw.wild_id WHERE ps.user_id='".$_SESSION['id']."' AND ps.id='".$_GET['id']."'");
        $pokemon = $qryzita->fetch_assoc();
        $pokemon['naam'] = pokemon_naam($pokemon['naam'], $pokemon['roepnaam'], $pokemon['icon']);

        if (isset($_POST['item']) && isset($_POST['send_item'])) {
            $item = $_POST['item'];
            if (pokemon_equip($pokemon['wild_id'], $item) && $pokemon['ei'] == 0 && $item != $pokemon['item']) {
                $item2 = DB::exQuery("SELECT * FROM `gebruikers_item` WHERE `user_id`='".$_SESSION['id']."' AND `$item` > 0");
                if ($item2->num_rows > 0) {
                    DB::exQuery("UPDATE `gebruikers_item` SET `$item`=`$item`-1 WHERE `user_id`='".$_SESSION['id']."'");
                    DB::exQuery("UPDATE `pokemon_speler` SET `item`='$item' WHERE id='$_GET[id]'");
                }
            }
        }
    }
?>
<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="content-type" content="text/html; charset=utf-8" />
	<title><?=$site_title;?></title>
	<link rel="stylesheet" href="<?=$static_url;?>/stylesheets/style.css" />
    <style>
        table {
            font-size: 13px
        }
    </style>
</head>
<body>
    <?php
            if ($qryzita->num_rows > 0) {
                $itemData = DB::exQuery("SELECT * FROM `gebruikers_item` WHERE `user_id`='" . $_SESSION['id'] . "'")->fetch_assoc();
                $arrayItems = array();
            
                $getItems = DB::exQuery("SELECT * FROM `markt` WHERE `soort`='special items' AND `equip`='1' ORDER BY `naam` ASC");
                while ($item = $getItems->fetch_assoc()) {
                    if ($itemData[$item['naam']] > 0) {
                        array_push($arrayItems, $item);
                    }
                }

                echo '<div class="box-content"><table class="general" width="100%">
                        <thead>
                            <tr><th colspan="3">Equipar '.$pokemon['naam'].':</th></tr>
                            <tr>
                                <th width="65%">ITEM</th>
                                <th width="15%">QNTD.</th>
                                <th width="20%">USAR</th>
                            </tr>
                            <tr><th colspan="3">Se este Pokémon já tiver um item equipado, o item antigo será destruído!</th></tr>
                        </thead>
                        <tbody>';
                if ($pokemon['ei'] == 0) {
                    if (count($arrayItems) > 0) {
                        foreach ($arrayItems as $q) {
                            if (pokemon_equip($pokemon['wild_id'], $q['naam'])) {
                                echo '<tr>
                                    <td>
                                        <div style="margin-left: 10px" title="'.nl2br($q['omschrijving_' . $_COOKIE['pa_language']]).'">
                                            <img src="'.$static_url.'/images/items/'.$q['naam'].'.png" style="vertical-align: middle"> '.$q['naam'].'
                                        </div>
                                    </td>
                                    <td style="text-align: center">
                                        '.$itemData[$q['naam']].'x
                                    </td>
                                    <td>
                                        <form method="post">
                                            <input type="hidden" name="item" value="'.$q['naam'].'"/>
                                            <input type="submit" name="send_item" value="Equipar '.$q['naam'].'" class="button blue" style="width: 100%"/>
                                        </form>
                                    </td>
                                </tr>';
                            }
                        }
                    } else {
                        echo '<tr><td colspan="3"><div class="red">Você não tem nenhum item para equipar em seus Pokémon!</div></td></tr>';
                    }
                } else {
                    echo '<tr><td colspan="3"><div class="red">Você não pode equipar este item em um Ovo Pokémon!</div></td></tr>';
                }
                echo '</tbody></table></div>';
            }
    ?>

    <script src="<?=$static_url;?>/javascripts/jquery-2.1.3.min.js"></script>
</body>
</html>