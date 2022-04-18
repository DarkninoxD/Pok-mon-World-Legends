<?php

include("app/includes/resources/security.php");
include("app/classes/Friends.php");

$friends = new Friends();

$value = '';
if (isset($_GET['name']))  $value = 'value="'.$_GET['name'].'"';

$aprox = false;
$success = false;
if (isset($_GET['name'])) {
    if (isset($_GET['like']) && $_GET['like'] == 'true') $aprox = true;
    if (!is_numeric($_GET['subpage'])) $subpage = 1; 
    else $subpage = $_GET['subpage']; 

    if (!$aprox) {
        $number = DB::exQuery ("SELECT * FROM `gebruikers` WHERE username='".$_GET['name']."'")->num_rows;
    } else {
        $number = DB::exQuery ("SELECT * FROM `gebruikers` WHERE username LIKE '".$_GET['name']."%'")->num_rows;
    }

    $max = 20;
    $aantal_paginas = ceil($number / $max); 

    if ($aantal_paginas == 0) $aantal_paginas = 1;   
    $pagina = $subpage * $max - $max; 

    if (!$aprox) {
        $query = DB::exQuery ("SELECT * FROM `gebruikers` WHERE username='".$_GET['name']."' LIMIT ".$pagina.",".$max);
    } else {
        $query = DB::exQuery ("SELECT * FROM `gebruikers` WHERE username LIKE '".$_GET['name']."%' LIMIT ".$pagina.",".$max);
    }

    $success = true;
    $value = 'value="'.$_GET['name'].'"';
}

if (isset($_POST['player']) && ctype_digit($_POST['player'])) {
    $player = $_POST['player'];
    $exists = DB::exQuery ("SELECT `user_id` FROM `gebruikers` WHERE `user_id`='$player'")->num_rows;
    $is_friend = $friends->isFriend($_SESSION['id'], $player);
    $blocklist_1 = explode(',', $gebruiker['blocklist']);
    $blocklist_2 = explode(',', $friends->getInfos($player)['blocklist']);

    if ($exists == 0) {
        echo '<div class="red">Este treinador não existe!</div>';
    } else if ($player == $_SESSION['id']) {
        echo '<div class="red">Você não pode adicionar a si mesmo!</div>';
    } else if ($is_friend) {
        echo '<div class="red">Vocês já são amigos ou há uma solicitação pendente!</div>';
    } else if (in_array($player, $blocklist_1)) { 
        echo '<div class="red">Você bloqueou este treinador!</div>';
    } else if (in_array($_SESSION['id'], $blocklist_2)) { 
        echo '<div class="red">Você foi bloqueado por este treinador!</div>';
    } else {
        $friends->sendSolicitation($_SESSION['id'], $player);
        $quests->setStatus('friend', $_SESSION['id']);
        echo '<div class="green">Solicitação de amizade enviada!</div>';
    }
}

echo addNPCBox (15, 'Adicionar amigos', 'Não há nada mais divertido do que jogar com <b>Amigos</b>! <br>Aqui você pode pesquisar por treinadores em todas as regiões para adicioná-los como amigos! <br>Solicitações que passarem de uma semana serão excluidas automaticamente!');
?>

<div style="min-height: 18px;" class="box-content">
    <div style="padding: 10px">
        <label>
            <span style="font-size: 14px; color: #fff">Treinador:</span> <input type="text" class="text_long" placeholder="Treinador" <?=$value?> name="name" style="width: 78.5%; height:30px; padding: 5px 0 5px 10px; margin-bottom: 5px" maxlength="10" required="">
        </label>
    </div>
    <div style="border-top: 1px solid #577599; padding: 10px">
        <label>
            <input type="checkbox" style="vertical-align: middle" name="like" <?=($aprox)? 'checked' : '';?>> <span style="font-size: 12.5px; color: #fff">Fazer busca aproximada?</span>
        </label>
        <br>
        <button id="search" onclick="search()">Procurar</button>
        <br>
    </div>
</div>

<?php if ($success) { ?>

<style>
    #example td {
        text-align: center;
    }    
</style>

