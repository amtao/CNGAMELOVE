<?php include(TPL_DIR.'header.php');?>
<?php $_SESSION['zi_page'] = 3;?>
<?php include(TPL_DIR.'zi_header.php');?>
<script type="text/javascript">
	function checkForm(){
		if(document.rechargeSearch.startTime.value.length == 0){
			if(document.rechargeSearch.endTime.value.length > 0 ){
				alert("请选择时间范围");
				document.rechargeSearch.startTime.focus();
				return false;
			}
		}
		if(document.rechargeSearch.startTime.value.length > 0){
			if(document.rechargeSearch.endTime.value.length == 0 ){
				alert("请选择时间范围");
				document.rechargeSearch.endTime.focus();
				return false;
			}
		}
		if( (document.rechargeSearch.startTime.value.length > 0) && (document.rechargeSearch.endTime.value.length > 0 )){
			var startTime = document.rechargeSearch.startTime.value; 
			var startTimeJsArray = startTime.split("-");
			var startTimeJsMake = new Date(startTimeJsArray[0],startTimeJsArray[1],startTimeJsArray[2]);
			var startTimeJs = startTimeJsMake.getTime();

			var endTime = document.rechargeSearch.endTime.value; 
			var endTimeJsArray = endTime.split("-");
			var endTimeJsMake = new Date(endTimeJsArray[0],endTimeJsArray[1],endTimeJsArray[2]);
			var endTimeJs = endTimeJsMake.getTime();

			if( endTimeJs < startTimeJs ){
				alert("结束时间不能小于起始时间,请重新输入");
				document.rechargeStat.endTime.focus();
				return false;     
			}	
		}
		return true;
	}


    //排序 tableId: 表的id,iCol:第几列 ；dataType：iCol对应的列显示数据的数据类型
    function sortAble(th, tableId, iRow, iCol, dataType) {

        var ascChar = "▲";
        var descChar = "▼";

        var table = document.getElementById(tableId);
        var rows = table.tHead.rows;
        //排序标题加背景色
        for (var i = 0; i < rows.length; i++) {
            for(var j=0;j<rows[i].cells.length;j++){//取得第几行下面的td个数，再次循环遍历该行下面的td元素
                var th = rows[i].cells[j];
                var thText= th.innerHTML.replace(ascChar, "").replace(descChar, "");
                if(i==iRow&&j==iCol){
                }
                else{
                    th.innerHTML=thText;
                }
            }
        }

        var tbody = table.tBodies[0];
        var colRows = tbody.rows;
        var aTrs = new Array;

        //将得到的行放入数组，备用
        for (var i = 0; i < colRows.length; i++) {
            aTrs.push(colRows[i]);
        }


        //判断上一次排列的列和现在需要排列的是否同一个。
        if (table.sortCol == iCol) {
            aTrs.reverse();
        } else {
            //如果不是同一列，使用数组的sort方法，传进排序函数
            aTrs.sort(compareEle(iCol, dataType));
        }

        var oFragment = document.createDocumentFragment();
        for (var i = 0; i < aTrs.length; i++) {
            oFragment.appendChild(aTrs[i]);
        }
        tbody.appendChild(oFragment);

        //记录最后一次排序的列索引
        table.sortCol = iCol;

        //给排序标题加“升序、降序” 小图标显示
        var th = rows[iRow].cells[iCol];

        if (th.innerHTML.indexOf(ascChar) == -1 && th.innerHTML.indexOf(descChar) == -1) {
            th.innerHTML += ascChar;
            //alert(th.innerHTML);
        }
        else if (th.innerHTML.indexOf(ascChar) != -1) {
            th.innerHTML=th.innerHTML.replace(ascChar, descChar);
            //alert(th.innerHTML.replace(ascChar,descChar));

        }
        else if (th.innerHTML.indexOf(descChar) != -1) {
            th.innerHTML=th.innerHTML.replace(descChar, ascChar);
        }


    }

    //将列的类型转化成相应的可以排列的数据类型
    function convert(sValue, dataType) {
        switch (dataType) {
            case "int":
                return parseInt(sValue, 10);
            case "float":
                return parseFloat(sValue);
            case "date":
                return new Date(Date.parse(sValue));
            case "string":
            default:
                return sValue.toString();
        }
    }

    //排序函数，iCol表示列索引，dataType表示该列的数据类型
    function compareEle(iCol, dataType) {
        return function (oTR1, oTR2) {

            var vValue1 = convert(removeHtmlTag($(oTR1.cells[iCol]).html()), dataType);
            var vValue2 = convert(removeHtmlTag($(oTR2.cells[iCol]).html()), dataType);
            if (vValue1 < vValue2) {
                return -1;
            }
            else {
                return 1;
            }

        };
    }

    //去掉html标签
    function removeHtmlTag(html) {
        return html.replace(/<[^>]+>/g, "");
    }

