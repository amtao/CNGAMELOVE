<?php include(TPL_DIR.'header.php');?>
<?php $_SESSION['zi_page'] = 5;?>
<?php include(TPL_DIR.'zi_header.php');?>

<hr class="hr" />
<form name="form" method="post" action="">
    <table>
        <tr><th colSpan="2">白名单管理</th></tr>
        <tr><th style="width: 40%">白名单版本*</th><td><input type="text" name="ip" style="width: 300px;" size="120" id="ip"  value="">&nbsp;例如:1.0.0.1</td></tr>
        <tr><th colspan="2"><input type="submit"  class="add" value="添加" /></th></tr>
    </table>
    <input type="hidden" value="all" name="step"></input>
</form>
<script type="text/javascript">
    $(function(){
        $("input[type=submit]").click(function(){

            if (!$("#ip").val()) {
                alert("白名单不为空");
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
        <th>IP</th>
        <th>操作</th>
    </tr>
    <?php foreach($versionList as $k => $val){?>
        <tr style="background-color:#f6f9f3" >
            <td style="text-align:center;"><?php echo $val['id']; ?></td>
            <td style="text-align:center;"><?php echo $val['ip']; ?></td>
            <td style="text-align:center;">
                <?php
                    echo '<a style="border-color: #92799a;background-color: #fb9da3;" href="?sevid='.$SevidCfg["sevid"].'&mod=fun&act=whiteList&type=delete&id='.$val['id'].'">删除 </a>';
                ?>
            </td>
        </tr>
    <?php } ?>
</table>

<div class="hero_div">
    <?php include(TPL_DIR.'footer.php');?>
</div>