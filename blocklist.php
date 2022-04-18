<?php
#Script laden zodat je nooit pagina buiten de index om kan laden
include("app/includes/resources/security.php");
	
#ALs er al een speler naam binnenkomt met een GET, deze laden

if (isset($_GET['player'])) $naamget = $_GET['player'];

#Als er op de verwijder knop gedrukt word
if (isset($_POST['deletenaam'])) {
  #Persoon filteren uit de blocklist array
  $info = DB::exQuery("SELECT `user_id` FROM `gebruikers` WHERE `username`='".$_POST['deletenaam']."'")->fetch_assoc();
  $blocknaam = str_replace(",".$info['user_id'].",", "", $gebruiker['blocklist']);
  $id = $info['user_id'];
  #Array zonder persoon weer update'en
  DB::exQuery("UPDATE `gebruikers` SET `blocklist`='".$blocknaam."' WHERE `user_id`='".$_SESSION['id']."'");

  #Melding maken dat speler verwijderd is.
  $error = '<div class="green">'.$txt['success_deleted'].'</div>';
  
  $new_block = DB::exQuery("SELECT blocklist FROM gebruikers WHERE user_id='".$_SESSION['id']."'")->fetch_assoc();
  $gebruiker['blocklist'] = $new_block['blocklist'];
}
#Als er op voeg toe word gedrukt
if (isset($_POST['blocknaam'])) {
  $naamget = $_POST['blocknaam'];
  #Gegevens laden van je block list
  $blocknaam = strtolower($_POST['blocknaam']);
  $spelerkleineletter = strtolower($_SESSION['naam']);
  
  #Je kunt jezelf natuurlijk niet toevoegen
  if ($blocknaam == $spelerkleineletter)
    $error = '<div class="red">'.$txt['alert_block_yourself'].'</div>';
  else{
    $info = DB::exQuery("SELECT `user_id`, `username`, `admin` FROM `gebruikers` WHERE `username`='".$naamget."'")->fetch_assoc();
	     
	  $block = explode(",",$gebruiker['blocklist']);
		
    #Kijken als de speler wel bestaat
    if (empty($info['user_id']))
      $error = '<div class="red">'.$txt['alert_unknown_username'].'</div>';
    #Admin check
    else if ($info['admin'] > 0)
      $error = '<div class="red">'.$txt['alert_admin_block'].'</div>';
    #Kijken als hij al geblocked is 
    else if (in_array($info['user_id'], $block) === true)
      $error = '<div class="red">'.$txt['alert_already_in_blocklist'].'</div>';
    #Anders toevoegen
    else{
      $id = $info['user_id'];
      #Buddy opslaan
      DB::exQuery("UPDATE `gebruikers` SET `blocklist`='".$gebruiker['blocklist'].",".$id.",' WHERE `user_id`='".$_SESSION['id']."'");
      DB::exQuery("DELETE FROM `friends` WHERE (`uid`='$_SESSION[id]' OR `uid_2`='$_SESSION[id]') AND (`uid`='$id' OR `uid_2`='$id') AND `accept`='1'");

      require_once 'app/classes/Sharing_account.php';

      $share = new Sharing_account();

      if ($share->remove($id)) {
        $user = $share->username($id);
        $shared2 = $share->getShared();
        $shared2 = array_merge(array_diff($shared2, array($id)));
        $shared2 = implode(',', $shared2);

        DB::exQuery("UPDATE `rekeningen` SET `shared` = '$shared2' WHERE `acc_id` = '$_SESSION[acc_id]'");
      }

	  #Alert
	  $error = '<div class="green">'.$txt['success_blocked'].'</div>';
    }
  }
  $new_block = DB::exQuery("SELECT blocklist FROM gebruikers WHERE user_id='".$_SESSION['id']."'")->fetch_assoc();
  $gebruiker['blocklist'] = $new_block['blocklist'];
}

