var i = require("Utils");
var n = require("Initializer");
var l = require("UIUtils");
var r = require("ShaderUtils");
cc.Class({
    extends: cc.Component,
    properties: {
        lblNext: cc.Label,
        lblNum: cc.Label,
        lblTime: cc.Label,
        nextNode: cc.Node,
        btnQiang: cc.Button,
        lblNode: cc.Node,
        startNode: cc.Node,
        tipNode: cc.Node,
        zhezhao: cc.Node,
        bg: cc.Sprite,
        tyArr: [cc.Sprite],
        spriteArr: [cc.SpriteFrame],
        cdAnie: cc.Animation,
        barAnie: cc.Animation,
        tyFly: cc.Animation,
        barNode: cc.Node,
    },
    ctor() {
        this.flag = !1;
        this.count = 0;
    },
    onLoad() {
        facade.subscribe(n.tangyuanProxy.TANG_YUAN_INFO_UPDATE, this.onInfo, this);
        facade.subscribe(n.tangyuanProxy.TANG_YUAN_BASE_UPDATE, this.onInfo, this);
        n.tangyuanProxy.sendOpenActivity();
        r.shaderUtils.setBlur(this.bg);
        l.uiUtils.scaleRepeat(this.tipNode, 0.9, 1.1);
    },
    onInfo() {
        var t = this;
        n.tangyuanProxy.info && l.uiUtils.countDown(n.tangyuanProxy.info.info.eTime, this.lblTime,
        function() {
            t.lblTime.string = i18n.t("ACTHD_OVERDUE");
        },
        !0, "USER_REMAIN_TIME", "d");
        var e = n.tangyuanProxy.getCurTangyuanTime(!0);
        if (null == e) this.lblNext.string = i18n.t("TANG_YUAN_YI_QIANG_WAN");
        else {
            var o = i.timeUtil.getTodaySecond(e.need);
            l.uiUtils.countDown(o, this.lblNext,
            function() {
                t.lblNum.string = e.all + "";
                n.tangyuanProxy.sendOpenActivity();
            },
            !0, "TANG_YUAN_XIA_YI_CI", "d");
        }
        if (i.timeUtil.second > n.tangyuanProxy.info.info.eTime) {
            this.nextNode.active = !1;
            this.tipNode.active = this.startNode.active = !1;
            this.lblNum.string = "";
        } else {
            var r = n.tangyuanProxy.getCurTangyuanTime();
            if (r) {
                var a = n.tangyuanProxy.getCurRemain(r.need);
                this.btnQiang.interactable = !0;
                var s = r.all - (a ? a.count: 0);
                this.lblNum.string = i18n.t("TANG_YUAN_BEN_LUN_SHENG_YU", {
                    num: s
                });
                this.startNode.active = this.tipNode.active = n.tangyuanProxy.base.rwd != r.need && s > 0 && i.timeUtil.getTodaySecond(r.need) < i.timeUtil.second;
                this.nextNode.active = !this.startNode.active;
                l.uiUtils.scaleRepeat(this.startNode, 0.9, 1.1);
                this.lblNode.active = !0;
            } else {
                this.tipNode.active = this.startNode.active = !1;
                this.nextNode.active = i.timeUtil.second < n.tangyuanProxy.info.info.eTime;
                this.lblNum.string = i18n.t("ACTHD_ACTIVITY_UNOPEN");
            }
        }
    },
    onClickQiang() {
        if (n.tangyuanProxy.info.info.eTime <= i.timeUtil.second) i.alertUtil.alert18n("ACTHD_OVERDUE");
        else {
            var t = n.tangyuanProxy.getCurTangyuanTime();
            if (t) {
                var e = n.tangyuanProxy.getCurRemain(t.need);
                if (e && t.all == e.count) {
                    i.alertUtil.alert18n("TANG_YUAN_QIANG_WAN");
                    return;
                }
                if (n.tangyuanProxy.base.rwd == t.need) {
                    i.alertUtil.alert18n("TANG_YUAN_JIN_RI_YI_QIANG");
                    return;
                }
                if (this.flag) {
                    this.count++;
                    for (var o = 0; o < this.tyArr.length; o++) {
                        var l = Math.floor(3 * Math.random());
                        this.tyArr[o].spriteFrame = this.spriteArr[l];
                    }
                } else {
                    this.flag = !0;
                    this.startNode.active = !1;
                    this.zhezhao.active = !0;
                    this.cdAnie.node.active = !0;
                    this.cdAnie.play();
                    this.barNode.active = !0;
                    this.barAnie.play();
                    this.tyFly.node.active = !0;
                    this.tyFly.play();
                    this.scheduleOnce(this.onTimer, 5);
                }
            } else i.alertUtil.alert18n("TANG_YUAN_WEI_KAI_QI");
        }
    },
    onTimer() {
        this.flag = !1;
        this.btnQiang.interactable = !1;
        this.zhezhao.active = !1;
        this.cdAnie.node.active = !1;
        this.barNode.active = !1;
        n.tangyuanProxy.sendQiang(this.count);
        this.count = 0;
    },
    onClickRank() {
        i.utils.openPrefabView("tangyuan/TangyuanReward");
    },
    onClickShop() {
        i.utils.openPrefabView("ActivityShopView", null, n.tangyuanProxy.dhShop);
    },
    onClickClose() {
        i.utils.closeView(this);
    },
});
