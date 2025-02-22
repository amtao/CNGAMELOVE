<?php
//团队类
class TeamModel
{
	public $_key = "_team";
	public $uid;
	public $info;
	public function getKey(){
		return $this->uid.$this->_key;
	}

	//update type 1 hero, 2 wife, 3 son, 4 clothe 5 clothe_suit 6card 7 baowu
	public function __construct($uid,$clear = false, $update_type = 0)
	{
		$this->uid = $uid;
        $cache = Common::getCacheByUid($this->uid);
		$this->info = $cache->get($this->getKey());
		if($this->info == false || $clear || !isset($this->info["baseReset"]) ){

            $this->info["baseReset"] = 1;
		    if (empty($this->info['clothe_suit']) || empty($this->info['pct_epadd'])){
		        $update_type = 0;
            }
			//总属性
			$allep = array(
				1 => 0, 2 => 0,
				3 => 0, 4 => 0,
			);

			//团队信息 将会输出
			//门客列表 => 门客属性/红颜加成
			//主角总属性 / 来自各种地方的加成 / 门客 / 子嗣 / 知己(额外加成)
			if ($update_type == 0 || $update_type == 5){
                $act6140Model = Master::getAct6140($this->uid);
                $this->info['clothe_suit'] = $act6140Model->getSuitAdd();
            }

			//红颜配置
            //红颜类
            // if ($update_type == 0 || $update_type == 2){
            //     $this->updateWife();
            // }

            //徒弟
            if ($update_type == 0 || $update_type == 3){
                $this->updateSon();
            }

            $allep = Game::epadd($allep, $this->info['sonep']);

            //伙伴
            if ($update_type == 0 || $update_type == 1 || $update_type == 2 || $update_type == 5){
                $this->updateHero($this->info['clothe_suit']);
            }

            $allep = Game::epadd($allep, $this->info['hero_ep']);

            //卡牌属性
            if ($update_type == 0 || $update_type == 6){
                $this->updateCard();
            }
            //四海奇珍属性
            if ($update_type == 0 || $update_type == 7){
                $this->updateBaowu();
            }

            //服装属性
            $act6140Model = Master::getAct6140($this->uid);
            if ($update_type == 0 || $update_type == 4){
                $this->info['clothe'] = $act6140Model->getAddProp();

                $clothe_suit = Game::epmultiply($this->info['clothe_suit'],count($this->info['heros']));
                $clothe = Game::epaddr1($this->info['clothe'],$clothe_suit);
                $this->info['clotheaddep'] = $this->info['clothebaseep'] = $clothe;
            }

            $this->info['baseep'] = $allep; //四项属性 (前端用)
            //百分比属性加成
            $this->info['percentage'] = $act6140Model->getAddPercentage();
            $this->info['pct_epadd']  = $this->percentage_add();

            $allep = Game::epadd($allep, $this->info['baowuep']);
            $allep = Game::epadd($allep, $this->info['cardep']);

            $allep = Game::epadd($allep, $this->info['clothe']);
            $allep = Game::epadd($allep, $this->info['pct_epadd']);

            $this->info['addep'] = $allep; //四项属性 (前端用)
            // 新增属性，在最后的时候总属性百分比
            if (isset($this->info['pct_epadd']["all"])) {
                $alladdep =  Game::epmultiply_arr($allep, $this->info['pct_epadd']["all"]);
                $allep = Game::epaddr1($allep, $alladdep);
            }

            $this->info['shili'] = array_sum($allep);//总势力(自己用)
            $this->info['allep'] = $allep;//四项属性 (输出用)

            //记录势力值
            $Act99Model = Master::getAct99($this->uid);
            $Act99Model->setep($allep);

			//设置到缓存
			$cache->set($this->getKey(),$this->info);

			//主线任务 - 刷新
			$Act39Model = Master::getAct39($this->uid);
            $Act39Model->task_refresh(8);
            
            $Act39Model->task_add(118, $this->info['herozzLvl']);
            $Act39Model->task_add(119, $this->info['fetterlv']);
		}
	}

