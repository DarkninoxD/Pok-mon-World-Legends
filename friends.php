<?php
include("app/includes/resources/security.php");
include("app/classes/Friends.php");

$friends = new Friends();

$msg = 'Logo abaixo estão listados todos os seus amigos Treinadores, caso queira adicionar mais algum clique <a href="./friends-add">AQUI</a>. <br>Há, e não esqueça de sempre honrar os novos treinadores, quando possível.';
echo addNPCBox(11, 'Meus Amigos', $msg);

$query = $friends->query($_SESSION['id']);

$number = $query->num_rows;

$max = 20;
$aantal_paginas = ceil($number / $max);

if (!is_numeric($_GET['subpage'])) $subpage = 1; 
else $subpage = $_GET['subpage']; 

if ($aantal_paginas == 0) $aantal_paginas = 1;   
$pagina = $subpage * $max - $max;

$query = $friends->query($_SESSION['id'], '', $pagina, $max);

if (isset($_POST['id']) && isset($_POST['remove'])) {
    $list = $_POST['id'];

    DB::exQuery("DELETE FROM `friends` WHERE `id`='$list' AND (`uid`='$_SESSION[id]' OR `uid_2`='$_SESSION[id]') AND `accept`='1'");
    echo '<script>window.location = window.location.href</script>';
}

if (isset($_POST['id']) && isset($_POST['accept'])) {
    $list = $_POST['id'];
    $queried = $friends->queried ($list);

    if ($queried['id'] > 0 && $queried['accept'] == 0) {
        $date = date ('Y-m-d H:i:s');
        $uid2 = '';

        if ($queried['uid'] == $_SESSION['id']) {
            $uid2 = $queried['uid2'];
        } else {
            $uid2 = $queried['uid'];
        }

        $username = $gebruiker['username'];

        $event = '<img src="public/images/icons/blue.png" width="16" height="16" class="imglower" /> <a href="./profile&player='.$username.'">'.$username.'</a> aceitou sua solicitação de amizade.';

        DB::exQuery("INSERT INTO gebeurtenis (`datum`,`ontvanger_id`,`bericht`,`gelezen`) VALUES (NOW(), '" . $uid2 . "', '" . $event . "', '0')");
        
        DB::exQuery("UPDATE `friends` SET `accept`='1',`date`='$date' WHERE `id`='$list' AND (`uid`='$_SESSION[id]' OR `uid_2`='$_SESSION[id]')");
        
        echo '<script>window.location = window.location.href</script>';
    }
}

if (isset($_POST['id']) && isset($_POST['decline'])) {
    $list = $_POST['id'];
    
    DB::exQuery("DELETE FROM `friends` WHERE `id`='$list' AND (`uid`='$_SESSION[id]' OR `uid_2`='$_SESSION[id]') AND `accept`='0'");
    echo '<div class="green">Solicitação recusada.</div>';
}
?>

<script src="<?=$static_url?>/javascripts/timeago/jquery.timeago.js"></script>

<div class="box-content" style="margin-top: 5px; text-align: center">
    <table class="general blue" id="example">
        <thead>
            <tr>
                <td><strong>Treinador</strong></td>
                <td><strong>Última Visita</strong></td>
                <td><strong>Amigos desde</strong></td>
                <td class="no-sort" style="width: 50px"><strong>Status</strong></td>
                <td class="no-sort" style="width: 230px"><strong>Ações</strong></td>
            </tr>
        </thead>
        <tbody>
            <?php
                while ($q = $query->fetch_assoc()) {
                    $id = '';
                    if ($q['uid'] == $_SESSION['id']) {
                        $id = $q['uid_2'];
                    } else {
                        $id = $q['uid'];
                    }
                    
                    $infos = $friends->getInfos($id);
                    $q['username'] = $infos['username'];
                    $q['ultimo_login'] = $infos['ultimo_login'];
                    $q['online'] = $infos['online'];
                    $q['rank'] = $infos['rank'];
                    
                    if (($q['online'] + 900) > time()) {
                        $plaatje = '<img src="'.$static_url.'/images/icons/status_online.png" title="Online">';
                    } else {
                        $plaatje = '<img src="'.$static_url.'/images/icons/status_offline.png" title="Offline">';
                    }

                    if ($q['accept'] == 0) {
                        $quando = 'AGUARDANDO...';
                        if ($q['uid'] != $_SESSION['id']) {
                            $btn = '<form method="post" style="display: inline-block; width: 47%"><input type="hidden" name="id" value="'.$q['id'].'"><input type="submit" name="accept" value="Aceitar"></form><form method="post" style="display: inline-block; width: 47%"><input type="hidden" name="id" value="'.$q['id'].'"><input type="submit" name="decline" value="Recusar"></form>';
                        } else {
                            $btn = 'AGUARDANDO...';
                        }
                    } else {
                        $quando = '<span><script id="remove">document.write(jQuery.timeago("'.$q['date'].' UTC")); document.getElementById("remove").outerHTML = "";</script></span>';
                        $btn = '<a href="./inbox&action=send&player='.$q['username'].'" class="noanimate"><img src="'.$static_url.'/images/icons/berichtsturen.png" title="Enviar Mensagem" class="icon-img"/></a>
                                    <a href="./blocklist&player='.$q['username'].'" class="noanimate"><img src="'.$static_url.'/images/icons/blokkeer.png" title="Bloquear Treinador" class="icon-img"/></a>
                                    <a href="./bank&player='.$q['username'].'" class="noanimate"><img src="'.$static_url.'/images/icons/bank.png" title="Transferir Valores" class="icon-img"/></a>';
                        $btn .= (($gebruiker['rank'] >= 4) && ($gebruiker['in_hand'] != 0) && ($q['rank'] >= 4))? '<a href="./attack/duel/invite&player='.$q['username'].'" class="noanimate"><img src="' . $static_url . '/images/icons/duel.png" title="Desafiar Treinador para Duelo" class="icon-img"/></a>' : '';
                        $btn .= '<form method="post" id="remove-form-'.$q['id'].'" onsubmit="return confirm(\'Realmente deseja excluir '.$q['username'].' da sua lista de amigos?\');" style="display: inline-block;"><input type="hidden" name="id" value="'.$q['id'].'"><input type="hidden" name="remove" value="1"><img src="'.$static_url.'/images/icons/delete.png" title="Remover da Lista de Amigos" onclick="$(\'#remove-form-'.$q['id'].'\').submit()" class="icon-img" style="cursor:pointer;margin-left:3px"/></form>';
                    }

                    echo '<tr><td><a href="./profile&player='.$q['username'].'">'.$q['username'].'</a></td><td>'.$q['ultimo_login'].'</td><td>'.$quando.'</td><td>'.$plaatje.'</td><td>'.$btn.'</td></tr>';
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