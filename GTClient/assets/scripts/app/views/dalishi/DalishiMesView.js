var i = require("List");
var n = require("Utils");
var l = require("Initializer");
var UrlLoad = require("UrlLoad");

cc.Class({
    extends: cc.Component,

    properties: {
        btnName: [cc.Node],
        list1: i,
        lblNode1: cc.Node,
        list2: i,
        lblNode2: cc.Node,
        input: cc.EditBox,
        box1: cc.Node,
        box2: cc.Node,
        box3: cc.Node,
        roleInfo: cc.Node,
        roleSpine: UrlLoad,
        lblName: cc.Label,
        lblShili: cc.Label,
        lblCount: cc.Label,
        scoreRank: cc.Label,
        seColor: cc.Color,
        norColor: cc.Color,
    },

    ctor() {
        this._curFuChouId = 0;
        this.curIndex = 0;
    },

    onLoad() {
        this.input.placeholder = i18n.t("COMMON_INPUT_TXT");
        facade.subscribe(l.dalishiProxy.UPDATE_DALISHI_ZHUISHA, this.onUpdateZhuisha, this);
        facade.subscribe(l.dalishiProxy.UPDATE_DALISHI_DEFLOG, this.onUpdateDefLog, this);
        facade.subscribe("DALISI_SERVANT_SELECT", this.onServantSelect, this);
        l.dalishiProxy.sendGetHistory();
        this.onTab(null, 1);
    },

    onTab(t, e) {
        if(null != t && !t.isChecked) {
            return;
        }
        var o = parseInt(e) - 1;
        this.curIndex = o;
        for(let i = 0, len = this.btnName.length; i < len; i++) {
            this.btnName[i].color = this.curIndex == i ? this.seColor : this.norColor;
        }
        //for (var i = 0; i < this.buttons.length; i++) this.buttons[i].interactable = i != o;
        this.box1.active = 0 == o;
        this.box2.active = 1 == o;
        this.box3.active = 2 == o;
        this.onShowData(o);
    },

    onUpdateDefLog() {
        0 == this.curIndex && this.onTab(null, 1);
    },

    onShowData(t) {
        switch (t) {
        case 0:
            l.dalishiProxy.defLog && l.dalishiProxy.defLog.length > 1 && l.dalishiProxy.defLog.sort(function(t, e) {
                return e.dtime - t.dtime;
            });
            this.list1.data = l.dalishiProxy.defLog;
            this.lblNode1.active = null == l.dalishiProxy.defLog || l.dalishiProxy.defLog.length <= 0;
            break;
        case 1:
            l.dalishiProxy.enemyMsg && l.dalishiProxy.enemyMsg.length > 1 && l.dalishiProxy.enemyMsg.sort(function(t, e) {
                return e.time - t.time;
            });
            this.list2.data = l.dalishiProxy.enemyMsg;
            this.lblNode2.active = null == l.dalishiProxy.enemyMsg || l.dalishiProxy.enemyMsg.length <= 0;
            break;
        case 2:
            this.input.string = "";
            this.roleInfo.active = !1;
        }
    },
    onClickFuChou(t, e) {
        var o = e.data;
        if (o) {
            if (l.dalishiProxy.isHaveFight()) {
                n.alertUtil.alert18n("YAMUN_HAVE_PLAYING_HERO");
                return;
            }
            this._curFuChouId = o.fuser.id;
            facade.send("DALISI_FUCHOU_SELECT", o.fuser.id);
            n.utils.openPrefabView("dalishi/DServantSelect", !1, {
                id: n.utils.getParamInt("gongdou_attack_id")
            });
        }
    },
    onServantSelect(t) {
        if (this.roleInfo.active && this.box3.active) {
            var e = n.utils.getParamInt("gongdou_zhuisha_id");
            if (l.bagProxy.getItemCount(e) < 1) {
                n.alertUtil.alertItemLimit(e);
                return;
            }
            l.dalishiProxy.sendZhuiSha(l.dalishiProxy.zhuisha.fuser.id, t);
            this.onClickClost();
        }
        if (this.box2.active && 0 != this._curFuChouId) {
            e = n.utils.getParamInt("gongdou_attack_id");
            if (l.bagProxy.getItemCount(e) < 1) {
                n.alertUtil.alertItemLimit(e);
                return;
            }
            l.dalishiProxy.sendFuChou(this._curFuChouId, t);
            this._curFuChouId = 0;
            this.onClickClost();
        }
    },
    onClickSearch() {
        var t = this.input.string;
        if (n.stringUtil.isBlank(t)) n.alertUtil.alert(i18n.t("SON_TO_QIN_UID"));
        else {
            this.input.string = "";
            l.dalishiProxy.sendFindZhuiSha(t);
        }
    },
    onUpdateZhuisha() {
        var t = l.dalishiProxy.zhuisha;
        this.roleInfo.active = null != t;
        if (this.roleInfo.active) {
            this.lblName.string = i18n.t("DALISI_USER_NAME_SCORE", {
                n: t.fuser.name,
                d: t.score
            });
            this.lblCount.string = t.hnum + "";
            this.lblShili.string = n.utils.formatMoney(t.fuser.shili);
            this.scoreRank.string = t.rank + "";
            //this.roelSpine.setClothes(t.fuser.sex, t.fuser.job, t.fuser.level, t.fuser.clothe);
            l.playerProxy.loadPlayerSpinePrefab(this.roleSpine,{job:t.fuser.job,level:t.fuser.level,clothe:t.fuser.clothe,clotheSpecial:t.fuser.clotheSpecial});
        }
    },
    onClickAttack() {
        l.dalishiProxy.isHaveFight() ? n.alertUtil.alert18n("YAMUN_HAVE_PLAYING_HERO") : n.utils.openPrefabView("dalishi/DServantSelect", !1, {
            id: n.utils.getParamInt("gongdou_zhuisha_id")
        });
    },
    onClickClost() {
        n.utils.closeView(this);
    },
});
