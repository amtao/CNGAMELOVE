var i = require("UserUpEffectItem");
var n = require("Utils");
var l = require("Initializer");
var r = require("UrlLoad");
cc.Class({
    extends: cc.Component,
    properties: {
        leftItem: i,
        rightItem: i,
        spine: sp.Skeleton,
        leftSpine: r,
        rightSpine: r,
        showSpine: r,
        nodeBg: cc.Node,
        nodeInfo: cc.Node,
        backgroundEffect: sp.Skeleton,
    },
    ctor() {},
    onLoad() {
        // this.backgroundEffect.animation = "idleBefore";
        this.nodeInfo.active = !1;
        this.onShowEffect();
    },
    onShowEffect() {
        var t = l.playerProxy.userData.level;
        //this.showSpine.setRoleLevel(t - 1);
        l.playerProxy.loadPlayerSpinePrefab(this.showSpine)
        this.scheduleOnce(this.delayShow, 0.6);
        //this.backgroundEffect.animation = "appear";
    },
    delayShow() {
        var t = l.playerProxy.userData.level;
        //this.showSpine.setRoleLevel(t);
        l.playerProxy.loadPlayerSpinePrefab(this.showSpine,{job:l.playerProxy.userData.job,level:t})
        //this.backgroundEffect.animation = "idleAfter";
        this.scheduleOnce(this.onHideSpine, 2.4);
    },
    onHideSpine() {
        this.nodeInfo.active = !0;
    },
    onClickClost() {
        n.utils.closeView(this);
        l.timeProxy.floatReward();
        let userClothe = l.playerProxy.userClothe;
        if (userClothe == null) return;
        let curLevel = l.playerProxy.userData.level;
        let nowData = localcache.getItem(localdb.table_officer, curLevel);
        let shizhuangCfg = localcache.getItem(localdb.table_roleSkin, nowData.shizhuang);
        let clothArr = shizhuangCfg.clotheid.split("|");
        let needChange = false;
        let tempMap = {};
        for (let clothId of clothArr){
            let cg = localcache.getItem(localdb.table_userClothe,clothId);
            if (cg != null){
                if (cg.part == 2 && Number(clothId) != userClothe.body){
                    needChange = true;
                    tempMap["body"] = Number(clothId)
                }
                else if(cg.part == 1 && Number(clothId) != userClothe.head){
                    needChange = true;
                    tempMap["head"] = Number(clothId)
                }
                else if(cg.part == 3 && Number(clothId) != userClothe.ear){
                    needChange = true;
                    tempMap["ear"] = Number(clothId)
                }
            }
        }
        if (needChange){
            var t = l.playerProxy.userClothe;
            n.utils.showConfirm(i18n.t("USER_CHANGE_CLOTHE"),
                function() {
                    var t = l.playerProxy.userClothe;
                    l.playerProxy.sendCloth(tempMap["head"], tempMap["body"], tempMap["ear"], t.background, t.effect, t.animal, !1);
                });
        }
    },
});
