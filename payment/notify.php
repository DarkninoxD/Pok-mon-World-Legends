<?php
require_once('../app/includes/resources/config.php');

if (isset($_POST['notificationCode'])) {
    $notificationCode = preg_replace('/[^[:alnum:]-]/','',$_POST['notificationCode']);
    
    //SANDBOX
    // $data['token'] = '95C7BFB7BA5E4E1CA6A6D0A70752D5B3';
    $data['token'] = '736AFAF9FD8944F29DB2068DA881985F';
    $data['email'] = 'guigirao@hotmail.com';

    //REMOVER SANDBOX;
    // $url = 'https://ws.sandbox.pagseguro.uol.com.br/v3/transactions/notifications/'.$notificationCode.'?email='.$data['email'].'&token='.$data['token'];
    $url = 'https://ws.pagseguro.uol.com.br/v3/transactions/notifications/'.$notificationCode.'?email='.$data['email'].'&token='.$data['token'];
    
    $data = http_build_query($data);
    
    $curl = curl_init($url);
    
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
    curl_setopt($curl, CURLOPT_HTTP_VERSION, CURL_HTTP_VERSION_1_1);
    $xml = curl_exec($curl);
    
    curl_close($curl);
    
    $xml = simplexml_load_string($xml);
    $reference = $xml->reference;
    $status = $xml->status;
    $method = $xml->paymentMethod->type;
    
    if (isset($reference) && isset($status)) {
        $exist = DB::exQuery("SELECT `ref`, `id_user`, `pack_id`, `id`, `user_id` FROM `fatura` WHERE `id`='".$reference."'");
        if ($exist->num_rows > 0) {
            $exist = $exist->fetch_assoc();
            
            DB::exQuery("UPDATE `fatura` SET `status` = '".$status."' WHERE `id` = '$reference';");
            
            if ($status == 3) {
                $get_pack = DB::exQuery("SELECT * FROM `donate_packs` WHERE `id`='$exist[pack_id]'")->fetch_assoc();
                if ($get_pack['starter'] != 1) {
                    if ($get_pack['silvers'] <= 0) {
                        if ($get_pack['premium'] > 0) {
                          $gebruiker = DB::exQuery("SELECT `premiumaccount` FROM `gebruikers` WHERE `user_id`='".$exist['user_id']."'")->fetch_assoc();
                          $new_vip = ($gebruiker['premiumaccount'] > time()) ? ($gebruiker['premiumaccount'] + (86400 * $get_pack['premium'])) : (time() + (86400 * $get_pack['premium']));
                          DB::exQuery("UPDATE `gebruikers` SET `premiumaccount`={$new_vip} WHERE `user_id`={$exist['user_id']}");

                          $event = '<img src="' . $static_url . '/images/icons/blue.png" width="16" height="16" class="imglower" /> <b>Parabéns!</b> O <b> Pacote '.$get_pack['naam'].' ('.$get_pack['golds'].' Golds e +'.$get_pack['premium'].' dias de PREMIUM)</b> que você comprou acabou de chegar!';
                        } else {
                          $event = '<img src="' . $static_url . '/images/icons/blue.png" width="16" height="16" class="imglower" /> <b>Parabéns!</b> O <b> Pacote '.$get_pack['naam'].' ('.$get_pack['golds'].' Golds)</b> que você comprou acabou de chegar!';
                        }
                    } else {
                        if ($get_pack['premium'] > 0) {
                          $gebruiker = DB::exQuery("SELECT `premiumaccount` FROM `gebruikers` WHERE `user_id`='".$exist['user_id']."'")->fetch_assoc();
                          $new_vip = ($gebruiker['premiumaccount'] > time()) ? ($gebruiker['premiumaccount'] + (86400 * $get_pack['premium'])) : (time() + (86400 * $get_pack['premium']));
                          DB::exQuery("UPDATE `gebruikers` SET `premiumaccount`={$new_vip} WHERE `user_id`={$exist['user_id']}");

                          $event = '<img src="' . $static_url . '/images/icons/blue.png" width="16" height="16" class="imglower" /> <b>Parabéns!</b> O <b> Pacote '.$get_pack['naam'].' ('.$get_pack['golds'].' Golds, '.$get_pack['silvers'].' Silvers e +'.$get_pack['premium'].' dias de PREMIUM)</b> que você comprou acabou de chegar!';
                        } else {
                          $event = '<img src="' . $static_url . '/images/icons/blue.png" width="16" height="16" class="imglower" /> <b>Parabéns!</b> O <b> Pacote '.$get_pack['naam'].' ('.$get_pack['golds'].' Golds e '.$get_pack['silvers'].' Silvers)</b> que você comprou acabou de chegar!';
                        }

                        DB::exQuery("UPDATE `gebruikers` SET `silver`=`silver`+'".$get_pack['silvers']."' WHERE `user_id`='".$exist['user_id']."'");
                    }

                    if (!empty($get_pack['pokemon'])) {
                      $pokemon = explode(',', $get_pack['pokemon']);
                      $rand = rand(0, (sizeof($pokemon) - 1));
                      $poke_id = $pokemon[$rand];
                      
                      $new_computer_sql = DB::exQuery("SELECT * FROM `pokemon_wild` WHERE `wild_id`='".$poke_id."'")->fetch_assoc();

                      //Alle gegevens vast stellen voordat alles begint.
                      $new_computer['id']             = $new_computer_sql['wild_id'];
                      $new_computer['pokemon']        = $new_computer_sql['naam'];
                      $new_computer['aanval1']        = $new_computer_sql['aanval_1'];
                      $new_computer['aanval2']        = $new_computer_sql['aanval_2'];
                      $new_computer['aanval3']        = $new_computer_sql['aanval_3'];
                      $new_computer['aanval4']        = $new_computer_sql['aanval_4'];
                      $ability        = explode(',', $new_computer_sql['ability']);
                      $klaar          = false;
                      $loop           = 0;
                      $lastid         = 0;
                    
                      //Loop beginnen
                      do{ 
                        $teller = 0;
                        $loop++;
                        //Levelen gegevens laden van de pokemon
                        $levelenquery = DB::exQuery("SELECT * FROM `levelen` WHERE `wild_id`='".$new_computer['id']."' AND `level`<='".'5'."' ORDER BY `id` ASC ");
                  
                        //Voor elke pokemon alle gegeven behandelen
                        while($groei = $levelenquery->fetch_assoc()) {
                  
                          //Teller met 1 verhogen
                          $teller++;
                          //Is het nog binnen de level?
                          if ('5' >= $groei['level']) {
                            //Is het een aanval?
                            if ($groei['wat'] == 'att') {
                              //Is er een plek vrij
                              if (empty($new_computer['aanval1'])) $new_computer['aanval1'] = $groei['aanval'];
                              else if (empty($new_computer['aanval2'])) $new_computer['aanval2'] = $groei['aanval'];
                              else if (empty($new_computer['aanval3'])) $new_computer['aanval3'] = $groei['aanval'];
                              else if (empty($new_computer['aanval4'])) $new_computer['aanval4'] = $groei['aanval'];
                              //Er is geen ruimte, dan willekeurig een aanval kiezen en plaatsen
                              else{
                                if (($new_computer['aanval1'] != $groei['aanval']) AND ($new_computer['aanval2'] != $groei['aanval']) AND ($new_computer['aanval3'] != $groei['aanval']) AND ($new_computer['aanval4'] != $groei['aanval'])) {
                                  $nummer = rand(1,4);
                                  if ($nummer == 1) $new_computer['aanval1'] = $groei['aanval'];
                                  else if ($nummer == 2) $new_computer['aanval2'] = $groei['aanval'];
                                  else if ($nummer == 3) $new_computer['aanval3'] = $groei['aanval'];
                                  else if ($nummer == 4) $new_computer['aanval4'] = $groei['aanval'];
                                }
                              }
                            }
                          }
                  
                          //Er gebeurd niks dan stoppen
                          else{
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
                      }
                    while(!$klaar);
                  
                      //Karakter kiezen 
                      $karakter  = DB::exQuery("SELECT * FROM `karakters` ORDER BY rand() limit 1")->fetch_assoc();
                  
                      //Expnodig opzoeken en opslaan
                      $level = 6;
                      $experience = DB::exQuery("SELECT `punten` FROM `experience` WHERE `soort`='".$new_computer_sql['groei']."' AND `level`='".$level."'")->fetch_assoc();
                  
                        $attack_iv = rand(25, 31);
                        $defence_iv = rand(25, 31);
                        $speed_iv = rand(25, 31);
                        $spcattack_iv = rand(25, 31);
                        $spcdefence_iv = rand(25, 31);
                        $hp_iv = rand(25, 31);
                  
                      
                      //Promo IV minimo 15
                  
                      //Stats berekenen
                      $new_computer['attackstat']     = round(((($new_computer_sql['attack_base']*2+$attack_iv)*5/100)+5)*$karakter['attack_add']);
                      $new_computer['defencestat']    = round(((($new_computer_sql['defence_base']*2+$defence_iv)*5/100)+5)*$karakter['defence_add']);
                      $new_computer['speedstat']      = round(((($new_computer_sql['speed_base']*2+$speed_iv)*5/100)+5)*$karakter['speed_add']);
                      $new_computer['spcattackstat']  = round(((($new_computer_sql['spc.attack_base']*2+$spcattack_iv)*5/100)+5)*$karakter['spc.attack_add']);
                      $new_computer['spcdefencestat'] = round(((($new_computer_sql['spc.defence_base']*2+$spcdefence_iv)*5/100)+5)*$karakter['spc.defence_add']);
                      if ($new_computer_sql['wild_id'] != 292) {
                        $new_computer['hpstat']       = round(((($new_computer_sql['hp_base']*2+$hp_iv)*5/100)+'5')+10);
                      } else {
                        $new_computer['hpstat']       = 1;
                      }
                      
                  
                      //Save Computer
                      $tijd = date('Y-m-d H:i:s');
                      $egg = 1;
                      $date = date('Y-m-d H:i:s');
                  
                      $rand_ab = rand(0, (sizeof($ability) - 1));
                      $ability = $ability[$rand_ab];
                      
                      DB::exQuery("INSERT INTO `pokemon_speler` (`wild_id`, `user_id`, `opzak`, `opzak_nummer`, `karakter`, `level`, `levenmax`, `leven`, `totalexp`, `expnodig`, `attack`, `defence`, `speed`, `spc.attack`, `spc.defence`, `attack_iv`, `defence_iv`, `speed_iv`, `spc.attack_iv`, `spc.defence_iv`, `hp_iv`, `attack_ev`, `defence_ev`, `speed_ev`, `spc.attack_ev`, `spc.defence_ev`, `hp_ev`, `aanval_1`, `aanval_2`, `aanval_3`, `aanval_4`, `effect`, `ei`, `ei_tijd`, `ability`, `capture_date`) 
                      VALUES ('".$new_computer['id']."', '".$exist['user_id']."', 'nee', '', '".$karakter['karakter_naam']."', '".'5'."', '".$new_computer['hpstat'] ."', '".$new_computer['hpstat'] ."', '".$experience['punten']."', '".$experience['punten']."', '".$new_computer['attackstat']."', '".$new_computer['defencestat']."', '".$new_computer['speedstat']."', '".$new_computer['spcattackstat']."', '".$new_computer['spcdefencestat']."', '".$attack_iv."', '".$defence_iv."', '".$speed_iv."', '".$spcattack_iv."', '".$spcdefence_iv."', '".$hp_iv."', '".$new_computer_sql['effort_attack']."', '".$new_computer_sql['effort_defence']."', '".$new_computer_sql['effort_spc.attack']."', '".$new_computer_sql['effort_spc.defence']."', '".$new_computer_sql['effort_speed']."', '".$new_computer_sql['effort_hp']."', '".$new_computer['aanval1']."', '".$new_computer['aanval2']."', '".$new_computer['aanval3']."', '".$new_computer['aanval4']."', '".$new_computer_sql['effect']."', '".$egg."', '".$tijd."', '".$ability."', '".$date."')");
                      
                      $event2 = '<img src="' . $static_url . '/images/icons/blue.png" width="16" height="16" class="imglower" /> O <b> Pacote '.$get_pack['naam'].' que você comprou, veio de presente um: '.$new_computer_sql['naam'].'!';
                      DB::exQuery("INSERT INTO gebeurtenis (`datum`,`ontvanger_id`,`bericht`,`gelezen`) VALUES (NOW(), '" . $exist['user_id'] . "', '" . $event2 . "', '0')");
                    }
                } else {
                    DB::exQuery("UPDATE `gebruikers` SET `silver`=`silver`+'".$get_pack['silvers']."' WHERE `user_id`='".$exist['user_id']."'");
                    
                    $gebruiker = DB::exQuery("SELECT `premiumaccount` FROM `gebruikers` WHERE `user_id`='".$exist['user_id']."'")->fetch_assoc();
                    $new_vip = ($gebruiker['premiumaccount'] > time()) ? ($gebruiker['premiumaccount'] + (86400 * 7)) : (time() + (86400 * 7));
		                DB::exQuery("UPDATE `gebruikers` SET `premiumaccount`={$new_vip} WHERE `user_id`={$exist['user_id']}");
                    
                    $event = '<img src="' . $static_url . '/images/icons/blue.png" width="16" height="16" class="imglower" /> <b>Parabéns!</b> O <b> '.$get_pack['naam'].' ('.$get_pack['golds'].' Golds, '.$get_pack['silvers'].' Silvers, +1 Semana de PREMIUM)</b> que você comprou acabou de chegar!';
                }

                DB::exQuery("INSERT INTO gebeurtenis (`datum`,`ontvanger_id`,`bericht`,`gelezen`) VALUES (NOW(), '" . $exist['user_id'] . "', '" . $event . "', '0')");
                DB::exQuery("UPDATE `rekeningen` SET `gold`=`gold`+'".$get_pack['golds']."' WHERE `acc_id`='".$exist['id_user']."'");
            }
        }
    }
}