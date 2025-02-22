<?php include(TPL_DIR.'header.php');?>
<?php $_SESSION['zi_page'] = 9;?>
<?php include(TPL_DIR.'zi_header.php');?>
<?php
$userchange = array(
    1 => array('title' =>  '用 户', 'src' => "?sevid=".$SevidCfg['sevid']."&mod=user&act=userChange&uid=".$uid."&cur=userChange", 'ban' => 'userChange'),
    2 => array('title' =>  '道 具', 'src' => "?sevid=".$SevidCfg['sevid']."&mod=user&act=userItem&uid=".$uid."&cur=userChange", 'ban' => 'userItem'),
    3 => array('title' =>  '活 动', 'src' => "?sevid=".$SevidCfg['sevid']."&mod=user&act=activitys&uid=".$uid."&cur=userChange", 'ban' => 'activitys'),
    4 => array('title' =>  '服 装', 'src' => "?sevid=".$SevidCfg['sevid']."&mod=user&act=clothes&uid=".$uid."&cur=userChange", 'ban' => 'clothes'),
    5 => array('title' =>  '伙 伴', 'src' => "?sevid=".$SevidCfg['sevid']."&mod=user&act=hero&uid=".$uid."&cur=userChange", 'ban' => 'hero'),
    6 => array('title' =>  '信 物', 'src' => "?sevid=".$SevidCfg['sevid']."&mod=user&act=herotokens&uid=".$uid."&cur=userChange", 'ban' => 'herotokens'),
    7 => array('title' =>  '知 己', 'src' => "?sevid=".$SevidCfg['sevid']."&mod=user&act=wife&uid=".$uid."&cur=userChange", 'ban' => 'wife'),
    8 => array('title' =>  '徒 弟', 'src' => "?sevid=".$SevidCfg['sevid']."&mod=user&act=son&uid=".$uid."&cur=userChange", 'ban' => 'son'),
    9 => array('title' =>  '卡 牌', 'src' => "?sevid=".$SevidCfg['sevid']."&mod=user&act=userCard&uid=".$uid."&cur=userChange", 'ban' => 'userCard'),
    10 => array('title' =>  '四海奇珍', 'src' => "?sevid=".$SevidCfg['sevid']."&mod=user&act=userBaowu&uid=".$uid."&cur=userChange", 'ban' => 'userBaowu'),
    11 => array('title' =>  '流 水', 'src' => "?sevid=".$SevidCfg['sevid']."&mod=user&act=flow&uid=".$uid."&cur=userChange", 'ban' => 'flow'),
    12 => array('title' =>  '邮 件', 'src' => "?sevid=".$SevidCfg['sevid']."&mod=user&act=mail&uid=".$uid."&cur=userChange", 'ban' => 'mail'),
    13 => array('title' =>  '聊 天', 'src' => "?sevid=".$SevidCfg['sevid']."&mod=user&act=chat&uid=".$uid."&cur=userChange", 'ban' => 'chat'),
    14 => array('title' =>  '宫殿', 'src' => "?sevid=".$SevidCfg['sevid']."&mod=user&act=club&uid=".$uid."&cur=userChange", 'ban' => 'club'),
    15 => array('title' =>  '流 水(后端)', 'src' => "?sevid=".$SevidCfg['sevid']."&mod=user&act=flowAdmin&uid=".$uid."&cur=userChange", 'ban' => 'flowAdmin'),
    16 => array('title' =>  'IP', 'src' => "?sevid=".$SevidCfg['sevid']."&mod=user&act=getip&uid=".$uid."&cur=userChange", 'ban' => 'getip'),

);
?>
<hr class="hr"/>
<table style="width: 100%" class="mytable">
    <tr><th colspan="6">账号添加</th></tr>
    <tr>
        <th style="text-align:right;width: 200px;">账号</th>
        <td style="text-align:left;">
            <select name="userAccount">
                <?php foreach ($userAccount as $uk => $uv):?>
                    <option value="<?php echo $uk;?>"><?php echo $uv['name'];?></option>
                <?php endforeach;?>
            </select>
        </td>
    </tr>
    <tr><th colspan="6"><input type="submit" value="确定提交" /></th></tr>
