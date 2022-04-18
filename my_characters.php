<?php
require_once('app/includes/resources/security-account.php');

$sql = DB::exQuery("SELECT * FROM `gebruikers` WHERE `acc_id`=" . $_SESSION['acc_id'] . " ORDER BY `rank` DESC,`user_id` ASC");
if ($sql->num_rows < 1)	exit(header("LOCATION: ./new_character"));
else {
	if ($_POST['submit'] && is_numeric($_POST['user_id'])) {
		$geb_sql = DB::exQuery("SELECT * FROM `gebruikers` WHERE `user_id`=" . $_POST['user_id'] . " LIMIT 1");
		if ($geb_sql->num_rows != 1)	echo "<div class=\"red\">Personagem não encontrado!</div>";
		else {
			$geb_login = $geb_sql->fetch_assoc();
			if ($geb_login['acc_id'] != $_SESSION['acc_id'])	echo "<div class=\"red\">Este personagem não pertence a você!</div>";
			else if ($geb_login['banned'] == 'Y')	echo "<div class=\"red\">Este personagem está bloqueado!</div>";
			else {
				# Ganha 3 dias premium
				if ($geb_login['premiumaccount'] == 0) {
					$endPremium = time() + (86400 * 3);
					DB::exQuery("UPDATE `gebruikers` SET `premiumaccount`=" . $endPremium . " WHERE `user_id`=" . $geb_login['user_id'] . " LIMIT 1");
				}
				$_SESSION['id'] = $geb_login['user_id'];
				$_SESSION['naam'] = $geb_login['username'];
				//$_SESSION['hash'] = md5($_SERVER['REMOTE_ADDR'] . "," . $geb_login['username']);
				$_SESSION['hash'] = md5($geb_login['user_id'] . "," . $geb_login['username']);
				$sec_key = mt_rand(100000, 999999);
				$_SESSION['sec_key'] = $sec_key;
				$date_lo = date('d/m/Y');
				$date_loh = date('H:i:s');

				if (($date_lo) > ($geb_login['ultimo_login'])) {
					DB::exQuery("UPDATE `gebruikers` SET `ultimo_login`='{$date_lo}', `ultimo_login_hour`='{$date_loh}', `antiguidade`=`antiguidade`+1, `sec_key`='{$sec_key}', `session`='{$_COOKIE['PHPSESSID']}', chat_key = '" . md5(time()) . "' WHERE `user_id`={$geb_login['user_id']} LIMIT 1");
				} else {
					DB::exQuery("UPDATE `gebruikers` SET `ultimo_login`='{$date_lo}', `ultimo_login_hour`='{$date_loh}', `sec_key`='{$sec_key}', `session`='{$_COOKIE['PHPSESSID']}', chat_key = '" . md5(time()) . "' WHERE `user_id`={$geb_login['user_id']} LIMIT 1");
				}

				exit(header("LOCATION: ./home"));
			}
		}
	}
echo addNPCBox(11, 'Meus Personagens', 'Aqui está a lista dos seus personagens. Escolha qual deseja jogar.');
?>
<div class="blue">Para criar um novo Personagem é só clicar <a href="./new_character">AQUI</a>.</div>
<style>
	.carousel-cell {
		margin: 10px 10px;
		filter: grayscale(100%);
		transform: scale(0.85);
		overflow: hidden;
	}
	.carousel-cell.is-selected {
		filter: grayscale(20%) invert(8%);
		transition: 1s;
		transform: scale(1);
	}
</style>

<div class="box-content"><table class="general" width="100%">
	<thead><tr><th><?=$txt['title_text'];?></th></tr></thead>
	<tbody><tr>
		<td style="padding: 0">
		<script>
			var $user_array_id = [];
			var $user_array_name = [];
			var $user_array_type = [];
			var $recent = [];
		</script>
			<div class="main-carousel"><?php
			$i = 0;
		while($gebruiker = $sql->fetch_assoc()) {
		    if (!empty($gebruiker)) {
			$type = '';
			$gebruiker['ultimo_login'] = $gebruiker['ultimo_login'].' '.$gebruiker['ultimo_login_hour'];
			echo "<div class='carousel-cell' style=\"padding-top: 30px;display:inline-block; margin:1.8px\">";
			echo "<img src=\"" . $static_url . "/images/characters/" . $gebruiker['character'] . "/Thumb.png\" id=\"trainer_infos\" title=\"" . gebruiker_popup($gebruiker, $txt) . "\" width=\"130\" height=\"130\" /><br>";
			echo "</div>";

			if ($gebruiker['banned'] != 'N') $type = "<font color='grey'><b>[BANIDO]</b></font>";
			else if ($gebruiker['admin'] > 0) $type = "<font><b>[STAFF]</b></font>";
			else $type = "<font><b>[TRAINER]</b></font>";
			?>
			<script id="remove">
				$user_array_name.push("<?=$gebruiker['username']?>");
				$user_array_type.push("<?=$type?>");
				$user_array_id.push("<?=$gebruiker['user_id']?>");
				$recent.push("<?=$gebruiker['ultimo_login']?>");

				document.querySelector('#remove').outerHTML = '';
			</script>
			<?php
			$i++;
		    }
		}
		?>

		</div>
		<div style="width: 100%; background: rgba(0, 0, 0, .3); position: relative; bottom: 0; text-align: center; height: 53px; padding-top: 3px; margin-top: -35px; border-bottom-right-radius: 2px;  border-bottom-left-radius: 2px">
			<div style="width: 100%; text-align: center; font-size: 17px; margin-top: 3px">
				<h4 id="user_name" style="margin: 0; color: #eee; font-weight: bold;"></h4>
				<span id="user_type" style="color: #eee; font-size: 13px"></span>
			</div>
		</div>
		</td></tr></tbody>
		<tfoot>
			<tr style="text-align: center; font-size: 13px">
				<td>
					<form action="./my_characters" method="POST">
						<input type="hidden" id="user_id" name="user_id" value="">
						<input type="submit" id="user_submit" name="submit" style="margin: 6px" value="">
					</form>
				</td>
			</tr>
		</tfoot>
</table></div>

<script type="text/javascript">
	$(document).ready(function() {
		Tipped.create("*#trainer_infos", {
			hook: 'bottommiddle',
			maxWidth: 390
		});
	});

	var $carousel = $('.main-carousel');
	var $user_name = $('#user_name');
	var $user_type = $('#user_type');
	var $user_id = $('#user_id');
	var $user_submit = $('#user_submit');
// 	var $most_recent = $recent.maxIndex();
	var $most_recent = 0;

	var $car = $carousel.flickity({
		cellAlign: 'center',
		contain: false,
		pageDots: false,
		wrapAround: false,
		initialIndex: $most_recent
	});

	$carousel.on('staticClick.flickity', function(event, pointer, cellElement, cellIndex) {
		$carousel.flickity( 'select', cellIndex );
	});

	var flkty = $carousel.data('flickity');

	$carousel.on('select.flickity', function() {
		$user_name.text($user_array_name[flkty.selectedIndex]);
		$user_type.html($user_array_type[flkty.selectedIndex]);
		$user_id.val($user_array_id[flkty.selectedIndex]);
		$user_submit.val('JOGAR COM '+$user_array_name[flkty.selectedIndex]);
	});
	
	$user_name.text($user_array_name[$most_recent]);
	$user_type.html($user_array_type[$most_recent]);
	$user_id.val($user_array_id[$most_recent]);
	$user_submit.val('JOGAR COM '+$user_array_name[$most_recent]);

	$car.resize();
</script>
<?php } ?>