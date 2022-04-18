<?php
//Security laden
include('app/includes/resources/security.php');

function redirect($location) {
	header('Location: '.$location.'');
	die();
}

echo addNPCBox(19, 'Zona do Safari', 'Procure e capture por Pokémons exóticos de todas as Regiões aqui na <b>Zona do Safari</b>! <br>O evento acaba <b>28/06/2019</b>!');
if(empty($Saffari)) exit(header("Location: ./home"));


$map = (int)$_GET['map'];
if (!is_numeric($map) || ($map <= 0 || $map > 7))
	$map = 1;

$map_info = DB::exQuery("SELECT * FROM maps WHERE id='".$map."'")->fetch_assoc();

if (isset($_GET['map'], $_GET['x'], $_GET['y'])) {
	$time = time();
	$x = (int) $_GET['x'];
	$y = (int) $_GET['y'];
	DB::exQuery("UPDATE `gebruikers` SET `map_num`='{$map}',`map_x`='{$x}',`map_y`='{$y}',`map_lastseen`='{$time}',`captcha_time`='{$time}' WHERE `user_id`='{$_SESSION['id']}'") or die(mysql_error());
	header('Location: ./eventos&actual=safari&map=' . $map);
}

if (empty($map)) {
	header('Location: ./eventos&actual=safari&map=1');
	exit;
} else {
	$startX = $map_info['start_x'];
	$startY = $map_info['start_y'];
	$mapTitle = $map_info['name'];
	$tileArray = $map_info['tile'];
}

$time = time();

$tenMinsAgo = $time - (60 * 10);
$usersQuery = DB::exQuery("SELECT * FROM `gebruikers` WHERE `map_num`='{$map}' AND `map_lastseen`>='{$tenMinsAgo}'") or die(mysql_error());
$numUsersOnMap = $usersQuery->num_rows;
$usersArray = array();

$i = 0;
$onMap = false;
while ($user = $usersQuery->fetch_assoc()) {
	if ($user['user_id'] == $_SESSION['id']) {
		$startX = $user['map_x'];
		$startY = $user['map_y'];
		$onMap = true;
		continue;
	}

	$usersArray[$i]['username'] = $user['username'];
	$usersArray[$i]['id'] = $user['user_id'];
	$usersArray[$i]['x']  = $user['map_x'];
	$usersArray[$i]['y']  = $user['map_y'];
	$usersArray[$i]['sprite']  = $user['map_sprite'];
	$usersArray[$i]['in_battle']  = $user['in_battle'];
	$i++;
}
DB::exQuery("UPDATE `gebruikers` SET `map_num`='{$map}',`map_x`='{$startX}',`map_y`='{$startY}',`map_lastseen`='{$time}' WHERE `user_id`='{$_SESSION['id']}'") or die(mysql_error());

if (!$onMap)
	$numUsersOnMap++;

$mySprite = $gebruiker['map_sprite'];

if($map == 1) $gebied = 'Gras';
elseif($map == 2) $gebied = 'Water';
elseif($map == 3) $gebied = 'Grot';
elseif($map == 4) $gebied = 'Spookhuis';
elseif($map == 5) $gebied = 'Lavagrot';
elseif($map == 6) $gebied = 'Strand';
elseif($map == 7) $gebied = 'Vechtschool';

