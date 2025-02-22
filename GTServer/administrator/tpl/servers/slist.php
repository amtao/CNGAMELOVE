<?php include(TPL_DIR . 'header.php');?>

<div class="mytable">
<a href="?sevid=<?php echo $SevidCfg['sevid'];?>&mod=servers&act=addslist">添加服务器</a>
    <a href="?sevid=<?php echo $SevidCfg['sevid'];?>&mod=servers&act=listStatus">查看服务器状态</a>
</div>
<hr class="hr"/>

<table style='width:100%;text-align: center'>
	<tr>
		<th>区号</th>
		<th>名字</th>
		<th>域名</th>
		<th>状态</th>
        <th>皮肤</th>
		<th>选区图标开放时间</th>
        <th>已开服天数</th>
        <?php if (empty($auth['ban']['servers']['slist'])):?>
		<th>操作</th>
        <?php endif;?>
	</tr>

<?php
if ( is_array($sList) ) {
	foreach ($sList as $k => $v) {
        $style = empty($v['hdend'])?'':' style="color:red;"';
?>
	<tr>
		<td <?php echo $style;?>><?php echo $v['id'];?></td>
		<td <?php echo $style;?>>
		<?php 
		echo $v['name']['zh'];
		?>
		</td>
		<td <?php echo $style;?>><?php echo $v['url'];?></td>
		
		<td <?php echo $style;?>><?php echo $statusCfg[$v['status']] ;?></td>
        <td <?php echo $style;?>><?php echo $v['skin'] ;?></td>
		<td <?php echo $style;?>><?php echo date("Y-m-d H:i:s", $v['showtime']) ;?></td>
        <td <?php echo $style;?>><?php if ($v['showtime']>Game::get_now()){
                echo date("Y-m-d H:i:s",$v['showtime']);
            }else{
                $openday = strtotime(date('Y-m-d 00:00:00',$v['showtime']));
                $today = Game::day_0();
                echo intval(($today-$openday)/86400)+1;
            } ?>
        </td>
        <?php if (empty($auth['ban']['servers']['slist'])):?>
		<td>
			<input type='button' value='编辑' 
				onclick="location.href='?sevid=<?php echo $SevidCfg['sevid'];?>&mod=servers&act=addslist&key=<?php echo $v['id'];?>'" />
			<a class="delete" data-name="<?php echo $v['name']['zh']?>" href='?sevid=<?php echo $SevidCfg['sevid'];?>&mod=servers&act=delslist&delkey=<?php echo $v['id'];?>'>删除</a>
		</td>
        <?php endif;?>
	</tr>
<?php
	}
}
?>

</table>
<?php include(TPL_DIR.'footer.php');?>
<script>
    $(document).ready(function () {
        $(".delete").on('click',function (e) {
            e.preventDefault();
            var name_zh = $(this).data('name');
            if (confirm('确定要删除'+name_zh+'吗?')){
                var href = $(this).prop('href');
                window.location.href = href;
            }else{
                return false;
            }
        });
    });
</script>