	//百分比加成
    public function percentage_add(){
	    $add_ep = array(1 => 0,2 => 0,3 => 0,4 => 0);
        if (!empty($this->info['percentage'])){
            foreach ($this->info['percentage'] as $k=>$v){
                switch ($k){
                    case 'son':
                        $ep =  Game::epmultiply_arr($this->info['sonep'],$v);
                        $this->info['sonaddep'] = Game::epaddr1($this->info['sonep'],$ep);
                        break;
                    case 'hero':
                        //扣掉服饰加成
                        $clothe_suit = Game::epmultiply($this->info['clothe_suit'],count($this->info['heros']));
                        $hero_ep = $this->info['hero_ep'];
                        for ($i=1;$i<=count($clothe_suit);$i++){
                            $hero_ep[$i] -= $clothe_suit[$i];
                        }
                        $ep =  Game::epmultiply_arr($hero_ep,$v);
                        $this->info['heroaddep'] = Game::epaddr1($hero_ep,$ep);
                        break;
                    case 'clothe':
                        //计算服饰加成
                        $clothe_suit = Game::epmultiply($this->info['clothe_suit'],count($this->info['heros']));
                        $clothe = Game::epaddr1($this->info['clothe'],$clothe_suit);
                        $ep =  Game::epmultiply_arr($clothe,$v);
                        $this->info['clotheaddep'] = Game::epaddr1($clothe,$ep);
                        break;
                    case 'card':
                        //卡牌加成百分比
                        $ep =  Game::epmultiply_arr($this->info['cardep'],$v);
                        $this->info['cardaddep'] = Game::epaddr1($this->info['cardep'],$ep);
                        break;
                    case 'baowu':
                        //卡牌加成百分比
                        $ep =  Game::epmultiply_arr($this->info['baowuep'],$v);
                        $this->info['baowuaddep'] = Game::epaddr1($this->info['baowuep'],$ep);
                        break;
                    case 'all':
                        //总属性百分比加成
                        $ep =  array(1 => 0,2 => 0,3 => 0,4 => 0);
                        break;
                }
                $add_ep = Game::epaddr1($add_ep,$ep);
            }
        }
        return $add_ep;
    }

    private function wife_add_hero($wife_epadd, $hid, $_hero_ep){
        //--------计算红颜加成--------
        $wife_ep = array(1 => 0,2 => 0,3 => 0,4 => 0);
        //遍历红颜的资质 计算加成
        if (isset($wife_epadd[$hid])){
            foreach ($wife_epadd[$hid] as $kep => $vep){
                if ($kep < 10){
                    //如果是 数值加成 直接加上
                    $wife_ep[$kep] += $vep; // + 红颜数值加成
                }else{
                    //比例加成  = (资质属性 + 嗑药属性) * 红颜比例加成
                    $_kep = $kep - 10;
                    $wife_ep[$_kep] += floor($_hero_ep[$_kep] * $vep/10000);
                }
            }
        }
        return $wife_ep;
    }

