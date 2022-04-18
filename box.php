<?php
	error_reporting(0);
	#Script laden zodat je nooit pagina buiten de index om kan laden
	include("app/includes/resources/security.php");

    $inhuissql = DB::exQuery("SELECT COUNT(`id`) AS `aantal` FROM `pokemon_speler` WHERE `user_id`='".$_SESSION['id']."' AND (opzak = 'nee' OR opzak = 'tra')")->fetch_assoc();
	$inhuis = $inhuissql['aantal'];

    if ($gebruiker['huis'] == "doos") {
        $huiss = $txt['box'];
        $linkk = "house1.png";
        $over  = 2-$inhuis;
    } else if ($gebruiker['huis'] == "shuis") {
        $huiss = $txt['little_house'];
        $linkk = "house2.gif";
        $over  = 20-$inhuis;
    } else if ($gebruiker['huis'] == "nhuis") {
        $huiss = $txt['normal_house'];
        $linkk = "house3.gif";
        $over  = 100-$inhuis;
    } else if (($gebruiker['huis'] == "villa") OR ($gebruiker['huis'] == "Villa")) {
        $huiss = $txt['big_house'];
        $linkk = "house4.gif";
        $over  = 2500-$inhuis;
    }

    $huis = DB::exQuery("SELECT `ruimte` FROM `huizen` WHERE `afkorting`='".$gebruiker['huis']."'")->fetch_assoc();

    $pokes100 = DB::exQuery("SELECT id FROM pokemon_speler WHERE user_id = '".$_SESSION['id']."' AND level='100'")->num_rows;
	$top33 = DB::exQuery("SELECT id FROM pokemon_speler WHERE user_id = '".$_SESSION['id']."' AND top3='3'")->num_rows;
	$top22 = DB::exQuery("SELECT id FROM pokemon_speler WHERE user_id = '".$_SESSION['id']."' AND top3='2'")->num_rows;
	$top11 = DB::exQuery("SELECT id FROM pokemon_speler WHERE user_id = '".$_SESSION['id']."' AND top3='1'")->num_rows;

	$top3 = '<img src=\'' . $static_url . '/images/icons/medal3.png\' title=\'Tops 3 Pokémons\' /> ' . $top33 . ' | ';
	$top3 .= '<img src=\'' . $static_url . '/images/icons/medal2.png\' title=\'Tops 2 Pokémons\' /> ' . $top22 . ' | ';
	$top3 .= '<img src=\'' . $static_url . '/images/icons/medal1.png\' title=\'Tops 1 Pokémons\' /> ' . $top11 . ' ';

    if ($gebruiker['in_hand'] == 0) {
        echo '<div class="red">É perigoso ficar andando por aí sem nenhum pokémon com você, pegue pelo menos um pokémon e continue sua aventura.</div>';
    }


 if (empty($_GET['box'])) $_GET['box'] = 1;
 if ($_GET['box'] <= 0) $_GET['box'] = 1;


$upgradeposition = DB::exQuery("SELECT id FROM `pokemon_speler` WHERE `user_id`='".$_SESSION['id']."' AND `opzak`='nee' AND `opzak_nummer`='' ORDER BY `id` ASC");

for($i=1;$positupdt=$upgradeposition->fetch_assoc();++$i) {
    for ($x = 1; $x <= $huis['ruimte']; $x++) {
        $verifyy = DB::exQuery("SELECT id from `pokemon_speler` WHERE `opzak_nummer`='".$x."' AND `user_id`='".$_SESSION['id']."' AND `opzak`='nee'")->num_rows;

        if ($verifyy == 0) {
            DB::exQuery("UPDATE `pokemon_speler` SET `opzak_nummer`='".$x."' WHERE `id`='".$positupdt['id']."' AND `user_id`='".$_SESSION['id']."' AND `opzak`='nee'");
            break;
        }
    }
}

