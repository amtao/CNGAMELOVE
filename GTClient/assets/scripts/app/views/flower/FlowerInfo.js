var i = require("Initializer");
var n = require("Utils");
var l = require("UIUtils");
cc.Class({
    extends: cc.Component,
    properties: {
        lblName: cc.Label,
        lblChenlu: cc.Label,
        lblExp: cc.Label,
        pro: cc.ProgressBar,
        lblBJ: cc.Label,
        imgHua: cc.Node,
        imgProtect: cc.Node,
        lblProtectTip: cc.Label,
        lblCoolCd: cc.Label,
    },
    ctor() {},
    onLoad() {
        facade.subscribe(i.flowerProxy.UPDATE_FLOWER_LEVEL, this.onLevel, this);
        facade.subscribe(i.flowerProxy.UPDATE_FLOWER_PROTECT, this.onProtect, this);
        this.onLevel();
        this.onProtect();
    },
    onLevel() {
        if (null != i.flowerProxy.level) {
            var t = i.flowerProxy.level.lv,
            e = i.flowerProxy.level.exp,
            o = localcache.getItem(localdb.table_flowerLv, t);
            this.lblExp.string = i18n.t("COMMON_NUM", {
                f: e,
                s: o ? o.exp: 0
            });
            this.lblBJ && (this.lblBJ.string = i18n.t("FLOWER_BAOJI_PER", {
                d: o.chance / 100
            }));
            this.pro.progress = o ? e / o.exp: 1;
            this.lblName.string = i18n.t("FLOWER_LEVEL_NAME", {
                d: t
            });
            this.lblChenlu.string = n.utils.formatMoney(i.flowerProxy.level.chenlu);
        }
    },
    onProtect() {
        var t = this;
        if (null != this.lblProtectTip) {
            var e = i.flowerProxy.getProtectCd();
            if (i.flowerProxy.getProtectLeftCd() > 0) {
                this.imgHua.active = !1;
                this.imgProtect.active = !0;
                l.uiUtils.countDown(e, this.lblProtectTip,
                function() {
                    t.lblProtectTip.string = i18n.t("FLOWER_PROTECT_NOT");
                    t.imgHua.active = !0;
                    t.imgProtect.active = !1;
                    t.onUpdateCoolCd();
                },
                !0, "FLOWER_PROTECT_CD_LEFT", "d");
            } else {
                this.onUpdateCoolCd();
                this.imgHua.active = !0;
                this.imgProtect.active = !1;
                this.lblProtectTip.unscheduleAllCallbacks();
                this.lblProtectTip.string = i18n.t("FLOWER_PROTECT_NOT");
            }
        }
    },
    onUpdateCoolCd() {
        var t = this;
        if (i.flowerProxy.getProtectCoolCd() > 0) l.uiUtils.countDown(i.flowerProxy.getProtectCoolEndCd(), this.lblCoolCd,
        function() {
            t.lblCoolCd.string = i18n.t("FLOWER_PROTECT_START");
        },
        !0, "FLOWER_PROTECT_CD_DOWN", "d");
        else {
            this.lblCoolCd.unscheduleAllCallbacks();
            this.lblCoolCd.string = i18n.t("FLOWER_PROTECT_START");
        }
    },
});
