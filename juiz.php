<?php
    #include dit script als je de pagina alleen kunt zien als je ingelogd bent.
    include('app/includes/resources/security.php');
     
    #Als je geen pokemon bij je hebt, terug naar index.
    if ($gebruiker['in_hand'] == 0) header('Location: index.php');

    $custo2 = 10000;
    
    //$gebruiker['premiumaccount'] > time() || $gebruiker['admin'] >= 3
if ($gebruiker['rank'] >= 4) {
    if (isset($_POST['juiz']) && isset($_POST['pokemonid'])) {
     
            $pokemoninfo = DB::exQuery("SELECT pokemon_wild.wild_id,pokemon_wild.naam,pokemon_speler.*, pokemon_wild.zeldzaamheid FROM pokemon_speler INNER JOIN pokemon_wild ON pokemon_speler.wild_id = pokemon_wild.wild_id WHERE pokemon_speler.id = '".$_POST['pokemonid']."'")->fetch_assoc();
            $img = pokemonei($pokemoninfo, $txt);
            #Is er geen pokemon gekozen?
            if (empty($_POST['pokemonid'])) echo '<div class="red">Escolha um pokémon.</div>';
            else if ($pokemoninfo['ei'] == 1) echo '<div class="red">Este pokémon ainda é um ovo.</div>';
            else if ($pokemoninfo['user_id'] != $_SESSION['id']) echo '<div class="red">Esse pokémon não é seu</div>';
            else if ($pokemoninfo['opzak'] != 'ja') echo '<div class="red">Esse pokémon não está no seu time.</div>';
            else if ($gebruiker['silver']  < $custo2) echo '<div class="red">Você não tem silvers suficientes.</div>';
            else{
            $analise = "";
     	    $sucesso = true;
    	      DB::exQuery("UPDATE `gebruikers` SET `silver`=`silver`-{$custo2} WHERE `user_id`={$_SESSION['id']} LIMIT 1");
     
     $somatoria = $pokemoninfo['attack_iv'] + $pokemoninfo['defence_iv'] + $pokemoninfo['speed_iv'] + $pokemoninfo['spc.attack_iv'] + $pokemoninfo['spc.defence_iv'] + $pokemoninfo['hp_iv'];

     if ($somatoria >= 0 and $somatoria <= 90) { $potencial = "<font color='red'>Decente..."; }
     else if ($somatoria >= 91 and $somatoria <= 120) { $potencial = "<font color='black'>Acima da Média."; }
     else if ($somatoria >= 121 and $somatoria <= 150) { $potencial = "<font color='green'>Relativamente Superior."; }
     else if ($somatoria >= 151 and $somatoria <= 186) { $potencial = "<font color='green'>Excelente!"; }
     
     $analise .= "Eu vejo... Eu vejo...<br>";
     $analise .= "Esse <b>".$pokemoninfo['naam']."</b> (". ucfirst($pokemoninfo['karakter']).") tem um potencial <b>".$potencial."</b></font><br>";
     
   
           
           
            if ($pokemoninfo['attack_iv'] >= $pokemoninfo['defence_iv'] AND $pokemoninfo['attack_iv'] >= $pokemoninfo['speed_iv'] AND $pokemoninfo['attack_iv'] >= $pokemoninfo['spc.attack_iv'] AND $pokemoninfo['attack_iv'] >= $pokemoninfo['spc.defence_iv'] AND $pokemoninfo['attack_iv'] >= $pokemoninfo['hp_iv']) {
             $maiorstats = "Ataque";
             $mr = $pokemoninfo['attack_iv'];
             $aa = 1;
             }
            else if ($pokemoninfo['defence_iv'] >= $pokemoninfo['attack_iv'] AND $pokemoninfo['defence_iv'] >= $pokemoninfo['speed_iv'] AND $pokemoninfo['defence_iv'] >= $pokemoninfo['spc.attack_iv'] AND $pokemoninfo['defence_iv'] >= $pokemoninfo['spc.defence_iv'] AND $pokemoninfo['defence_iv'] >= $pokemoninfo['hp_iv']) {
             $maiorstats = "Defesa";
             $mr = $pokemoninfo['defence_iv'];
             $bb = 1;
             }
              else if ($pokemoninfo['speed_iv'] >= $pokemoninfo['defence_iv'] AND $pokemoninfo['speed_iv'] >= $pokemoninfo['attack_iv'] AND $pokemoninfo['speed_iv'] >= $pokemoninfo['spc.attack_iv'] AND $pokemoninfo['speed_iv'] >= $pokemoninfo['spc.defence_iv'] AND $pokemoninfo['speed_iv'] >= $pokemoninfo['hp_iv']) {
               $maiorstats = "Speed";
               $mr = $pokemoninfo['speed_iv'];
               $cc = 1;
               }
               else if ($pokemoninfo['spc.attack_iv'] >= $pokemoninfo['defence_iv'] AND $pokemoninfo['spc.attack_iv'] >= $pokemoninfo['speed_iv'] AND $pokemoninfo['spc.attack_iv'] >= $pokemoninfo['attack_iv'] AND $pokemoninfo['spc.attack_iv'] >= $pokemoninfo['spc.defence_iv'] AND $pokemoninfo['spc.attack_iv'] >= $pokemoninfo['hp_iv']) {
                $maiorstats = "Spc. Ataque";
                $mr = $pokemoninfo['spc.attack_iv'];
                $dd = 1;
                }
                else if ($pokemoninfo['spc.defence_iv'] >= $pokemoninfo['defence_iv'] AND $pokemoninfo['spc.defence_iv'] >= $pokemoninfo['speed_iv'] AND $pokemoninfo['spc.defence_iv'] >= $pokemoninfo['spc.attack_iv'] AND $pokemoninfo['spc.defence_iv'] >= $pokemoninfo['attack_iv'] AND $pokemoninfo['spc.defence_iv'] >= $pokemoninfo['hp_iv']) {
                $maiorstats = "Spc. Defesa";
                $mr = $pokemoninfo['spc.defence_iv'];
                $ee = 1;
                            }
                else if ($pokemoninfo['hp_iv'] >= $pokemoninfo['attack_iv'] AND $pokemoninfo['hp_iv'] >= $pokemoninfo['defence_iv'] AND $pokemoninfo['hp_iv'] >= $pokemoninfo['speed_iv'] AND $pokemoninfo['hp_iv'] >= $pokemoninfo['spc.attack_iv'] AND $pokemoninfo['hp_iv'] >= $pokemoninfo['spc.defence_iv']) {
                $maiorstats = "HP";
                $mr = $pokemoninfo['hp_iv'];
                $ff = 1;
                }
            
                $analise .= "<br>Aliás, eu diria que seu maior potencial está em seu <font color='green'><b>".$maiorstats."</b></font>.<br>";
                
                if ($mr >= 1 AND $mr <= 15) $analise .= "> Esse Pokémon tem status <font color='red'><b>decentes</b></font>.";
                else if ($mr >= 16 AND $mr <= 25) $analise .= "> Esse Pokémon definitivamente tem <b>bons status</b>.";
                else if ($mr >= 26 AND $mr <= 30) $analise .= "> Esse Pokémon tem status <font color='green'><b>fantásticos</b></font>.";
                else if ($mr >= 31) $analise .= "> Status como esses... Simplesmente<font color='green'><b> não podem ser batidos</b></font>!";
                $analise .= '<br>';
       
                if ($aa != 1 AND $pokemoninfo['attack_iv'] >= $mr) {
                    $analise .= "<br>- Mas o Status de <b>Ataque</b> é bom também.<br>";
                }
                if ($bb != 1 AND $pokemoninfo['defence_iv'] >= $mr) {
                    $analise .= "<br>- Hmm. E o Status de <b>Defesa</b> é bom também.<br>";
                }
                if ($dd != 1 AND $pokemoninfo['spc.attack_iv'] >= $mr) {
                    $analise .= "<br>- Embora seu Status de <b>Sp. Ataque</b> sejam igualmente bons.<br>";
                }
                if ($ee != 1 AND $pokemoninfo['spc.defence_iv'] >= $mr) {
                    $analise .= "<br>- No entanto, seu Status de <b>Sp. Defesa</b> parecem ser tão bons.<br>";
                }
                if ($cc != 1 AND $pokemoninfo['speed_iv'] >= $mr) {
                    $analise .= "<br>- E, bem, o seu Status de <b>Speed</b> é bom também.<br>";
                }
                if ($ff != 1 AND $pokemoninfo['hp_iv'] >= $mr) {
                    $analise .= "<br>- Sim! O Status de <b>HP</b> é igualmente bom.<br>";
                }
           
                if ($pokemoninfo['hp_iv'] == 0) {
                    $analise .= "<br>Mas seu Status de <font color='red'>HP</font>... é bem triste, sabe?";
                    $a = 1;
                }else if ($pokemoninfo['attack_iv'] == 0) {
                    $analise .= "<br>Mas esse Status de <font color='red'>Ataque</font>... é terrível...";
                    $b = 1;
                }else if ($pokemoninfo['defence_iv'] == 0) {
                    $analise .= "<br>Mas como você pode vencer a batalha com esse tipo de Status de <font color='red'>Defesa</font>?";
                    $c = 1;
                }else if ($pokemoninfo['spc.attack_iv'] == 0) {
                    $analise .= "<br>Mas esse Status de <font color='red'>Sp. Ataque</font> nem vai deixar um arranhão em um adversário...";
                    $d = 1;
                }else if ($pokemoninfo['spc.defence_iv'] == 0) {
                    $analise .= "<br>Mas esse baixo Status de <font color='red'>Sp. Defesa</font> pode te deixar na mão...";
                    $e = 1;
                }else if ($pokemoninfo['speed_iv'] == 0) {
                    $analise .= "<br>Mas você chegará em nenhum lugar rapidamente com esse baixo Status de <font color='red'>Speed</font>...";
                    $f = 1;
                }

                $analise .= "<br><br>De qualquer maneira, é assim que eu o julgo.";
        
                $analise .= "<br><a href='./juiz'>Ajude-me com outro Pokémon.</a>";
          
            }
        }
    }

    echo addNPCBox(30, 'Juiz Pokémon', 'Olá, treinador, tudo bem? <br><br>Com o uso de minhas pesquisas posso te ajudar a julgar as habilidades de seus Pokémon.
    <br>Você teria algum ai? Caso sim, vai será um prazer te ajudar!
    <br><br>
    Com esta ferramenta você podera saber em que seu pokémon é forte ou fraco e por apenas: <b>'.highamount($custo2).'</b> silvers!');
    if ($gebruiker['rank'] >= 4) {
    // if (!$sucesso AND $gebruiker['premiumaccount'] < time()) { echo '<div class="blue">Necessário ser premium.</div>'; }
    ?>
    <?php 
    if ($sucesso) {
        echo '<div class="box-content" style="color: #fff; text-align: left; padding: 10px; border-radius: 6px; font-size: 14.4px">
                <table>
                    <tr>
                        <td style="width: 70%"><div style="width: 100%; padding-left: 30px;">
                        '.$analise.'
                    </div></td>
                        <td style="width: 28%"><center><img src="'.$static_url . '/'.$img['link'].'" class="sprite"><br>#'.$pokemoninfo['id'].'</center></td>
                    </tr>
                </table>
             </div>';
    }
    ?>
         <?php 
    if (!$sucesso) {
    ?>
        <style>
            .carousel-cell {
                margin: 10px 10px;
                filter: grayscale(100%);
                transform: scale(0.85);
                overflow: hidden;
            }
            .carousel-cell.is-selected {
                filter: grayscale(20%) invert(8%);
                transition: 1s;
                transform: scale(1);
            }
        </style>
        <div class="box-content" style="width: 100%">
            <table width="100%" style="height: 170px;" class="general">
                <thead><tr><th colspan="6">Minha equipe</th></tr></thead>
                <tbody><tr>
                        <script>
                            var $poke_array_id = [];
                            var $poke_array_iid = [];
                            var $poke_array_name = [];
                            var $poke_array_spe = [];
                        </script>

                        <td style="padding: 0">
                            <div class="main-carousel" style="height: 97px; position: relative">
                                <?php
                                    $pokemon_profiel_sql = DB::exQuery("SELECT `pokemon_speler`.*,`pokemon_wild`.`naam`,`pokemon_wild`.`type1`,`pokemon_wild`.`type2` FROM `pokemon_speler` INNER JOIN `pokemon_wild` ON `pokemon_speler`.`wild_id`=`pokemon_wild`.`wild_id` WHERE `user_id`='" . $_SESSION['id'] . "' AND `opzak`='ja' ORDER BY `opzak_nummer` ASC");
                                    //Pokemons opzak weergeven op het scherm
                                    while($pokemon_profile = $pokemon_profiel_sql->fetch_assoc()) {
                                        $pokemon_profile = pokemonei($pokemon_profile, $txt);
                                        $of_name = $pokemon_profile['naam'];
                                        $popup = pokemon_popup($pokemon_profile, $txt);
                                        $pokemon_profile['naam'] = pokemon_naam($pokemon_profile['naam'], $pokemon_profile['roepnaam'], $pokemon_profile['icon']);
                                ?>
                                        <div class="carousel-cell" style="text-align: center;">
                                            <div style="display:table-cell; vertical-align:middle; min-width: 150px; height: 150px;">
                                                <?='<img id="my_pokes_infos" class="tip_bottom-middle" title="'.$popup.'" src="' . $static_url . '/'.$pokemon_profile['link'].'" />';?>
                                                <script id="remove">
                                                    $poke_array_id.push("<?=$pokemon_profile['wild_id']?>");
                                                    $poke_array_iid.push("<?=$pokemon_profile['id']?>");
                                                    $poke_array_name.push("<?=$of_name?>");
                                                    $poke_array_spe.push("<?=$pokemon_profile['naam']?>");

                                                    document.querySelector('#remove').outerHTML = '';
                                                </script>
                                            </div>
                                        </div>
                                <?php
                                    }
                                ?>
                            </div>
                            <div style="width: 100%; background: rgba(0, 0, 0, .3); position: relative; bottom: 0; text-align: center; height: 53px; padding-top: 3px; margin-top: -8px; border-bottom-right-radius: 2px;  border-bottom-left-radius: 2px">
                                <div style="width: 100%; text-align: center; font-size: 17px; margin-top: 3px">
                                    <h4 id="poke_name" style="margin: 0; color: #eee; font-weight: bold;"></h4>
                                    <a href="./pokedex&poke=1" id="poke_link" style="color: #eee; font-size: 13px"></a>
                                </div>
                            </div>
                        </td>
                </tr></tbody>
                <tfoot>
                    <tr>
                        <td>
                             <form method="post" action="./juiz">
                                <input type="hidden" name="pokemonid" id="poke_id" value=""/>
                                <center><input type="submit" name="juiz" id="poke_submit" value="Ver JUIZ de " class="button"  style="margin: 3px"/></center>
                            </form>
                        </td>
                    </tr>
                </tfoot>
            </table>
        </div>

        <script>
            var $carousel = $('.main-carousel');
            var $poke_name = $('#poke_name');
            var $poke_link = $('#poke_link');
            var $poke_id = $('#poke_id');
            var $poke_submit = $('#poke_submit');

            var $car = $carousel.flickity({
                cellAlign: 'center',
                contain: false,
                pageDots: false,
                wrapAround: false,
                autoPlay: false
            });

            var flkty = $carousel.data('flickity');

            $carousel.on('select.flickity', function() {
                $poke_link.attr('href', '/pokedex&poke='+$poke_array_id[flkty.selectedIndex]);
                $poke_link.html($poke_array_name[flkty.selectedIndex]);
                $poke_name.html($poke_array_spe[flkty.selectedIndex]);

                $poke_id.val ($poke_array_iid[flkty.selectedIndex]);
                $poke_submit.val ('Ver JUIZ');
            });

            $poke_link.attr('href', '/pokedex&poke='+$poke_array_id[0]);
            $poke_link.html($poke_array_name[0]);
            $poke_name.html($poke_array_spe[0]);

            $poke_id.val ($poke_array_iid[0]);
            $poke_submit.val ('Ver JUIZ');

            $car.resize();
        </script>
<?php } 
        
    } else {
    echo '<div class="red">RANK MÍNIMO PARA VER AS IVs DOS POKÉMONS NO JUIZ: 4 - TRAINER. CONTINUE UPANDO PARA LIBERAR!</div>';
} ?>