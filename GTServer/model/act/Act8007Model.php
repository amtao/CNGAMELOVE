<?php
require_once "ActBaseModel.php";
require_once "ActHDBaseModel.php";
/*
 * 活动8007
 */
class Act8007Model extends ActHDBaseModel
{
    public $atype = 8007;//活动编号
    public $comment = "珍绣坊";
    public $b_mol = "zhenxiufang";//返回信息 所在模块
    public $b_ctrl = "zhenxiushizhuang";//子类配置
    public $hd_id = 'huodong_8007';//活动配置文件关键字

    /*
     * 初始化结构体
     * 累计数量
     * 领奖档次
     */
    public $_init =  array(
        'refresh'      => 0,  //刷新时间
        'rNum'       => 0,  //刷新次数
        'sale'      => array(),  //折扣
    );

    /*
     * 构造输出结构体
     */
    public function data_out($isRefresh){

        $hd_cfg = $this->hd_cfg;

        // 判断是否需要刷新
        $hd_cfg["shopList"] = $this->refreshItem($isRefresh);
        $hd_cfg["bagList"] = $this->getBagList();

        $endtime = strtotime( date('Y-m-d 23:59:59', $_SERVER['REQUEST_TIME']));
        foreach ($this->info["sale"] as $k => $v) {
            if ( $v <= 5 ) {
                $endtime = $this->info["refresh"] + 3600;
                break;
            }
        }
        $hd_cfg["refresh"] = $endtime - $_SERVER['REQUEST_TIME'];
        $hd_cfg["rNum"] = $this->info["rNum"];
        $hd_cfg["sale"] = $this->info["sale"];

        $this->outf = $hd_cfg;
        Master::back_data(0,$this->b_mol,$this->b_ctrl,$this->outf);
    }

    public function back_data_hd($isRefresh = false){
        self::data_out($isRefresh);
    }

    public function refreshItem($isRefresh){

        $clotheshop = Game::getCfg('clotheshop');
        $refresh = false;
        // 线判断是否隔天刷新
        if ($this->info["refresh"] < strtotime( date('Y-m-d 00:00:00', $_SERVER['REQUEST_TIME'])) ) {

            $refresh = true;
            $this->info["rNum"] = 0;
        }

        // 判断是否打折时间到刷新
        if ( !empty($this->info["sale"]) ) {

            foreach ($this->info["sale"] as $k => $v) {
                if ($v <= 5 && ($this->info["refresh"] + 3600) < $_SERVER['REQUEST_TIME'] ) {
                    $refresh = true;
                    break;
                }
            }
        }

        // 花钱强制刷新
        if ($isRefresh) {

            $clotheInfo = array();
            foreach ($clotheshop as $k => $v) {
                $clotheInfo = $v;
                break;
            }

            if ( $this->info["rNum"] >= $clotheInfo["re_time"] ) {
                Master::error(JIULOU_FRESH_COUNT_LIMIT);
            }

            $countList = explode("|",$clotheInfo['count']);
            $itemNum = $countList[$this->info["rNum"]];
            if ($itemNum > 0) {
                Master::sub_item($this->uid,KIND_ITEM,$clotheInfo['item'], $itemNum);
            }

            $this->info["rNum"]++;
            $refresh = true;
        }

        // 是否需要刷新
        if($refresh) {

            $discount = Game::getCfg('discount');
            $this->info["refresh"] = $_SERVER['REQUEST_TIME'];
            $this->info["sale"] = array();

            for ($i=0; $i < 6; $i++) {

                $probMax = 0;
                $newClothesShop = array();
                foreach ($clotheshop as $k => $v) {

                    if (isset($this->info["sale"][$k])) {
                        continue;
                    }
                    $newClothesShop[$k] = $v;
                    $probMax += $v["rate"];
                }
                $rid =  Game::get_rand_key($probMax,$newClothesShop,'rate');
                $itemInfo = $newClothesShop[$rid];
                foreach ($discount as $k => $v) {
                    if ( $itemInfo["quality"] == $v["quality"] && $itemInfo["part"] == $v["part"] ) {

                        $discountList = $v["dicount"];
                        if ( strtotime('+'.$itemInfo["duration"].' day', $itemInfo["sell_time"]) >= $_SERVER['REQUEST_TIME'] ) {
                            $discountList = $v["dicount_new"];
                        }

                        $this->info["sale"][$rid] = Game::array_rand($discountList, 1)[0];
                    }
                }
            }
            $this->save();
        }

        // 用户时装
        $Act6140Model = Master::getAct6140($this->uid);
        $clothes = $Act6140Model->info['clothes'];

        // 返回数据
        $clotheList = array();
        foreach ($clotheshop as $k => $v) {

            if ( strtotime( $v["sell_time"] ) > $_SERVER['REQUEST_TIME'] ) {
                continue;
            }

            $clotheList[$k]["is_new"] = 0;
            $clotheList[$k]["is_have"] = 0;
            $clotheList[$k]["sale"] = 10;

            if ( strtotime('+'.$v["duration"].' day', $v["sell_time"]) >= $_SERVER['REQUEST_TIME'] ) {
                $clotheList[$k]["is_new"] = 1;
            }

            if (in_array($v["clothe_id"],$clothes)){
                $clotheList[$k]["is_have"] = 1;
            }

            if ( isset($this->info["sale"][$k]) ) {
                $clotheList[$k]["sale"] = $this->info["sale"][$k];
            }
        }
        return $clotheList;
    }

    public function getBagList(){

        $giftBag = Game::getGiftBagCfg();
        $bagList = array();
        foreach ($giftBag as $k => $v) {
            if ($v["actid"] == $this->atype) {
                $bagList[$k] = $v;
            }
        }
        return $bagList;
    }

    /**
     * 兑换
     * $id
     */
    public function exchange($ids = 0){
        if( $this->get_state() == 0){
            Master::error(ACTHD_OVERDUE);
        }

        // 用户时装
        $Act6140Model = Master::getAct6140($this->uid);
        $clothes = $Act6140Model->info['clothes'];
        $clotheshop = Game::getCfg('clotheshop');
        $Act68Model = Master::getAct68($this->uid);

        $price = 0;
        $items = array();
        foreach ($ids as $k => $id) {

            $clotheInfo = $clotheshop[$id];
            if (empty($clotheshop)) {
                Master::error(PARAMS_ERROR);
            }

            if (in_array($clotheInfo["clothe_id"],$clothes)){
                // Master::error(USER_CLOTHE_DUPLICATE);
                return;
            }

            $sale = 1;
            $cardSale = 1;
            if ( isset($this->info["sale"][$id]) ) {
                $sale = $this->info["sale"][$id] / 10;
            }

            if ( isset($Act68Model->info["1"]) ) {
                $cardSale = $clotheInfo["monthcard"] / 10;
            }

            $price += $clotheInfo["price"] * $sale * $cardSale;
            $items[] = array('id' => $clotheInfo["clothe_id"],'kind' => 95,'count' => 1);
        }

        Master::sub_item($this->uid, KIND_ITEM, 6, intval($price));
        Master::add_item3($items);

        self::data_out(false);
    }

    /**
     * 支持活动配置直购礼包
     * @param  [type] $id      [description]
     * @param  [type] $zc_item [description]
     * @return [type]          [description]
     */
    public function exchangeItem($id, $zc_item){

        $this->info["itemInfo"] = $zc_item['items'][0];
        $this->save();
    }

}

