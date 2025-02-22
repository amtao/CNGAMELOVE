<?php include(TPL_DIR.'header.php');?>
<?php $_SESSION['zi_page'] = 4;?>
<?php include(TPL_DIR.'zi_header.php');?>
    <hr class="hr"/>
    <div class="header">
        <a class='backGroundColor' <?php if ($_GET['act'] == 'baseConfig'){echo 'style="color:red;';} ?> href="?sevid=<?php echo $SevidCfg['sevid'];?>&mod=config&act=baseConfig&uid=<?php echo $uid;?>" >基础配置</a>
        <?php if(SERVER_ID == 999 || SERVER_ID == 1){?>
            <a class='backGroundColor' <?php if ($_GET['act'] == 'allConfig'){echo 'style="color:red;';} ?> href="?sevid=<?php echo $SevidCfg['sevid'];?>&mod=config&act=allConfig&uid=<?php echo $uid;?>">通服基础配置</a>
        <?php }?>
    </div>
    <hr class="hr"/>
    <script>

        function checkTempArg(){
            var form = $("#formid").val();
            console.log(form);
            var contents = $("#contents").val();
            $("#formid").submit();
        };
        function add(){
            var id = $("#id").val();
            id = parseInt(id) + 1;
            console.log(id);
            $("#body").append("<tr><td></td><td>\n" +
                "                    内容：<input type='text' size='150'  name='body_"+id+"_word' value='' />\n" +
                "                    字号：<input type='text' size='2' id='server' name='body_"+id+"_size' value='' />"+
                "                    颜色：(<input type='text' size='3' id='server' maxlength='3' value='255' name='body_"+id+"_color_0' />,\n" +
                "                    <input type='text' size='3' id='server' maxlength='3' value='255' name='body_"+id+"_color_1'/>,\n" +
                "                    <input type='text' size='3' id='server' maxlength='3' value='255' name='body_"+id+"_color_2'/>)\n" +
                "                </td></tr>");
            $("#id").val(id);
        };


    </script>

    <form id="formid" method="POST" action="" >
        <input type="hidden" id="id" name='flag' value='<?php echo (count($body)-1)?>' />
        <input type="hidden"   name='type' value='edit' />
        <input type="hidden"   name='key' value='<?php echo $key_d;?>' />
        <table style='width:100%;' id="body" class="mytable">
            <tr><th colspan="6">格式化公告配置</th></tr>
            <tr>
                <td style='text-align: right;'>配置：</td>
                <td><?php echo $info['id'];?></td>
            </tr>
            <tr>
                <td style='text-align: right;'>头部：</td>
                <td>
                    内容：<input type='text' size='150' id='server' name='header_word' value='<?php  echo $header['word']?>' />
                    字号：<input type='text' size='2' id='server' name='header_size' value='<?php  echo $header['size']?>' />
                    颜色：(<input type="text" size='3' id='color' maxlength="3" name="header_color_0"  onchange="check_color" value='<?php  echo $header['color'][0]?>'/>,
                    <input type="text" size='3' id='color' maxlength="3" name="header_color_1" value='<?php  echo $header['color'][1]?>'/>,
                    <input type="text" size='3' id='color' maxlength="3" name="header_color_2" value='<?php  echo $header['color'][2]?>'/>)颜色('0~255','0~255','0~255',)
                </td>
            </tr>
            <tr>
                <td style='text-align: right;'>标题：</td>
                <td>
                    内容：<input type='text' size='150' id='server' name='title_word' value='<?php  echo $title['word']?>' />
                    字号：<input type='text' size='2' id='server' name='title_size' value='<?php  echo $title['size']?>' />
                    颜色：(<input type="text" size='3' id='server' maxlength="3" name="title_color_0" value='<?php  echo $title['color'][0]?>'/>,
                    <input type="text" size='3' id='server' maxlength="3" name="title_color_1" value='<?php  echo $title['color'][1]?>'/>,
                    <input type="text" size='3' id='server' maxlength="3" name="title_color_2" value='<?php  echo $title['color'][2]?>'/>)
                </td>
            </tr>
            <tr>
                <td style='text-align: right;'>详情：</td>
                <td>
                    内容：<input type='text' size='150' id='server' name='body_0_word' value="<?php  print $body[0]['word']?>" />
                    字号：<input type='text' size='2' id='server' name='body_0_size' value='<?php  echo $body[0]['size']?>' />
                    颜色：(<input type='text' size='3' id='server' maxlength='3' name="body_0_color_0" value='<?php  echo $body[0]['color'][0]?>'/>,
                    <input type='text' size='3' id='server' maxlength='3' name="body_0_color_1" value='<?php  echo $body[0]['color'][1]?>'/>,
                    <input type='text' size='3' id='server' maxlength='3' name="body_0_color_2" value='<?php  echo $body[0]['color'][2]?>'/>)
                </td>
                <td><a class="backGroundColor" href="javascript:add()">增加</a></td>
            </tr>

            <?php
                if(count($body) > 1){
                    for($i=1;$i<count($body);$i++){
                     echo "<tr>
                <td style='text-align: right;'></td>
                <td>
                    内容：<input type='text' size='150' id='server' name='body_{$i}_word' value='{$body[$i]['word']}' />
                    字号：<input type='text' size='2' id='server' name='body_{$i}_size' value='{$body[$i]['size']}' />
                    颜色：(<input type='text' size='3' id='server' maxlength='3' name='body_{$i}_color_0' value='{$body[$i]['color'][0]}'/>,
                    <input type='text' size='3' id='server' maxlength='3' name='body_{$i}_color_1' value='{$body[$i]['color'][1]}'/>,
                    <input type='text' size='3' id='server' maxlength='3' name='body_{$i}_color_2' value='{$body[$i]['color'][2]}'/>)
                </td>
            </tr>";


                    }
                }
            ?>

        </table>
    </form>
    <a href="javascript:checkTempArg()" style="color: green;display: inline-block;">【--保存--】</a>
<?php include(TPL_DIR.'footer.php');?>