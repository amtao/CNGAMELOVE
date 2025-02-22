let UrlLoad = require("UrlLoad");
let UIUtils = require("UIUtils");
let renderItem = require("RenderListItem");
let Initializer = require("Initializer");
let Utils = require("Utils");

cc.Class({
    extends: renderItem,

    properties: {
        spCard: UrlLoad,//卡图
        //frameImg: UrlLoad,
        qualityImg: UrlLoad,
        nameLabel: cc.Label,
        ndRedPot:cc.Node,//红点提示
        ndStar:cc.Node,//星级节点
        spStar:[UrlLoad],//星级图
        ndBlack:cc.Node,//黑色遮罩
        ndAni: cc.Node,
        selectImg: cc.Node,
        spParam:[UrlLoad],//属性背景图
        lbParamTxt: [cc.Label],
        lbParam:[cc.Label],//属性文本
        spType:UrlLoad,
    },

    onLoad() {
        facade.subscribe(Initializer.fuyueProxy.TEMP_REFRESH_SELECT, this.updateSelect, this);
        //facade.subscribe(Initializer.cardProxy.CARD_DATA_UPDATE, this.showInfo, this);
    },

    initListItem(data,allListData) {
        this.cardData = null;
        this.cfgData = null;
        if (null != data) {
            this.cfgData = data;
            this.allListData = allListData;
            this.showInfo();
        }
    },

    showData: function() {
        let data = this._data;
        if (data) {
            this.cfgData = data;
            this.showInfo();
        }
    },

    showInfo () {
        let cardData = Initializer.cardProxy.getCardInfo(this.cfgData.id);
        this.spCard.url = UIUtils.uiHelps.getCardSmallFrame(this.cfgData.picture);
        //this.frameImg.url = UIUtils.uiHelps.getQualityFrame(this.cfgData.quality, 0);
        this.qualityImg.url = UIUtils.uiHelps.getQualitySp(this.cfgData.quality, 0);
        //this.qualityImg.node.active = true;
        // if(cardData){
        //     this.qualityImg.node.active = this.cfgData.quality != 4 && this.cfgData.quality != 3;
        // }
        if (this.spType){
            this.spType.url = UIUtils.uiHelps.getUICardPic("kpsj_icon_" + this.cfgData.shuxing);
        }
        
        this.nameLabel.string = this.cfgData.name;
        this.ndStar.active = false;
        this.ndBlack.active = true;
        this.ndRedPot.active = false;
        this.ndAni.active = false;
        if(cardData) {
            this.ndBlack.active = false;
            this.ndStar.active = (cardData.star > 0);
            if(this.ndStar.active) {
                for(let i = 0; i < this.spStar.length; i++) {
                    this.spStar[i].url = UIUtils.uiHelps.getStarFrame(i < cardData.star);
                }
            }
            if(this.cfgData.quality == 4 || this.cfgData.quality == 3) {
                this.ndAni.active = true;
                Utils.utils.showNodeEffect(this.ndAni, 0);
            }

            // this.ndRedPot.active = Initializer.cardProxy.checkCardRedPot(this.cfgData, cardData);
            // if(this.ndRedPot.active) {
            //     Utils.utils.showNodeEffect(this.ndRedPot, 0);
            // }
            this.cardData = cardData;
            this.showProp();

            this.selectImg.active = this.node.parent.getComponent("List").chooseId == this.cfgData.id;
        }
    },

    updateSelect: function(data) {
        if(this.cfgData && data.set == Initializer.fuyueProxy.conditionType.card) {
            this.selectImg.active = data.id == this.cfgData.id;
        }
    },

    showProp () {
        // let starParamCfg = localcache.getFilter(localdb.table_card_starup,'quality',
        //     this.cfgData.quality,'star',this.cardData.star);
        // if(this.cardData.star == 9){
        //     starParamCfg.cost = null;
        // }

        for(let i = 1; i <= 4; i++) {
            let pIndex = i - 1;
            this.spParam[pIndex].url = UIUtils.uiHelps.getLangSp(i);
            this.lbParamTxt[pIndex].string = UIUtils.uiHelps.getPinzhiStr(i);
            this.lbParam[pIndex].string = Initializer.cardProxy.getCardCommonPropValue(this.cfgData.id,i);
        }       
    },

    onClickCard() {
        if(this.cardData) {
            let fuyueProxy = Initializer.fuyueProxy;
            facade.send(fuyueProxy.TEMP_REFRESH_SELECT, { set: fuyueProxy.conditionType.card, id: this.cardData.id });
            // Initializer.fuyueProxy.iSelectCard = this.cardData.id;
            // facade.send(Initializer.fuyueProxy.REFRESH_CARD);
            // Utils.utils.closeNameView("fuyue/FuyueCardListView");
        }
    },
});
