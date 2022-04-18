<?php
include("app/includes/resources/security.php");
include('app/classes/Official_messages.php');

echo addNPCBox(14, 'Caixa de Mensagens', 'Você pode enviar e receber mensagens de outros treinadores utilizando as Mensagens Privadas, Bloquear Treinadores e ver as Mensagens Oficiais do jogo. <br>Não é permitido utilizá-lo para fins de propaganda!');
?>

<div class="red">NUNCA dê sua senha ou e-mail a ninguém através de mensagem privada. Em nenhum momento, alguém da equipe do jogo irá pedir sua senha.</div>
<div style="width: 100%; display: flex" class="box-content">
    <table style="flex: 0 0 17%;" class="msg-table">
        <tr>
            <td class="selected" onclick="window.location = './official-messages'">
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
            <td onclick="window.location = './blocklist'">
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
        <div class="title"><p id="title"></p></div>
        <div style="max-height: 500px; overflow-y: auto;">
            <ul class="ul">
                <?php
                    
                    $var = new Official ( $_GET['id'] );

                    if ( empty ( $_GET['id'] ) ) {
                        $var->include_list ();
                    } else {
                        $var->include_by_id ();
                    }

                ?>
            </ul>
        </div>
    </div>
</div>