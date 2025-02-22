<?php 
/**
 * 
 * 使用simplexml方法构建xml
 * @author wenyj
 * @version
 * 		+ 20140401
 *
 */
class SimpleXmlClass {
	private $_xmlHeader = '<?xml version=\'1.0\' encoding=\'utf-8\' standalone=\'yes\' ?>';
	private $_xmlBody = '';
	private $_xmlAttributeConfig = array();
	private $_topNote = 'request';
	private $_xmlObject;

	public function __construct($xmlConfig = array()) {
		if ( isset($xmlConfig['topNote']) ) {
			$this->setTopNote($xmlConfig['topNote']);
		}
		if ( isset($xmlConfig['attribute']) ) {
			$this->setAttributeConfig($xmlConfig['attribute']);
		}
		$this->init();
	}

	public function SimpleXmlClass($xmlConfig = array()) {
		$this->__construct($xmlConfig);
	}

	public function setTopNote($notename) {
		$this->_topNote = $notename;
	}

	public function setAttributeConfig($attributeconfig) {
		$this->_xmlAttributeConfig = $attributeconfig;
	}

	public function getXmlBody() {
		return $this->_xmlBody;
	}

	public function setXmlBody($xmlConfig) {
		$this->addChild($this->_xmlObject, $xmlConfig, $this->_xmlAttributeConfig);
		$this->_xmlBody = $this->_xmlObject->asXML();
	}

	public function init() {
		$this->_xmlBody = $this->_xmlHeader . '<' . $this->_topNote . '></' . $this->_topNote . '>';
		$this->_xmlObject = simplexml_load_string($this->_xmlBody);
		if ( false == is_object($this->_xmlObject) ) {
			return new Exception('new xmlObject Fail', __LINE__);
		}
	}

	/**
	 * 
	 * 生成子节点
	 * @param unknown_type $leftLeaf, 上级叶子节点
	 * @param unknown_type $noteConfig, 节点配置
	 * @param unknown_type $attributeConfig, 节点属性配置
	 */
	public function addChild(&$leftLeaf, $noteConfig, $attributeConfig=array()) {
		if ( is_array($noteConfig) ) {
			foreach ($noteConfig as $noteName => $data) {
				if (is_array($data)) {
					$rightLeaf = $leftLeaf->addChild($noteName);
					$this->addChild($rightLeaf, $data, $attributeConfig);
				} else {
					$rightLeaf = $leftLeaf->addChild($noteName, $data);
				}
				if (isset($attributeConfig[$noteName]) && is_array($attributeConfig[$noteName])) {
					foreach ($attributeConfig[$noteName] as $akey => $aval) {// 设置属性值
						$rightLeaf->addAttribute($akey, $aval);
					}
				}
			}
		}
	}

}