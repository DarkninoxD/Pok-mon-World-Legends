<?php		
//Script laden zodat je nooit pagina buiten de index om kan laden
include("app/includes/resources/security.php");

//Admin controle
if ($gebruiker['admin'] < 1)
{
  header('location: ./home');
  exit;
}
$contarecordo4 = DB::exQuery("SELECT `valor` FROM `configs` WHERE `config`='recorde_online'")->fetch_assoc();
echo "<font size='3'><center>Recorde de <b>".$contarecordo4['valor']."</b> treinadores online.</center></font><br>";
$contarecordo5 = DB::exQuery("SELECT `acc_id` FROM `rekeningen` WHERE `account_code`='1' AND `aanmeld_datum` LIKE '%".date("Y-m-d")."%'")->num_rows;
echo "<font size='3'><center><b>".$contarecordo5."</b> novas contas hoje.</center></font><br>";
?>
		<center>
                 <table width="500" border="0">
                   <tr>
                     <td width="50"><center><img src="<?=$static_url?>/images/icons/user_admin.png" /></center></td>
                     <td width="130"><a href="./admin/admins">Administrar Equipe (Level 3)</a></td>
                   </tr>
                                      <tr>
                     <td><center><img src="<?=$static_url?>/images/icons/user_ban.png" /></center></td>
                     <td><a href="./admin/pass">Gerar senha CRYPT SIMBOL (Level 1)</a></td>
                   </tr>
                   <tr>
                     <td><center><img src="<?=$static_url?>/images/icons/user_ban.png" /></center></td>
                     <td><a href="./admin/ban-conta">Bloquear Conta (Level 2)</a></td>
                   </tr>
                   <tr>
                     <td><center><img src="<?=$static_url?>/images/icons/user_ban.png" /></center></td>
                     <td><a href="./admin/ban-char">Bloquear Personagem (Level 2)</a></td>
                   </tr>
                   <tr>
                     <td><center><img src="<?=$static_url?>/images/icons/user_view.png" /></center></td>
                     <td><a href="./admin/search-on-ip">Procurar por IP (Level 1)</a></td>
                   </tr>
                   <tr>
                     <td><center><img src="<?=$static_url?>/images/icons/user_ban.png" /></center></td>
                     <td><a href="./admin/ban-ip">Banir IP (Level 2)</a></td>
                   </tr>
                   <tr>
                     <td><center><img src="<?=$static_url?>/images/icons/groep_magnify.png" /></center></td>
                     <td><a href="./admin/more-accounts">Multi contas (Level 1)</a></td>
                   </tr>
                   <tr>
                     <td><center><img src="<?=$static_url?>/images/icons/key_delete.png" /></center></td>
                     <td><a href="./admin/wrong-login">Erros de login (Level 2)</a></td>
                   </tr>
     		          <tr>
                     <td><center><img src="<?=$static_url?>/images/icons/bank.png" /></center></td>
                     <td><a href="./admin/bank">Logs do Banco (Level 1)</a></td>
                   </tr>
                      <tr>
                     <td><center><img src="<?=$static_url?>/images/icons/pokeball.gif" /></center></td>
                     <td><a href="./admin/release">Logs de Soltura (Level 1)</a></td>
                   </tr>    
                     <td><center><img src="<?=$static_url?>/images/icons/pokeball.gif" /></center></td>
                     <td><a href="./admin/battles">Logs de Batalha (Level 1) (Desativado)</a></td>
                   </tr>    
                    <td><center><img src="<?=$static_url?>/images/icons/pokeball.gif" /></center></td>
                     <td><a href="./admin/transfer">Logs de Mercado (Level 1)</a></td>
                   </tr>                                      
                   <tr>
                     <td colspan="2"><div style="padding-top:20px;"></div></td>
                   </tr>
                   <tr>
                     <td><center><img src="<?=$static_url?>/images/icons/gebeurtenis.png" alt="" /></center></td>
                     <td><a href="./admin/change-headline">Msg Headline (Level 1)</a></td>
                   </tr>
                   <tr>
                     <td><center><img src="<?=$static_url?>/images/icons/gebeurtenis.png" alt="" /></center></td>
                     <td><a href="./admin/change-homepage">Pagina inicial (Level 1)</a></td>
                   </tr>
                   <tr>
                     <td><center><img src="<?=$static_url?>/images/icons/comments.png" /></center></td>
                     <td><a href="./admin/messages">Mensagens (Level 2)</a></td>
                   </tr>
                   <tr>
                     <td><center><img src="<?=$static_url?>/images/icons/email.png" /></center></td>
                     <td><a href="./admin/mass-mail">Enviar e-mail para todos(Level 3)</a></td>
                   </tr>
                   <tr>
                     <td><center><img src="<?=$static_url?>/images/icons/email.png" /></center></td>
                     <td><a href="./admin/official-message">Enviar Mensagem Oficial(Level 3)</a></td>
                   </tr>
                   <tr>
                     <td colspan="2"><div style="padding-top:20px;"></div></td>
                   </tr>
                   <!--<tr>
                     <td><center><img src="<?=$static_url?>/images/icons/doneer.png" /></center></td>
                     <td><a href="./admin/pay-list">Doações realizadas (Level 1)</a></td>
                   </tr>-->
                   <tr>
                     <td><center><img src="<?=$static_url?>/images/icons/pokeball.gif" /></center></td>
                     <td><a href="./admin/addpoke">Adicionar Novo Pokémon (Level 3)</a></td>
                   </tr>
                   <tr>
                     <td><center><img src="<?=$static_url?>/images/icons/egg2.gif" /></center></td>
                     <td><a href="./admin/give-egg">Dar ovo inicial (Level 3)</a></td>
                   </tr>
                    <tr>
                     <td><center><img src="<?=$static_url?>/images/icons/gold.png" /></center></td>
                     <td><a href="./admin/promo">Dar moeda promocional (Level 3)</a></td>
                   </tr>
                   
                   <tr>
                   	<td><center><img src="<?=$static_url?>/images/icons/pokeball.gif" /></center></td>
                    <td><a href="./admin/give-pokemon">Dar pokémon (Level 3)</a></td>
                   </tr>        
                    <tr>
                   	<td><center><img src="<?=$static_url?>/images/icons/pokeball.gif" /></center></td>
                    <td><a href="./admin/transfer-pokemon">Transferir pokémon (Prêmio) (Level 1)</a></td>
                   </tr>
				          <tr>
                   	<td><center><img src="<?=$static_url?>/images/icons/silver-gold.png" /></center></td>
                    <td><a href="./admin/massa-donatie">Doação em massa (Level 3)</a></td>
                   </tr>
                   <tr>
                      <td><center><img src="<?=$static_url?>/images/icons/gold.png" /></center></td>
                      <td><a href="./admin/massa-gold">Gold/Silver p/ todos (Level 3)</a></td>
                      </tr>
                      <tr>
                      <td><center><img src="<?=$static_url?>/images/icons/boy.gif" /></center></td>
                      <td><a href="./admin/massa-premium">Premium p/ todos (Level 3)</a></td>
                      </tr>  
                      <tr>
                      <td><center><img src="<?=$static_url?>/images/icons/boy.gif" /></center></td>
                      <td><a href="./admin/premium">Premium p/ treinador (Level 3)</a></td>
                   </tr>      
    
                   <tr>
                     <td colspan="2"><div style="padding-top:20px;"></div></td>
                   </tr>
                                <tr>
                   	<td><center><img src="<?=$static_url?>/images/icons/on-transferlist.gif" /></center></td>
                    <td><a href="./admin/lottery">Loteria (Level 2)</a></td>
                   </tr>
                   <tr>
                   	<td><center><img src="<?=$static_url?>/images/icons/on-transferlist.gif" /></center></td>
                    <td><a href="./admin/tournament">Torneio (Level 2)</a></td>
                   </tr>
                   <tr>
                   	<td><center><img src="<?=$static_url?>/images/icons/on-transferlist.gif" /></center></td>
                    <td><a href="./admin/league">Liga Pokémon (Level 2)</a></td>
                   </tr>
                   <!--<tr>
                   	<td><center><img src="<?=$static_url?>/images/icons/on-transferlist.gif" /></center></td>
                    <td><a href="./admin/reset-accounts">Resetar dia (Level 3)</a></td>
                   </tr>-->
             		 <tr>
                   	<td><center><img src="<?=$static_url?>/images/icons/on-transferlist.gif" /></center></td>
                    <td><a href="./admin/exp">Exp (Level 2)</a></td>
                   </tr>    
                    <tr>
                   	<td><center><img src="<?=$static_url?>/images/icons/on-transferlist.gif" /></center></td>
                    <td><a href="./admin/silver">Silver (Level 2)</a></td>
                   </tr>                   
                 </table>
		</center>