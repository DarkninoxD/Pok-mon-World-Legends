<?php
	if (!empty($_SESSION['id'])) {
		$user = DB::exQuery("SELECT * FROM `gebruikers` WHERE `user_id`='".$_SESSION["id"]."'")->fetch_assoc();
?>
<div>
	<table style="width: 100%">
		<tr>
			<td style="width: 50%">
				<div id="npc-section" data-npc="home" style="padding: 5px; width: 95%;">
					<div id="npc-content" style="width: 285px;">
						<h3 style="font-size: 19px; text-transform: none; background-position: left bottom">Bem-vindo(a), <a href="./profile&player=<?=$user['username']?>" style="color: #d25757;"><?=$user['username']?></a>!</h3>
						<p>Hoje é seu <?=$user['antiguidade']?>º dia de conexão!</p>
					</div>
					<div id="npc-image" style="background: url('<?=$static_url?>/images/characters/<?=DB::exQuery('SELECT naam FROM characters ORDER BY RAND() LIMIT 1')->fetch_assoc()['naam'].'/Thumb'?>.png') center center no-repeat;background-size: 100% 100%;width: 93px;height: 96px;margin-left: -5px;position: absolute;"></div>
				</div>
			</td>
			<td style="width: 50%">	
				<div id="npc-section" data-npc="home" style="padding: 5px;width: 97.7%;text-align: center;height: 73px;">
					<div id="npc-content" style="width: 100%">
						<h3 style="font-size: 19px;text-transform: none;">Eventos</h3>
						<p>NENHUM EVENTO DISPONÍVEL!</p>
					</div>
						
				</div>
			</td>
		</tr>
	</table>

		<div class="row">
			<div style="width: 27%;" class="col">
				<div id="npc-section" style="background: url('<?=$static_url?>/images/layout/starProfile.png') no-repeat #34465f; background-position: center;height: 185px; border-top-right-radius: 0; border-bottom-right-radius: 0; border-right: 1px solid #577599;">
					<div id="npc-image" style="background: url('<?=$static_url?>/images/characters/<?=$gebruiker['character']?>/npc.png') center center no-repeat; background-size: 100% 100%; height: 180px;width: 160px;margin-top: 5px;"></div>
				</div>
			</div>
			<div style="width: 73%" class="col">
				<div class="box-content" style="width: 100%; height: 185px;border-top-left-radius: 0;border-bottom-left-radius: 0;">
					<table width="100%" class="general">
						<thead><tr><th colspan="6">Minha equipe</th></tr></thead>
						<tbody><tr>
								<script>
									var $poke_array_id = [];
									var $poke_array_name = [];
									var $poke_array_spe = [];
								</script>

								<td style="padding: 0">
									<div class="main-carousel" style="height: 97px; position: relative">
										<?php
											while($pokemon_profile = $pokemon_sql->fetch_assoc()) {
												$pokemon_profile = pokemonei($pokemon_profile, $txt);
												$of_name = $pokemon_profile['naam'];
												$popup = pokemon_popup($pokemon_profile, $txt);
												$pokemon_profile['naam'] = pokemon_naam($pokemon_profile['naam'], $pokemon_profile['roepnaam'], $pokemon_profile['icon']);
										?>
												<div class="carousel-cell" style="text-align: center; min-width: 200px;">
													<div style="display:table-cell; vertical-align: middle; height: 143px; text-align: left; padding-right: 100px">
														<?='<img id="my_pokes_infos" class="tip_top-middle" title="'.$popup.'" src="' . $static_url . '/'.$pokemon_profile['link'].'" />';?>
														<script id="remove">
															$poke_array_id.push("<?=$pokemon_profile['wild_id']?>");
															$poke_array_name.push("<?=$of_name?>");
															$poke_array_spe.push("<?=$pokemon_profile['naam']?>");

															document.querySelector('#remove').outerHTML = '';
														</script>
													</div>
												</div>
										<?php
											}
										?>
									</div>
									<div style="width: 100%; background: rgba(0, 0, 0, .3); position: relative; bottom: 0; text-align: center; height: 53px; padding-top: 3px; margin-top: -3px; border-bottom-right-radius: 2px;  border-bottom-left-radius: 2px">
										<div style="width: 79%; text-align: center; font-size: 17px; margin-top: 5px; float: left; height: 75%;">
											<h4 id="poke_name" style="margin: 0; color: #eee; font-weight: 500"></h4>
											<a href="./pokedex&poke=1" id="poke_link" style="color: #eee; font-size: 13px"></a>
										</div>
										<div style="float: right; width: 20%; height: 75%; margin-top: 13px; font-size: 13px">
											<a href="./box" style="color: #fff">Ver BOX Pokémon</a>
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
										wrapAround: false
									});

									var flkty = $carousel.data('flickity');

									$carousel.on('select.flickity', function() {
										$poke_link.attr('href', './pokedex&poke='+$poke_array_id[flkty.selectedIndex]);
										$poke_link.html($poke_array_name[flkty.selectedIndex]);

										$poke_name.html($poke_array_spe[flkty.selectedIndex]);
									});

									$poke_link.attr('href', './pokedex&poke='+$poke_array_id[0]);
									$poke_link.html($poke_array_name[0]);

									$poke_name.html($poke_array_spe[0]);

									$car.resize();
								</script>

						</tr></tbody>
					</table>
				</div>
			</div>
		</div>
		<div class="box-content">
			<h3 class="title">NOVIDADES</h3>
			<p>[16/07/2019] - <b>VERSÃO 1.4</b> LANÇADA! CONFIRA AS <b>NOTAS DE ATUALIZAÇÃO</b> NAS <a href="./official-messages">MENSAGENS OFICIAIS</a>!</p>
		</div>
