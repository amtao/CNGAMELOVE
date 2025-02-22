<?php include(TPL_DIR.'header.php');?>
<?php $_SESSION['zi_page'] = 2;?>
<?php include(TPL_DIR.'zi_header.php');?>
<hr class="hr"/>
<form name="chat" method="POST" action="">
    <table style="width: 100%">
        <tr><th colspan="6">敏感字库添加</th></tr>
        <tr>
            <th style="text-align:right;">敏感词：</th>
            <td style="text-align:left;">
                <input style="width: 60%;"  type="text" id="sensitive" name="sensitive"  value=""/>
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
            <th>操作</th>
        </tr>
        <?php foreach($data as $k => $val){?>
            <tr style="background-color:#f6f9f3">
                <td style="text-align:center;"><?php echo $val; ?></td>
                <td style="text-align:center;"><a style="" href="?sevid=<?php echo $SevidCfg["sevid"];?>&mod=infomation&act=sensitive&key=<?php echo $k;?>">删除</a></td>
            </tr>
        <?php } ?>
    </table>
<?php  }?>