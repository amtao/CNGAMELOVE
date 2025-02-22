<?php include(TPL_DIR.'header.php');?>
<?php $_SESSION['zi_page'] = 1;?>
<?php include(TPL_DIR.'zi_header.php');?>
<?php include(TPL_DIR.'user/playmsg_head.php');?>


</form>
</div>
    <div style="width: 400px;display: inline-block;float: left;">
        <p>伙伴信物:</p>
        <form action="" method="post">
            <table style="width: 300px;">
                <tr>
                    <td style="text-align: center;background-color: #B0CFED;">伙伴id</td>
                    <td style="text-align: center;background-color: #B0CFED;">信物id</td>
                    <td style="text-align: center;background-color: #B0CFED;">信物等级</td>
                    <td style="text-align: center;background-color: #B0CFED;">是否激活</td>
                </tr>
                <?php if(!empty($tokenInfo)){ foreach ($tokenInfo as $key => $value){?>
                <tr>
                    <td style="text-align: center;"><?php echo $value["heroid"];?></td>
                    <td style="text-align: center;"><?php echo $value["tokenid"];?></td>
                    <td style="text-align: center;"><?php echo $value["lv"];?></td>
                    <td style="text-align: center;"><?php echo $value["isJihuo"];?></td>
                </tr>
                <?php } }?>
            </table>
        </form></div>

<div style="width: 400px;display: inline-block;float: left;">
    <p>伙伴好感度羁绊值:</p>
<form action="" method="post">
    <table style="width: 300px;">
        <tr>
            <td style="text-align: center;background-color: #B0CFED;">伙伴id</td>
            <td style="text-align: center;background-color: #B0CFED;">好感度</td>
            <td style="text-align: center;background-color: #B0CFED;">羁绊值</td>
        </tr>
        <?php if(!empty($loveJbInfo)){ foreach ($loveJbInfo as $heroid => $value){?>
        <tr>
            <td style="text-align: center;"><?php echo  $heroid;?></td>
            <td style="text-align: center;"><?php echo  $value['love'];?></td>
            <td style="text-align: center;"><?php echo  $value['jiban'];?></td>
        </tr>
        <?php } }?>
    </table>
</form></div>
