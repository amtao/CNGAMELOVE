let Utils = require("Utils");
let UrlLoad = require("UrlLoad");
let UIUtils = require("UIUtils");
let Initializer = require("Initializer");
/**
 * 已经获得的卡牌详情
 */
cc.Class({
    extends: cc.Component,

    properties: {
        lbName:cc.Label,//名字
        spCard: UrlLoad,//卡图
        frameImg: UrlLoad,
        spParam:[UrlLoad],//属性背景图
        lbParamTxt: [cc.Label],
        lbParam:[cc.Label],//属性文本
        tagBG:UrlLoad,//
        tagSpine: sp.Skeleton,
        lbQuality: cc.Label,
        effectNode: cc.Node,
    },
    onLoad(){
        this.cfgData = this.node.openParam;
        this.initCardDetail();
    },
    onClickBack() {
        Utils.utils.closeView(this);
    },

    onClickToDrawCard(){
        Utils.utils.openPrefabView("draw/drawMainView", null);
        this.onClickBack();
        facade.send(Initializer.cardProxy.JUMP_DRAW_CARD);
    },
    initCardDetail(){
        //名字
        this.lbName.string = this.cfgData.name;
        //tag
        this.tagBG.url = UIUtils.uiHelps.getCardTagFrame(this.cfgData.quality);
        // if(this.cfgData.quality == 4){
        //     this.tagBG.node.active = false;
        //     this.tagSpine.node.active = true;
        //     this.tagSpine.setAnimation(1,'animation2',true);
        // }else if(this.cfgData.quality == 3){
        //     this.tagBG.node.active = false;
        //     this.tagSpine.node.active = true;
        //     this.tagSpine.setAnimation(1,'animation',true);
        // }else{
        //     this.tagSpine.node.active = false;
        //     this.tagBG.node.active = true;
        // }
        //卡图片
        if(this.cfgData.picture == 'card_tc_003' || this.cfgData.picture == 'card_tc_002'){
            this.spCard.url = UIUtils.uiHelps.getTianCiCardEffect(this.cfgData.picture);
        }else{
            this.spCard.url = UIUtils.uiHelps.getCardFrame(this.cfgData.picture);
        }
        //this.frameImg.url = UIUtils.uiHelps.getQualityFrame(this.cfgData.quality, 1);
        this.lbQuality.string = i18n.t("XINDONG_QUALITY_" + this.cfgData.quality);
        switch(this.cfgData.quality){
            // case 1:
            // case 2:{
            //     this.frameImg.node.width = 520;
            //     this.frameImg.node.height = 800;
            //     this.frameImg.node.x = 0;
            // }break;
            case 3:
                this.effectNode.active = true;
            case 4:{
                // this.frameImg.node.width = 540;
                // this.frameImg.node.height = 820;
                // this.frameImg.node.x = -10;
                this.effectNode.active = true;
            }break;
        }
        //属性
        this.showProp();
    },
    showProp () {
        for(let i = 1; i <= 4; i++){
            let index = i - 1;
            this.spParam[index].url = UIUtils.uiHelps.getLangSp(i);
            let paramBaseValue = this.cfgData['ep' + i];
            this.lbParam[index].string = "" + Math.floor(paramBaseValue);
            this.lbParamTxt[index].string = UIUtils.uiHelps.getPinzhiStr(i);
        }
    }
});