echo addNPCBox(14, 'Caixa de Mensagens', 'Você pode enviar e receber mensagens de outros treinadores utilizando as Mensagens Privadas, Bloquear Treinadores e ver as Mensagens Oficiais do jogo. <br>Não é permitido utilizá-lo para fins de propaganda!');
?>

<div class="red">NUNCA dê sua senha ou e-mail a ninguém através de mensagem privada. Em nenhum momento, alguém da equipe do jogo irá pedir sua senha.</div>

<?php if ($error) echo $error; ?>
<div style="width: 100%; display: flex" class="box-content">
    <table style="flex: 0 0 17%;" class="msg-table">
        <tr>
            <td onclick="window.location = './official-messages'">
                <i class="material-icons" style="font-size: 30px">email</i> <br>Mensagens Oficiais <span class="badges" id="official-badges">0</span>
            </td>
        </tr>
        <tr>
            <td onclick="window.location = './inbox'">
                <i class="material-icons" style="font-size: 30px">people</i> <br>Conversas <span class="badges" id="mail-badges">0</span>
            </td>
        </tr>
        <tr>
            <td onclick="window.location = './inbox&action=send'" id="new_msg">
                <i class="material-icons" style="font-size: 30px">message</i> <br>Nova Conversa
            </td>
        </tr>
        <tr>
            <td class="selected" onclick="window.location = './blocklist'">
                <i class="material-icons" style="font-size: 30px">block</i> <br>Bloqueados (<span id="block-badges">0</span>)
            </td>
        </tr>
    </table>

    <script>
        $('#official-badges').text(<?=$official_count?>);
        $('#mail-badges').text(<?=$mails_count?>);

        <?php
            $blocks = (count(explode(',', $gebruiker['blocklist']))-1)/2;
        ?>

        $('#block-badges').text(<?=$blocks?>);
    </script>

    <div style="flex: 1;" class="msg-container">
        <div class="title">
            <p style="padding: 10px; margin: 0; font-weight: bold" id="title">Bloquear Treinador<br><span style="font-size: 12px"></span></p>
        </div>
        <div class="blue">Caso bloqueie algum treinador, você não poderá mandar ou receber mensagens dele e irá exluí-lo de sua lista de amigos.</div>
        <div style="max-height: 500px; overflow-y: auto;">
            <ul class="ul">
                <li>
                <center>
            <form method="POST" action="./blocklist">
            <div style="width: 100%;">
                  <div style="background: #34465f;padding: 10px;border-bottom: 2px solid #27374e;">
                      <div>
                          <input type="text" class="text_long" placeholder="Treinador" name="blocknaam" value="<?php echo $naamget; ?>" style="width: 100%; height:30px; padding: 5px 0 5px 10px; margin-bottom: 5px" maxlength="10" required="" />
                          <br>
                          <input type="submit" value="<?php echo $txt['button']; ?>" name="voegtoe" class="button"/>
                      </div>
                  </div>
              </div>
              </form>
              
                <?php
                #Pagina nummer opvragen
                $subpage = 1; 
                if (isset($_GET['subpage'])) $subpage = $_GET['subpage']; 
                #Max aantal spelers per pagina
                #Pagina systeem
                $max = 10;       
                $pagina = $subpage*$max-$max; 
                
                $blockblock = $gebruiker['blocklist'];
                $blockblock = explode(",", $gebruiker['blocklist']);
                $blocks = 0;
                $names = array();
                foreach($blockblock as $user_name) {
                  if (!empty($user_name)) {
                    $blocks++;
                    array_push($names, $user_name); 
                  }
                }
                
                $aantal_paginas = ceil($blocks/$max);

                if ($blocks != 0) {
                  echo '<br />
                <div class="box-content"><table width="100%" style="text-align: center;" class="general">
                    <tr>
                      <td width="30" class="top_first_td">'.$txt['#'].'</td>
                      <td width="110" class="top_td">'.$txt['username'].'</td>
                      <td width="80" class="top_td">'.$txt['status'].'</td>
                      <td width="30" class="top_td">Desbloquear</td>
                    </tr>';
                }
                
                foreach ($names as $i => $name) {
                  if (($i >= $pagina) AND ($pagina+$max > $i)) {
                    $i += 1;
                    $user_info = DB::exQuery("SELECT `username`, `online`, `premiumaccount` FROM `gebruikers` WHERE `user_id`='".$name."'")->fetch_assoc();
              $ster = '';
                    $plaatje = "".$static_url."/images/icons/status_offline.png";
                    $online  = $txt['offline'];

              #Ster maken als blockkereltje premium is
              if ($user_info['premiumaccount'] > time()) $ster = ' <img src="'.$static_url.'/images/icons/vip.gif" width="16" height="16" border="0" alt="Premium" title="Premium" style="margin-bottom:-3px;">';
              
              #Tijd voor plaatje
                    $tijd = time();
                    if (($user_info['online']+300) > $tijd) {
                      $plaatje = "".$static_url."/images/icons/status_online.png";
                      $online  = $txt['online'];
                    }
                
                    echo '
                      <tr>
                        <td class="normal_first_td">'.$i.'.</td>
                        <td class="normal_td"><a href="./profile&player='.$user_info['username'].'">'.$user_info['username'].$ster.'</a></td>
                        <td class="normal_td"><img src="'.$plaatje.'" width="18" height="15" />'.$online.'</td>
                        <form method="post" name="form1" action="./blocklist">
                          <td class="normal_td"><center><button onclick="form1.submit();">Desbloquear</button></center></td>
                          <input type="hidden" value="'.$user_info['username'].'" name="deletenaam">
                        </form>
                      </tr>';
                  }
                }
                
                if ($blocks >= 1) {
                  #Pagina systeem
                  $links = false;
                  $rechts = false;
                  echo '<tr><td colspan=4><center><br /><div class="sabrosus">';
                  if ($subpage == 1)
                    echo '<span class="disabled"> &lt; </span>';
                  else{
                    $back = $subpage-1;
                    echo '<a href="'.$_SERVER['PHP_SELF'].'/'.$_GET['page'].'&subpage='.$back.'"> &lt; </a>';
                  }
                  for($i = 1; $i <= $aantal_paginas; $i++) { 
                    if ((2 >= $i) && ($subpage == $i))
                      echo '<span class="current">'.$i.'</span>';
                    else if ((2 >= $i) && ($subpage != $i))
                      echo '<a href="'.$_SERVER['PHP_SELF'].'/'.$_GET['page'].'&subpage='.$i.'">'.$i.'</a>';
                    else if (($aantal_paginas-2 < $i) && ($subpage == $i))
                      echo '<span class="current">'.$i.'</span>';
                    else if (($aantal_paginas-2 < $i) && ($subpage != $i))
                      echo '<a href="'.$_SERVER['PHP_SELF'].'/'.$_GET['page'].'&subpage='.$i.'">'.$i.'</a>';
                    else{
                      $max = $subpage+3;
                      $min = $subpage-3;  
                      if ($subpage == $i)
                        echo '<span class="current">'.$i.'</span>';
                      else if (($min < $i) && ($max > $i))
                        echo '<a href="'.$_SERVER['PHP_SELF'].'/'.$_GET['page'].'&subpage='.$i.'">'.$i.'</a>';
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
                  if ($aantal_paginas == $subpage)
                    echo '<span class="disabled"> &gt; </span>';
                  else{
                    $next = $subpage+1;
                    echo '<a href="'.$_SERVER['PHP_SELF'].'/'.$_GET['pagina'].'&subpage='.$next.'"> &gt; </a>';
                  }
              echo '</div></center></td></tr>
              </td></tr>
              </table></div>';
                } 
                ?></center>
                </li>
            </ul>
        </div>
    </div>
</div>