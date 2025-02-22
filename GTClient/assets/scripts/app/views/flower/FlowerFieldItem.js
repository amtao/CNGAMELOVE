var i = require("RenderListItem");
var n = require("Utils");
var l = require("UIUtils");
var r = require("UrlLoad");
var a = require("Initializer");
var s = require("ShaderUtils");
cc.Class({
    extends: i,
    properties: {
        nodeCd: cc.Node,
        nodeUnlock: cc.Node,
        lblCd: cc.Label,
        lblUnlock: cc.Label,
        nodePlant: cc.Node,
        unplant: cc.Sprite,
        nodeSelect: cc.Node,
        urlload: r,
        select:{
            set: function(t) {
                this.nodeSelect.active = t;
            },
            enumerable: !0,
            configurable: !0
        },
    },
    ctor() {},
    showData() {
        var t = this.data;
        if (t) {
            this.lblCd.string = "";
            this.lblCd.unscheduleAllCallbacks();
            this.nodePlant.active = this.nodeCd.active = 0 != t.pid && -1 != t.pid;
            this.unplant.node.active = 0 == t.pid || -1 == t.pid;
            this.nodeUnlock.active = -1 == t.pid && a.flowerProxy.isNextUnlock(t.id);
            this.urlload.url = "";
            s.shaderUtils.setImageGray(this.unplant, -1 == t.pid);
            if (this.nodeCd.active) {
                var e = localcache.getItem(localdb.table_flowerCore, t.pid),
                o = t.sTime + e.time;
                if (n.timeUtil.second > o) this.lblCd.string = i18n.t("FLOWER_PLANT_CD_OVER");
                else {
                    var i = this;
                    l.uiUtils.countDown(o, this.lblCd,
                    function() {
                        i.lblCd.string = i18n.t("FLOWER_PLANT_CD_OVER");
                    });
                }
                this.urlload.url = l.uiHelps.getFlowerPlant(t.pid, a.flowerProxy.getStatu(t.sTime, e.time));
            }
            if (this.nodeUnlock.active) {
                var r = this.nodeCd.active ? null: localcache.getItem(localdb.table_flowerFeild, t.id);
                this.lblUnlock.string = i18n.t("FLOWER_UNLOCK_LEVEL", {
                    d: r.lv
                });
            }
        }
    },
});
