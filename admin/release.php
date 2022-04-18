<?php	
#Script laden zodat je nooit pagina buiten de index om kan laden
include("app/includes/resources/security.php");

#Admin controle
if ($gebruiker['admin'] < 1) { header('location: ./home'); exit; }
  
	
?>

<style>
.top { background-color:#fff;padding-top:5px;padding-bottom:5px;padding-right:5px;border-bottom:1px dashed #000; }
.user { background-color:#eee;font-weight:bold;padding-top:5px;padding-bottom:5px;padding-right:5px;border-bottom:1px solid #fff; }
.title { background-color:#e7c692;padding-top:5px;padding-bottom:5px;padding-right:5px;border-bottom:1px solid #fff; }
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
        $aantal_leden = DB::exQuery("SELECT id FROM release_log")->num_rows; 
        $aantal_paginas = ceil($aantal_leden/$max); 
        
        $pagina = $subpage*$max-$max;
		
		
//Alle berichten laden
$messagesquery = DB::exQuery("SELECT * FROM release_log
							 ORDER BY id DESC LIMIT ".$pagina.", ".$max."");
							 			$messagesquery2 = DB::exQuery("SELECT berichten.datum, berichten.afzender_id, berichten.ontvanger_id, berichten.bericht, berichten.onderwerp, gebruikers.username
							 FROM berichten
							 INNER JOIN gebruikers
							 ON ontvanger_id = gebruikers.user_id ORDER BY datum DESC LIMIT ".$pagina.", ".$max."");
  //Als er meer dan 1 bericht is
	if ($messagesquery->num_rows >= 1)
	{  
	  //Scherm weergave
  	echo '<center><table width=670" border="0" cellspacing="0" cellpadding="0" style="text-align:center;">
			<tr>
				<td width="130" class="top"><b>Quem</b></td>
				<td width="150" class="top"><b>Quando</b></td>
				<td width="230" class="top"><b>Pokémon</b></td>
				<td width="200" class="top"><b>Pokébola</b></td>
			</tr>
			<tr>
				<td colspan="4"></td>
			</tr>';
			
		//Alle berichten laten zien
		while ($row = $messagesquery->fetch_assoc())
        {
        $row2 = DB::exQuery("SELECT username FROM gebruikers WHERE user_id = ".$row['user_id']."")->fetch_assoc();
	$info2 = DB::exQuery("SELECT * FROM pokemon_wild WHERE wild_id = '".$row['wild_id']."'")->fetch_assoc();

			 $j++;
			//Enters in de textarea ook weergeven als een enter


			echo'<tr>
					<td class="user"><a href="./profile&player='.$row2['username'].'">'.$row2['username'].'</a></td>
					<td class="title"><b>'.date('d/m/Y H:m:s', strtotime($row['date'])).'</b></td>
					<td class="user"><b>#'.$row['real_id'].' - '.$info2['naam'].'</b></td>
					<td class="title"><b>'.$row['pokeball'].'</b></td>
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