<?php include(TPL_DIR.'header.php');?>
<?php $_SESSION['zi_page'] = 5;?>
<?php include(TPL_DIR.'zi_header.php');?>

<hr class="hr" />
<form name="form" method="post" action="">
    <table>
        <tr><th colSpan="2">版本管理</th></tr>
        <tr><th style="width: 40%">正式版本*</th><td><input type="text" name="all" style="width: 300px;" size="120" id="all"  value="<?php echo isset($versionInfo['all']) ? $versionInfo['all'] : '';?>">&nbsp;例如:1.0.0.1</td></tr>
        <tr><th style="width: 40%">白名单版本*</th><td><input type="text" name="white" style="width: 300px;" size="120" id="white"  value="<?php echo isset($versionInfo['white']) ? $versionInfo['white'] : '';?>">&nbsp;例如:1.0.0.1</td></tr>
        <tr><th colspan="2"><input type="submit"  class="input" value="修改" /></th></tr>
    </table>
    <input type="hidden" value="all" name="step"></input>
</form>
<script type="text/javascript">
    $(function(){
        $("input[type=submit]").click(function(){
            if (!$("#all").val()) {
                alert("正式版本不为空");
                return false;
            }

            if (!$("#white").val()) {
                alert("白名单版本不为空");
                return false;
            }
            return true;
        });
    });
</script>

<hr class="hr" />
<table style="width: 100%" class="mytable">
    <tr>
        <th>ID</th>
        <th>渠道标识</th>
        <th>包版本</th>
        <th>热更新地址</th>
        <th>是否强制更新,1.是 0.否</th>
        <th>强制更新地址</th>
        <th>生产服版本</th>
        <th>白名单版本</th>
        <th>服务器列表地址</th>
        <th>是否提审加密,1.是 0.否</th>
        <th>操作</th>
    </tr>
    <?php foreach($versionList as $k => $val){?>
        <tr style="background-color:#f6f9f3" >
            <td style="text-align:center;"><?php echo $val['id']; ?></td>
            <td style="text-align:center;"><?php echo $val['channel_id']; ?></td>
            <td style="text-align:center;"><?php echo $val['base_ver']; ?></td>
            <td style="text-align:center;"><?php echo $val['cdn_path']; ?></td>
            <td style="text-align:center;"><?php echo $val['is_constraint']; ?></td>
            <td style="text-align:center;"><?php echo $val['constraint_path']; ?></td>
            <td style="text-align:center;"><?php echo $val['all_version']; ?></td>
            <td style="text-align:center;"><?php echo $val['white_version']; ?></td>
            <td style="text-align:center;"><?php echo $val['server_list_url']; ?></td>
            <td style="text-align:center;"><?php echo $val['is_ts']; ?></td>
            <td style="text-align:center;">
                <?php

                    if ($val['id'] > 0) {

                        echo '<a style="border-color: #92799a;background-color: #fb9da3;" href="?sevid='.$SevidCfg["sevid"].'&mod=fun&act=getVersion&type=select&id='.$val['id'].'">修改 </a>';

                        echo '<a style="border-color: #92799a;background-color: #fb9da3;" href="?sevid='.$SevidCfg["sevid"].'&mod=fun&act=getVersion&type=delete&id='.$val['id'].'">删除 </a>';
                    }else{
                        echo '<a style="border-color: #92799a;background-color: #fb9da3;" href="?sevid='.$SevidCfg["sevid"].'&mod=fun&act=getVersion&type=select&id='.$val['id'].'">新增 </a>';
                    }
                ?>
            </td>
        </tr>
    <?php } ?>
</table>

<div class="hero_div">
    <?php include(TPL_DIR.'footer.php');?>
</div>