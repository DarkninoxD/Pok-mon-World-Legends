<?php
if (isset($_SESSION['ploot']) && isset($_SESSION['id'])) {
    $gebruiker = DB::exQuery("SELECT `pokeloot_token`,`premiumaccount`,`rank`,`rankexp`,`rankexpnodig` FROM `gebruikers` WHERE `user_id`={$_SESSION['id']} LIMIT 1")->fetch_assoc();

    if ($_SESSION['ploot'] != $gebruiker['pokeloot_token']) {
        echo 'error | Acesso inválido!';
    } else {
        function addSilvers($min = 500, $max = 4999) {
            global $static_url;
            $silvers = rand($min, $max);
            DB::exQuery("UPDATE `gebruikers` SET `silver`=`silver`+{$silvers},`pokeloot_token`='NULL' WHERE `user_id`={$_SESSION['id']} LIMIT 1");
            return 'success | Parabéns, você ganhou <img src="' . $static_url . '/images/icons/silver.png" /> <b>' . highamount($silvers) .'</b> no <b>Poké-Loot</b>!';
        }
    
        function getSlot () {
            $gb_itens = DB::exQuery("SELECT `itembox` FROM `gebruikers_item` WHERE `user_id`={$_SESSION['id']} LIMIT 1")->fetch_assoc();
            if ($gb_itens['itembox'] == 'Black box') $ruimte['max'] = 1000;
            else if ($gb_itens['itembox'] == 'Purple box') $ruimte['max'] = 500;
            else if ($gb_itens['itembox'] == 'Red box') $ruimte['max'] = 250;
            else if ($gb_itens['itembox'] == 'Blue box') $ruimte['max'] = 100;
            else if ($gb_itens['itembox'] == 'Yellow box') $ruimte['max'] = 50;
            else if ($gb_itens['itembox'] == 'Bag') $ruimte['max'] = 20;

            return $ruimte['max'] - freeSlots();
        }

        function addItems ($item, $quant) {
            global $static_url;
            $slots = getSlot ();
            if ($slots > $quant) {
                DB::exQuery("UPDATE `gebruikers_item` SET `$item`=`$item`+$quant WHERE user_id='$_SESSION[id]'");
                return 'success | Parabéns, você ganhou <b>x'.$quant.'</b> <img src="' . $static_url . '/images/items/' . $item . '.png" style="vertical-align: middle"/> no <b>Poké-Loot</b>!';
            } else {
                return 'error | Você não tem espaço suficiente em sua mochila!';
            }
        }

        function addTM ($item, $quant) {
            global $static_url;
            $slots = getSlot ();
            if ($slots > $quant) {
                DB::exQuery("UPDATE `gebruikers_tmhm` SET `$item`=`$item`+$quant WHERE user_id='$_SESSION[id]'");
                return 'success | Parabéns, você ganhou <b>x'.$quant.'</b> '.$item.' no <b>Poké-Loot</b>!';
            } else {
                return 'error | Você não tem espaço suficiente em sua mochila!';
            }
        }

        $random = rand(1, 6);
        if ($random == 6) if (rand (1, 3) == 3) $random = rand (1, 5);

        $quests->setStatus('pokeloot', $_SESSION['id']);
        
		switch($random) {
			case 1:
				echo addSilvers(1000, 8000);
                break;
            case 2:
                if (rand(1, 5) <= 4) {
                    echo addItems('Poke ball', rand(3, 5));
                } else {
                    echo addItems('Ultra Ball', 2);
                }
                break;
            case 3:
                $rand = rand(1, 10);
                if ($rand <= 7) {
                    echo addItems('Potion', rand(2, 3));
                } else if ($rand <= 9) {
                    echo addItems('Revive', 1);
                } else {
                    echo addItems('Hyper potion', 1);
                }
                break;
            case 4:
                $list = array ('Protein', 'Iron', 'Carbos', 'HP up', 'Calcium');
                echo addItems ($list[rand(0, sizeof($list)-1)], 2);
                break;
            case 5:
                $list = array ('Duskstone', 'Firestone', 'Leafstone', 'Moonstone', 'Ovalstone', 'Shinystone', 'Sunstone', 'Thunderstone', 'Waterstone', 'Dawnstone', 'Ice Stone');
                echo addItems ($list[rand(0, sizeof($list)-1)], 1);
                break;
            case 6:
                $rand = rand(1, 11);
                if ($rand <= 8) {
                    echo addSilvers(1000, 8000);
                } else if ($rand <= 10) {
                    $arr = array('07', '87', '92');
                    echo addTM ('TM'.$arr[rand(0, 2)], 1);
                } else {
                    $premium = 86400 * 1;
                    if ($gebruiker['premiumaccount'] < time()) $premium += time();
                    else $premium += $gebruiker['premiumaccount'];

                    DB::exQuery("UPDATE `gebruikers` SET `premiumaccount`={$premium} WHERE `user_id`={$_SESSION['id']} LIMIT 1");
                    echo 'success | Parabéns, você ganhou <b>1 dia</b> de <img src="' . $static_url . '/images/icons/vip.gif" style="vertical-align: middle"/> no <b>Poké-Loot</b>!';
                }
                break;
            default:
                echo addSilvers(1000, 8000);
        }
    }
}