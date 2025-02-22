var Utils = require("Utils");
let List = require("List");
let UrlLoad = require("UrlLoad");
let Initializer = require("Initializer");
let ItemSlotUI = require("ItemSlotUI");
cc.Class({
    extends: cc.Component,
    properties: {
    	lblTitle:cc.Label,
    	lblCanGet:cc.Label,
    	lblGot:cc.Label,
    	listItem:List,
    	rewardItem:ItemSlotUI,
    	lblRewardNum:cc.Label,
    },
    ctor() {
        
    },
    onLoad() {
    	let data = Initializer.unionProxy.redBagData;
    	//this.listItem.data = data.robList;
    	
    	for (let key in data.robList){
    		this.lblTitle.string = i18n.t("UNION_TIPS45",{v1:data.robList[key].name});
    		this.listItem.data = data.robList[key].list;
    		let redlist = data.redlist[key];
    		if (redlist == null){
    			this.lblGot.string = i18n.t("UNION_TIPS46",{v1:data.robList[key].list.length,v2:data.robList[key].list.length})
    		}
    		else{
    			this.lblGot.string = i18n.t("UNION_TIPS46",{v1:data.robList[key].list.length,v2:data.redlist[key].items.length})
    		}    		
    	}
    	let sum = 0;  	
    	for (let key in data.robLog){
    		let cg = data.robLog[key]
    		for (let ii = 0; ii < cg.length; ii++){
    			if (cg[ii].uid == Initializer.playerProxy.userData.uid){
    				sum++;
    			}
    		}
    	}
        let maxNum = Utils.utils.getParamInt("club_giftTimes");
    	this.lblCanGet.string = i18n.t("COMMON_NUM",{f:maxNum - sum,s:maxNum})
    	let reward = Initializer.timeProxy.itemReward;
    	if (reward && reward[0]){
    		this.lblRewardNum.string = `x${reward[0].count}`;
    		reward[0].count = 1;
    		this.rewardItem.data = reward[0];
    	}
    },
    eventClose() {
        Utils.utils.closeView(this);
    },

    onDestroy(){
    	Initializer.timeProxy.itemReward = null;
    }
});