<?php
#Script laden zodat je nooit pagina buiten de index om kan laden
include("app/includes/resources/security.php");

#Admin controle
if ($gebruiker['admin'] < 3) { header('location: ./home'); exit; }
 
 
 if ($_POST) {
 
 //INSERE POKEMON
 
 DB::exQuery("INSERT INTO `pokemon_wild`(`wild_id`, `wereld`, `naam`, `zeldzaamheid`, `evolutie`, `type1`, `type2`, `gebied`, `vangbaarheid`, `groei`, `base_exp`, `aanval_1`, `aanval_2`, `aanval_3`, `aanval_4`, `attack_base`, `defence_base`, `spc.attack_base`, `spc.defence_base`, `speed_base`, `hp_base`, `effort_attack`, `effort_defence`, `effort_spc.attack`, `effort_spc.defence`, `effort_speed`, `effort_hp`, `aparece`, `lendario`, `comerciantes`) VALUES 
 ('".$_POST['id']."','".$_POST['zona']."','".$_POST['nome']."','".$_POST['raridade']."','".$_POST['evolutie']."','".$_POST['type1']."','".$_POST['type2']."','".$_POST['local']."','".$_POST['captura']."','".$_POST['exp']."','".$_POST['baseexp']."','".$_POST['atack1']."','".$_POST['atack2']."','".$_POST['atack3']."','".$_POST['atack4']."','".$_POST['atkbase']."','".$_POST['defbase']."','".$_POST['spatkbase']."','".$_POST['spdefbase']."','".$_POST['speedbase']."','".$_POST['hpbase']."','".$_POST['effortatk']."','".$_POST['effortdef']."','".$_POST['effortspatk']."','".$_POST['effortspdef']."','".$_POST['effortspeed']."','".$_POST['efforthp']."','".$_POST['aparece']."','".$_POST['lendario']."','".$_POST['comerciantes']."')");
 
 //INSERE POKEMON
 
 
 
 $_POST['id'] = DB::insertID();
 

 // INSERE MOVE UP LIST
 
 
if ($_POST['lvlupatk1'] != "" AND $_POST['upatk1'] != "") DB::exQuery("INSERT INTO `levelen`(`level`, `stone`, `trade`, `wild_id`, `wat`, `nieuw_id`, `aanval`) VALUES ('".$_POST['lvlupatk1']."','','0','".$_POST['id']."','att','0','".$_POST['upatk1']."')");
 
if ($_POST['lvlupatk2'] != "" AND $_POST['upatk2'] != "") DB::exQuery("INSERT INTO `levelen`(`level`, `stone`, `trade`, `wild_id`, `wat`, `nieuw_id`, `aanval`) VALUES ('".$_POST['lvlupatk2']."','','0','".$_POST['id']."','att','0','".$_POST['upatk2']."')");
 
if ($_POST['lvlupatk3'] != "" AND $_POST['upatk3'] != "") DB::exQuery("INSERT INTO `levelen`(`level`, `stone`, `trade`, `wild_id`, `wat`, `nieuw_id`, `aanval`) VALUES ('".$_POST['lvlupatk3']."','','0','".$_POST['id']."','att','0','".$_POST['upatk3']."')");

if ($_POST['lvlupatk4'] != "" AND $_POST['upatk4'] != "") DB::exQuery("INSERT INTO `levelen`(`level`, `stone`, `trade`, `wild_id`, `wat`, `nieuw_id`, `aanval`) VALUES ('".$_POST['lvlupatk4']."','','0','".$_POST['id']."','att','0','".$_POST['upatk4']."')");
 
if ($_POST['lvlupatk5'] != "" AND $_POST['upatk5'] != "") DB::exQuery("INSERT INTO `levelen`(`level`, `stone`, `trade`, `wild_id`, `wat`, `nieuw_id`, `aanval`) VALUES ('".$_POST['lvlupatk5']."','','0','".$_POST['id']."','att','0','".$_POST['upatk5']."')"); 

if ($_POST['lvlupatk6'] != "" AND $_POST['upatk6'] != "") DB::exQuery("INSERT INTO `levelen`(`level`, `stone`, `trade`, `wild_id`, `wat`, `nieuw_id`, `aanval`) VALUES ('".$_POST['lvlupatk6']."','','0','".$_POST['id']."','att','0','".$_POST['upatk6']."')"); 

if ($_POST['lvlupatk7'] != "" AND $_POST['upatk7'] != "") DB::exQuery("INSERT INTO `levelen`(`level`, `stone`, `trade`, `wild_id`, `wat`, `nieuw_id`, `aanval`) VALUES ('".$_POST['lvlupatk7']."','','0','".$_POST['id']."','att','0','".$_POST['upatk7']."')"); 

if ($_POST['lvlupatk8'] != "" AND $_POST['upatk8'] != "") DB::exQuery("INSERT INTO `levelen`(`level`, `stone`, `trade`, `wild_id`, `wat`, `nieuw_id`, `aanval`) VALUES ('".$_POST['lvlupatk8']."','','0','".$_POST['id']."','att','0','".$_POST['upatk8']."')"); 

if ($_POST['lvlupatk9'] != "" AND $_POST['upatk9'] != "") DB::exQuery("INSERT INTO `levelen`(`level`, `stone`, `trade`, `wild_id`, `wat`, `nieuw_id`, `aanval`) VALUES ('".$_POST['lvlupatk9']."','','0','".$_POST['id']."','att','0','".$_POST['upatk9']."')"); 

if ($_POST['lvlupatk10'] != "" AND $_POST['upatk10'] != "") DB::exQuery("INSERT INTO `levelen`(`level`, `stone`, `trade`, `wild_id`, `wat`, `nieuw_id`, `aanval`) VALUES ('".$_POST['lvlupatk10']."','','0','".$_POST['id']."','att','0','".$_POST['upatk10']."')"); 

if ($_POST['lvlupatk11'] != "" AND $_POST['upatk11'] != "") DB::exQuery("INSERT INTO `levelen`(`level`, `stone`, `trade`, `wild_id`, `wat`, `nieuw_id`, `aanval`) VALUES ('".$_POST['lvlupatk11']."','','0','".$_POST['id']."','att','0','".$_POST['upatk11']."')"); 

if ($_POST['lvlupatk12'] != "" AND $_POST['upatk12'] != "") DB::exQuery("INSERT INTO `levelen`(`level`, `stone`, `trade`, `wild_id`, `wat`, `nieuw_id`, `aanval`) VALUES ('".$_POST['lvlupatk12']."','','0','".$_POST['id']."','att','0','".$_POST['upatk12']."')"); 

if ($_POST['lvlupatk13'] != "" AND $_POST['upatk13'] != "") DB::exQuery("INSERT INTO `levelen`(`level`, `stone`, `trade`, `wild_id`, `wat`, `nieuw_id`, `aanval`) VALUES ('".$_POST['lvlupatk13']."','','0','".$_POST['id']."','att','0','".$_POST['upatk13']."')"); 

if ($_POST['lvlupatk14'] != "" AND $_POST['upatk14'] != "") DB::exQuery("INSERT INTO `levelen`(`level`, `stone`, `trade`, `wild_id`, `wat`, `nieuw_id`, `aanval`) VALUES ('".$_POST['lvlupatk14']."','','0','".$_POST['id']."','att','0','".$_POST['upatk14']."')"); 

if ($_POST['lvlupatk15'] != "" AND $_POST['upatk15'] != "") DB::exQuery("INSERT INTO `levelen`(`level`, `stone`, `trade`, `wild_id`, `wat`, `nieuw_id`, `aanval`) VALUES ('".$_POST['lvlupatk15']."','','0','".$_POST['id']."','att','0','".$_POST['upatk15']."')"); 

if ($_POST['lvlupatk16'] != "" AND $_POST['upatk16'] != "") DB::exQuery("INSERT INTO `levelen`(`level`, `stone`, `trade`, `wild_id`, `wat`, `nieuw_id`, `aanval`) VALUES ('".$_POST['lvlupatk16']."','','0','".$_POST['id']."','att','0','".$_POST['upatk16']."')"); 

if ($_POST['lvlupatk17'] != "" AND $_POST['upatk17'] != "") DB::exQuery("INSERT INTO `levelen`(`level`, `stone`, `trade`, `wild_id`, `wat`, `nieuw_id`, `aanval`) VALUES ('".$_POST['lvlupatk17']."','','0','".$_POST['id']."','att','0','".$_POST['upatk17']."')"); 

if ($_POST['lvlupatk18'] != "" AND $_POST['upatk18'] != "") DB::exQuery("INSERT INTO `levelen`(`level`, `stone`, `trade`, `wild_id`, `wat`, `nieuw_id`, `aanval`) VALUES ('".$_POST['lvlupatk18']."','','0','".$_POST['id']."','att','0','".$_POST['upatk18']."')"); 

if ($_POST['lvlupatk19'] != "" AND $_POST['upatk19'] != "") DB::exQuery("INSERT INTO `levelen`(`level`, `stone`, `trade`, `wild_id`, `wat`, `nieuw_id`, `aanval`) VALUES ('".$_POST['lvlupatk19']."','','0','".$_POST['id']."','att','0','".$_POST['upatk19']."')"); 

if ($_POST['lvlupatk20'] != "" AND $_POST['upatk20'] != "") DB::exQuery("INSERT INTO `levelen`(`level`, `stone`, `trade`, `wild_id`, `wat`, `nieuw_id`, `aanval`) VALUES ('".$_POST['lvlupatk20']."','','0','".$_POST['id']."','att','0','".$_POST['upatk20']."')"); 




 
 
 
 // INSERE MOVE UP LIST
 
 
 
 
    foreach($_POST['movetutor'] as $check) {
   
    $pegasobre = DB::exQuery("select * from tmhm_movetutor where naam='".$check."'")->fetch_assoc();
  
    $rel = "".$pegasobre['relacionados'].",".$_POST['id']."";
  
    DB::exQuery("UPDATE `tmhm_movetutor` SET `relacionados`='$rel' WHERE naam='".$check."'");
  
  
    }
    
    
    
      foreach($_POST['relacionados'] as $check2) {
      
      $pegasobre2 = DB::exQuery("select * from tmhm_relacionados where naam='".$check2."'")->fetch_assoc();
      
      $rel = "".$pegasobre2['relacionados'].",".$_POST['id']."";
          
      DB::exQuery("UPDATE `tmhm_relacionados` SET `relacionados`='$rel' WHERE naam='".$check2."'");     
          
          
    }
 }
 
?>

<h3>Inclusão de Pokémon</h3>


<form method="post">



<table>
<tr><td>ID</td> <td><input type="number" value="<?php echo $_POST['id']; ?>" id="id" name="id" required></td></tr>


<tr><td>Zona</td> <td><select name="zona"><option value="Kanto">Kanto</option><option value="Johto">Johto</option><option value="Hoenn">Hoenn</option><option value="Sinnoh">Sinnoh</option><option value="Unova">Unova</option><option value="Kalos">Kalos</option><option value="Alola">Alola</option> </select></td></tr>


<tr><td>Nome</td> <td><input type="text" value="<?php echo $_POST['nome']; ?>" id="nome" name="nome" required></td></tr>

<tr><td>Raridade</td> <td><select name="raridade"><option value="1">Comum</option><option value="2">Incomum</option><option value="3">Raro</option> </select></td></tr>


<tr><td>N. Linha Ev.</td> <td><input type="number" value="<?php echo $_POST['evolutie']; ?>" id="evolutie" name="evolutie" required> Número na linha evolutiva do pokémon</td></tr>




<tr><td>Type 1</td> <td>
<select name="type1">
<option value="Normal">Normal</option>
<option value="Fire">Fire</option>
<option value="Fighting">Fighting</option>
<option value="Water">Water</option>
<option value="Flying">Flying</option>
<option value="Grass">Grass</option>
<option value="Poison">Poison</option>
<option value="Electric">Electric</option>
<option value="Ground">Ground</option>
<option value="Psychic">Psychic</option>
<option value="Rock">Rock</option>
<option value="Ice">Ice</option>
<option value="Bug">Bug</option>
<option value="Dragon">Dragon</option>
<option value="Ghost">Ghost</option>
<option value="Dark">Dark</option>
<option value="Steel">Steel</option>
<option value="Fairy">Fairy</option>
</select>

</td></tr>






<tr><td>Type 2</td> <td>
<select name="type2">
<option value=""></option>
<option value="Normal">Normal</option>
<option value="Fire">Fire</option>
<option value="Fighting">Fighting</option>
<option value="Water">Water</option>
<option value="Flying">Flying</option>
<option value="Grass">Grass</option>
<option value="Poison">Poison</option>
<option value="Electric">Electric</option>
<option value="Ground">Ground</option>
<option value="Psychic">Psychic</option>
<option value="Rock">Rock</option>
<option value="Ice">Ice</option>
<option value="Bug">Bug</option>
<option value="Dragon">Dragon</option>
<option value="Ghost">Ghost</option>
<option value="Dark">Dark</option>
<option value="Steel">Steel</option>
<option value="Fairy">Fairy</option>
</select>

</td></tr>




<tr><td>Local</td> <td>
<select name="local">
<option value=""></option>
<option value="Gras">Grama</option>
<option value="Grot">Caverna</option>
<option value="Lavagrot">Lava</option>
<option value="Strand">Areia/Praia</option>
<option value="Spookhuis">Torre Assombrada</option>
<option value="Vechtschool">Dojo</option>
<option value="Water">Agua</option>
<option value="Promo">Promo</option>
<option value="Mega">Mega</option>
<option value="Primal">Primal</option>
</select>

</td></tr>



<tr><td>% Captura</td> <td><input type="number" value="<?php echo $_POST['captura']; ?>" id="captura" name="captura" required></td></tr>


<tr><td>Grupo de EXP</td> <td><input type="text" value="<?php echo $_POST['exp']; ?>" id="exp" name="exp" required> Ex:  Medium Slow / Slow</td></tr>

<tr><td>Base EXP</td> <td><input type="number" value="<?php echo $_POST['baseexp']; ?>" id="baseexp" name="baseexp" required></td></tr>


<tr><td>Ataque 1</td> <td><input type="text" value="<?php echo $_POST['atack1']; ?>" id="atack1" name="atack1" required></td></tr>
<tr><td>Ataque 2</td> <td><input type="text" value="<?php echo $_POST['atack2']; ?>" id="atack2" name="atack2" required></td></tr>
<tr><td>Ataque 3</td> <td><input type="text" value="<?php echo $_POST['atack3']; ?>" id="atack3" name="atack3" required></td></tr>
<tr><td>Ataque 4</td> <td><input type="text" value="<?php echo $_POST['atack4']; ?>" id="atack4" name="atack4" required></td></tr>










<tr><td>ATK Base</td> <td><input type="number" value="<?php echo $_POST['atkbase']; ?>" id="atkbase" name="atkbase" required></td></tr>
<tr><td>DEF Base</td> <td><input type="number" value="<?php echo $_POST['defbase']; ?>" id="defbase" name="defbase" required></td></tr>
<tr><td>SP.ATK Base</td> <td><input type="number" value="<?php echo $_POST['spatkbase']; ?>" id="spatkbase" name="spatkbase" required></td></tr>
<tr><td>SP.DEF Base</td> <td><input type="number" value="<?php echo $_POST['spdefbase']; ?>" id="spdefbase" name="spdefbase" required></td></tr>
<tr><td>SPEED Base</td> <td><input type="number" value="<?php echo $_POST['speedbase']; ?>" id="speedbase" name="speedbase" required></td></tr>
<tr><td>HP Base</td> <td><input type="number" value="<?php echo $_POST['hpbase']; ?>" id="hpbase" name="hpbase" required></td></tr>



<tr><td>Effort ATK</td> <td><input type="number" value="<?php echo $_POST['effortatk']; ?>" id="effortatk" name="effortatk" required></td></tr>
<tr><td>Effort DEF</td> <td><input type="number" value="<?php echo $_POST['effortdef']; ?>" id="effortdef" name="effortdef" required></td></tr>
<tr><td>Effort SP.ATK</td> <td><input type="number" value="<?php echo $_POST['effortspatk']; ?>" id="effortspatk" name="effortspatk" required></td></tr>
<tr><td>Effort SP.DEF</td> <td><input type="number" value="<?php echo $_POST['effortspdef']; ?>" id="effortspdef" name="effortspdef" required></td></tr>
<tr><td>Effort SPEED</td> <td><input type="number" value="<?php echo $_POST['effortspeed']; ?>" id="effortspeed" name="effortspeed" required></td></tr>
<tr><td>Effort HP</td> <td><input type="number" value="<?php echo $_POST['efforthp']; ?>" id="efforthp" name="efforthp" required></td></tr>


<tr><td>Aparece nas infos?</td> <td><select name="aparece"><option value="sim">Sim</option><option value="nao">Não</option> </select></td></tr>
<tr><td>Lendario/Raro?</td> <td><select name="lendario"><option value="1">Sim</option><option value="0">Não</option> </select></td></tr>
<tr><td>Comerciantes?</td> <td><select name="comerciantes"><option value="sim">Sim</option><option value="nao">Não</option> </select></td></tr>





<tr><td><h1>MOVE UPS</h1></td></tr>





<tr><td><input type="number" value="<?php echo $_POST['lvlupatk1']; ?>" id="lvlupatk1" name="lvlupatk1" placeholder="LvL"></td>
 <td><input type="upatk1" value="<?php echo $_POST['upatk1']; ?>" id="upatk1" name="upatk1" placeholder="Ataque"></td></tr>
 
 
 
 
 
 
<tr><td><input type="number" value="<?php echo $_POST['lvlupatk2']; ?>" id="lvlupatk2" name="lvlupatk2" placeholder="LvL"></td>
 <td><input type="upatk2" value="<?php echo $_POST['upatk2']; ?>" id="upatk2" name="upatk2" placeholder="Ataque"></td></tr>

<tr><td><input type="number" value="<?php echo $_POST['lvlupatk3']; ?>" id="lvlupatk3" name="lvlupatk3" placeholder="LvL"></td>
 <td><input type="upatk3" value="<?php echo $_POST['upatk3']; ?>" id="upatk3" name="upatk3" placeholder="Ataque"></td></tr>

<tr><td><input type="number" value="<?php echo $_POST['lvlupatk4']; ?>" id="lvlupatk4" name="lvlupatk4" placeholder="LvL"></td>
 <td><input type="upatk4" value="<?php echo $_POST['upatk4']; ?>" id="upatk4" name="upatk4" placeholder="Ataque"></td></tr>

<tr><td><input type="number" value="<?php echo $_POST['lvlupatk5']; ?>" id="lvlupatk5" name="lvlupatk5" placeholder="LvL"></td>
 <td><input type="upatk5" value="<?php echo $_POST['upatk5']; ?>" id="upatk5" name="upatk5" placeholder="Ataque"></td></tr>

<tr><td><input type="number" value="<?php echo $_POST['lvlupatk6']; ?>" id="lvlupatk6" name="lvlupatk6" placeholder="LvL"></td>
 <td><input type="upatk6" value="<?php echo $_POST['upatk6']; ?>" id="upatk6" name="upatk6" placeholder="Ataque"></td></tr>
 
<tr><td><input type="number" value="<?php echo $_POST['lvlupatk7']; ?>" id="lvlupatk7" name="lvlupatk7" placeholder="LvL"></td>
 <td><input type="upatk7" value="<?php echo $_POST['upatk7']; ?>" id="upatk7" name="upatk7" placeholder="Ataque"></td></tr>

<tr><td><input type="number" value="<?php echo $_POST['lvlupatk8']; ?>" id="lvlupatk8" name="lvlupatk8" placeholder="LvL"></td>
 <td><input type="upatk8" value="<?php echo $_POST['upatk8']; ?>" id="upatk8" name="upatk8" placeholder="Ataque"></td></tr>

<tr><td><input type="number" value="<?php echo $_POST['lvlupatk9']; ?>" id="lvlupatk9" name="lvlupatk9" placeholder="LvL"></td>
 <td><input type="upatk9" value="<?php echo $_POST['upatk9']; ?>" id="upatk9" name="upatk9" placeholder="Ataque"></td></tr>

<tr><td><input type="number" value="<?php echo $_POST['lvlupatk10']; ?>" id="lvlupatk10" name="lvlupatk10" placeholder="LvL"></td>
 <td><input type="upatk10" value="<?php echo $_POST['upatk10']; ?>" id="upatk10" name="upatk10" placeholder="Ataque"></td></tr>

<tr><td><input type="number" value="<?php echo $_POST['lvlupatk11']; ?>" id="lvlupatk11" name="lvlupatk11" placeholder="LvL"></td>
 <td><input type="upatk11" value="<?php echo $_POST['upatk11']; ?>" id="upatk11" name="upatk11" placeholder="Ataque"></td></tr>
 
<tr><td><input type="number" value="<?php echo $_POST['lvlupatk12']; ?>" id="lvlupatk12" name="lvlupatk12" placeholder="LvL"></td>
 <td><input type="upatk12" value="<?php echo $_POST['upatk12']; ?>" id="upatk12" name="upatk12" placeholder="Ataque"></td></tr>

<tr><td><input type="number" value="<?php echo $_POST['lvlupatk13']; ?>" id="lvlupatk13" name="lvlupatk13" placeholder="LvL"></td>
 <td><input type="upatk13" value="<?php echo $_POST['upatk13']; ?>" id="upatk13" name="upatk13" placeholder="Ataque"></td></tr>

<tr><td><input type="number" value="<?php echo $_POST['lvlupatk14']; ?>" id="lvlupatk14" name="lvlupatk14" placeholder="LvL"></td>
 <td><input type="upatk14" value="<?php echo $_POST['upatk14']; ?>" id="upatk14" name="upatk14" placeholder="Ataque"></td></tr>

<tr><td><input type="number" value="<?php echo $_POST['lvlupatk15']; ?>" id="lvlupatk15" name="lvlupatk15" placeholder="LvL"></td>
 <td><input type="upatk15" value="<?php echo $_POST['upatk15']; ?>" id="upatk15" name="upatk15" placeholder="Ataque"></td></tr>

<tr><td><input type="number" value="<?php echo $_POST['lvlupatk16']; ?>" id="lvlupatk16" name="lvlupatk16" placeholder="LvL"></td>
 <td><input type="upatk16" value="<?php echo $_POST['upatk16']; ?>" id="upatk16" name="upatk16" placeholder="Ataque"></td></tr>
 
<tr><td><input type="number" value="<?php echo $_POST['lvlupatk17']; ?>" id="lvlupatk17" name="lvlupatk17" placeholder="LvL"></td>
 <td><input type="upatk17" value="<?php echo $_POST['upatk17']; ?>" id="upatk17" name="upatk17" placeholder="Ataque"></td></tr>

<tr><td><input type="number" value="<?php echo $_POST['lvlupatk18']; ?>" id="lvlupatk18" name="lvlupatk18" placeholder="LvL"></td>
 <td><input type="upatk18" value="<?php echo $_POST['upatk18']; ?>" id="upatk18" name="upatk18" placeholder="Ataque"></td></tr>

<tr><td><input type="number" value="<?php echo $_POST['lvlupatk19']; ?>" id="lvlupatk19" name="lvlupatk19" placeholder="LvL"></td>
 <td><input type="upatk19" value="<?php echo $_POST['upatk19']; ?>" id="upatk19" name="upatk19" placeholder="Ataque"></td></tr>

<tr><td><input type="number" value="<?php echo $_POST['lvlupatk20']; ?>" id="lvlupatk20" name="lvlupatk20" placeholder="LvL"></td>
 <td><input type="upatk20" value="<?php echo $_POST['upatk20']; ?>" id="upatk20" name="upatk20" placeholder="Ataque"></td></tr>




<tr><td><h1>TM/HM RELACIONADOS</h1></td></tr>



<?php
$relacionados = DB::exQuery("select * from tmhm_relacionados");
while($rel = $relacionados->fetch_assoc()) {
?>

<tr><td><input name="relacionados[]" value="<?php echo $rel['naam']; ?>" type="checkbox"> <?php echo $rel['naam']; ?> (<?php echo $rel['omschrijving']; ?>)</td></tr>
 
<?php
}
?>




<tr><td><h1>MOVE TUTOR</h1></td></tr>



<?php
$movetutor = DB::exQuery("select * from tmhm_movetutor");
while($mt = $movetutor->fetch_assoc()) {
?>

<tr><td><input name="movetutor[]" value="<?php echo $mt['naam']; ?>" type="checkbox"> <?php echo $mt['naam']; ?></td></tr>
 
<?php

}

?>









</table>

<br><br>
<input type="submit" value="OK">Certifique-se de que tudo está OK!




</form>






