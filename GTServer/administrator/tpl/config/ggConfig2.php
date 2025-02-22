<?php include(TPL_DIR.'header.php');?>
<?php $_SESSION['zi_page'] = 4;?>
<?php include(TPL_DIR.'zi_header.php');?>
<style>
    .tr{
        background:white;
    }
    .tr th{
        background:white;
    }
</style>
    <hr class="hr"/>
    <div class="mytable">
    <a href="?sevid=<?php echo $SevidCfg['sevid'];?>&mod=config&act=ggConfig">公告内容</a>
    <a href="?sevid=<?php echo $SevidCfg['sevid'];?>&mod=config&act=ggConfig2">公告配置</a>
    <hr class="hr"/>
    <a href="?sevid=<?php echo $SevidCfg['sevid'];?>&mod=config&act=addggConfig">添加公告配置</a>
    </div>
    <hr class="hr"/>
    <table style="text-align: center;" class="mytable">
        <tr>
            <th>配置编号</th>
            <th>开始时间</th>
            <th>结束时间</th>
            <th>区服</th>
            <th>top</th>
            <th>内容</th>
            <th>包含平台</th>
            <th>不包含平台</th>
            <th style="width: 100px;">操作</th>
        </tr>
<?php if(is_array($data) && !empty($data)):?>
        <?php foreach ($data as $k => $v){

            ?>

            <tr class="tr">
                <th>
                    <?php echo $k?>
                </th>
                <th>
                    <?php echo $v['sTime']?>
                </th>
                <th>
                    <?php echo $v['eTime']?>
                </th>
                <th>
                    <?php echo $v['serv']?>
                </th>
                <th>
                    <?php echo $v['top']?>
                </th>
                <th>

                    <div style="text-align: left">
                        header：<?php echo $v['header']?>
                    </div>
                    <div style="text-align: left">
                        title：<?php echo $v['title']?>
                    </div>
                    <div style="text-align: left">
                        body：<?php echo substr($v['body'],0,36)?>
                    </div>

                </th>
                <th>
                    <?php
                    $include = explode(',',$v['include']);
                    $lenght = count($include) > 4?3:count($include);
                    for($i=0;$i<$lenght;$i++){
                        echo "<div>".$platformList[$include[$i]]."</div>";
                    }

                    ?>

                </th>
                <th>
                    <?php
                    $exclusive = explode(',',$v['exclusive']);
                    $lenght = count($exclusive) > 4?3:count($exclusive);
                    for($i=0;$i<$lenght;$i++){
                        echo "<div>".$platformList[$exclusive[$i]]."</div>";
                    }
                    ?>

                </th>
                <th>
                    <a href="?sevid=<?php echo $SevidCfg['sevid'];?>&mod=config&act=ggConfig2&key=<?php echo $k;?>&del=1">删除</a>
                    <a href="?sevid=<?php echo $SevidCfg['sevid'];?>&mod=config&act=addggConfig&key=<?php echo $k;?>">编辑</a>
                </th>
            </tr>


        <?php
        }
        ?>
    <?php endif;?>
    </table>
    <br />

<?php include(TPL_DIR.'footer.php');?>