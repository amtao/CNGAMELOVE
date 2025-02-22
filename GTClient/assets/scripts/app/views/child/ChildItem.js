var i = require("RenderListItem");
var n = require("UrlLoad");
var l = require("Utils");
var r = require("Initializer");
var a = require("ChildSpine");
cc.Class({
    extends: i,
    properties: {
        select:{
            set: function(t) {
                this.nodeSelect.active = t;
            },
            visible:false,
        },
        lblLv:cc.Label,
        nodeLock:cc.Node,
        head:n,
        nodeSelect:cc.Node,
        nodeLv:cc.Node,
        nodeFree:cc.Node,
        childSpine:a,
        sp_lblbac:cc.Sprite,
        nRed: cc.Node,
    },

    showData() {
        var t = this._data;
        if (t && null != t.sex) {
            var e = localcache.getItem(localdb.table_minor, t.talent);
            t.name == ""? this.lblLv.string = i18n.t("SON_NAME_NEED"):this.lblLv.string = i18n.t("SON_LEVEL", {
                l: t.level,
                m: e.level_max
            });
            this.nodeLock.active = !1;
            this.nodeLv.active = !0;
            this.childSpine.setKid(t.id, t.sex, !1);
            this.childSpine.node.active = !0;
            this.nRed.active = t.state == proto_sc.SomState.tName || t.power > 0;
        } else {
            this.nodeLock.active = t.isLock;
            this.nodeFree.active = null == t.isLock;
            this.lblLv.string = t.isLock ? i18n.t("JINGYING_WEIJIESUO") : i18n.t("SON_SEAT_FREE"); 
            this.childSpine.clearKid();
            this.childSpine.node.active = !1;
            this.nRed.active = false;
        }
    },

    onClickLock() {
        var t = localcache.getItem(localdb.table_seat, r.sonProxy.base.seat + 1);
        t && l.utils.showConfirmItem(i18n.t("SON_LOCK_SEAT", {
            value: t.cash,
            index: t.seat
        }), 1, r.playerProxy.userData.cash,
        function() {
            r.playerProxy.userData.cash < t.cash ? l.alertUtil.alertItemLimit(1) : r.sonProxy.sendBuySeat();
        },
        "SON_LOCK_SEAT");
    },
});