    private function updateHero($suit_add){
        //门客类
        $HeroModel = Master::getHero($this->uid);
        //信物等级
        $act2001Model = Master::getAct2001($this->uid);
        //羁绊类
        $act6001Model = Master::getAct6001($this->uid);
        //羁绊类
        $act6005Model = Master::getAct6005($this->uid);
        $jb_group_ep = $act6005Model->getGroupProp();
        //领袖气质
        $act6219Model = Master::getAct6219($this->uid);
        //获取翰林等级
        $Act58Model = Master::getAct58($this->uid);
        $hl_skill = $Act58Model->getskill();
        //灵囿类
        $act19Model = Master::getAct19($this->uid);
        $pets_ep = $act19Model->get_pets_ep();
        //时装
        $act6143Model = Master::getAct6143($this->uid);
        $dress_ep = $act6143Model->getAddProp();

        $allep = array(
            1 => 0, 2 => 0,
            3 => 0, 4 => 0,
        );

        //资质技能星级配置
        $hero_epskill_cfg = Game::getcfg('hero_epskill');
        //遍历门客 计算总战力
        $all_hero_opt = array();//门客总信息表 (输出用)
        $pk_hero =  array();//衙门战可用的门客列表
        $max_hero_lv =  0;//当前最高门客等级
        $hero_count =  0;//门客数量
        $hero_allep = array();//门客总属性(排序用)
        $hero_ep = array();
        $all_hero_zz = array(1 => 0,2 => 0,3 => 0,4 => 0);//门客总资质
        $charisma_ep = $act6219Model->get_add_ep();//领袖气质
        $drug = $act6219Model->get_lead_drug();
        $alllove = 0;//总好感值
        $allFetters = 0;//总羁绊值
        $allFettersLv = 0;//总羁绊等级
        $zzLvl = 0;

        foreach ($HeroModel->info as $hid => $hv){
            //获取英雄输出信息
            $hero_base = $HeroModel->getBase_buyid($hid);//单个英雄 输出信息

            //--------计算英雄资质--------
            $hero_zz = array(1 => 0,2 => 0,3 => 0,4 => 0);
            $_hero_ep = array(1 => 0,2 => 0,3 => 0,4 => 0);
            //遍历一个英雄的 所有资质技能 算出总资质
            foreach ($hv['epskill'] as $epsk_id => $epsk_lv){
                //加上翰林等级
                $ace = 1;
                if(isset($hl_skill[$epsk_id])){
                    $epsk_lv += $hl_skill[$epsk_id];
                    $ace = 2;
                }
                $star = $hero_epskill_cfg[$epsk_id]['star'];
                $_hero_ep[$hero_epskill_cfg[$epsk_id]['ep']] += Game::getCfg_formula()->partner_prop($hv['level'], $star, ($epsk_lv - $ace));
                //累加技能资质
                $hero_zz[$hero_epskill_cfg[$epsk_id]['ep']] += $star * $epsk_lv;
                $zzLvl += $epsk_lv;
            }
            $alllove += $hv['love'];
            $all_hero_zz = Game::epaddr1($all_hero_zz,$hero_zz);
            //资质 保存 内部保存信息
            $hero_base['zz'] = Game::fmt_ep($hero_zz); //总资质属性信息 输出
            //资质属性 保存 输出保存信息
            $hero_base['zep'] = Game::fmt_ep($_hero_ep); //资质属性信息 输出

            $hero_base['hep'] = empty($drug)?$hero_base['hep']:Game::epmultiply_arr($hero_base['hep'],$drug);

            //嗑药属性
            $_yao = Game::det_ep($hero_base['hep']);
            //属性 累加到英雄总属性表 嗑药属性 + 资质属性
            $hero_ep = Game::epadd($_hero_ep,$_yao);

            //红颜加成属性 保存到输出数组
            // $wife_ep = $this->wife_add_hero($wife_epadd, $hid, $_hero_ep);
            // $hero_base['wep'] = Game::fmt_ep($wife_ep);
            // $hero_ep = Game::epadd($hero_ep,$wife_ep);

            //--------计算光环加成--------
            $gh_ep = self::hero_gh($hero_base,$hero_ep);
            $hero_base['gep'] = Game::fmt_ep($gh_ep);
            $hero_ep = Game::epadd($hero_ep,$gh_ep);

            //领袖气质记录
            $hero_base['lep'] = Game::fmt_ep($charisma_ep);
            //计算领袖气质加成
            $hero_ep = Game::epadd($hero_ep, $charisma_ep);

            //计算羁绊剧情加成
            $jb_story_ep = $act6005Model->getHeroId($hid);
            $hero_ep = Game::epadd($hero_ep,$jb_story_ep);

            $heroToken_ep = $act2001Model->getTokenProp($hid);
            $hero_ep = Game::epadd($hero_ep,$heroToken_ep);

            //计算羁绊属性加成
            $lv = $act6001Model->getHeroJBLv($hid);
            $jb_ep = self::hero_jb($lv, $hero_ep);
            $jb_jep = Game::epadd($jb_ep,$jb_story_ep);
            $jbValue = $act6001Model->getHeroJB($hid);
            $allFetters += $jbValue;
            $allFettersLv += $lv%1000;

            //剧情图鉴记录
            $jb_jep = Game::epadd($jb_jep, $jb_group_ep);
            $hero_base['jep'] = Game::fmt_ep($jb_jep);
            $hero_ep = Game::epadd($hero_ep,$jb_ep);

            //计算剧情图鉴加成
            $hero_ep = Game::epadd($hero_ep,$jb_group_ep);

            //计算灵囿加成
            $hero_ep = Game::epadd($hero_ep,$pets_ep);

            //套装属性记录
            $hero_base['cep'] = Game::fmt_ep($suit_add);

            //计算套装加成
            $hero_ep = Game::epadd($hero_ep, $suit_add);

            //计算时装加成
            $hero_base['dep'] = array();
            if (isset($dress_ep[$hid])) {
                $hero_base['dep'] = $dress_ep[$hid];
                $_dress = Game::det_ep($dress_ep[$hid]);
                $hero_ep = Game::epadd($hero_ep, $_dress);
            }

            //总属性 也拿来输出(前端暂时没用)
            $hero_base['aep'] = Game::fmt_ep($hero_ep);

            //门客信息增加领袖等级
            $hero_info = Game::getcfg_info('hero',$hid);
            if ($hero_info['leaderid']>0){
                $hero_base['leadlv'] = $act6219Model->get_leadlv($hero_info['leaderid']);
            }
            //保存到总数组
            $all_hero_opt[$hid] = $hero_base;//门客总信息表
            $oneHero_ep = $hero_ep;

            //主角总属性
            $allep = Game::epadd($allep,$hero_ep);

            //门客总属性(衙门排序用)
            $hero_allep[$hid] = array_sum($hero_base['aep']);//门客总属性表

            //-----其他需求---
            //60级以上 记录为衙门出战英雄
            if ($hv['level'] >= Game::getcfg_param("gongdou_unlock_level")){
                $pk_hero[] = $hid;
            }
            //最高门客等级
            $max_hero_lv = max($max_hero_lv,$hv['level']);
            //门客数量
            $hero_count += 1;
        }

        $this->info['heros'] = $all_hero_opt;
        $this->info['pkhero'] = $pk_hero;
        $this->info['maxlv'] = $max_hero_lv;
        $this->info['herocount'] = $hero_count;
        $this->info['hero_allep'] = $hero_allep;
        $this->info['wifeep'] = $wife_ep;
        $this->info['herozz'] = array_sum($all_hero_zz);
        $this->info['hero_ep'] = $allep;
        $this->info['alllove'] = $alllove;
        $this->info['herozzLvl'] = $zzLvl;
        $this->info['fetter'] = $allFetters;
        $this->info['fetterlv'] = intval($allFettersLv);

        // 计算属性用
        $clothe_suit = Game::epmultiply($suit_add,count($all_hero_opt));
        $heroEp = $allep;
        for ($i=1;$i<=count($clothe_suit);$i++){
            $heroEp[$i] -= $clothe_suit[$i];
        }

        $this->info['heroaddep'] = $this->info['herobaseep'] = $heroEp;
    }

