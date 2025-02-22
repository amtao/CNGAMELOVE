<?php
class PlatFormModel {
	
	public function __construct()
	{
		
	}
	
	// 加载平台配置
	public static function loadPlatFormCfg($platform) {
		if ( !file_exists(ROOT_DIR . '/config/platform/' . $platform . '_config.php') ) {
			throw new Exception($platform . ' config not exist', __LINE__);
		}
		require_once ROOT_DIR . '/config/platform/' . $platform . '_config.php';
	}

	// 加载所有平台配置
	public static function getPlatFormApiInstance($platform) {
		$platformConfig = self::loadPlatFormConfig();// 获取平台配置
		if ( !isset($platformConfig[$platform]) || 
				!file_exists($platformConfig[$platform]['classLib']) ) {
			Common::logMsg($logfile, sprintf('==== verify order 111fail (%s)====', __LINE__));
			throw new Exception($platform . ' config not exist', __LINE__);
		}
		// 加载平台类
		require_once $platformConfig[$platform]['classLib'];
		return Api::getInstance();
	}
	
	// 加载平台配置
	public static function loadPlatFormConfig() {
		$platformConfig = Common::getConfig('platform/platform_config');// 获取平台配置
		return (is_array($platformConfig)) ? $platformConfig : array();
	}
	
	// 加载后台查询所需的平台配置
	public static function loadPlatFormConfigForQueryByAdmin($admin='') {
		$hasSpecifyPlatform = false;// 是否需要判断指定平台
		$limit = $specifyPlatforms = array();
		$platformConfig = self::loadPlatFormConfig();// 获取所有接入平台配置
		$platformAdminCfg = Common::getConfigAdmin('platform_cfg');// 获取聚到可见的后台配置
		$query_data_level_cfg = (isset($platformAdminCfg['query_data_level_cfg'])) ? 
				$platformAdminCfg['query_data_level_cfg'] : array();
		//如果该用户 是受限用户
		if ( isset($query_data_level_cfg[$admin]['platforms']) 
				&& is_array($query_data_level_cfg[$admin]['platforms']) ) {
			$specifyPlatforms = $query_data_level_cfg[$admin]['platforms'];
			$hasSpecifyPlatform = true;
		}
		if ( isset($query_data_level_cfg[$admin]['orderType'])
				&& is_array($query_data_level_cfg[$admin]['orderType']) ) {
					$limit['orderType'] = $query_data_level_cfg[$admin]['orderType'];
					
				}
		if ( is_array($platformConfig) ) {
			foreach ($platformConfig as $k => $v) {
				if ( defined('IS_TEST_SERVER') && IS_TEST_SERVER ) {
					$limit['platforms'][$k] = $v;
				} elseif ( defined('AGENT_CHANNEL_ALIAS') && (AGENT_CHANNEL_ALIAS == $v['channel'] || AGENT_CHANNEL_ALIAS == 'MSJX' || (!empty($v['pass']) && AGENT_CHANNEL_ALIAS == $v['pass']))) {
					$limit['platforms'][$k] = $v;
					//如果是受限用户 去除不可见平台
					if ( $hasSpecifyPlatform && !in_array($k, $specifyPlatforms) ) {
						unset($limit['platforms'][$k]);
					}
				}
			}
		}
		return $limit;
	}

	// 返回支付的父类平台
	public static function getPayExtentPlatFormType($platform) {
		$platformConfig = self::loadPlatFormConfig();// 获取平台配置
		return isset($platformConfig[$platform]['extends']) ? $platformConfig[$platform]['extends'] : $platform;
	}
}