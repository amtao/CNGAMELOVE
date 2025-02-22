<?php include(TPL_DIR.'header.php');?>
<?php $_SESSION['zi_page'] = 4;?>
<?php include(TPL_DIR.'zi_header.php');?>
<hr class="hr"/>
<div class="mytable">
<a href="?sevid=<?php echo $SevidCfg['sevid'];?>&mod=config&act=ggConfig">公告内容</a>
<a href="?sevid=<?php echo $SevidCfg['sevid'];?>&mod=config&act=ggConfig2">公告配置</a>
</div>
<hr class="hr"/>
<style>
    .th{
        text-align: right;
    }
    .th2{
        text-align: left;
    }
</style>
<script>
    function add(){
        var key = $("#in_sel").find("option:selected").val();
        var value = $("#in_sel").find("option:selected").text();
        $("#in_td").append("<div><input type='hidden' name= 'include[]' value='"+ key +"' />"+ value +"&nbsp;&nbsp;&nbsp;<a class='backGroundColor' href=javascript:void(0) onclick='del(this)'>删除</a></div>");

    };
    function add2(){
        var key = $("#ex_sel").find("option:selected").val();
        var value = $("#ex_sel").find("option:selected").text();
        $("#ex_td").append("<div><input type='hidden' name= 'exclusive[]' value='"+ key +"' />"+ value +"&nbsp;&nbsp;&nbsp;<a class='backGroundColor' href=javascript:void(0) onclick='del(this)'>删除</a></div>");

    };
    function del(arg){
        $(arg).parent("div").remove();
    }
</script>
<form name="form1" id="form1" method="post" action="">
    <input type="hidden" name="updata_key" value="<?php echo $key;?>" />
    <input type="hidden" id="sevid" value="<?php echo $_GET['sevid'];?>" />
    <table style="text-align: center;" >
        <tr>
            <th></th><th></th>
        </tr>
        <tr>
            <th class="th">
                开始时间：
            </th>
            <td class="th2">
                <input class='Wdate' type='text' size='40' id='startTime' name='sTime'
                       value='<?php echo (empty($data['sTime'])) ? date('Y-m-d 00:00:00') : $data['sTime'];?>'
                       onFocus="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm:ss',isShowClear:false,readOnly:true,
						maxDate:'#F{$dp.$D(\'endTime\')}'})" />
            </td>

        </tr>
        <tr>
            <th class="th">
                结束时间：
            </th>
            <td class="th2">
                <input class='Wdate' type='text' size='40' id='endTime' name='eTime'
                       value='<?php echo (empty($data['eTime'])) ? date('Y-m-d 23:59:59') : $data['eTime'];?>'
                       onFocus="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm:ss',isShowClear:false,readOnly:true,
						minDate:'#F{$dp.$D(\'startTime\')}'})" />
            </td>

        </tr>
        <tr>
            <th class="th">区服：</th>
            <td class="th2"><input type="text" size="40" name="serv" value="<?php if(isset($data['serv'])){echo $data['serv'];}else{echo "all";};?>"> 区服的格式     1-10,20,30,40-50
            </td>

        </tr>

        <tr>
            <th class="th">top:</th>
            <td class="th2"> <input type="text" size="5" name="top" value="<?php if(isset($data['top'])){echo $data['top'];}else{echo "";};?>"> top越大越在顶部</td>
        </tr>
        <tr>
            <th class="th">header:</th>
            <td class="th2">
                <input type='text' size='100'   id='header' name='header' value="<?php echo htmlspecialchars($data['header']);?>" />
            </td>
        </tr>
        <tr>
            <th class="th">title:</th>
            <td class="th2"><input type='text' size='100'   id='title' name='title' value="<?php echo htmlspecialchars($data['title']);?>" /></td>
        </tr>
        <tr>
            <th class="th">body:</th>
            <td class="th2">
                <textarea type='text' rows="20" cols="140"  id='body' name='body' ><?php echo $data['body'];?></textarea>
            </td>
        </tr>
        <tr>
            <th class="th">包含平台：</th>
            <td class="th2" id="in_td"><select id="in_sel">
                    <?php
                    $youdongPlat = include (ROOT_DIR . '/administrator/config/youdong.php');
                    if(!empty($platformList)){
                        foreach ($platformList as $k => $v) {
                            $checkboxHtml .= sprintf("<option  value='%s'/>%s</option>", $k, $v);
                        }
                    }
                    echo $checkboxHtml;
                    ?>
                </select>&nbsp;&nbsp;&nbsp;<a href=javascript:void(0) onclick="add()">增加</a>
                <?php
                if($data['include'] != ''){
                    $include = explode(',',$data['include']);
                    foreach ($include as $k){?>
                        <div>
                        <input type='hidden' name= 'include[]' value='<?php echo $k?>' /><?php echo $platformList[$k]?>&nbsp;&nbsp;&nbsp;<a class='backGroundColor' href=javascript:void(0) onclick='del(this)'>删除</a>
                        </div>

                <?php    }
                }


                ?>
            </td>

        </tr>
        <tr>
            <th class="th">不包含平台：</th>
            <td class="th2" id="ex_td"><select id="ex_sel">
                    <?php
                    $youdongPlat = include (ROOT_DIR . '/administrator/config/youdong.php');
                    if(!empty($platformList)){
                        foreach ($platformList as $k => $v) {
                            $checkboxHtml .= sprintf("<option  value='%s'/>%s", $k, $v);
                        }
                    }
                    echo $checkboxHtml;
                    ?>
                </select>&nbsp;&nbsp;&nbsp;<a href=javascript:void(0) onclick="add2()">增加</a>
                <?php
                if($data['exclusive'] != ''){
                    $include = explode(',',$data['exclusive']);
                    foreach ($include as $k){?>
                        <div>
                            <input type='hidden' name= 'exclusive[]' value='<?php echo $k?>' /><?php echo $platformList[$k]?>&nbsp;&nbsp;&nbsp;<a class='backGroundColor' href=javascript:void(0) onclick='del(this)'>删除</a>
                        </div>

                    <?php    }
                }


                ?>
            </td>

        </tr>
        <tr>
            <th></th><th>
               <input type="submit" value="更新" />
            </th><th></th>
        </tr>
    </table>
</form>

<?php include(TPL_DIR.'footer.php');?>