    private function updateSon(){
        //遍历子嗣 / 计算子嗣加成 / 子嗣数量
        //子嗣类
        $SonModel = Master::getSon($this->uid);
        $son_all_epadd = array();//子嗣总属性加成
        $smson = 0;//未成年子嗣数量
        $qingjia = array(); //记录亲家信息
        $qjsonshili = 0;  //亲家一键拜访需要的势力值
//        $Act134Model = Master::getAct134($this->uid);  //亲家好感度
        foreach($SonModel->info as $sonid=> $son_v){
            if ($son_v['state'] < 4){
                $smson ++;
            }

            $son_base = $SonModel->getSonMsg_buyid($sonid);
            //累加总属性
            $son_all_epadd = Game::epadd($son_base['ep'],$son_all_epadd);

            //如果已婚 加上配偶属性
            if ($son_base['state'] == 9){

                //配偶信息
                $spson = Master::getMarryDate($son_base['spuid'],$son_base['spsonuid']);
                //累加配偶总属性
                $son_all_epadd = Game::epadd($spson['ep'],$son_all_epadd);

//                if( $SonModel->check_qjadd($this->uid,$sonid,$son_base['spuid'])){
//                    //亲家拜访属性加成 =>我方好感度
//                    if(empty($Act134Model->info['my'][$son_base['spuid']]['num'])){
//                        $Act134Model->info['my'][$son_base['spuid']]['num'] = 0;
//                    }
//                    $mylove = $Act134Model->info['my'][$son_base['spuid']]['num'];
//                    $myep = Game::qjepadd($son_base['honor'],$mylove);
//                    $son_all_epadd = Game::epadd($myep,$son_all_epadd);
//
//                    $qjsonshili += array_sum($myep);  //我方子嗣拜访加成
//                }

//                if($SonModel->check_qjadd($son_base['spuid'],$son_base['spsonuid']) ){
//                    //亲家拜访属性加成 =>对方好感度
//                    if(empty($Act134Model->info['f'][$son_base['spuid']]['num'])){
//                        $Act134Model->info['f'][$son_base['spuid']]['num'] = 0;
//                    }
//                    $flove = $Act134Model->info['f'][$son_base['spuid']]['num'];
//                    $qjep = Game::qjepadd($spson['honor'],$flove);
//                    $son_all_epadd = Game::epadd($qjep,$son_all_epadd);
//
//                    $qjsonshili += array_sum($qjep);//对方子嗣拜访加成
//                }

//                //记录亲家信息
//                $qingjia[$son_base['spuid']] = empty($qingjia[$son_base['spuid']])?1:$qingjia[$son_base['spuid']]+1;
//
//                //亲家一键拜访需要的势力值
//                $qjsonshili += array_sum($son_base['ep']); //我方子嗣基础势力
//                $qjsonshili += array_sum($spson['ep']);  //对方子嗣基础势力
            }
        }

        $this->info['sonep'] = $son_all_epadd;
        $this->info['smson'] = $smson;
        $this->info['qingjia'] = $qingjia;
        $this->info['qjsonshili'] = $qjsonshili;

        $this->info['sonaddep'] = $this->info['sonbaseep'] = !empty($son_all_epadd)? $son_all_epadd : array(1 => 0,2 => 0,3 => 0,4 => 0);
    }

