let UrlLoad = require("UrlLoad");
let UIUtils = require("UIUtils");
let renderItem = require("RenderListItem");
let Initializer = require("Initializer");
let Utils = require("Utils");

cc.Class({

    extends: renderItem,

    properties: {
        spCard: UrlLoad,   //卡图
        qualityImg: UrlLoad,
        nameLabel: cc.Label,
        lblProp:cc.Label,
        nodeSelect:cc.Node,
        iconProp:UrlLoad,
    },

    showData: function() {
        let t = this._data;
        if (t) {
            let data = t.cfg;
            this.spCard.url = UIUtils.uiHelps.getCardSmallFrame(data.picture);
            this.qualityImg.url = UIUtils.uiHelps.getQualitySpNew(data.quality, 0);
            this.nameLabel.string = data.name;
            // let cardData = Initializer.cardProxy.getCardInfo(data.id);
            // let starParamCfg = localcache.getFilter(localdb.table_card_starup,'quality',
            // data.quality,'star',cardData.star);
        
            // let paramBaseValue = data['ep1'];
            // let paramAdditionValue = starParamCfg ? starParamCfg['ep1'] : 0;
            // let paramStrengthValue = 0;//strengthParamCfg ? strengthParamCfg['ep'+i]:0;
            // if(null != paramAdditionValue && null != paramStrengthValue){
            //     paramBaseValue = paramBaseValue * paramAdditionValue + paramStrengthValue;
            // }
            this.iconProp.url = UIUtils.uiHelps.getUICardPic(`kpsj_icon_${t.propType}`);    
            this.lblProp.string = `${t.propE1}`;
            this.nodeSelect.active = t.isChoose;
        }
    },

    onClickCard(){
        let data = this._data;
        if (data == null) return;
        facade.send("CLOTHE_CARD_REFRESH_SELECT", { id: data.cfg.id });
    },
});
