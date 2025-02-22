var i = require("Utils");
var n = require("Initializer");
var l = require("ItemSlotUI");
var r = require("UIUtils");
var a = require("SelectMax");
cc.Class({
    extends: cc.Component,
    properties: {
        itemSlot:l,
        btnSure:cc.Button,
        btnCancel:cc.Button,
        lblTip:cc.RichText,
        lblCoolCd:cc.Label,
        silder:a,
        chenluNode:cc.Node,
        yuanbaoNode:cc.Node,
    },

    ctor(){
        this.bCliCK = false;
        this.type = 0;
    },
    onLoad() {
        facade.subscribe(n.flowerProxy.UPDATE_FLOWER_PROTECT, this.onUpdateShow, this);
        this.ininProtect();
    },
    ininProtect() {
        var t = this,
        e = this.node.openParam;
        this.type = e.type;
        var o = new r.ItemSlotData();
        o.id = 1 == this.type ? 10001 : 1;
        o.count = 1 == this.type ? n.flowerProxy.level.chenlu: n.playerProxy.userData.cash;
        this.itemSlot.data = o;
        this.chenluNode.active = 1 == this.type;
        this.yuanbaoNode.active = 2 == this.type;
        this.protects = localcache.getList(localdb.table_flowerProtect);
        if (null != this.protects && !(this.protects.length <= 0) && this.silder) {
            this.silder.max = this.protects.length;
            this.silder.changeHandler = function() {
                var e = t.protects[t.silder.curValue - 1];
                if (null != e) {
                    t.curProtect = e;
                    var o = Math.floor(t.curProtect.cd / 3600),
                    i = Math.floor(t.curProtect.time / 3600);
                    t.lblCoolCd.string = i18n.t("FLOWER_PROTECT_TIME", {
                        h: o
                    });
                    t.lblTip.string = i18n.t("FLOWER_PROTECT_CD_LIMIT", {
                        n: 1 == t.type ? e.dew: e.yb
                    });
                    t.silder.lblCount.string = i18n.t("COMMON_HOUR", {
                        d: i
                    });
                    t.chenluNode.x = e.dew > 9999 ? -216 : -215;
                    t.yuanbaoNode.x = e.yb > 999 ? -215 : -215;
                }
            };
            this.silder.curValue = 1;
        }
    },
    onUpdateShow() {
        if (null != this.curProtect && this.bCliCK) {
            this.bCliCK = !1;
            var t = Math.floor(this.curProtect.time / 3600);
            i.alertUtil.alert(i18n.t("FLOWER_PROTECT_SUCCESS", {
                h: t
            }));
            this.onClickClost();
        }
    },
    onClickGo() {
        if (!this.bCliCK && null != this.curProtect) {
            if (1 == this.type) {
                if (this.curProtect.dew > n.flowerProxy.level.chenlu) {
                    i.alertUtil.alert18n("FLOWER_PROTECT_CHENLU_LIMIT");
                    return;
                }
            } else if (this.curProtect.yb > n.playerProxy.userData.cash) {
                i.alertUtil.alertItemLimit(1);
                return;
            }
            this.bCliCK = !0;
            n.flowerProxy.sendProtect(this.curProtect.id, 1 == this.type ? 0 : 1);
        }
    },
    onClickClost() {
        i.utils.closeView(this);
    },
});
