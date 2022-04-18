<?php
include("app/includes/resources/security.php");

echo addNPCBox(11, 'Cidade', 'Bem, aqui é a <b>Cidade</b>... Você poderá fazer várias atividades na cidade, seja como Viajar, comprar Poké balls, Enfrentar Ginásios e muitas
outras coisas... <br>Se cuida, treinador!');
?>

<div class="blue">Coloque o cursor do mouse sobre os edifícios para saber sobre o lugar.</div>
<div class="box-content container-tag" style="padding: 10px">
  <div class="tag">
    <a href="./attack/gyms" title="Enfrente os Líderes de Ginásios para conseguir as Insígnias e desbloquear novas Regiões!" id="a" class="noanimate"></a>
    <a href="./specialists" title="Mude o nome, humor de seus Pokémons, além de poder transformá-los em Shiny com nossos Especialistas Pokémon!" id="b" class="noanimate"></a>
    <a href="./travel" title="Que tal fazer as mochilas e ir conhecer novos Treinadores e Pokémons em outras Regiões?" id="c" class="noanimate"></a>
    <a href="./bank" title="Faça transferências de Silvers ou Golds com outros Treinadores." id="d" class="noanimate"></a>
    <a href="./daycare" title="Coloque seu Pokémon aqui no Jardim de Infância para subir de níveis e ter a possibilidade de ganhar um EGG!" id="e" class="noanimate"></a>
    <a href="./transferlist" title="Compre e venda Pokémons pelo melhor preço com nossos métodos de venda aqui no Mercado de Pokémons" id="f" class="noanimate"></a>
    <a href="./market" title="Poké Bolls, Itens, Pedras, Pokémons e outros itens você encontra aqui no PokéMart!" id="g" class="noanimate"></a>
    <a href="./casino" title="Aposte Tickets, jogue Minijogos e ganhe recompensas!" id="h" class="noanimate"></a>
    <a href="./pokemoncenter" title="Seus pokémons estão cansados? Você está no lugar certo, a Enfermeira Joy vai te ajudar aqui no Centro Pokémon." id="i" class="noanimate"></a>
    <a href="./traders" title="Troque seu Pokémon por outro com aqui nos Comerciantes!" id="j" class="noanimate"></a>
    <a href="./moves" title="Ensine ou Relembre os Ataques de seus Pokémons aqui!" id="k" class="noanimate"></a>
    <a href="./fountain" title="Faça seus Pokémon passarem pela Fonte da Juventude para os Rejuvenescer!" id="l" class="noanimate"></a>
  </div>

  <img src="<?=$static_url?>/images/town/town.png" width="610" height="610"/>
</div>

<style>
  .container-tag {
    position: relative;
  }
  .tag {
    float: left;
    position: absolute;
    left: 0px;
    top: 0px;
  }

  #a {
    width: 98px;
    height: 117px;
    position: absolute;
    left: 218px;
    top: 40px;
  }

  #b {
    width: 44px;
    height: 95px;
    position: absolute;
    left: 343px;
    top: 102px;
  }
  
  #c {
    width: 94px;
    height: 140px;
    position: absolute;
    left: 439px;
    top: 22px;
  }

  #d {
    width: 53px;
    height: 91px;
    position: absolute;
    left: 626px;
    top: 108px;
  }

  #e {
    width: 51px;
    height: 78px;
    position: absolute;
    left: 209px;
    top: 430px;
  }

  #f {
    width: 103px;
    height: 184px;
    position: absolute;
    left: 275px;
    top: 229px;
  }

  #g {
    width: 45px;
    height: 47px;
    position: absolute;
    left: 625px;
    top: 256px;
  }

  #h {
    width: 93px;
    height: 75px;
    position: absolute;
    left: 460px;
    top: 320px;
  }

  #i {
    width: 54px;
    height: 57px;
    position: absolute;
    left: 626px;
    top: 338px;
  }

  #j {
    width: 49px;
    height: 80px;
    position: absolute;
    left: 493px;
    top: 428px;
  }
  
  #k {
    width: 40px;
    height: 91px;
    position: absolute;
    left: 659px;
    top: 465px;
  }

  #l {
    width: 45px;
    height: 47px;
    position: absolute;
    left: 492px;
    top: 248px;
  }
</style>