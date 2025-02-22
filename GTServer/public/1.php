<?php
error_reporting(E_ALL^E_NOTICE);
$str = $_GET['s'];
print_r(getHttpJson($str));

function getHttpJson($str)
{
    $codeArray = array(
        array('3','1','5','8','9','7','4','2','0','6'),
        array('0','5','3','2','1','7','9','4','8','6'),
        array('1','0','6','7','3','8','2','5','4','9'),
        array('6','1','5','4','2','9','0','3','8','7'),
        array('7','6','0','2','5','8','1','4','9','3'),
        array('6','5','3','4','0','2','8','1','7','9'),
        array('9','6','1','4','0','5','3','2','8','7'),
        array('8','9','3','1','5','7','0','6','4','2'),
        array('6','2','4','9','1','5','3','8','0','7'),
    );
    $lstr = strlen($str);

    if ($str[$lstr -1] == '}' && $str[0] == '{' && $str[$lstr-14] != '#'){
        $param = json_decode($str,1);
//            if (empty($param['user']['adok']) && empty($param['login']['loginAccount'])){
//                Master::error(LOGIN_SERVER_DELAY_ENTER_ERROR);
//            }
        return $param;
    }

    if ($str[$lstr-14] == '#'){
        $code = substr($str, -13);
        $context = substr($str, 0, -14);
        $codeLength = strlen($code);
        $contextLength = strlen($context);
        $randomL = intval($code[2]);
        $isUrlEncode = intval($code[1]);
        $randomYu = intval($code[0]);

        $time = "";
        for ($i = 3; $i < $codeLength; $i++){
            $index = intval($code[$i]);
            $time = $time.$codeArray[$randomL][$index];
        }



        $row = floor($contextLength/$randomYu);
        $remain = $contextLength - $randomYu * $row;
        $curRow = 0;
        $curLine = 0;
        $remainCount = 0;
        $s = "";

        for($i = 0; $i < $contextLength; $i++){
            $s = $s.$context[$curRow * $row + $curLine + $remainCount];
            $remainCount += $curRow < $remain?1:0;
            $curRow ++;
            if ($curRow * $row + $curLine + $remainCount >= $contextLength){
                $curLine++;
                $curRow = 0;
                $remainCount = 0;
            }
        }

        if ($isUrlEncode == 1){
            $s = urldecode($s);
        }
        return json_decode($s,1);
    }

    return null;
}