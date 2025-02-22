var i = require("RenderListItem");
var n = require("Initializer");
var l = require("Utils");
var r = require("UrlLoad");
var a = require("UIUtils");
var s = require("ShaderUtils");
cc.Class({
    extends: i,
    properties: {
        lblUnlock: cc.Label,
        lblName: cc.Label,
        lblCost: cc.Label,
        lblTime: cc.Label,
        nodeUnlock: cc.Node,
        nodelock: cc.Node,
        btn: cc.Button,
        plant: r,
        grays: [cc.Sprite],
    },
    ctor() {},
    onLoad() {
        this.addBtnEvent(this.btn);
    },
    onClickItem() {
        var t = this.data;
        if (t) {
            var e = new a.ItemSlotData();
            e.id = t.itemid;
            l.utils.openPrefabView("ItemInfo", !1, e);
        }
    },
    showData() {
        var t = this.data;
        if (t) {
            this.nodeUnlock.active = t.lv > n.flowerProxy.level.lv;
            for (var e = 0; e < this.grays.length; e++) s.shaderUtils.setImageGray(this.grays[e], this.nodeUnlock.active);
            this.nodeUnlock.active && (this.lblUnlock.string = i18n.t("FLOWER_UNLOCK_LEVEL", {
                d: t.lv
            }));
            this.nodelock.active = !this.nodeUnlock.active;
            this.lblName.string = t.flower;
            this.lblCost.string = l.utils.formatMoney(t.dew);
            this.lblTime.string = i18n.t("FLOWER_PLANT_TIME", {
                d: l.timeUtil.second2hms(t.time)
            });
            this.plant.url = a.uiHelps.getFlowerPlant(t.id, 2);
        }
    },
});
