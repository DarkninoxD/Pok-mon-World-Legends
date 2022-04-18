<?php

class Official {

    public $id;
    public $user;
    public $messages;

    function __construct ( $id ) {
        if ( !empty ($id) ) {
            if ( is_numeric ($id) ) {
                $this->id = $id;
            } else {
                $this->home_page ();
            }
        }

        $this->user = $_SESSION['id'];
        $this->messages = $this->message ();
    }

    public function include_list () {
        $i = 0;
        $this->text_modify ('#title', 'Mensagens Oficiais');

        while ( $rel = $this->messages->fetch_assoc () ) {
            $this->id = $rel['id'];
            $new = $this->read();
            
            if (!$new) {
                echo '<li class="li" style="text-center"><a href="./official-messages&id='.$rel['id'].'" class="noanimate"><div><b>'.$rel['title'].'<span class="span">'.$rel['date'].'</span></b></div></a></li>';
            } else {
                echo '<li class="li" style="text-center"><a href="./official-messages&id='.$rel['id'].'" class="noanimate"><div>'.$rel['title'].'<span class="span">'.$rel['date'].'</span></div></a></li>';
            }
            $i++;
        }

        if ( $i == 0 ) {
            echo '<li style="text-align: center">Não há Mensagens Oficiais para serem listadas</li>';
        }
    }

    public function include_by_id () {
        if ( $this->messages->num_rows > 0 ) {
            $var = $this->messages->fetch_assoc();
            if ( $var['hidden'] == 0 ) {
                $this->set_read();
                $this->text_modify('#title', $var['title'].'<br>Postado em: '.$var['date']);
            
                echo htmlspecialchars_decode($var['message']);
            } else {
                $this->home_page ();
            }
        }  else {
            $this->home_page ();
        }
    }

    protected function message () {
        if ( empty ($this->id) ) {
            return DB::exQuery ("SELECT * FROM `official_message` WHERE hidden='0' ORDER BY id DESC");
        } else {
            return DB::exQuery ("SELECT * FROM `official_message` WHERE id='$this->id' AND hidden='0'");
        }
    }

    protected function read () {
        return DB::exQuery("SELECT * FROM `official_message_read` WHERE id_msg='$this->id' AND id_user='$this->user'")->num_rows;
    }

    protected function set_read () {
        $var = $this->read();

        if (!$var) {
            DB::exQuery("INSERT INTO `official_message_read` (`id_msg`, `id_user`) VALUES ('$this->id', '$this->user')");
        }
    }

    protected function home_page () {
        header('Location: ./official-messages'); 
        exit;
    }

    protected function text_modify ( $el, $text ) {
        echo '<script id="remove">$("'.$el.'").html ("'.$text.'"); document.getElementById("remove").outerHTML = "";</script>';
    }

}
