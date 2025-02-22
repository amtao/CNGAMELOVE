var i = require("Utils");
var n = require("UrlLoad");
var l = require("UIUtils");
var r = require("Initializer");
var a = require("List");
cc.Class({
    extends: cc.Component,
    properties: {
        lblHp:cc.Label,
        lblName:cc.Label,
        lblPower:cc.Label,
        hpBar:cc.ProgressBar,
        bossImg:n,
        servantImg:n,
        lblPro:cc.Label,
        heroList:a,
        flowerEff:sp.Skeleton,
        nodeFight:cc.Node,
        nodeRecord:cc.Node,
        recordList:a,
        nodeUp:cc.Node,
        nodeDown:cc.Node,
        scorll:cc.ScrollView,
        beginEffect:sp.Skeleton,
    },

    ctor(){

        this.flag = false;
    },
    onLoad() {
        facade.subscribe("UNION_COPY_HERO_CHANGE", this.onHeroChange, this);
        facade.subscribe("UPDATE_BOSS_INFO", this.onUpdateBoss, this);
        facade.subscribe("UNION_CLOSE_WINDOW", this.onCloseWindow, this);
        facade.subscribe("UNION_FT_LIST_UPDATE", this.onUpdateList, this);
        facade.subscribe("UNION_RECORD_UPDATE", this.onRecordUpdate, this);
        this.bossImg.url = l.uiHelps.getServantSpine(r.unionProxy.openCopyParam.image);
        this.onShowHero();
        this.onHeroChange();
        this.onUpdateBoss();
        this.onUpdateList();
        r.unionProxy.sendGetBossRecord(r.unionProxy.openCopyParam.id);
        this.beginEffect.animation = "animation";
    },
    onClickLeft(t) {
        t < 340 || this.eventClose();
    },
    onUpdateBoss() {
        for (var t = null,
        e = 0; r.unionProxy.bossInfo && e < r.unionProxy.bossInfo.length; e++) if (r.unionProxy.bossInfo[e].id == r.unionProxy.openCopyParam.id) {
            t = r.unionProxy.bossInfo[e];
            break;
        }
        this.lblName.string = r.unionProxy.openCopyParam.name;
        var o = t.hp < 0 ? 0 : t.hp,
        i = ((o / r.unionProxy.openCopyParam.hp) * 100).toFixed(2);
        this.hpBar.progress = o / r.unionProxy.openCopyParam.hp;
        this.lblHp.string = i + "%";
        3 == t.type ? i18n.t("union_scaped") : i18n.t("union_killed");
    },
    eventClose() {
        r.unionProxy.fighting = !1;
        i.utils.closeView(this);
    },
    onClickFight() {
        if (0 != r.unionProxy.curSelectId) {
            var t = r.unionProxy.getHeroFightData(r.unionProxy.curSelectId);
            if (null == t || 1 == t.h) {
                this.nodeFight.active = !1;
                this.flowerEff.node.active = !0;
                this.flowerEff.animation = "animation";
                this.scheduleOnce(this.onTimer, 1.5);
                r.fightProxy.playerRandomHit();
                r.unionProxy.fighting = !0;
            } else i.alertUtil.alert18n("HERO_RESTING");
        } else i.alertUtil.alert18n("UNION_NO_CHOSE");
    },
    onTimer() {
        r.unionProxy.sendFightBoss(r.unionProxy.openCopyParam.id, r.unionProxy.curSelectId);
        r.unionProxy.fighting = !1;
    },
    onClickServant() {
        i.utils.openPrefabView("union/UnionHeroSelect");
    },
    onHeroChange() {
        if (0 != r.unionProxy.curSelectId) {
            this.servantImg.url = l.uiHelps.getServantSpine(r.unionProxy.curSelectId);
            var t = r.servantProxy.getHeroData(r.unionProxy.curSelectId);
            this.lblPro.string = i18n.t("COMMON_PROP1") + t.aep.e1;
        } else {
            i.alertUtil.alert18n("UNION_NO_HERO_FIGHT");
            this.lblPro.string = i18n.t("UNION_NO_CHOSE");
        }
    },
    onShowHero() {
        var t = r.servantProxy.getServantList();
        t.sort(r.servantProxy.sortServantEp);
        for (var e = 0; e < t.length; e++) {
            var o = r.unionProxy.getHeroFightData(t[e].id);
            if (null == o || 1 == o.h) {
                r.unionProxy.curSelectId = t[e].id;
                break;
            }
        }
    },
    onUpdateList() {
        var t = r.servantProxy.getServantList();
        t.sort(r.servantProxy.sortServantEp);
        this.heroList.data = t;
    },
    onCloseWindow() {
        this.onShowHero();
        this.onHeroChange();
        this.scheduleOnce(this.onTimer2, 0.2);
    },
    onTimer2() {
        for (var t = null,
        e = 0; r.unionProxy.bossInfo && e < r.unionProxy.bossInfo.length; e++) if (r.unionProxy.bossInfo[e].id == r.unionProxy.openCopyParam.id) {
            t = r.unionProxy.bossInfo[e];
            break;
        }
        this.nodeFight.active = t && t.hp > 0;
    },
    onClickRecord() {
        if (1 == this.flag) {
            this.flag = !1;
            this.nodeRecord.y = -(this.node.height / 2 - 40);
        } else {
            this.flag = !0;
            this.scorll.scrollToTop();
            this.nodeRecord.y = -(this.node.height / 2 - 390);
        }
        this.nodeUp.active = !this.flag;
        this.nodeDown.active = this.flag;
    },
    onRecordUpdate() {
        this.recordList.data = r.unionProxy.heroLog;
    },
});
