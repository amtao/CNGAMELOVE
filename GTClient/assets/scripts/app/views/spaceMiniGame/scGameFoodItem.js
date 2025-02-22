let UrlLoad = require("UrlLoad");
let UIUtils = require("UIUtils");

cc.Class({
    extends: cc.Component,

    properties: {
        btnSelf: cc.Button,
        nShow: cc.Node,
        spGuo: sp.Skeleton,
        urlItem: UrlLoad,
    },

    onLoad: function() {
        let self = this;
        this.spGuo.setCompleteListener((trackEntry) => {
            var animationName = trackEntry.animation ? trackEntry.animation.name : "";
            if (animationName === 'on') {
                if(self.foodNum == 2) {
                    self.scheduleOnce(() => {
                        self.target.turnFood.call(self.target);
                    }, 0.2);
                }
            } else if(animationName === 'off') {          
                self.spGuo.setAnimation(0, 'idle', true);
                self.btnSelf.interactable = true;
                if(self.foodNum == 2) {
                    self.target.recoverOver.call(self.target);
                }
                self.foodNum = null;
            } 
        });
    },

    setData(index, foodId, target) {
        this.node.active = true;
        this.foodNum = null;
        this.iIndex = index;
        this.foodId = foodId;
        this.target = target;
        let bShow = foodId > 0;
        this.nShow.active = bShow;
        this.btnSelf.interactable = true;
        this.urlItem.url = UIUtils.uiHelps.getItemSlot(foodId);
        if(bShow) {
            this.spGuo.setAnimation(0, 'idle', true);
        }
    },

    hide: function() {
        this.nShow.active = false;
        this.btnSelf.interactable = true;
        this.foodNum = null;
    },

    recover: function() {
        if(this.foodNum != null) {
            this.spGuo.setAnimation(0, 'off', false);
        }
    },

    onClickShow: function() {
        if(null != this.foodNum) {
            return;
        }
        let checkData = this.target.check(this.iIndex, this.foodId);
        if(checkData.bCan) {
            this.foodNum = checkData.num;
            this.btnSelf.interactable = false;
            this.spGuo.setAnimation(0, 'on', false);
        }
    },
});