if($_POST['goid'] != "" && $_POST['level'] != "") {
	if(DB::exQuery("SELECT `id` FROM `pokemon_speler` WHERE `leven`>'0' AND `user_id`='{$_SESSION['id']}' AND opzak='ja'")->num_rows == 0)
		echo '<div class="red">Todos seus pokemon estão desmaiados.</div>';
	else {
		$wid = $_POST['goid'];
        $leveltegenstander = $_POST['level'];
		if($map == 1) $gebied = 'Gras';
		elseif($map == 2) $gebied = 'Water';
		elseif($map == 3) $gebied = 'Grot';
		elseif($map == 4) $gebied = 'Spookhuis';
		elseif($map == 5) $gebied = 'Lavagrot';
		elseif($map == 6) $gebied = 'Strand';
		elseif($map == 7) $gebied = 'Vechtschool';
		if ($wid !== $gebruiker['map_wild']) {
			echo "<div class='red'>Você não achou este Pokémon, ou então você já batalhou com ele!</div>";
		} elseif ($leveltegenstander !== $gebruiker['pokemon_level']) {
			echo "<div class='red'>Não é este o level do pokemon que você encontrou!</div>";
		} else {
			include("attack/wild/wild-start.php");
			$info = create_new_attack($wid, $leveltegenstander, $gebied);
			if (empty($info['bericht'])) {
				$_SESSION['map_live'] = $map;
				$_SESSION['battle'] = true;
				if ($gebied == 'Gras') {
					$chance     = rand(1, 3);
					$background = "gras-" . $chance;
				} else if ($gebied == 'Water') {
					$chance = rand(1, 2);
					$background = "water-" . $chance;
				}
				
				DB::exQuery("UPDATE `gebruikers` SET `pagina`='attack',`background`='$background' WHERE `user_id`='" . $_SESSION['id'] . "'");
				header("Location: ./attack/wild/wild-attack");
				exit;
            } else
                echo "<div class='red'>" . $txt['alert_no_pokemon'] . "</div>";
        }
	}
}

