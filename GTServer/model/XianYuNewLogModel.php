<?php

/**
 * 咸鱼打点上报日志
 */

class XianYuNewLogModel{
    const URL = "http://106.53.187.24:8082/gameDotOperationApi/";
    //1 国内 2 港澳台 3 泰国 4 韩国 5 英文 6 越南 7 日本
    const COUNTRY = 5;
    const GAMENAME = "花落长安";

    public static function sendCurl($url, $data){

        $urlinfo = parse_url($url);
        $host = $urlinfo['host'];
        $port = $urlinfo['port'];
        $path = $urlinfo['path'];
        $query = isset($data)? http_build_query($data) : '';
        // echo json_encode($urlinfo)."<br /><br />";
        // echo $query."<br /><br />";

        $errno = 0;
        $errstr = '';
        $fp = fsockopen($host, $port, $errno, $errstr, 1);

        $out = "POST ".$path." HTTP/1.1\r\n";
        $out .= "host:".$host."\r\n"; 
        $out .= "content-length:".strlen($query)."\r\n";
        $out .= "content-type:application/x-www-form-urlencoded\r\n";
        $out .= "connection:close\r\n\r\n";
        $out .= $query;
        // echo $out."<br /><br />";
        if (!$fp) {

            $logfile = "/data/logs/con/xianyuNewLog_".date('ymdH',time()).".log";
            if ( false == Common::createFolders(dirname($logfile)) ) {
                return ;
            }

            $res = array(
                "host" => $host,
                "port" => $port,
                "out" => $out
            );
            file_put_contents($logfile, json_encode($res)."==", FILE_APPEND);

            return "fail";
        }

        fputs($fp, $out);
        // $result = "";
        // //获取返回结果, 如果不循环接收返回值,请求发出后直接关闭连接, 则为异步请求
        // while(!feof($fp)) {
        //     $result .= fgets($fp, 1024);
        // }
        // print_r($result);
        fclose($fp);
        return 'ok';
    }

    public static function getDate()
    {

        if ("hlca-gs-ab.tomatogames.com" == $_SERVER ['HTTP_HOST']) {
            return date('Y-m-d H:i:s', $_SERVER['REQUEST_TIME']);
        }

        return date('2026-m-d H:i:s', $_SERVER['REQUEST_TIME']);
    }

    /**
     * 新增游戏角色注册打点信息
     * @param  [type] $uid      [角色ID]
     * @param  [type] $name     [角色昵称]
     * @param  [type] $platform     [渠道编码]
     * @return [type]           [返回值]
     */
    public static function InsertGameCharacterDataDot($uid, $name, $platform)
    {
        $serverID = Game::get_sevid($uid);
        $data = array(
            'playerId' => $uid, //游戏角色ID
            'playerNickname' => $name, //游戏角色昵称
            'registerDate' => self::getDate(), //注册时间
            'TheServer' => "{$serverID}", //玩家所在服务器编码
            'Country' => self::COUNTRY, //创角时间
            'GameName' => self::GAMENAME, //游戏项目
            'trenchId_cp' => $platform, //渠道编码
        );

        $url = self::URL."InsertGameCharacterDataDot";
        return self::sendCurl($url, $data);
    }

    /**
     * 新增游戏角色升级打点信息
     * @param  [type] $uid      [角色ID]
     * @param  [type] $level     [游戏角色等级]
     * @return [type]           [返回值]
     */
    public static function InsertGameCharacterlevelDot($uid, $level)
    {
        $data = array(
            'playerId' => $uid, //游戏角色ID
            'playerRank' => $level, //游戏角色等级
            'registUpDateerDate' => self::getDate(), //升级时间
            'Country' => self::COUNTRY, //创角时间
        );

        $url = self::URL."InsertGameCharacterlevelDot";
        return self::sendCurl($url, $data);
    }

    /**
     * 新增游戏角色的游戏币打点信息
     * @param  [type] $uid      [角色ID]
     * @param  [type] $name     [货币名称]
     * @param  [type] $num     [货币数量]
     * @param  [type] $Type     [获取类型：充值获取：1，充值赠送：2，游戏内获取：3]
     * @param  [type] $dataType     [数据类型：1：货币获取 2：货币消耗]
     * @param  [type] $playerRank     [游戏角色等级]
     * @return [type]           [返回值]
     */
    public static function InsertGameCharacterCurrencyDot($uid, $name, $num, $Type, $dataType, $playerRank)
    {
        $data = array(
            'playerId' => $uid, //游戏角色ID
            'CurrencyName' => $name, //货币名称
            'CurrencyNumber' => $num, //货币数量
            'Type' => $Type, //获取类型：充值获取：1，充值赠送：2，游戏内获取：3
            'dataType' => $dataType, //数据类型：1：货币获取 2：货币消耗
            'playerRank' => $playerRank, //数据类型：1：货币获取 2：货币消耗
            'Date' => self::getDate(), //日期
            'Country' => self::COUNTRY, //创角时间
        );

        $url = self::URL."InsertGameCharacterCurrencyDot";
        return self::sendCurl($url, $data);
    }

    /**
     * 新增游戏中道具的产生和消耗记录打点信息
     * @param  [type] $uid      [角色ID]
     * @param  [type] $propId     [道具编码]
     * @param  [type] $propName     [道具名称]
     * @param  [type] $propCount     [道具数量]
     * @param  [type] $propMoney     [道具金额]
     * @param  [type] $propType     [道具类型 获取：1 消耗：2]
     * @param  [type] $currencyName     [货币名称]
     * @return [type]           [返回值]
     */
    public static function InsertGameCharacterPropDot($uid, $propId, $propName, $propCount, $propMoney, $currencyName, $propType)
    {
        $data = array(
            'playerId' => $uid, //游戏角色ID
            'PropId' => $propId, //道具编码
            'PropCount' => $propCount, //道具数量
            'PropName' => $propName, //道具名称
            'PropMoney' => $propMoney, //道具金额
            'CurrencyName' => $currencyName, //货币名称
            'PropType' => $propType, //道具类型 获取：1 消耗：2
            'PurchaseDate' => self::getDate(), //购买时间
            'Country' => self::COUNTRY, //国家
        );

        $url = self::URL."InsertGameCharacterPropDot";
        return self::sendCurl($url, $data);
    }

    /**
     * 游戏角色上下线时间打点信息
     * @param  [type] $uid      [角色ID]
     * @param  [type] $type     [1：上线 2：下线]
     * @return [type]           [返回值]
     */
    public static function InsertGameCharacterLineDateDot($uid, $type)
    {
        $data = array(
            'playerId' => $uid, //游戏角色ID
            'Type' => $type, //1：上线 2：下线
            'Date' => self::getDate(), //上线时间
            'Country' => self::COUNTRY, //国家
            'GameName' => self::GAMENAME, //游戏项目
        );

        $url = self::URL."InsertGameCharacterLineDateDot";
        return self::sendCurl($url, $data);
    }
}