<?php
/**
 * Description of League
 *
 * @author Bruno Agenor
 */
class League {

    private $id;
    private $regiao = "Kanto";
    private $total_participantes = 64;
    private $participantes = 0;
    private $inicio_inscricoes = null;
    private $fim_inscricoes = null;
    private $inicio = null;
    private $intervalo_fase = 1800;
    private $round_atual = 0;
    private $n_lendas = 6;
    private $n_shinys = 6;
    private $n_megas = 6;
    private $lv_max_pokemon = 100;
    private $preco_silvers = 0;
    private $preco_golds = 0;
    private $mods = false;
    private $admins = false;
    private $donos = false;
    private $vip = false;
    private $premiacao_entregue = 0;
    private $em_operacao = 0;
    private $insignias = array(
        'Kanto' => array(
            'Boulder',
            'Cascade',
            'Thunder',
            'Rainbow',
            'Marsh',
        /* 'Soul',
          'Volcano',
          'Earth' */
        ),
        'Johto' => array(
            'Zephyr',
            'Hive',
            'Plain',
            'Fog',
            'Storm',
            'Mineral',
            'Glacier',
            'Rising'
        )
    );
     public $erros = array();
   //public static $ajuste_tempo_int = -14537; //- (4 * 60 * 60 + 2 * 60 + 17);
   //public static $ajuste_tempo_string = " - INTERVAL 4 HOUR - INTERVAL 2 MINUTE - INTERVAL 17 SECOND";
	 
   //public static $ajuste_tempo_int = -14620; //- (4 * 60 * 60 + 4 * 60 + 17);
   //public static $ajuste_tempo_string = " - INTERVAL 4 HOUR - INTERVAL 3 MINUTE - INTERVAL 40 SECOND";	 
	 
     public static $ajuste_tempo_int = ""; //- (4 * 60 * 60 + 2 * 60 + 17);
     public static $ajuste_tempo_string = "";


    public function getId() {
        return $this->id;
    }

    public function getRegiao() {
        return $this->regiao;
    }

    public function getTotal_participantes() {
        return $this->total_participantes;
    }

    public function getParticipantes() {
        return $this->participantes;
    }

    public function getInicio_inscricoes() {
        return $this->inicio_inscricoes;
    }

    public function getFim_inscricoes() {
        return $this->fim_inscricoes;
    }

    public function getInicio() {
        return $this->inicio;
    }

    public function getIntervalo_fase() {
        return $this->intervalo_fase;
    }

    public function getRound_atual() {
        return $this->round_atual;
    }

    public function getN_lendas() {
        return $this->n_lendas;
    }

    public function getN_shinys() {
        return $this->n_shinys;
    }

    public function getN_megas() {
        return $this->n_megas;
    }

    public function getLv_max_pokemon() {
        return $this->lv_max_pokemon;
    }

    public function getPreco_silvers() {
        return $this->preco_silvers;
    }

    public function getPreco_golds() {
        return $this->preco_golds;
    }

    public function getMods() {
        return $this->mods;
    }

    public function getAdmins() {
        return $this->admins;
    }

    public function getDonos() {
        return $this->donos;
    }

    public function getVip() {
        return $this->vip;
    }

    public function getPremiacao_entregue() {
        return $this->premiacao_entregue;
    }