if(isset($_POST['trainer'])){
	if (DB::exQuery("SELECT `id` FROM `pokemon_speler` WHERE `leven`>'0' AND `user_id`='{$_SESSION['id']}' AND opzak='ja'")->num_rows == 0)
		echo '<div class="red">Todos seus pokemon estão desmaiados.</div>';
	else {
		$query = DB::exQuery("SELECT `naam` FROM `trainer` WHERE `badge`='' AND (`gebied`='{$gebied}' OR `gebied`='All') AND (`evento`=0 || (NOW() BETWEEN `inicio` AND `fim`)) ORDER BY rand() limit 1")->fetch_assoc();
		$pokemon_sql->data_seek(0);
		$opzak = $pokemon_sql->num_rows;

		$level = 0;
		while($pokemon = $pokemon_sql->fetch_assoc())
			$level += $pokemon['level'];
		$trainer_ave_level = $level/$opzak;

		//Make Fight
		include('attack/trainer/trainer-start.php');
		$info = create_new_trainer_attack($query['naam'], $trainer_ave_level, $gebied);
		if (empty($info['bericht'])) {
			$_SESSION['map_live'] = $map;
			$_SESSION['battle'] = true;
			header("Location: ./trainer/trainer-attack");
			exit;
		} else
			echo "<div class='red'>" . $txt['alert_no_pokemon'] . "</div>";
	}
}
?>
<script>
	var movements = {
		up        : {up: -1, left:  0},
		left      : {up:  0, left: -1},
		right     : {up:  0, left:  1},
		down      : {up:  1, left:  0}
	};

	var x = <?php echo $startX; ?>, y = <?php echo $startY; ?>;
	<?php echo str_replace(['\r', '\n'], '', $tileArray); ?>

    <?php
        if ($gebruiker['admin'] >= 3) {
            if (isset($_GET['li']) && ctype_digit($_GET['li'])) {
                $li = 'li='.$_GET['li'].'&';
            }
        }
    ?>
	function moveSprite(key) {
		var sprite = document.getElementById('mySprite');
		var newX = x + movements[key]['left'];
		var newY = y + movements[key]['up'];

		if (typeof map[newY] !== "undefined" && typeof map[newY][newX] !== "undefined" && (map[newY][newX] >= 1 || map[newY][newX] > 10000)) {
			x = newX;
			y = newY;
			sprite.style.left = x*16 + 'px';
			sprite.style.top = (y*16)-4 + 'px';

			$("#result").html('<img src="public/images/loading.gif" /></br>Aguarde...');
			$.get('ajax.php?act=map_ajax&<?=$li?>map=<?php echo $map; ?>&x='+x+'&y='+y, function(result) {
				var res = jQuery.parseJSON(result);
				if ( typeof res.name !== "undefined" ) {

					var html = '';
					html += '<p>';
					html += '<span>';
					html += '<div style="margin-left: auto; margin-right: auto; background-image: url(\'public/images/maps/<?php echo $gebied; ?>.png\'); position: relative; background-position: center bottom; background-repeat:no-repeat;">';
					html += '<img src="public/images/pokemon/'+res.id+'.gif" /><br />';
					html += '<font style="text-shadow: 0 0 0.4em #000, 0 0 0.4em #000;color:#fff;"><b>Você encontrou um '+res.name+'!</b></font><br />';
					html += '</div>';
					html += '<font style="text-shadow: 0 0 0.4em #000, 0 0 0.4em #000;color:#fff;"><b>Nível: '+res.level+'</b></font><br />';
					html += '</span>';
					html += '</p>';
					html += '<form action="./eventos&actual=safari&map=<?php echo $map; ?>" method="post">';
					html += '    <input type="hidden" value="'+res.id+'" name="goid" required />';
					html += '    <input type="hidden" value="'+res.level+'" name="level" required />';
					html += '    <input type="submit" value="Atacar!" name="start" class="button"/>';
					html += '</form>';

					$("#result").html(html);
				} else if(res.trainer == 1){
					var html = '';
					html += '<p>';
					html += '<span>';
					html += '<div style="margin-left: auto; margin-right: auto; background-image: url(\'public/images/maps/<?php echo $gebied; ?>.png\'); position: relative; background-position: center bottom; background-repeat:no-repeat;">';
					html += '<img src="public/images/maps/pokemon_trainer.png" /><br />';
					html += '<font style="text-shadow: 0 0 0.4em #000, 0 0 0.4em #000;color:#fff;"><b>Veja, um treinador!</b></font><br />';
					html += '</div>';
					html += '<font style="text-shadow: 0 0 0.4em #000, 0 0 0.4em #000;color:#fff;"><b>Duelar com ele?</b></font><br />';
					html += '</span>';
					html += '</p>';
					html += '<form method="post">';
					html += '    <input type="submit" value="Batalhar!" name="trainer" class="button"/>';
					html += '</form>';
					$("#result").html(html);
				} else {
					$("#result").html('<font class="wrappers"><span><b><?php echo'<a style="color: #ff0000;" href="././profile&player='.$gebruiker['username'].'">'.$gebruiker['username'].'</a>:'; ?> '+res.msg+'</b></span></font>');
				}
			});
		}
		if (map[y][x] >= 10000) {
			var nMap = Math.floor(map[y][x] / 10000);
			var nX   = Math.floor((map[y][x] % 10000) / 100);
			var nY   = Math.floor((map[y][x] % 100));
			window.location = './eventos&actual=safari&map='+nMap+'&x='+nX+'&y='+nY;
		}
		
	}


	var users = <?php echo json_encode($usersArray); ?>;
	function addUsersToMap() {
		for (var i=0; i<users.length; i++) {
			var user = users[i];
			if (user.map_wild > 0) {
				var wild_poke = document.createElement('img');
				wild_poke.src = 'public/images/pokemon/icon/'+user.map_wild+'.gif';
				wild_poke.style.position = 'absolute';
				wild_poke.style.zIndex = 15;
				wild_poke.style.top = ((user.y*16)-4) + 'px';
				wild_poke.style.left = (user.x*16) + 'px';
				wild_poke.title = user.username;
				wild_poke.setAttribute('class', 'trainer-user');
			}
			var image = document.createElement('img');
			image.src = 'public/images/sprites/<?php if($map == 2){ echo 'water/'; ?>'+user.sprite+'<?php }else{ ?>'+user.sprite+'<?php } ?>.png';
			image.title = user.username;
			image.style.position = 'absolute';
			image.style.zIndex = 10;
			image.style.top = ((user.y*16)-4) + 'px';
			image.style.left = (user.x*16) + 'px';

			if (user.map_wild <= 0) {
				image.setAttribute('class', 'trainer-user');
			}

// 			image.onclick = function(user) {
//       			return function() {
//       				//alert(user.username);
//       				var html = '';
//       				html += '<center><img src="public/images/sprites/'+user.sprite+'.png"/> ';
//       				html += '<a href="./profile&player='+user.username+'">'+user.username+'</a> &bull; ';
// 							html += '<a href="./send-message&player='+user.username+'">Enviar Mensagem</a> &bull; ';
//       				html += '<a href="./attack/duel/invite&player='+user.username+'">Duelar</a> &bull; ';
// 							html += '<a href="./buddylist&player='+user.username+'">Add Amigo</a> &bull; ';
//       				html += '<span onclick="document.getElementById(\'pInfo\').style.display=\'none\';">';
//       				html +=     '[Fechar]';
//       				html += '</span>';
      				
//       				document.getElementById('pInfo').style.display = 'table-row';
//       				document.getElementById('info').innerHTML = html;
//       			};
// 			}(user);

			$(image).insertAfter('#mySprite');
			
			if (user.map_wild > 0) {
				$(wild_poke).insertAfter('#mySprite');
			}

		}
	}

