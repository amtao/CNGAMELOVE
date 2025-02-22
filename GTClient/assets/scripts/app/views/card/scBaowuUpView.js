let urlLoad = require("UrlLoad");
let scItem = require("ItemSlotUI");
let scUtils = require("Utils");
let scUIUtils = require("UIUtils");
let initializer = require("Initializer");

cc.Class({
    extends: cc.Component,

    properties: {
        imgSlot: urlLoad,
        colorFrame: urlLoad,
        lbName: cc.Label,
        lbStar: cc.Label,
        lbDesc: cc.Label,

        nActive: cc.Node,
        lbCurTitle: cc.Label,
        lbCurNum: cc.Label,
        lbNextTitle: cc.Label,
        lbNextNum: cc.Label,
        costItem: scItem,
        lbCount: cc.Label,

        nUnactive: cc.Node,
        btnUpStar: cc.Button,
        nMax: cc.Node,
    },

    onLoad: function() {
        this._data = this.node.openParam;
        this.showData();
        facade.subscribe(initializer.baowuProxy.UPDATE_BAOWU_STAR, this.updateData, this);
    },

    updateData: function() {
        this._data = initializer.baowuProxy.getBaowuData(this._data.id);
        this.showData();
    },

    showData: function() {
        let data = this._data;
        let bHas = data.bHas;
        this.imgSlot.url = scUIUtils.uiHelps.getBaowuIcon(data.picture);
        this.colorFrame.url = scUIUtils.uiHelps.getItemColor(data.quality + 1);
        this.lbName.string = data.name;
        this.lbStar.string = i18n.t("CARD_CLOTHESINFO_2") + (bHas ? data.data.star + "" : "0");
        this.lbDesc.string = data.desc;
        this.nActive.active = bHas;
        this.nUnactive.active = !bHas;
        this.btnUpStar.node.active = bHas;
        this.nMax.active = bHas;
        if(bHas) {
            let starParamCfg = localcache.getFilter(localdb.table_baowu_starup, 'quality'
             , data.quality, 'star', data.data.star);
            let nextStarParamCfg = localcache.getFilter(localdb.table_baowu_starup, 'quality'
             , data.quality, 'star', data.data.star + 1);
            let bNext = null != nextStarParamCfg;
            this.btnUpStar.node.active = bNext;
            this.nMax.active = !bNext;
            let prop = initializer.baowuProxy.getBaowuProp(data);
            this.lbCurTitle.string = this.lbNextTitle.string = scUIUtils.uiHelps.getPinzhiStr(prop.id) + ":";
            this.lbCurNum.string = prop.val * starParamCfg["ep" + prop.id];
            if(bNext) {
                this.costItem.node.active = true;  
                this.lbNextNum.string = prop.val * nextStarParamCfg["ep" + prop.id];
                this.costItem._data = { id: data.item, kind: data.use };
                this.costItem.showData();
                let count = initializer.bagProxy.getItemCount(data.item);
                this.costCount = starParamCfg.cost;
                this.costItem.lblcount.string = count + "/" + this.costCount;
                let enoughColor = new cc.Color(77, 93, 122, 255);
                this.bEnough = count >= this.costCount;
                this.costItem.lblcount.node.color = this.bEnough ? enoughColor : cc.Color.RED;
            } else {
                this.lbNextTitle.string = i18n.t("SERVANT_MAX_STAR");
                this.lbNextNum.string = "";
                this.costItem.node.active = false;
            }
        }
    },

    onClickStarUp: function() {
        let count = initializer.bagProxy.getItemCount(this._data.item);
        if(count < this.costCount) {
            scUtils.alertUtil.alert(i18n.t("USER_ITEMS_SHORT"));
        } else {
            initializer.baowuProxy.sendStarUp(this._data.id);
            scUtils.audioManager.playSound("levelup", !0, !0);
        }
    },

    onClickClose: function() {
        scUtils.utils.closeView(this);
    },
});
