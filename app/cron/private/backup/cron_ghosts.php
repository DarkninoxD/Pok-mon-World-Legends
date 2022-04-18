<?php
include_once '../includes/resources/config.php';

#bora gerar uns player online fantasma ai

#5 GHOSTS NO-PREMIUM
$result = DB::exQuery("SELECT `user_id`,`online` FROM `gebruikers` WHERE `online` + 300 < UNIX_TIMESTAMP() AND `premiumaccount` < UNIX_TIMESTAMP() ORDER BY RAND() LIMIT " . rand(1, 5));
while ($row = $result->fetch_assoc()) {
    $online = time() + rand(300, 500);
    DB::exQuery("UPDATE `gebruikers` SET `online` = '{$online}' WHERE `user_id` = '{$row['user_id']}'");
}


#5 GHOSTS PREMIUM
$i = 0;
$result = DB::exQuery("SELECT `user_id`,`online` FROM `gebruikers` WHERE `online` + 300 < UNIX_TIMESTAMP() AND `premiumaccount` > UNIX_TIMESTAMP() ORDER BY RAND() LIMIT " . rand(1, 5));
while ($row = $result->fetch_assoc()) {
    $online = time() + rand(300, 500);
    DB::exQuery("UPDATE `gebruikers` SET `online` = '{$online}' WHERE `user_id` = '{$row['user_id']}'");
	$i++;
}


if ($i == 0) {
#5 GHOSTS NO-PREMIUM
$result = DB::exQuery("SELECT `user_id`,`online` FROM `gebruikers` WHERE `online` + 300 < UNIX_TIMESTAMP() AND `premiumaccount` < UNIX_TIMESTAMP() ORDER BY RAND() LIMIT " . rand(1, 5));
while ($row = $result->fetch_assoc()) {
    $online = time() + rand(300, 500);
    DB::exQuery("UPDATE `gebruikers` SET `online` = '{$online}' WHERE `user_id` = '{$row['user_id']}'");
}
}

echo "Cron executado com sucesso.";
?>