</div>
<?php } else {
	if (!empty($_SESSION['acc_id'])) header('location: ./my_characters');
} 
?>

<?php
	if (empty($_SESSION['acc_id'])) {
		// echo addNPCBox(15, 'Venha jogar Pokémon World Legends!', 'Faça amigos, crie seu próprio clã, capture mais de <b>900</b> variedades Pokémon e seja o melhor do jogo! <br> Cadastre-se e participe desta incrível jornada Pokémon!');
?>

<center>
<form method="post" autocomplete="off" style="height: 230px; padding: 10px; width: 520px; z-index: 10">
	<table width="70%" cellspacing="0" celpadding="0" border="0">
	<?php if (!empty($inlog_error)) echo '<tr><td colspan="2"><div class="red">' . $inlog_error . '</div></td></tr>'; ?>
		<tr>
			<td colspan="2">
				<input type="text" name="username" placeholder="<?=$txt['login_username'];?>:" style="width:99%; height: 40px; margin-bottom: 10px; font-size: 14px" value="<?=$_SESSION['user'];?>" required<?php if ($_GET['page']!='register') { ?> autofocus<?php } ?> />
				<input type="password" name="password" placeholder="<?=$txt['login_password'];?>:" style="width:99%; height: 37px; font-size: 14px" required />
			</td>
		</tr>
		<tr style="font-style: italic;">
			<td style="padding-left: 5px; padding-top: 5px; width: 50%">
				<a href="./activate"><img src="<?=$static_url?>/images/layout/seta1.png" style="margin-right: 3px; vertical-align: 1px;">Ative sua Conta</a>
			</td>
			<td style="width: 50%; text-align: right; padding-top: 5px; padding-right: 10px">
				<a href="./forgot"><img src="<?=$static_url?>/images/layout/seta1.png" style="margin-right: 3px; vertical-align: 1px">Recuperar Conta</a>
			</td>
		</tr>
		<tr>
			<td colspan="2" style="padding-top: 10px">
				<button class="button-rounded ripple" name="login" type="submit" value="login">Começar Aventura!</button>
			</td>
		</tr>
		<tr>
			<td colspan="2" style="font-style: italic; text-align: center; padding-top: 5px">
				Não tem uma conta? <a href="./register" style="color: #6ac7ee; font-weight: bold">CADASTRE-SE</a> agora mesmo!
			</td>
		</tr>
	</table>
</form>
</center>

<?php } ?>