    private function updateWife(){
        //由更新的红颜列表 添加对应的要刷新的英雄ID
        $u_wifes = Master::$u_wifes;

        $WifeModel = Master::getWife($this->uid);
        $wife_skill_cfg = Game::getcfg('wife_skill');
        $wife_epadd = array();//红颜加成表 被加成英雄ID => 加成属性->值
        $alllove = 0;//总好感值
        //羁绊类
        $act6001Model = Master::getAct6001($this->uid);
        //遍历红颜 // 计算红颜对门客的加成信息 //构造加成表
        foreach($WifeModel->info as $wife_id=> $wife_v){
            //计算羁绊属性加成
            $lv = $act6001Model->getWifeJBLv($wife_id);
            $jb_sys = Game::getcfg_info('jinban_lv', $lv);
            $prop = empty($jb_sys)? 0:$jb_sys['prop'];
            // $alllove += $wife_v['love'];
            //遍历红颜技能
            foreach ($wife_v['skill'] as $sid => $slv){
                //技能配置
                $_cfg_info = $wife_skill_cfg[$sid];

                //如果这个红颜 涉及了阵法更新  则更新对应羁绊英雄的阵法信息
                if(!empty($u_wifes[$wife_id])){
                    Master::add_hero_rst($_cfg_info['heroid']);
                }

                //初始化加成值为0
                if (!isset($wife_epadd[$_cfg_info['heroid']])){
                    $wife_epadd[$_cfg_info['heroid']] = array(
                        1 => 0, 2 => 0, 3 => 0, 4 => 0,
                        11 => 0, 12 => 0, 13 => 0, 14 => 0
                    );
                }

                //全属性 或者单属性
                if ( in_array(5,$_cfg_info['epid']) ){
                    $eparr = array(1,2,3,4);
                }else{
                    $eparr = $_cfg_info['epid'];
                }
                //百分比或者 直接加
                $psc = $_cfg_info['type'] == 2?10:0;

                //计算加成
                foreach ($eparr as $ep){
                    $wife_epadd[$_cfg_info['heroid']][$ep+$psc] += ceil($_cfg_info['base'] * $slv * (1+$prop / 10000));
                }
            }
        }

        $this->info['alllove'] = $alllove;
        $this->info['wife_epadd'] = $wife_epadd;
    }

    private function updateCard(){
       
        //遍历卡牌 
        //卡牌类
        
        $CardModel = Master::getCard($this->uid);
        
        $card_all_epadd = array();//子嗣总属性加成
        $cardepall = array();
        $cardHero = array();
        $cardList = $CardModel->getCardList(true);
        $addHeroEp = array('e1' => 300,'e2' => 50,'e3'=> 50,'e4' => 50);
        //echo json_encode( $cardList );
        foreach($cardList as $cardinfo){
            $cardCfg = Game::getcfg_info("card",$cardinfo['id']);
            $heroId = $cardCfg['hero'];
            if($heroId != 0){
                if(empty($cardHero[$heroId])){
                    $cardHero[$heroId] = array();
                }
                $cardHero[$heroId] = Game::epadd(Game::filterep($cardinfo),$cardHero[$heroId]);
            }
            
            $cardepall = Game::epadd(Game::filterep($cardinfo),$cardepall);
        }
        for($i = 1;$i <= 6;$i++){
            if(empty($cardHero[$i])){
                $cardHero[$i] = array();
            }
            $cardHero[$i] = Game::epadd(Game::filterep($addHeroEp),$cardHero[$i]);
        }
        //echo json_encode( $cardepall );
        $this->info['cardep'] = $cardepall;
        $this->info['cardaddep'] = $cardepall;
        $this->info['cardHeroEp'] = $cardHero;
        /*
        $smson = 0;//未成年子嗣数量
        $qingjia = array(); //记录亲家信息
        $qjsonshili = 0;  //亲家一键拜访需要的势力值
//        $Act134Model = Master::getAct134($this->uid);  //亲家好感度
        foreach($SonModel->info as $sonid=> $son_v){
            if ($son_v['state'] < 4){
                $smson ++;
            }

            $son_base = $SonModel->getSonMsg_buyid($sonid);
            //累加总属性
            $son_all_epadd = Game::epadd($son_base['ep'],$son_all_epadd);

            //如果已婚 加上配偶属性
            if ($son_base['state'] == 9){

                //配偶信息
                $spson = Master::getMarryDate($son_base['spuid'],$son_base['spsonuid']);
                //累加配偶总属性
                $son_all_epadd = Game::epadd($spson['ep'],$son_all_epadd);
            }
        }

        $this->info['sonep'] = $son_all_epadd;
        $this->info['smson'] = $smson;
        $this->info['qingjia'] = $qingjia;
        $this->info['qjsonshili'] = $qjsonshili;
        */
    }
    
