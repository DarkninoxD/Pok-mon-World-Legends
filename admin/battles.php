<?php	
#Script laden zodat je nooit pagina buiten de index om kan laden
include("app/includes/resources/security.php");

#Admin controle
if ($gebruiker['admin'] < 1) { header('location: ./home'); exit; }
  
	
?>

<style>
.top { background-color:#fff;padding-top:5px;padding-bottom:5px;padding-right:5px;border-bottom:1px dashed #000; }
.user { background-color:#eee;font-weight:bold;padding-top:5px;padding-bottom:5px;padding-right:5px;border-bottom:1px solid #fff;text-align:right; }
.poke { text-decoration:underline;background-color:#cce792;font-weight:bold;padding-top:5px;padding-bottom:5px;padding-right:5px;border-bottom:1px solid #fff;text-align:right; }
.title { background-color:#eee;padding-top:5px;padding-bottom:5px;padding-right:5px;border-bottom:1px solid #fff; }
.lol1 { background-color:#fff;padding-top:5px;padding-bottom:5px;padding-right:5px;border-bottom:1px solid #fff; }
.lol2 { background-color:#ccc;padding-top:5px;padding-bottom:5px;padding-right:5px;border-bottom:1px solid #fff; }
.lol3 { background-color:#ff9999;padding-top:5px;padding-bottom:5px;padding-right:5px;border-bottom:1px solid #fff; }
.lol4 { background-color:#000;padding-top:5px;padding-bottom:5px;padding-right:5px;border-bottom:1px solid #fff; }
.massege { background-color:#cce792;padding-top:5px;padding-bottom:5px;padding-right:5px;border-bottom:1px solid #fff;width:320px; }
.readed { background-color:#ffffcc; padding-right:5px; }

</style>

<?php
           if (empty($_GET['subpage'])) { 
          $subpage = 1; 
        } 
        else{ 
          $subpage = $_GET['subpage']; 
        } 
        //Max aantal leden per pagina
        $max = 50; 
        //Leden tellen
        $aantal_leden = DB::exQuery("SELECT id FROM battle_logs")->num_rows; 
        $aantal_paginas = ceil($aantal_leden/$max); 
        
        $pagina = $subpage*$max-$max;
		
		
//Alle berichten laden
$messagesquery = DB::exQuery("SELECT * FROM battle_logs
							 ORDER BY id DESC LIMIT ".$pagina.", ".$max."");

  //Als er meer dan 1 bericht is
	if ($messagesquery->num_rows >= 1)
	{  
	  //Scherm weergave
  	echo '<center><table width=700" border="0" cellspacing="0" cellpadding="0" style="text-align:center;">
			<tr>
				<td width="140" class="top"><b>Treinador</b></td>
				<td width="140" class="top"><b>Pokémon</b></td>
				<td width="50" class="top"><b>Level</b></td>
				<td width="200" class="top"><b>Quando</b></td>
				<td width="200" class="top"><b>Raridade</b></td>
			</tr>
			<tr>
				<td colspan="4"></td>
			</tr>';
			
		//Alle berichten laten zien
		while ($row = $messagesquery->fetch_assoc())
        {
	$pokemon = DB::exQuery("SELECT * FROM pokemon_wild WHERE wild_id = '".$row['pokemon']."'")->fetch_assoc();
	$player = DB::exQuery("SELECT * FROM gebruikers WHERE user_id = '".$row['player']."'")->fetch_assoc();

  if ($pokemon['zeldzaamheid'] == 1) $zeldzaam = '<b>Comum</b>';
  else if ($pokemon['zeldzaamheid'] == 2) $zeldzaam = '<font color=red><b>Incomum</b></font>';
  else $zeldzaam = '<font color=gold><b>Raro</b></font>';
 
			 $j++;
			//Enters in de textarea ook weergeven als een enter


			echo'<tr>
					<td class="user"><a href="./profile&player='.$player['username'].'">'.$player['username'].'</a><span style="float:left;"></span></td>
					<td class="poke">'.$pokemon['naam'].'</td>
					<td class="title"><b>'.$row['pokemon_level'].'</b></td>
					<td class="user" style="text-align:center;color:red;">'.date('d/m/Y H:m:s', strtotime($row['date'])).'</td>
					<td class="lol'.$pokemon['zeldzaamheid'].'">'.$zeldzaam.'</td>
				</tr>
				<tr>
					<td colspan="4"></td>
				</tr>';
		}
		
		  //Pagina systeem
          $links = false;
          $rechts = false;
          echo '<tr><td colspan=4><center><div class="sabrosus">';
          if ($subpage == '1') {
            echo '<span class="disabled"> &lt; </span>';
          }
          else{
            $back = $subpage-1;
            echo '<a href="'.$_SERVER['PHP_SELF'].'/'.$_GET['page'].'&subpage='.$back.'"> &lt; </a>';
          }
          for($i = 1; $i <= $aantal_paginas; $i++) 
          { 
              
            if ((2 >= $i) && ($subpage == $i)) {
              echo '<span class="current">'.$i.'</span>';
            }
            else if ((2 >= $i) && ($subpage != $i)) {
              echo '<a href="'.$_SERVER['PHP_SELF'].'/'.$_GET['page'].'&subpage='.$i.'">'.$i.'</a>';
            }
            else if (($aantal_paginas-2 < $i) && ($subpage == $i)) {
              echo '<span class="current">'.$i.'</span>';
            }
            else if (($aantal_paginas-2 < $i) && ($subpage != $i)) {
              echo '<a href="'.$_SERVER['PHP_SELF'].'/'.$_GET['page'].'&subpage='.$i.'">'.$i.'</a>';
            }
            else{
              $max = $subpage+3;
              $min = $subpage-3;  
              if ($subpage == $i) {
                echo '<span class="current">'.$i.'</span>';
              }
              else if (($min < $i) && ($max > $i)) {
              	echo '<a href="'.$_SERVER['PHP_SELF'].'/'.$_GET['page'].'&subpage='.$i.'">'.$i.'</a>';
              }
              else{
                if ($i < $subpage) {
                  if (!$links) {
                    echo '...';
                    $links = True;
                  }
                }
                else{
                  if (!$rechts) {
                    echo '...';
                    $rechts = True;
                  }
                }
          
              }
            }
          } 
          if ($aantal_paginas == $subpage) {
            echo '<span class="disabled"> &gt; </span>';
          }
          else{
            $next = $subpage+1;
            echo '<a href="'.$_SERVER['PHP_SELF'].'/'.$_GET['page'].'&subpage='.$next.'"> &gt; </a>';
          }
          echo "</div></center>
		  		</td>
		  	</tr>
		  </table>";
	}
?>