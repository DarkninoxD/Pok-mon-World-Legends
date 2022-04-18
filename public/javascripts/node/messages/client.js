let socket = io.connect('http://www.pokemonwordlegends.com:21039');

var lock = true;
var time = '';

$('#messageForm').submit(function () {
	let msg = $('#mensagem').val();
	let conversa = $('#conversa').val();
	let send = $('#sender').val();
	
	if (lock) {
		if (msg && conversa && send) {
			lock = false;
			$.ajax({
				url: "ajax.php?act=new-message",
				type: "POST",
				data: {
					id: conversa,
					message: msg,
					sender: send
				},
				success: function (data) {
					msg = data.split(' | ');
					if (msg[0] == 'success') {
						let info = { id: conversa, message: msg[1], sender: send }

						socket.emit('message', info);
					} else {
						time = msg[1];
					}
				}
			});
		}
	}

	return false;
});

socket.on( 'message', function( data ) {
	let cont = $('#message-container');
	let container = cont.html();
	let classe = 'O';
	
	if (data.sender === $('#sender').val()) classe = 'I';

	if (time == '') time = time_gen();
	
	let new_row = '<div class="speech-bubble-'+classe+'"><p>'+emojis(data.message)+'<br><span class="bubble-span">'+time+'</span></p></div>';
	let	new_msg = container + new_row;

	time = '';
	cont.html(new_msg);
	$('.mensagem_'+data.sender).val('');
	$("#div-container").animate({ scrollTop: $('#div-container').prop('scrollHeight') }, 0);
});

function emojis ( param ) {
	let path =  "public/images/emoticons/";
	let text = param;
	let smiles = {
		":)" : "001.png",
		":D" : "002.png",
		"xD" : "003.png",
		":P" : "004.png",
		";)" : "005.png",
		":S" : "006.png",
		":O" : "007.png",
		"8-)" : "008.png",
		":*" : "009.png",
		":(" : "010.png",
		":'(" : "011.png",
		":|" : "012.png",
		":b" : "013.png",
		"(BOO)" : "014.png",
		"(zZZ)" : "015.png",
		":v" : "016.png",
		"(GRR)" : "017.png",
		":3" : "018.png",
		"@-)" : "019.png",
		"o_O" : "020.png",
		"._." : "021.png",
		"(S2)" : "022.png"
	};

	for (let key in smiles) {
		let value = smiles[key];

		if (text.indexOf(key) !== -1) {
			let count = text.lastIndexOf (key);
			for (let i = 0; i < count; i++) {
				text = text.replace (key, '<img src="'+path+value+'">');
			}
		}
		
	}
	return text;
}

function time_gen () {
	var today = new Date();
	var dd = String(today.getUTCDate()).padStart(2, '0');
	var mm = String(today.getUTCMonth() + 1).padStart(2, '0');
	var yyyy = today.getUTCFullYear();
	var hh = String(today.getUTCHours()).padStart(2, '0');
	var ii = String(today.getUTCMinutes()).padStart(2, '0');
	var ss = String(today.getUTCSeconds()).padStart(2, '0');
	var t = dd + '/' + mm + '/' + yyyy + ' ' + hh + ':' + ii + ':' + ss;;

	return t;
}