    private function updateBaowu(){
       
        //遍历卡牌 
        //卡牌类
        
        $BaowuModel = Master::getBaowu($this->uid);
        
        $baowu_all_epadd = array();//子嗣总属性加成
        $baowuepall = array();
        $baowuList = $BaowuModel->getBaowuList(true);
        //echo json_encode( $cardList );
        foreach($baowuList as $baowuinfo){
            
            $baowuepall = Game::epadd(Game::filterep($baowuinfo),$baowuepall);
        }
        //echo json_encode( $cardepall );
        $this->info['baowuep'] = $baowuepall;
        $this->info['baowuaddep'] = $baowuepall;
    }
    

	//光环计算
	public function hero_gh($hero,$hero_ep){
	    //pk技能配置
	    $hero_epskill_cfg = Game::getcfg('hero_pkskill');
		$pk = array();
	    if(!empty($hero['ghskill'])){
	        foreach ($hero['ghskill'] as $val){
	            $pk_cfg = $hero_epskill_cfg[$val['id']];
	            if($pk_cfg['type'] == 3){
	                foreach ($pk_cfg['epid'] as $ep){
	                    $upshux = empty($val['level']) ? 0 : $val['level']*$pk_cfg['upgrade'];//升级获得的属性
						if(isset($pk[$ep])){
							$pk[$ep] = $pk[$ep] + $pk_cfg['base'] + $upshux;
						}else{
							$pk[$ep] = $pk_cfg['base'] + $upshux;//pk[11] = 1000  pk[12]....
						}
	                }
	            }
	        }
	    }
	    if(!empty($pk)){
	        foreach ($pk as $k => $v){
	            if($k > 10){
	                $k = $k-10;
	            }
	            if($k !=5){
	                $pkzz[$k] = empty($pkzz[$k]) ? $v : $pkzz[$k]+$v;
	            }else{
	                $pkzz[1] = empty($pkzz[1]) ? $v : $pkzz[1]+$v;
	                $pkzz[2] = empty($pkzz[2]) ? $v : $pkzz[2]+$v;
	                $pkzz[3] = empty($pkzz[3]) ? $v : $pkzz[3]+$v;
	                $pkzz[4] = empty($pkzz[4]) ? $v : $pkzz[4]+$v;
	            }
	        }
	    }
	    
	    $pk_ep = array(1=>0,2=>0,3=>0,4=>0);
	    if(!empty($pkzz)){
    	    foreach ($pkzz as $k => $v){
    	        $pk_ep[$k] = floor($hero_ep[$k] * $v/10000);
    	    }
	    }
	    return $pk_ep;
	}
    //计算羁绊加成
    public function hero_jb($lv, $hero_ep){
        $jb_sys = Game::getcfg_info('jinban_lv', $lv);
        $prop = empty($jb_sys)? 0:$jb_sys['prop'];
        if (empty($jb_sys) || $prop == 0)return array(1 => 0,2 => 0,3 => 0,4 => 0);
        $ep = array(1 => 0,2 => 0,3 => 0,4 => 0);
        foreach ($hero_ep as $k => $v){
            $ep[$k] = floor($v * $prop/10000);
        }
        return $ep;
    }
	
