var i = require("Utils");
var n = require("Initializer");
var l = require("UIUtils");
var r = require("UrlLoad");
var a = require("JiBanShow");
var s = require("formula");
cc.Class({
    extends: cc.Component,
    properties: {
        lblLove: cc.Label,
        lblMeili: cc.Label,
        urlLoad: r,
        lblTalk: cc.Label,
        nodeWife: cc.Node,
        btnNode: cc.Node,
        btn_node: cc.Node,
        lblSonNum: cc.Label,
        lblWifeExp: cc.Label,
        lblWifeInfo: cc.Label,
        lblWifeInfo_2: cc.Label,
        btnLeft: cc.Node,
        btnRight: cc.Node,
        speakAnie: cc.Animation,
        luckImg: a,
        lblJbValue: cc.Label,
        changeNode: cc.Node,
        btnGuanShi: cc.Node,
        techangIcon: cc.Sprite,
        techangArr: [cc.SpriteFrame],
        skillRed: cc.Node,
    },
    ctor() {
        this._marryList = null;
        this._unMarryList = null;
        this._curIndex = 0;
        this._isMarry = !0;
        this.curWifeData = null;
    },
    onLoad() {
        facade.subscribe("WIFE_LIST_UPDATE", this.updateShow, this);
        facade.subscribe("UI_TOUCH_MOVE_LEFT", this.onClickLeft, this);
        facade.subscribe("UI_TOUCH_MOVE_RIGHT", this.onClickRight, this);
        facade.subscribe("PLAYER_HERO_SHOW", this.onHeroShow, this);
        facade.subscribe("UPDATE_WIFE_JB", this.updateCurShow, this);
        facade.subscribe(n.wifeProxy.WIFE_TALK_UPDATE, this.onWifeTalk, this);
        var t = this.node.openParam;
        this._curIndex = t.index;
        this.updateShow();
        this.btn_node.active = n.wifeProxy.getMarryList(!1).length > 1;
        l.uiUtils.scaleRepeat(this.btnLeft, 0.9, 1.2);
        l.uiUtils.scaleRepeat(this.btnRight, 0.9, 1.2);
        i.utils.showEffect(this.speakAnie, 0);
        this.changeNode.active = n.wifeProxy.getMarryList(!1).length > 1;
        this.onPlayVoice(null, null);
        this.onHeroShow();
        t.openSkill && this.onClickSkill();
    },
    updateShow() {
        if (null == this._marryList) {
            this._marryList = n.wifeProxy.getMarryList(!1);
            this._unMarryList = n.wifeProxy.getMarryList(!0);
        }
        this.btnNode.active = this._isMarry;
        this.updateCurShow();
    },
    updateCurShow() {
        var t = this._isMarry ? this._marryList[this._curIndex] : this._unMarryList[this._curIndex];
        this.nodeWife.active = null != t;
        if (t) {
            var e = localcache.getItem(localdb.table_wife, t.wid),
            o = n.wifeProxy.getWifeData(t.wid);
            this.techangIcon.spriteFrame = this.techangArr[t.type - 1];
            n.wifeProxy.curSelectWife = o;
            this.urlLoad.url = l.uiHelps.getWifeBody(e.res);
            if (o) {
                this.lblLove.string = o.love + "";
                this.lblMeili.string = o.flower + "";
                this.lblWifeExp.string = o.exp + "";
                this.lblSonNum.string = n.sonProxy.getWifeSonNum(o.id) + "";
                this.lblTalk.string = (n.playerProxy.userData.sex, t.talk[Math.floor(Math.random() * t.talk.length)]);
                var i = (2 == n.playerProxy.userData.sex ? e.info2: e.info).split("|");
                this.lblWifeInfo.string = i[0];
                this.lblWifeInfo_2.string = i.length >= 2 ? i[1] : "";
            }
            var r = n.jibanProxy.getWifeJbLv(e.wid).level % 1e3;
            this.luckImg.setValue(5, r);
            var a = n.jibanProxy.getWifeNextJb(r),
            s = r > 1 ? i18n.t("WIFE_SKILL_EFF_2", {
                num: n.jibanProxy.getWifeJbLv(e.wid).prop / 100
            }) : "";
            this.lblJbValue.string = null == a ? s: n.jibanProxy.getWifeJB(e.wid) + "/" + (a ? a.yoke: "8000") + s;
            this.skillRed.active = n.wifeProxy.hasSkillUp(e.wid);
        }
    },
    getFrom(t, e) {
        switch (t) {
        case "juqing":
            return i18n.t("WIFE_JUQING");
        case "xf":
            return i18n.t("WIFE_XF");
        case "shili":
            return i18n.t("WIFE_SHILI", {
                d: i.utils.formatMoney(e)
            });
        case "vip":
            return i18n.t("WIFE_VIP", {
                d: e
            });
        case "huodong":
            return i18n.t("WIFE_HUODONG");
        }
        return "";
    },
    wifeBtn() {
        this._isMarry = !0;
        this.updateShow();
    },
    flanceeBtn() {
        this._isMarry = !1;
        this.updateShow();
    },
    onClickLeft() {
        this.onClickTrun( - 1);
        i.utils.showEffect(this.speakAnie, 0);
        this.onPlayVoice(null, null);
        this.onHeroShow();
    },
    onClickRight() {
        this.onClickTrun(1);
        i.utils.showEffect(this.speakAnie, 0);
        this.onPlayVoice(null, null);
        this.onHeroShow();
    },
    onClickTrun(t) {
        var e = this._isMarry ? this._marryList.length: this._unMarryList.length;
        this._curIndex += t;
        this._curIndex = this._curIndex < 0 ? e - 1 : this._curIndex;
        this._curIndex = this._curIndex >= e ? 0 : this._curIndex;
        this.updateCurShow();
    },
    helpBtn() {},
    randomBtn() {
        n.wifeProxy.jingliData.num <= 0 ? i.alertUtil.alert(i18n.t("WIFE_JINGLING_LIMIT")) : n.wifeProxy.sendSJXO();
    },
    resetBtn() {
        var t = i.utils.getParamInt("hg_cost_item_jl");
        n.bagProxy.getItemCount(t) <= 0 ? i.alertUtil.alertItemLimit(t) : i.utils.showConfirmItem(i18n.t("WIFE_USE_JING_LI_DAN", {
            num: 1
        }), t, 1,
        function() {
            n.wifeProxy.sendWeige();
        },
        "WIFE_USE_JING_LI_DAN");
    },
    onClickOpen(t, e) {},
    onClickLove() {
        var t = s.formula.wife_meet_cost(n.wifeProxy.curSelectWife.flower, n.wifeProxy.curSelectWife.love),
        e = localcache.getItem(localdb.table_item, 1);
        i.utils.showConfirmItem(i18n.t("WIFE_XO_TIP", {
            name: e.name,
            price: t
        }), 1, n.playerProxy.userData.cash,
        function() {
            n.playerProxy.userData.cash < t ? i.alertUtil.alertItemLimit(1) : n.wifeProxy.sendXXOOnoBaby(n.wifeProxy.curSelectWife.id);
        },
        "WIFE_XO_TIP");
    },
    onClickGift() {
        n.wifeProxy.wifeGiftId = n.wifeProxy.curSelectWife.id;
        i.utils.openPrefabView("wife/GiftView");
    },
    onClickSkill() {
        i.utils.openPrefabView("wife/WifeSkillView");
    },
    onClickYJXO() {
        n.wifeProxy.jingliData.num <= 0 ? i.alertUtil.alert(i18n.t("WIFE_JINGLING_LIMIT")) : n.wifeProxy.sendYJXO();
    },
    onClickBack() {
        i.utils.closeView(this);
    },
    onClickChuYou() {
        var t = s.formula.wife_chuyou_cost(n.wifeProxy.curSelectWife.love),
        e = localcache.getItem(localdb.table_item, 1);
        i.utils.showConfirmItem(i18n.t("WIFE_CHU_YOU_TIP", {
            name: e.name,
            price: t
        }), 1, n.playerProxy.userData.cash,
        function() {
            n.playerProxy.userData.cash < t ? i.alertUtil.alertItemLimit(1) : n.wifeProxy.sendXXOO(n.wifeProxy.curSelectWife.id);
        },
        "WIFE_CHU_YOU_TIP");
    },
    onClickGuanBi() {
        i.utils.closeNameView("wife/WifeSelectView");
        i.utils.closeView(this);
    },
    onClickJiBan() {
        var t = this._isMarry ? this._marryList[this._curIndex] : this._unMarryList[this._curIndex];
        i.utils.openPrefabView("jiban/JibanView", !1, {
            wifeid: t.wid
        });
    },
    onPlayVoice(t, e) {
        if ("1" != e || !i.audioManager.isPlayLastSound()) {
            var o = this._isMarry ? this._marryList[this._curIndex] : this._unMarryList[this._curIndex],
            l = n.voiceProxy.randomWifeVoice(o.wid);
            if (l) {
                this.lblTalk.string = l.wifetext;
                i.audioManager.playSound("wife/" + l.wifevoice, !0, !0);
            }
        }
    },
    onClickSetWife() {
        var t = this._isMarry ? this._marryList[this._curIndex] : this._unMarryList[this._curIndex];
        n.playerProxy.sendHeroShow(t.wid + 200);
        i.alertUtil.alert(i18n.t("SERVANT_GUAN_SHI", {
            name: t.wname2
        }));
    },
    onHeroShow() {
        var t = this._isMarry ? this._marryList[this._curIndex] : this._unMarryList[this._curIndex];
        this.btnGuanShi.active = 2 != t.wid && (n.playerProxy.heroShow < 200 || t.wid != n.playerProxy.heroShow - 200);
    },
    onWifeTalk(t) {
        if (!i.audioManager.isPlayLastSound()) if (0 == t.chatType) this.onPlayVoice(null, null);
        else {
            var e = this._isMarry ? this._marryList[this._curIndex] : this._unMarryList[this._curIndex];
            n.playerProxy.addStoryId(t.stroyid);
            i.utils.openPrefabView("StoryView", !1, {
                wifeid: e.wid,
                type: 4,
                talkType: 2
            });
        }
    },
    onClickTalk() {
        var t = this._isMarry ? this._marryList[this._curIndex] : this._unMarryList[this._curIndex];
        n.wifeProxy.sendWifeTalk(t.wid);
    },
});
