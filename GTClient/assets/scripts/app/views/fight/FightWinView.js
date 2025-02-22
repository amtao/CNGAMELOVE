var i = require("List");
var n = require("Utils");
var l = require("Initializer");
cc.Class({
    extends: cc.Component,
    properties: {
        list: i,
    },
    ctor() {},
    onLoad() {
        if(this.node.openParam && this.node.openParam.isff){
            this.list.data = l.timeProxy.itemReward
        }else{
            if (l.fightProxy.isBoss) {
                this.list.data = l.fightProxy.pvbData ? l.fightProxy.pvbData.items: null;
                l.fightProxy.isBoss = !1;
            } else this.list.data = l.fightProxy.pveData ? l.fightProxy.pveData.items: null;
        }
        //this.list.node.x = -this.list.node.width / 2 + 30;
        

        //动画监听
        // this.winSpine1.setCompleteListener((trackEntry) => {
        //     var animationName = trackEntry.animation ? trackEntry.animation.name : "";
        //     if (animationName === 'win_on') {
        //         this.winSpine1.animation = "win_idle";
        //         this.winSpine1.loop = true;
        //     }       
        // });       
    },

    start() {
        n.audioManager.playEffect("4", true, true);
    },

    onClickView() {
        if(this.node.openParam && this.node.openParam.isff){
            n.utils.closeView(this)
            facade.send("FIGHT_CLOST_WIN_VIEW");
            return
        }
        if (n.utils.closeView(this)) {
            facade.send("FIGHT_CLOST_WIN_VIEW");
            l.fightProxy.initSmapData();
            l.taskProxy.setDelayShow(!1);
        }
    },
});