	/*
	 * 输出英雄信息
	 * back_act1_info
	 */
	public function back_hero(){
		Master::back_data($this->uid,'hero','heroList',$this->info['heros']);
	}
	/*
	 * 输出总属性
	 * back_act1_info
	 */
	public function back_all_ep(){
		Master::back_data($this->uid,'user','ep',Game::fmt_ep($this->info['allep']));
        Master::back_data($this->uid,'user','baseep',Game::fmt_ep($this->info['baseep']));
        Master::back_data($this->uid,'user','addep',Game::fmt_ep($this->info['addep']));
        Master::back_data($this->uid,'user','sonbaseep',Game::fmt_ep($this->info['sonbaseep']));
        Master::back_data($this->uid,'user','sonaddep',Game::fmt_ep($this->info['sonaddep']));
        Master::back_data($this->uid,'user','herobaseep',Game::fmt_ep($this->info['herobaseep']));
        Master::back_data($this->uid,'user','heroaddep',Game::fmt_ep($this->info['heroaddep']));
        Master::back_data($this->uid,'user','clothebaseep',Game::fmt_ep($this->info['clothebaseep']));
        Master::back_data($this->uid,'user','clotheaddep',Game::fmt_ep($this->info['clotheaddep']));
        Master::back_data($this->uid,'user','clotheDamage',Game::fmt_ep($this->info['clothe']));
        Master::back_data($this->uid,'user','cardbaseep',Game::fmt_ep($this->info['cardep']));
        Master::back_data($this->uid,'user','cardaddep',Game::fmt_ep($this->info['cardaddep']));
        Master::back_data($this->uid,'user','baowubaseep',Game::fmt_ep($this->info['baowuep']));
        Master::back_data($this->uid,'user','baowuaddep',Game::fmt_ep($this->info['baowuaddep']));
        Master::$bak_data['a']['user']['cardHeroEp'] = $this->info['cardHeroEp'];
	}

	/**
	 * 输出历史最高属性
	 */
	public function back_all_old_ep(){
		$ep = empty($this->info['old_shili']) ? $this->info['shili'] : $this->info['old_shili'];
		Master::back_data($this->uid,'user','old_ep',array('ep' => $ep));
	}
	
	/*
	 * 获取衙门战斗属性信息
	 */
	public function get_pvp_buyid($heroid){
		$info = $this->info['heros'][$heroid];
		//生命值
		$hp = array_sum($info['aep']);//生命值 = 总属性
		$hp = Game::getCfg_formula()->gongdou_hp($hp);
		//总资质
		$all_zz = array_sum($info['zz']);
		//攻击力
		$attack = Game::getCfg_formula()->gongdou_attk($all_zz);
		//暴击
		$b_prop = 0;
		$b_hurt = 100;
		foreach ($info['pkskill'] as $v){
			if ($v['id'] == 1){
				//暴击概率 / 万分之
				$b_prop = $v['level'] * 35;
			}elseif ($v['id'] == 2){
				//暴击伤害 / 百分之
				$b_hurt += $v['level'] * 3;
			}
		}
		
		$data = array(
			//战斗属性数据
			'prop' => array(
				'hpmax' => $hp,//生命值上限
				'hp' => $hp,//生命值
				'attack' => $attack,//攻击力
				'bprob' => $b_prop,//暴击概率
				'bhurt' => $b_hurt,//暴击伤害
				'epnum' =>  count($info['epskill']),//资质技能数量
			),
			//显示信息
			'base' => array(
				'hid' => $heroid,//门客id
				'level' => $info['level'],//门客等级
				'azz' => array_sum($info['zz']),//门客综合资质
				'hpmax' => $hp,//生命值上限
				'hp' => $hp,//生命值
			),
		);
		return $data;
	}

	/*
	 * 获取门客总属性列表信息 (衙门排序专用)
	 * */
	public function get_heroallep_list(){
		if(empty($this->info['hero_allep'])){
			$this->reset();
		}
		return empty($this->info['hero_allep']) ? array() : $this->info['hero_allep'];
	}
	
	/*
	 * 检查需要刷新的英雄ID 进行刷新
	 */
	public function back_hero_info($hero_ids){
		//检查轮询要刷新的英雄ID 执行刷新
		foreach ($hero_ids as $hid => $v){
			Master::back_data($this->uid,'hero','heroList',array($this->info['heros'][$hid]),true);
		}
		
		//刷新总属性信息
		Master::back_data($this->uid,'user','ep',Game::fmt_ep($this->info['allep']));
	}
	
