<?php
/**
 * Class AdminModel
 * author:luffy
 * date:2017/11/1
 */
class AdminModel{
    /**
     * @param $admin   操作玩家
     * @param $model   操作的模块
     * @param $control 操作的控制器
     * @param $data    操作的数据
     */
    public static function admin_log($admin, $model, $control, $data){
        $table = 'admin_log';
        $time = time();
        $data = addslashes(json_encode($data));
        $sql = 'INSERT INTO '.$table." (`admin`,`model`,`control`,`data`,`time`) VALUES ('".$admin."','". $model."','".$control."','".$data."',".$time.')';
        $db = Common::getComDb('flow');
        $result = $db->query($sql);
    }
    /**
     * @param $platformList
     * @param $platformClassify
     * @param array $channels
     */
    public static function wrap($platformList, $platformClassify, $channels = array()){
        $tmp = 0;
        $you = 0;
        $ios = 0;
        $meimei = 0;
        $sulu = 0;
        $wumin = 0;
        $cps = 0;
        $checkboxHtml = '';
        $youdongHtml = '';
        $iosdongHtml = '';
        $meimeiHtml = '';
        $suluHtml = '';
        $wuminHtml = '';
        $cpsHtml = '';
        $youdongPlat = include (ROOT_DIR . '/administrator/config/youdong.php');
        if(!empty($platformList)){
            foreach ($platformList as $k => $v) {
                $isChecked = in_array($k, $channels) ? 'checked' : '';
                if (!in_array($k,$youdongPlat['android']) && !in_array($k,$youdongPlat['ios'])){
                    if ($platformClassify[$k] != 'ios'){
                        $platform_ios = "";
                    }else{
                        $platform_ios = "data-platform='ios'";
                    }
                    $tmp++;
                    $brSting = ($tmp%5) ? '' : '<br/>';
                    $checkboxHtml .= sprintf("<input type='checkbox' name='channels[]' value='%s' %s %s />%s&nbsp;%s" . PHP_EOL, $k, $isChecked, $platform_ios , $v, $brSting);
                }elseif(in_array($k,$youdongPlat['android'])){
                    if ($platformClassify[$k] != 'ios'){
                        $platform_ios = "";
                    }else{
                        $platform_ios = "data-platform='ios'";
                    }
                    $you++;
                    $youSting = ($you%5) ? '' : '<br/>';
                    $youdongHtml .= sprintf("<input type='checkbox' class='youdong' name='channels[]' value='%s' %s %s />%s&nbsp;%s" . PHP_EOL, $k, $isChecked, $platform_ios , $v, $youSting);
                }elseif(in_array($k,$youdongPlat['ios'])){
                    if ($platformClassify[$k] != 'ios'){
                        $platform_ios = "";
                    }else{
                        $platform_ios = "data-platform='ios'";
                    }
                    $ios++;
                    $iosSting = ($ios%5) ? '' : '<br/>';
                    $iosdongHtml .= sprintf("<input type='checkbox' class='soeasy' name='channels[]' value='%s' %s %s />%s&nbsp;%s" . PHP_EOL, $k, $isChecked, $platform_ios , $v, $iosSting);
                }

//                if(in_array($k,$youdongPlat['wumin'])){
//                    if ($platformClassify[$k] != 'ios'){
//                        $platform_ios = "";
//                    }else{
//                        $platform_ios = "data-platform='ios'";
//                    }
//                    $wumin++;
//                    $wuminSting = ($wumin%5) ? '' : '<br/>';
//                    $wuminHtml .= sprintf("<input type='checkbox' class='wumin' name='channels[]' value='%s' %s %s />%s&nbsp;%s" . PHP_EOL, $k, $isChecked,$platform_ios , $v, $wuminSting);
//                }
//                if(in_array($k,$youdongPlat['meimei'])){
//                    if ($platformClassify[$k] != 'ios'){
//                        $platform_ios = "";
//                    }else{
//                        $platform_ios = "data-platform='ios'";
//                    }
//                    $meimei++;
//                    $meimeiSting = ($meimei%5) ? '' : '<br/>';
//                    $meimeiHtml .= sprintf("<input type='checkbox' class='meimei' name='channels[]' value='%s' %s %s />%s&nbsp;%s" . PHP_EOL, $k, $isChecked,$platform_ios , $v, $meimeiSting);
//                }
//                if(in_array($k,$youdongPlat['sulu'])){
//                    if ($platformClassify[$k] != 'ios'){
//                        $platform_ios = "";
//                    }else{
//                        $platform_ios = "data-platform='ios'";
//                    }
//                    $sulu++;
//                    $suluSting = ($sulu%5) ? '' : '<br/>';
//                    $suluHtml .= sprintf("<input type='checkbox' class='sulu' name='channels[]' value='%s' %s %s />%s&nbsp;%s" . PHP_EOL, $k, $isChecked,$platform_ios , $v, $suluSting);
//                }
//                if(in_array($k,$youdongPlat['cps'])){
//                    if ($platformClassify[$k] != 'ios'){
//                        $platform_ios = "";
//                    }else{
//                        $platform_ios = "data-platform='ios'";
//                    }
//                    $cps++;
//                    $cpsSting = ($cps%5) ? '' : '<br/>';
//                    $cpsHtml .= sprintf("<input type='checkbox' class='cps' name='channels[]' value='%s' %s %s />%s&nbsp;%s" . PHP_EOL, $k, $isChecked,$platform_ios , $v, $cpsSting);
//                }
            }
        }
        echo $checkboxHtml;
        if ($suluHtml != ''){
            echo '<hr class="hr"/>';
            echo $suluHtml;
        }
        if ($meimeiHtml != ''){
            echo '<hr class="hr"/>';
            echo $meimeiHtml;
        }
        if ($youdongHtml != ''){
            echo '<hr class="hr"/>';
            echo $youdongHtml;
        }

        if ($iosdongHtml != ''){
            echo '<hr class="hr"/>';
            echo $iosdongHtml;
        }
        if ($wuminHtml != ''){
            echo '<hr class="hr"/>';
            echo $wuminHtml;
        }
        if ($cpsHtml != ''){
            echo <<<STR
            <hr class="hr"/>;
            {$cpsHtml}
STR;


        }
    }
}