<?php
/**
 * 开关模型
 * Class SwitchModel
 */
class SwitchModel
{
    public static function isKuaRankOpen()
    {
        $gq_status = Game::get_peizhi('gq_status');
        return isset($gq_status['isKuaRankOpen']) && $gq_status['isKuaRankOpen'] == 1;
    }
    /*
     * 获取红颜技能等级上限
     */
    public static function getWifeSkillMaxLevel($type){
    	switch($type){
    		case 1:
    			$key = 'wifeSkillLevalMax1';
    			$dft_level = 300;
    			break;
    		case 2:
    			$key = 'wifeSkillLevalMax2';
    			$dft_level = 200;
    			break;
    		default:
    			return 0;
    	}
    	$max_level = Game::get_gq_status($key);
    	if (empty($max_level)){
    		return $dft_level;
    	}
    	return $max_level;
    }
    
}