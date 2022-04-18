<?php
include("app/includes/resources/security.php");

//if ($gebruiker['admin'] == 1) $senha = "simbol2017";
//else if ($gebruiker['admin'] == 2) $senha = "simbol2017";
//else if ($gebruiker['admin'] == 3) $senha = "simbol2017";

$senha = "egvwl2018";

$captcha = $_POST['captcha'];
if ($captcha != "")
{

if (($captcha) != $senha) {
		echo '<div class="red">A senha de segurança da equipe está errada!</div>';
	
	} else {		
	$_SESSION['equipe'] = 1;
	Header("Location: ./home");
		echo '<div class="green">Senha de segurança correta, você pode continuar!</div>';
   }
   
 }   else {
 
 		echo '<div class="red">Digite a senha de segurança da equipe!</div>';
 		}
?>
<form method="post">
<center><p>Senha de segurança - Equipe World Legends.</p></center>
<center><table width="600" border="0" cellspacing="0" cellpadding="0">
  <tr>
    <td width="280" style="background: url('<?=$static_url?>/images/<?php echo rand(1,15); ?>.png') no-repeat left top; width: 100%; height: 250px;"></td>
    <td width="320" align="left" valign="top"><br /><br />
      <table width="300" border="0" cellspacing="0" cellpadding="0">
 
    <BR><BR><BR><BR>
        <tr>
          <td height="37">Senha de segurança:</td>
          <td><input type="password" name="captcha" class="text_long"/></td>
        </tr>
        <tr>
          <td>&nbsp;</td>
          <td><input type="submit" name="submit" value="OK!" class="button"></td>
        </tr>
      </table></td>
      <br><br>
  </tr>
</table>
</center>
</form>