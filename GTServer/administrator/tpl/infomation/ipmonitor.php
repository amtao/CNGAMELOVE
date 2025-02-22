<?php include(TPL_DIR.'header.php');?>
<?php $_SESSION['zi_page'] = 2;?>
<?php include(TPL_DIR.'zi_header.php');?>
    <hr class="hr"/>
    <table style="width:100%;">
        <caption>ip超过限制列表</caption>
        <tbody>
        <?php
        if(!empty($passInfo)){
            foreach ($passInfo as $v) {
                echo '<tr style="background-color:#f6f9f3;">';
                echo '<td style="text-align:center;">' . $v . '</td>';
                echo '</tr>';
            }
        }else{
            echo '<tr>';
            echo '<td colspan="2" style="text-align:center;">暂无ip超过限制列表</td>';
            echo '</tr>';
        }
        ?>
        </tbody>
    </table>

    <br/>
<?php include(TPL_DIR.'footer.php');?>