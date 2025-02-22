var i = require("Utils");
var n = require("UrlLoad");
var l = require("ItemSlotUI");
var r = require("Initializer");
var a = require("UIUtils");
var s = require("BagProxy");
cc.Class({
    extends: cc.Component,
    properties: {
        explain: cc.Label,
        getWayDetails: cc.Label,
        unlockDetails: cc.Label,
        clothesName: cc.Label,
        roleSpine: n,
        ItemSlotUI: l,
        btnTakeOff: cc.Button,
        btnName: cc.Label,
        qiShi: cc.Label,
        zhiMou: cc.Label,
        zhengLue: cc.Label,
        meiLi: cc.Label,
        ep1: cc.Node,
        ep2: cc.Node,
        ep3: cc.Node,
        ep4: cc.Node,
        btnLeft: cc.Node,
        btnRight: cc.Node,
    },
    ctor() {
        this.curHero = null;
        this.skinSysArr = null;
        this.curIndex = 0;
        this.btnType = 0;
    },
    onLoad() {
        facade.subscribe("SERVANT_CHUANZHUANG", this.onHuanZhuang, this);
        this.curHero = this.node.openParam;
        this.skinSysArr = localcache.getGroup(localdb.table_heroClothe, "heroid", this.curHero.id);
        this.btnRight.active = null != this.skinSysArr && this.skinSysArr.length > 1;
        this.btnLeft.active = null != this.skinSysArr && this.skinSysArr.length > 1;
        var t = r.servantProxy.getSink(this.curHero.id);
        if (null != t && null != t.dress) {
            for (var e = 0; e < this.skinSysArr.length; e++) this.skinSysArr[e].id == t.dress && (this.curIndex = e);
            this.onHuanZhuang();
            this.showTheBtn(this.curIndex);
        } else {
            this.curDate(this.curIndex);
            this.showTheBtn(this.curIndex);
        }
    },
    onClickClose() {
        i.utils.closeView(this);
    },
    onClost() {
        r.servantProxy.curSelectId = 0;
        i.audioManager.playSound("", !0);
        i.utils.closeView(this);
        r.servantProxy.isRenMaiOpen ? (r.servantProxy.isRenMaiOpen = !1) : i.utils.openPrefabView("servant/ServantListView");
    },
    onBack() {
        r.servantProxy.curSelectId = 0;
        i.audioManager.playSound("", !0);
        i.utils.closeView(this, !0);
        r.servantProxy.isRenMaiOpen && (r.servantProxy.isRenMaiOpen = !1);
    },
    onHuanZhuang() {
        this.curDate(this.curIndex);
        this.showTheBtn(this.curIndex);
    },
    onClickLeft(t) {
        t < 300 || this.showClickData( - 1);
    },
    onClickRight(t) {
        t < 300 || this.showClickData(1);
    },
    showClickData(t) {
        this.curIndex += t;
        this.curIndex = this.curIndex < 0 ? this.skinSysArr.length - 1 : this.curIndex;
        this.curIndex = this.curIndex > this.skinSysArr.length - 1 ? 0 : this.curIndex;
        this.curDate(this.curIndex);
        this.showTheBtn(this.curIndex);
    },
    curDate(t) {
        var e = this.skinSysArr[t];
        this.qiShi.string = e.prop[0][1];
        this.zhiMou.string = e.prop[0][2];
        this.zhengLue.string = e.prop[0][3];
        this.meiLi.string = e.prop[0][4];
        this.roleSpine.url = a.uiHelps.getServantSkinSpine(e.model);
        this.explain.string = e.txt;
        this.getWayDetails.string = e.way;
        var o = new a.ItemSlotData();
        o.id = e.id;
        o.kind = s.DataType.HERO_CLOTHE;
        this.ItemSlotUI.data = o;
    },
    showTheBtn(t) {
        var e = r.servantProxy.getSink(this.curHero.id);
        if (null != e) {
            var o = this.skinSysArr[t];
            if (e.dress != o.id) for (var i = 0; i < e.list.length; i++) {
                if (o.id == e.list[i]) {
                    this.btnTakeOff.interactable = !0;
                    this.btnName.string = i18n.t("SERVANT_HUANZHUANG_WEAR");
                    this.btnType = 2;
                    return;
                }
                this.btnTakeOff.interactable = !1;
                this.btnName.string = i18n.t("SERVANT_HUANZHUANG_WEAR");
            } else {
                this.btnTakeOff.interactable = !0;
                this.btnName.string = i18n.t("SERVANT_HUANZHUANG_TAKEOFF");
                this.btnType = 1;
            }
        } else {
            this.btnTakeOff.interactable = !1;
            this.btnName.string = i18n.t("SERVANT_HUANZHUANG_WEAR");
        }
    },
    ClickTakeOff() {
        var t = this.skinSysArr[this.curIndex];
        1 != this.btnType ? 2 == this.btnType && r.servantProxy.sendHz(this.curHero.id, t.id) : r.servantProxy.sendHz(this.curHero.id, 0);
    },
});
