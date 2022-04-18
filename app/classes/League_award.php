<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of League_award
 *
 * @author Bruno Agenor
 */
class League_award {

    private $id;
    private $league_id;
    private $colocacao = 1;
    private $golds = 0;
    private $silvers = 0;
    private $vip = 0;
    private $pokemon_id = 0;
    private $lv_pokemon = 0;
    private $item_id = 0;
    private $qnt_item = 0;

    public function getId() {
        return $this->id;
    }

    public function getLeague_id() {
        return $this->league_id;
    }

    public function getColocacao() {
        return $this->colocacao;
    }

    public function getGolds() {
        return $this->golds;
    }

    public function getSilvers() {
        return $this->silvers;
    }

    public function getVip() {
        return $this->vip;
    }

    public function getPokemon_id() {
        return $this->pokemon_id;
    }

    public function getLv_pokemon() {
        return $this->lv_pokemon;
    }

    public function getItem_id() {
        return $this->item_id;
    }

    public function getQnt_item() {
        return $this->qnt_item;
    }

    public function setId($id) {
        $this->id = $id;
    }

    public function setLeague_id($league_id) {
        $this->league_id = $league_id;
    }

    public function setColocacao($colocacao) {
        $this->colocacao = $colocacao;
    }

    public function setGolds($golds) {
        $this->golds = $golds;
    }

    public function setSilvers($silvers) {
        $this->silvers = $silvers;
    }

    public function setVip($vip) {
        $this->vip = $vip;
    }

    public function setPokemon_id($pokemon_id) {
        $this->pokemon_id = $pokemon_id;
    }

    public function setLv_pokemon($lv_pokemon) {
        $this->lv_pokemon = $lv_pokemon;
    }

    public function setItem_id($item_id) {
        $this->item_id = $item_id;
    }

    public function setQnt_item($qnt_item) {
        $this->qnt_item = $qnt_item;
    }

    
    public function insert() {
        DB::exQuery("insert into league_award(league_id, colocacao, golds, silvers, vip, pokemon_id, lv_pokemon, item_id, qnt_item) values "
                . "('$this->league_id', '$this->colocacao', '$this->golds', '$this->silvers', '$this->vip', "
                . "'$this->pokemon_id', '$this->lv_pokemon', '$this->item_id', '$this->qnt_item')");
        $this->id = DB::insertID();
        return $this->id;
    }

    public function update() {
        return DB::exQuery("update league_award set league_id='$this->league_id', colocacao='$this->colocacao', "
                . "golds='$this->golds', silvers='$this->silvers', vip='$this->vip', pokemon_id='$this->pokemon_id', "
                . "lv_pokemon='$this->lv_pokemon', item_id='$this->item_id', qnt_item='$this->qnt_item' where id = '$this->id'");
    }

    public function select($id) {

        $result = DB::exQuery("select * from league_award where id = '$id'");

        $award = $result->fetch_assoc();

        $this->id = $award['id'];
        $this->league_id = $award['league_id'];
        $this->colocacao = $award['colocacao'];
        $this->golds = $award['golds'];
        $this->silvers = $award['silvers'];
        $this->vip = $award['vip'];
        $this->pokemon_id = $award['pokemon_id'];
        $this->lv_pokemon = $award['lv_pokemon'];
        $this->item_id = $award['item_id'];
        $this->qnt_item = $award['qnt_item'];

        return $this->id;
    }

    public static function select_league($league_id) {

        $awards = array();

        $result = DB::exQuery("select * from league_award where league_id = '$league_id' ORDER BY colocacao");

        while ($award = $result->fetch_assoc()) {
            
            $league_award = new League_award();

            $league_award->id = $award['id'];
            $league_award->league_id = $award['league_id'];
            $league_award->colocacao = $award['colocacao'];
            $league_award->golds = $award['golds'];
            $league_award->silvers = $award['silvers'];
            $league_award->vip = $award['vip'];
            $league_award->pokemon_id = $award['pokemon_id'];
            $league_award->lv_pokemon = $award['lv_pokemon'];
            $league_award->item_id = $award['item_id'];
            $league_award->qnt_item = $award['qnt_item'];
            
            $awards[] = $league_award;
        }

        return $awards;
    }

}
