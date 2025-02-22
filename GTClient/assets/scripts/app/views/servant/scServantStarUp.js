let scItem = require("ItemSlotUI");
let scUtils = require("Utils");
let initializer = require("Initializer");

cc.Class({
    extends: cc.Component,

    properties: {
        lbCurCount: cc.Label,
        lbNextCount: cc.Label,
        scItems: [scItem],
    },

    onLoad: function() {
        this._data = this.node.openParam;
        this.star = this._data.star;
        facade.subscribe(initializer.playerProxy.PLAYER_USER_UPDATE, this.updateData, this);
        this.showData();
    },

    updateData: function() {
        let data = initializer.servantProxy.getHeroData(this._data.id);
        if(data.star > this.star) {
            this.data = data;
            this.star = this._data.star;
            this.showData();

            scUtils.utils.openPrefabView("servant/ServantStarUpSucc", null, data);
            this.onClickClose();
        }
    },

    showData: function() {
        this.starData = localcache.getItem(localdb.table_upStar, this.star);
        this.nextStarData = localcache.getItem(localdb.table_upStar, this.star + 1);
        this.lbCurCount.string = this.starData.num_show;
        this.lbNextCount.string = null == this.nextStarData ? i18n.t("SERVANT_MAX_STAR") : this.nextStarData.num_show;
        this.bEnough = true;
        for(let i = 0, len = this.scItems.length; i < len; i++) {
            let item = this.scItems[i];
            if(i < this.starData.cost.length) {
                item.node.active = true;
                let cost = this.starData.cost[i];
                item._data = { id: cost.itemid, count: cost.count };
                item.showData();
                let enoughColor = new cc.Color("#7A849F");
                let count = initializer.bagProxy.getItemCount(cost.itemid);
                item.lblcount.string = count + "/" + cost.count;
                item.lblcount.node.color = count >= cost.count ? enoughColor : cc.Color.RED;
                if(this.bEnough && count < cost.count) {
                    this.bEnough = false;
                }
            } else {
                item.node.active = false;
            }
        }
    },

    onClickClose: function() {
        scUtils.utils.closeView(this);
    },

    onClickStarUp: function() {
        if(null == this.nextStarData) {
            scUtils.alertUtil.alert(i18n.t("SERVANT_MAX_STAR"));
        } else if(!this.bEnough) {
            scUtils.alertUtil.alert(i18n.t("USER_ITEMS_SHORT"));
        } else {
            initializer.servantProxy.sendStarUp(this._data.id);
            scUtils.audioManager.playSound("levelup", !0, !0);
        }
    },
});
