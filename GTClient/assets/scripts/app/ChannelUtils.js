var config = require("Config");

let ChannelUtils = function () {

    //渠道类型
    this.channelType = cc.Enum({
    	CHINA_ANDROID: 14,     //国服安卓
    	HONGKONG_ANDROID: 15,  //港澳台安卓
    	SINGAPORE_ANDROID: "android_sgp", //新加坡安卓
    	CHINA_IOS: 17,     //国服苹果
		HONGKONG_IOS: 18,  //港澳台苹果
		JUNHAI_H5:19,//君海H5
    });
};

ChannelUtils.prototype.init = function () {
    this.channelId = window.xygChannel;
    this.channelVer = window.xygVer;
};

//========================原生相关开始==============================
ChannelUtils.prototype.getChannelVer = function(){
	return this.channelVer;
};

ChannelUtils.prototype.isMobile = function(){
	return cc.sys.os == cc.sys.OS_ANDROID || cc.sys.os == cc.sys.OS_IOS;
};

//是否obb包（暂时用version来判断）
ChannelUtils.prototype.isObb = function(){
	this.channelId = window.xygChannel;
	this.channelVer = window.xygVer;
	
	return this.channelVer > 0 && this.isForeignAndroid();
	// return true;
};

//是否显示切换语言包按钮
ChannelUtils.prototype.isShowChangeLang = function(){
	return this.isChinaAndroid();
};

//新加坡安卓渠道
ChannelUtils.prototype.isSgpAndroid = function(){
	return this.channelId == this.channelType.SINGAPORE_ANDROID && cc.sys.os == cc.sys.OS_ANDROID;
};

//国服安卓
ChannelUtils.prototype.isChinaAndroid = function(){
	return this.channelId == this.channelType.CHINA_ANDROID && cc.sys.os == cc.sys.OS_ANDROID;
};

//港澳台安卓谷歌
ChannelUtils.prototype.isHongkongAndroid = function(){
	return this.channelId == this.channelType.HONGKONG_ANDROID && cc.sys.os == cc.sys.OS_ANDROID;
};

//海外安卓
ChannelUtils.prototype.isForeignAndroid = function(){
	this.channelId = window.xygChannel;
	this.channelVer = window.xygVer;
	return (this.channelId == this.channelType.SINGAPORE_ANDROID ||
		   this.channelId == this.channelType.HONGKONG_ANDROID) && cc.sys.os == cc.sys.OS_ANDROID;
	// return true;
};

ChannelUtils.prototype.isChinaIos = function(){
	return this.channelId == this.channelType.CHINA_IOS && cc.sys.os == cc.sys.OS_IOS;
};

ChannelUtils.prototype.isHongkongIos = function(){
	return this.channelId == this.channelType.HONGKONG_IOS && cc.sys.os == cc.sys.OS_IOS;
};

//========================原生相关结束==============================
//君海H5渠道
ChannelUtils.prototype.isJunHaiH5 = function(){
	return false;
	//return this.channelId == this.channelType.JUNHAI_H5;
};

window.ChannelUtils = new ChannelUtils();