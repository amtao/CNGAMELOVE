<?php include(TPL_DIR.'header.php');?>
<?php $_SESSION['zi_page'] = 5;?>
<?php include(TPL_DIR.'zi_header.php');?>
<form name="form1" id="form1" method="post" action="">
	角色ID: <input name="keyword1" value="" type="text">
	<input value="查找" type="submit">
</form>
<table style="width:100%;">
<tbody><tr>
<th>序号</th>
<th>角色ID</th>
<th>平台标识</th>
<th>状态</th>
<th>充值金额(元)</th>
<th>兑换游戏币</th>
<th>创建时间</th>
<th>支付时间</th>
<!--<th>管理</th>-->
</tr>
<tbody>
<?php if(count($data)>0){foreach($data as $v){?>
<tr>
<th><?php echo $v['orderid']?></th>
<!--<th><?php echo $v['openid']?></th>
--><!--<th><?php echo $v['tradeno']?></th>
--><th><a style="border:none" href="admin.php?sevid=<?php echo $SevidCfg['sevid'];?>&mod=user&act=userChange&uid=<?php echo $v['roleid']?>"><?php echo $v['roleid']?></a></th>
<th><?php echo $v['platform']?></th>
<th><?php echo $v['status']?></th>
<th><?php echo $v['money']?></th>
<th><?php echo $v['diamond']?></th>
<th><?php echo date("Y-m-d H:i:s", $v['ctime'])?></th>
<th><?php echo date("Y-m-d H:i:s", $v['ptime'])?></th>
<!--<th><?php echo $v['paytype']?></th>
--></tr>
<?php }}else{?> <td colspan="11" style="color:#F00" align="center">无数据</td><?php }?>
</tbody>
</table>
<div class="hero_div">
    <?php include(TPL_DIR.'footer.php');?>
</div>