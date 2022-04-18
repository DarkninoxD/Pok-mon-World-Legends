<?php

class Quests {

    public function allowedQuest ($uid) {
        $user = $this->getInfos($uid)->fetch_assoc();

        if ($user['rank'] >= 4) return true;
        else return false;
    }

    public function setStatus ($type, $uid, $force = 1) {
        if ($this->allowedQuest($uid)) {
            $user = $this->getInfos($uid)->fetch_assoc();
            $quest_act = $this->getActualQuests();

            for ($i = 0; $i < sizeof($quest_act); $i++) {
                $getQuest = $this->getQuest($quest_act[$i])->fetch_assoc();

                if ($type == $getQuest['type']) {
                    $quest .= 'quest_'.($i+1).'_req';

                    if ($force == 1) {
                        if ($user[$quest] < $getQuest['quant_wid']) {
                            DB::exQuery("UPDATE `gebruikers` SET `".$quest."`=`".$quest."`+'".$force."' WHERE `user_id`='".$uid."'");
                        }
                    } else {
                        if ($type == 'heal') {
                            if ($user[$quest] < $getQuest['quant_wid']) {
                                if (($user[$quest]+$force) <= $getQuest['quant_wid']) {
                                    DB::exQuery("UPDATE `gebruikers` SET `".$quest."`=`".$quest."`+'".$force."' WHERE `user_id`='".$uid."'");
                                } else {
                                    DB::exQuery("UPDATE `gebruikers` SET `".$quest."`='".$getQuest['quant_wid']."' WHERE `user_id`='".$uid."'");
                                }
                            }
                        } else {
                            if ($force == $getQuest['quant_wid']) {
                                DB::exQuery("UPDATE `gebruikers` SET `".$quest."`='".$force."' WHERE `user_id`='".$uid."'");
                            }
                        }
                    }
                }
            }
        }
    }

    public function setQuest ($id) {
        $quest = $this->getQuest($id)->fetch_assoc();
        if ($quest['type'] == 'catch_single') {
            $quant_wid = DB::exQuery("SELECT `wild_id` FROM `pokemon_wild` WHERE `zeldzaamheid` <= 3 AND `aparece` = 'sim' AND `gebied` != '' ORDER BY RAND() LIMIT 1")->fetch_assoc()['wild_id'];
        } else {
            $quant_all = explode(',', $quest['quant_wid_all']);
            shuffle($quant_all);
            $quant_wid = $quant_all[0];
        }

        DB::exQuery("UPDATE `daily_quest` SET `quant_wid`='".$quant_wid."' WHERE `id` = '$id'");
    }

    public function setPrize ($id) {
        $type = array('item', 'silver', 'gold');
        $quant_gold = array(3, 5, 10);
        $quant_silver = array(5000, 7000, 10000, 13000, 15000, 20000);
        $quant_item = array('106-5-10-20', '140-2-4', '62-1', '139-2-4', '103-5-10', '14-1', '27-1', '425-1', '138-2-4', '89-1', '389-2-4', '19-1', '30-1', '68-1', '424-1', '438-1', '116-2-4', '135-2-4', '439-1', '2-1', '126-2-4-5', '127-2-4-5', '128-2-4-5', '129-2-4-5', '130-2-4-5', '442-1-2', '443-1-2-3', '444-1-2-3', '445-1-2-3');
        $rand = rand(1, 100);

        if ($rand <= 65) {
            shuffle($quant_item);

            $type = $type[0];
            $items = explode('-', $quant_item[0]);
            $item = $items[0];
            unset($items[0]);

            $items = array_values($items);
            shuffle($items);
            $qnt = $items[0];
        } else if ($rand <= 90) {
            $type = $type[1];
            $item = '0';
            shuffle($quant_silver);
            $qnt = $quant_silver[0];
        } else {
            $type = $type[2];
            $item = '0';
            shuffle($quant_gold);
            $qnt = $quant_gold[0];
        }

        DB::exQuery("UPDATE `daily_quest` SET `recomp_type`='".$type."',`recomp_id`='".$item."',`recomp_quant`='".$qnt."'  WHERE `id` = '$id'");
    }

    public function getQuest ($id) {
        return DB::exQuery("SELECT * FROM `daily_quest` WHERE id='$id'");
    }

    public function getActualQuests () {
        $quest_1 = DB::exQuery("SELECT * FROM `configs` WHERE id='6'")->fetch_assoc()['valor'];
        $quest_2 = DB::exQuery("SELECT * FROM `configs` WHERE id='7'")->fetch_assoc()['valor'];

        return array($quest_1, $quest_2);
    }

    public function getItem ($id) {
        return DB::exQuery("SELECT * FROM `markt` WHERE `id`='$id'");
    }

    public function getInfos ($id, $get = '*') {
        if (ctype_digit($id)) {
            return DB::exQuery("SELECT $get FROM `gebruikers` WHERE user_id='$id'");
        } else {
            return DB::exQuery("SELECT $get FROM `gebruikers` WHERE username='$id'");
        }
    }

}