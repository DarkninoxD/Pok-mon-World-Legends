<?php
    if (isset($_SESSION['id'])) {
        $chat = DB::exQuery("SELECT * FROM `chat` ORDER BY `id` ASC LIMIT 0, 100");
?>

<div class="box-content msg-container" style="margin-top: 7px; float: left; width: 100%;">
    <div class="title"><p>CHAT - POKÉMON WORLD LEGENDS</p></div>
    <div id="div-container" style="max-height: 250px; overflow-y: auto;">
        <ul class="ul" style="margin: 0">
            <div id="message-container">
                <?php
                while ($var_msg = $chat->fetch_assoc()) {
                    $class = 'O';
                    if ($var_msg['sender'] == $_SESSION['id'])  {
                        $class = 'I';
                        
                        echo '<div class="speech-bubble-'.$class.'"><p>'.ubbcode($var_msg['message']).'<br><span class="bubble-span">'.$var_msg['hour'].'</span></p></div>';
                    } else {
                        $player = '<b><a href="./profile&player='.$var_msg['nickname'].'">'.$var_msg['nickname'].'</a></b>';
                        echo '<div class="speech-bubble-'.$class.'"><p><span style="font-weight:bold">['.$var_msg['hour'].']</span> '.$player.': '.ubbcode($var_msg['message']).'</p></div>';
                    }
                }
                ?>
            </div>
        </ul>
    </div>
    <div style="width: 100%;float: left; margin-top: 25px;">
        <div style="background: #34465f; padding: 10px">
            <div id="chat-warn"></div>
            <form method="post" id="messageFormChat">
                <table style="width: 100%">
                    <tbody><tr>
                        <td style="width: 99%; padding-right: 10px">
                            <textarea name="chat" id="mensagem" placeholder="Responder" maxlength="255" style="padding: 5px 15px 5px 15px; border-radius: 5px; resize: none; height: 65px; width: 100%;word-wrap: break-word;overflow-wrap: break-word;word-break: break-all;" required=""></textarea>
                            <input type="hidden" id="sender" name="sender" value="<?=$gebruiker['username']?>">
                        </td>
                        <td style="width: 1%; padding-right: 10px">
                            <div class="emote-carousel" style="position: relative; width: 200px;height:32px">
                                <div class="carousel-cell" style="text-align: center; min-width: 40px;"><img src="<?=$static_url?>/images/emoticons/001.png" style="cursor: pointer;" onclick="emote_chat_global(':)')"></div>
                                <div class="carousel-cell" style="text-align: center; min-width: 40px;"><img src="<?=$static_url?>/images/emoticons/002.png" style="cursor: pointer;" onclick="emote_chat_global(':D')"></div>
                                <div class="carousel-cell" style="text-align: center; min-width: 40px;"><img src="<?=$static_url?>/images/emoticons/003.png" style="cursor: pointer;" onclick="emote_chat_global('xD')"></div>
                                <div class="carousel-cell" style="text-align: center; min-width: 40px;"><img src="<?=$static_url?>/images/emoticons/004.png" style="cursor: pointer;" onclick="emote_chat_global(':P')"></div>
                                <div class="carousel-cell" style="text-align: center; min-width: 40px;"><img src="<?=$static_url?>/images/emoticons/005.png" style="cursor: pointer;" onclick="emote_chat_global(';)')"></div>
                                <div class="carousel-cell" style="text-align: center; min-width: 40px;"><img src="<?=$static_url?>/images/emoticons/006.png" style="cursor: pointer;" onclick="emote_chat_global(':S')"></div>
                                <div class="carousel-cell" style="text-align: center; min-width: 40px;"><img src="<?=$static_url?>/images/emoticons/007.png" style="cursor: pointer;" onclick="emote_chat_global(':O')"></div>
                                <div class="carousel-cell" style="text-align: center; min-width: 40px;"><img src="<?=$static_url?>/images/emoticons/008.png" style="cursor: pointer;" onclick="emote_chat_global('8-)')"></div>
                                <div class="carousel-cell" style="text-align: center; min-width: 40px;"><img src="<?=$static_url?>/images/emoticons/009.png" style="cursor: pointer;" onclick="emote_chat_global(':*')"></div>
                                <div class="carousel-cell" style="text-align: center; min-width: 40px;"><img src="<?=$static_url?>/images/emoticons/010.png" style="cursor: pointer;" onclick="emote_chat_global(':(')"></div>
                                <div class="carousel-cell" style="text-align: center; min-width: 40px;"><img src="<?=$static_url?>/images/emoticons/011.png" style="cursor: pointer;" onclick="emote_chat_global(':\'(')"></div>
                                <div class="carousel-cell" style="text-align: center; min-width: 40px;"><img src="<?=$static_url?>/images/emoticons/012.png" style="cursor: pointer;" onclick="emote_chat_global(':|')"></div>
                                <div class="carousel-cell" style="text-align: center; min-width: 40px;"><img src="<?=$static_url?>/images/emoticons/013.png" style="cursor: pointer;" onclick="emote_chat_global(':b')"></div>
                                <div class="carousel-cell" style="text-align: center; min-width: 40px;"><img src="<?=$static_url?>/images/emoticons/014.png" style="cursor: pointer;" onclick="emote_chat_global('(BOO)')"></div>
                                <div class="carousel-cell" style="text-align: center; min-width: 40px;"><img src="<?=$static_url?>/images/emoticons/015.png" style="cursor: pointer;" onclick="emote_chat_global('(zZZ)')"></div>
                                <div class="carousel-cell" style="text-align: center; min-width: 40px;"><img src="<?=$static_url?>/images/emoticons/016.png" style="cursor: pointer;" onclick="emote_chat_global(':v')"></div>
                                <div class="carousel-cell" style="text-align: center; min-width: 40px;"><img src="<?=$static_url?>/images/emoticons/017.png" style="cursor: pointer;" onclick="emote_chat_global('(GRR)')"></div>
                                <div class="carousel-cell" style="text-align: center; min-width: 40px;"><img src="<?=$static_url?>/images/emoticons/018.png" style="cursor: pointer;" onclick="emote_chat_global(':3')"></div>
                                <div class="carousel-cell" style="text-align: center; min-width: 40px;"><img src="<?=$static_url?>/images/emoticons/019.png" style="cursor: pointer;" onclick="emote_chat_global('@-)')"></div>
                                <div class="carousel-cell" style="text-align: center; min-width: 40px;"><img src="<?=$static_url?>/images/emoticons/020.png" style="cursor: pointer;" onclick="emote_chat_global('o_O')"></div>
                                <div class="carousel-cell" style="text-align: center; min-width: 40px;"><img src="<?=$static_url?>/images/emoticons/021.png" style="cursor: pointer;" onclick="emote_chat_global('._.')"></div>
                                <div class="carousel-cell" style="text-align: center; min-width: 40px;"><img src="<?=$static_url?>/images/emoticons/022.png" style="cursor: pointer;" onclick="emote_chat_global('(S2)')"></div>
                            </div>
                            <input type="submit" value="Enviar Mensagem" class="button" style="width:100%">
                        </td>
                    </tr>
                </tbody></table>
            </form>
        </div>
    </div>
</div>

<script>
    var $emote = $('.emote-carousel');

    $emote.flickity({
        cellAlign: 'center',
        contain: true,
        pageDots: false,
        wrapAround: false,
        prevNextButtons: false,
        initialIndex: 2
    });

    function emote_chat_global ($param = ':)') {
        let msg = $('#mensagem').val();
        msg = msg + ' ' + $param;
        
        $('#mensagem').val(msg);
    }

    $("#div-container").animate({ scrollTop: $("#div-container").prop("scrollHeight") }, 0);
    // window.location.protocol = 'http://';
</script>

<script src="<?=$static_url?>/javascripts/socket.io.js"></script>
<script src="<?=$static_url?>/javascripts/node/chat/client.js"></script>

<?php } ?>