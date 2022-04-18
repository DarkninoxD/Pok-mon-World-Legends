<?php
//Script laden zodat je nooit pagina buiten de index om kan laden
include("app/includes/resources/security.php");

//Admin controle
if ($gebruiker['admin'] < 2) { header('location: ./home'); exit; }

################################################################

        //ALS ER MEER LEDEN ZIJN, PAGINA SYSTEEM!!
        //Pagina nummer opvragen
        if (empty($_GET['subpage'])) { 
          $subpage = 1; 
        } 
        else{ 
          $subpage = $_GET['subpage']; 
        } 
        //Max aantal leden per pagina
        $max = 50; 
        //Leden tellen
        $aantal_leden = (DB::exQuery("SELECT id FROM berichten"))->num_rows; 
        $aantal_paginas = ceil($aantal_leden/$max); 
        
        $pagina = $subpage*$max-$max;
		
		
//Alle berichten laden
$messagesquery = DB::exQuery("SELECT berichten.afzender_id, berichten.ontvanger_id, berichten.bericht, berichten.onderwerp, gebruikers.username
							 FROM berichten
							 INNER JOIN gebruikers
							 ON afzender_id = gebruikers.user_id
							 ORDER BY id DESC LIMIT ".$pagina.", ".$max."");
  //Als er meer dan 1 bericht is
	if ($messagesquery->num_rows >= 1)
	{  
	  //Scherm weergave
  	echo '<table width=600" border="0" cellspacing="0" cellpadding="0">
			<tr>
				<td width="100"><b>De</b></td>
				<td width="100"><b>Para</b></td>
				<td width="100"><b>Assunto</b></td>
				<td width="300"><b>Texto</b></td>
			</tr>
			<tr>
				<td colspan="4"><HR></td>
			</tr>';
			
		//Alle berichten laten zien
		while ($row = ($messagesquery)->fetch_assoc())
        {
			 $j++;
			//Enters in de textarea ook weergeven als een enter
      		$tekst = nl2br($row['bericht']);
			$tekst = htmlspecialchars($tekst);
			//Anti langezin
			$tekst = anti_langezin($tekst);
			
			echo'<tr>
					<td><a href="./profile&player='.$row['username'].'">'.$row['username'].'</a></td>
					<td><a href="./profile&player='.$row['ontvanger'].'">'.$row['ontvanger_id'].'</a></td>
					<td>'.$row['onderwerp'].'</td>
					<td><table width="300" border="0">
							<tr>
								<td>'.$tekst.'</td>
							</tr>
						</table></td>
				</tr>
				<tr>
					<td colspan="4"><HR></td>
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