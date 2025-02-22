var i = require("Utils");
var n = require("UIUtils");
var l = require("ShaderUtils");
var r = require("Initializer");
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
        facade.subscribe(r.gaodianProxy.GAO_DIAN_INFO_UPDATE, this.onInfo, this);
        facade.subscribe(r.gaodianProxy.GAO_DIAN_BASE_UPDATE, this.onInfo, this);
        r.gaodianProxy.sendOpenActivity();
        l.shaderUtils.setBlur(this.bg);
        n.uiUtils.scaleRepeat(this.tipNode, 0.9, 1.1);
    },
    onInfo() {
        var t = this;
        r.gaodianProxy.info && n.uiUtils.countDown(r.gaodianProxy.info.info.eTime, this.lblTime,
        function() {
            t.lblTime.string = i18n.t("ACTHD_OVERDUE");
        },
        !0, "USER_REMAIN_TIME", "d");
        var e = r.gaodianProxy.getCurGaodianTime(!0);
        if (null == e) this.lblNext.string = i18n.t("GAO_DIAN_YI_QIANG_WAN");
        else {
            var o = i.timeUtil.getTodaySecond(e.need);
            n.uiUtils.countDown(o, this.lblNext,
            function() {
                t.lblNum.string = e.all + "";
                r.gaodianProxy.sendOpenActivity();
            },
            !0, "GAO_DIAN_XIA_YI_CI", "d");
        }
        if (i.timeUtil.second > r.gaodianProxy.info.info.eTime) {
            this.nextNode.active = !1;
            this.tipNode.active = this.startNode.active = !1;
            this.lblNum.string = "";
        } else {
            var l = r.gaodianProxy.getCurGaodianTime();
            if (l) {
                var a = r.gaodianProxy.getCurRemain(l.need);
                this.btnQiang.interactable = !0;
                var s = l.all - (a ? a.count: 0);
                this.lblNum.string = i18n.t("GAO_DIAN_BEN_LUN_SHENG_YU", {
                    num: s
                });
                this.startNode.active = this.tipNode.active = r.gaodianProxy.base.rwd != l.need && s > 0 && i.timeUtil.getTodaySecond(l.need) < i.timeUtil.second;
                this.nextNode.active = !this.startNode.active;
                n.uiUtils.scaleRepeat(this.startNode, 0.9, 1.1);
                this.lblNode.active = !0;
            } else {
                this.tipNode.active = this.startNode.active = !1;
                this.nextNode.active = i.timeUtil.second < r.gaodianProxy.info.info.eTime;
                this.lblNum.string = i18n.t("ACTHD_ACTIVITY_UNOPEN");
            }
        }
    },
    onClickQiang() {
        if (r.gaodianProxy.info.info.eTime <= i.timeUtil.second) i.alertUtil.alert18n("ACTHD_OVERDUE");
        else {
            var t = r.gaodianProxy.getCurGaodianTime();
            if (t) {
                var e = r.gaodianProxy.getCurRemain(t.need);
                if (e && t.all == e.count) {
                    i.alertUtil.alert18n("GAO_DIAN_QIANG_WAN");
                    return;
                }
                if (r.gaodianProxy.base.rwd == t.need) {
                    i.alertUtil.alert18n("GAO_DIAN_JIN_RI_YI_QIANG");
                    return;
                }
                if (this.flag) {
                    this.count++;
                    for (var o = 0; o < this.tyArr.length; o++) {
                        var n = Math.floor(3 * Math.random());
                        this.tyArr[o].spriteFrame = this.spriteArr[n];
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
            } else i.alertUtil.alert18n("GAO_DIAN_WEI_KAI_QI");
        }
    },
    onTimer() {
        this.flag = !1;
        this.btnQiang.interactable = !1;
        this.zhezhao.active = !1;
        this.cdAnie.node.active = !1;
        this.barNode.active = !1;
        r.gaodianProxy.sendQiang(this.count);
        this.count = 0;
    },
    onClickRank() {
        i.utils.openPrefabView("tangyuan/GaodianReward");
    },
    onClickShop() {
        i.utils.openPrefabView("ActivityShopView", null, r.gaodianProxy.dhShop);
    },
    onClickClose() {
        i.utils.closeView(this);
    },
});