</script>
<hr class="hr"/>
<form id="form-search" method="POST" action="" >
<table style="width: 100%">
    <tr><th colspan="4">消费统计</th></tr>
    <tr >
        <th style="text-align:right;width: 50px;">类型：</th>
        <td style="text-align:left;">
            <select name="from" id="form">
                <option value="">选择类型</option>
                <?php foreach ($from as $key => $value):?>
                    <option value="<?php echo $key; ?>" <?php if($_POST['from'] == $key) echo "selected";?>><?php echo $value; ?></option>
                <?php endforeach;?>
            </select>
        </td>
        <th style="text-align:right;width: 50px;">时间：</th>
        <td style="text-align:left;">
            <input class='Wdate' type='text' size='40' id='startTime' name='startTime'
                   value='<?php echo (empty($startTime)) ? date('Y-m-d 00:00:00') : date('Y-m-d H:i:s', $startTime);?>'
                   onFocus="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm:ss',isShowClear:false,readOnly:true,
						maxDate:'#F{$dp.$D(\'endTime\')}'})" />
            &nbsp;&nbsp;&nbsp;~&nbsp;&nbsp;&nbsp;

            <input class='Wdate' type='text' size='40' id='endTime' name='endTime'
                   value='<?php echo (empty($endTime)) ? date('Y-m-d 23:59:59') : date('Y-m-d 23:59:59', $endTime);?>'
                   onFocus="WdatePicker({dateFmt:'yyyy-MM-dd HH:mm:ss',isShowClear:false,readOnly:true,
						minDate:'#F{$dp.$D(\'startTime\')}'})" />
        </td>
        </td>
    </tr>
    <tr>
    <th style="text-align:right;width: 50px;">区服：</th>
    <td style="text-align:left;" colspan="3">
        <input style="margin-top:3px;" type="button" value="全选" data-btn="all" />
        <input style="margin-top:3px;" type="button" value="全不选" data-btn="delAll" />
        <input style="margin-top:3px;" type="button" value="奇选" data-btn="ji" />
        <input style="margin-top:3px;" type="button" value="偶选" data-btn="ou" />
        </br><hr class="hr"/>
        <?php foreach ($serverList as $key => $value):?>
            <?php if ($value['id']==999){continue;}?>
            <input type="checkbox" data-btn="check" name="server[]" <?php if (!empty($_POST['server']) && in_array($value['id'], $_POST['server'])){ echo 'checked="checked"';}; ?> value="<?php echo $value['id']; ?>">  <?php echo $value['id']; ?> 服<span style="color: #97c6ff"> | </span><?php if ($value['id']%20 == 0){echo '<br/>';}; ?>
        <?php endforeach;?>
    </td>
    </tr>
	 <tr><th colspan="4"><input type="submit" value="确定查询" /></th></tr>
</table>
</form>
<BR>

<hr class="hr" />
<table style="width: 100%" id="tableId">
    <thead>
        <tr>
            <th colspan="4">消费统计</th>
        </tr>
        <tr>
            <th>消费类型</th>
            <th onclick="sortAble(this,'tableId',1, 1, 'int')" style="cursor:pointer">消费人数▼</th>
            <th>占比</th>
            <th>参与率</th>
        </tr>
    </thead>
    <tbody>
        <?php if(!empty($consume)){  ?>
            <?php foreach ($consume as $key => $value){
                echo '<tr style="background-color:#f6f9f3;">';
                echo '<td style="text-align:center;">'.$value['name'].'</td>';
                echo '<td style="text-align:center;">'.$value['num'].'</td>';
                echo '<td style="text-align:center;">'.number_format($value['num']*100/$nums, 2).'%</td>';
                if (empty($loginNum[0]['num'])){
                    echo '<td style="text-align:center;"> 0 </td>';
                }else{
                    echo '<td style="text-align:center;">'.number_format($value['num']*100/$loginNum[0]['num'], 2).'%</td>';
                }

                echo '</tr>';
                } ?>
        <?php } ?>
    </tbody>
    <th style="text-align:center;">总计</th>
    <td style="text-align:center;"><?php echo $nums; ?></td>
    <td style="text-align:center;"><?php echo 100; ?>%</td>
    <td style="text-align:center;"><?php  if (empty($loginNum[0]['num'])){echo 0;}else{echo number_format($nums*100/$loginNum[0]['num'], 2).'%';} ?></td>
</table>
<br>
<div class="hero_div">
    <?php include(TPL_DIR.'footer.php');?>
</div>
<script>
    $(document).ready(function () {
        $('[data-btn="all"]').click(function () {
            $('[data-btn="check"]').prop('checked','checked');
        });
        $('[data-btn="delAll"]').click(function () {
            $('[data-btn="check"]').prop('checked','');
        });
        $('[data-btn="ji"]').click(function () {
            $('[data-btn="check"]').each(function () {
                var v = $(this).val();
                if (v%2 != 0){
                    $(this).prop('checked','checked');
                }else {
                    $(this).prop('checked','');
                }
            });
        });
        $('[data-btn="ou"]').click(function () {
            $('[data-btn="check"]').each(function () {
                var v = $(this).val();
                if (v%2 == 0){
                    $(this).prop('checked','checked');
                }else {
                    $(this).prop('checked','');
                }
            });
        });
    });
</script>