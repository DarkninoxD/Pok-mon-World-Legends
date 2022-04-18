<?php
include('app/includes/resources/security.php');

$id = $_GET['id'];
if (empty($id) && !is_numeric($id)) header('location: ./clans&action=central');

$infos = $clan->get($id);

if (empty($infos)) {
    header('location: ./clans&action=central');
} else {
    $your_clan = false;
    if (!empty($gebruiker['clan']) && $id == $gebruiker['clan']) $your_clan = true;
?>
<style>
	.u-infos b {
		color: #98a7c7;
	}

	.u-infos {
		border-collapse: separate!important;
		border-spacing: 20px 0!important;
	}

	.u-infos img {
		vertical-align: middle;
	}
</style>

<script src="<?=$static_url?>/javascripts/timeago/jquery.timeago.js"></script>

<table width="100%" border="0" cellpadding="0" cellspacing="0" class="box-content" style="padding: 10px; box-shadow: 0 0 15px #0e0d0d66; border-radius: 4px; margin: 10px 0;">
	<tr>
		<td class="box-content" width="200" valign="top" align="center">
			<div style="height: 185px; border-top-right-radius: 0; border-bottom-right-radius: 0; border-right: 1px solid #577599; padding-top: 27px">
				<div style="background: url('<?=$static_url?>/images/clans_icons/<?=$infos['image']?>.png') center center no-repeat; background-size: 100% 100%; height: 160px;width: 160px;"></div>
			</div>
		</td>
		<td width="500" valign="top">
		<div>
        <table class="no-stripped bordered general u-infos" width="100%" style="margin: 10px">
        <tr>
            <td colspan="2" style="text-align: center"><h3 class="title" style="font-size: 17px; margin-top: -13px; margin-bottom: 3px"><?=$infos['name']?> - <?=$infos['sigla']?></h3></td>
        </tr>
	<tr>
		<td height="20" style="width: 48%"><b>Data de Criação:</b> <?=$infos['date'];?></td>
		<td><b>Membros: </b> <?=$infos['num_members']?>/<?=$infos['max_members']?></td>
	</tr>
    <?php if ($your_clan) { ?>
    <tr>
		<td height="20" style="width: 48%"><b>Silvers:</b> <?=balance_converter($infos['silvers']);?> <img src="<?=$static_url?>/images/icons/silver.png" title="Silvers"></td>
		<td><b>Golds: </b> <?=balance_converter($infos['golds']);?> <img src="<?=$static_url?>/images/icons/gold.png" title="Golds"></td>
	</tr>
    <?php } ?>
	<tr>
		<td height="20"><b>Level: </b> <?=$infos['level'];?>/10</td>
		<td><b>Ranking:</b> <?=$infos['rank_name']?></td>
	</tr>
	<tr>
		<td height="20"><b>Missões Concluídas: </b> <?=$infos['missoes_concluidas'];?></td>
		<td><b>Prêmios: </b> <?=highamount($infos['premios']);?> <img src="<?=$static_url?>/images/icons/silver.png" title="Silvers"></td>
	</tr>
    
    </table></div>
		</td>
	</tr>
</table>

<div class="box-content" style="margin-top: 7px">
    <table class="general" width="100%"><thead><tr><th>APRESENTAÇÃO</th></tr></thead>
        <tr>
            <td style="text-align: center">
                <?php
                    if (!empty($infos['descr'])) echo '<div id="apresentacao">'.ubbcode($infos['descr']).'</div>';
		            else echo '<div id="apresentacao">ESTE CLÃ NÃO TEM APRESENTAÇÃO!</div>';
                ?>
            </td>
        </tr>
    </table>
</div>

<div class="box-content" style="margin-top: 7px">
    <table class="general" width="100%"><thead><tr><th>MEMBROS (<?=$infos['num_members']?>/<?=$infos['max_members']?>)</th></tr></thead>
        <tr>
            <td style="text-align: center; padding:0">
                <table class="general blue" id="example">
                    <thead>
                        <tr>
                            <td><strong>Treinador</strong></td>
                            <td class="no-sort"><strong>Cargo</strong></td>
                            <?php 
                                if ($your_clan) {
                                    echo '<td style="width:150px"><strong>Contribuição em Silvers<strong></td>';
                                    echo '<td><strong>Contribuição em Golds<strong></td>';
                                }
                            ?>
                            <td><strong>Membro desde</strong></td>
                            <td class="no-sort" style="width: 50px"><strong>Status</strong></td>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                            $clan_member = $clan->getMembers($id);
                            while ($q = $clan_member->fetch_assoc()) {
                                $cid = $q['user_id'];
                                
                                $infos = $clan->getInfos($cid, "`username`, `online`")->fetch_assoc();
                                $q['username'] = $infos['username'];
                                $q['online'] = $infos['online'];
                                
                                if (($q['online'] + 900) > time()) {
                                    $plaatje = '<img src="'.$static_url.'/images/icons/status_online.png" title="Online">';
                                } else {
                                    $plaatje = '<img src="'.$static_url.'/images/icons/status_offline.png" title="Offline">';
                                }

                                $priori = array('Dono', 'Moderador', 'Membro');
                                $q['prioridade'] = '<b>'.$priori[$q['prioridade']].'</b>';

                                $q['silvers_contribuicao'] = balance_converter($q['silvers_contribuicao']);
                                $q['golds_contribuicao'] = balance_converter($q['golds_contribuicao']);

                                $quando = '<span><script id="remove">document.write(jQuery.timeago("'.$q['date'].' UTC")); document.getElementById("remove").outerHTML = "";</script></span>';

                                if ($your_clan) {
                                    echo '<tr><td><a href="./profile&player='.$q['username'].'">'.$q['username'].'</a></td><td>'.$q['prioridade'].'</td><td><b>'.$q['silvers_contribuicao'].'</b> <img src="'.$static_url.'/images/icons/silver.png" title="Silvers"></td><td><b>'.$q['golds_contribuicao'].'</b> <img src="'.$static_url.'/images/icons/gold.png" title="Golds"></td><td>'.$quando.'</td><td>'.$plaatje.'</td></tr>';
                                } else {
                                    echo '<tr><td><a href="./profile&player='.$q['username'].'">'.$q['username'].'</a></td><td>'.$q['prioridade'].'</td><td>'.$quando.'</td><td>'.$plaatje.'</td></tr>';
                                }
                            }
                        ?>
                    </tbody>
                </table>
            </td>
        </tr>
    </table>
</div>

<?php
}