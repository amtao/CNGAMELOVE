let RenderListItem = require("RenderListItem");
let urlLoad = require("UrlLoad");
let UIUtils = require("UIUtils");
let Initializer = require("Initializer");

cc.Class({
    extends: RenderListItem,

    properties: {
        cardUrl:urlLoad,
        chooseNode:cc.Node,
        qualitySp:cc.Sprite,
        qualityFrame:[cc.SpriteFrame],
        cardName:cc.Label,
        spType:urlLoad
    },

    onLoad () {
        facade.subscribe("JIAOYOU_SHOUHU_CARD",this.refreshUI,this)
    },

    start () {

    },

    showData() {
        var t = this._data;
        if (t) {
            this.cardId = parseInt(t.cardid)
            this.jiaoyouId = parseInt(t.jiaoyouId)
            var cardCfg = localcache.getItem(localdb.table_card,this.cardId)

            if (this.spType){
                this.spType.url = UIUtils.uiHelps.getUICardPic("kpsj_icon_" + cardCfg.shuxing)
            }

            this.cardUrl.url = UIUtils.uiHelps.getItemSlot(this.cardId);
            var showQuality = cardCfg.quality>4?4:cardCfg.quality
            this.qualitySp.spriteFrame = this.qualityFrame[showQuality - 1]
            this.cardName.string = cardCfg.name

            this.chooseNode.active = false
        }
    },

    refreshUI() {
        this.chooseNode.active = Initializer.jiaoyouProxy.shouhuChooseCard.indexOf(this.cardId) >= 0
    },

    onClickCard() {
        Initializer.jiaoyouProxy.addCardByChoose(this.jiaoyouId,this.cardId)
    }
});
