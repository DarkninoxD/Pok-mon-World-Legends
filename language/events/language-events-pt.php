<?php
	######################## Rank up ###################
	$txt['event_rank_up'] = 'Você subiu de rank, seu novo rank é: <b>%s</b>.';
	$txt['event_rank_up_refferal'] = 'Jou refferal, %s, is een rank omhoog gegaan je hebt hiervoor 15 Gold gekregen!';
	######################## Evolve ####################
	$txt['event_is_evolved_in'] = '<b>%s</b> evoluiu para <b>%s</b>!';
	######################## Level up ##################
	$txt['event_is_level_up'] = '<b>%s</b> passou de nível!';
	
	######################## Work ######################
	if ($page == 'work' OR $workvs) {
		$txt['event_work_1_1'] = 'Você excedeu o número máximo vendido! Você ganhou <img src="' . $static_url . '/images/icons/silver.png" width="16" height="16" alt="Silver" class="imglower"> 15!';
		$txt['event_work_1_2_1'] = 'Você ganhou';
		$txt['event_work_1_2_2'] = 'silvers vendendo limonada.';
		$txt['event_work_1_3'] = 'Você não vendeu nenhuma limonada.';
		$txt['event_work_1_4'] = 'Sua limonada estava horrivel.';
		
		$txt['event_work_2_1'] = 'Você trabalhou no mercado por 1 minuto, você ganhou <img src="' . $static_url . '/images/icons/silver.png" width="16" height="16" alt="Silver" class="imglower">';
		$txt['event_work_2_2'] = 'Você não encontrou o mercado.';
		$txt['event_work_2_3'] = 'Você passou o tempo inteiro passeando, você não merece nada.';
		$txt['event_work_2_4'] = 'Você está preso, porque tentou roubar algo.';
		
		$txt['event_work_3_1'] = 'Você entregou todos os documentos,você ganhou <img src="' . $static_url . '/images/icons/silver.png" width="16" height="16" alt="Silver" class="imglower">';
		$txt['event_work_3_2'] = 'Você entregou todos os documentos, você ganhou <img src="' . $static_url . '/images/icons/silver.png" width="16" height="16" alt="Silver" class="imglower">';
		$txt['event_work_3_3'] = 'Você não conseguiu entregar todos documentos.';
		$txt['event_work_3_4'] = 'Está chovendo! Você adora o frio né? Você é preguiçoso demais para trabalhar.';
		
		$txt['event_work_4_1'] = 'Você limpou o centro pokémon, você ganhou <img src="' . $static_url . '/images/icons/silver.png" width="16" height="16" alt="Silver" class="imglower">';
		$txt['event_work_4_2'] = 'Você não limpou direito o centro pokémon.';
		$txt['event_work_4_3'] = 'Chansey não está nada feliz com sua preguiça, você não merece nada!';
		
		$txt['event_work_5_1'] = 'Você ganhou a partida de golf contra a Equipe Rocket! Você ganhou <img src="' . $static_url . '/images/icons/silver.png" width="16" height="16" alt="Silver" class="imglower">';
		$txt['event_work_5_2'] = 'Você perdeu a partida de golf contra a Equipe Rocket.';
		$txt['event_work_5_3'] = 'Você perdeu a partida de golf contra a Equipe Rocket por muito pouco.';
		
		$txt['event_work_6_1'] = 'WOW! Você encontrou um relogio de ouro, você vendeu ele por <img src="' . $static_url . '/images/icons/silver.png" width="16" height="16" alt="Silver" class="imglower">';
		$txt['event_work_6_2'] = 'Você teve sorte e encontrou uma reliquia que você vendeu por <img src="' . $static_url . '/images/icons/silver.png" width="16" height="16" alt="Silver" class="imglower">';
		$txt['event_work_6_3'] = 'Você não encontrou nada de valor.';
		$txt['event_work_6_4'] = 'Você só encontrou um jornal antigo.';
		
		$txt['event_work_7_1'] = 'A demonstração do seu pokémon foi um sucesso! Você ganhou <img src="' . $static_url . '/images/icons/silver.png" width="16" height="16" alt="Silver" class="imglower">';
		$txt['event_work_7_2'] = 'Ninguém gostou da sua demonstração.';
		$txt['event_work_7_3'] = 'Estava chovendo, ninguém parou para assistir sua demonstração.';
		
		$txt['event_work_8_1'] = 'O experimento foi um sucesso! Você ganhou <img src="' . $static_url . '/images/icons/silver.png" width="16" height="16" alt="Silver" class="imglower">';
		$txt['event_work_8_2'] = 'O experimento não deu certo.';
		$txt['event_work_8_3'] = 'Seu pokémon não conseguiu participar do experimento, você não ganhou nada.';
		
		$txt['event_work_9_1'] = 'WOW! Você ganhou muitos silvers, <img src="' . $static_url . '/images/icons/silver.png" width="16" height="16" alt="Silver" class="imglower">';
		$txt['event_work_9_2'] = 'Não foi muito interessante, você ganhou <img src="' . $static_url . '/images/icons/silver.png" width="16" height="16" alt="Silver" class="imglower">';
		$txt['event_work_9_3'] = 'Sua demonstração livre foi um sucesso, você ganhou <img src="' . $static_url . '/images/icons/silver.png" width="16" height="16" alt="Silver" class="imglower">';
		$txt['event_work_9_4'] = 'Seu pokémon estava muito cansado.';
		$txt['event_work_9_5'] = 'Estava chovendo, ninguém parou para assistir sua demonstração.';
		
		$txt['event_work_10_1'] = 'Sucesso! Você ajudou a capturar a Equipe Rocket, você ganhou <img src="' . $static_url . '/images/icons/silver.png" width="16" height="16" alt="Silver" class="imglower">';
		$txt['event_work_10_2'] = 'Você não encontrou a Equipe Rocket.';
		$txt['event_work_10_3'] = 'Você conseguiu capturar a Equipe Rocket porém eles conseguiram escapar.';
		
		$txt['event_work_11_1'] = 'Sucesso! Seu pokémon conseguiu um monte de silvers, <img src="' . $static_url . '/images/icons/silver.png" width="16" height="16" alt="Silver" class="imglower">';
		$txt['event_work_11_2'] = 'Seu pokémon conseguiu para você <img src="' . $static_url . '/images/icons/silver.png" width="16" height="16" alt="Silver" class="imglower">';
		$txt['event_work_11_3'] = 'Seu pokémon amarelou.';
		$txt['event_work_11_4'] = 'Você não teve nenhuma oportunidade para faturar silvers.';
		$txt['event_work_11_5'] = 'Você foi preso pela Oficial Jenny.';
		
		$txt['event_work_12_1'] = 'Você roubou 2 malotes de silvers do cassino! Você ganhou <img src="' . $static_url . '/images/icons/silver.png" width="16" height="16" alt="Silver" class="imglower">';
		$txt['event_work_12_2_1'] = 'Você roubou <img src="' . $static_url . '/images/icons/silver.png" width="16" height="16" alt="Silver" class="imglower">';
		$txt['event_work_12_2_2'] = 'do cassino.';
		$txt['event_work_12_3'] = 'Seu pokémon ficou com muito medo de roubar o cassino.';
		$txt['event_work_12_4'] = 'O alarme do cassino foi disparado, você teve que correr.';
		$txt['event_work_12_5'] = 'Você foi preso pela Oficial Jenny.';
		
		$txt['event_jail_1'] = 'Você agora está';
		$txt['event_jail_2'] = 'minutos e';
		$txt['event_jail_3'] = 'segundos na prisão.';
	}

	######################## Bank ######################
	if ($page == 'bank') {
		$txt['event_gave_you'] = 'enviou para você';
	}
	
	####################### Steal ######################
	else if ($page == 'steal') {
		$txt['event_steal_failed'] = 'tentou te sabotar, mas você era mais forte.';
		$txt['event_steal_jail'] = 'foi para a prisão porque tentou sabotar você.';
		$txt['event_success_stole_1'] = 'sabotou você e conseguiu';
		$txt['event_success_stole_2'] = 'porque seus pokémons eram mais fortes.';
	}
	
	###################### Jail ########################
	else if ($page == 'jail') {
		$txt['event_bust'] = 'você foi preso.';
		$txt['event_bought'] = 'comprou sua saida por';
	}

	######################## Race ######################
	else if ($page == 'race') {
		$txt['event_race_denied'] = 'O pedido para uma corrida foi negado.';
		
		$txt['event_race_won_your'] = 'Você ganhou a corrida! Seu';
		$txt['event_finished_in'] = 'terminou em';
		$txt['event_sec_the'] = 'segs, o';
		$txt['event_from'] = 'de';
		$txt['event_hit_tree_ko'] = 'bateu em uma árvore e desmaiou.';
		$txt['event_sec'] = 'segs.';
		$txt['event_race_lost_your'] = 'Você perdeu a corrida! Seu';
		$txt['event_the'] = 'O';
		$txt['event_race_draw_your'] = 'Empate na corrida! Seu';
		$txt['event_and_the'] = 'e o';
		$txt['event_hit_both_tree'] = 'Ambos bateram em uma árvore e desmaiaram.';
		$txt['event_finished_both_in'] = 'Ambos terminaram em';
	}
	
	######################## Race invite ########################
	else if ($page == 'race-invite') {
		$txt['event_want_race'] = 'deseja correr contra você por';
		$txt['event_accept'] = 'Aceitar';
		$txt['event_deny'] = 'Recusar';
	}
	
	####################### Transferlist ########################
	else if ($page == 'transferlist-box') {
		$txt['event_bought_your'] = 'comprou';
		$txt['event_for'] = 'por';
		$txt['event_silver_from_tf'] = 'silvers no mercado de pokémons.';
	}
	else if ($page == 'register') {
		$txt['refferal_register'] = '%s cadastrou-se com sucesso! Por este motivo você receberá 5 golds!';
	}
?>