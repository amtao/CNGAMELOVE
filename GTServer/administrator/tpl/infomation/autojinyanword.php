<?php include(TPL_DIR.'header.php');?>
<?php $_SESSION['zi_page'] = 2;?>
<?php include(TPL_DIR.'zi_header.php');?>
<hr class="hr"/>
<form name="chat" method="POST" action="">
    <table style="width: 100%">
        <tr><th colspan="6">自动禁言设置</th></tr>
        <tr>
            <th style="text-align:right;">时间：</th>
            <td style="text-align:left;">
                <input style="width: 60%;"  type="text" id="time" name="time"  value="<?php echo $time; ?>"/>
            </td>
        </tr>
        <tr><th colspan="6"><input type="submit" value="修改" /></th></tr>
        <tr><th colspan="6">自动禁言字库添加</th></tr>
        <tr>
            <th style="text-align:right;">敏感词：</th>
            <td style="text-align:left;">
                <input style="width: 60%;"  type="text" id="sensitive" name="sensitive"  value=""/>
            </td>
        </tr>
        <tr>
            <th style="text-align:right;">权重值：</th>
            <td style="text-align:left;">
                <input style="width: 60%;"  type="text" id="percentage" name="percentage"  value="10"/>
            </td>
        </tr>
        <tr><th colspan="6"><input type="submit" value="添加" /></th></tr>
    </table>
</form>
<BR>
<?php
if(!empty($data) && is_array($data)){
    ?>
    <table style="width: 100%"  class="mytable">
        <tr>
            <th>敏感词</th>
            <th>权重值</th>
            <th>操作</th>
        </tr>
        <?php foreach($data as $k => $val){?>
            <tr style="background-color:#f6f9f3">
                <td style="text-align:center;"><?php echo $k; ?></td>
                <td style="text-align:center;"><?php echo $val; ?></td>
                <td style="text-align:center;"><a style="" href="?sevid=<?php echo $SevidCfg["sevid"];?>&mod=infomation&act=autojinyanword&key=<?php echo $k;?>">删除</a></td>
            </tr>
        <?php } ?>
    </table>
<?php  }?>