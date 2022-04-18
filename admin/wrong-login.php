<?php		
//Script laden zodat je nooit pagina buiten de index om kan laden
include("app/includes/resources/security.php");

//Admin controle
if ($gebruiker['admin'] < 2) { header('location: ./home'); exit; }
?>


<table width="600">
	<tr>
		<td width="50">#</td>
		<td width="150"><strong>Treinador:</strong></td>
		<td width="150"><strong>Senha incorreta:</strong></td>
		<td width="150"><strong>IP:</strong></td>
		<td width="100"><strong>Banir:</strong></td>
	</tr>

<?php
//Alle buddy's tellen voor de pagina
$aantalfout = DB::exQuery("SELECT `id` FROM `inlog_fout`")->num_rows;

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
$pagina = $subpage*$max-$max; 
$aantal_paginas = ceil($aantalfout/$max);

//Buddy's laden
$fout = DB::exQuery("SELECT `spelernaam`, `wachtwoord`, `ip`, `datum` FROM `inlog_fout` ORDER BY `id` DESC LIMIT ".$pagina.", ".$max."");

for($j=$pagina+1; $foutgegevens = $fout->fetch_assoc(); $j++) {
  //Scherm weergave  
  echo '<tr>
  				<td height="30">'.$j.'.</td>
  				<td><a href="./profile&player='.$foutgegevens['spelernaam'].'">'.$foutgegevens['spelernaam'].'</a></td>
  				<td>****</a></td>
  				<td>'.$foutgegevens['ip'].'</a></td>
  				<td><a href="./admin/ban-user&ip='.$foutgegevens['ip'].'">Banir IP</a></td>
  			</tr>';
}

		  //Pagina systeem
          $links = false;
          $rechts = false;
          echo '<tr><td colspan=5><center><div class="sabrosus">';
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
?>