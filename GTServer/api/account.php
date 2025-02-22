<?php
/**
 * 
 * 处理自有账户的业务类
 * @author wenyj
 *
 */
class accountMod {
	// 绑定账户
	public function bindAccount($params) {
		// TODO 平台验证
		$platform = strtolower(trim($_REQUEST['platform']));
		try {
			Common::loadModel('PlatFormModel');
			PlatFormModel::loadPlatFormCfg($platform);
			$Api = PlatFormModel::getPlatFormApiInstance($platform);
			if ( !$Api->bindAccount($params) ) {
				return array(
					0 => 0,// 失败：0，成功：1
					1 => sprintf('%s(%s)', 'error_bind_account', __LINE__),
				);
			}
		} catch (Exception $e) {
			return array(
				0 => 0,// 失败：0，成功：1
				1 => sprintf('%s(%s_%s)', 'error_bind_account', $e->getMessage(), __LINE__),
			);
		}
		
		//返回用户ID信息
		return array(
			0 => 1,
		);
	}
	
	// 忘记密码
	public function forgotPassword($params) {
		// TODO 平台验证
		$platform = strtolower(trim($_REQUEST['platform']));
		try {
			Common::loadModel('PlatFormModel');
			PlatFormModel::loadPlatFormCfg($platform);
			$Api = PlatFormModel::getPlatFormApiInstance($platform);
			if ( !$Api->forgotPassword($params) ) {
				return array(
					0 => 0,// 失败：0，成功：1
					1 => sprintf('%s(%s)', 'error_reset_password', __LINE__),
				);
			}
		} catch (Exception $e) {
			return array(
				0 => 0,// 失败：0，成功：1
				1 => sprintf('%s(%s_%s)', 'error_reset_password', $e->getMessage(), __LINE__),
			);
		}
		
		//返回用户ID信息
		return array(
			0 => 1,
		);
	}
}