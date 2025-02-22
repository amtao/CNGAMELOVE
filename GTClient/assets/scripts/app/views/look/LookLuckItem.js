var i = require("RenderListItem");
var n = require("Initializer");
var l = require("Utils");
var r = require("ItemSlotUI");
var a = require("formula");
cc.Class({
    extends: i,
    properties: {
        lblName:cc.Label,
        lblCostName:cc.Label,
        lblCost:cc.Label,
        lblLuck:cc.Label,
        nodeZhenzai:cc.Node,
        nodeZhuanyun:cc.Node,
        itemSlot:r,
    },
    showData() {
        var t = this._data;
        if (t) {
            var e = localcache.getItem(localdb.table_vip, n.playerProxy.userData.vip).free_zy > n.lookProxy.zhenzai.used ? i18n.t("COMMON_FREE") : this.getCost();
            this.itemSlot.data = {
                id: t.itemId
            };
            this.lblName.string = i18n.t("LOOK_ZHEN_ZAI_NAME" + t.type);
            this.lblCostName.string = i18n.t("LOOK_ZHEN_ZAI_COST" + t.type);
            var o = n.lookProxy.zhenzai.num + 1,
            i = a.formula.city_lucky(n.playerProxy.userEp["e" + t.itemId] + n.jingyingProxy.getWeipaiAdd(t.itemId), o);
            this.lblCost.string = (3 == t.type ? e: i) + "";
            this.nodeZhenzai.active = 3 != t.type;
            this.nodeZhuanyun.active = 3 == t.type;
            this.lblLuck.string = i18n.t("LOOK_LUCK_TITLE") + "+" + t.add;
        }
    },
    getCost() {
        var t = this._data;
        if (t) {
            if (3 != t.type) return t.cost;
            if (localcache.getItem(localdb.table_vip, n.playerProxy.userData.vip).free_zy > n.lookProxy.zhenzai.used) return 0;
            for (var e = l.utils.getParamStrs("look_cost_gold"), o = e[0][1], i = n.lookProxy.recover.num, r = 0; r < e.length; r++) if (i < parseInt(e[r][0]) || 0 == parseInt(e[r][0])) {
                o = e[r][1];
                break;
            }
            return parseInt(o);
        }
        return 0;
    },
    onClickZhenzai() {
        var t = this._data;
        if (t) {
            var e = n.lookProxy.zhenzai.num + 1,
            o = n.bagProxy.getItemCount(t.itemId),
            i = a.formula.city_lucky(n.playerProxy.userEp["e" + t.itemId] + n.jingyingProxy.getWeipaiAdd(t.itemId), e);
            if (3 != t.type && o < i) {
                l.alertUtil.alertItemLimit(t.itemId);
                return;
            }
            if (3 == t.type && o < parseInt(this.getCost() + "")) {
                l.alertUtil.alertItemLimit(t.itemId);
                return;
            }
            n.lookProxy.sendZzHand(t.type);
        }
    },
});