    public function em_operacao() {
        return $this->em_operacao;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function setRegiao($regiao) {
        $this->regiao = $regiao;
    }

    public function setTotal_participantes($total_participantes) {
        $this->total_participantes = $total_participantes;
    }

    public function setParticipantes($participantes) {
        $this->participantes = $participantes;
    }

    public function setInicio_inscricoes($inicio_inscricoes) {
        $this->inicio_inscricoes = $inicio_inscricoes;
    }

    public function setFim_inscricoes($fim_inscricoes) {
        $this->fim_inscricoes = $fim_inscricoes;
    }

    public function setInicio($inicio) {
        $this->inicio = $inicio;
    }

    public function setIntervalo_fase($intervalo_fase) {
        $this->intervalo_fase = $intervalo_fase;
    }

    public function setRound_atual($round_atual) {
        $this->round_atual = $round_atual;
    }

    public function setN_lendas($n_lendas) {
        $this->n_lendas = $n_lendas;
    }

    public function setN_shinys($n_shinys) {
        $this->n_shinys = $n_shinys;
    }

    public function setN_megas($n_megas) {
        $this->n_megas = $n_megas;
    }

    public function setLv_max_pokemon($lv_max_pokemon) {
        $this->lv_max_pokemon = $lv_max_pokemon;
    }

    public function setPreco_silvers($preco_silvers) {
        $this->preco_silvers = $preco_silvers;
    }

    public function setPreco_golds($preco_golds) {
        $this->preco_golds = $preco_golds;
    }

    public function setMods($mods) {
        $this->mods = $mods;
    }

    public function setAdmins($admins) {
        $this->admins = $admins;
    }

    public function setDonos($donos) {
        $this->donos = $donos;
    }

    public function setVip($vip) {
        $this->vip = $vip;
    }

    public function setPremiacao_entregue($premiacao_entregue) {
        $this->premiacao_entregue = $premiacao_entregue;
    }

    public function setEm_operacao($em_operacao) {
        $this->em_operacao = $em_operacao;
    }

    public function cont_rounds() {
        $cont = 0;

        $participantes = $this->total_participantes;

        while ($participantes != 1) {
            $participantes /= 2;
            $cont++;
        }

        return $cont;
    }

    public function inscrever($user_id) {

        //NOW() - INTERVAL 4 HOUR - INTERVAL 2 MINUTE - INTERVAL 17 SECOND
        $time = time() + League::$ajuste_tempo_int;

        if ($time < strtotime($this->inicio_inscricoes)) {
            $this->erros[] = "As inscrições ainda não começaram!";
        }
        if ($time > strtotime($this->fim_inscricoes)) {
            $this->erros[] = "As inscrições já foram encerradas!";
        }
        if ($this->participantes >= $this->total_participantes) {
            $this->erros[] = "Todas as vagas já foram preenchidas!";
        }

        if (count($this->erros) > 0) {
            return false;
        }

        $result = DB::exQuery("SELECT * FROM gebruikers WHERE user_id = '$user_id'");

        $user = $result->fetch_assoc();
        
        $result2 = DB::exQuery("SELECT gold FROM rekeningen WHERE acc_id = '$user[acc_id]'");

        $user2 = $result2->fetch_assoc();

        if ($user['silver'] < $this->preco_silvers) {
            $this->erros[] = "Você não tem silvers suficientes!";
        }
        if ($user2['gold'] < $this->preco_golds) {
            $this->erros[] = "Você não tem golds suficientes!";
        }
        if ($this->vip && $user['premiumaccount'] < time()) {
            $this->erros[] = "Você precisa ser vip para participar!";
        }

        if (!$this->mods && $user['admin'] == 1) {
            $this->erros[] = "Os moderadores não podem participar!";
        } else if (!$this->admins && $user['admin'] == 2) {
            $this->erros[] = "Os administradores não podem participar!";
        } else if (!$this->donos && $user['admin'] == 3) {
            $this->erros[] = "Os donos do jogo não podem participar!";
        }

        // if (count(explode(',', $user['pok_bezit'])) < 7) {
        //     $this->erros[] = "Você precisa ter pelo menos 6 pokémon!";
        // }

        if (count($this->erros) > 0) {
            return false;
        }

        if ($this->total_participantes > 16) {
            $result = DB::exQuery("SELECT * FROM gebruikers_badges WHERE user_id = '$user_id'");

            $badges = $result->fetch_assoc();

            foreach ($this->insignias[$this->regiao] as $insignia) {
                if (!$badges[$insignia]) {
                    $this->erros[] = "Você precisa ter 5 insígnias da região de " . $this->regiao . "! <a href=\"./attack/gyms\">Ir para os ginásios -></a>";
                    break;
                }
            }

            if (count($this->erros) > 0) {
                return false;
            }
        }

        if (DB::exQuery("SELECT * FROM `league_participant` WHERE user_id='$user_id' AND league_id='$this->id'")->num_rows > 0) {
            $this->erros[] = "Você já esta participando!";
        }
        
        if (count($this->erros) > 0) {
            return false;
        }
        
        $participantes = DB::exQuery("SELECT COUNT(*) FROM `league_participant` WHERE `league_id`='$this->id'")->fetch_array['0'];
        
        if ($participantes >= $this->total_participantes) {
            $this->erros[] = "Todas as vagas já foram preenchidas!";
            return false;
        }

        if ($this->total_participantes > 16) {
            $sql_desconta_insignias = "UPDATE gebruikers_badges SET ";
            for ($i = 0; $i < count($this->insignias[$this->regiao]); $i++) {
                $sql_desconta_insignias .= "`" . $this->insignias[$this->regiao][$i] . "`" . ($i == (count($this->insignias[$this->regiao]) - 1) ? "='0'" : "='0', ");
            }
            $sql_desconta_insignias .= " WHERE user_id = '$user_id'";
            DB::exQuery($sql_desconta_insignias);

            if (count($this->erros) > 0) {
                $this->erros[] = "Não foi possivel realizar a sua inscrição! #1";
                return false;
            }
        }

        DB::exQuery("UPDATE gebruikers SET silver = silver - $this->preco_silvers WHERE user_id = $user_id");
        DB::exQuery("UPDATE rekeningen SET gold = gold - $this->preco_golds WHERE acc_id = $user[acc_id]");

        if (count($this->erros) > 0) {
            if ($this->total_participantes > 16) {
                $sql_desconta_insignias = "UPDATE gebruikers_badges SET ";
                for ($i = 0; $i < count($this->insignias[$this->regiao]); $i++) {
                    $sql_desconta_insignias .= "`" . $this->insignias[$this->regiao][$i] . "`" . ($i == (count($this->insignias[$this->regiao]) - 1) ? "='1'" : "='1', ");
                }
                $sql_desconta_insignias .= " WHERE user_id = '$user_id'";
                DB::exQuery($sql_desconta_insignias);
            }

            $this->erros[] = "Não foi possivel realizar a sua inscrição! #2";
            return false;
        }

        DB::exQuery("INSERT INTO league_participant(user_id, league_id, inscricao, ativo) VALUES('$user_id', '$this->id', NOW(), '1')");

        if (count($this->erros) > 0) {
            if ($this->total_participantes > 16) {
                $sql_desconta_insignias = "UPDATE gebruikers_badges SET ";
                for ($i = 0; $i < count($this->insignias[$this->regiao]); $i++) {
                    $sql_desconta_insignias .= "`" . $this->insignias[$this->regiao][$i] . "`" . ($i == (count($this->insignias[$this->regiao]) - 1) ? "='1'" : "='1', ");
                }
                $sql_desconta_insignias .= " WHERE user_id = '$user_id'";
                DB::exQuery($sql_desconta_insignias);
            }

            DB::exQuery("UPDATE gebruikers SET silver = silver + $this->preco_silvers WHERE user_id = $user_id");
	    DB::exQuery("UPDATE rekeningen SET gold = gold + $this->preco_golds WHERE acc_id = $user[acc_id]");

            $this->erros[] = "Não foi possivel realizar a sua inscrição! #3";
            return false;
        }

        $this->participantes = DB::exQuery("SELECT * FROM `league_participant` WHERE `league_id`='$this->id'")->num_rows;
        $this->update();
        //$log_liga = fopen("liga.log", 'a');
        //fwrite($log_liga, ">[INSC] O Jogador " . $user['user_id'] . ":" . $user['username'] . " realizou a inscrição na liga $this->id e ocupou a vaga $this->participantes às " . date("Y-m-d H:i:s") . "\n");
        //fclose($log_liga);
        return true;
    }

    public function desfazer_inscricao($user_id) {
        DB::exQuery("DELETE FROM league_participant WHERE user_id = '$user_id' AND league_id = $this->id");

       if (count($this->erros) > 0) {
            $this->erros[] = "Não foi possivel desfazer a sua inscrição!";
            return false;
        }

        $this->participantes--;

       // $log_liga = fopen("liga.log", 'a');
       // fwrite($log_liga, ">[DESC] O Jogador $user_id saiu da liga $this->id e agora a liga possui $this->participantes participantes às " . date("Y-m-d H:i:s") . "\n");
       // fclose($log_liga);

        $this->update();
    }

    public function passar_round() {

        foreach (League_battle::select_nao_terminadas($this->round_atual) as $batalha_empacada) {
            if ($this->erro_duelo($batalha_empacada->getUser_id1())) {
                $batalha_empacada->setVencedor($batalha_empacada->getUser_id2());
            } else if ($this->erro_duelo($batalha_empacada->getUser_id2())) {
                $batalha_empacada->setVencedor($batalha_empacada->getUser_id1());
            } else if ($batalha_empacada->getPontos_user1() > $batalha_empacada->getPontos_user2()) {
                $batalha_empacada->setVencedor($batalha_empacada->getUser_id1());
            } else if ($batalha_empacada->getPontos_user1() < $batalha_empacada->getPontos_user2()) {
                $batalha_empacada->setVencedor($batalha_empacada->getUser_id2());
            } else {
                $poder_pokes1 = DB::exQuery("SELECT SUM(`attack` + `defence` + `speed` + "
                                . "`spc.attack` + `spc.defence`) AS poder_total, SUM(`level`) AS level_total FROM "
                                . "`pokemon_speler` ps WHERE `opzak` = 'ja' "
                                . "AND ps.`user_id` = '" . $batalha_empacada->getUser_id1() . "'")->fetch_assoc();

                $poder_pokes2 = DB::exQuery("SELECT SUM(`attack` + `defence` + `speed` + "
                                . "`spc.attack` + `spc.defence`) AS poder_total, SUM(`level`) AS level_total FROM "
                                . "`pokemon_speler` ps WHERE `opzak` = 'ja' "
                                . "AND ps.`user_id` = '" . $batalha_empacada->getUser_id2() . "'")->fetch_assoc();

                if ($poder_pokes1['poder_total'] > $poder_pokes1['poder_total']) {
                    $batalha_empacada->setVencedor($batalha_empacada->getUser_id1());
                } else if ($poder_pokes1['poder_total'] < $poder_pokes1['poder_total']) {
                    $batalha_empacada->setVencedor($batalha_empacada->getUser_id2());
                } else if ($poder_pokes1['level_total'] > $poder_pokes2['level_total']) {
                    $batalha_empacada->setVencedor($batalha_empacada->getUser_id1());
                } else if ($poder_pokes1['level_total'] < $poder_pokes2['level_total']) {
                    $batalha_empacada->setVencedor($batalha_empacada->getUser_id2());
                } else {
                    $sql = "SELECT gb.user_id, (gb.gewonnen + gb.verloren) AS victories, SUM(ps.`level`) AS lv_sum, 
                        AVG(ps.`level`) AS lv_average, (((gb.gewonnen + gb.verloren)/10) + (AVG(ps.`level`) * 10) + 
                        (SUM(ps.`level`) * 0) + (gb.badges * 5)) AS total_points FROM gebruikers AS gb JOIN 
                        pokemon_speler AS ps ON gb.user_id = ps.user_id WHERE 
                        gb.user_id IN (" . $batalha_empacada->getUser_id1() . "," . $batalha_empacada->getUser_id2() . ") 
                        AND gb.banned='N' GROUP BY gb.user_id
                        ORDER BY total_points DESC, gb.rank DESC, gb.rankexp DESC, gb.username ASC LIMIT 1";
		    $winner = DB::exQuery($sql)->fetch_assoc();	
                    $batalha_empacada->setVencedor($winner['user_id']);
                }
            }

            DB::exQuery("DELETE FROM `duel` WHERE `id`='" . $batalha_empacada->getDuel_id() . "'");
            $batalha_empacada->setDuel_id("'+null+'");
            $batalha_empacada->setTermino(date("Y-m-d H:i:s"));
            $batalha_empacada->update();
        }

        if ($this->total_participantes <= 16) {
            switch ($this->round_atual) {
                case 0:
                    if ($this->participantes < $this->total_participantes) {
                        $this->fim_inscricoes = date("Y-m-d H:i:s", strtotime($this->fim_inscricoes) + 300);
                        $this->inicio = date("Y-m-d H:i:s", strtotime($this->inicio) + 300);
                        $this->update();
                        
                        return false;
                    }
                    $this->sortear_matamata();
                    return true;
                default:
                    if ($this->round_atual == $this->cont_rounds()) {
                        return false;
                    }
                    $this->passar_round_matamata();
                    return true;
            }
        } else {
            switch ($this->round_atual) {
                case 0:
                case 1:
                case 2:
                case 3:
                    $this->sortear_preeliminares();
                    return true;
                case 4:
                    if ($this->participantes < ($this->total_participantes - ($this->total_participantes / 8))) {
                        $this->fim_inscricoes = date("Y-m-d H:i:s", strtotime($this->fim_inscricoes) + 1800);
                        $this->inicio = date("Y-m-d H:i:s", strtotime($this->inicio) + 1800);
                        $this->update();
                        
                        return false;
                    }
                    $this->sortear_matamata();
                    return true;
                default:
                    if ($this->round_atual == $this->cont_rounds()) {
                        return false;
                    }
                    $this->passar_round_matamata();
                    return true;
            }
        }
    }

    private function sortear_preeliminares() {
        set_time_limit(0);

        $this->round_atual++;
        $this->update();
        
        $campos = array('water', 'ice', 'rock', 'grass');

        foreach ($campos as $n_campo => $campo) {
            $sql = "SELECT lp.user_id FROM league_participant lp WHERE lp.league_id='$this->id' AND lp.ativo='1' "
                    . "AND (SELECT COUNT(lb.id) FROM league_battle lb WHERE lb.league_id='$this->id' AND "
                    . "lb.round='$this->round_atual' AND (lp.user_id = lb.user_id1 OR lp.user_id = lb.user_id2))=0 ";

            if ($this->round_atual > 1) {
                $campo_anterior = null;
                if (isset($campos[array_search($campo, $campos) + 1])) {
                    $campo_anterior = $campos[array_search($campo, $campos) + 1];
                } else {
                    $campo_anterior = $campos[0];
                }
                $sql .= "AND (SELECT lb2.campo FROM league_battle lb2 WHERE "
                        . "lb2.league_id='$this->id' AND lb2.round='" . ($this->round_atual - 1) . "' AND "
                        . "lb2.vencedor=lp.user_id)='$campo_anterior' ";
            }

            if ($this->round_atual > 1) {
                $sql.= "ORDER BY RAND() LIMIT " . ($this->participantes / 4);
            } else {
                $sql.= "ORDER BY RAND() LIMIT " . ($this->total_participantes / 4);
            }
            $result = DB::exQuery($sql);
            $battle = null;
            $cont = 1;
            $cont2 = 1;

            while ($participants = $result>fetch_assoc()) {

                if ($cont % 2 != 0) {
                    $battle = new League_battle();
                    $battle->setLeague_id($this->id);
                    $battle->setRound($this->round_atual);
                    $battle->setCampo($campo);
                    $battle->setN_pokemons(3);
                    if ($this->round_atual == 1) {
                        $battle->setInicio($this->inicio);
                    } else {
                        $result_inicio = DB::exQuery("SELECT inicio FROM league_battle "
                                . "WHERE league_id = '$this->id' AND round='" . ($this->round_atual - 1) . "' LIMIT 1");
                        $inicio_anterior = $result_inicio->fetch_array()['0'];

                        $battle->setInicio(date("Y-m-d H:i:s", strtotime($inicio_anterior) + $this->intervalo_fase));
                    }

                    $battle->setUser_id1($participants['user_id']);
                    if ($this->round_atual == 1 && $this->participantes < $this->total_participantes && $n_campo == 3 && $cont2 >= (($this->total_participantes / 4) - (($this->total_participantes - $this->participantes) * 2))) {
                        $battle->setVencedor($participants['user_id']);
                        $battle->setTermino(date("Y-m-d H:i:s"));
                        $cont++;
                    }
                    $battle->insert();
                } else {
                    $battle->setUser_id2($participants['user_id']);
                    $battle->update();
                    $battle = null;
                }

                $cont++;
                $cont2++;
            }
        }
        $this->participantes /= 2;
        $this->update();
    }

    public function lista_preeliminares($round = 1) {
        $batalhas = array();

        $campos = array('water', 'ice', 'rock', 'grass');
        foreach ($campos as $campo) {

            $batalhas[$campo] = array();
            $sql = "SELECT lb.user_id1 as user1_id, lb.user_id2 as user2_id, user1.username as user1_username, user2.username as user2_username, "
                    . "lb.pontos_user1 as user1_pontos, lb.pontos_user2 as user2_pontos, lb.vencedor"
                    . " FROM league_battle lb, gebruikers user1, gebruikers user2 WHERE "
                    . "lb.user_id1 = user1.user_id AND lb.user_id2 = user2.user_id AND "
                    . "lb.league_id = '$this->id' AND lb.campo='$campo' AND lb.round='$round' ORDER BY lb.id";
            $result = DB::exQuery($sql);

            while ($batalha = $result->fetch_assoc()) {
                $batalhas[$campo][] = $batalha;
            }

            $sql2 = "SELECT lb.user_id1 as user1_id, user1.username as user1_username, "
                    . "lb.pontos_user1 as user1_pontos, lb.vencedor"
                    . " FROM league_battle lb, gebruikers user1 WHERE "
                    . "lb.user_id1 = user1.user_id AND lb.user_id2='0' AND "
                    . "lb.league_id = '$this->id' AND lb.campo='$campo' AND lb.round='$round' ORDER BY lb.id";
            $result2 = DB::exQuery($sql2);

            while ($batalha2 = $result2->fetch_assoc()) {
                $batalhas[$campo][] = $batalha2;
            }
        }

        return $batalhas;
    }

    private function sortear_matamata() {

        set_time_limit(0);

        $this->round_atual++;
        $this->update();

        $sql = "SELECT `user_id` FROM `league_participant` WHERE `league_id`='$this->id' AND `ativo`='1' ORDER BY RAND()";
        $result = DB::exQuery($sql);
        $battle = null;
        $cont = 1;

        while ($participants = $result->fetch_assoc()) {

            if ($cont % 2 != 0) {
                $battle = new League_battle();
                $battle->setLeague_id($this->id);
                $battle->setRound($this->round_atual);
                $battle->setCampo('main');
                $battle->setN_pokemons(6);

                if ($this->round_atual == 1) {
                    $battle->setInicio($this->inicio);
                } else {
                    $result_inicio = DB::exQuery("SELECT inicio FROM league_battle "
                            . "WHERE league_id = '$this->id' AND round='" . ($this->round_atual - 1) . "' LIMIT 1");
                    $inicio_anterior = $result_inicio->fetch_array()['0'];

                    $battle->setInicio(date("Y-m-d H:i:s", strtotime($inicio_anterior) + $this->intervalo_fase));
                }

                $battle->setUser_id1($participants['user_id']);
                $battle->insert();
            } else {
                $battle->setUser_id2($participants['user_id']);
                $battle->update();
                $battle = null;
            }

            $cont++;
        }

        $this->participantes /= 2;
        $this->update();
    }

    private function passar_round_matamata() {
        set_time_limit(0);

        $this->round_atual++;
        $this->update();

        $sql = "SELECT `vencedor` FROM `league_battle` WHERE `league_id`='$this->id' "
                . "AND `round`='" . ($this->round_atual - 1) . "' ORDER BY `id` ASC";
        $result = DB::exQuery($sql);
        $battle = null;
        $cont = 1;

        while ($participants = $result->fetch_assoc()) {

            if ($cont % 2 != 0) {
                $battle = new League_battle();
                $battle->setLeague_id($this->id);
                $battle->setRound($this->round_atual);
                $battle->setCampo('main');
                $battle->setN_pokemons(6);

                $result_inicio = DB::exQuery("SELECT inicio FROM league_battle "
                        . "WHERE league_id = '$this->id' AND round='" . ($this->round_atual - 1) . "' LIMIT 1");
                $inicio_anterior = $result_inicio->fetch_array()['0'];
                $battle->setInicio(date("Y-m-d H:i:s", strtotime($inicio_anterior) + $this->intervalo_fase));

                $battle->setUser_id1($participants['vencedor']);
                $battle->insert();
            } else {
                $battle->setUser_id2($participants['vencedor']);
                $battle->update();
                $battle = null;
            }

            $cont++;
        }

        if ($this->participantes == 2) {
            $this->criar_disputa_3lugar();
        }
        $this->participantes /= 2;
        $this->update();
    }

    public function lista_matamata() {
        $batalhas = array();

        $sql = "SELECT lb.user_id1 as user1_id, lb.user_id2 as user2_id, user1.username as user1_username, user2.username as user2_username, "
                . "lb.pontos_user1 as user1_pontos, lb.pontos_user2 as user2_pontos, lb.vencedor, lb.round"
                . " FROM league_battle lb, gebruikers user1, gebruikers user2 WHERE "
                . "lb.user_id1 = user1.user_id AND lb.user_id2 = user2.user_id AND "
                . "lb.league_id = '$this->id' " . ($this->total_participantes > 16 ? "AND lb.round>'4' " : "")
                . "ORDER BY lb.id";
        $result = DB::exQuery($sql);

        while ($batalha = $result->fetch_assoc()) {
            $batalhas[$batalha['round']][] = $batalha;
        }
        return $batalhas;
    }

    private function criar_disputa_3lugar() {

        $sql = "SELECT lb.user_id1, lb.user_id2, lb.vencedor FROM league_battle lb WHERE lb.league_id='$this->id' "
                . "AND round='" . ($this->round_atual - 1) . "' ORDER BY lb.id ASC";
        $result = DB::exQuery($sql);
        $battle = null;
        $cont = 1;

        while ($participants = $result->fetch_assoc()) {

            $player = null;

            if ($participants['vencedor'] == $participants['user_id1']) {
                $player = $participants['user_id2'];
            } else {
                $player = $participants['user_id1'];
            }

            if ($cont % 2 != 0) {
                $battle = new League_battle();
                $battle->setLeague_id($this->id);
                $battle->setRound(30);
                $battle->setCampo('main');
                $battle->setN_pokemons(6);

                $result_inicio = DB::exQuery("SELECT inicio FROM league_battle "
                        . "WHERE league_id = '$this->id' AND round='" . ($this->round_atual - 1) . "' LIMIT 1");
                $inicio_anterior = $result_inicio->fetch_array()['0'];
                $battle->setInicio(date("Y-m-d H:i:s", strtotime($inicio_anterior) + $this->intervalo_fase));


                $battle->setUser_id1($player);
                $battle->insert();
            } else {
                $battle->setUser_id2($player);
                $battle->update();
                $battle = null;
            }

            $cont++;
        }
    }

    public function tabela_matamata() {

        $n_rounds = $this->cont_rounds();
        $batalhas = $this->lista_matamata();
        $round_mata_mata = 5;

        if ($this->total_participantes <= 16) {
            $round_mata_mata = 1;
        }

        $tabela = "<script type='text/javascript'>
            (function(win, doc, $) {
                win.data_$this->id = [";
        for ($round = $round_mata_mata, $n = count($batalhas[$round_mata_mata]); $round <= $n_rounds; $round++, $n /= 2) {
            $tabela .="[";
            for ($j = 0; $j < $n; $j++) {
                $tabela .= "[{'name': '";

                if (isset($batalhas[$round][$j]['user1_username'])) {
                    $tabela .= $batalhas[$round][$j]['user1_username'];
                } else if (isset($batalhas[$round - 1][2 * $j]['vencedor']) && $batalhas[$round - 1][2 * $j]['vencedor']) {
                    $tabela .= ($batalhas[$round - 1][2 * $j]['vencedor'] == $batalhas[$round - 1][2 * $j]['user1_id'] ? $batalhas[$round - 1][2 * $j]['user1_username'] : $batalhas[$round - 1][2 * $j]['user2_username']);
                } else {
                    $tabela .= "?????";
                }

                $tabela .= "', 'id': '";
                if (isset($batalhas[$round][$j]['user1_id'])) {
                    $tabela .= $batalhas[$round][$j]['user1_id'] . "_" . $this->id;
                } else if (isset($batalhas[$round - 1][2 * $j]['vencedor']) && $batalhas[$round - 1][2 * $j]['vencedor']) {
                    $tabela .= $batalhas[$round - 1][2 * $j]['vencedor'] . "_" . $this->id;
                } else {
                    $tabela .= "0_$this->id";
                }

                $tabela .= "', "
                        . "'score': " . (isset($batalhas[$round][$j]['user1_pontos']) ? $batalhas[$round][$j]['user1_pontos'] : "0") . "}, "
                        . "{'name': '";

                if (isset($batalhas[$round][$j]['user2_username'])) {
                    $tabela .= $batalhas[$round][$j]['user2_username'];
                } else if (isset($batalhas[$round - 1][2 * $j + 1]['vencedor']) && $batalhas[$round - 1][2 * $j + 1]['vencedor']) {
                    $tabela .= ($batalhas[$round - 1][2 * $j + 1]['vencedor'] == $batalhas[$round - 1][2 * $j + 1]['user1_id'] ? $batalhas[$round - 1][2 * $j + 1]['user1_username'] : $batalhas[$round - 1][2 * $j + 1]['user2_username']);
                } else {
                    $tabela .= "?????";
                }

                $tabela .= "', 'id': '";

                if (isset($batalhas[$round][$j]['user2_id'])) {
                    $tabela .= $batalhas[$round][$j]['user2_id'] . "_" . $this->id;
                } else if (isset($batalhas[$round - 1][2 * $j + 1]['vencedor']) && $batalhas[$round - 1][2 * $j + 1]['vencedor']) {
                    $tabela .= $batalhas[$round - 1][2 * $j + 1]['vencedor'] . "_" . $this->id;
                } else {
                    $tabela .="0_$this->id";
                }

                $tabela .="', "
                        . "'score': " . (isset($batalhas[$round][$j]['user2_pontos']) ? $batalhas[$round][$j]['user2_pontos'] : "0") . "}],";
            }
            $tabela .="],";
        }

        $nome_capeao = "?????";
        $id_capeao = "0";

        if (isset($batalhas[$n_rounds]['0']['vencedor']) && !empty($batalhas[$n_rounds]['0']['vencedor'])) {
            if ($batalhas[$n_rounds]['0']['vencedor'] == $batalhas[$n_rounds]['0']['user1_id']) {
                $nome_capeao = $batalhas[$n_rounds]['0']['user1_username'];
                $id_capeao = $batalhas[$n_rounds]['0']['user1_id'];
            } else {
                $nome_capeao = $batalhas[$n_rounds]['0']['user2_username'];
                $id_capeao = $batalhas[$n_rounds]['0']['user2_id'];
            }
        }

        $tabela .=
                "
                    [
                        [{'name': '$nome_capeao', 'id': '$id_capeao" . "_$this->id', 'displaySeed': '1º'}]
                    ],
                ];

                win.data2_$this->id = [
                    [
                        [{'name': '" . (isset($batalhas['30']['0']['user1_username']) ? $batalhas['30']['0']['user1_username'] : "?????") . "', 
                        'id': '" . (isset($batalhas['30']['0']['user1_id']) ? $batalhas['30']['0']['user1_id'] : "0") . "_$this->id', 
                        'score': " . (isset($batalhas['30']['0']['user1_pontos']) ? $batalhas['30']['0']['user1_pontos'] : "0") . "}, 
                        {'name': '" . (isset($batalhas['30']['0']['user2_username']) ? $batalhas['30']['0']['user2_username'] : "?????") . "', 
                        'id': '" . (isset($batalhas['30']['0']['user2_id']) ? $batalhas['30']['0']['user2_id'] : "0") . "_$this->id', 
                        'score': " . (isset($batalhas['30']['0']['user2_pontos']) ? $batalhas['30']['0']['user2_pontos'] : "0") . "}]
                    ],
                    [";

        $nome_3lugar = "?????";
        $id_3lugar = "0";

        if (isset($batalhas['30']['0']['vencedor']) && !empty($batalhas['30']['0']['vencedor'])) {
            if ($batalhas['30']['0']['vencedor'] == $batalhas['30']['0']['user1_id']) {
                $nome_3lugar = $batalhas['30']['0']['user1_username'];
                $id_3lugar = $batalhas['30']['0']['user1_id'];
            } else {
                $nome_3lugar = $batalhas['30']['0']['user2_username'];
                $id_3lugar = $batalhas['30']['0']['user2_id'];
            }
        }
        $tabela .=
                "[{'name': '$nome_3lugar', 'id': '$id_3lugar" . "_$this->id', 'displaySeed': '3º' }]
                    ]
                ];

                $('#gracket_$this->id').gracket({src: win.data_$this->id, canvasLineColor : '#000', roundLabels: [";
        if ($n_rounds == 8 || $n_rounds == 4) {
            $tabela .= "'Oitavas de final";
            if ($this->round_atual >= ($n_rounds - 3)) {
                $tabela .= "<br/><small>" . $this->inicio_round($n_rounds - 3) . "</small>";
            }
            $tabela .="', ";
        }
        if ($n_rounds >= 7 || ($n_rounds >= 3 && $n_rounds <= 4)) {
            $tabela .= "'Quartas de final";
            if ($this->round_atual >= ($n_rounds - 2)) {
                $tabela .= "<br/><small>" . $this->inicio_round($n_rounds - 2) . "</small>";
            }
            $tabela .="', ";
        }

        $tabela .= "'Semifinais";
        if ($this->round_atual >= ($n_rounds - 1)) {
            $tabela .= "<br/><small>" . $this->inicio_round($n_rounds - 1) . "</small>";
        }
        $tabela .="', 'Final";
        if ($this->round_atual >= $n_rounds) {
            $tabela .= "<br/><small>" . $this->inicio_round($n_rounds) . "</small>";
        }
        $tabela .="', 'Campeões']" 
                . ($n_rounds == 8 || $n_rounds == 4 ? ", gracketClass : 'g_gracket2', roundClass : 'g_round2'" : "") . "});
                $('#gracket2_$this->id').gracket({src: win.data2_$this->id, canvasLineColor : '#000', roundLabels: ['Disputa pelo 3º lugar'], winnerClass : 'g_winner g_3rd_place'" 
                . ($n_rounds == 8 || $n_rounds == 4 ? ", gracketClass : 'g_gracket2', roundClass : 'g_round2'" : "") . "});
                $('#gracket2_$this->id')\n
                \t\t.css('left', $('#gracket_$this->id > div:last').prev().offset().left - 475)
                \t\t.css('bottom', ";
        if ($n_rounds == 8 || $n_rounds == 4) {
            $tabela .= "160";
        } else if ($n_rounds >= 7 || ($n_rounds >= 3 && $n_rounds <= 4)) {
            $tabela .= "120";
        } else {
            $tabela .= "35";
        }

        $tabela .=
                ");

            })(window, document, jQuery);
        </script>";

        return $tabela;
    }

    public function ranking($limit = "") {
        $jogadores = array();

        $sql = "SELECT lb.user_id1 as user1_id, lb.user_id2 as user2_id, user1.username as user1_username, "
                . "user2.username as user2_username, lb.vencedor, lb.round, "
                . "(SELECT (COALESCE((SELECT SUM(lb1.pontos_user1) FROM league_battle lb1 WHERE "
                . "lb1.league_id='$this->id' AND lb1.user_id1 = user1_id),0)"
                . " + COALESCE((SELECT SUM(lb2.pontos_user2) FROM league_battle lb2 WHERE "
                . "lb2.league_id='$this->id' AND lb2.user_id2 = user1_id),0))) AS user1_pontos,"
                . "(SELECT (COALESCE((SELECT SUM(lb1.pontos_user1) FROM league_battle lb1 WHERE "
                . "lb1.league_id='$this->id' AND lb1.user_id1 = user2_id),0)"
                . " + COALESCE((SELECT SUM(lb2.pontos_user2) FROM league_battle lb2 WHERE "
                . "lb2.league_id='$this->id' AND lb2.user_id2 = user2_id),0))) AS user2_pontos "
                . "FROM league_battle lb, gebruikers user1, gebruikers user2 WHERE "
                . "lb.user_id1 = user1.user_id AND lb.user_id2 = user2.user_id AND "
                . "lb.league_id = '$this->id' AND lb.round>='" . $this->cont_rounds() . "' ORDER BY lb.round";

        $result = DB::exQuery($sql);


        while ($batalha = $result->fetch_assoc()) {
            if ($batalha['vencedor'] == $batalha['user1_id']) {
                $jogadores[] = array(
                    'id' => $batalha['user1_id'],
                    'username' => $batalha['user1_username'],
                    'pontos' => $batalha['user1_pontos']
                );
                $jogadores[] = array(
                    'id' => $batalha['user2_id'],
                    'username' => $batalha['user2_username'],
                    'pontos' => $batalha['user2_pontos']
                );
            } else {
                $jogadores[] = array(
                    'id' => $batalha['user2_id'],
                    'username' => $batalha['user2_username'],
                    'pontos' => $batalha['user2_pontos']
                );
                $jogadores[] = array(
                    'id' => $batalha['user1_id'],
                    'username' => $batalha['user1_username'],
                    'pontos' => $batalha['user1_pontos']
                );
            }
        }

        $sql = "SELECT lp.user_id as id, user.username, (SELECT (COALESCE((SELECT SUM(lb1.pontos_user1) "
                . "FROM league_battle lb1 WHERE lb1.league_id='$this->id' AND lb1.user_id1 = lp.user_id),0) + "
                . "COALESCE((SELECT SUM(lb2.pontos_user2) FROM league_battle lb2 WHERE lb2.league_id='$this->id' AND "
                . "lb2.user_id2 = lp.user_id),0))) AS pontos FROM league_participant lp, gebruikers user, "
                . "(SELECT gb.user_id, (gb.gewonnen + gb.verloren) AS victories, SUM(ps.`level`) AS lv_sum, "
                . "AVG(ps.`level`) AS lv_average, (((gb.gewonnen + gb.verloren)/10) + (AVG(ps.`level`) * 10) + "
                . "(SUM(ps.`level`) * 2) + (gb.badges * 5)) AS total_points FROM gebruikers AS gb JOIN "
                . "pokemon_speler AS ps ON gb.user_id = ps.user_id WHERE gb.banned='N' GROUP BY gb.user_id) "
                . "ranking WHERE lp.league_id = '$this->id' AND lp.user_id = user.user_id AND lp.user_id = ranking.user_id AND "
                . "lp.user_id NOT IN("
                . $jogadores['0']['id'] . ", " . $jogadores['1']['id'] . ", " . $jogadores['2']['id'] . ", " . $jogadores['3']['id']
                . ") ORDER BY pontos DESC, ranking.total_points DESC $limit";
        $result = DB::exQuery($sql);
        while ($jogador = $result->fetch_assoc()) {
            $jogadores[] = $jogador;
        }

        return $jogadores;
    }

    public function entregar_premiacao() {

        $rank = $this->ranking();
        $result = DB::exQuery("SELECT * FROM league_award WHERE league_id = '$this->id' ORDER BY colocacao ASC");

        while ($premiacao = $result->fetch_assoc()) {
            $user_id = $rank[$premiacao['colocacao'] - 1]['id'];

            DB::exQuery("UPDATE gebruikers SET "
                    . "silver=silver+'" . $premiacao['silvers'] . "' "
                    . "WHERE user_id='$user_id'");

	    $result = DB::exQuery("SELECT acc_id FROM gebruikers WHERE user_id = '$user_id'");
        $user = $result->fetch_assoc();
        
			DB::exQuery("UPDATE rekeningen SET "
                    . "gold=gold+'" . $premiacao['golds'] . "' "
                    . "WHERE acc_id='$user[acc_id]'");
					
            $timexx = time();
            $endPremium1 = (86400 * $premiacao['vip']);
            $endPremium2 = time() + (86400 * $premiacao['vip']);
			
			DB::exQuery("UPDATE gebruikers SET "
                    . "premiumaccount=premiumaccount+'" . $endPremium1 . "' "
                    . "WHERE user_id='$user_id' AND premiumaccount > '$timexx' limit 1");
					
			DB::exQuery("UPDATE gebruikers SET "
                    . "premiumaccount='" . $endPremium2 . "' "
                    . "WHERE user_id='$user_id' AND premiumaccount < '$timexx' limit 1");
	
            if ($premiacao['pokemon_id']) {
                //Load pokemon basis
                $new_computer_sql = DB::exQuery("SELECT * FROM `pokemon_wild` WHERE `wild_id`='" . $premiacao['pokemon_id'] . "'")->fetch_assoc();

                //Alle gegevens vast stellen voordat alles begint.
                $new_computer['id'] = $new_computer_sql['wild_id'];
                $new_computer['pokemon'] = $new_computer_sql['naam'];
                $new_computer['aanval1'] = $new_computer_sql['aanval_1'];
                $new_computer['aanval2'] = $new_computer_sql['aanval_2'];
                $new_computer['aanval3'] = $new_computer_sql['aanval_3'];
                $new_computer['aanval4'] = $new_computer_sql['aanval_4'];
                $klaar = false;
                $loop = 0;
                $lastid = 0;

                //Loop beginnen
                do {
                    $teller = 0;
                    $loop++;
                    //Levelen gegevens laden van de pokemon
                    $levelenquery = DB::exQuery("SELECT * FROM `levelen` WHERE `wild_id`='" . $new_computer['id'] . "' AND `level`<='" . $premiacao['lv_pokemon'] . "' ORDER BY `id` ASC ");

                    //Voor elke pokemon alle gegeven behandelen
                    while ($groei = $levelenquery->fetch_assoc()) {

                        //Teller met 1 verhogen
                        $teller++;
                        //Is het nog binnen de level?
                        if ($premiacao['lv_pokemon'] >= $groei['level']) {
                            //Is het een aanval?
                            if ($groei['wat'] == 'att') {
                                //Is er een plek vrij
                                if (empty($new_computer['aanval1']))
                                    $new_computer['aanval1'] = $groei['aanval'];
                                else if (empty($new_computer['aanval2']))
                                    $new_computer['aanval2'] = $groei['aanval'];
                                else if (empty($new_computer['aanval3']))
                                    $new_computer['aanval3'] = $groei['aanval'];
                                else if (empty($new_computer['aanval4']))
                                    $new_computer['aanval4'] = $groei['aanval'];
                                //Er is geen ruimte, dan willekeurig een aanval kiezen en plaatsen
                                else {
                                    if (($new_computer['aanval1'] != $groei['aanval']) AND ( $new_computer['aanval2'] != $groei['aanval']) AND ( $new_computer['aanval3'] != $groei['aanval']) AND ( $new_computer['aanval4'] != $groei['aanval'])) {
                                        $nummer = rand(1, 4);
                                        if ($nummer == 1)
                                            $new_computer['aanval1'] = $groei['aanval'];
                                        else if ($nummer == 2)
                                            $new_computer['aanval2'] = $groei['aanval'];
                                        else if ($nummer == 3)
                                            $new_computer['aanval3'] = $groei['aanval'];
                                        else if ($nummer == 4)
                                            $new_computer['aanval4'] = $groei['aanval'];
                                    }
                                }
                            }

                            //Evolueert de pokemon
                            else if ($groei['wat'] == "evo") {
                                $evo = DB::exQuery("SELECT * FROM `pokemon_wild` WHERE `wild_id`='" . $groei['nieuw_id'] . "'")->fetch_assoc();
                                $new_computer['id'] = $groei['nieuw_id'];
                                $new_computer['pokemon'] = $groei['naam'];
                                $loop = 0;
                                break;
                            }
                        }

                        //Er gebeurd niks dan stoppen
                        else {
                            $klaar = true;
                            break;
                        }
                    }
                    if ($teller == 0) {
                        break;
                        $klaar = true;
                    }
                    if ($loop == 2) {
                        break;
                        $klaar = true;
                    }
                } while (!$klaar);

                //Karakter kiezen 
                $karakter = DB::exQuery("SELECT * FROM `karakters` ORDER BY rand() limit 1")->fetch_assoc();

                //Expnodig opzoeken en opslaan
                $level = $premiacao['lv_pokemon'] + 1;
                $experience = DB::exQuery("SELECT `punten` FROM `experience` WHERE `soort`='"
                                . $new_computer_sql['groei'] . "' AND `level`='" . $level . "'")->fetch_assoc();

                //Iv willekeurig getal tussen 2,31
                $attack_iv = rand(2, 31);
                $defence_iv = rand(2, 31);
                $speed_iv = rand(2, 31);
                $spcattack_iv = rand(2, 31);
                $spcdefence_iv = rand(2, 31);
                $hp_iv = rand(2, 31);

                //Stats berekenen
                $new_computer['attackstat'] = round(((($new_computer_sql['attack_base'] * 2 + $attack_iv) * $premiacao['lv_pokemon'] / 100) + 5) * 1);
                $new_computer['defencestat'] = round(((($new_computer_sql['defence_base'] * 2 + $defence_iv) * $premiacao['lv_pokemon'] / 100) + 5) * 1);
                $new_computer['speedstat'] = round(((($new_computer_sql['speed_base'] * 2 + $speed_iv) * $premiacao['lv_pokemon'] / 100) + 5) * 1);
                $new_computer['spcattackstat'] = round(((($new_computer_sql['spc.attack_base'] * 2 + $spcattack_iv) * $premiacao['lv_pokemon'] / 100) + 5) * 1);
                $new_computer['spcdefencestat'] = round(((($new_computer_sql['spc.defence_base'] * 2 + $spcdefence_iv) * $premiacao['lv_pokemon'] / 100) + 5) * 1);
                $new_computer['hpstat'] = round(((($new_computer_sql['hp_base'] * 2 + $hp_iv) * $premiacao['lv_pokemon'] / 100) + $premiacao['lv_pokemon']) + 10);

                //Baby pokemon timer starten
                $tijd = date('Y-m-d H:i:s');

                //Ability
                $ability = explode(',', $new_computer_sql['ability']);
                $rand_ab = rand(0, (sizeof($ability) - 1));
                $ability = $ability[$rand_ab];

                //Save Computer
                DB::exQuery("INSERT INTO `pokemon_speler` (`wild_id`, `user_id`, `opzak`, `opzak_nummer`, `karakter`, "
                        . "`level`, `levenmax`, `leven`, `totalexp`, `expnodig`, `attack`, `defence`, `speed`, "
                        . "`spc.attack`, `spc.defence`, `attack_iv`, `defence_iv`, `speed_iv`, `spc.attack_iv`, "
                        . "`spc.defence_iv`, `hp_iv`, `attack_ev`, `defence_ev`, `speed_ev`, `spc.attack_ev`, "
                        . "`spc.defence_ev`, `hp_ev`, `aanval_1`, `aanval_2`, `aanval_3`, `aanval_4`, `effect`, "
                        . "`ei`, `ei_tijd`, `ability`, `capture_date`) VALUES ('" . $new_computer['id'] . "', '$user_id', 'nee', '', "
                        . "'" . $karakter['karakter_naam'] . "', '" . $premiacao['lv_pokemon'] . "', '"
                        . $new_computer['hpstat'] . "', '" . $new_computer['hpstat'] . "', '" . $experience['punten']
                        . "', '" . $experience['punten'] . "', '" . $new_computer['attackstat'] . "', '"
                        . $new_computer['defencestat'] . "', '" . $new_computer['speedstat'] . "', '"
                        . $new_computer['spcattackstat'] . "', '" . $new_computer['spcdefencestat'] . "', "
                        . "'" . $attack_iv . "', '" . $defence_iv . "', '" . $speed_iv . "', '" . $spcattack_iv . "', "
                        . "'" . $spcdefence_iv . "', '" . $hp_iv . "', '" . $new_computer_sql['effort_attack'] . "', "
                        . "'" . $new_computer_sql['effort_defence'] . "', '" . $new_computer_sql['effort_spc.attack']
                        . "', '" . $new_computer_sql['effort_spc.defence'] . "', '" . $new_computer_sql['effort_speed']
                        . "', '" . $new_computer_sql['effort_hp'] . "', '" . $new_computer['aanval1'] . "', '"
                        . $new_computer['aanval2'] . "', '" . $new_computer['aanval3'] . "', '" . $new_computer['aanval4']
                        . "', '" . $new_computer_sql['effect'] . "', '1', '" . $tijd . "', '" . $ability . "', '" . $date . "')");
            }
        }

        $this->premiacao_entregue = 1;
        $this->update();
    }

    public function erro_duelo($user_id, $oponent = false) {
        $erros = array();

        $user = DB::exQuery("SELECT wereld, online FROM gebruikers WHERE user_id='$user_id'")->fetch_assoc();

        if ($user['online'] + 900 < time()) {
            $erros[] = ($oponent ? "Seu oponente nao estava online!" : "Você não está online!");
        }

        if ($user['wereld'] != $this->regiao) {
            $erros[] = ($oponent ? "Seu oponente nao estava na região de $this->regiao!" : "Você precisa estar na região de $this->regiao!");
        }

        $result = DB::exQuery("SELECT ps.id, ps.wild_id, ps.shiny, ps.level, pw.naam, pw.lendario FROM pokemon_speler ps, pokemon_wild pw WHERE ps.user_id='$user_id' AND ps.opzak='ja' AND ps.wild_id = pw.wild_id");

        if ($result->num_rows < 6) {
            $erros[] = ($oponent ? "Seu oponente não possuia 6 pokémon no bolso!" : "Você não tem 6 pokémon no bolso!");
        }

        if (DB::exQuery("SELECT * FROM `pokemon_speler` WHERE `leven`>'0' AND `user_id`='$user_id' AND opzak='ja'")->num_rows == 0) {
            $erros[] = ($oponent ? "Todos os pokémon do seu oponente estavam desmaiados!" : "Todos os seus pokémon estão desmaiados!");
        }

        if ($this->lv_max_pokemon < 100 || $this->n_shinys < 6 || $this->n_lendas < 6 || $this->n_megas < 6) {
            $lv_max = false;
            $n_shinys = 0;
            $n_lendas = 0;
            $n_megas = 0;

            while ($poke = $result->fetch_assoc()) {
                if ($poke['level'] > $this->lv_max_pokemon) {
                    $lv_max = true;
                }
                if ($poke['shiny']) {
                    $n_shinys++;
                }
                if ($poke['lendario']) {
                    $n_lendas++;
                }
                if (strpos($poke['naam'], "Mega ") !== false) {
                    $n_megas++;
                }
            }

            if ($lv_max) {
                $erros[] = ($oponent ? "Seu oponete tinha algum pokémon com o lv maior que $this->lv_max_pokemon no bolso!" : "Algum de seus pokémon no bolso tem o lv maior que $this->lv_max_pokemon!");
            }

            if ($n_shinys > $this->n_shinys) {
                $erros[] = ($oponent ? "Seu oponete tinha mais que $this->n_shinys pokémon shiny no bolso!" : "Você tem mais que $this->n_shinys pokémon shiny no bolso!!");
            }

            if ($n_lendas > $this->n_lendas) {
                $erros[] = ($oponent ? "Seu oponete tinha mais que $this->n_lendas pokémon lendário no bolso!" : "Você tem mais que $this->n_lendas pokémon lendário no bolso!!");
            }

            if ($n_megas > $this->n_megas) {
                $erros[] = ($oponent ? "Seu oponete tinha mais que $this->n_megas pokémon mega evoluido no bolso!" : "Você tem mais que $this->n_megas pokémon mega evoluido no bolso!!");
            }
        }

        if (count($erros)) {
            return $erros;
        }
        return false;
    }

    public function criar_duelos() {

        set_time_limit(0);

        $result = DB::exQuery("SELECT lb.id,us1.username as username1, us2.username as username2, "
                . "us1.character as character1, us2.character as character2 FROM league_battle lb, "
                . "gebruikers us1, gebruikers us2 WHERE lb.league_id='$this->id' AND lb.duel_id='0' AND "
                . "lb.termino IS NULL AND lb.user_id1 = us1.user_id AND lb.user_id2 = us2.user_id");

        while ($batalha = $result->fetch_assoc()) {
            $l_batlle = new League_battle();
            $l_batlle->select($batalha['id']);
            if ($erro_duelo = $this->erro_duelo($l_batlle->getUser_id1())) {
                $l_batlle->informarVencedor($l_batlle->getUser_id2(), implode(" | ", $erro_duelo));
            } else if ($erro_duelo = $this->erro_duelo($l_batlle->getUser_id2())) {
                $l_batlle->informarVencedor($l_batlle->getUser_id1(), implode(" | ", $erro_duelo));
            } else {
                $date = strtotime(date("Y-m-d H:i:s"));
                DB::exQuery("DELETE FROM duel WHERE uitdager='" . $batalha['username1'] . "' OR "
                        . "uitdager='" . $batalha['username2'] . "' OR tegenstander='" . $batalha['username1'] . "' "
                        . "OR tegenstander='" . $batalha['username2'] . "'");

                DB::exQuery("INSERT INTO duel (datum, uitdager, tegenstander, u_character, t_character, "
                        . "bedrag, status, laatste_beurt_tijd, laatste_beurt) "
                        . "VALUES ('$date', '" . $batalha['username1'] . "', '" . $batalha['username2'] . "', "
                        . "'" . $batalha['character1'] . "', '" . $batalha['character2'] . "', '0', 'accept', "//wait
                        . "'$date', '" . $batalha['username1'] . "')");

                $l_batlle->setDuel_id(DB::insertID());

                $this->start_duel($l_batlle->getDuel_id(), 'uitdager', $l_batlle->getUser_id1());
                //DB::exQuery("UPDATE duel SET status='accept', laatste_beurt_tijd=" . strtotime(date("Y-m-d H:i:s")) . " WHERE id='" . $l_batlle->getDuel_id() . "'");
                $this->start_duel($l_batlle->getDuel_id(), 'tegenstander', $l_batlle->getUser_id2());

                $this->start_attack($l_batlle->getDuel_id());

                $l_batlle->setUser_pokemon1((DB::exQuery("SELECT u_pokemonid FROM duel WHERE id='" . $l_batlle->getDuel_id() . "'")->fetch_array()['0']));
                $l_batlle->setUser_pokemon2((DB::exQuery("SELECT t_pokemonid FROM duel WHERE id='" . $l_batlle->getDuel_id() . "'")->fetch_array()['0']));
            }
            $l_batlle->update();
        }
    }

    public function start_duel($duel_id, $wat, $user_id) {
        DB::exQuery("DELETE FROM `pokemon_speler_gevecht` WHERE `user_id`='$user_id'");
        //Update Player as Duel
        DB::exQuery("UPDATE `gebruikers` SET `pagina`='duel' WHERE `user_id`='$user_id'");

        $count = 0;
        //Spelers van de pokemon laden die hij op zak heeft
        $pokemonopzaksql = DB::exQuery("SELECT * FROM pokemon_speler WHERE user_id='$user_id' AND `opzak`='ja' ORDER BY opzak_nummer ASC");
        //Nieuwe stats berekenen aan de hand van karakter, en opslaan
        while ($pokemonopzak = $pokemonopzaksql->fetch_assoc()) {
            //Alle gegevens opslaan, incl nieuwe stats
            DB::exQuery("INSERT INTO `pokemon_speler_gevecht` (`id`, `user_id`, `aanval_log_id`, `duel_id`, `levenmax`, `leven`, `exp`, `totalexp`, `effect`, `hoelang`) 
      VALUES ('" . $pokemonopzak['id'] . "', '$user_id', '-1', '" . $duel_id . "', '" . $pokemonopzak['levenmax'] . "', '" . $pokemonopzak['leven'] . "', '" . $pokemonopzak['exp'] . "', '" . $pokemonopzak['totalexp'] . "', '" . $pokemonopzak['effect'] . "', '" . $pokemonopzak['hoelang'] . "')");
            if (($count == 0) AND ( $wat == 'tegenstander') AND ( $pokemonopzak['leven'] > 0) AND ( $pokemonopzak['ei'] == 0)) {
                $count++;
                DB::exQuery("UPDATE `duel` SET `t_pokemonid`='" . $pokemonopzak['id'] . "', `t_used_id`='," . $pokemonopzak['id'] . ",' WHERE `id`='" . $duel_id . "'");
            }
        }
    }

    public function start_attack($duel_id) {

        $duel_sql = DB::exQuery("SElECT `id`, `datum`, `uitdager`, `tegenstander`, `t_pokemonid`, `status` FROM `duel` WHERE `id`='$duel_id'");
        $duel = $duel_sql->fetch_assoc();
        //Check who can start
        $uitdager = DB::exQuery("SELECT pw.naam, pw.wild_id, psg.user_id, ps.id, ps.speed FROM "
                        . "pokemon_speler AS ps INNER JOIN pokemon_speler_gevecht AS psg ON psg.id = ps.id INNER JOIN "
                        . "pokemon_wild AS pw On ps.wild_id = pw.wild_id WHERE psg.user_id=(SELECT `user_id` FROM `gebruikers` "
                        . "WHERE `username` = '" . $duel['uitdager'] . "') AND psg.leven>'0' AND ps.ei='0' "
                        . "ORDER BY ps.opzak_nummer ASC LIMIT 1")->fetch_assoc();
        $tegenstander = DB::exQuery("SELECT pw.naam, pw.wild_id, psg.user_id, ps.id, ps.speed "
                        . "FROM pokemon_speler AS ps INNER JOIN pokemon_speler_gevecht AS psg ON psg.id = ps.id INNER JOIN "
                        . "pokemon_wild AS pw On ps.wild_id = pw.wild_id WHERE psg.user_id=(SELECT `user_id` FROM `gebruikers` "
                        . "WHERE `username` = '" . $duel['tegenstander'] . "') AND psg.leven>'0' AND ps.ei='0' "
                        . "ORDER BY ps.opzak_nummer ASC LIMIT 1")->fetch_assoc();
        //Check who is the fastest
        if ($uitdager['speed'] >= $tegenstander['speed']) {
            $duel_info['laatste_beurt'] = $duel['uitdager'] . "_begin";
            $duel_info['volgende_beurt'] = $duel['uitdager'];
        } else {
            $duel_info['laatste_beurt'] = $duel['tegenstander'] . "_begin";
            $duel_info['volgende_beurt'] = $duel['tegenstander'];
        }
        //Remember id's
        $duel_info['u_pokemonid'] = $uitdager['id'];
        //Save 
        DB::exQuery("UPDATE `duel` SET `u_pokemonid`='" . $duel_info['u_pokemonid'] . "', `u_used_id`=',"
                . $duel_info['u_pokemonid'] . ",', `laatste_beurt`='" . $duel_info['laatste_beurt']
                . "', `volgende_beurt`='" . $duel_info['volgende_beurt'] . "' WHERE `id`='" . $duel['id'] . "'");
        //Update Both pokedexes
        DB::exQuery("UPDATE gebruikers SET `pok_gezien`=concat(pok_gezien,'," . $uitdager['wild_id'] . "') WHERE user_id='" . $tegenstander['user_id'] . "'");
        DB::exQuery("UPDATE gebruikers SET `pok_gezien`=concat(pok_gezien,'," . $tegenstander['wild_id'] . "') WHERE user_id='" . $uitdager['user_id'] . "'");
    }

    public function inscrito($user_id) {
        return DB::exQuery("SELECT * FROM `league_participant` WHERE user_id='$user_id' AND league_id='$this->id'")->num_rows;
    }

    public function rezetarLiga() {
        $this->participantes = $this->total_participantes;
        $this->round_atual = 0;
        $this->update();
        DB::exQuery("DELETE FROM league_battle WHERE league_id='$this->id'");
        DB::exQuery("UPDATE `league_participant` SET `ativo`= 1 WHERE league_id='$this->id'");
    }

    public function inicio_round($round, $datetime = false) {

        $result = DB::exQuery("SELECT `inicio` FROM `league_battle` WHERE `league_id` = '$this->id' AND `round` = '$round' LIMIT 1");

        if ($datetime) {
            return $result->fetch_array()['0'];
        }

        $inicio = strtotime($result->fetch_array()['0']);

        $hora_inicio = date("H:i:s", $inicio);

        return $hora_inicio;
    }

    public function finalizada() {
        $result = DB::exQuery("SELECT `vencedor` FROM `league_battle` WHERE `league_id`='$this->id' AND `round`='" . $this->cont_rounds() . "'");
        $result2 = DB::exQuery("SELECT `vencedor` FROM `league_battle` WHERE `league_id`='$this->id' AND `round`='30'");
        if ($result->num_rows && $result->fetch_array()['0'] && $result2->num_rows && $result2->fetch_array()['0']) {
            return true;
        }
        return false;
    }

    public function batalhas_criadas() {
        $result = DB::exQuery("SELECT COUNT(id) FROM league_battle WHERE `league_id`='$this->id' AND `round`='$this->round_atual' AND `duel_id`='0' AND `termino` IS NULL");
        if ($result->fetch_array()['0']) {
            return true;
        }
        return false;
    }

    /*
     * Contas as ligas que ainda não foram finalizadas
     */

    public static function aberta($torneio = false) {
        $result = DB::exQuery("SELECT id FROM league WHERE `premiacao_entregue`='0' AND "
                . "total_participantes " . ($torneio ? "<=" : ">") . "16 AND "
                . "((NOW()" . League::$ajuste_tempo_string . ") BETWEEN "
                . "inicio_inscricoes AND (NOW()" . League::$ajuste_tempo_string . ")) "
                . "ORDER BY inicio LIMIT 1");
        if ($result->num_rows > 0) {
        $id = $result->fetch_assoc();
        return $id['id'];
        }
        return false;
    }

    public function insert() {
        DB::exQuery("insert into league(regiao, total_participantes, participantes, inicio_inscricoes, "
                . "fim_inscricoes, inicio, intervalo_fase, round_atual, n_lendas, n_shinys, n_megas, lv_max_pokemon,  "
                . "preco_silvers, preco_golds, mods, admins, donos, vip, premiacao_entregue, em_operacao) "
                . "values ('$this->regiao', '$this->total_participantes', '$this->participantes', '$this->inicio_inscricoes', "
                . "'$this->fim_inscricoes', '$this->inicio', '$this->intervalo_fase', '$this->round_atual', "
                . "'$this->n_lendas', '$this->n_shinys', '$this->n_megas', '$this->lv_max_pokemon', '$this->preco_silvers', "
                . "'$this->preco_golds', '$this->mods', '$this->admins', '$this->donos', '$this->vip', "
                . "'$this->premiacao_entregue', '$this->em_operacao');");

        $this->id = DB::insertID();
        return $this->id;
    }

    public function update() {
        return DB::exQuery("update league set regiao='$this->regiao', total_participantes='$this->total_participantes', "
                . "participantes='$this->participantes', inicio_inscricoes='$this->inicio_inscricoes', "
                . "fim_inscricoes='$this->fim_inscricoes', inicio='$this->inicio', intervalo_fase='$this->intervalo_fase', "
                . "round_atual='$this->round_atual', n_lendas='$this->n_lendas', "
                . "n_shinys='$this->n_shinys', n_megas='$this->n_megas', lv_max_pokemon='$this->lv_max_pokemon', preco_silvers='$this->preco_silvers', "
                . "preco_golds='$this->preco_golds', mods='$this->mods', admins='$this->admins', "
                . "donos='$this->donos', vip='$this->vip', premiacao_entregue='$this->premiacao_entregue', "
                . "em_operacao='$this->em_operacao' WHERE id = '$this->id'");
    }

    public function select($id) {
        $result = DB::exQuery("SELECT * FROM league WHERE id = '$id'");
        $league = $result->fetch_assoc();

        $this->id = $league['id'];
        $this->regiao = $league['regiao'];
        $this->total_participantes = $league['total_participantes'];
        $this->participantes = $league['participantes'];
        $this->inicio_inscricoes = $league['inicio_inscricoes'];
        $this->fim_inscricoes = $league['fim_inscricoes'];
        $this->inicio = $league['inicio'];
        $this->intervalo_fase = $league['intervalo_fase'];
        $this->round_atual = $league['round_atual'];
        $this->n_lendas = $league['n_lendas'];
        $this->n_shinys = $league['n_shinys'];
        $this->n_megas = $league['n_megas'];
        $this->lv_max_pokemon = $league['lv_max_pokemon'];
        $this->preco_silvers = $league['preco_silvers'];
        $this->preco_golds = $league['preco_golds'];
        $this->mods = $league['mods'];
        $this->admins = $league['admins'];
        $this->donos = $league['donos'];
        $this->vip = $league['vip'];
        $this->premiacao_entregue = $league['premiacao_entregue'];
        $this->em_operacao = $league['em_operacao'];

        return $this->id;
    }

    public function select_atual($regiao = NULL) {

        $result = DB::exQuery("SELECT * FROM league WHERE premiacao_entregue = '0' AND "
                . "(NOW()" . League::$ajuste_tempo_string . ") BETWEEN "
                . "fim_inscricoes AND (NOW()" . League::$ajuste_tempo_string . ")"
                . ($regiao ? " AND regiao='$regiao'" : ""));
        $league = $result->fetch_assoc();

        $this->id = $league['id'];
        $this->regiao = $league['regiao'];
        $this->total_participantes = $league['total_participantes'];
        $this->participantes = $league['participantes'];
        $this->inicio_inscricoes = $league['inicio_inscricoes'];
        $this->fim_inscricoes = $league['fim_inscricoes'];
        $this->inicio = $league['inicio'];
        $this->intervalo_fase = $league['intervalo_fase'];
        $this->round_atual = $league['round_atual'];
        $this->n_lendas = $league['n_lendas'];
        $this->n_shinys = $league['n_shinys'];
        $this->n_megas = $league['n_megas'];
        $this->lv_max_pokemon = $league['lv_max_pokemon'];
        $this->preco_silvers = $league['preco_silvers'];
        $this->preco_golds = $league['preco_golds'];
        $this->mods = $league['mods'];
        $this->admins = $league['admins'];
        $this->donos = $league['donos'];
        $this->vip = $league['vip'];
        $this->premiacao_entregue = $league['premiacao_entregue'];
        $this->em_operacao = $league['em_operacao'];

        return $this->id;
    }

    public static function select_atuais($torneio = false, $regiao = null, $limit = null) {

        $ligas = array();

        $result = DB::exQuery("SELECT * FROM league WHERE "
                . "(NOW()" . League::$ajuste_tempo_string . ") BETWEEN "
                . "(NOW()" . League::$ajuste_tempo_string . ") AND "
                . "fim_inscricoes" . ($regiao ? " AND regiao='$regiao'" : "")
                . " AND total_participantes " . ($torneio ? "<=" : ">") . " 16"
                . " ORDER BY inicio"
                . ($limit ? " LIMIT $limit" : ""));
        while ($league = $result->fetch_assoc()) {

            $liga = new League();

            $liga->id = $league['id'];
            $liga->regiao = $league['regiao'];
            $liga->total_participantes = $league['total_participantes'];
            $liga->participantes = $league['participantes'];
            $liga->inicio_inscricoes = $league['inicio_inscricoes'];
            $liga->fim_inscricoes = $league['fim_inscricoes'];
            $liga->inicio = $league['inicio'];
            $liga->intervalo_fase = $league['intervalo_fase'];
            $liga->round_atual = $league['round_atual'];
            $liga->n_lendas = $league['n_lendas'];
            $liga->n_shinys = $league['n_shinys'];
            $liga->n_megas = $league['n_megas'];
            $liga->lv_max_pokemon = $league['lv_max_pokemon'];
            $liga->preco_silvers = $league['preco_silvers'];
            $liga->preco_golds = $league['preco_golds'];
            $liga->mods = $league['mods'];
            $liga->admins = $league['admins'];
            $liga->donos = $league['donos'];
            $liga->vip = $league['vip'];
            $liga->premiacao_entregue = $league['premiacao_entregue'];
            $liga->em_operacao = $league['em_operacao'];

            $ligas[] = $liga;
        }

        return $ligas;
    }

    public static function select_terminadas($torneio = false, $regiao = null, $limit = null) {

        $ligas = array();

        $result = DB::exQuery("SELECT * FROM league WHERE (NOW()" . League::$ajuste_tempo_string . ") BETWEEN fim_inscricoes AND "
                . "(NOW()" . League::$ajuste_tempo_string . ")" . ($regiao ? " AND regiao='$regiao'" : "")
                . " AND total_participantes " . ($torneio ? "<=" : ">") . " 16 ORDER BY inicio DESC "
                . ($limit ? " LIMIT $limit" : ""));
        while ($league = $result->fetch_assoc()) {

            $liga = new League();

            $liga->id = $league['id'];
            $liga->regiao = $league['regiao'];
            $liga->total_participantes = $league['total_participantes'];
            $liga->participantes = $league['participantes'];
            $liga->inicio_inscricoes = $league['inicio_inscricoes'];
            $liga->fim_inscricoes = $league['fim_inscricoes'];
            $liga->inicio = $league['inicio'];
            $liga->intervalo_fase = $league['intervalo_fase'];
            $liga->round_atual = $league['round_atual'];
            $liga->n_lendas = $league['n_lendas'];
            $liga->n_shinys = $league['n_shinys'];
            $liga->n_megas = $league['n_megas'];
            $liga->lv_max_pokemon = $league['lv_max_pokemon'];
            $liga->preco_silvers = $league['preco_silvers'];
            $liga->preco_golds = $league['preco_golds'];
            $liga->mods = $league['mods'];
            $liga->admins = $league['admins'];
            $liga->donos = $league['donos'];
            $liga->vip = $league['vip'];
            $liga->premiacao_entregue = $league['premiacao_entregue'];
            $liga->em_operacao = $league['em_operacao'];

            $ligas[] = $liga;
        }

        return $ligas;
    }

}
