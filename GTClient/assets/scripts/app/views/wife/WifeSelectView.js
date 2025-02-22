var i = require("List");
var n = require("Initializer");
var l = require("Utils");
var r = require("UIUtils");
var a = require("ItemSlotUI");
var s = require("SelectMax");
cc.Class({
    extends: cc.Component,
    properties: {
        jingLiText: cc.Label,
        btn_Reset: cc.Button,
        btn_ResetJq: cc.Button,
        btn_sjwh: cc.Button,
        btn_yjwh: cc.Button,
        btn_yjcy: cc.Node,
        btn_SJCY: cc.Button,
        jiaqiText: cc.Label,
        wifeList: i,
        itemSlot: a,
        lblItemName: cc.Label,
        bar: s,
        useNode: cc.Node,
        check_onKey: cc.Toggle,
    },
    ctor() {
        this.cyLv = 0;
        this.whLv = 0;
        this.curWifeList = [];
    },
    onLoad() {
        facade.subscribe("WIFE_JING_LI", this.onJingliUpdate, this);
        facade.subscribe("WIFE_JIA_QI", this.onJiaQiUpdate, this);
        facade.subscribe(n.wifeProxy.WIFE_LIST_UPDATE, this.showWifeList, this);
        facade.subscribe("UI_TOUCH_MOVE_LEFT", this.onClickClose, this);
        this.showWifeList();
        this.onJingliUpdate();
        this.onJiaQiUpdate();
        this.onClickBtn(null, "0");
        this.whLv = l.utils.getParamInt("wenhou_vip_level");
        this.cyLv = l.utils.getParamInt("chuyou_vip_level");
    },
    showWifeList() {
        for (var t = localcache.getList(localdb.table_wife), e = [], o = 0; o < t.length; o++) 1 == t[o].open && (n.jibanProxy.getWifeJB(t[o].wid) > 0 || null != n.wifeProxy.getWifeData(t[o].wid)) && e.push(t[o]);
        e.sort(function(t, e) {
            var o = n.wifeProxy.getWifeData(t.wid),
            i = n.wifeProxy.getWifeData(e.wid),
            l = null == o ? 1 : 0,
            r = null == i ? 1 : 0,
            a = null == o ? 0 : o.love,
            s = null == i ? 0 : i.love;
            return l == r ? s - a: l - r;
        });
        n.wifeProxy.wifeSys = e;
        this.wifeList.data = e;
    },
    onClickBtn(t, e) {
        var o = parseInt(e);
        this.curWifeList = n.wifeProxy.getMarryList(1 == o);
        this.curWifeList.length;
    },
    onJingliUpdate() {
        var t = localcache.getItem(localdb.table_vip, n.playerProxy.userData.vip),
        e = n.wifeProxy.jingliData;
        e.num < t.jingli ? r.uiUtils.countDown(e.next, this.jingLiText,
        function() {
            n.playerProxy.sendAdok(e.label);
        },
        0 == e.num) : this.jingLiText.unscheduleAllCallbacks();
        e.num > 0 && (this.jingLiText.string = i18n.t("COMMON_NUM", {
            f: e.num,
            s: t.jingli
        }));
        this.onShowBtnState();
    },
    onJiaQiUpdate() {
        var t = localcache.getItem(localdb.table_vip, n.playerProxy.userData.vip),
        e = n.wifeProxy.jiaqiData;
        e.num < t.jiaqi ? r.uiUtils.countDown(e.next, this.jiaqiText,
        function() {
            n.playerProxy.sendAdok(e.label);
        },
        0 == e.num) : this.jiaqiText.unscheduleAllCallbacks();
        e.num > 0 && (this.jiaqiText.string = i18n.t("COMMON_NUM", {
            f: e.num,
            s: t.jiaqi
        }));
        this.btn_SJCY.interactable = e.num > 0;
        this.onShowBtnState();
    },
    onShowBtnState() {
        if (n.wifeProxy.jingliData.num <= 0) {
            this.btn_Reset.node.active = !0;
            this.btn_sjwh.node.active = this.btn_yjwh.node.active = !1;
        } else {
            this.btn_sjwh.node.active = !this.check_onKey.isChecked;
            this.btn_yjwh.node.active = this.check_onKey.isChecked;
            this.btn_Reset.node.active = !1;
        }
        if (n.wifeProxy.jiaqiData.num <= 0) {
            this.btn_ResetJq.node.active = !0;
            this.btn_SJCY.node.active = this.btn_yjcy.active = !1;
        } else {
            this.btn_SJCY.node.active = !this.check_onKey.isChecked;
            this.btn_yjcy.active = this.check_onKey.isChecked;
            this.btn_ResetJq.node.active = !1;
        }
    },
    onClickYJXO() {
        n.playerProxy.userData.vip < this.whLv ? l.alertUtil.alert(i18n.t("WIFE_ONE_KEY_OPEN_WEN_HOU", {
            num: this.whLv
        })) : n.wifeProxy.jingliData.num <= 0 ? l.alertUtil.alert(i18n.t("WIFE_JINGLING_LIMIT")) : n.wifeProxy.sendYJXO();
    },
    randomBtn() {
        n.wifeProxy.jingliData.num <= 0 ? l.alertUtil.alert(i18n.t("WIFE_JINGLING_LIMIT")) : n.wifeProxy.sendSJXO();
    },
    resetBtn() {
        var t = l.utils.getParamInt("hg_cost_item_jl"),
        e = n.bagProxy.getItemCount(t);
        if (e <= 0) l.alertUtil.alertItemLimit(t);
        else {
            var o = localcache.getItem(localdb.table_item, t);
            l.utils.showConfirmItem(i18n.t("WIFE_USE_JING_LI_DAN", {
                name: o.name,
                num: 1
            }), t, e,
            function() {
                n.wifeProxy.sendWeige();
            },
            "WIFE_USE_JING_LI_DAN");
        }
    },
    onClickClose() {
        l.utils.closeView(this, !0);
    },
    onClickRandowChuYou() {
        n.wifeProxy.sendSJCY();
    },
    restJiaQi() {
        var t = l.utils.getParamInt("jiaqi_cost_item_chuyou"),
        e = n.bagProxy.getItemCount(t);
        if (e <= 0) l.alertUtil.alertItemLimit(t);
        else {
            var o = localcache.getItem(localdb.table_item, t),
            i = localcache.getItem(localdb.table_vip, n.playerProxy.userData.vip);
            this.useNode.active = !0;
            this.bar.max = e >= i.jiaqi ? i.jiaqi: e;
            this.bar.curValue = 1;
            this.lblItemName.string = o.name;
            var a = new r.ItemSlotData();
            a.id = o.id;
            a.count = e;
            this.itemSlot.data = a;
        }
    },
    onClickUseChe() {
        n.wifeProxy.sendJiaQi(this.bar.curValue ? this.bar.curValue: 1);
        this.useNode.active = !1;
    },
    onCloseUseNode() {
        this.useNode.active = !1;
    },
    onClickYjcy() {
        n.playerProxy.userData.vip < this.cyLv ? l.alertUtil.alert(i18n.t("WIFE_ONE_KEY_OPEN_CHU_YOU", {
            num: this.cyLv
        })) : n.wifeProxy.sendYJCY();
    },
});
