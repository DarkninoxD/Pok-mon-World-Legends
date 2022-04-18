<?php
    include('app/includes/resources/security.php');
    
	$result = DB::exQuery("SELECT `user_id`,`character`, `username`, `rang`, `points` FROM `gebruikers` WHERE `rang` != 0 ORDER BY `rang` ASC LIMIT 0, 3");
?>
<div class="blue">As posições do ranking são atualizadas à cada 30 minutos.</div>
<div class="box-content">
	<h3 class="title" style="font-size: 21px; width: 36%">Ranking</h3>
<?php	
	if ($result->num_rows > 0) {
		$pos = array('primeiro', 'segundo', 'terceiro');
		echo '<div class="podium">';
		while ($row = $result->fetch_assoc()) {
			if ($row['rang'] > 0 && $row['rang'] < 4) { 
				echo '<div class="'.$pos[$row['rang']-1].'">';
				echo '<a href="./profile&player='.$row['username'].'">'.GetColorName($row['user_id']).'</a> <br><b style="color: #eee">('.number_format($row['points'], 0, ',', '.').')</b>';
				echo '<img src="' . $static_url . '/images/characters/' . $row['character'] . '/Thumb.png" width="70" height="70"/>';
				echo '</div>';
			}
		}
		echo '</div>';
		
	}

	$max_per_page = 20;
	$subpage = $_GET['subpage'];
	if (!isset($subpage) && $player['rang_temp'] != 0) {
		$subpage = ceil($subpage / $max_per_page);
	} else	$subpage = round($subpage);
	$subpage = !is_numeric($subpage) ? 1 : $subpage;
	if ($subpage <= 0)	$subpage = 1;
	$start_records = ($subpage * $max_per_page) - $max_per_page;

	$result = DB::exQuery("SELECT `user_id`, `character`, `username`, `rank`, `points`, `online`, `rang` FROM `gebruikers` WHERE `rang` != 0 ORDER BY `rang` ASC LIMIT {$start_records}, {$max_per_page}");
?>
<table class="general" width="100%">
	<thead><tr>
		<th width="50" style="text-align:center;">#</th>
		<th width="190" colspan="2">Treinador</th>
		<th width="190" colspan="2">Rank</th>
		<th width="190">Pontuação</th>
		<th width="110" style="text-align:center;">Status</th>
	</tr></thead>
	<tbody><?php
		if ($result->num_rows <= 0)	echo "<tr><td colspan=\"9\"><div class=\"red\">Nenhum resultado.</div></td></tr>";
		else {
			while ($row = $result->fetch_assoc()) {
				//Default offline
				$plaatje = $static_url . "/images/icons/status_offline.png";

				//Get Rank Info
				$rank = rank($row['rank']);

				//Tijd voor plaatje
				if (($row['online'] + 300) > time())	$plaatje = $static_url . "/images/icons/status_online.png";

				if ($row['rang'] > 0 && $row['rang'] < 4) { 
					continue;
				} else {
					$medaille = $row['rang'] . '&ordm;';
				}

				// if ($row['rang'] == 1) { 
				// 	$medaille = "<img src='" . $static_url . "/images/icons/plaatsnummereen.png' />";
				// } else if ($row['rang'] == 2) {
				// 	$medaille = "<img src='" . $static_url . "/images/icons/plaatsnummertwee.png' />";
			    // } else if ($row['rang'] == 3) {
				// 	$medaille = "<img src='" . $static_url . "/images/icons/plaatsnummerdrie.png' />";
				// } else { 
				// 	$medaille = $row['rang'] . '&ordm;';
				// }

				echo '<tr>
					<td width="30" align="center">' . $medaille . '</td>
					<td style="padding: 0;"><img src="' . $static_url . '/images/characters/' . $row['character'] . '/Thumb.png" width="30" height="30" /></td>
					<td width="180"><a href="./profile&player='.$row['username'].'">'.GetColorName($row['user_id']).'</a></td>
					<td width="10" align="center"><img src="' . $static_url . '/images/icons/bookmark.png" /></td>
					<td width="180">'.$rank['ranknaam'].'</td>
					<td width="180" align="center">'.number_format($row['points'], 0, ',', '.').'</td>
					<td width="50" align="center"><img src="' . $plaatje . '" /></td>
				</tr>';
			}
		}
	?></tbody>
	<tfoot><tr>
<?php
$total_records = DB::exQuery("SELECT `user_id` FROM `gebruikers` WHERE `rang` != 0")->num_rows;
$aantal_paginas = ceil($total_records / $max_per_page);
if ($aantal_paginas > 1) {
	//Pagina systeem
	$links = false;
	$rechts = false;

	echo '<td colspan="9" align="center"><div class="sabrosus">';
	if ($subpage == 1)	echo '<span class="disabled">&laquo;</span>';
	else {
		$back = $subpage - 1;
		echo '<a href="./' . $_GET['page'] . '&subpage=' . $back . '">&laquo;</a>';
	}

	for($i = 1; $i <= $aantal_paginas; $i++) { 
		if ((2 >= $i) && ($subpage == $i))	echo '<span class="current">' . $i . '</span>';
		else if ((2 >= $i) && ($subpage != $i))	echo '<a href="./'.$_GET['page'].'&subpage='.$i.'">'.$i.'</a>';
		else if (($aantal_paginas-2 < $i) && ($subpage == $i))	echo '<span class="current">' . $i . '</span>';
		else if (($aantal_paginas-2 < $i) && ($subpage != $i))	echo '<a href="./'.$_GET['page'].'&subpage='.$i.'">'.$i.'</a>';
		else {
			$max = $subpage + 3;
			$min = $subpage - 3;
			if ($subpage == $i)	echo '<span class="current">' . $i . '</span>';
			else if (($min < $i) && ($max > $i))	echo '<a href="./'.$_GET['page'].'&subpage='.$i.'">'.$i.'</a>';
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
		$next = $subpage + 1;
		echo '<a href="./' . $_GET['page'] . '&subpage=' . $next . '">&raquo;</a>';
	}
	echo "</td>";
}
?>
	</tr></tfoot>
</table></div>