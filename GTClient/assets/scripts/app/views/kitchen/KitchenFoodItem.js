var i = require("RenderListItem");
var n = require("ItemSlotUI");
var l = require("Utils");
var r = require("List");
var a = require("Initializer");
var s = require("ShaderUtils");
var c = require("LabelShadow");
cc.Class({
    extends: i,
    properties: {
        item: n,
        lblName: cc.Label,
        nodeBtn: cc.Button,
        lblTime: cc.Label,
        list: r,
        nodeUnlock: cc.Node,
        lblUnlock: c,
        grays: [cc.Sprite],
    },
    ctor() {},
    onLoad() {
        this.addBtnEvent(this.nodeBtn);
    },
    showData() {
        var t = this._data;
        if (t) {
            var e = localcache.getItem(localdb.table_kitchen, t.id);
            this.item.data = {
                id: e.itemid
            };
            for (var o = [], i = 0; i < e.fooditemid.length; i++) o.push({
                id: e.fooditemid[i]
            });
            var n = a.wifeProxy.getWifeData(t.wid);
            this.nodeUnlock.active = n && (n.love < e.intmin || n.flower < e.intmax);
            if (this.nodeUnlock.active) {
                var r = n.love < e.intmin ? i18n.t("KIT_NEED_LOVE", {
                    d: e.intmin
                }) : "",
                c = n.flower < e.intmax ? i18n.t("KIT_NEED_FLOWER", {
                    d: e.intmax
                }) : "";
                this.lblUnlock.string = l.stringUtil.isBlank(r) ? c: l.stringUtil.isBlank(c) ? r: i18n.t("COMMON_CONTEXT_NUM", {
                    c: r,
                    n: c
                });
            }
            this.nodeBtn.interactable = !this.nodeUnlock.active;
            for (i = 0; i < this.grays.length; i++) s.shaderUtils.setImageGray(this.grays[i], this.nodeUnlock.active);
            this.list.data = o;
            this.lblTime.string = i18n.t("KIT_COST_TIME", {
                t: l.timeUtil.second2hms(e.time, "HH:mm:ss")
            });
        }
    },
});
