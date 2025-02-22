var i = require("Utils");
var n = require("Initializer");
var l = require("UIUtils");
var r = require("List");
cc.Class({
    extends: cc.Component,
    properties: {
        lblSeat: cc.Label,
        lblScore: cc.Label,
        lblTime: cc.Label,
        lblId: cc.Label,
        lblName: cc.Label,
        listLog: r,
        listSeat: r,
        tipNode: cc.Node,
        lblTip: cc.Label,
        jia_bg: cc.Node,
        guan_bg: cc.Node,
        diban_jia: cc.Node,
        diban_guan: cc.Node,
        btnLeft: cc.Node,
        btnRight: cc.Node,
        lblPage: cc.Label,
        xinxiNode: cc.Node,
        laibinNode: cc.Node,
        btnArr: [cc.Button],
        lblNameRich: cc.RichText,
        bottomNode: cc.Node,
    },
    ctor() {
        this.curIndex = 1;
    },
    onLoad() {
        facade.subscribe("JIU_LOU_YH_INFO", this.onYhInfo, this);
        facade.subscribe("UI_TOUCH_MOVE_LEFT", this.onClickLeft, this);
        facade.subscribe("UI_TOUCH_MOVE_RIGHT", this.onClickRight, this);
        n.jiulouProxy.isCreate && i.utils.openPrefabView("jiulou/JiulouCreateSuccess");
        this.onYhInfo();
        this.onClickTab(null, "0");
        l.uiUtils.scaleRepeat(this.btnLeft, 0.9, 1.2);
        l.uiUtils.scaleRepeat(this.btnLeft, 0.9, 1.2); ! n.jiulouProxy.isCreate && n.playerProxy.userData && n.jiulouProxy.yhInfo && n.jiulouProxy.yhInfo.uid == n.playerProxy.userData.uid && i.alertUtil.alert18n("JIU_LOU_JOIN_OWN");
    },
    onYhInfo() {
        if (n.jiulouProxy.yhInfo) {
            var t = n.jiulouProxy.yhInfo;
            this.lblNameRich.string = i18n.t("JIU_LOU_YAN_HUI_NAME", {
                str: t.name,
                value: t.addPer / 100
            });
            this.lblScore.string = t.score + "";
            this.lblId.string = t.uid + "";
            this.lblSeat.string = i18n.t("COMMON_NUM", {
                f: t.num,
                s: t.maxnum
            });
            if (0 != t.ltime.next) l.uiUtils.countDown(t.ltime.next, this.lblTime);
            else {
                this.lblTime.unscheduleAllCallbacks();
                this.lblTime.string = "00:00:00";
            }
            for (var e = [], o = 0; o < t.list.length; o++) 0 != t.list[o].hid && e.push(t.list[o]);
            this.listLog.data = e;
            this.tipNode.active = 0 == e.length;
            this.lblTip.string = 1 == t.id ? i18n.t("JIU_LOU_JIA_YAN_TXT") : i18n.t("JIU_LOU_GUAN_TAN_TXT");
            null != n.jiulouProxy.win && (1 == n.jiulouProxy.win.yhnew.isover ? i.utils.openPrefabView("jiulou/JiulouOver") : i.utils.openPrefabView("jiulou/JiulouMes"));
            this.bottomNode.active = null == n.jiulouProxy.win || 1 != n.jiulouProxy.win.yhnew.isover;
            this.jia_bg.active = this.diban_jia.active = 1 == n.jiulouProxy.yhInfo.id || 0 == n.jiulouProxy.yhInfo.id;
            this.guan_bg.active = this.diban_guan.active = 2 == n.jiulouProxy.yhInfo.id;
            this.onShowList();
        }
    },
    onClickClost() {
        i.utils.closeNameView("jiulou/JiulouDinnce");
    },
    onShowList() {
        for (var t = n.jiulouProxy.yhInfo.list,
        e = 6 * (this.curIndex - 1), o = 6 * this.curIndex, i = [], l = 0; l < t.length; l++) l >= e && l < o && i.push(t[l]);
        this.listSeat.data = i;
        this.btnLeft.active = this.curIndex > 1;
        this.btnRight.active = this.curIndex < Math.ceil(t.length / 6);
        this.lblPage.string = this.curIndex + "/" + Math.ceil(t.length / 6);
    },
    onClickLeft() {
        if (! (this.curIndex <= 1)) {
            this.curIndex--;
            this.onShowList();
        }
    },
    onClickRight() {
        if (! (this.curIndex >= Math.ceil(n.jiulouProxy.yhInfo.list.length / 6))) {
            this.curIndex++;
            this.onShowList();
        }
    },
    onClickTab(t, e) {
        for (var o = parseInt(e), i = 0; i < this.btnArr.length; i++) this.btnArr[i].interactable = o != i;
        this.xinxiNode.active = 0 == o;
        this.laibinNode.active = 1 == o;
    },
});