<div class="box-content" style="margin-top: 5px;">
    <table class="general blue" id="example">
        <thead>
            <tr>
                <td><strong>Treinador</strong></td>
                <td><strong>Antiguidade</strong></td>
                <td><strong>Última Visita</strong></td>
                <td><strong>Classificação</strong></td>
                <td class="no-sort"><strong>Status</strong></td>
                <td class="no-sort"><strong>Adicionar Amigo</strong></td>
            </tr>
        </thead>
        <tbody>
            <?php
                while ($q = $query->fetch_assoc()) {
                    $voortgang = $q['rang'];

                    if ($voortgang == 0) {
                        $number = "-";   
                    } else {
                        $number = $voortgang."º";
                    }
                        
                    if ($voortgang == '1') {
                        $medaille = "<img src='".$static_url."/images/icons/plaatsnummereen.png'>";
                    } else if ($voortgang == '2') {
                        $medaille = "<img src='".$static_url."/images/icons/plaatsnummertwee.png'>";
                    } else if ($voortgang == '3') {
                        $medaille = "<img src='".$static_url."/images/icons/plaatsnummerdrie.png'>";
                    } else if ($voortgang > '3' && $voortgang <= '10') {
                        $medaille = "<img src='".$static_url."/images/icons/gold_medaille.png'>"; 
                    } else if ($voortgang > '10' && $voortgang <= '30') {
                        $medaille = "<img src='".$static_url."/images/icons/silver_medaille.png'>";
                    } else if ($voortgang > '30' && $voortgang <= '50') {
                        $medaille = "<img src='".$static_url."/images/icons/bronze_medaille.png'>";
                    } else if ($q['admin'] >= 1) {
                        $number = '';
                        $medaille = "<b><font color='red'>Administrador</font></b>";
                    }
                            
                    if (($q['online'] + 900) > time()) {
                        $plaatje = '<img src="'.$static_url.'/images/icons/status_online.png" title="Online">';
                    } else {
                        $plaatje = '<img src="'.$static_url.'/images/icons/status_offline.png" title="Offline">';
                    }  

                    $is_friend = $friends->isFriend($_SESSION['id'], $q['user_id']);

                    if ($is_friend) {
                        $is_accept = $friends->isAccept($_SESSION['id'], $q['user_id']);
                        if ($is_accept) {
                            $btn = 'Vocês já são amigos!';
                        } else {
                            $btn = 'Aguardando...';
                        }
                    } else {
                        $btn = '<form method="post"><input type="hidden" name="player" value="'.$q['user_id'].'"><button class="btn">Adicionar</button></form>';
                    }

                    echo '<tr><td><a href="./profile&player='.$q['username'].'">'.$q['username'].'</a></td><td>'.$q['antiguidade'].' dias</td><td>'.$q['ultimo_login'].'</td><td style="font-size: 14px">'.$number.' '.$medaille.'</td><td>'.$plaatje.'</td><td>'.$btn.'</td></tr>';
                }
            ?>
        </tbody>
        <?php
		    $base_url = getUrl('/&subpage=[0-9]/');
            if ($aantal_paginas > 1) {
                $links = false;
                $rechts = false;
                echo '<tfoot>';
                echo '<td align="center" colspan="6"><div class="sabrosus">';
                if ($subpage == 1)	echo '<span class="disabled">&laquo;</span>';
                else {
                    $back = $subpage-1;
                    echo '<a href="'.$base_url.'&subpage='.$back.'">&laquo;</a>';
                }
                for($i=1;$i<=$aantal_paginas;++$i) {
                    if (3 >= $i && $subpage == $i)	echo '<span class="current">'.$i.'</span>';
                    else if (3 >= $i && $subpage != $i)	echo '<a href="'.$base_url.'&subpage='.$i.'">'.$i.'</a>';
                    else if ($aantal_paginas-2 < $i && $subpage == $i)	echo '<span class="current">'.$i.'</span>';
                    else if ($aantal_paginas-2 < $i && $subpage != $i)	echo '<a href="'.$base_url.'&subpage='.$i.'">'.$i.'</a>';
                    else {
                        $max = $subpage + 3;
                        $min = $subpage -3;  
                        if ($page == $i)	echo '<span class="current">'.$i.'</span>';
                        else if ($min < $i && $max > $i)	echo '<a href="'.$base_url.'&subpage='.$i.'">'.$i.'</a>';
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
                    echo '<a href="'.$base_url.'&subpage='.$next.'">&raquo;</a>';
                }
                echo '</div></td></tfoot>';
            }
    ?>
    </table>
</div>

<?php } ?>

<script>
    function search () {
        let name = $('input[name="name"]').val();

        if (name != '') {
            let url = '<?=getUrl('/(&like=[true-false]+)/', '/(&name=[A-z]+)/', '/&subpage=[0-9]/')?>';

            if ($('input[name="like"]').is(':checked')) {
                window.location = url+'&name='+name+'&like=true';
            } else {
                window.location = url+'&name='+name;
            }
        }
    }
</script>