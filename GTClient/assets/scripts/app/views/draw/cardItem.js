

var RenderListItem = require("RenderListItem");
var urlLoad = require("UrlLoad");
var UtilsUI = require("UIUtils");

cc.Class({
    extends: RenderListItem,

    properties: {
        skin: 1,
        isBigCard: false,
        qualityImg: urlLoad,
        qsLabel: cc.Label,
        zmLabel: cc.Label,
        zlLabel: cc.Label,
        mlLabel: cc.Label,
        nameLabel: cc.Label,
        icon: urlLoad,
        //frameImg:urlLoad,
        lblquality:cc.Label,
        spType:urlLoad,
    },

    // LIFE-CYCLE CALLBACKS:

    onLoad () {

    },

    start () {

    },

    showData() {
        var t = this._data;
        if (t) {
            this.showInfo(this._data.id,this.data.istreasure);
        }
    },

    showInfo (cardId,istreasure) {
        var table
        if (istreasure){
            table = localcache.getItem(localdb.table_baowu,cardId);
        }
        else{
            table = localcache.getItem(localdb.table_card, cardId);
        }
        if (!table) return;
        if (this.qualityImg) {
            this.qualityImg.url = UtilsUI.uiHelps.getQualitySp(table.quality, this.skin);
        }
        // if (this.frameImg) {
        //     this.frameImg.url = UtilsUI.uiHelps.getQualityFrame(table.quality, this.skin);
        // }
        if (this.nameLabel) {
            this.nameLabel.string = table.name ? table.name : "";
        }
        if (this.lblquality){
            this.lblquality.string = i18n.t("XINDONG_QUALITY_" + table.quality);
        }
        if (this.icon) {
            if (table.quality === 4 && this.isBigCard) {
                this.icon.url = UtilsUI.uiHelps.getTianCiCardEffect(table.picture);
            } else {
                this.icon.url = this.isBigCard ? UtilsUI.uiHelps.getCardFrame(table.picture) : UtilsUI.uiHelps.getCardSmallFrame(table.picture);
            }
        }
        if (this.spType){
            this.spType.url = UtilsUI.uiHelps.getUICardPic("kpsj_icon_" + table.shuxing);
        }
        this.showProp(table);

        // if (this.skin === 1) {
        //     if (table.quality == 1 || table.quality == 2) {
        //         this.frameImg.node.widget = 520;
        //         this.frameImg.node.height = 800;
        //         this.frameImg.node.x = 0;
        //     } else {
        //         this.frameImg.node.widget = 540;
        //         this.frameImg.node.height = 820;
        //         this.frameImg.node.x = -10;
        //     }
        // }


    },

    // 卡牌属性
    showProp (data) {
        if (this.qsLabel) {
            this.qsLabel.string = data.ep1;
        }
        if (this.zmLabel) {
            this.zmLabel.string = data.ep2;
        }
        if (this.zlLabel) {
            this.zlLabel.string = data.ep3;
        }
        if (this.mlLabel) {
            this.mlLabel.string = data.ep4;
        }
    }


    // update (dt) {},
});
