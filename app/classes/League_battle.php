<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of League_battle
 *
 * @author Bruno Agenor
 */
class League_battle {

    private $id;
    private $league_id;
    private $duel_id = 0;
    private $user_id1;
    private $user_id2;
    private $pontos_user1 = 0;
    private $pontos_user2 = 0;
    private $round;
    private $vencedor;
    private $inicio;
    private $termino = "'+null+'";
    private $n_pokemons;
    private $campo;
    private $user_pokemon1 = "";
    private $user_pokemon2 = "";

    public function getId() {
        return $this->id;
    }

    public function getLeague_id() {
        return $this->league_id;
    }

    public function getDuel_id() {
        return $this->duel_id;
    }

    public function getUser_id1() {
        return $this->user_id1;
    }

    public function getUser_id2() {
        return $this->user_id2;
    }

    public function getPontos_user1() {
        return $this->pontos_user1;
    }

    public function getPontos_user2() {
        return $this->pontos_user2;
    }

    public function getRound() {
        return $this->round;
    }

    public function getVencedor() {
        return $this->vencedor;
    }

    public function getInicio() {
        return $this->inicio;
    }

    public function getTermino() {
        return $this->termino;
    }

    public function getN_pokemons() {
        return $this->n_pokemons;
    }

    public function getCampo() {
        return $this->campo;
    }

    public function getUser_pokemon1() {
        return $this->user_pokemon1;
    }