</table>
<hr class="hr"/>
<table style='width:100%;line-height: 30px;' class="mytable">
    <tr>
        <th style="width: 60px;">后台账户</th>
        <th style="width: 60px;">名称</th>
        <th style="width: 900px;" >权限</th>
        <th style="width: 300px;" >渠道</th>
    </tr>

    <?php
    if ( is_array($authConfig) ) {

        foreach ($authConfig as $ak => $av) {
            ?>
            <tr>
                <td style="text-align: center"><?php echo '&nbsp;&nbsp;<span data-account="'.$av['user'].'" data-auth="account" style="border: 1px solid rosybrown;cursor:pointer;padding: 2px;margin: 1px;border-radius:0.2em;background-color: #ffdec2">'.$av['user'].'</span>';?></td>
                <td style="text-align: center"><?php echo $av['name'];?></td>
                <td style="text-align: left;padding: 10px;">
                    <?php foreach ($links as $k => $v) {
                            if (!empty($av['ml'][$k])){
                                $color = "#ffdec2;";
                            }else{
                                $color = "#fff;";
                            }
                            echo '&nbsp;&nbsp;<span  style="border: 1px solid rosybrown;cursor:pointer;padding: 2px;margin: 1px;border-radius:0.2em;background-color: '.$color.'">'.$v['title'].'</span>';
                            $i = 0;
                            foreach ($zi_links[$k] as $key => $value){

                                if (!empty($av["power"]['ml'][$k]) && in_array($key ,$av["power"]['ml'][$k])){
                                    $colors = "#f5d6ca;";
                                }else{
                                    $colors = "#fff;";
                                }
                                if (!empty($value['title'])){
                                    echo '&nbsp;&nbsp;<span data-auth="auth" data-account="'.$av['user'].'" data-key="'.$k.'-'.$key.'" data-title="'.$value['title'].'"style="border: 1px solid rosybrown;cursor:pointer;border-radius:0.2em;padding: 2px;margin: 1px;background-color: '.$colors.'">'.$value['title'].'</span>';
                                }
                                $i++;
                                if ($i>10){
                                    echo '<br/>';
                                    $i = 0;
                                }
                            }
                            echo '<hr style="color: #f5f0d4;margin: 5px;"/>';
                        }
                        foreach ($userchange as $uk => $uv){
                            if (empty($av['ban']['user'][$uv['ban']])){
                                $colores = "#efbca8;";
                            }else{
                                $colores = "#fff;";
                            }
                            if (!empty($uv['title'])){
                                echo '&nbsp;&nbsp;<span data-auth="ban" data-account="'.$av['user'].'" data-title="'.$uv['title'].'"  data-ban="'.$uv['ban'].'" style="border: 1px solid rosybrown;cursor:pointer;border-radius:0.2em;padding: 2px;margin: 1px;background-color: '.$colores.'">'.$uv['title'].'</span>';
                            }
                        }
                    ?>
                </td>
                <td style="text-align: left;">
                    <?php
                    if(!empty($av['qd'])){
                        echo '&nbsp;&nbsp; sdk :';
                        foreach ($av['qd']['sdk'] as $sdk){
                            echo '&nbsp;<span data-auth="sdk" data-account="'.$av['user'].'" data-sdk="'.$sdk.'" style="border: 1px solid rosybrown;cursor:pointer;border-radius:0.2em;padding: 2px;margin: 1px;background-color: #efbca8;">'.$sdk.'</span>  &nbsp;';
                        }
                        echo '<hr style="color: #f5f0d4;margin: 5px;"/>';
                        foreach ($av['qd']['pt'] as $pt){
                            echo '&nbsp;&nbsp;<span data-auth="pt" data-account="'.$av['user'].'" data-key="'.$pt.'" style="border: 1px solid rosybrown;cursor:pointer;border-radius:0.2em;padding: 2px;margin: 1px;background-color: #efbca8;">'.$platformList[$pt].'</span><br/>';
                        }
                    }
                    ?>
                    <hr style="color: #f5f0d4;margin: 5px;"/>
                    <select name="platform" data-accounts="<?php echo $av['user'];?>" style="width: 200px;">
                        <?php foreach ($platformList as $ptk => $ptv):?>
                            <?php if(empty($av['qd']['pt']) || !in_array($ptk, $av['qd']['pt'])):?>
                            <option value="<?php echo $ptk;?>"><?php echo $ptv;?></option>
                            <?php endif;?>
                        <?php endforeach;?>
                    </select>
                    <input type="button" data-account="<?php echo $av['user'];?>" data-btn="platform" value="提交">
                </td>
            </tr>
            <?php
        }
    }
    ?>

