<?php include(TPL_DIR.'header.php');?>
<?php $_SESSION['zi_page'] = 5;?>
<?php include(TPL_DIR.'zi_header.php');?>

<hr class="hr"/>
<form id="form-search" method="POST" action="" >
    <table style="width: 100%">
        <tr><th colspan="6">全服元宝查询</th></tr>
        <tr>
            <th style="text-align:right;width: 50px;">区服：</th>
            <td style="text-align:left;">
                <select name="serverid" id="serverid">
                     <option value="0" <?php if($_POST['serverid'] == 0) echo "selected";?>>全服</option>
                     <?php foreach ($serverList as $key => $value):?>
                         <option value="<?php echo $key; ?>" <?php if($_POST['serverid'] == $key) echo "selected";?>><?php echo $value['id'].'区'.$value['name']['zh']; ?></option>
                     <?php endforeach;?>
                </select>
            </td>
        </tr>
        <tr><th colspan="6"><input type="submit" value="确定查询" /></th></tr>
    </table>
</form>

<hr class="hr" />
<table style="width: 600px;margin-top:10px;line-height:30px;font-size: 13px;">
    <tr>
        <td style="text-align:center;width: 350px;background-color:#c6e4fe; font-weight: bold;">全服剩余元宝数</td>
    </tr>
    <tr>
        <td style="text-align:center;width: 100px;"><?php echo $allDiamond;?></td>
    </tr>

</table>
<hr class="hr" />
<?php
if($allList){
    ?>
    <table>
        <tr>
            <th>序号</th>
            <th>UID</th>
            <th>名称</th>
            <th>VIP</th>
            <th>当前元宝</th>
            <th>最后登录时间</th>
        </tr>
        <?php foreach ($allList as $key => $value): ?>
        <tr>
            <td style="text-align:center;"><?php echo $key+1;?></td>
            <td style="text-align:center;"><?php echo $value['uid'];?></td>
            <td style="text-align:center;"><?php echo $value["name"];?></td>
            <td style="text-align:center;"><?php echo $value["vip"];?></td>
            <td style="text-align:center;"><?php echo $value["diamond"];?></td>
            <td style="text-align:center;"><?php echo date('Y-m-d H:i:s',$value["lastlogin"]);?></td>
        </tr>
        <?php endforeach;?>
    </table>
<?php  }?>

<?php
if($sevidList){
    ?>
    <table>
        <tr>
            <th>区服</th>
            <th>总元宝</th>
        </tr>
        <?php foreach ($sevidList as $key => $value): ?>
        <tr>
            <td style="text-align:center;"><?php echo $value['sevid'];?></td>
            <td style="text-align:center;"><?php echo $value["diamond"];?></td>
        </tr>
        <?php endforeach;?>
    </table>
<?php  }?>

<div class="hero_div">
    <?php include(TPL_DIR.'footer.php');?>
</div>
