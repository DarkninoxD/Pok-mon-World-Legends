<?php
if ($type_timer == 'pokecenter') {
	$npc = 33;
	$title = 'Tratamento em andamento';
	$text = 'Você deve aguardar enquanto a Enfeimeira Joy cuida de seus pokémons. Assim que o tratamento estiver completo, você poderá continuar sua jornada.<br /><br /><br /><br />
		Fim do tratamento: <b><span class="timer">' . formatTime($wait_time) . '</span></b>';
} else if ($type_timer == 'travel') {
	$npc = 21;
	$title = 'Viajando';
	$text = 'Neste momento você está viajando para <b>' . $gebruiker['wereld'] . '</b>, e existe um pequeno tempo de viagem que você deve aguardar.</b>.<br /><br /><br /><br />
		Fim da viagem: <b><span class="timer">' . formatTime($wait_time) . '</span></b>';
}
echo addNPCBox($npc, $title, $text);
