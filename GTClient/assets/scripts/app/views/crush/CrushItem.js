
let UrlLoad = require("UrlLoad");
let UIUtils = require("UIUtils");
let Utils = require("Utils");

cc.Class({
    extends: cc.Component,

    properties: {
        itemUrl: UrlLoad,
        effect: sp.Skeleton,
        shootBossEffect: cc.Node,
        hint: sp.Skeleton,
        floor: cc.Node,
        floor2: cc.Node,
        bossNode: cc.Component,
    },

    onLoad(){
        let self = this;
        this.effect && this.effect.setCompleteListener(() => {
            self.effect.node.active = false;
        });
    },

    initEffectNode() {
        this.effect.node.active = false;
        this.itemUrl.node.active = false;
    },

    showSelect(bShow) {
        this.floor2.active = bShow;
    },

    onShootBoss() {
        let effctNode = this.shootBossEffect;
        effctNode.stopAllActions();
        effctNode.setPosition(0, 0);
        effctNode.active = true;
        let bossPos = this.bossNode.node.parent.convertToWorldSpaceAR(new cc.Vec2(200, 50));
        let endPos = effctNode.parent.convertToNodeSpaceAR(bossPos);
        UIUtils.uiUtils.moveNodeAction(effctNode, endPos, 0.5, ()=>{
            UIUtils.uiUtils.showShake(this.bossNode);
            Utils.audioManager.playEffect("5", true, true);
            effctNode.active = false;
            effctNode.stopAllActions();
            effctNode.setPosition(0, 0);
        });
    },

    hideChessAni(cb){
        this.itemUrl.node.runAction(cc.fadeTo(1,0));
    },

    resetChessAni(){
        this.itemUrl.node.stopAllActions();
        let time = Math.random() * 1+1;
        this.itemUrl.node.runAction(cc.fadeTo(time,255));
    },

    showHint(convert) {
        if(this.hint) {
            if(convert == 0) {
                this.hint.setAnimation(1, 'shangxia', true);
            } else {
                this.hint.setAnimation(1, 'zuoyou', true);
            }
        }
    },

    //设置item图片
    initCrushItem(frameName) {
        this.effect.node.active = false;
        this.itemUrl.node.active = true;
        this.itemUrl.url = (UIUtils.uiHelps.getCrushIconPath()+frameName);
        this.itemUrl.node.scaleX = 1;
        this.itemUrl.node.scaleY = 1;
    },

    initCrushFloor(frameName) {
        this.effect.node.active = false;
        this.floor.active = true;
        this.floor2.active = false;
        this.itemUrl.node.active = true;
        // this.itemUrl.url = (UIUtils.uiHelps.getCrushIconPath()+frameName);
    },

    resetFloorState(posi, posj, map) {
        return;
    },

    //item被销毁需要隐藏
    emptyCrushItem(combo, num) {
        this.itemUrl.node.scaleX = 1.1;
        this.itemUrl.node.scaleY = 1.1;
        this.itemUrl.node.active = true;
        this.scheduleOnce(() => {
            this.itemUrl.node.scaleX = 1;
            this.itemUrl.node.scaleY = 1;
            this.itemUrl.node.active = false;
            this.effect.node.active = true;
            if(num < 3) { //增加保护
                num = 3;
            } else if(num > 5) {
                num = 5;
            }
            if(this.effect.findAnimation("xiao" + num)) {
                this.effect.setAnimation(1, "xiao" + num, false);
            }   
            let soundIndex = combo > 3 ? 3 : combo;
            Utils.audioManager.playSound("crush" + soundIndex, !0);
        }, 0.2);
    }
});