echo addNPCBox(21, 'Box Pokémon', 'Seja bem vindo a sua BOX, Treinador!<br>
Aqui você poderá guardar seus Pokémons, organizá-los, vendê-los e utilizar de muitas outras funções... Seja um Treinador organizado, pois tenho a certeza que será melhor para você!');

if ($over <= 5 && strtolower($gebruiker['huis']) != "villa") {
?>
<div class="blue"><a href="./house-seller">Caso sua BOX fique cheia, compre AQUI uma CASA maior!</a></div>
<?php
}
?>
<link rel="stylesheet" type="text/css" href="<?= $static_url?>/stylesheets/box.css?v1.3" />
<script type="text/javascript" src="<?= $static_url?>/javascripts/box.js?v1.8"></script>

<div class="row">

<div class="col box-content" style="height: 508px;border-top-right-radius: 0; border-bottom-right-radius: 0; border-right: 1px solid #577599;" id="containmentHuiss">
   <p>
      <?php
         echo '<div style="color: white; vertical-align: middle;padding-top: 40px; padding-left: 4px;padding-left:4px;">
            <center>'.$txt['title_text_1'].' '.$huiss.', '.$txt['title_text_2'].' '.$huis['ruimte'].' Pokémons.<br><br>
            Pokémons Nv.100: '.$pokes100.' <br><br>
            Pokémons TOP 3: <br> '.$top3.'
            </center>
            </div><br>';
         
            ?> <br>        
   </p>
   <br/>
   <img src="<?= $static_url?>/images/<?php echo $linkk; ?>" />
   <br/>
   <p style="font-weight: bold;">
      <?php echo $over.' '.$txt['places_over']; ?>        
   </p>
</div>

<div class="col box-content" style="width:83%; border-top-left-radius: 0; border-bottom-left-radius: 0;">
   <div>
      <div style="width: 65%">
         <div id="containmentSortable" style="display: none;">
            <div id="hand">
               <h3 class="title" style="margin-bottom: 7px">Time atual: </h3>
               <ul style="margin: 0 auto" class="connectedSortable" id="ul_hand">
                  <?php
                     while($pokemon = $pokemon_sql->fetch_assoc()) {
                      $pokemon = pokemonei($pokemon, $txt);
                      $pokemon['naam'] = pokemon_naam($pokemon['naam'],$pokemon['roepnaam']);
                     
                      if ($pokemon['ei'] != 1) {
                          if ($pokemon['shiny'] == 1) $typp = 'shiny';
                      else $typp = 'pokemon';
                          $imgg = ''.$static_url.'/images/'.$typp.'/icon/'.$pokemon['wild_id'].'';
                      } else {
                          $imgg = ''.$static_url.'/images/icons/egg';
                      }
                     ?>
                  <li class="ui-state-default element" id="pkm_<?php echo $pokemon['id']; ?>">
                     <a href="#" class="noanimate" style="cursor: move;" onclick="return false;">
                     <?php
                        if (isset($pokemon['item']))
                            echo '<img src="'.$static_url.'/images/items/helditem.png" style="position: absolute;" title="Equipado com '.$pokemon['item'].'">';
                        ?>
                     <img src="<?php echo $imgg; ?>.gif" width="32" height="32"/>
                     </a>
                     <div class="options">
                        <a href="#" class="ui-icon ui-icon-search" onclick="moreInfo(<?php echo $pokemon['id']; ?>);
                           return false;" style="float: left;">Mais informações</a>
                        <?php if ($_SESSION['share_acc'] == 0) { ?>
                        <a href="ajax.php?act=box&option=equip&id=<?php echo $pokemon['id']; ?>"  class="ui-icon ui-icon-circlesmall-plus colorbox-equip"  style="float: left;">Equipar</a>
                        <div class="box-menu">
                           <a href="<?php echo $pokemon['id']; ?>" class="ui-icon ui-icon-triangle-1-se" onclick="return false;" style="float: left;">Mais opções</a>
                           <ul class="slot-options">
                              <li>
                                 <a href="./pokemon-profile&id=<?php echo $pokemon['id']; ?>" class="noanimate" target="_blank">
                                    <span class="ui-icon ui-icon-info"></span>
                                    <div style="margin-right: 5px;">Perfil</div>
                                 </a>
                              </li>
                              <li>
                                 <a href="ajax.php?act=sell-box&id=<?php echo $pokemon['id']; ?>&pokebox" class="colorbox-sell noanimate">
                                    <span class="ui-icon ui-icon-cart"></span>
                                    <div style="margin-right: 5px;">Vender</div>
                                 </a>
                              </li>
                              <li><a href="ajax.php?act=release-box&id=<?php echo $pokemon['id']; ?>" class="colorbox-release noanimate"><span class="ui-icon ui-icon-trash"></span>Soltar</a></li>
                              <li><a href="#" onclick="return false;" class="noanimate disabled"><span class="ui-icon ui-icon-refresh"></span>Transferir</a></li>
                           </ul>
                        </div>
                        <?php } ?>
                     </div>
                  </li>
                  <?php } ?>
               </ul>
            </div>
            <?php
               if ($_GET['box'] == 1) $anter = 1;
               else $anter = $_GET['box'] - 1;
               
               ?>
            <div>
               <form>
                  <input type="hidden" name="page" value="box"/>
                  <a href="./box&box=<?= $anter?>" class="noanimate"><img src="<?= $static_url?>/images/icons/arrow_left_25.png" style="vertical-align: unset;"/></a>
                  <select class="text_select" name="box" onchange="window.location = './box&box='+$(this).val()" style="padding-left: 45px;">
                  <?php
                     $calc1 = $huis['ruimte'] / 50;
                     if ($calc1 < 1) $calc1 = 1;
                     for ($i = 1; $i <= $calc1; $i++) {
                     if ($_GET['box'] == $i) $selected = 'selected';
                     else $selected = '';
                     $verifybox = DB::exQuery("SELECT nome FROM `boxes` WHERE `user_id`='".$_SESSION['id']."' AND `box_id`='".$i."' limit 1");
                     $nomebox = '';
                           	if ($verifybox->num_rows > 0) {
                           	$verifyboxx = $verifybox->fetch_assoc();
                           	$nomebox = '('.$verifyboxx['nome'].')';
                           	}
                       		echo '<option value="'.$i.'" '.$selected.'>Box '.$i.' '.$nomebox.'</option>';
                     }
                     
                     ?>
                  </select>
                  <?php
                     $calcbox = $i - 1;
                     if ($calcbox > $_GET['box']) $prox = $_GET['box'] + 1;
                     else $prox = $_GET['box'];
                     
                     if ($_GET['box'] > $calcbox) exit(header("Location: ./box&box=1"));
                     
                     ?>
                  <a href="./box&box=<?= $prox?>" class="noanimate"><img src="<?= $static_url?>/images/icons/arrow_right_25.png" style="vertical-align: unset;"/></a>
                  <button class="b-button b-white b-small" type="button" onclick="configBox(<?=$_GET['box']?>)"><img src="<?= $static_url?>/images/icons/config.gif"/> <span style="bottom: 0px;">Configurações da box</span></button>
               </form>
            </div>
            <?php
               $verifybox = DB::exQuery("SELECT fundo FROM `boxes` WHERE `user_id`='".$_SESSION['id']."' AND `box_id`='".$_GET['box']."' limit 1");
               if ($verifybox->num_rows > 0) {
               $verifyboxx = $verifybox->fetch_assoc();
               $classbox = $verifyboxx['fundo'];
               }
               ?>
            <div id="pokemon_box" class="<?php echo $classbox; ?>">
               <?php
                  $max = 50;
                  $pagina = (($_GET['box'] * $max) - $max) + 1;
                  if ($pagina == 0) $pagina = 1;
                  $max2 = ($pagina+$max);
                  for ($x = $pagina; $x < $max2; $x++) {
                  
                  $search2 = "SELECT `pokemon_speler`.*,`pokemon_wild`.`naam`,`pokemon_wild`.`type1`,`pokemon_wild`.`type2` FROM `pokemon_speler` INNER JOIN `pokemon_wild` ON `pokemon_speler`.`wild_id`=`pokemon_wild`.`wild_id` WHERE `pokemon_speler`.`user_id`='" . $_SESSION['id'] . "' AND `pokemon_speler`.`opzak`='nee' AND `pokemon_speler`.`opzak_nummer`=".$x."";
                      $poke = DB::exQuery($search2);
                  $cpoke = $poke->num_rows;
                  
                  if ($cpoke == 0) { echo  '<div class="connectedSortable slot" id="slot_'.$x.'"></div>';
                  } else if ($cpoke == 1) {
                  $pokemon = $poke->fetch_assoc();
                  $pokemon = pokemonei($pokemon, $txt);
                  $popup = pokemon_popup($pokemon, $txt);
                  $pokemon['naam'] = pokemon_naam($pokemon['naam'],$pokemon['roepnaam'],$pokemon['icon']);
                  
                  if ($pokemon['ei'] != 1) {
                    				if ($pokemon['shiny'] == 1) $typp = 'shiny';
                    				else $typp = 'pokemon';
                   			 		$imgg = ''.$static_url.'/images/'.$typp.'/icon/'.$pokemon['wild_id'].'';
                    				} else {
                    				$imgg = ''.$static_url.'/images/icons/egg';
                    				}
                    				$shinnytxt = '';
                    				if ($pokemon['shiny'] == 1) $shinnytxt = 'Shiny';
                  
                  echo '<div class="connectedSortable slot" id="slot_'.$x.'">';
                  ?>
               <li class="ui-state-default element" id="pkm_<?php echo $pokemon['id']; ?>">
                  <?php /* <a href="#" class="tip_bottom-left" style="cursor: move;" title="<?php echo $popup; ?>" onclick="return false;">  */ ?>
                  <a href="#" style="cursor: move;" onclick="return false;" class="noanimate">
                  <?php
                     if (isset($pokemon['item']))
                         echo '<img src="'.$static_url.'/images/items/helditem.png" style="position: absolute;" title="Equipado com '.$pokemon['item'].'">';
                     ?>
                  <img src="<?php echo $imgg; ?>.gif" width="32" height="32"/>
                  </a>
                  <div class="options">
                     <a href="#" class="ui-icon ui-icon-search" onclick="moreInfo(<?php echo $pokemon['id']; ?>);
                        return false;" style="float: left;">Mais informações</a>
                     <?php if ($_SESSION['share_acc'] == 0) { ?>
                     <a href="ajax.php?act=box&option=equip&id=<?php echo $pokemon['id']; ?>" class="ui-icon ui-icon-circlesmall-plus colorbox-equip"  style="float: left;">Equipar</a>
                     <div class="box-menu">
                        <a href="<?php echo $pokemon['id']; ?>" class="ui-icon ui-icon-triangle-1-se" onclick="return false;" style="float: left;">Mais opções</a>
                        <ul class="slot-options">
                           <li>
                              <a href="./pokemon-profile&id=<?php echo $pokemon['id']; ?>" target="_blank" class="noanimate">
                                 <span class="ui-icon ui-icon-info"></span>
                                 <div style="margin-right: 5px;">Perfil</div>
                              </a>
                           </li>
                           <li>
                              <a href="ajax.php?act=sell-box&id=<?php echo $pokemon['id']; ?>&pokebox" class="colorbox-sell noanimate">
                                 <span class="ui-icon ui-icon-cart"></span>
                                 <div style="margin-right: 5px;">Vender</div>
                              </a>
                           </li>
                           <li><a href="ajax.php?act=release-box&id=<?php echo $pokemon['id']; ?>" class="colorbox-release noanimate"><span class="ui-icon ui-icon-trash"></span>Soltar</a></li>
                           <li><a href="ajax.php?act=transfer-box&id=<?php echo $pokemon['id']; ?>&box=<?php echo $_GET['box']; ?>" class="colorbox-release noanimate"><span class="ui-icon ui-icon-refresh"></span>Transferir</a></li>
                        </ul>
                     </div>
                     <?php } ?>
                  </div>
               </li>
               <?php
                  echo '</div>';
                  
                  
                  
                  } else if ($cpoke > 1) {
                  $pokemon = $poke->fetch_assoc();
                  for ($continha = $pagina; $continha <= $huis['ruimte']; $continha++) {
                  $contg =  DB::exQuery("SELECT id FROM `pokemon_speler` WHERE `user_id`='".$_SESSION['id']."' AND `opzak`='nee' AND `opzak_nummer` = ".$continha."")->num_rows;
                  if ($contg == 0) { DB::exQuery("UPDATE `pokemon_speler` SET `opzak_nummer`='".$continha."' WHERE `id`='".$pokemon['id']."' AND `user_id`='".$_SESSION['id']."' AND `opzak`='nee'");
                  break; }
                  
                  }
                  
                  } else {
                  echo "ERROR 412 - INFORME OS ADMINISTRADORES.";
                  }
                  
                  
                  
                  
                  }
                  
                   ?>
            </div>
         </div>
      </div>
   </div>
</div>
</div>