<?php include(TPL_DIR.'header.php');?>
<?php $_SESSION['zi_page'] = 7;?>
<?php include(TPL_DIR.'zi_header.php');?>
<table class="mytable">
<tbody><tr>
<th>类型</th>
<th>第一名（名字）</th>
<th>UID</th>
<th>数值</th>
</tr>
<tr>
<th>势力榜</th>
<th><a style="border:none" href="admin.php?sevid=<?php echo $SevidCfg['sevid'];?>&mod=user&act=userChange&uid=<?php echo $redis1Model->outf[0]['id']?>"><?php echo $redis1Model->outf[0]['name']?></a></th>
<th><a style="border:none" href="admin.php?sevid=<?php echo $SevidCfg['sevid'];?>&mod=user&act=userChange&uid=<?php echo $redis1Model->outf[0]['id']?>"><?php echo $redis1Model->outf[0]['uid']?></a></th>
<th><?php echo $redis1Model->outf[0]['num']?></th>
</tr>
<th>好感榜</th>
<th><a style="border:none" href="admin.php?sevid=<?php echo $SevidCfg['sevid'];?>&mod=user&act=userChange&uid=<?php echo $redis3Model->outf[0]['id']?>"><?php echo $redis3Model->outf[0]['name']?></a></th>
<th><a style="border:none" href="admin.php?sevid=<?php echo $SevidCfg['sevid'];?>&mod=user&act=userChange&uid=<?php echo $redis3Model->outf[0]['id']?>"><?php echo $redis3Model->outf[0]['uid']?></a></th>
<th><?php echo $redis3Model->outf[0]['num']?></th>
</tr>
<th>关卡榜</th>
<th><a style="border:none" href="admin.php?sevid=<?php echo $SevidCfg['sevid'];?>&mod=user&act=userChange&uid=<?php echo $redis2Model->outf[0]['id']?>"><?php echo $redis2Model->outf[0]['name']?></a></th>
<th><a style="border:none" href="admin.php?sevid=<?php echo $SevidCfg['sevid'];?>&mod=user&act=userChange&uid=<?php echo $redis2Model->outf[0]['id']?>"><?php echo $redis2Model->outf[0]['uid']?></a></th>
<th><?php echo $redis2Model->outf[0]['num']?></th>
</tr>
<th>点赞榜</th>
<th><a style="border:none" href="admin.php?sevid=<?php echo $SevidCfg['sevid'];?>&mod=user&act=userChange&uid=<?php echo $redis8Model->outf[0]['id']?>"><?php echo $redis8Model->outf[0]['name']?></a></th>
<th><a style="border:none" href="admin.php?sevid=<?php echo $SevidCfg['sevid'];?>&mod=user&act=userChange&uid=<?php echo $redis8Model->outf[0]['id']?>"><?php echo $redis8Model->outf[0]['uid']?></a></th>
<th><?php echo $redis8Model->outf[0]['num']?></th>
</tr>
<th>宫殿榜</th>
<th><?php echo $redis10Model->outf[0]['name']?></th>
<th><?php echo $redis10Model->outf[0]['id']?></th>
<th><?php echo $redis10Model->outf[0]['num']?></th>
</tr>

</tbody></table>