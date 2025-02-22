<?php
$data = file_get_contents('proto.json');
$json = json_decode($data, true);
if(!empty($json)){
	foreach ($json as $key => $value){
    if ($value['p']){
        foreach ($value['p'] as $k => $v){
            if (substr($value['n'],0,2) != 'CS'){
                continue;
            }
            if (strlen($value['n'])>2){
                $dk = strtolower(substr($value['n'],3,strlen($value['n'])));
            }else{
                $dk = strtolower($value['n']);
            }
            $datas[$dk][$k] = $v[1];
        }
    }
}
$datainfo = var_export($datas ,true);
file_put_contents('msg_lang.php','<?php  return '.$datainfo. ';');
echo '成功';
}
