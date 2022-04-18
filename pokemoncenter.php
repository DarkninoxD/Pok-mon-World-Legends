<?php
#Script laden zodat je nooit pagina buiten de index om kan laden
require_once("app/includes/resources/security.php");

#Als je geen pokemon bij je hebt, terug naar index.
if ($gebruiker['in_hand'] == 0)	exit(header('LOCATION: ./'));

#Als er op de heal knop gedrukt word
if (isset($_POST['heal']) && is_array($_POST['pokemon']) && count($_POST['pokemon']) != 0) {
    $i = 0;
	$count_time = 0;
	foreach($_POST['pokemon'] as $key=>$value) {
		if (!is_numeric($value)) {
			$message = '<div class="red">Não foi possível executar está ação!</div>';
			break;
		}
		$pokeInfo = DB::exQuery("SELECT `user_id`,`leven`,`levenmax`,`effect` FROM `pokemon_speler` WHERE `id`=" . $value . " LIMIT 1")->fetch_assoc();
		if (empty($message) && $pokeInfo['user_id'] != $gebruiker['user_id']) {
			$message = '<div class="red">Os administradores foram comunicados sobre sua ação!</div>';
			break;
		}
		if (empty($message) && empty($pokeInfo['effect']) && $pokeInfo['leven'] >= $pokeInfo['levenmax']) {
			$message = '<div class="red">Você não pode curar um pokémon saudavel!</div>';
			break;
		}
		if (empty($message)) {
			if ($gebruiker['premiumaccount'] > time())	$count_time = 1;
			else	$count_time += 20;
			if ($gebruiker['admin'] > 0) $count_time = 1;
            DB::exQuery("UPDATE `pokemon_speler` SET `leven`=`levenmax`,`effect`='' WHERE `id`='" . $value . "' LIMIT 1");
            $i++;
		}
		
    }

    $quests->setStatus('heal', $_SESSION['id'], $i);
    
	if (empty($message)) {
		DB::exQuery("UPDATE `gebruikers` SET `pokecentertijdbegin`= NOW(), `pokecentertijd`='".$count_time."' WHERE `user_id`='" . $gebruiker['user_id'] . "'");
	    header("LOCATION: ./pokemoncenter");
	}
}
$title = 'Centro Pokémon';
$text = 'Seja bem vindo ao Centro Pokémon, eu me chamo <b>Enfermeira Joy</b>. Estou aqui para ajudar na recuperação de pokémons que estejam feridos e doentes. Caso tenha algum pokémon necessitando de tratamento marque-o para que eu possa trata-lo.<br /><br /><br />&mdash; ';
if ($gebruiker['premiumaccount'] < time())	$text .= $txt['title_text_premium'].'<br>&mdash; Seja VIP clicando <a href="./gold-market">AQUI</a> e diminua o tempo de espera para 1s.';
else	$text .= $txt['title_text_normal']; 
echo addNPCBox(33, $title, $text);
?>

<?php if (!empty($message)) echo $message; ?>
<link rel="stylesheet" type="text/css" href="<?= $static_url?>/stylesheets/box.css?v1.3" />
<script type="text/javascript" src="<?= $static_url?>/javascripts/pokecenter.js"></script>  
<style>
    #ul_hand > li, .slot, .slot > li {
        width: 50px;
        height: 50px;
        margin: 1px 2px 0px 2px;
    }
    
    #pokemon_box>.slot:first-child {
        margin: 1px 2px 0px -2px;
    }
    
    #ul_hand > li:nth-child(7) {
        display: block
    }

    .disabled-item a {
        cursor: not-allowed!important;
    }
</style>

<script>
    function heal () { $('#heal').submit(); }
    wlSound('center', <?=$gebruiker['volume']?>, true);
</script>

<div id="containmentSortable" style="display: block; width: 100%; float: none; height: 407px">
    <div style="border-radius: 4px;background: url(<?=$static_url?>/images/layout/joy.png); background-size: 100%; background-repeat: no-repeat; width: 100%; height: 300px; float: left">
        <div id="pokemon_box" style="margin-top: 40px;background: none;margin-left: 269px;width: 330px;height: 45px;float: none;border-image: none;">
            <?php
            
                for ($x = 1; $x < 7; $x++) {
                    echo  '<div class="connectedSortable slot" id="slot_'.$x.'"></div>';
                }
                    
            ?>
        </div>
        <button type="button" style="box-shadow:none;width: 85px;height: 33px;margin-left: 65px;border: none;background: transparent;margin-top: 122px;" onclick="heal()"></button>
    </div>
    <div class="box-content">
        <h3 class="title">Meus Pokémons</h3>
        <center>
            <div id="hand">
                <ul class="connectedSortable" id="ul_hand" style="background: url('<?=$static_url?>/images/layout/slots.png') no-repeat; width: 345px; background-size: 324px; height: 43px; margin: 7px; float: none; border-image: none; background-position: 11px;">

                <?php
                $checkbox = array();

                while($pokemon = $pokemon_sql->fetch_assoc()) {
                    $pokemon = pokemonei($pokemon, $txt);
                    $pokemon['naam'] = pokemon_naam($pokemon['naam'],$pokemon['roepnaam'],$pokemon['icon']);

                    if ($pokemon['ei'] != 1) {
                        if ($pokemon['shiny'] == 1) $typp = 'shiny';
                    else $typp = 'pokemon';
                        $imgg = ''.$static_url.'/images/'.$typp.'/icon/'.$pokemon['wild_id'].'';
                    } else {
                        $imgg = ''.$static_url.'/images/icons/egg';
                    }
                    
                    if ($pokemonei['ei'] == 1) $disabled = " disabled-item";
                    if ($pokemon['leven'] == $pokemon['levenmax']) $disabled = " disabled-item";
                    if ($pokemon['leven'] < $pokemon['levenmax'] || !empty($pokemon['effect'])) $disabled = "";

                    $arr = array('id' => $pokemon['id'], 'disabled' => $disabled);
                    array_push($checkbox, $arr);
                ?>
                        <li class="ui-state-default element<?=$disabled?>" id="pkm_<?php echo $pokemon['id']; ?>"> 
                            <a href="#" class="noanimate" title="<?php echo $shinnytxt; ?> <?php echo $pokemon['naam']; ?>" style="cursor: move;" onclick="return false;">
                                <img src="<?php echo $imgg; ?>.gif" width="32" height="32"/>
                            </a>
                            <div class="options" style="width: 20px; margin-top:10px;margin: 0 auto;">
                                <div style="cursor: pointer!important; -webkit-filter: invert(100%);" class="ui-icon ui-icon-search" onclick="moreInfo(<?= $pokemon['id']; ?>);">Mais informações</div>
                            </div>
                        </li>


                <?php } ?>
                
                </ul>
            </div>
        <form method="post" id="heal">
                <input type="hidden" name="heal" value="heal">
            <?php
                foreach($checkbox as $check) {
            ?>
                <input type="checkbox" style="display: none" <?= str_replace('-item', '', $check['disabled']); ?> name="pokemon[]" value="<?= $check['id']; ?>"/>
            <?php } ?>
        </form>
        </center>
    </div>
</div>