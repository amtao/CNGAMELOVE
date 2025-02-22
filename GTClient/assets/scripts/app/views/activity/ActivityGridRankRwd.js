let List = require("List");
let Initializer = require("Initializer");
let Utils = require("Utils");

cc.Class({
    extends: cc.Component,
    properties: {
        list: List,
    },
    onLoad () {
        this.rankRwdData = this.node.openParam;
        if(this.rankRwdData && this.rankRwdData.rankRwd){
            let t = Math.ceil(this.rankRwdData.rankRwd[0].member.length / 6),
                e = 80 * t + 10 * (t - 1) + 65;
            this.list.setWidthHeight(550, e);
            this.list.data = this.rankRwdData.rankRwd;
        }
    },
    onClickClose() {
        Utils.utils.closeView(this);
    },
    onClickShowRankInfo(){
        if(this.rankRwdData && this.rankRwdData.info){
            Initializer.limitActivityProxy.sendRankInfo(1,this.rankRwdData.info.id,()=>{
                let rankData = Initializer.limitActivityProxy.geRankInfo(this.rankRwdData.info.id);
                rankData && Utils.utils.openPrefabView("activity/ActivityRankView", null,rankData);        
            });
        }
    }
});
