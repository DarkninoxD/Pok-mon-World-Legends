<?php
#Script laden zodat je nooit pagina buiten de index om kan laden
require_once("app/includes/resources/security.php");

if (isset($_SESSION['id'])) {
	$have = explode(",", $gebruiker['pok_gezien']);
	$array = count($have);
	
	$zien = explode(",", $gebruiker['pok_bezit']);
	$array2 = count($zien);
}

$totaal = DB::exQuery("SELECT wild_id FROM pokemon_wild")->num_rows;

$pokedexverify = DB::exQuery("SELECT `pokedex` FROM `gebruikers_item` WHERE `user_id`='".$_SESSION['id']."'")->fetch_assoc();
$pokedexhave = $pokedexverify['pokedex'];
		
if ($pokedexhave == 0) {
echo '<div class="red">Compre uma Pokédex!</div>';
} else {
?>
<script type="text/javascript">
	$(document).ready(function() {
		$("#kwd_search").keyup(function() {
			if ( $(this).val() != "") {
				$("#my-table tr").hide();
				$("#my-table td:contains-ci('" + $(this).val() + "')").parent("tr").show();
			} else {
				$("#my-table tr").show();
			}
		});
	});
	$.extend($.expr[":"], {
		"contains-ci": function(elem, i, match, array) {
			return (elem.textContent || elem.innerText || $(elem).text() || "").toLowerCase().indexOf((match[3] || "").toLowerCase()) >= 0;
		}
	});
</script>

<div class="blue">Você já viu <b><?php echo $array; ?></b> Pokémons de <b><?php echo $totaal; ?></b> conhecidos sendo que <b><?php echo $array2; ?></b> já foram capturados por você.</div>

<div class="box-content" style="margin-bottom: 7px">
	<table class="general" style="width: 100%">
		<thead><tr><th colspan="4">Raridades</th></tr></thead>
		<tbody>
			<?php 
				$zh = DB::exQuery("SELECT * FROM `zeldzaamheid`");
				$i = 0;
				while ($z = $zh->fetch_assoc()) {
					$poke = DB::exQuery("SELECT * FROM `pokemon_wild` WHERE `zeldzaamheid`='".$z['id']."' AND `aparece`='sim' ORDER BY real_id");
					if ($i == 0) echo '<tr>';
					echo '<td><select style="width:200px" onchange="window.location = \'./pokedex&poke=\'+$(this).val()">';
					echo '<option selected disabled>'.$z['nome'].'</option>';
					while ($p = $poke->fetch_assoc()) {
						if (isset($_GET['poke'])) {
							if ($p['wild_id'] == $_GET['poke']) {
								$selected = ' selected';
							} else {
								$selected = '';
							}
						}
						echo '<option value="'.$p['wild_id'].'"'.$selected.'>'.$p['naam'].'</option>';
					}
					echo '</select></td>';
					$i++;
					if ($i == 4) {
						echo '</tr>';
						$i = 0;
					}
				}
			?>
		</tbody>
	</table>
</div>

<div class="box-content">
<table class="general" style="width: 100%">
	<thead>
		<?php if (isset($_SESSION['id'])) { ?><tr><th colspan="2" style="text-align: center;">Você já encontrou <?=$array;?> Pokémons de um total de <?=$totaal;?> pokémons!</th></tr><?php } ?>
		<tr>
			<th width="23%">Pokémons</th>
			<th width="70%">Informações</th>
		</tr>
	</thead>
	<tr>
		<td style="padding: 0;">
			<div style="overflow: auto; height: 640px;">
				<center><input type="text" id="kwd_search" style="width: 170px; margin: 5px;" class="input-blue" placeholder="Pesquisar Pokémon"/></center>
				<table class="general blue" id="my-table" style="width: 100%">
				<?php
				$allpokemonsql = DB::exQuery("SELECT wild_id, naam, real_id FROM pokemon_wild where aparece='sim' ORDER BY real_id, wild_id ASC");
				while($allpokemon = $allpokemonsql->fetch_assoc()) {
					$allpokemon['naam_goed'] = computer_naam($allpokemon['naam']);
					$bezit = explode(",", $gebruiker['pok_bezit']);
					if (in_array($allpokemon['wild_id'], $bezit))
						$naam = "<font color='green'>".$allpokemon['real_id'].". ".$allpokemon['naam_goed']."</font>";
					else
						$naam = $allpokemon['real_id'].". ".$allpokemon['naam_goed'];
				?>
					<tr>
						<td><a href="./pokedex&amp;poke=<?=$allpokemon['wild_id'];?>"><?=$naam;?></a></td>
						<td align="center">
					<?php
						$have = explode(",", $gebruiker['pok_gezien']);
						if (in_array($allpokemon['wild_id'], $have))
							echo '<img src="' . $static_url . '/images/icons/pokeball.gif" width="14" height="14" title="'.$txt['have_already'].' '.$allpokemon['naam_goed'].'" />';
						else
							echo '<img src="' . $static_url . '/images/icons/pokeball_black.gif" width="14" height="14" title="'.$txt['have_already'].' '.$allpokemon['naam_goed'].'" />';
					?>
						</td>
					</tr>
		<?php
		}
		?>
				</table>
			</div>
		</td>
		<td style="padding: 0;">
			<div style="overflow: auto; height: 640px;">
				<script type="text/javascript">
					function show_info(pokemon) {
						$('.red').remove();
						if (pokemon == "none")	$("#pokemon_info").html("<div class=\"red\">Escolha um Pokémon</div>");
						else if (pokemon == "undefined") $("#pokemon_info").html();
						else if (pokemon != '') {
							$("#pokemon_info").load("./ajax.php?act=pokemon_info&pokemon=" + pokemon);
						} else	$("#pokemon_info").html()
					}
					<?php if (!empty($_GET['poke']) && is_numeric($_GET['poke'])) { ?>$(document).ready(function() {
						show_info(<?=$_GET['poke'];?>);
					});<?php } ?>
				</script>
				<div id="pokemon_info">
					<div class="red" style="width: 99%; float: right">Escolha um Pokémon!</div>
				</div>
			</div>
		</td>
	</tr>
</table>
</div>
<?php } ?>