// var timer;
// function startTimer() {
// 	timer = setInterval( function () {
// 		$.get('ajax.php?act=map_users&map=<?php echo $map; ?>', function (userData) {
// 		//	alert(userData);
// 			users = JSON.parse(userData);
// 			removeUsersFromMap();
// 			addUsersToMap();
// 		});
// 	}, 3000);
// }	

function removeUsersFromMap() {
	var mapImgs = document.querySelectorAll('#map img:not([id="mySprite"])');
	
	for (var i=0; i<mapImgs.length; i++) {
		mapImgs[i].parentNode.removeChild(mapImgs[i]);
	}
}

function checkKeysUp(evt) {
	if (evt.keyCode == 38) {
		moveSprite('up');
	} else if (evt.keyCode == 37) {
		moveSprite('left');
	} else if (evt.keyCode == 39) {
		moveSprite('right');
	} else if (evt.keyCode == 40) {
		moveSprite('down');
	}
}

function checkKeysDown(evt){
	if (evt.keyCode >= 37 && evt.keyCode <= 40 ) {
		if(evt.preventDefault){
			evt.preventDefault();
		}
		if (evt.stopPropagation) {
			evt.stopPropagation();
		}
	}
}

$(window).on('keyup', function (e) {
	checkKeysUp(e);
}).on('keydown', function (e) {
	checkKeysDown(e);
});


// window.addEventListener('load', function () { addUsersToMap(); startTimer(); }, false);

</script>
<style>
.wrappers span{
 color:#fff;
 text-shadow: 0 0 0.4em #000, 0 0 0.4em #000;
 0 2px 0 #c9c9c9,
 0 3px 0 #bbb,
 0 4px 0 #b9b9b9,
 0 5px 0 #aaa,
 0 6px 1px rgba(0,0,0,.1),
 0 0 5px rgba(0,0,0,.1),
 0 1px 3px rgba(0,0,0,.3),
 0 3px 5px rgba(0,0,0,.2),
 0 5px 10px rgba(0,0,0,.25),
 0 10px 10px rgba(0,0,0,.2),
 0 20px 20px rgba(0,0,0,.15);
}
</style>
<center><div class="box-content" style="width: 100%;"><table class="general" style="width: 100%" cellpadding="0" cellspacing="0">
	<thead><tr>
		<th colspan="3">
			Mapa - <?php echo $mapTitle; ?>
		</th>
	</tr>
	<tr>
		<th colspan="3" style="
    background: #1d2b3e;
    border-bottom: 1px solid #577599;
    text-transform: uppercase;
    box-shadow: 0 -2px 3px rgba(0, 0, 0, .05);
    text-align: center;">
		Treinadores na área: <?php echo $numUsersOnMap; ?>
		</th>
	</tr>
	</thead>
    <tr><td style="padding: 1px;border-right: 1px solid #577599;">
			<center><div style="background-image: url('public/images/maps/kanto/map<?php echo $map; ?>.png'); width: 400px; height: 560px;position: relative;border: 1px solid;border-radius: 5px;margin:10px" id="map">
				<img src="public/images/sprites/<?php if($map == 2){ echo 'water/'.$mySprite.''; }else{ echo $mySprite; }?>.png" id="mySprite" title="Você" style="position: absolute; top: <?php echo (($startY*16)-4); ?>px; left: <?php echo ($startX*16); ?>px; z-index: 2;" />
			</div></center>
		</td>
		<td style="width: 87px;padding: 2px;vertical-align: middle;"><center>
				
				
