let scRenderItem = require("RenderListItem");
let Initializer = require("Initializer");
var UrlLoad = require("UrlLoad");
var Utils = require("Utils");

cc.Class({
    extends: scRenderItem,
    properties: {
        lbName: cc.Label,
        lblTime:cc.Label,
        nodeTongGuan:cc.Node,
        nodeWeiTongGuan:cc.Node,
        nodeJiBan:cc.Node,
        nodeUnLock:cc.Node,
        nodeSuo:cc.Node,
        nodeRed:cc.Node,
    },

    ctor() {},

    showData() {
        let t = this._data;
        if (t) { 
            this.lbName.string = t.appraise;
            this.lblTime.node.active = false;
            this.nodeTongGuan.active = false;
            this.nodeWeiTongGuan.active = false;
            this.nodeJiBan.active = false;
            this.nodeUnLock.active = false;
            this.nodeSuo.active = false;
            this.nodeRed.active = false;
            if (t.cardid == null || t.cardid == 0){
                this.nodeTongGuan.active = true;
            }
            else if(t.cardid == 1){
                this.nodeWeiTongGuan.active = true;
            }
            else{
                this.nodeJiBan.active = true;
            }
            if (Initializer.banchaiProxy.awardData != null && Initializer.banchaiProxy.awardData.pickInfo != null){
                let cData = Initializer.banchaiProxy.awardData.pickInfo;
                if (cData[t.endid] != null){
                    if (cData[t.endid].isPick == 0){
                        this.nodeRed.active = true;                    
                    }
                    this.lblTime.node.active = true;
                    this.lblTime.string = Utils.timeUtil.format(cData[t.endid].triTime,"yyyy-MM-dd");
                }
                else{
                    this.nodeUnLock.active = true;
                    this.nodeSuo.active = true;
                }             
            }
        }
    },

    onClick(){
        Utils.utils.openPrefabView("banchai/UIBanChaiStoryReward",null,this._data);
    },

});
