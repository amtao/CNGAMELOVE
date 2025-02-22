var i = require("List");
var n = require("UrlLoad");
var l = require("Utils");
var r = require("Initializer");
var a = require("UIUtils");
var s = require("ServantStarShow");
var c = require("TimeProxy");
var _ = require("ItemSlotUI");
cc.Class({
    extends: cc.Component,
    properties: {
        lblName: cc.Label,
        lblTime: cc.Label,
        lblMoney: cc.Label,
        lblZz: cc.Label,
        lblJindu: cc.Label,
        tablentList: i,
        tablentNode: cc.Node,
        btnRecharge: cc.Node,
        btnReward: cc.Node,
        btnYlq: cc.Node,
        btnYyy: cc.Node,
        proBar: cc.ProgressBar,
        urlArr: [n],
        nameArr: [n],
        nodeArr: [cc.Node],
        url_1: n,
        url_2: n,
        stars: s,
        itemNode: cc.Node,
        itemSlot: _,
        desNode: cc.Node,
        lblLeader: cc.Label,
    },
    ctor() {
        this.curSelect = null;
        this.posY = {};
        this.flag = !1;
    },
    onLoad() {
        facade.subscribe(r.princeRecruitProxy.PRINCE_DATA_UPDATE, this.onDatUpdate, this);
        facade.subscribe(r.playerProxy.PLAYER_USER_UPDATE, this.onDatUpdate, this);
        facade.subscribe("UI_TOUCH_MOVE_LEFT", this.onClickClose, this);
        this.posY = {
            0 : this.nodeArr[0].y,
            1 : this.nodeArr[1].y,
            2 : this.nodeArr[2].y,
            3 : this.nodeArr[3].y
        };
        r.princeRecruitProxy.sendOpenPrince();
    },
    onDatUpdate() {
        var t = this,
        e = r.princeRecruitProxy.data.cfg,
        o = r.princeRecruitProxy.data;
        if (e) {
            a.uiUtils.countDown(e.info.eTime, this.lblTime,
            function() {
                l.timeUtil.second >= e.info.eTime && (t.lblTime.string = i18n.t("ACTHD_OVERDUE"));
            });
            this.lblMoney.string = e.need + "";
            this.lblJindu.string = i18n.t("COMMON_NUM", {
                f: o.cons,
                s: e.need
            });
            this.proBar.progress = o.cons / e.need;
            this.btnRecharge.active = o.cons < e.need;
            this.btnReward.active = o.cons >= e.need && 0 == o.get;
            this.btnYlq.active = o.get == this.curSelect;
            for (var i = 0; i < e.heros.length; i++) if (i < this.urlArr.length) {
                this.urlArr[i].url = a.uiHelps.getServantSpine(e.heros[i]);
                this.nameArr[i].url = a.uiHelps.getStoryRoleName(e.heros[i]);
            }
            this.itemNode.active = 0 == r.princeRecruitProxy.data.type;
            this.desNode.active = 1 == r.princeRecruitProxy.data.type;
            this.itemSlot.data = r.princeRecruitProxy.data.cfg.rwd;
            this.lblLeader.string = r.servantProxy.getLeadActivieStr(r.princeRecruitProxy.data.cfg.heros[0]);
        }
    },
    onClickRecharge() {
        this.flag = !0;
        this.flag && c.funUtils.openView(c.funUtils.recharge.id);
        this.scheduleOnce(this.onTimer, 0.5);
    },
    onTimer() {
        this.flag = this.flag;
    },
    onClickReward() {
        var t = this;
        if (null == this.curSelect && 1 == r.princeRecruitProxy.data.type) l.alertUtil.alert18n("PRINCE_GET_REWARD_SELECT");
        else if (1 == r.princeRecruitProxy.data.type) {
            r.servantProxy.getHeroData(this.curSelect) ? l.alertUtil.alert18n("PRINCE_YI_HUO_DE") : l.utils.showConfirm(i18n.t("PRINCE_SHI_FOU_XUAN_ZE"),
            function() {
                r.princeRecruitProxy.sendGetReward(t.curSelect);
            });
        } else r.princeRecruitProxy.sendGetReward(0);
    },
    onClickRole(t, e) {
        for (var o = 0; o < this.nodeArr.length; o++) {
            if (parseInt(e) == o) {
                this.nodeArr[o].setSiblingIndex(3);
                this.nodeArr[o].y = this.posY[o + ""] - 82;
            } else {
                0 == o || 3 == o ? this.nodeArr[o].setSiblingIndex(0) : 2 == o ? this.nodeArr[o].setSiblingIndex(1) : 1 == o && this.nodeArr[o].setSiblingIndex(2);
                this.nodeArr[o].y = this.posY[o + ""];
            }
            this.nameArr[o].node.active = o == parseInt(e);
            this.urlArr[o].node.scale = o == parseInt(e) ? 0.62 : 0.52;
        }
        parseInt(e) < r.princeRecruitProxy.data.cfg.heros.length && (this.curSelect = r.princeRecruitProxy.data.cfg.heros[parseInt(e)]);
        this.showTanlent();
    },
    showTanlent() {
        var t = localcache.getItem(localdb.table_hero, this.curSelect),
        e = [];
        if (t) {
            this.lblName.string = t.name;
            for (var o = 0,
            i = 0; i < t.skills.length; i++) {
                var n = localcache.getItem(localdb.table_epSkill, t.skills[i].id);
                o += n.star;
                var l = {};
                l.id = n.sid;
                l.level = l.hlv = 0;
                e.push(l);
            }
            this.lblZz.string = i18n.t("SERVANT_PROP_TOTAL", {
                value: o
            });
            this.stars.setValue(t.star);
            this.url_1.url = a.uiHelps.getLangSp(t.spec[0]);
            this.url_2.node.active = t.spec.length > 1;
            t.spec.length > 1 && (this.url_2.url = a.uiHelps.getLangSp(t.spec[1]));
        }
        this.tablentList.data = e;
        this.tablentNode.active = !0;
        var s = r.servantProxy.getHeroData(this.curSelect);
        this.btnYyy.active = null != s && 0 != r.princeRecruitProxy.data.get && r.princeRecruitProxy.data.get != this.curSelect;
        this.btnYlq.active = r.princeRecruitProxy.data.get == this.curSelect;
    },
    onClickBg() {
        for (var t = 0; t < this.nodeArr.length; t++) {
            this.nodeArr[t].y = this.posY[t + ""];
            this.nameArr[t].node.active = !1;
            this.urlArr[t].node.scale = 0.52;
        }
        this.tablentNode.active = !1;
        this.nodeArr[0].setSiblingIndex(0);
        this.nodeArr[1].setSiblingIndex(2);
        this.nodeArr[2].setSiblingIndex(1);
        this.nodeArr[3].setSiblingIndex(0);
        this.curSelect = null;
        this.btnYyy.active = !1;
    },
    onClickClose() {
        l.utils.closeView(this);
    },
});