<div class="menuu" style="width: 87px;margin-top: auto;"><center>
			<a class="map1" title="Mapa Grama" href="./eventos&actual=safari&amp;map=1"><img src="public/images/animation.gif"></a>
			<a class="map2" title="Mapa Agua" href="./eventos&actual=safari&amp;map=2"><img src="public/images/animation.gif"></a>
			<a class="map3" title="Mapa Gruta" href="./eventos&actual=safari&amp;map=3"><img src="public/images/animation.gif"></a>
			<a class="map4" title="Mapa Torre" href="./eventos&actual=safari&amp;map=4"><img src="public/images/animation.gif"></a>
			<a class="map5" title="Mapa Lava" href="./eventos&actual=safari&amp;map=5"><img src="public/images/animation.gif"></a>
			<a class="map6" title="Mapa Praia" href="./eventos&actual=safari&amp;map=6"><img src="public/images/animation.gif"></a>
			<a class="map7" title="Mapa Dojô" href="./eventos&actual=safari&amp;map=7"><img src="public/images/animation.gif"></a>
			<a class="map8" title="Centro Pokémon" href="./pokemoncenter"><img src="public/images/animation.gif"></a>
			<a class="map9" title="Mercado" href="./market&shopitem=balls"><img src="public/images/animation.gif"></a>
		</center></div>
		
		</a></center></td>
		
		
		</center></td>
		<td style="width: 201px;;margin-left: 10px; text-align: center; vertical-align: top;">
		
			<table style="margin: 130px auto 50px auto;">
				<tr>
					<td><img src="public/images/maps/nav.png" alt="" usemap="#m">
							<map name="m" id="m"><area shape="poly" coords="43,43,88,3,-2,2" href="javascript:void(0);" onclick="moveSprite('up');"><area shape="poly" coords="42,44,45,42,89,-1,87,90" href="javascript:void(0);" onclick="moveSprite('right');"><area shape="poly" coords="1,89,43,45,88,89" href="javascript:void(0);" onclick="moveSprite('down');"><area shape="poly" coords="0,3,43,44,1,87" href="javascript:void(0);" onclick="moveSprite('left');"></map></td>
				</tr>
			</table>
			
			<div id="result" style="height:160px;margin: 15px;"><font class="wrappers"><span><b><a style="color: #ff0000;" href="./profile&amp;player=<?=$gebruiker['username']?>"><?=$gebruiker['username']?></a>: Utilize os botões acima ou as setas do teclado para se movimentar!</b></span></font></div>
			
			<tr id="pInfo" style="display: none;">
				<td colspan="3">
					<center><div id="info"></div>
				</td>
				<td>&nbsp;</td>
			</tr>
		</td>
		
			
	</tr>
</table></div>
<script>
$('.menuu a').not('.map<?=$map?>').animate({
	opacity: 0.3
}, 0);
$(document).ready(function() {
	$('.menuu a').not('.map<?=$map?>').hover(function() {
		$(this).stop().animate({
				opacity: 1
			}, 200);
				}, function() {
		$(this).stop().animate({
			opacity: 0.3
			}, 200);
	});
});
</script>