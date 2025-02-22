<?php include(TPL_DIR.'header.php');?>
<?php $_SESSION['zi_page'] = 5;?>
<?php include(TPL_DIR.'zi_header.php');?>

<hr class="hr" />
<form name="form" method="post" action="">
    <table>
        <tr><th colSpan="2">包版本管理</th></tr>
        <tr><th style="width: 40%">版本ID*</th><td><?php echo $versionInfo['id'];?></td></tr>
        <tr><th style="width: 40%">渠道标识*</th><td><input type="text" name="channel_id" style="width: 300px;" size="120" id="channel_id"  value="<?php echo $versionInfo['channel_id']?>"></td></tr>
        <tr><th style="width: 40%">包版本*</th><td><input type="text" name="base_ver" style="width: 300px;" size="120" id="base_ver"  value="<?php echo $versionInfo['base_ver']?>"></td></tr>
        <tr><th style="width: 40%">热更新地址*</th><td><input type="text" name="cdn_path" style="width: 300px;" size="120" id="cdn_path"  value="<?php echo $versionInfo['cdn_path']?>"></td></tr>
        <tr><th style="width: 40%">强制更新*</th><td><input type="text" name="is_constraint" style="width: 300px;" size="120" id="is_constraint"  value="<?php echo $versionInfo['is_constraint']?>"></td></tr>
        <tr><th style="width: 40%">强制更新地址*</th><td><input type="text" name="constraint_path" style="width: 300px;" size="120" id="constraint_path"  value="<?php echo $versionInfo['constraint_path']?>"></td></tr>
        <tr><th style="width: 40%">生产服版本*</th><td><input type="text" name="all_version" style="width: 300px;" size="120" id="all_version"  value="<?php echo $versionInfo['all_version']?>"></td></tr>
        <tr><th style="width: 40%">白名单版本*</th><td><input type="text" name="white_version" style="width: 300px;" size="120" id="white_version"  value="<?php echo $versionInfo['white_version']?>"></td></tr>
        <tr><th style="width: 40%">服务器列表地址*</th><td><input type="text" name="server_list_url" style="width: 300px;" size="120" id="server_list_url"  value="<?php echo $versionInfo['server_list_url']?>"></td></tr>
        <tr><th style="width: 40%">是否提审加密*</th><td><input type="text" name="is_ts" style="width: 300px;" size="120" id="is_ts"  value="<?php echo $versionInfo['is_ts']?>"></td></tr>
        <tr><th colspan="2"><input type="submit" class="input" value="<?php echo $versionInfo['id'] > 0 ? '修改' : '新增'?>" /></th></tr>
    </table>
    <input type="hidden" value="update" name="type"></input>
    <input type="hidden" value="<?php echo $versionInfo['id'];?>" name="id"></input>
</form>

<div class="hero_div">
    <?php include(TPL_DIR.'footer.php');?>
</div>