<?php include(TPL_DIR.'header.php');?>
<?php $_SESSION['zi_page'] = 7;?>
<?php include(TPL_DIR.'zi_header.php');?>
<hr class="hr" />
<table class="mytable">
<tbody><tr>
<th>排名</th>
<th>名字</th>
<th>UID</th>
<th>身份</th>
<th>点赞</th>
<!--<th>操作</th>-->
</tr>
<?php foreach($dianzan as $v){?>
<tr style="background-color:#f6f9f3;text-align: center;">
<td><?php echo $v['rid']?></td>

<td style="text-align: left;"><a  class="aColor" href="admin.php?sevid=<?php echo $SevidCfg['sevid'];?>&mod=user&act=userChange&uid=<?php echo $v['id']?>"><?php echo $v['name']?></a></td>
<td><a style="border:none" href="admin.php?sevid=<?php echo $SevidCfg['sevid'];?>&mod=user&act=userChange&uid=<?php echo $v['id']?>"><?php echo $v['id']?></a></td>
<td><?php echo $v['level']?></td>
<td><?php echo $v['num']?></td>
   <!-- <td><a style="border:none" href="admin.php?sevid=<?php /*echo $SevidCfg['sevid'];*/?>&mod=paihang&act=dianzan&uid=<?php /*echo $v['uid']*/?>">删除</a></td>
</tr>-->
<?php }?>
</tbody></table>