	/*
	 * 获取英雄伤害值(默认说去攻击属性)
	 */
	public function getHeroDamage($hero_id, $type = 1){
        $c = $this->info['herocount'];
        $clothe =  $this->info['clothe'];
        $sonep = $this->info['sonep'];
        $hero_info = $this->info['heros'][$hero_id];
        $extraCount = 0;
        if($type == 1){
            //服装升级增加的属性
            $Act756Model = Master::getAct756($this->uid);
            $extraCount = $Act756Model->getPropCount(5);
        }
        return floor($hero_info['aep']['e'.$type] + $extraCount) + ceil(($clothe[$type] + $sonep[$type])/ $c);
	}
	
	/*
	 * 清除阵法缓存 重新生成
	 */
	public function reset($type = 0)
	{
		//保存旧数据
		$old_team = $this->info;
		
		//重新生成阵法数据
		$this->__construct($this->uid,true, $type);
		
		if (empty($old_team)){
			//如果原数据没有 
			//略过差值记录?
		}
		
		//判断势力是否达到某一上限
	    $Act91Model = Master::getAct91($this->uid);
	    $Act91Model->addwife($this->info['shili']);
	    
		//势力改变

        $SevidCfg = Common::getSevidCfg();
        $serverId = $SevidCfg ['he'];
        $uidServerId = Game::get_sevid($this->uid);
        if ($serverId != $uidServerId){
            $filename = "shili_Redis_log";
            $content = 'http://'.$_SERVER ['HTTP_HOST'].$_SERVER['REQUEST_URI'].' | serverid:'.$serverId.' uidServerId:'.$uidServerId.'|'.date("Y-m-d H:i:s")."\r\n";
            $content.= file_get_contents("php://input")."\r\n";
            Common::log($filename, $content);
        }

		$Redis1Model = Master::getRedis1();
		$Redis1Model->zAdd($this->uid,$this->info['shili']);
        Common::loadModel('SwitchModel');
        if (SwitchModel::isKuaRankOpen()) {
            $selfRid = $Redis1Model->get_rank_id($this->uid);
            if ($selfRid >= 1 && $selfRid <= 100) {
                //跨服势力榜
                $Redis301Model = Master::getRedis301();
                $Redis301Model->zAdd($this->uid, $this->info['shili']);
            }
        }
		
		//活动消耗 - 势力涨幅
		$HuodongModel = Master::getHuodong($this->uid);
		$HuodongModel->xianshi_huodong('huodong206',$old_team['shili']);
		
		//活动消耗 - 势力冲榜
		$HuodongModel = Master::getHuodong($this->uid);
		$HuodongModel->chongbang_huodong('huodong252',$this->uid,$old_team['shili']);

        //活动消耗 - 跨服势力冲榜
        $HuodongModel = Master::getHuodong($this->uid);
        $HuodongModel->chongbang_huodong('huodong313',$this->uid,$this->info['shili'] - $old_team['shili']);

        //宫殿势力涨幅冲榜
        $HuodongModel = Master::getHuodong($this->uid);
        $HuodongModel->chongbang_huodong('huodong310',$this->uid,$this->info['shili'] - $old_team['shili']);

		//好感改变值
		//$d_love = $this->info['alllove'] - $old_team['alllove'];
			
		//原有亲密排行-改为伙伴羁绊值排行
		$Redis3Model = Master::getRedis3();
		$Redis3Model->zAdd($this->uid,$this->info['fetter']);
		
		//活动消耗 - 临时好感度涨幅
		$HuodongModel = Master::getHuodong($this->uid);
		$HuodongModel->xianshi_huodong('huodong205',$old_team['alllove']);
		
		//活动消耗 - 好感度冲榜
		$HuodongModel = Master::getHuodong($this->uid);
		$HuodongModel->chongbang_huodong('huodong253',$this->uid,$old_team['alllove']);

        //活动消耗 - 跨服好感冲榜
        $HuodongModel = Master::getHuodong($this->uid);
        $HuodongModel->chongbang_huodong('huodong314',$this->uid,$this->info['alllove'] - $old_team['alllove']);

        //伙伴资质涨幅冲榜
        $HuodongModel = Master::getHuodong($this->uid);
        $HuodongModel->chongbang_huodong('huodong6167',$this->uid,$this->info['herozz'] - $old_team['herozz']);

        //徒弟势力冲榜
        $HuodongModel = Master::getHuodong($this->uid);
        $HuodongModel->chongbang_huodong('huodong6218',$this->uid,array_sum($this->info['sonep']) - array_sum($old_team['sonep']));

		//刷新总属性
		$this->back_all_ep();
	}
	
}
