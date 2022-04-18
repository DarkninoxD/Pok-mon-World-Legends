<?php
unset($_SESSION['ploot']);

if (!isset($_SESSION['ploot']) && isset($_SESSION['id'])) {
    $pokeloot = rand(0, 1200);
    // $pokeloot = 1;
    
    if ($pokeloot < 5) {
        if (strpos($page, '/') !== false) {
            //não aparece ovni
        } else {
            $id = $_SESSION['id'];
            $token = substr(rtrim(strtr(base64_encode(hash('sha256', uniqid(mt_rand(), true), true)), '+/', '-_'), '='), 0, 13);
            
            $_SESSION['ploot'] = $token;
            DB::exQuery ("UPDATE `gebruikers` SET pokeloot_token='$token' WHERE user_id='$id'");
?>
            <script src="<?=$static_url?>/javascripts/jquery.ovni.js" class="remove"></script>

            <img src="<?=$static_url?>/images/pokeloot/pokegift.png" alt="Poke Loot" onclick="Game.catchLoot(this);" style="cursor: pointer; z-index: 10000; filter: drop-shadow(2px 2px 4px) invert(8%);" id="loot" />
            
            <script class="remove">
                $('#loot').ovni (1.5);
                $('.remove').remove();
            </script>
<?php
        }
    }
}