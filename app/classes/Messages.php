<?php

class Messages {

    public $id;
	public $user;
	public $hidden;
	public $messages;
    public $conversas;
    private $blocked;

    function __construct ( $id ) {
        if ( !empty ($id) ) {
            if ( is_numeric ($id) ) {
                $this->id = $id;
            } else {
                $this->home_page ();
            }
        }

        $this->user = $_SESSION['id'];
		$this->conversas = $this->conversa();
    }

    public function include_list () {
        $i = 0;
        $this->text_modify ('#title', 'Conversas');
        $a = $this->conversa();
       
        while ( $var = $a->fetch_assoc() ) {
            $hidden = 'trainer_1';
            $other_user = 'trainer_2';
            if ( $var['trainer_1'] == $this->user ) {
                $hidden = 'trainer_1';
                $other_user = 'trainer_2';
			} else if ( $var['trainer_2'] == $this->user ) {
                $hidden = 'trainer_2';
                $other_user = 'trainer_1';
			}
			$read = '';
            
            if ($var[$hidden.'_hidden'] == 0) {
                $var_msg = $this->message($var['id'])->fetch_assoc();
                $user = $this->user($var[$other_user])->fetch_assoc();
                
                $msg = mb_strimwidth($var_msg['message'], 0, 30, '...');                
                if ($var_msg['sender'] != $this->user) {
                    $msg = '<b>'.$user['username'].':</b> '.mb_strimwidth($var_msg['message'], 0, 30, '...');
                    if ($var_msg['seen'] == 0) { 
                        $read = 'style="background: #1d2b3f"';
                        $msg = '<b>'.$msg.'</b>';
                    } else {
                        $read = '';
                    }
                } else {
                    $msg = '<b>Você:</b> '.$msg;
                }

                if ($i == 0) {
                    echo '<form method="post" id="delete">';
                }
                
                echo '<li class="li" '.$read.'>
                            <a href="./inbox&id='.$var['id'].'" class="noanimate">
                                <div>
                                    <table style="float: left; width: 100%">
                                        <tbody>
                                            <tr>
                                                <td style="width: 5%">
                                                    <input type="checkbox" name="messages[]" value="'.$var['id'].'">
                                                </td>
                                                <td style="width: 10%">
                                                    <img src="public/images/characters/'.$user['character'].'/npc.png" style="vertical-align: middle; margin-top: 4px" width="40">
                                                </td>
                                                <td style="width: 67%"><span><a href="./profile&player='.$user['username'].'" class="noanimate" style="max-width: 17%">'.$user['username'].'</a> - <span style="font-weight: bold">'.$var['title'].'</span></span><span style="display: block; font-size: 12px; margin-top: -14px;">'.$msg.'</span></td>
                                                <td style="width: 20%">'.($var_msg['date']).'</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                            </a>
                      </li>';
                $i++;
            }
        }
        
        if ( $i == 0 ) {
            echo '<li class="li" style="text-align: center; color:#fff">Não há Conversas para serem listadas</li>';
        } else {
            echo '</form>';
            echo '<div style="background: #34465f;border-bottom: 2px solid #27374e;"><div style="padding: 10px 0;color: #fff;text-align: center;"><input type="checkbox" style="vertical-align: middle;" onclick="checkAll()">Entre as selecionadas: <br><button id="apagar" style="margin-top: 5px" class="btn">Apagar</button></div></div>';
            echo '<script>
            function checkAll() {
                $(\'input[name="messages[]"]\').trigger(\'click\');
            }
  
            $("#apagar").click (() => {
                let aceitar = confirm("Você realmente deseja apagar essa(s) mensagen(s)?");
                if (aceitar) {
                    $("#delete").submit();
                    return false;
                }
            });

            </script>';
        }
			
    }

    public function include_by_id () {
        if ( $this->conversas->num_rows == 1 ) {
            echo '<div id="message-container">';
			$var = $this->conversa()->fetch_assoc();
			$hidden = '';
			
			if ( $var['trainer_1'] == $this->user ) {
				$hidden = 'trainer_1';
			} else if ( $var['trainer_2'] == $this->user ) {
				$hidden = 'trainer_2';
			} else {
				$this->home_page ();
			}
			
            if ( $var[$hidden.'_hidden'] == 0 ) {
                $this->text_modify ('#title', $var['title']);
                
                $this->messages = $this->message($var['id'], 'ASC');

				while ($var_msg = $this->messages->fetch_assoc()) {
                    $class = 'O';
                    if ($var_msg['sender'] == $this->user) 
                        $class = 'I';
                    
                    echo '<div class="speech-bubble-'.$class.'"><p>'.ubbcode($var_msg['message']).'<br><span class="bubble-span">'.$var_msg['date'].'</span></p></div>';
                }
                
                DB::exQuery ("UPDATE `conversas_messages` SET seen='1' WHERE reciever='$this->user' AND seen='0' AND `conversa`='$var[id]'");

            } else {
                $this->home_page ();
            }
            echo '</div> <script>$("#div-container").animate({ scrollTop: $("#div-container").prop("scrollHeight") }, 0);</script>';
        }  else {
            $this->home_page ();
        }
	}
	
	public function selected_modify ( $el ) {
		echo '<script id="remove">$(".selected").removeClass(); $("'.$el.'").addClass("selected"); document.getElementById("remove").outerHTML = "";</script>';
	}

	public function text_modify ( $el, $text ) {
        echo '<script id="remove">$("'.$el.'").html ("'.$text.'"); document.getElementById("remove").outerHTML = "";</script>';
    }

    public function create_message ( $subject, $title, $message ) {
        $lower_title = strtolower ($title);
        $subject_validate = $this->user ($subject);
        $blocked = $this->blocked ($subject);

        if (!$blocked[0]) {
            if ($subject_validate->num_rows > 0) {
                $reciever = $subject_validate->fetch_assoc();
                
                if ($reciever['user_id'] != $this->user) {
                    $search = DB::exQuery("SELECT * FROM `conversas` WHERE (trainer_1='$this->user' OR trainer_2='$this->user') AND (trainer_1='$reciever[user_id]' OR trainer_2='$reciever[user_id]') AND (trainer_1_hidden='0' OR trainer_2='0') AND title='$lower_title'");
                    
                    if ($search->num_rows > 0) {
                        echo '<div class="red">Você já tem uma conversa com este treinador com o mesmo título!</div>';
                    } else {
                        echo '<div class="green">Mensagem enviada para '.$subject.'!</div>';
                        $date = date ('d/m/Y H:i:s');
                        DB::exQuery("INSERT INTO `conversas` (`trainer_1`, `trainer_2`, `title`, `last_message`) VALUES ('$this->user', '$reciever[user_id]', '$title', '$date')");
                        $conversa = DB::insertID();

                        $this->send_message( $conversa, $message, $reciever['user_id'] );
                    }
                } else {
                    echo '<div class="red">Você não pode mandar mensagens para si!</div>';
                }
            } else {
                echo '<div class="red">Este usuário não existe!</div>';
            }
        } else {
            echo $this->blocked_msg();
        }
    }

    public function send_message ( $conversa, $message, $reciever ) {
        $blocked = $this->blocked ($reciever);

        if (!$blocked[0]) {
            $date = date ('d/m/Y H:i:s');
            $message = mb_strimwidth($message, 0, 1000, '');
            
            DB::exQuery("INSERT INTO `conversas_messages` (`conversa`, `sender`, `reciever`, `message`, `date`) VALUES ('$conversa', '$this->user', '$reciever', '$message', '$date')");
        } else {
            echo $this->blocked_msg ();
        }
    }

    public function blocked ($reciever = '') {
        $controller = false;
        $who = '';

        if (empty($reciever)) {
            $var = $this->conversa()->fetch_assoc();
        } else {
            $var['trainer_1'] = $this->user;
            $var['trainer_2'] = $this->user( $reciever )->fetch_assoc()['user_id'];
        }

        if ( $var['trainer_1'] == $this->user ) {
            $list = $this->user;
            $trainer = $var['trainer_2'];
        } else if ( $var['trainer_2'] == $this->user ) {
            $list = $var['trainer_2'];
            $trainer = $var['trainer_1'];
        }
        
        $blocklist = $this->user( $list )->fetch_assoc()['blocklist'];

        if (!empty ($blocklist)) {
            $blocklist = explode (',', $blocklist);
            if (in_array($trainer, $blocklist)) {
                $controller = true;
                $who = $trainer;
            }
        }

        $blocklist = $this->user( $trainer )->fetch_assoc()['blocklist'];

        if (!empty ($blocklist)) {
            $blocklist = explode (',', $blocklist);
            if (in_array($list, $blocklist)) {
                $controller = true;
                $who = $list;
            }
        }

        $this->blocked = array($controller, $who);
        return $this->blocked;
    }

    public function delete_conversa ($id) {
        $valid = DB::exQuery ("SELECT * FROM `conversas` WHERE trainer_1='$this->user' AND trainer_1_hidden='0' AND id IN ('$id')")->num_rows;
        $valid2 = DB::exQuery ("SELECT * FROM `conversas` WHERE trainer_2='$this->user' AND trainer_2_hidden='0' AND id IN ('$id')")->num_rows;

        if ($valid > 0) {
            DB::exQuery ("UPDATE `conversas` SET trainer_1_hidden='1' WHERE id IN ('$id')");
        }

        if ($valid2 > 0) {
            DB::exQuery ("UPDATE `conversas` SET trainer_2_hidden='1' WHERE id IN ('$id')");
        }

        $valid += $valid2;

        return '<div class="green">'.$valid.' conversas foram apagadas!</div>';
    }

    public function blocked_msg () {
        $var = $this->blocked;

        if ($var[1] == $this->user) {
            return '<div class="red">Você foi bloqueado por este treinador, portanto não poderá mandar ou receber mensagens dele!</div>';
        } else {
            return '<div class="red">Você bloqueou este treinador, portanto não poderá mandar ou receber mensagens dele!</div>';
        }
    }

    public function conversa ( $order = 'DESC' ) {
		if ( empty ( $this->id ) ) {
			return DB::exQuery ("SELECT * FROM `conversas` WHERE (trainer_1='$this->user' OR trainer_2='$this->user') ORDER BY STR_TO_DATE(last_message, '%d/%m/%Y %H:%i:%s') $order");
		} else {
			return DB::exQuery ("SELECT * FROM `conversas` WHERE (trainer_1='$this->user' OR trainer_2='$this->user') AND id='$this->id' ORDER BY last_message $order");
		}
    }

    protected function message ( $conversa, $order = 'DESC' ) {
		return DB::exQuery ("SELECT * FROM `conversas_messages` WHERE conversa='$conversa' ORDER BY id $order");
    }

    protected function user ( $id ) {
        if (ctype_digit($id)) {
            return DB::exQuery("SELECT * FROM `gebruikers` WHERE user_id='$id'");
        } else {
            return DB::exQuery("SELECT * FROM `gebruikers` WHERE username='$id'");
        }
    }

    protected function home_page () {
        header('Location: ./inbox'); 
        exit;
    }

}