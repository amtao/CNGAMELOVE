<?php include(TPL_DIR.'header.php');?>
<?php $_SESSION['zi_page'] = 4;?>
<?php include(TPL_DIR.'zi_header.php');?>

    <br />
    <div class="mytable">
    <a href='?sevid=<?php echo $SevidCfg['sevid'];?>&mod=config&act=ggConfig'>返回公告列表</a>
    </div>
    <br />
    <br />


    <form name="form2" id="form2" method="post" action="">
        <table style='width:100%;' class="mytable">

            <tr>
                <td style='text-align: right;'>编号：</td>
                <td><input type='text' size='40'  <?php if(isset($_GET['key'])){echo "readonly='readonly'";}?> id='id' name='id' value="<?php echo $show['key'];?>" /></td>
            </tr>
            <tr>
                <td style='text-align: right;'>top：</td>
                <td>
                    <input type='text' size='100'   id='header' name='top' value="<?php echo htmlspecialchars($show['top']);?>" />
                </td>
            </tr>
            <tr>
                <td style='text-align: right;'>header：</td>
                <td>
                    <input type='text' size='100'   id='header' name='header' value="<?php echo htmlspecialchars($show['header']);?>" />
                </td>
            </tr>
            <tr>
                <td style='text-align: right;'>title：</td>
                <td><input type='text' size='100'   id='title' name='title' value="<?php echo htmlspecialchars($show['title']);?>" /></td>
            </tr>
            <tr>
                <td style='text-align: right;'>body：</td>
                <td><textarea type='text' rows="20" cols="140"  id='body' name='body' ><?php echo $show['body'];?></textarea></td>
            </tr>
            <tr>
                <td colspan='2' align='center'>
                    <input type='hidden' id='save' name='save' value='save' />
                    <input type='submit' value='保存' />
                </td>
            </tr>

        </table>
    </form>


    <script>
    </script>
<?php include(TPL_DIR.'footer.php');?>