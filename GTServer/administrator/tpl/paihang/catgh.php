<?php include(TPL_DIR.'header.php');?>
<?php $_SESSION['zi_page'] = 7;?>
<?php include(TPL_DIR.'zi_header.php');?>
<hr class="hr" />
<form name="form" method="post" action="">
<p>宫殿id: <input type="text" name="cid" value="<?php echo $_REQUEST['cid'];?>"  /></p>
<p><input type="submit" value="查找" /></p>
</form>


<?php if (!empty($result)){?>
<form action="" method="post">
<table class="mytable">
<tbody>
<tr><th colspan="4">宫殿信息</th></tr>
<tr><th>宫殿id</th><td colspan="3" style="background-color:#f6f9f3"><?php echo $result['id'];?></td></tr>
<tr><th>宫殿名称</th><td colspan="3" style="background-color:#f6f9f3"><?php echo $result['name'];?></td></tr>
<tr><th>宫殿等级</th><td colspan="3" style="background-color:#f6f9f3"><?php echo $result['level'];?></td></tr>
<tr><th>宫殿经验</th><td colspan="3" style="background-color:#f6f9f3"><form action="" method="post"> <?php echo $result['exp'];?> <input name="cid" style="display:none;" value="<?php echo $_REQUEST['cid'];?>" /><input name="addclubexp" value="" /> <input type="submit" value="更新" /> </form> </td> </tr>
<tr><th>宫殿财富</th><td colspan="3" style="background-color:#f6f9f3"><form action="" method="post"> <?php echo $result['fund'];?> <input name="cid" style="display:none;" value="<?php echo $_REQUEST['cid'];?>" /><input name="addclubfund" value="" /> <input type="submit" value="更新" /> </form> </td></tr>

<?php

    foreach ($result['members'] as $user){
        switch ($user['post']){
            //1:宫主  2:副宫主 3:尚宫 4:成员 5:其他
            case 1:
                $post = '宫主';
                break;
            case 2:
                $post = '副宫主';
                break;
            case 3:
                $post = '尚宫';
                break;
            case 4:
                $post = '成员';
                break;
            default:
                $post = '其他';
                break;
        }
        $tip = '';
        if(!empty($kua_b[$user['id']])){
        	$tip = '已参加帮战';
        }

        echo '<form action="" method="post">';
        echo '<tr>';
        echo '<th>'.$post.'</th>';
        echo '<td style="background-color:#f6f9f3"><a href="?sevid='.$SevidCfg['sevid'].'&mod=user&act=userChange&uid='.$user['id'].'">'.$user['id'].'</a>('.$user['name'].')
        <input name="cid" style="display:none;" value="'.$cid.'" />
        <input name="changeUid" style="display:none;" value="'.$user['id'].'" />
            <select name="jop">
                <option value="">请选择职位</option>
                <option value="1">宫主</option>
                <option value="2">副宫主</option>
                <option value="3">尚宫</option>
                <option value="4">成员</option>
            </select>
            <input type="submit" value="委任" />
        </td>';
        echo '<td style="background-color:#f6f9f3"><a class="delete" style="border-color: #92799a;background-color: #fcb9b4;" data-cid="'.$cid.'" data-id="'.$user['id'].'" data-name="'.$user['name'].'" href="?sevid='.$SevidCfg['sevid'].'&mod=paihang&act=catgh&cid='.$cid.'&uid='.$user['id'].'">删除</a>';
        echo '<td style="background-color:#f6f9f3">'.$tip.'</td>';
         echo '</tr></form>';
    }

?>

</tbody>
</table>

<?php }?>
<script>
    $(document).ready(function () {
        $(".delete").on('click',function (e) {
            e.preventDefault();
            var name_zh = $(this).data('name');
            var id = $(this).data('id');
            var cid = $(this).data('cid');
            var type = 'catgh';
            var url = "admin.php?sevid=<?php echo $SevidCfg['sevid'];?>&mod=paihang&act=delete";
            layer.confirm('确认删除<span style="color: red;font-size: 12px;">'+name_zh+"</span>", {
                btn: ['确定','取消'] //按钮
            }, function(){
                $.ajax({
                    type: "POST",
                    url: url,
                    data: "id="+id+"&type="+type+"&cid="+cid,
                    success: function(msg){
                        layer.msg(msg, {icon: 1});
                        setTimeout(function(){
                            document.location.reload();//页面刷新
                        } ,1000);
                    }
                });
            }, function(){
                layer.msg('已取消,慎重点好...', {icon: 2});
                return false;
            });
        });
    });
</script>
