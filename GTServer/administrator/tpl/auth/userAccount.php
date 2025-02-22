<?php include(TPL_DIR.'header.php');?>
<?php $_SESSION['zi_page'] = 9;?>
<?php include(TPL_DIR.'zi_header.php');?>
<hr class="hr"/>
<form name="addUser" method="POST" action='?sevid=<?php echo $SevidCfg['sevid'];?>&mod=auth&act=addUser'>
<table style="width: 100%" class="mytable">
    <tr><th colspan="6">账号添加</th></tr>
    <tr>
        <th style="text-align:right;width: 200px;">账号</th>
        <td style="text-align:left;">
            <input  type="text" id="account" name="account"  value=""/>
        </td>
    </tr>
    <tr >
        <th style="text-align:right;width: 200px;">账号名称</th>
        <td style="text-align:left;">
            <input   type="text" id="name" name="username"  value=""/>
        </td>
    </tr>
    <tr><th colspan="6"><input type="submit" value="确定提交" /></th></tr>
</table>
</form>
<hr class="hr"/>
<table style='width:100%;' class="mytable">
    <tr>
        <th>后台账户</th>
        <th>名称</th>
        <th>操作</th>
    </tr>

    <?php
    if ( is_array($userAccount) ) {

        foreach ($userAccount as $key => $value) {
            ?>
            <tr>
                <td style="text-align: center"><?php echo $key;?></td>
                <td style="text-align: center"><?php echo $value['name'];?></td>
                <td style="text-align: center">
                    <a class="delete" data-key="<?php echo $key?>" data-name="<?php echo $val['title']?>" href='?sevid=<?php echo $SevidCfg['sevid'];?>&mod=auth&act=deleteUser'>删除</a>
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
    $(".delete").on('click', function (e) {
        e.preventDefault();
        var  url = $(this).prop('href');
        var k = $(this).data('key');
        layer.confirm('确认删除'+k , {
            btn: ['确定','取消'] //按钮
        }, function(){
            $.ajax({
                type: "POST",
                url: url,
                data: "key="+k,
                success: function(msg){
                    layer.msg(msg, {icon: 1});
                    setTimeout(function(){
                        document.location.reload();//页面刷新
                    } ,500);
                }
            });
        }, function(){
            layer.msg('已取消,慎重点好...', {icon: 2});
            return false;
        });
    });

    $(':input[type="submit"]').on('click', function (e) {
        e.preventDefault();
        var account = $(':input[name="account"]').val();
        var username = $(':input[name="username"]').val();
        var url = '?sevid=<?php echo $SevidCfg['sevid'];?>&mod=auth&act=addUser';
        if (account == ''){
            layer.tips('账号不为空',$('[name="account"]'), {tips: [1, '#0FA6D8']});
            return false;
        }
        if (username == ''){
            layer.tips('名称不为空',$('[name="username"]'), {tips: [1, '#0FA6D8']});
            return false;
        }
        $.ajax({
            type: "POST",
            url: url,
            data: "account=" + account + "&username=" + username,
            success: function(msg){
                layer.msg(msg, {icon: 1});
                setTimeout(function(){
                    document.location.reload();//页面刷新
                } ,500);
            }
        });
    });
});
</script>
