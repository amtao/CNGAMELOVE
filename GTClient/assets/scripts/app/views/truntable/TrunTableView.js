var i = require("TrunTableItem");
var n = require("Utils");
var l = require("Initializer");
var r = require("ShaderUtils");
var a = require("TimeProxy");
cc.Class({
    extends: cc.Component,
    properties: {
        itemArr:[i],
        lblHqCount:cc.Label,
        bg:cc.Sprite,
        lblTime:cc.Label,
    },

    ctor(){
        this.curIndex = 0;
        this.roundIndex = 0;
    },
    onLoad() {
        facade.subscribe(l.trunTableProxy.TRUN_TABLE_DATA_UPDATE, this.onDataUpdate, this);
        facade.subscribe(l.bagProxy.UPDATE_BAG_ITEM, this.onItemUpdate, this);
        facade.subscribe("UI_TOUCH_MOVE_LEFT", this.onClickClose, this);
        r.shaderUtils.setBlur(this.bg);
        l.trunTableProxy.sendOpen();
        this.onItemUpdate();
        l.shopProxy.sendList(!1);
        this.schedule(this.onTimer, 1);
    },
    onDataUpdate(t) {
        if (t.prize) {
            this.curIndex = t.prize[0].dc;
            this.showEff(0);
        } else if (t) for (var e = 0; e < this.itemArr.length; e++) if (e < t.list.length) {
            this.itemArr[e].data = t.list[e];
            this.itemArr[e].select = 0 == e;
        }
        l.trunTableProxy.data && this.onItemUpdate();
    },
    showEff(t) {
        this.unscheduleAllCallbacks();
        this.schedule(this.showSelect, t);
    },
    showSelect() {
        for (var t = this.roundIndex % 10,
        e = 0; e < this.itemArr.length; e++) this.itemArr[e].select = t == e;
        this.roundIndex++;
        if (this.roundIndex >= 10 && this.roundIndex < 20) this.showEff(0.03);
        else if (this.roundIndex >= 20 && this.roundIndex < 30) this.showEff(0.03 + (this.roundIndex - 20) / 200);
        else if (this.roundIndex >= 30 + this.curIndex && this.roundIndex < 100) {
            this.roundIndex = 0;
            this.curIndex = 0;
            this.unscheduleAllCallbacks();
            l.timeProxy.floatReward();
        }
    },
    onItemUpdate() {
        if (l.trunTableProxy.data) {
            var t = l.bagProxy.getItemCount(l.trunTableProxy.data.need.id);
            this.lblHqCount.string = t + "";
        }
    },
    onClickRoll(t, e) {
        if (0 == this.curIndex) {
            var o = parseInt(e);
            if (l.bagProxy.getItemCount(l.trunTableProxy.data.need.id) < o) {
                var i = localcache.getItem(localdb.table_item, l.trunTableProxy.data.need.id);
                n.alertUtil.alert(i18n.t("COMMON_LIMIT", {
                    n: i.name
                }));
                this.onClickAdd();
            } else l.trunTableProxy.sendRoll(o);
        } else n.alertUtil.alert18n("TRUN_TABLE_IS_ROLLING");
    },
    onClickClose() {
        n.utils.closeView(this);
    },
    onClickAdd() {
        l.shopProxy.openShopBuy(l.trunTableProxy.data.need.id);
    },
    onTimer() {
        if (l.trunTableProxy.data) {
            var t = l.trunTableProxy.data.info.eTime - n.timeUtil.second > 0 ? l.trunTableProxy.data.info.eTime - n.timeUtil.second: 0;
            this.lblTime.string = i18n.t("union_endcool", {
                time: n.timeUtil.second2hms(t)
            });
        }
    },
    onClickRecharge() {
        a.funUtils.openView(a.funUtils.recharge.id);
    },
});
