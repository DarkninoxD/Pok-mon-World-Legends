var socket = io.connect('http://www.pokemonwordlegends.com:21042');

var lock = true;
var time = '';

$('#messageFormChat').submit(function () {
    let msg = $('#mensagem').val();
    let send = $('#sender').val();
    
    if (lock) {
        if (msg && send) {
            $.ajax({
                url: "ajax.php?act=new-chat",
                type: "POST",
                data: {
                    message: msg,
                },
                success: function (data) {
                    msg = data.split(" | ");

                    if (msg[0] == 'success') {
                        let info = {
                            message: msg[1],
                            sender: send
                        };
                        
                        lock = false;            
                        
                        $('#mensagem').val('');
                        setTimeout(function () {
                            lock = true;
                            $('#chat-warn').html('');
                        }, 5000);
                        
                        socket.emit('chat', info);
                    } else {
                        time = msg[1];
                    }
                }
            });
        }
    } else {
        $('#chat-warn').html('<div class="red">Aguarde 5 segundos para enviar outra Mensagem!</div>');
    }

    return false;
});

socket.on('chat', function (data) {
    let cont = $('#message-container');
    let container = cont.html();
    let classe = 'O';
    let new_row = '';

    if (time == '') time = time_gen();

    if (data.sender === $('#sender').val()) {
        classe = 'I';

        new_row = '<div class="speech-bubble-' + classe + '"><p>' + emojis(data.message) + '<br><span class="bubble-span">' + time + '</span></p></div>';
    } else {
        let player = '<b><a href="./profile&player=' + data.sender + '">' + data.sender + '</a></b>';

        new_row = '<div class="speech-bubble-' + classe + '"><p><span style="font-weight:bold">[' + time + ']</span> ' + player + ': ' + emojis(data.message) + '</p></div>';
    }

    let new_msg = container + new_row;

    time = '';
    cont.html(new_msg);
    $("#div-container").animate({
        scrollTop: $('#div-container').prop('scrollHeight')
    }, 0);
});

function emojis(param) {
    let path = "public/images/emoticons/";
    let text = param;
    let smiles = {
        ":)": "001.png",
        ":D": "002.png",
        "xD": "003.png",
        ":P": "004.png",
        ";)": "005.png",
        ":S": "006.png",
        ":O": "007.png",
        "8-)": "008.png",
        ":*": "009.png",
        ":(": "010.png",
        ":'(": "011.png",
        ":|": "012.png",
        ":b": "013.png",
        "(BOO)": "014.png",
        "(zZZ)": "015.png",
        ":v": "016.png",
        "(GRR)": "017.png",
        ":3": "018.png",
        "@-)": "019.png",
        "o_O": "020.png",
        "._.": "021.png",
        "(S2)": "022.png"
    };

    for (let key in smiles) {
        let value = smiles[key];

        if (text.indexOf(key) !== -1) {
            let count = text.lastIndexOf(key);
            for (let i = 0; i < count; i++) {
                text = text.replace(key, '<img src="' + path + value + '">');
            }
        }

    }
    return text;
}

function time_gen () {
    var today = new Date();
    var hh = String(today.getUTCHours()).padStart(2, '0');
    var ii = String(today.getUTCMinutes()).padStart(2, '0');
    var ss = String(today.getUTCSeconds()).padStart(2, '0');
    t = hh + ':' + ii + ':' + ss;

    return t;
}