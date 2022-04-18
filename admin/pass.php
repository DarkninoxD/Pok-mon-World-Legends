<?php
include("app/includes/resources/security.php");

//Admin controle
if ($gebruiker['admin'] < 1) { header('location: ./home'); exit; }


?>

<center>
<form method="post">
    <table width="300" border="0">
        <tr>
          <td><strong>Senha:</strong></td>
          <td><input name="senha" type="text" value="<?php if ($_POST['senha'] != '') echo $_POST['senha']; ?>" class="text_long" maxlength="15"></td>
          <td><input name="submit1" type="submit" value="Crypt!" class="button_mini"></td>
        </tr>
     </table>
</form>
</center>

<?php

if (isset($_POST['senha'])) {
  //Is er wel een ip opgegeven zo ja dan verder
  if ($_POST['senha'] != "") {
    $senhacript = password($_POST['senha']);
    echo "A senha crypt é: ".$senhacript."";
    
    }
}
?>
</table></center>