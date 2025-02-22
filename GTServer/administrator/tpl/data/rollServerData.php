<?php include(TPL_DIR.'header.php');?>
<?php $_SESSION['zi_page'] = 3;?>
<?php include(TPL_DIR.'zi_header.php');?>
<hr class="hr" />
<table>
    <tr>
        <th colspan="7">滚服数据</th>
    </tr>
    <tr>
        <th>区服</th>
        <th>区服总人数</th>
        <th>滚服人数</th>
        <th>人数占比</th>
        <th>区服总充值</th>
        <th>滚服充值</th>
        <th>占比</th>
    </tr>
    <?php if(!empty($data)){ ?>
        <?php foreach ($data as $key => $value){
            echo '<tr style="background-color:#f6f9f3;"><td style="text-align:center;">'.$key.'</td>';
            echo '<td style="text-align:center;">'.$value['count'].'</td>';
            echo '<td style="text-align:center;">'.$value['rollCount'].'</td>';
            echo '<td style="text-align:center;">';
            if ($value['count'] == 0){
                echo 0;
            }else{
                echo $value['rollCount']*100/$value['count'];
            }
            echo '%</td>';
            echo '<td style="text-align:center;">'.$value['money'].'</td>';
            echo '<td style="text-align:center;">'.$value['rollMoney'].'</td>';
            echo '<td style="text-align:center;">';
            if ($value['money'] == 0){
                echo 0;
            }else{
                echo $value['rollMoney']*100/$value['money'];
            }
            echo  '%</td></tr>';
            } ?>
    <?php } ?>
</table>
<hr class="hr" />

<div class="hero_div">
    <?php include(TPL_DIR.'footer.php');?>
</div>