</table>
<?php include(TPL_DIR.'footer.php');?>
<script>
    $(document).ready(function(){
        $(':input[type="submit"]').on('click', function (e) {
            e.preventDefault();
            var account = $('[name="userAccount"]').val();
            var url = '?sevid=<?php echo $SevidCfg['sevid'];?>&mod=auth&act=addAuthConfig';
            if (account == ''){
                layer.tips('账号不为空',$('[name="userAccount"]'), {tips: [1, '#0FA6D8']});
                return false;
            }
            $.ajax({
                type: "POST",
                url: url,
                data: "account=" + account,
                success: function(msg){
                    layer.msg(msg, {icon: 1});
                    setTimeout(function(){
                        document.location.reload();//页面刷新
                    } ,300);
                }
            });
        });
        $('[data-auth="account"]').on('click', function (e) {
            e.preventDefault();
            var url = '?sevid=<?php echo $SevidCfg['sevid'];?>&mod=auth&act=deleteAccount';
            var account = $(this).data('account');
            layer.confirm('确认删除 <span style="color: red;font-size: 12px;">'+account+'</span>?' , {
                btn: ['确定','取消'] //按钮
            }, function(){
                $.ajax({
                    type: "POST",
                    url: url,
                    data: "account="+account,
                    success: function(msg){
                        layer.msg(msg, {icon: 1});
                        setTimeout(function(){
                            document.location.reload();//页面刷新
                        } ,300);
                    }
                });
            }, function(){
                layer.msg('已取消,慎重点好...', {icon: 2});
                return false;
            });
        });
        $('[data-auth="ban"]').on('click', function (e) {
            e.preventDefault();
            var url = '?sevid=<?php echo $SevidCfg['sevid'];?>&mod=auth&act=changeBan';
            var ban = $(this).data('ban');
            var account = $(this).data('account');
            var title = $(this).data('title');
            layer.confirm('确认变更 <span style="color: red;font-size: 12px;">'+title+'</span>?' , {
                btn: ['确定','取消'] //按钮
            }, function(){
                $.ajax({
                    type: "POST",
                    url: url,
                    data: "ban="+ban+"&account="+account,
                    success: function(msg){
                        layer.msg(msg, {icon: 1});
                        setTimeout(function(){
                            document.location.reload();//页面刷新
                        } ,300);
                    }
                });
            }, function(){
                layer.msg('已取消,慎重点好...', {icon: 2});
                return false;
            });
        });
        $('[data-auth="sdk"]').on('click', function (e) {
            e.preventDefault();
            var url = '?sevid=<?php echo $SevidCfg['sevid'];?>&mod=auth&act=removeSdk';
            var sdk = $(this).data('sdk');
            var account = $(this).data('account');
            layer.confirm('确认删除 <span style="color: red;font-size: 12px;">'+sdk+'</span>?' , {
                btn: ['确定','取消'] //按钮
            }, function(){
                $.ajax({
                    type: "POST",
                    url: url,
                    data: "sdk="+sdk+"&account="+account,
                    success: function(msg){
                        layer.msg(msg, {icon: 1});
                        setTimeout(function(){
                            document.location.reload();//页面刷新
                        } ,300);
                    }
                });
            }, function(){
                layer.msg('已取消,慎重点好...', {icon: 2});
                return false;
            });
        });
        $('[data-auth="pt"]').on('click', function (e) {
            e.preventDefault();
            var url = '?sevid=<?php echo $SevidCfg['sevid'];?>&mod=auth&act=addPt';
            var platform = $(this).data('key');
            var account = $(this).data('account');

            layer.confirm('确认删除 <span style="color: red;font-size: 12px;">'+platform+'</span>?' , {
                btn: ['确定','取消'] //按钮
            }, function(){
                $.ajax({
                    type: "POST",
                    url: url,
                    data: "platform="+platform+"&account="+account,
                    success: function(msg){
                        layer.msg(msg, {icon: 1});
                        setTimeout(function(){
                            document.location.reload();//页面刷新
                        } ,300);
                    }
                });
            }, function(){
                layer.msg('已取消,慎重点好...', {icon: 2});
                return false;
            });
        });
        $('[data-btn="platform"]').on('click', function (e) {
            e.preventDefault();
            var url = '?sevid=<?php echo $SevidCfg['sevid'];?>&mod=auth&act=addPt';

            var account = $(this).data('account');
            var platform = $('[data-accounts="'+account +'"]').val();
            layer.confirm('确认提交 <span style="color: red;font-size: 12px;">'+platform+'</span>?' , {
                btn: ['确定','取消'] //按钮
            }, function(){
                $.ajax({
                    type: "POST",
                    url: url,
                    data: "platform="+platform+"&account="+account,
                    success: function(msg){
                        layer.msg(msg, {icon: 1});
                        setTimeout(function(){
                            document.location.reload();//页面刷新
                        } ,300);
                    }
                });
            }, function(){
                layer.msg('已取消,慎重点好...', {icon: 2});
                return false;
            });
        });
        $("[data-auth='auth']").on('click', function (e) {
            e.preventDefault();
            var url = '?sevid=<?php echo $SevidCfg['sevid'];?>&mod=auth&act=changeAuth';
            var k = $(this).data('key');
            var title = $(this).data('title');
            var account = $(this).data('account');
            layer.confirm('确认变更  <span style="color: red;font-size: 12px;">'+title+'</span>?' , {
                btn: ['确定','取消'] //按钮
            }, function(){
                $.ajax({
                    type: "POST",
                    url: url,
                    data: "key="+k+"&account="+account,
                    success: function(msg){
                        layer.msg(msg, {icon: 1});
                        setTimeout(function(){
                            document.location.reload();//页面刷新
                        } ,300);
                    }
                });
            }, function(){
                layer.msg('已取消,慎重点好...', {icon: 2});
                return false;
            });
        });
    });
</script>
