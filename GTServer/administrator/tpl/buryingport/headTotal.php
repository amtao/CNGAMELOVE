<?php include(TPL_DIR.'header.php');?>
<?php $_SESSION['zi_page'] = 10;?>
<?php include(TPL_DIR.'zi_header.php');?>

<hr class="hr"/>
<form name="rechargeSearch" method="POST" action=""  onsubmit="return checkForm();">
<table>
     <tr><th colspan="6">头像查询</th></tr>
    <tr>
        <th style="text-align:right;width: 150px;">区服：</th>
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
<BR>

</form>
</div>
    <div style="width: 400px;display: inline-block;float: left;">
        <p>头像获得:</p>
        <form action="" method="post">
            <table style="width: 300px;">
                <tr>
                    <td style="text-align: center;background-color: #B0CFED;">头像id</td>
                    <td style="text-align: center;background-color: #B0CFED;">个数</td>
                </tr>
                <?php if(!empty($headInfo)){ foreach ($headInfo as $key => $value){?>
                <tr>
                    <td style="text-align: center;"><?php echo $key;?></td>
                    <td style="text-align: center;"><?php echo $value;?></td>
                </tr>
                <?php } }?>
            </table>
        </form></div>

<div style="width: 400px;display: inline-block;float: center;">
    <p>头像框获得:</p>
<form action="" method="post">
    <table style="width: 300px;">
        <tr>
            <td style="text-align: center;background-color: #B0CFED;">头像框id</td>
            <td style="text-align: center;background-color: #B0CFED;">个数</td>
        </tr>
        <?php if(!empty($headblankInfo)){ foreach ($headblankInfo as $blankid => $value){?>
        <tr>
            <td style="text-align: center;"><?php echo  $blankid;?></td>
            <td style="text-align: center;"><?php echo  $value;?></td>
        </tr>
        <?php } }?>
    </table>
</form></div>
<BR>

<?php include(TPL_DIR.'footer.php');?>