    public function getUser_pokemon2() {
        return $this->user_pokemon2;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function setLeague_id($league_id) {
        $this->league_id = $league_id;
    }

    public function setDuel_id($duel_id) {
        $this->duel_id = $duel_id;
    }

    public function setUser_id1($user_id1) {
        $this->user_id1 = $user_id1;
    }

    public function setUser_id2($user_id2) {
        $this->user_id2 = $user_id2;
    }

    public function setPontos_user1($pontos_user1) {
        $this->pontos_user1 = $pontos_user1;
    }

    public function setPontos_user2($pontos_user2) {
        $this->pontos_user2 = $pontos_user2;
    }

    public function setRound($round) {
        $this->round = $round;
    }

    public function setVencedor($vencedor) {
        $this->vencedor = $vencedor;
    }

    public function setInicio($inicio) {
        $this->inicio = $inicio;
    }

    public function setTermino($termino) {
        $this->termino = $termino;
    }

    public function setN_pokemons($n_pokemons) {
        $this->n_pokemons = $n_pokemons;
    }

    public function setCampo($campo) {
        $this->campo = $campo;
    }

    public function setUser_pokemon1($user_pokemon1) {
        $this->user_pokemon1 = $user_pokemon1;
    }

    public function setUser_pokemon2($user_pokemon2) {
        $this->user_pokemon2 = $user_pokemon2;
    }

    public function informarVencedor($vencedor, $motivo = "") {
        $this->vencedor = $vencedor;
        $this->termino = date("Y-m-d H:i:s");
        $this->duel_id = "'+null+'";

        DB::exQuery("UPDATE league_participant SET ativo='0' WHERE user_id = '"
                . ($this->user_id1 == $vencedor ? $this->user_id2 : $this->user_id1)
                . "' AND league_id='$this->league_id'");

        $sql_vencedor = DB::exQuery("SELECT user_id, username FROM gebruikers WHERE user_id='$vencedor'")->fetch_assoc();
        $sql_perdedor = DB::exQuery("SELECT user_id, username FROM gebruikers WHERE user_id='"
                        . ($this->user_id1 == $vencedor ? $this->user_id2 : $this->user_id1) . "'")->fetch_assoc();
        //$log_liga = fopen("batalhas_liga.log", 'a');
        //fwrite($log_liga, ">[VENC] O Jogador " . $sql_vencedor['user_id'] . ":" . $sql_vencedor['username'] . " "
        //        . "venceu o jogador " . $sql_perdedor['user_id'] . ":" . $sql_perdedor['username'] . " "
        //        . "na liga $this->league_id no round $this->round às " . date("Y-m-d H:i:s") . " - $motivo\n");
        // fclose($log_liga);
    }

    public function criar_duelo() {
        $erro_duelo = false;
        $league = new League();
        $league->select($this->league_id);

        if ($erro_duelo = $league->erro_duelo($this->user_id1)) {
            $this->informarVencedor($this->user_id2, implode(" | ", $erro_duelo));
        } else if ($erro_duelo = $league->erro_duelo($this->user_id2)) {
            $this->informarVencedor($this->user_id1, implode(" | ", $erro_duelo));
        } else {
            $sql = "SELECT lb.id,us1.username as username1, us2.username as username2, "
                    . "us1.character as character1, us2.character as character2 FROM league_battle lb, "
                    . "gebruikers us1, gebruikers us2 WHERE lb.id='$this->id' AND "
                    . "lb.user_id1 = us1.user_id AND lb.user_id2 = us2.user_id";
            $batalha = DB::exQuery($sql)->fetch_assoc();

            DB::exQuery("INSERT INTO duel (datum, uitdager, tegenstander, u_character, t_character, "
                    . "bedrag, status, laatste_beurt_tijd, laatste_beurt) "
                    . "VALUES (NOW(), '" . $batalha['username1'] . "', '" . $batalha['username2'] . "', '" . $batalha['character1'] . "', '" . $batalha['character2'] . "', '0', 'wait', 'NOW()', '" . $batalha['username1'] . "')");

            $this->duel_id = DB::insertID();

            $league->start_duel($this->duel_id, 'uitdager', $this->user_id1);
            DB::exQuery("UPDATE duel SET status='accept', laatste_beurt_tijd=NOW() WHERE id='" . $this->duel_id . "'");
            $league->start_duel($this->duel_id, 'tegenstander', $this->user_id2);

            $league->start_attack($this->duel_id);

	    $pgxdl1 = DB::exQuery("SELECT u_pokemonid FROM duel WHERE id='" . $this->duel_id . "'")->fetch_assoc();
	    $pgxdl2 = DB::exQuery("SELECT t_pokemonid FROM duel WHERE id='" . $this->duel_id . "'")->fetch_assoc();

            $this->user_pokemon1 = $pgxdl1['u_pokemonid'];

            $this->user_pokemon2 = $pgxdl2['t_pokemonid'];

            $this->update();
        }

        return $erro_duelo;
    }

    public function insert() {
        DB::exQuery("insert into league_battle(league_id, duel_id, user_id1, user_id2, pontos_user1, pontos_user2, "
                . "round, vencedor, inicio, termino, n_pokemons, campo, user_pokemon1, user_pokemon2) values ('$this->league_id', '$this->duel_id', "
                . "'$this->user_id1', '$this->user_id2', '$this->pontos_user1', '$this->pontos_user2', '$this->round', "
                . "'$this->vencedor', '$this->inicio', '$this->termino', '$this->n_pokemons', '$this->campo', "
                . "'$this->user_pokemon1', '$this->user_pokemon2')");
        $this->id = DB::insertID();
        return $this->id;
    }

    public function update() {
        DB::exQuery("update league_battle set league_id='$this->league_id', duel_id='$this->duel_id', "
                . "user_id1='$this->user_id1', user_id2='$this->user_id2', pontos_user1='$this->pontos_user1', "
                . "pontos_user2='$this->pontos_user2', round='$this->round', vencedor='$this->vencedor', inicio='$this->inicio', "
                . "termino='$this->termino', n_pokemons='$this->n_pokemons', campo='$this->campo', "
                . "user_pokemon1='$this->user_pokemon1', user_pokemon2='$this->user_pokemon2' where id = '$this->id'");
    }

    public function select($id) {

        $result = DB::exQuery("select * from league_battle where id = '$id'");
        $league_battle = $result->fetch_assoc();

        $this->id = $league_battle['id'];
        $this->league_id = $league_battle['league_id'];
        $this->duel_id = $league_battle['duel_id'];
        $this->user_id1 = $league_battle['user_id1'];
        $this->user_id2 = $league_battle['user_id2'];
        $this->pontos_user1 = $league_battle['pontos_user1'];
        $this->pontos_user2 = $league_battle['pontos_user2'];
        $this->round = $league_battle['round'];
        $this->vencedor = $league_battle['vencedor'];
        $this->inicio = $league_battle['inicio'];
        $this->termino = $league_battle['termino'];
        $this->n_pokemons = $league_battle['n_pokemons'];
        $this->campo = $league_battle['campo'];
        $this->user_pokemon1 = $league_battle['user_pokemon1'];
        $this->user_pokemon2 = $league_battle['user_pokemon2'];
        return $this->id;
    }

    public static function select_duel($duel_id) {

        $result = DB::exQuery("select * from league_battle where duel_id = '$duel_id'");

        if ($result->num_rows != 1) {
            return null;
        }

        $league_battle = $result->fetch_assoc();

        $battle = new League_battle();

        $battle->id = $league_battle['id'];
        $battle->league_id = $league_battle['league_id'];
        $battle->duel_id = $league_battle['duel_id'];
        $battle->user_id1 = $league_battle['user_id1'];
        $battle->user_id2 = $league_battle['user_id2'];
        $battle->pontos_user1 = $league_battle['pontos_user1'];
        $battle->pontos_user2 = $league_battle['pontos_user2'];
        $battle->round = $league_battle['round'];
        $battle->vencedor = $league_battle['vencedor'];
        $battle->inicio = $league_battle['inicio'];
        $battle->termino = $league_battle['termino'];
        $battle->n_pokemons = $league_battle['n_pokemons'];
        $battle->campo = $league_battle['campo'];
        $battle->user_pokemon1 = $league_battle['user_pokemon1'];
        $battle->user_pokemon2 = $league_battle['user_pokemon2'];

        return $battle;
    }

    public static function select_nao_terminadas($round) {

        $batalhas = array();

        $result = DB::exQuery("select * from league_battle where round = '$round' AND (termino IS NULL OR termino='0000-00-00 00:00:00')");
        while ($league_battle = $result->fetch_assoc()) {

            $batalha = new League_battle;

            $batalha->id = $league_battle['id'];
            $batalha->league_id = $league_battle['league_id'];
            $batalha->duel_id = $league_battle['duel_id'];
            $batalha->user_id1 = $league_battle['user_id1'];
            $batalha->user_id2 = $league_battle['user_id2'];
            $batalha->pontos_user1 = $league_battle['pontos_user1'];
            $batalha->pontos_user2 = $league_battle['pontos_user2'];
            $batalha->round = $league_battle['round'];
            $batalha->vencedor = $league_battle['vencedor'];
            $batalha->inicio = $league_battle['inicio'];
            $batalha->termino = $league_battle['termino'];
            $batalha->n_pokemons = $league_battle['n_pokemons'];
            $batalha->campo = $league_battle['campo'];
            $batalha->user_pokemon1 = $league_battle['user_pokemon1'];
            $batalha->user_pokemon2 = $league_battle['user_pokemon2'];

            $batalhas[] = $batalha;
        }
        return $batalhas;
    }

}