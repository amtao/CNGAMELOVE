
var utlis = require("Utils");
var initializer = require("Initializer");
var settlementItem = require("drawSettlementItem");
var list = require("List");

cc.Class({
    extends: cc.Component,

    properties: {
        oneCard: cc.Node,
        tenCard: cc.Node,
        settlementItem: settlementItem,
        onceAnimation: cc.Animation,
        oneTip: cc.Node,
        tenTip: cc.Node,
        listcard: list,
        listitems: list,
        btnClose: cc.Button,
        spine:sp.Skeleton,
        nodeCard:cc.Node,
        spineH:sp.Skeleton,
    },

    // LIFE-CYCLE CALLBACKS:
    ctor(){
        this.data = null;
        this.currentIdx = 0;
        this.listdata1 = [];
        this.listdata2 = [];
    },

    onLoad () {
        this._inParams  = this.node.openParam;
        this.spine.node.active = false;
        this.spineH.node.active = false;
        this.spine.setCompleteListener((trackEntry) => {
            var animationName = trackEntry.animation ? trackEntry.animation.name : "";
            if (animationName == "on"){
                this.spine.animation = "off";
                this.playOneCardAni();
            }
            // else if (animationName === 'off') {
            //     this.playOneCardAni();
            // }    
        });
        this.spineH.setCompleteListener((trackEntry) => {
            var animationName = trackEntry.animation ? trackEntry.animation.name : "";
            if (animationName == "on"){
                this.spineH.animation = "off";
            }
            else if (animationName === 'off') {
                this.playTenCardAni();
            }       
        });
        var data = initializer.drawCardProxy.getGainCardArray();
        this.data = data;
        if (data.length === 0) return;
        if (data.length > 1) {
            // 十连抽
            this.oneCard.active = false;
            this.tenCard.active = false;
            this.listdata1 = [];
            this.listdata2 = [];
            for (var ii = 0;ii < data.length;ii++){
                var hh_ = data[ii];
                if (hh_.state && hh_.state == 1){
                    this.listdata1.push(hh_);
                }
                else{
                    if (hh_.state != null){
                        var cfg = localcache.getItem(localdb.table_card, hh_.id);
                        if (cfg){
                            this.listdata2.push({kind:99,id:cfg.item,count:1});
                        }
                    }
                    else{
                        this.listdata2.push(hh_);
                    }                  
                }
            }
            this.currentIdx = 0;           
            if (!initializer.cardProxy.isSkipCardEffect){
                this.spineH.node.active = true;
                this.spineH.animation = "on";
                this.spineH.loop = false;
            }
            else{
                this.playTenCardAni();
            }
            
        } else {
            // 单抽
            this.oneCard.active = true;
            this.tenCard.active = false;
            this.isCanClose = false;
            this.settlementItem.data = data[0];
            this.oneTip.active = initializer.drawCardProxy.checkCardChip(data);
            if (!initializer.cardProxy.isSkipCardEffect){
                this.spine.node.active = true;
                this.spine.animation = "on";
                this.spine.loop = false;
            }
            else{
                this.playOneCardAni();
            }        
        }
        utlis.audioManager.playEffect("7", true, true);
    },

    playOneCardAni(){
        let self = this;
        self.onceAnimation.on("finished", () => {
            self.btnClose.target = self.btnClose.node;
            self.isCanClose = true;
            self.spine.node.active = false;
        });
        self.onceAnimation.play();
    },

    playTenCardAni(){
        // if (!initializer.cardProxy.isSkipCardEffect && this.currentIdx < this.listdata1.length){
        //     this.spine.node.active = true;
        //     this.oneCard.active = false;
        //     this.spine.animation = "on1";
        //     this.spine.loop = false;
        //     return;
        // }
        this.spineH.node.active = false;
        let data = this.data;
        this.oneCard.active = false;
        this.tenCard.active = true;
        if (this.listdata1.length <= 0){
            this.listcard.node.active = false;
        }
        else{
            this.listcard.node.active = true;
            this.listcard.data = this.listdata1;
        }
        if (this.listdata2.length <= 0){
            this.listitems.node.active = false;
        }
        else{
            this.listitems.node.active = true;
            this.listitems.data = this.listdata2;
        }

        this.tenTip.active = initializer.drawCardProxy.checkCardChip(data);
        this.btnClose.target = this.btnClose.node;
        this.isCanClose = true;

    },

    playSingleAni(){
        this.nodeCard.opactity = 0;
        this.oneCard.active = true;
        this.tenCard.active = false;
        let data = this.listdata1;
        this.settlementItem.data = data[this.currentIdx];
        this.currentIdx++;
        let self = this;
        self.onceAnimation.on("finished", () => {
            self.spine.node.active = false;
            self.unscheduleAllCallbacks();
            self.scheduleOnce(()=>{
                self.playTenCardAni();
            },0.5)
            
        });
        self.onceAnimation.play();
    },

    jumpOver () {
        var animationState = this.onceAnimation.play();
        if (animationState) {
            animationState.time = 2;
            animationState.sample();
        }
        this.btnClose.target = this.btnClose.node;
        this.isCanClose = true;
        facade.send("DRAW_CARD_OVER");
    },

    onClickClose() {
        if (!this.isCanClose) {
            //this.jumpOver();
            return;
        }     
        initializer.drawCardProxy.clearSettlementData();
        facade.send("DRAW_CARD_OVER");
        utlis.utils.closeView(this);
    },
    // update (dt) {},
});
