var i = require("Utils");
var n = require("List");
var l = require("UIUtils");
var r = require("ConfirmView");
var a = require("Initializer");
var s = require("BalloonPoint");
cc.Class({
    extends: cc.Component,
    properties: {
        scroll: cc.ScrollView,
        lblTime: cc.Label,
        content: cc.Node,
        records: n,
        pointItem: cc.Node,
        balloon: cc.Node,
        lblCount: cc.Label,
        recordScroll: cc.ScrollView,
        check: cc.Toggle,
    },
    ctor() {
        this.nextId = 0;
        this._oldId = 0;
        this._pointNum = 0;
        this._backNum = 0;
        this._maxNum = 0;
        this.selfMoving = !1;
        this.isMove = !1;
        this.isFirst = !0;
        this.playType = "";
        this.pointList = [];
        this.curAct = null;
        this.isEnd = !1;
    },
    onLoad() {
        facade.subscribe(a.balloonProxy.BALLOON_CFG_DATA, this.onBalloonData, this);
        facade.subscribe(a.balloonProxy.BALLOON_ACT_DATA, this.onActData, this);
        facade.subscribe(a.balloonProxy.BALLOON_MOVE_POINT, this.updateMove, this);
        facade.subscribe(a.bagProxy.UPDATE_BAG_ITEM, this.onItemUpdate, this);
        facade.subscribe(a.balloonProxy.BALLOON_RECORDS, this.onRecords, this);
        a.balloonProxy.sendOpenActivity();
        this.createPoint();
    },
    onBalloonData() {
        if (a.balloonProxy.cfg) {
            var t = this;
            l.uiUtils.countDown(a.balloonProxy.cfg.info.eTime, this.lblTime,
            function() {
                t.lblTime.string = i18n.t("ACTHD_OVERDUE");
            },
            !0, "USER_REMAIN_TIME", "d");
        }
    },
    onActData() {
        this.curAct = a.balloonProxy.getNextAct(this._oldId);
        if (null != this.curAct) {
            this.isEnd = !1;
            var t = this.curAct;
            if (null != t) {
                var e = localcache.getItem(localdb.table_balloon_point, 0 == t.place ? 1 : t.place);
                if (null != e) if (this.isFirst && 0 == this._oldId) {
                    this.balloon.x = e.x;
                    this.balloon.y = e.y;
                    this.isFirst = !1;
                    this.nextId = this._oldId = t.place;
                    this.onItemUpdate();
                    if ((null == t.cons || 0 == t.cons) && a.balloonProxy.isFirst) {
                        var o = i.utils.getParamStr("reqiqiu_story_id");
                        a.playerProxy.addStoryId(o);
                        i.utils.openPrefabView("StoryView", !1, {
                            type: 3
                        });
                        a.balloonProxy.isFirst = !1;
                    }
                    this.check.isChecked = a.balloonProxy.isSelf;
                    this.updatePoint();
                } else {
                    this.nextId = t.place;
                    facade.send(a.balloonProxy.BALLOON_MOVE_POINT);
                }
            }
        }
    },
    createPoint() {
        this.pointList = localcache.getList(localdb.table_balloon_point);
        this._maxNum = this.pointList.length;
        for (var t = 0; t < this.pointList.length; t++) {
            var e = cc.instantiate(this.pointItem);
            e.getComponent(s).data = this.pointList[t];
            e.active = !0;
            e.x = this.pointList[t].x;
            e.y = this.pointList[t].y;
            this.content.addChild(e);
        }
        this.balloon.setSiblingIndex(99);
    },
    updateMove() {
        this._pointNum = this.nextId - this._oldId;
        this.unscheduleAllCallbacks();
        if (! (this._pointNum <= 0)) {
            this.isMove = !0;
            this.schedule(this.playAction, 0.5);
        }
    },
    playAction() {
        if (this._pointNum > 0) {
            this._pointNum--;
            this._oldId++;
            this.moveBalloon();
        } else {
            if (this.isEnd && !a.balloonProxy.isSelf) return;
            i.alertUtil.alert("BALLOON_JIN_FEN", {
                num: this.curAct.add
            });
            if (2 == this.curAct.type) {
                var t = localcache.getItem(localdb.table_balloon_event, this.curAct.tid);
                null != t && i.alertUtil.alert(t.talk1);
            } else 3 == this.curAct.type && i.alertUtil.alert18n("BALLOON_FINISH");
            var e = a.balloonProxy.getLastAct();
            if (null != e && e.place == this._oldId) {
                this.unscheduleAllCallbacks();
                this.resetPoint();
                a.timeProxy.floatReward();
                this.isMove = !1;
                this.isEnd = !0;
                if (this.check.isChecked && a.balloonProxy.isSelf) if (a.bagProxy.getItemCount(a.balloonProxy.cfg.need) > 0) this.scheduleOnce(this.onSelfTimer, 1);
                else {
                    this.check.isChecked = a.balloonProxy.isSelf = this.selfMoving = !1;
                    i.alertUtil.alertItemLimit(a.balloonProxy.cfg.need);
                    this.scheduleOnce(this.colseTipWin, 1);
                }
            } else {
                this.resetPoint();
                this.curAct = a.balloonProxy.getNextAct(this._oldId);
                if (null == this.curAct) return;
                this.nextId = this.curAct.place;
                this._pointNum = this.nextId - this._oldId;
            }
        }
    },
    updatePoint() {
        for (var t = this.content.getComponentsInChildren(s), e = 0; e < t.length; e++) {
            var o = t[e],
            i = t[e].data;
            if (o) {
                o.yellowEvent.node.active = (2 == i.type || 4 == i.type) && i.id <= this._oldId;
                o.yellowNor.node.active = 1 == i.type && i.id <= this._oldId;
                o.redEvent.node.active = (2 == i.type || 4 == i.type) && i.id > this._oldId;
                o.redNor.node.active = 1 == i.type && i.id > this._oldId;
                o.city.active = 2 == i.type || 3 == i.type || 4 == i.type;
                if (o.city.active) {
                    o.cityUrl.url = l.uiHelps.getXunfangIcon(i.icon);
                    107 == i.icon && (o.city.y = -50);
                    o.cityName.string = i.name;
                }
            }
        }
    },
    onTimer() {
        if (this._pointNum > 0) {
            this._pointNum--;
            this._oldId++;
            this.moveBalloon();
        } else if (this._backNum > 0) {
            this._backNum--;
            this._oldId--;
            this.nextId--;
            this.moveBalloon();
        } else {
            if (null == this.curAct) return;
            if (this.isEnd && !a.balloonProxy.isSelf) {
                this.unscheduleAllCallbacks();
                return;
            }
            i.alertUtil.alert("BALLOON_JIN_FEN", {
                num: this.curAct.add
            });
            if (2 == this.curAct.type) {
                var t = localcache.getItem(localdb.table_balloon_event, this.curAct.tid);
                null != t && i.alertUtil.alert(t.talk1);
            } else 3 == this.curAct.type && i.alertUtil.alert18n("BALLOON_FINISH");
            this.isMove = !1;
            var e = a.balloonProxy.getLastAct();
            if (null != e && e.place == this._oldId) {
                this.unscheduleAllCallbacks();
                this.resetPoint();
                a.timeProxy.floatReward();
                this.isEnd = !0;
                if (this.check.isChecked && a.balloonProxy.isSelf) if (a.bagProxy.getItemCount(a.balloonProxy.cfg.need) > 0) this.scheduleOnce(this.onSelfTimer, 1);
                else {
                    this.check.isChecked = a.balloonProxy.isSelf = this.selfMoving = !1;
                    i.alertUtil.alertItemLimit(a.balloonProxy.cfg.need);
                    this.scheduleOnce(this.colseTipWin, 1);
                }
            } else {
                this.unscheduleAllCallbacks();
                this.resetPoint();
                a.balloonProxy.isPlayEnd || facade.send(a.balloonProxy.BALLOON_ACT_DATA);
            }
        }
    },
    resetPoint() {
        var t = localcache.getList(localdb.table_balloon_point);
        if (this.curAct && t[t.length - 1].id == this.curAct.place) {
            this.balloon.x = this.pointList[0].x;
            this.balloon.y = this.pointList[0].y;
            this._oldId = this.nextId = 1;
            this.curAct && (this.curAct.place = 1);
            this.updatePoint();
        }
    },
    moveBalloon() {
        var t = localcache.getItem(localdb.table_balloon_point, this._oldId);
        if (null != t) {
            this.balloon.runAction(cc.moveTo(0.1, new cc.Vec2(t.x, t.y)));
            this.updatePoint();
        }
    },
    onClickPlay(t, e) {
        var o = this;
        if (a.balloonProxy.isSelf && this.check.isChecked && this.selfMoving) i.utils.showConfirm(i18n.t("BALLOON_GUAN_BI"),
        function() {
            o.check.isChecked = a.balloonProxy.isSelf = o.selfMoving = !1;
        });
        else if (this.isMove) i.alertUtil.alert18n("GIRLS_DAY_ROLLING");
        else if (i.timeUtil.second > a.balloonProxy.cfg.info.eTime) i.alertUtil.alert18n("ACTHD_OVERDUE");
        else {
            var n = parseInt(e);
            this.playType = e;
            if (a.bagProxy.getItemCount(a.balloonProxy.cfg.need) < n) {
                i.alertUtil.alertItemLimit(a.balloonProxy.cfg.need);
                i.utils.openPrefabView("ActivitySpecialBuy", null, {
                    data: a.balloonProxy.shop[0],
                    activityId: a.balloonProxy.cfg.info.id
                });
            } else {
                if (a.balloonProxy.isSelf) {
                    this.curAct && this.curAct.place == this.pointList[this.pointList.length - 1].id && this._oldId != this.pointList[0].id && this.resetPoint();
                    var l = "BALLOON_SELF_" + e;
                    i.utils.showConfirm(i18n.t(l),
                    function(t) {
                        if (t != r.NO) {
                            a.balloonProxy.sendPlay(n);
                            i.alertUtil.alert18n("BALLOON_MOVEING_START");
                            o.selfMoving = !0;
                            o.isMove = !0;
                        }
                    });
                } else {
                    this.curAct && this.curAct.place == this.pointList[this.pointList.length - 1].id && this._oldId != this.pointList[0].id && this.resetPoint();
                    a.balloonProxy.sendPlay(n);
                    this.isMove = !0;
                }
            }
        }
    },
    onClickTab(t, e) {
        var o = this;
        if (a.balloonProxy.isSelf && this.check.isChecked) i.utils.showConfirm(i18n.t("BALLOON_GUAN_BI"),
        function() {
            o.check.isChecked = a.balloonProxy.isSelf = o.selfMoving = !1;
        });
        else switch (e) {
        case "1":
            i.utils.openPrefabView("ActivityShopView", null, a.balloonProxy.dhShop);
            break;
        case "2":
            i.utils.openPrefabView("limitactivity/LimitActivityView", null, {
                type: a.limitActivityProxy.BALLOON_TYPE
            });
            break;
        case "3":
            i.utils.openPrefabView("balloon/BalloonRankRwd");
            break;
        case "4":
            i.utils.openPrefabView("ActivitySpecialBuy", null, {
                data: a.balloonProxy.shop[0],
                activityId: a.balloonProxy.cfg.info.id
            });
        }
    },
    onItemUpdate() {
        a.balloonProxy.cfg && (this.lblCount.string = a.bagProxy.getItemCount(a.balloonProxy.cfg.need) + "");
    },
    onClickClose() {
        a.balloonProxy.isSelf = !1;
        i.utils.closeView(this);
    },
    onRecords() {
        this.records.data = a.balloonProxy.records;
        this.recordScroll.scrollToBottom();
    },
    onClickCheck() {
        if (this.check.isChecked && this.selfMoving) {
            i.alertUtil.alert18n("BALLOON_AUTO_CLOSE");
            this.check.isChecked = !1;
            this.selfMoving = !1;
            a.balloonProxy.isSelf = !1;
        } else if (this.isMove && this.check.isChecked) {
            i.alertUtil.alert18n("BALLOON_MOVING");
            this.check.isChecked = !1;
        } else {
            if (!this.check.isChecked) {
                this.selfMoving = !1;
                i.alertUtil.alert18n("BALLOON_AUTO_CLOSE");
            }
            a.balloonProxy.isSelf = this.check.isChecked;
        }
    },
    colseTipWin() {
        if (i.utils.isOpenView("AlertItemMore")) {
            i.utils.closeNameView("AlertItemMore");
            i.utils.popNext(!1);
        } else if (i.utils.isOpenView("AlertItemShow")) {
            i.utils.closeNameView("AlertItemShow");
            i.utils.popNext(!1);
        }
    },
    onSelfTimer() {
        this.colseTipWin();
        if (a.balloonProxy.cfg.info.eTime < i.timeUtil.second) {
            this.check.isChecked = a.balloonProxy.isSelf = this.selfMoving = !1;
            i.alertUtil.alert18n("ACTHD_OVERDUE");
        } else if (a.bagProxy.getItemCount(a.balloonProxy.cfg.need) < parseInt(this.playType)) {
            this.check.isChecked = a.balloonProxy.isSelf = this.selfMoving = !1;
            i.alertUtil.alertItemLimit(a.balloonProxy.cfg.need);
        } else a.balloonProxy.sendPlay(parseInt(this.playType));
    },
});
