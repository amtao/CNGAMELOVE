var i = require("RenderListItem");
var n = require("ItemSlotUI");
var l = require("UIUtils");
var r = require("Initializer");
var a = require("Utils");
var s = require("SelectMax");
cc.Class({
    extends: i,
    properties: {
        slot: n,
        //lblName: cc.Label,
        lbCount: cc.Label,
        nBtnUse: cc.Node,
        nBtnMax: cc.Node,
        //bar: s,
    },
    ctor() {},

    showData() {
        var t = this._data;
        if (t) {
            var o = new l.ItemSlotData();
            o.id = t.itemid;
            o.count = r.bagProxy.getItemCount(t.itemid);
            this.slot.data = o;
            // n = a.utils.getParamInt("show_slider_count");
            let itemDatas = r.servantProxy.useItemList[r.servantProxy.curSelectId];
            this.num = 0;
            if(null != itemDatas && null != itemDatas[t.itemid]) {
                this.num = itemDatas[t.itemid];
            }
            this.maxCount = t.count;
            // let heroData = r.servantProxy.getHeroData(r.servantProxy.curSelectId);
            // if (heroData){
            //     let itemCfgDatas = localcache.getItem(localdb.table_upStar, heroData.star);
            //     let cfgData = null;
            //     for(let i = 0, len = itemCfgDatas.itemLimit.length; i < len; i++) {
            //         if(itemCfgDatas.itemLimit[i].itemid == t) {
            //             cfgData = itemCfgDatas.itemLimit[i];
            //             break;
            //         }
            //     }
            //     this.maxCount = cfgData.count;
            //     this.lbCount.string = this.num + "/" + this.maxCount;
            // }
            this.lbCount.string = this.num + "/" + this.maxCount;           
            let bMax = this.num >= this.maxCount;
            this.nBtnMax.active = bMax;
            this.nBtnUse.active = !bMax;
        }
    },

    onClickUse() {
        let id = this._data.itemid;
        let count = r.bagProxy.getItemCount(id);
        if (count <= 0) a.alertUtil.alertItemLimit(id);
        else {
            a.utils.openPrefabView("bag/BagUse", !1, { 
                id: id, 
                heroId: r.servantProxy.curSelectId, 
                canUseCount: this.maxCount - this.num,
                count: count });
            //r.bagProxy.sendUseItemHero(parseInt(this._data + ""), this.bar.curValue ? this.bar.curValue: 1, r.servantProxy.curSelectId);
            //a.alertUtil.alert(i18n.t("SERVANT_TRAIN_SUCCESS"));
        }
    },
});
