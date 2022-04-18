<?php
if ($page == 'use_spcitem') {
} else if ($page == 'use_potion') {
} else if ($page == 'use_rarecandy') {
} else if ($page == 'use_stone') {
} else if ($page == 'use_pokemon') {
} else if ($page == 'use_attack') {
} else if ($page == 'use_attack_finish') {
} else if ($page == 'sell-box') {
	$txt['alert_not_your_pokemon']			= 'Cuidado, este pokémon não pertence à você!';
	$txt['alert_beginpokemon']				= 'Você não pode vender seu pokémon inicial!';
	$txt['alert_too_low_rank']				= 'Você não pode vender pokémons!';
	$txt['alert_geb_too_low_rank']			= 'Este treinador não pode efetuar a compra deste pokémon!';
	$txt['alert_no_amount']					= 'Você deve inserir um valor válido!';
	$txt['alert_price_too_less']			= 'O valor não pode ser menor que %s!';
	$txt['alert_price_too_much']			= 'O valor não pode ser maior que %s!';
	$txt['alert_user_dont_exist']			= 'Treinador não encontrado!';
	$txt['alert_pokemon_already_for_sale']	= 'Este pokémon já está à venda!';
	$txt['alert_success_sell']				= 'Pokémon anunciado com sucesso!';

	$txt['pagetitle']	= 'Você tem certeza que deseja colocar %s à venda?';
	$txt['information']	= 'Informações';
	$txt['sell']		= 'Vender';
	$txt['pokemon']		= 'Pokémon';
	$txt['min_silver']	= 'Preço minímo';
	$txt['min_gold']	= 'Preço minímo';
	$txt['level']		= '<b>Nv.</b> %s';
	$txt['gebruiker']	= 'Treinador';
	$txt['price']		= 'Valor';
	$txt['currency']	= 'Moeda';
	$txt['button']		= 'Colocar à venda';
} else if ($page == 'release-box') {
	$txt['alert_not_your_pokemon']			= 'Cuidado, este pokémon não pertence à você!';
	$txt['alert_beginpokemon']				= 'Você não pode soltar seu pokémon inicial!';
	$txt['alert_too_low_rank']				= 'Você não pode soltar pokémons!';
	$txt['alert_success_release']				= 'Pokémon solto com sucesso!';

	$txt['pagetitle']	= 'Você tem certeza que deseja soltar %s?';
	$txt['information']	= 'Informações';
	$txt['pokemon']		= 'Pokémon';
	$txt['level']		= '<b>Nv.</b> %s';
	$txt['button']		= 'Soltar';
	$txt['irreversivel']    = 'Lembre-se que esta ação é irreversível.';
} else if ($page == 'transfer-box') {
	$txt['alert_not_your_pokemon']			= 'Cuidado, este pokémon não pertence à você!';
	$txt['alert_pokeequiped']			= 'Você não pode transferir um pokémon do seu time!';
	$txt['alert_success']				= 'Pokémon transferido com sucesso!';
	$txt['alert_fail']				= 'A box '.$_POST['newbox'].' está cheia!';

	$txt['pagetitle']	= 'Você deseja transferir %s de box?';
	$txt['information']	= 'Informações';
	$txt['pokemon']		= 'Pokémon';
	$txt['level']		= '<b>Nv.</b> %s';
	$txt['button']		= 'Transferir';
	$txt['box1']		= 'Box Atual';
	$txt['box2']		= 'Nova Box';
}
?>