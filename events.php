<?php 
#Script laden zodat je nooit pagina buiten de index om kan laden
require_once("app/includes/resources/security.php");	
#Alle ongelezen gebeurtenisse op gelezen zetten

if ($gebruiker['admin'] > 0) $toegestaan = 1000;
else if ($gebruiker['premiumaccount'] > time()) $toegestaan = 60;
else $toegestaan = 30;

$events_arr = array();

$event_sql = DB::exQuery("SELECT `id`,`datum`,`ontvanger_id`,`bericht`,`gelezen` FROM `gebeurtenis` WHERE `ontvanger_id`='" . $_SESSION['id'] . "' ORDER BY `id` DESC LIMIT " . $toegestaan);
$event_count = $event_sql->num_rows;

if ($events_count > 0) DB::exQuery("UPDATE `gebeurtenis` SET `gelezen`='1' WHERE `ontvanger_id`='".$_SESSION['id']."'");

#Lijst opbouwen per bericht gaat vanzelf
while($events = $event_sql->fetch_assoc()) {
  $dt = explode(' ', $events['datum']);
  $arr = array('date' => $dt[0], 'msg' => $events['bericht'], 'hour' => $dt[1]);

  array_push($events_arr, $arr);
}

$text = 'Bom, aqui você poderá acompanhar todos os procedimentos feitos em seu jogo. <br>';
if ($gebruiker['premiumaccount'] < time())	$text .= 'Usuários normais têm um limite de até as 30 notificações mais recentes.<br><br>&mdash; Seja VIP clicando <a href="./gold-market">AQUI</a> e aumente o limite para 60!';
else	$text .= '<br><br>Você têm acesso as 60 notificações mais recentes.'; 

echo addNPCBox(14, 'Minhas Notificações', $text);
?>

<div class="box-content" style="max-height: 495px; overflow-y: scroll">
    <?php 
    $size = sizeof($events_arr);

    if ($size == 0) {
      echo '<h3 class="title" style="font-size: 14px">NÃO HÁ NOTIFICAÇÕES A SEREM LISTADAS!</h3>'; 
    }

    for ($i = 0; $i < $size; $i++) {
      $date = $events_arr[$i]['date'];
      $hour = $events_arr[$i]['hour'];
      $msg = '<tr><td><b>['.$hour.']</b> - '.$events_arr[$i]['msg'].'</td></tr>';
      
      if ($i > 0) {
        $date_ant = $events_arr[($i - 1)]['date'];
      } else {
        $date_ant = 'xxxx-01-xx';
      }

      if ($date != $date_ant) {
        echo '<table class="general" style="width:100%; text-align: center; border-top: 1px solid #577599;"><thead><tr><th>'.date_show($date).'</th></tr></thead><tbody>';
      }
      
      echo $msg;

      if (($i + 1) == $size) echo '</tbody></table>';

    }
    
    ?>
</div>