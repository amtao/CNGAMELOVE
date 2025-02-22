//9-17
//郊游副本守护宝箱
let Initializer = require("Initializer");
let Utils = require("Utils");
cc.Class({
    extends: cc.Component,

    properties: {
        openBox:cc.Node,
        closeBox:cc.Node,
        progress:cc.ProgressBar,
        ctLbl:cc.Label,
        boxId:1,
        boxEftNode:cc.Component,
        nodeRed:cc.Node,
    },
    onLoad () {

    },

    start () {
        this.onShow()

        facade.subscribe("ON_JIAOYOU_INFO", this.onShow, this);
        facade.subscribe("REFRESH_JIAOYOU_GUARD", this.onShow, this);
    },

    onShow(){
        let boxCfg = localcache.getItem(localdb.table_jiaoyouWeek,this.boxId)

        this.isOpen = Initializer.jiaoyouProxy.weekAwardPick.indexOf(this.boxId) >= 0

        this.openBox.active = this.isOpen
        this.closeBox.active = !this.isOpen

        if(this.boxId - 1 > 0){
            let boxCfgBack = localcache.getItem(localdb.table_jiaoyouWeek,this.boxId-1)
            this.progress.progress = (Initializer.jiaoyouProxy.weekdefendCount - boxCfgBack.cishu)/ (boxCfg.cishu - boxCfgBack.cishu)
        }else{    
            this.progress.progress = Initializer.jiaoyouProxy.weekdefendCount/boxCfg.cishu
        }
        
        this.ctLbl.string = boxCfg.cishu
        this.nodeRed.active = false;
        if(!this.isOpen && this.progress.progress >= 1){
            Utils.utils.showEffect(this.boxEftNode, 0, () => {});
            this.nodeRed.active = true;
        }else{
            Utils.utils.stopEffect(this.boxEftNode, 0, () => {});
        }
    },

    onClickBox(){
        if(this.closeBox.active && this.progress.progress >= 1){
            Initializer.jiaoyouProxy.snedPickGuardWeekAward(this.boxId)
        }
        else{
            let boxCfg = localcache.getItem(localdb.table_jiaoyouWeek,this.boxId)
            Utils.utils.openPrefabView("jiaoyou/JiaoyouAwardDetail",null,{listdata:boxCfg.jiangli});
        }
    }
});
