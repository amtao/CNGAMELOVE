<?php include(TPL_DIR.'header.php');?>
<?php $_SESSION['zi_page'] = 2;?>
<?php include(TPL_DIR.'zi_header.php');?>
    <hr class="hr"/>
    <div style="display: inline-block;width: 200px; " class="mytable"><a class='backGroundColor' href="?sevid=<?php echo $SevidCfg['sevid'];?>&mod=infomation&act=showMemcache&cur=showCache" <?php if ($_GET['act'] == 'showMemcache') echo 'style="color:red;"';?> style="border: 1px solid #bda2a2;padding: 1px 5px;">Memcache缓存</a></div>
    <div style="padding: 0 20px;display: inline-block;float:left;width: 200px; " class="mytable"><a class='backGroundColor' href="?sevid=<?php echo $SevidCfg['sevid'];?>&mod=infomation&act=showRedis&cur=showCache" <?php if ($_GET['act'] == 'showRedis') echo 'style="color:red;"';?> style="border: 1px solid #bda2a2;padding: 1px 5px;">Redis缓存</a></div>
<hr/>
<h2>Redis查询</h2>
<div style="color:#F00"><?php echo $msg;?></div>

<form name="form1" id="form1" method="post" action="">
<p>Key: <input type="text" style="width:400px;" name="rediskey" value="<?php echo $key;?>"  />
<input class="input" type="submit" name="submit" value="查询" />
<input class="input" id="delete" type="submit" name="submit" value="删除" <?php if(!$_POST['rediskey']) echo 'disabled="disabled"';?> />
</p>
</form>
<hr/>
<h2>Redis修改</h2>
<form name="add_form" id="add_form" method="post" action="">
<input class="input" type="hidden" name="jsontype" value="1"  />
Key:<input class="input" style="width:400px;" type="text" name="key" value="<?php echo $key;?>"  /><br/>
<textarea id="json_data" rows="6" cols="100" style="margin: 5px 0px;" name="json_data">
<?php echo json_encode($data);?>
</textarea><br/>
<input id="posts" type="submit" name="submit" value="提交" />
</form>
<br/>

<?php
if(!empty($key))
{
    echo '<table>
            <tbody><tr>
            <th style="text-align: center">编号</th>
            <th style="text-align: center">key</th>
            <th style="text-align: center">value</th>
            <th style="text-align: center">操作</th></tr>';
    $i=1;
    foreach ($data as $k => $v){
        echo '<tr>';
        echo '<td style="text-align: center">'.$i.'</td>';
        echo '<td style="text-align: center">'.$k.'</td>';
        echo '<td style="text-align: center">'.$v.'</td>';
        echo '<td style="text-align: center"><a class="delete" href="?sevid='.$SevidCfg['sevid'].'&mod=infomation&act=showRedis&redis='.$key.'&key='.$k.'">删除</td>';
        echo '</tr>';
        $i++;
    }
    echo '</tbody></table>';
}

?>

<br/>
<?php include(TPL_DIR.'footer.php');?>
<script>
$(document).ready(function () {
    $(".delete").on('click',function (e) {
        e.preventDefault();
        if (confirm('确定要删除吗?')){
            var href = $(this).prop('href');
            window.location.href = href;
        }else{
            return false;
        }
    });
});
</script>
