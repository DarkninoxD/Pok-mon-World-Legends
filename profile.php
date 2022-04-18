<?php

include('app/includes/resources/security.php');

if (isset($_GET['player'])) {

$profiel = DB::exQuery("SELECT `r`.`karma`,`g`.`ultimo_login`,`g`.`antiguidade`,`g`.`user_id`,`g`.`username`,`r`.`datum`,`g`.`clan`,`g`.`rang`,`g`.`rang_temp`,`r`.`email`,`r`.`ip_aangemeld`,`r`.`ip_ingelogd`,`g`.`silver`,`r`.`gold`,`g`.`premiumaccount`,`g`.`admin`,`g`.`wereld`,`g`.`online`,`g`.`character`,`g`.`character_num`,`g`.`profiel`,`g`.`teamzien`,`g`.`badgeszien`,`g`.`rank`,`g`.`wereld`,`g`.`aantalpokemon`,`g`.`badges`,`g`.`gewonnen`,`g`.`verloren`,COUNT(DISTINCT `g`.`user_id`) AS `check`,`gi`.`Badge case` FROM `gebruikers` AS `g` INNER JOIN `rekeningen` AS `r` ON `g`.`acc_id`=`r`.`acc_id` INNER JOIN `gebruikers_item` AS `gi` ON `g`.`user_id`=`gi`.`user_id` WHERE `g`.`username`='" .$_GET['player']."' AND `g`.`banned`!='Y' GROUP BY `g`.`user_id` LIMIT 1")->fetch_assoc();

if ($profiel['check'] != 1)	exit(header("LOCATION: ./"));
else {
	$plaatje = $static_url . "/images/icons/status_offline.png";
	$online  = $txt['offline'];

	$voortgang = $profiel['rang'];
	
	if ($voortgang == 0) {
    $voortgangplaats = "-";   
    } else {
    $voortgangplaats = $voortgang."º";
    }
    
	if ($voortgang == '1') {
	    $medaille = "<img src='".$static_url."/images/icons/plaatsnummereen.png'>";
	    $voortgangplaats = $voortgang."º";
	  }
	  else if ($voortgang == '2')
	    $medaille = "<img src='".$static_url."/images/icons/plaatsnummertwee.png'>";
	  else if ($voortgang == '3')
	    $medaille = "<img src='".$static_url."/images/icons/plaatsnummerdrie.png'>";
	  else if ($voortgang > '3' && $voortgang <= '10')
	    $medaille = "<img src='".$static_url."/images/icons/gold_medaille.png'>";
	  else if ($voortgang > '10' && $voortgang <= '30')
	    $medaille = "<img src='".$static_url."/images/icons/silver_medaille.png'>";
	  else if ($voortgang > '30' && $voortgang <= '50')
	    $medaille = "<img src='".$static_url."/images/icons/bronze_medaille.png'>";
	  else if ($profiel['admin'] >= 1) 
	    $voortgangplaats = "<b><font color='red'>Administrador</font></b>";

		$voortgang2 = $profiel['rang_temp'];
	
		 if ($voortgang == 0) {
			$voortgangplaats2 = "-";   
		} else {
			$voortgangplaats2 = $voortgang2."º";
		}
    
		if ($voortgang2 == '1') {
			$medaille2 = "<img src='".$static_url."/images/icons/plaatsnummereen.png'>";
			$voortgangplaats2 = $voortgang2."º";
		}
		else if ($voortgang2 == '2')
			$medaille2 = "<img src='".$static_url."/images/icons/plaatsnummertwee.png'>";
		else if ($voortgang2 == '3')
			$medaille2 = "<img src='".$static_url."/images/icons/plaatsnummerdrie.png'>";
		else if ($voortgang2 > '3' && $voortgang2 <= '10')
			$medaille2 = "<img src='".$static_url."/images/icons/gold_medaille.png'>";
		else if ($voortgang2 > '10' && $voortgang2 <= '30')
			$medaille2 = "<img src='".$static_url."/images/icons/silver_medaille.png'>";
		else if ($voortgang2 > '30' && $voortgang2 <= '50')
			$medaille2 = "<img src='".$static_url."/images/icons/bronze_medaille.png'>";
		else if ($profiel['admin'] >= 1) 
			$voortgangplaats2 = "<b><font color='red'>Administrador</font></b>";
	    

		//Tijd voor plaatje
		if (($profiel['online'] + 900) > time()) {
			$plaatje = $static_url . "/images/icons/status_online.png";
			$online  = $txt['online'];
		}
		//Rank naam laden
		$rank = rank($profiel['rank']);

		//Datum mooi maken
		$datum = explode("-", $profiel['datum']);
		$tijd = explode(" ", $datum[2]);
		$datum = $tijd[0]."/".$datum[1]."/".$datum[0];
		$date = $datum;

		$profile_silver = number_format(round($profiel['silver']),0,",",".");
		$profile_gold = number_format(round($profiel['gold']),0,",",".");
		
		if ($profiel['premiumaccount'] >= time()) $star  = '<img src="'.$static_url.'/images/icons/lidbetaald.png" width="16" height="16" border="0" alt="Premium" title="Premium" style="margin-bottom:-3px;">';
		
		$pokes100 = DB::exQuery("SELECT id FROM pokemon_speler WHERE user_id = '".$profiel['user_id']."' AND level='100'")->num_rows;
		$top33 = DB::exQuery("SELECT id FROM pokemon_speler WHERE user_id = '".$profiel['user_id']."' AND top3='3'")->num_rows;
		$top22 = DB::exQuery("SELECT id FROM pokemon_speler WHERE user_id = '".$profiel['user_id']."' AND top3='2'")->num_rows;
		$top11 = DB::exQuery("SELECT id FROM pokemon_speler WHERE user_id = '".$profiel['user_id']."' AND top3='1'")->num_rows;

		$top3 = '<img src=\'' . $static_url . '/images/icons/medal3.png\' title=\'Tops 3 Pokémons\' /> ' . $top33 . ' | ';
		$top3 .= '<img src=\'' . $static_url . '/images/icons/medal2.png\' title=\'Tops 2 Pokémons\' /> ' . $top22 . ' | ';
		$top3 .= '<img src=\'' . $static_url . '/images/icons/medal1.png\' title=\'Tops 1 Pokémons\' /> ' . $top11 . ' ';
		
		$inhuissql = DB::exQuery("SELECT COUNT(`id`) AS `aantal` FROM `pokemon_speler` WHERE `user_id`='".$profiel['user_id']."' AND (opzak = 'nee' OR opzak = 'tra')")->fetch_assoc();
	    $inhuis = $inhuissql['aantal'];
		
        $profile_clan = '';
        $profiel['clan'] = $clan->getUserClan($profiel['user_id']);

        if (!empty($profiel['clan'])) {
            $name = $clan->get($profiel['clan'])['sigla'];
            $profile_clan = '(<a href="./clans&action=profile&id='.$profiel['clan'].'">'.$name.'</a>)';
        }
?>

<style>
	.u-infos b {
		color: #98a7c7;
	}

	.u-infos {
		border-collapse: separate!important;
		border-spacing: 20px 0!important;
	}

	.u-infos img {
		vertical-align: middle;
	}
</style>

<table width="100%" border="0" cellpadding="0" cellspacing="0" class="box-content" style="padding: 10px; box-shadow: 0 0 15px #0e0d0d66; border-radius: 4px; margin: 10px 0;">
	<tr>
		<td class="box-content" width="200" valign="top" align="center">
			<h3 class="title" style="font-size: 17px; margin-top: -37px; margin-bottom: 18px">Perfil - <?=$profiel['username']?> <?=$profile_clan?></h3>
			<div style="background: url('<?=$static_url?>/images/layout/starProfile.png') no-repeat #34465f; background-position: center;height: 185px; border-top-right-radius: 0; border-bottom-right-radius: 0; border-right: 1px solid #577599;">
				<div style="background: url('<?=$static_url?>/images/characters/<?=$profiel['character']?>/npc.png') center center no-repeat; background-size: 100% 100%; height: 180px;width: 160px;margin-top: 5px;"></div>
			</div>
		</td>
		<td width="500" valign="top">
		<div>
		<table class="no-stripped bordered general u-infos" width="100%" style="margin: 10px">
	

	<?php if ($gebruiker['admin'] > 0) { ?><tr>
		<td height="20"><b>ID:</b> <?=$profiel['user_id'];?></td>
		<td height="20"><b> <?=$txt['email'];?></b><?=$profiel['email'];?></td>
	</tr>
	<tr>
		<td height="20"><b><?=$txt['world'];?></b> <?=$profiel['wereld'];?></td>
		<td height="20"><b><?=$txt['silver'];?></b> <img src="<?=$static_url;?>/images/icons/silver.png" title="Silver" style="margin-bottom:-3px;" /> <?=$profile_silver;?></td>
	</tr>
	<tr>
		<td height="20"><b><?=$txt['gold'];?></b><img src="<?=$static_url;?>/images/icons/gold.png" title="Gold" style="margin-bottom:-3px;" /> <?=$profile_gold;?></td>
	</tr>
	<tr>
		<td height="20"><b><?=$txt['ip_registered'];?></b> <a href="./admin/search-on-ip&amp;ip=<?=$profiel['ip_aangemeld'];?>&amp;which=aangemeld"><?=$profiel['ip_aangemeld'];?></a></td>
		<td><?=$txt['ip_login'];?> <a href="./admin/search-on-ip&amp;ip=<?=$profiel['ip_ingelogd'];?>&amp;which=ingelogd"><?=$profiel['ip_ingelogd'];?></a></td>
	</tr>
	<?php } ?> 
	<tr>
		<td height="20" style="width: 48%"><b><?=$txt['date_started'];?></b> <?=$date;?></td>
		<td><b><?=$txt['rank'];?></b> <?=$rank['ranknaam'];?></td>
	</tr>
	<tr>
		<td height="20"><b><?=$txt['rank_number'];?></b> <?=$medaille;?> <?=$voortgangplaats;?></td>
		<td><b>Antiguidade:</b> <?=$profiel['antiguidade'];?></td>
	</tr>
	<tr>
		<td height="20"><b><?=$txt['badges_number'];?></b> <?=$profiel['badges'];?></td>
		<td><b><?=$txt['pokemon'];?></b> <?=$inhuis;?></td>
	</tr>
	<tr>
		<td height="20"><b>Pokémons Level 100:</b> <?=$pokes100;?></td>
		<td><b>Pokémons TOP 3:</b> <?=$top3;?></td>
	</tr>
	<tr>
		<td height="20"><b><?=$txt['win'];?></b> <?=$profiel['gewonnen'];?></td>
		<td><b><?=$txt['lost'];?></b> <?=$profiel['verloren'];?></td>
	</tr>
	<tr>
		<td height="20"><b><?=$txt['status'];?></b> <img src="<?=$plaatje;?>" style="vertical-align:-4px;" /><?=$online;?></td>
		<td><b>Última Visita:</b> <?=$profiel['ultimo_login']?></td>
	</tr>
	<tr>
	</tr>
	<?php if ($_SESSION['id'] != $profiel['user_id'] && !in_array($profiel['user_id'], explode(',', $gebruiker['blocklist']))) { ?>
	<tr>
		<td height="20"><b><?=$txt['action'];?></b></td>
		<td>
			<a href="./friends-add&name=<?=$profiel['username'];?>" class="noanimate"><img src="<?=$static_url;?>/images/icons/groep.png" title="<?=$txt['add_buddy'];?>" class="icon-img"/></a>
			<a href="./inbox&action=send&player=<?=$profiel['username'];?>" class="noanimate"><img src="<?=$static_url;?>/images/icons/berichtsturen.png" title="<?=$txt['send_message'];?>" class="icon-img"/></a>
			<a href="./blocklist&player=<?=$profiel['username'];?>" class="noanimate"><img src="<?=$static_url;?>/images/icons/blokkeer.png" title="<?=$txt['block'];?>" class="icon-img"/></a>
			<?php if (($gebruiker['rank'] >= 4) && ($gebruiker['in_hand'] != 0) && ($profiel['rank'] >= 4)) echo '<a href="./attack/duel/invite&player='.$profiel['username'].'" class="noanimate"><img src="' . $static_url . '/images/icons/duel.png" title="'.$txt['duel'].'" class="icon-img"/></a>'; ?>
			<a href="./bank&player=<?=$profiel['username'];?>" class="noanimate"><img src="<?=$static_url;?>/images/icons/bank.png" title="Transferir Valores" class="icon-img"/></a>
		</td>
	</tr>
	<?php if ($gebruiker['admin'] >= 2) { ?>
	<tr>
		<td height="20"><b>ADM <?=$txt['action'];?></b></td>
		<td>
			<?php echo '<a href="./admin/change-profile&player='.$profiel['username'].'">
  	        <img src="'.$static_url.'/images/icons/user_edit.png" width="16" height="16" alt="'.$txt['edit_profile'].'" title="'.$txt['edit_profile'].'" /></a> - <a href="./admin/admins&player='.$profiel['username'].'"><img src="'.$static_url.'/images/icons/user_admin.png" width="16" height="16" alt="'.$txt['make_admin'].'" title="'.$txt['make_admin'].'" /></a> - <a href="./admin/give-egg&player='.$profiel['user_id'].'"><img src="'.$static_url.'/images/icons/egg2.gif" width="16" height="16" alt="'.$txt['give_egg'].'" title="'.$txt['give_egg'].'"></a> - <a href="./admin/give-pokemon&player='.$profiel['user_id'].'"><img src="'.$static_url.'/images/icons/pokeball.gif" width="14" height="14" alt="'.$txt['give_pokemon'].'" title="'.$txt['give_pokemon'].'"></a> - <a href="./admin/give-pack&player='.$profiel['username'].'"><img src="'.$static_url.'/images/icons/basket_put.png" alt="'.$txt['give_pack'].'" title="'.$txt['give_pack'].'"></a> - <a href="./admin/ban-char&player='.$profiel['username'].'"><img src="'.$static_url.'/images/icons/user_ban.png" alt="Bloquear Treinador" title="Bloquear Treinador"></a>  - <a href="./admin/ban-conta&player='.$profiel['email'].'"><img src="'.$static_url.'/images/icons/user_ban.png" alt="Bloquear Conta" title="Bloquear Conta"></a>'; ?>
  	        
		</td>
	</tr>
	<?php } } ?>
</table></div>
		</td>
	</tr>
</table>

<script src="<?=$static_url?>/javascripts/timeago/jquery.timeago.js"></script>

<div class="row" style="margin-bottom: 7px">
	
	<?php

	$p_title = 'Apresentação';
	if ($_SESSION['id'] == $profiel['user_id']) {
		$p_title .= ' <img src="'.$static_url.'/images/icons/edit.png" title=\'Editar apresentação\' style="vertical-align: bottom; cursor: pointer" onclick="editApresentacao()" id="apresentacao-button">';
	?>
		<script>
			var edit = false;
			function editApresentacao () {
				if (edit) {
					$('#apresentacao-button').attr('src', 'public/images/icons/edit.png').attr('data-original-title', 'Editar apresentação');
					edit = false;
				} else {
					$('#apresentacao-button').attr('src', 'public/images/icons/cancel.png').attr('data-original-title', 'Cancelar edição');
					edit = true;
				}

				$('#apresentacao').toggle();
				$('#edit').toggle();
			}
		</script>
	<?php
	}

	echo '<div class="box-content col" style="width: 55%; height: 150px; overflow-y: auto;border-top-right-radius: 0; border-bottom-right-radius: 0;"><table class="general" style="width: 100%"><thead><tr><th>'.$p_title.'</th></tr></thead>';
	echo '<tr><td colspan="3" style="text-align: center;height: 109px; padding: 3px">';
		if (!empty($profiel['profiel']))	echo '<div id="apresentacao">'.ubbcode($profiel['profiel']).'</div>';
		else	echo '<div id="apresentacao">'.$txt['no_profile_insert'].'</div>';

		if ($_SESSION['id'] == $profiel['user_id']) {
			if (isset($_POST['apresentacao'])) {
				$tekst = htmlspecialchars($_POST['apresentacao']);
				DB::exQuery("UPDATE `gebruikers` SET `profiel`='".$tekst."' WHERE `user_id`='".$_SESSION['id']."'");
				echo '<script>window.location = window.location.href</script>';
			}
			
			echo '<form method="post" id="edit" style="display: none"><script type="text/javascript">$(document).ready(function() { $(".colorbox").colorbox({width:"850", height:"1050", iframe:true}); $("#click").click(function() { $("#click").css({"background-color":"#f00", "color":"#fff", "cursor":"inherit"}).text("Open this window again and this message will still be here."); return false; }); }); </script><u><a href="codes.php?category=profile" class="colorbox cboxElement" title="Efeitos para o perfil"><b>Aqui</b></a></u> você pode ver algumas dicas para personalizar seu perfil!<br><textarea name="apresentacao" style="width: 100%; height: 101px">'.htmlspecialchars_decode($profiel['profiel']).'</textarea><input type="submit" name="editAprensetacao" value="Salvar Alterações"></form>';
		}
		
		echo '</td></tr></table></div>';

	?>

	<div class="box-content col" style="width: 45%; border-top-left-radius: 0;border-bottom-left-radius: 0; height: 150px">
		<table width="100%" class="general" align="center">	
			<thead><tr><th colspan="2">Amigos</th></tr></thead>
			<tbody>
				<?php
					include ('app/classes/Friends.php');
					$friends = new Friends ();
					$query = $friends->query($profiel['user_id'], 'AND `accept`="1"');
					$i = 0;

					$number = $query->num_rows;

					if (!is_numeric($_GET['friends'])) $subpage = 1; 
					else $subpage = $_GET['friends']; 
					
					$max = 4;
					$aantal_paginas = ceil($number / $max); 

					if ($aantal_paginas == 0) $aantal_paginas = 1;   
					$pagina = $subpage * $max - $max;

					$query = $friends->query($profiel['user_id'], 'AND `accept`="1"', $pagina, $max, 'ASC');

					while ($q = $query->fetch_assoc()) {
						$i++;
						if ($i == 1) {
							echo '<tr style="border-bottom: 1px solid #577599;">';
						}

						$id = '';
						if ($q['uid'] == $profiel['user_id']) {
							$id = $q['uid_2'];
						} else {
							$id = $q['uid'];
						}
						
						$infos = $friends->getInfos($id);
						$q['username'] = $infos['username'];
						$quando = '<div style="display: inline-block; float: right;"><script id="remove">document.write(jQuery.timeago("'.$q['date'].' UTC")); document.getElementById("remove").outerHTML = "";</script></div>';
						echo '<td style="border-right: 1px solid #577599">- <a href="./profile&player='.$q['username'].'">'.$q['username'].'</a>'.$quando.'</td>';

						if ($i == 2) {
							$i = 0;
							echo '</tr>';
						}
					}

					if ($number == 0) {
						if ($profiel['user_id'] == $_SESSION['id']) {
							echo '<tr><td style="text-align: center">Faça novos <b>amigos</b>! Clique <a href="./friends-add">AQUI</a> para procurar novos treinadores!</tr>';
						} else {
							echo '<tr><td style="text-align: center"><b>'.$profiel['username'].'</b> não tem amigos. Seja o <b>primeiro</b> a adicioná-lo!</td></tr>';
						}
					}
				?>
			</tbody>
			<?php
				$base_url = getUrl('/&friends=[0-9]/');
				if ($aantal_paginas > 1) {
					$links = false;
					$rechts = false;
					echo '<tfoot>';
					echo '<td align="center" colspan="6"><div class="sabrosus">';
					if ($subpage == 1)	echo '<span class="disabled">&laquo;</span>';
					else {
						$back = $subpage-1;
						echo '<a href="'.$base_url.'&friends='.$back.'">&laquo;</a>';
					}
					for($i=1;$i<=$aantal_paginas;++$i) {
						if (3 >= $i && $subpage == $i)	echo '<span class="current">'.$i.'</span>';
						else if (3 >= $i && $subpage != $i)	echo '<a href="'.$base_url.'&friends='.$i.'">'.$i.'</a>';
						else if ($aantal_paginas-2 < $i && $subpage == $i)	echo '<span class="current">'.$i.'</span>';
						else if ($aantal_paginas-2 < $i && $subpage != $i)	echo '<a href="'.$base_url.'&friends='.$i.'">'.$i.'</a>';
						else {
							$max = $subpage + 3;
							$min = $subpage -3;  
							if ($page == $i)	echo '<span class="current">'.$i.'</span>';
							else if ($min < $i && $max > $i)	echo '<a href="'.$base_url.'&friends='.$i.'">'.$i.'</a>';
							else {
								if ($i < $subpage) {
									if (!$links) {
										echo '...';
										$links = true;
									}
								} else {
									if (!$rechts) {
										echo '...';
										$rechts = true;
									}
								}
							}
						}
					} 
					if ($aantal_paginas == $subpage) echo '<span class="disabled">&raquo;</span>';
					else {
						$next = $subpage+1;
						echo '<a href="'.$base_url.'&friends='.$next.'">&raquo;</a>';
					}
					echo '</div></td></tfoot>';
				}
		?>
		</table>
	</div>	

</div>

<div class="row">

<div class="box-content col" style="width: 30%; height: 200px; overflow-y: auto; border-top-right-radius: 0; border-bottom-right-radius: 0;">
	<table width="100%" class="general">
		<thead><tr><th colspan="2">Honras</th></tr></thead>
		<tbody>
			<?php
				$query = DB::exQuery("SELECT * FROM `honra` WHERE u_id='$profiel[user_id]' ORDER BY `id` DESC LIMIT 5");
				$query2 = DB::exQuery("SELECT * FROM `honra` WHERE u_id='$profiel[user_id]'");
			?>
			<tr>
				<td colspan="2" style="text-align: center"><b><?=$profiel['username']?></b> possui <b><?=$query2->num_rows?></b> honras, incluindo:</td>
			</tr>
				<?php
				$i = 0;
				while ($q = $query->fetch_assoc()) {
					$q['username'] = DB::exQuery("SELECT `username` FROM `gebruikers` WHERE user_id='$q[u_honor]'")->fetch_assoc()['username'];
					$date = ($q['date']);
					echo '<tr><td class="first" style="padding: 0 10px"><a href="./profile&player='.$q['username'].'">'.($q['username']).'</a></td><td class="last last-right"><p class="date" style="margin:0; padding-right: 13px"></p></td></tr>';
			?>
			<script id="scr">
				$('.date').text(jQuery.timeago("<?=$date?> UTC"));
				$('.date').toggleClass();
				$('#scr').remove();
			</script>
			<?php
					$i++;
				}
				if ($profiel['user_id'] != $_SESSION['id'] && isset($_SESSION['id'])) {
					if ($i == 0) echo '<tr><td colspan="2" style="text-align: center; padding: 3px">Seja o primeiro a honrar <b>'.$profiel['username'].'</b> por seu jogo.</td></tr>';
				}
				?>
		</tbody>
		
	</table>

		<?php 
				if ($profiel['user_id'] != $_SESSION['id'] && isset($_SESSION['id'])) {
				$date = date('Y-m-d');
				$time = DB::exQuery("SELECT * FROM `honra` WHERE `u_id`='".$profiel['user_id']."' AND `u_honor`='".$_SESSION['id']."' AND `date_ctrl`='".$date."'")->num_rows;
				if ($time == 0) {
		?>		
				<div style="text-align: center; padding: 3px; border-top:1px solid #577599"><button type="button" style="background: url('<?=$static_url?>/images/layout/honrar.png'); width: 103px; height: 30px; border: none; border-radius: 0" title="Honrar <?=$profiel['username']?> por seu jogo." onclick="wlHonor()"></button></div>
				<script>
					function wlHonor () {
						$.ajax({
							url: 'ajax.php?act=honor',
							method: 'get',
							data: {'id': '<?=$profiel['user_id']?>'},
							success: function() {
								location.reload();
							}
						});
					}
				</script>
		<?php 
				} else {
					echo '<div style="text-align: center; padding: 3px; border-top: 1px solid #577599">'; 
		?>
					<button type="button" style="background: url('<?=$static_url?>/images/layout/honrar.png'); width: 103px; height: 30px; border: none; border-radius: 0; cursor: not-allowed; filter: grayscale(50%);" title="Você não pode honrar mais esse treinador por hoje."></button>
		<?php
					echo '</div>';
				}
			} 
		?>
</div>

<div class="box-content col" style="width: 70%; height: 200px; border-top-left-radius: 0;border-bottom-left-radius: 0;background:#2e3d53">
	<table width="100%" style="height: 170px;" class="general">
		<thead><tr><th colspan="6">Minha equipe</th></tr></thead>
		<tbody><tr>

<?php
	if ($profiel['teamzien'] == 1 || $gebruiker['admin'] > 0) {
?>
				<script>
					var $poke_array_id = [];
					var $poke_array_name = [];
					var $poke_array_spe = [];
				</script>

				<td style="padding: 0">
					<div class="main-carousel" style="height: 97px; position: relative">
						<?php
							$pokemon_profiel_sql = DB::exQuery("SELECT `pokemon_speler`.*,`pokemon_wild`.`naam`,`pokemon_wild`.`type1`,`pokemon_wild`.`type2` FROM `pokemon_speler` INNER JOIN `pokemon_wild` ON `pokemon_speler`.`wild_id`=`pokemon_wild`.`wild_id` WHERE `user_id`='" . $profiel['user_id'] . "' AND `opzak`='ja' ORDER BY `opzak_nummer` ASC");
							//Pokemons opzak weergeven op het scherm
							while($pokemon_profile = $pokemon_profiel_sql->fetch_assoc()) {
								$pokemon_profile = pokemonei($pokemon_profile, $txt);
								$of_name = $pokemon_profile['naam'];
								$popup = pokemon_popup($pokemon_profile, $txt);
								$pokemon_profile['naam'] = pokemon_naam($pokemon_profile['naam'], $pokemon_profile['roepnaam'],$pokemon_profile['icon']);
						?>
								<div class="carousel-cell" style="text-align: center;">
									<div style="display:table-cell; vertical-align:middle; min-width: 150px; height: 150px;">
										<?='<img id="my_pokes_infos" class="tip_top-middle" title="'.$popup.'" src="' . $static_url . '/'.$pokemon_profile['link'].'" />';?>
										<script id="remove">
											$poke_array_id.push("<?=$pokemon_profile['wild_id']?>");
											$poke_array_name.push("<?=$of_name?>");
											$poke_array_spe.push("<?=$pokemon_profile['naam']?>");

											document.querySelector("#remove").outerHTML = '';
										</script>
									</div>
								</div>
						<?php
							}
						?>
					</div>
					<div style="width: 100%;background: rgba(0, 0, 0, .3);position: relative;bottom: 0;text-align: center;height: 53px;padding-top: 3px;margin-top: 12px;border-bottom-right-radius: 2px;border-bottom-left-radius: 2px;">
						<div style="width: 100%; text-align: center; font-size: 17px; margin-top: 3px">
							<h4 id="poke_name" style="margin: 0; color: #eee; font-weight: bold;"></h4>
							<a href="./pokedex&poke=1" id="poke_link" style="color: #eee; font-size: 13px"></a>
						</div>
					</div>
				</td>

				<script>
					var $carousel = $('.main-carousel');
					var $poke_name = $('#poke_name');
        			var $poke_link = $('#poke_link');

					var $car = $carousel.flickity({
						cellAlign: 'center',
						contain: false,
						pageDots: false,
						wrapAround: false,
						autoPlay: true
					});

					var flkty = $carousel.data('flickity');

					$carousel.on('select.flickity', function() {
						$poke_link.attr('href', './pokedex&poke='+$poke_array_id[flkty.selectedIndex]);
						$poke_link.html($poke_array_name[flkty.selectedIndex]);

						$poke_name.html($poke_array_spe[flkty.selectedIndex]);
					});

					$poke_link.attr('href', '/pokedex&poke='+$poke_array_id[0]);
					$poke_link.html($poke_array_name[0]);

					$poke_name.html($poke_array_spe[0]);

					$car.resize();
				</script>
<?php 
} else {
	echo '<td><h3 style="text-align: center">O treinador optou por esconder sua EQUIPE!</h3></td>';
}
?>
</tr>
</tbody>
	</table>
</div>

</div>

<?php
if ($profiel['badgeszien'] == 1 && $profiel['Badge case'] == 1) {
	$badge = DB::exQuery("SELECT * FROM gebruikers_badges WHERE user_id = '".$profiel['user_id']."'")->fetch_assoc();

echo '<div class="box-content" style="margin-top: 7px"><table class="general" width="100%"><thead><tr><th>Insígnias</th></tr></thead>
<tr>
<td colspan="3" onclick="wlBadges(\'#kanto\')" style="cursor: pointer"><h3 style="margin: 0"><b><center>'.$txt['badges'].' Kanto:</center></b></h3></td>
</tr>
<tr class="wlBadges" id="kanto">
<td colspan="3" align="center">';

 
  			if ($badge['Boulder'] != 0) echo '<img src="'.$static_url.'/images/badges/pixel/Boulder.png" alt="Boulder Badge" title="Boulder Badge" />';
  			if ($badge['Cascade'] != 0) echo '<img src="'.$static_url.'/images/badges/pixel/Cascade.png" alt="Cascade Badge" title="Cascade Badge" />';
  			if ($badge['Thunder'] != 0) echo '<img src="'.$static_url.'/images/badges/pixel/Thunder.png" alt="Thunder Badge" title="Thunder Badge" />';
  			if ($badge['Rainbow'] != 0) echo '<img src="'.$static_url.'/images/badges/pixel/Rainbow.png" alt="Rainbow Badge" title="Rainbow Badge" />';
  			if ($badge['Marsh'] != 0) echo '<img src="'.$static_url.'/images/badges/pixel/Marsh.png" alt="Marsh Badge" title="Marsh Badge" />';
  			if ($badge['Soul'] != 0) echo '<img src="'.$static_url.'/images/badges/pixel/Soul.png" alt="Soul Badge" title="Soul Badge" />';
  			if ($badge['Volcano'] != 0) echo '<img src="'.$static_url.'/images/badges/pixel/Volcano.png" alt="Volcano Badge" title="Volcano Badge" />';
  			if ($badge['Earth'] != 0) echo '<img src="'.$static_url.'/images/badges/pixel/Earth.png" alt="Earth Badge" title="Earth Badge" />';
  			
  	
if ($badge['Boulder'] == 0 && $badge['Cascade'] == 0 && $badge['Thunder'] == 0 && $badge['Rainbow'] == 0 && $badge['Marsh'] == 0 && $badge['Soul'] == 0 && $badge['Volcano'] == 0 && $badge['Earth'] == 0) echo $txt['no_badges_from'].' Kanto';

echo '</td></tr>
<tr>
<td colspan="3" onclick="wlBadges(\'#johto\')" style="cursor: pointer"><h3 style="margin: 0"><b><center>'.$txt['badges'].' Johto:</center></b></h3></td>
</tr>
<tr class="wlBadges" id="johto">
<td colspan="3" align="center">';

		if ($badge['Zephyr'] != 0) echo '<img src="'.$static_url.'/images/badges/pixel/Zephyr.png" alt="Zephyr Badge" title="Zephyr Badge" />';
			if ($badge['Hive'] != 0) echo '<img src="'.$static_url.'/images/badges/pixel/Hive.png" alt="Hive Badge" title="Hive Badge" />';
			if ($badge['Plain'] != 0) echo '<img src="'.$static_url.'/images/badges/pixel/Plain.png" alt="Plain Badge" title="Plain Badge" />';
			if ($badge['Fog'] != 0) echo '<img src="'.$static_url.'/images/badges/pixel/Fog.png" alt="Fog Badge" title="Fog Badge" />';
			if ($badge['Storm'] != 0) echo '<img src="'.$static_url.'/images/badges/pixel/Storm.png" alt="Storm Badge" title="Storm Badge" />';
			if ($badge['Mineral'] != 0) echo '<img src="'.$static_url.'/images/badges/pixel/Mineral.png" alt="Mineral Badge" title="Mineral Badge" />';
			if ($badge['Glacier'] != 0) echo '<img src="'.$static_url.'/images/badges/pixel/Glacier.png" alt="Glacier Badge" title="Glacier Badge" />';
			if ($badge['Rising'] != 0) echo '<img src="'.$static_url.'/images/badges/pixel/Rising.png" alt="Rising Badge" title="Rising Badge" />';
			

	if ($badge['Zephyr'] == 0 && $badge['Hive'] == 0 && $badge['Plain'] == 0 && $badge['Fog'] == 0 && $badge['Storm'] == 0 && $badge['Mineral'] == 0 && $badge['Glacier'] == 0 && $badge['Rising'] == 0) echo $txt['no_badges_from'].' Johto';
			

echo '</td></tr>
<tr>
<td colspan="3" onclick="wlBadges(\'#hoenn\')" style="cursor: pointer"><h3 style="margin: 0"><b><center>'.$txt['badges'].' Hoenn:</center></b></h3></td>
</tr>
<tr class="wlBadges" id="hoenn">
<td colspan="3" align="center">';

	if ($badge['Stone'] != 0) echo '<img src="'.$static_url.'/images/badges/pixel/Stone.png" alt="Stone Badge" title="Stone Badge" />';
			if ($badge['Knuckle'] != 0) echo '<img src="'.$static_url.'/images/badges/pixel/Knuckle.png" alt="Knuckle Badge" title="Knuckle Badge" />';
			if ($badge['Dynamo'] != 0) echo '<img src="'.$static_url.'/images/badges/pixel/Dynamo.png" alt="Dynamo Badge" title="Dynamo Badge" />';
			if ($badge['Heat'] != 0) echo '<img src="'.$static_url.'/images/badges/pixel/Heat.png" alt="Heat Badge" title="Heat Badge" />';
			if ($badge['Balance'] != 0) echo '<img src="'.$static_url.'/images/badges/pixel/Balance.png" alt="Balance Badge" title="Balance Badge" />';
			if ($badge['Feather'] != 0) echo '<img src="'.$static_url.'/images/badges/pixel/Feather.png" alt="Feather Badge" title="Feather Badge" />';
			if ($badge['Mind'] != 0) echo '<img src="'.$static_url.'/images/badges/pixel/Mind.png" alt="Mind Badge" title="Mind Badge" />';
			if ($badge['Rain'] != 0) echo '<img src="'.$static_url.'/images/badges/pixel/Rain.png" alt="Rain Badge" title="Rain Badge" />';
			
			if ($badge['Stone'] == 0 && $badge['Knuckle'] == 0 && $badge['Dynamo'] == 0 && $badge['Heat'] == 0 && $badge['Balance'] == 0 && $badge['Feather'] == 0 && $badge['Mind'] == 0 && $badge['Rain'] == 0) echo $txt['no_badges_from'].' Hoenn';
		

echo '</td></tr>
<tr>
<td colspan="3" onclick="wlBadges(\'#sinnoh\')" style="cursor: pointer"><h3 style="margin: 0"><b><center>'.$txt['badges'].' Sinnoh:</center></b></h3></td>
</tr>
<tr class="wlBadges" id="sinnoh">
<td colspan="3" align="center">';

		if ($badge['Coal'] != 0) echo '<img src="'.$static_url.'/images/badges/pixel/Coal.png" alt="Coal Badge" title="Coal Badge" />';
			if ($badge['Forest'] != 0) echo '<img src="'.$static_url.'/images/badges/pixel/Forest.png" alt="Forest Badge" title="Forest Badge" />';
			if ($badge['Cobble'] != 0) echo '<img src="'.$static_url.'/images/badges/pixel/Cobble.png" alt="Cobble Badge" title="Cobble Badge" />';
			if ($badge['Fen'] != 0) echo '<img src="'.$static_url.'/images/badges/pixel/Fen.png" alt="Fen Badge" title="Fen Badge" />';
			if ($badge['Relic'] != 0) echo '<img src="'.$static_url.'/images/badges/pixel/Relic.png" alt="Relic Badge" title="Relic Badge" />';
			if ($badge['Mine'] != 0) echo '<img src="'.$static_url.'/images/badges/pixel/Mine.png" alt="Mine Badge" title="Mine Badge" />';
			if ($badge['Icicle'] != 0) echo '<img src="'.$static_url.'/images/badges/pixel/Icicle.png" alt="Icicle Badge" title="Icicle Badge" />';
			if ($badge['Beacon'] != 0) echo '<img src="'.$static_url.'/images/badges/pixel/Beacon.png" alt="Beacon Badge" title="Beacon Badge" />';
			
			if ($badge['Coal'] == 0 && $badge['Forest'] == 0 && $badge['Cobble'] == 0 && $badge['Fen'] == 0 && $badge['Relic'] == 0 && $badge['Mine'] == 0 && $badge['Icicle'] == 0 && $badge['Beacon'] == 0) echo $txt['no_badges_from'].' Sinnoh';
			

echo '</td></tr>
<tr>
<td colspan="3" onclick="wlBadges(\'#unova\')" style="cursor: pointer"><h3 style="margin: 0"><b><center>'.$txt['badges'].' Unova:</center></b></h3></td>
</tr>
<tr class="wlBadges" id="unova">
<td colspan="3" align="center">';

		if ($badge['Trio'] != 0) echo '<img src="'.$static_url.'/images/badges/pixel/Trio.png" alt="Trio Badge" title="Trio Badge" />';
			if ($badge['Basic'] != 0) echo '<img src="'.$static_url.'/images/badges/pixel/Basic.png" alt="Basic Badge" title="Basic Badge" />';
			if ($badge['Insect'] != 0) echo '<img src="'.$static_url.'/images/badges/pixel/Insect.png" alt="Insect Badge" title="Insect Badge" />';
			if ($badge['Bolt'] != 0) echo '<img src="'.$static_url.'/images/badges/pixel/Bolt.png" alt="Bolt Badge" title="Bolt Badge" />';
			if ($badge['Quake'] != 0) echo '<img src="'.$static_url.'/images/badges/pixel/Quake.png" alt="Quake Badge" title="Quake Badge" />';
			if ($badge['Jet'] != 0) echo '<img src="'.$static_url.'/images/badges/pixel/Jet.png" alt="Jet Badge" title="Jet Badge" />';
			if ($badge['Freeze'] != 0) echo '<img src="'.$static_url.'/images/badges/pixel/Freeze.png" alt="Freeze Badge" title="Freeze Badge" />';
			if ($badge['Legend'] != 0) echo '<img src="'.$static_url.'/images/badges/pixel/Legend.png" alt="Legend Badge" title="Legend Badge" />';
			
			if ($badge['Trio'] == 0 && $badge['Basic'] == 0 && $badge['Insect'] == 0 && $badge['Bolt'] == 0 && $badge['Quake'] == 0 && $badge['Jet'] == 0 && $badge['Freeze'] == 0 && $badge['Legend'] == 0) echo $txt['no_badges_from'].' Unova';
			
			

echo '</td></tr>
<tr>
<td colspan="3" onclick="wlBadges(\'#kalos\')" style="cursor: pointer"><h3 style="margin: 0"><b><center>'.$txt['badges'].' Kalos:</center></b></h3></td>
</tr>
<tr class="wlBadges" id="kalos">
<td colspan="3" align="center">';

			if ($badge['Bug'] != 0) echo '<img src="'.$static_url.'/images/badges/pixel/Bug.png" alt="Bug Badge" title="Bug Badge" />';
			if ($badge['Cliff'] != 0) echo '<img src="'.$static_url.'/images/badges/pixel/Cliff.png" alt="Cliff Badge" title="Cliff Badge" />';
			if ($badge['Rumble'] != 0) echo '<img src="'.$static_url.'/images/badges/pixel/Rumble.png" alt="Rumble Badge" title="Rumble Badge" />';
			if ($badge['Plant'] != 0) echo '<img src="'.$static_url.'/images/badges/pixel/Plant.png" alt="Plant Badge" title="Plant Badge" />';
			if ($badge['Voltage'] != 0) echo '<img src="'.$static_url.'/images/badges/pixel/Voltage.png" alt="Voltage Badge" title="Voltage Badge" />';
			if ($badge['Fairy'] != 0) echo '<img src="'.$static_url.'/images/badges/pixel/Fairy.png" alt="Fairy Badge" title="Fairy Badge" />';
			if ($badge['Psychic'] != 0) echo '<img src="'.$static_url.'/images/badges/pixel/Psychic.png" alt="Psychic Badge" title="Psychic Badge" />';
			if ($badge['Iceberg'] != 0) echo '<img src="'.$static_url.'/images/badges/pixel/Iceberg.png" alt="Iceberg Badge" title="Iceberg Badge" />';
			
			if ($badge['Bug'] == 0 && $badge['Cliff'] == 0 && $badge['Rumble'] == 0 && $badge['Plant'] == 0 && $badge['Voltage'] == 0 && $badge['Fairy'] == 0 && $badge['Psychic'] == 0 && $badge['Iceberg'] == 0) echo $txt['no_badges_from'].' Kalos';
			
			
echo '</td></tr>
<tr>
<td colspan="3" onclick="wlBadges(\'#alola\')" style="cursor: pointer"><h3 style="margin: 0"><b><center>'.$txt['badges'].' Alola:</center></b></h3></td>
</tr>
<tr class="wlBadges" id="alola">
<td colspan="3" align="center">';

			if ($badge['Melemele Normal'] != 0) echo '<img src="'.$static_url.'/images/badges/pixel/Melemele Normal.png" alt="Melemele Normal Badge" title="Melemele Normal Badge" />';
			if ($badge['Akala Water'] != 0) echo '<img src="'.$static_url.'/images/badges/pixel/Akala Water.png" alt="Akala Water Badge" title="Akala Water Badge" />';
			if ($badge['Akala Fire'] != 0) echo '<img src="'.$static_url.'/images/badges/pixel/Akala Fire.png" alt="Akala Fire Badge" title="Akala Fire Badge" />';
			if ($badge['Akala Grass'] != 0) echo '<img src="'.$static_url.'/images/badges/pixel/Akala Grass.png" alt="PlanAkala Grasst Badge" title="Akala Grass Badge" />';
			if ($badge['Ulaula Electric'] != 0) echo '<img src="'.$static_url.'/images/badges/pixel/Ulaula Electric.png" alt="Ulaula Electric" title="Ulaula Electric" />';
			if ($badge['Ulaula Ghost'] != 0) echo '<img src="'.$static_url.'/images/badges/pixel/Ulaula Ghost.png" alt="Ulaula Ghost Badge" title="Ulaula Ghost Badge" />';
			if ($badge['Poni Fairy'] != 0) echo '<img src="'.$static_url.'/images/badges/pixel/Poni Fairy.png" alt="Poni Fairy Badge" title="Poni Fairy Badge" />';
			if ($badge['Poni Ground'] != 0) echo '<img src="'.$static_url.'/images/badges/pixel/Poni Ground.png" alt="Poni Ground Badge" title="Poni Ground Badge" />';
			
			if ($badge['Melemele Normal'] == 0 && $badge['Akala Water'] == 0 && $badge['Akala Fire'] == 0 && $badge['Akala Grass'] == 0 && $badge['Ulaula Electric'] == 0 && $badge['Ulaula Ghost'] == 0 && $badge['Poni Fairy'] == 0 && $badge['Poni Ground'] == 0) echo $txt['no_badges_from'].' Alola';
echo '</table></div>';
?>
<script>
	function wlBadges( el ) {
		$(el).toggleClass('wlBadges');
	}
</script>
<?php
}

		}
?>
<?php
	} else {
		header('location: ./notfound');
	}
?>