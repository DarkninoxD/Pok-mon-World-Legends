<?php
$duel = $duel_sql->fetch_assoc();
if (empty($duel)) header('location: ./invite');

if (isset($_POST['accept'])) {
  if ($gebruiker['silver'] < $duel['bedrag']) {
    echo '<div class="red"> '.$txt['alert_not_enough_silver'].'</div>';
    DB::exQuery("UPDATE duel SET status='no_money' WHERE id='".$duel['id']."'");
  }
  else if (DB::exQuery("SELECT id FROM pokemon_speler WHERE leven>'0' AND user_id='".$_SESSION['id']."' AND opzak='ja'")->num_rows <= 0) {
    echo '<div class="red"> '.$txt['alert_all_pokemon_ko'].'</div>';
    DB::exQuery("UPDATE duel SET status='all_dead' WHERE id='".$duel['id']."'");
  }
  else{
    //Include Duel Functions
    include_once('duel-start.php');
    include_once('duel.inc.php');
    $time = strtotime(date("Y-m-d H:i:s"));
    DB::exQuery("UPDATE `duel` SET `status`='accept', `laatste_beurt_tijd`='".$time."' WHERE id='".$duel['id']."'");
    // Start Duel
    $chance = rand(1,2);
    $background = "duelo-".$chance."";    
    $_SESSION['background'] = $background;   
    DB::exQuery("UPDATE `gebruikers` SET `background`='$background' WHERE `user_id`='".$_SESSION['id']."'");   
    start_duel($duel['id'],'tegenstander');
    $_SESSION['duel']['duel_id'] = $duel['id'];
    $_SESSION['duel']['begin_zien'] = true;
    start_attack($duel);
    // sleep(1);
    header("location: ./attack/duel/duel-attack");
  }
}
else if (isset($_POST['cancel'])) {
  echo '<div class="blue"> '.$txt['success_cancelled'].'</div>';
  DB::exQuery("DELETE FROM `duel` WHERE id='".$duel['id']."'");
}
else{
  if ($duel['status'] == "expired") {
    echo '<div class="red"> '.$duel['uitdager'].' '.$txt['alert_too_late'].'</div>';
    DB::exQuery("DELETE FROM `duel` WHERE id='".$duel['id']."'");
  }
  else{
    if ($duel['bedrag'] > 0) $text = ''.$txt['dueltext_1'].' <img src="'.$static_url.'/images/icons/silver.png" title="Silver"> '.$duel['bedrag'].'';
	echo '<div class="blue"><img src="'.$static_url.'/images/icons/duel.png" style="vertical-align: bottom;"> '.$duel['uitdager'].' '.$txt['dueltext_2'].'
      '.$text.' <img src="'.$static_url.'/images/icons/duel.png" style="vertical-align: bottom;"></div>';

    // include("app/classes/Utils.php");

    $gb = new Gebruikers();
    $ps = new PokemonSpeler();

    $infos = $gb->getInfos($duel['uitdager'], '`user_id`, `exibepokes`')->fetch_assoc();
?> 

<div class="row">
    <div style="width: 27%;" class="col">
      <div id="npc-section" style="background: url('public/images/layout/starProfile.png') no-repeat #34465f; background-position: center;height: 185px; border-top-right-radius: 0; border-bottom-right-radius: 0; border-right: 1px solid #577599;">
        <div id="npc-image" style="background: url('public/images/characters/<?=$gebruiker['character']?>/npc.png') center center no-repeat; background-size: 100% 100%; height: 180px;width: 160px;margin-top: 5px;"></div>
      </div>
    </div>
    
		<div style="width: 46%;" class="col">
				<div id="npc-section" class="row" style="height: 185px;border-radius: 0;border-right: unset;">
          <div class="col" style="margin: 18px 0px 0px 13px; width: 146px;">
            <h3 class="title" style="padding: 5px;background: url(public/images/layout/line.png) no-repeat;background-position: bottom; background-size: 70%; font-size: 21px; margin: 5px 0 0; font-weight: bold; text-transform: uppercase; color: #9eadcd;"><?=$gebruiker['username']?></h3>
            <div class="duel-pokemon" style="float: right">
								<?php if ($gebruiker['in_hand'] > 0) { 
										$pkm_count = 6;
										while($pokemon = $pokemon_sql->fetch_assoc()) {
											$pokemon = pokemonei($pokemon, $txt);
											$popup = pokemon_popup($pokemon, $txt);
											$pokemon['naam'] = pokemon_naam($pokemon['naam'], $pokemon['roepnaam'], $pokemon['icon']);

											echo '<div class="icon"><div style="background-image: url(\'' . $static_url . '/' . $pokemon['animatie'] . '\');" class="tip_bottom-left' . ($pokemon['leven'] < 1 ? ' dead' : '') . '" title="' . $popup . '"></div></div>';
				
											$pkm_count--;
										}

										for ($i = 0; $i < $pkm_count; $i++) echo '<div class="icon"></div>';
										$pokemon_sql->data_seek(0);
									} else { 
										for ($i = 0; $i < 6; $i++) echo '<div class="icon"></div>';
									}
									?>
								</div>
          </div>   
          <div class="col" style="width: 121px;">
			      <img src="public/images/icons/avatar/vs.png" style="width: 119%;margin-top: 28px;">		
          </div>
          <div class="col" style="margin: 18px 0px 0px 13px;width: 146px;">
              <h3 class="title" style="padding: 5px;background: url(public/images/layout/line.png) no-repeat;background-position: bottom; background-size: 70%; font-size: 21px; margin: 5px 0 0; font-weight: bold; text-transform: uppercase; color: #9eadcd;"><?=$duel['uitdager']?></h3>
              <div class="duel-pokemon" style="float: right">
              <?php if ($infos['exibepokes'] == 'sim') { 
                        $pkm_count = 6;
                        $pinfos = $ps->getInfos($infos['user_id']);

                        while($pokemon = $pinfos->fetch_assoc()) {
                            $pokemon = pokemonei($pokemon, $txt);
                            $popup = pokemon_popup($pokemon, $txt);
                            $pokemon['naam'] = pokemon_naam($pokemon['naam'], $pokemon['roepnaam'], $pokemon['icon']);

                            echo '<div class="icon"><div style="background-image: url(\'' . $static_url . '/' . $pokemon['animatie'] . '\');" class="tip_bottom-left' . ($pokemon['leven'] < 1 ? ' dead' : '') . '" title="' . $popup . '"></div></div>';

                            $pkm_count--;
                        }

                        for ($i = 0; $i < $pkm_count; $i++) echo '<div class="icon"></div>';
                    } else { 
                        for ($i = 0; $i < 6; $i++) echo '<div class="icon"></div>';
                    }
                    ?>
              </div>
          </div>  
        </div>     	
    </div>
    
    <div style="width: 27%;" class="col">
				<div id="npc-section" style="background: url('public/images/layout/starProfile.png') no-repeat #34465f;background-position: center;height: 185px;border-top-left-radius: 0;border-bottom-left-radius: 0;border-left: 1px solid #577599;">
					<div id="npc-image" style="background: url('public/images/characters/<?=$duel['u_character']?>/npc.png') center center no-repeat;background-size: 100% 100%;height: 180px;width: 160px;margin-top: 5px;"></div>
				</div>
		</div>
</div>

<div class="box-content" style="padding: 6px">

<?php
  echo  '<form method="post">
        <input type="submit" name="accept" value="'.$txt['accept'].'" class="button"> | <input type="submit" name="cancel" value="'.$txt['cancel'].'" class="button">
      </form></div>';
  }
}
?>