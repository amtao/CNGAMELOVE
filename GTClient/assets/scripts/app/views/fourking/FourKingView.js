var i = require("List");
var n = require("UrlLoad");
var l = require("Utils");
var r = require("Initializer");
var a = require("UIUtils");
var s = require("ServantStarShow");
var c = require("ItemSlotUI");
cc.Class({
    extends: cc.Component,
    properties: {
        lblName: cc.Label,
        lblZz: cc.Label,
        tablentList: i,
        tablentNode: cc.Node,
        urlArr: [n],
        nameArr: [n],
        nodeArr: [cc.Node],
        url_1: n,
        url_2: n,
        stars: s,
        itemSlot: c,
        lblLeader: cc.Label,
        lblCount: cc.Label,
        nodeOwned: cc.Node,
        nodeGeted: cc.Node,
        nodeGo: cc.Node,
        leaderNode: cc.Node,
        leader: cc.Node,
        lblTime: cc.Label,
        itemNum: cc.Node,
    },
    ctor() {
        this.curSelect = null;
        this.posY = {};
        this._curIndex = 0;
        this._leaderPosX = 0;
        this.isClick = !1;
        this.isMoving = !1;
    },
    onLoad() {
        facade.subscribe(r.fourKingProxy.FOURKING_DATA_UPDATE, this.onDatUpdate, this);
        facade.subscribe(r.playerProxy.PLAYER_USER_UPDATE, this.onDatUpdate, this);
        facade.subscribe("UI_TOUCH_MOVE_LEFT", this.onClickClose, this);
        this.posY = {
            0 : this.nodeArr[0].y,
            1 : this.nodeArr[1].y,
            2 : this.nodeArr[2].y,
            3 : this.nodeArr[3].y
        };
        r.fourKingProxy.sendOpenPrince();
        this._leaderPosX = this.leaderNode.x;
    },
    onDatUpdate() {
        var t = this,
        e = r.fourKingProxy.data;
        if (e) {
            a.uiUtils.countDown(e.info.eTime, this.lblTime,
            function() {
                l.timeUtil.second >= e.info.eTime && (t.lblTime.string = i18n.t("ACTHD_OVERDUE"));
            });
            for (var o = new Array(), i = 0; i < e.rwd.length; i++) {
                if (i < this.urlArr.length) {
                    this.urlArr[i].url = a.uiHelps.getServantSpine(e.rwd[i].heroid);
                    this.nameArr[i].url = a.uiHelps.getStoryRoleName(e.rwd[i].heroid);
                }
                o.push(e.rwd[i].heroid);
            }
            this.itemSlot.data = {
                id: e.rwd[this._curIndex].itemid
            };
            this.lblLeader.string = r.servantProxy.getFourKingActivieStr(o[0]);
        }
    },
    onClickRecharge() {
        var t = r.fourKingProxy.data.rwd[this._curIndex];
        if (t) {
            if (r.bagProxy.getItemCount(t.itemid) < t.need) {
                l.alertUtil.alertItemLimit(t.itemid);
                return;
            }
            r.limitActivityProxy.sendGetActivityReward(r.limitActivityProxy.FOURKING_ID, 0 == t.heroid ? t.heroid + 200 : t.heroid);
            this.onClickBg();
            this.onLoad();
        }
    },
    onClickRole(t, e) {
        this._curIndex = e;
        this.itemNum.active = !0;
        var o = r.fourKingProxy.data.rwd[this._curIndex];
        this.itemSlot.data = {
            id: o.itemid
        };
        var i = r.bagProxy.getItemCount(o.itemid);
        this.lblCount.string = i18n.t("COMMON_NUM", {
            f: i,
            s: o.need
        });
        for (var n = 0; n < this.nodeArr.length; n++) {
            if (parseInt(e) == n) {
                this.nodeArr[n].setSiblingIndex(3);
                this.nodeArr[n].y = this.posY[n + ""] - 82;
            } else {
                0 == n || 3 == n ? this.nodeArr[n].setSiblingIndex(0) : 2 == n ? this.nodeArr[n].setSiblingIndex(1) : 1 == n && this.nodeArr[n].setSiblingIndex(2);
                this.nodeArr[n].y = this.posY[n + ""];
            }
            this.nameArr[n].node.active = n == parseInt(e);
            this.urlArr[n].node.scale = n == parseInt(e) ? 0.62 : 0.52;
        }
        parseInt(e) < r.fourKingProxy.data.rwd.length && (this.curSelect = r.fourKingProxy.data.rwd[parseInt(e)].heroid);
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
        this.nodeOwned.active = null != s && 0 != r.fourKingProxy.data.rwd[this._curIndex].open && r.fourKingProxy.data.rwd[this._curIndex].open != this.curSelect;
        this.nodeGeted.active = r.fourKingProxy.data.rwd[this._curIndex].open == this.curSelect;
        this.nodeGo.active = r.fourKingProxy.data.rwd[this._curIndex].open && 0 != r.fourKingProxy.data.rwd[this._curIndex].open;
    },
    onClickBg() {
        for (var t = 0; t < this.nodeArr.length; t++) {
            this.nodeArr[t].y = this.posY[t + ""];
            this.nameArr[t].node.active = !1;
            this.urlArr[t].node.scale = 0.52;
        }
        this.itemNum.active = !1;
        this.tablentNode.active = !1;
        this.nodeArr[0].setSiblingIndex(0);
        this.nodeArr[1].setSiblingIndex(2);
        this.nodeArr[2].setSiblingIndex(1);
        this.nodeArr[3].setSiblingIndex(0);
        this.curSelect = null;
    },
    onClickClose() {
        l.utils.closeView(this);
    },
    onClickLeader() {
        if (1 != this.isMoving) {
            this.isMoving = !0;
            if (0 == this.isClick) {
                this.isClick = !0;
                var t = cc.moveTo(1, cc.p(this.leader.x, this.leader.y));
                this.leaderNode.runAction(t);
                this.scheduleOnce(function() {
                    this.isMoving = !1;
                },
                1);
            } else {
                this.isClick = !1;
                t = cc.moveTo(1, cc.p(this._leaderPosX, this.leader.y));
                this.leaderNode.runAction(t);
                this.scheduleOnce(function() {
                    this.isMoving = !1;
                },
                1);
            }
        }
    },
});
