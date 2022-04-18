<?php
include('app/includes/resources/security.php');
$sql = "SELECT `username`,`premiumaccount`,`admin`,`rang`,`dv` FROM `gebruikers` WHERE (`online` + 900) >= UNIX_TIMESTAMP() AND `banned` = 'N' ORDER BY `admin` DESC, `points` DESC, `rang` ASC, `user_id` ASC";
$expire = 30;
$records = query_cache("online",$sql,$expire);
$total_online = count($records);
?>
<div class="box-content" style="float: left; width: 100%; margin-top: 10px; margin-bottom: 10px">
	<table class="general" width="100%">
		<thead><tr>
			<th>Treinadores Online (<?=$total_online;?>)</th>
		</tr></thead>
		<tbody><tr><td><?php
		
		if ($total_online == 0) { 
			echo '<h3>Nenhum treinador online!</h3>';
		} else { 
			$i = 1;
			foreach ($records as $id=>$online) { 
			
				$fixt = ','.$online['user_id'].',';
			
				$buddy_check = strpos($gebruiker['buddy'], $fixt);
				$block_check = strpos($gebruiker['blocklist'], $fixt);
				
				if ($online['admin'] == 1)								$name = "<div class='admin_container'><span style='color: #A1FF77; text-shadow:#000 1px -1px 2px, #000 -1px 1px 2px, #000 1px 1px 2px, #000 -1px -1px 2px'><b>".$online['username']."</b></span> <img src=\"".$static_url."/images/icons/user.png\" width=\"16\" height=\"16\" border=\"0\" alt=\"ico\" title=\"Moderador\" style=\"margin-bottom:-3px;\">";
				else if ($online['admin'] == 2)							$name = "<div class='admin_container'><span style='color: #FF3030; text-shadow:#000 1px -1px 2px, #000 -1px 1px 2px, #000 1px 1px 2px, #000 -1px -1px 2px'><b>".$online['username']."</b></span> <img src=\"".$static_url."/images/icons/user_suit.png\" width=\"16\" height=\"16\" border=\"0\" alt=\"ico\" title=\"Supervisor\" style=\"margin-bottom:-3px;\">";
				else if ($online['admin'] == 3)							$name = "<div class='admin_container'><b><span style='color: yellow; text-shadow:#000 1px -1px 2px, #000 -1px 1px 2px, #000 1px 1px 2px, #000 -1px -1px 2px'>".$online['username']."</span></b>";
				else if ($online['dv'] == 1)							$name = "<div class='name_container'><b><span style='color: orange; text-shadow:#000 1px -1px 2px, #000 -1px 1px 2px, #000 1px 1px 2px, #000 -1px -1px 2px'>[DV]".$online['username']."</span></b> <img src=\"".$static_url."/images/icons/dv.png\" width=\"16\" height=\"16\" border=\"0\" alt=\"ico\" title=\"Divulgador\" style=\"margin-bottom:-3px;\">";
				else if ($buddy_check !== false) 						$name = "<div class='name_container'><span class='buddytext'>".$online['username']."</span>";
				else if ($block_check !== false) 						$name = "<div class='name_container'><span class='blocktext'>".$online['username']."</span>";
				else if ($online['rang'] >= 1 && $online['rang'] <= 4)	$name = "<div class='name_container'><span style='font-weight: bold;><font color='black'>".$online['username']."</font></span>";
				else													$name = "<div class='name_container'>".$online['username'];

				if ($online['rang'] >= 1 && $online['rang'] <= 4 && $online['admin'] == 0) $name .= '<img src="'.$static_url.'/images/icons/elite.png" width="16" height="16" border="0" alt="Elite dos 4" title="Elite dos 4">';
					
				if (($online['premiumaccount'] > time()) && ($online['admin'] == 0)) $name .= '<img src="'.$static_url.'/images/icons/vip.gif" width="16" height="16" border="0" alt="Premium" title="Premium" style="margin-bottom:-3px;">';

				$name .= '</div>';
				echo '<a href="./profile&player='.$online['username'].'" class="noanimate">'.$name.'</a>';
				++$i;
			}
		}
		$record_online = DB::exQuery("SELECT `valor` FROM `configs` WHERE `config` = 'recorde_online'")->fetch_assoc();
		if ($total_online > $record_online['valor'])
			DB::exQuery("UPDATE `configs` SET `valor` = '{$total_online}' WHERE `config` = 'recorde_online'");
		?></td></tr></tbody>
	</table>
</div>