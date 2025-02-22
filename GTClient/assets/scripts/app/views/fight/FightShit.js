var i = require("List");
var n = require("Utils");
var l = require("UrlLoad");
var r = require("Initializer");
var a = require("UIUtils");
var s = require("FightEnemyItem");
var c = require("FightProxy");
cc.Class({
    extends: cc.Component,
    properties: {
        rightHead: l,
        lblName: cc.Label,
        prgLeft: cc.ProgressBar,
        lblLeftPrg: cc.Label,
        lblLeftWuli: cc.Label,
        lblLost: cc.Label,
        prgRight: cc.ProgressBar,
        lblRightPrg: cc.Label,
        lblRightWuli: cc.Label,
        rightSp: sp.Skeleton,
        leftSp: sp.Skeleton,
        btnStart: cc.Button,
        list: i,
        lblDamage: cc.Label,
        nodeServantTalk: cc.Node,
        lblServantTalk: cc.Label,
        nodeEnemyTalk: cc.Node,
        lblEnemyTalk: cc.Label,
        roleSpine: l,
        enemyItems: [s],
        beginEffect:sp.Skeleton,
        nvzhuspine: l,
    },
    ctor() {
        this.isOver = !1;
        this.isStart = !1;
        this.fightType = 0;
        this.context = null;
    },
    onLoad() {
        this.fightType = this.node.openParam ? this.node.openParam.id: 0;
        r.taskProxy.setDelayShow(!0);
        this.showBattleData();
        facade.subscribe("FIGHT_CLOST_WIN_VIEW", this.clostWin, this);
        facade.subscribe("FIGHT_CLOST_LOST_VIEW", this.onClickBack, this);
        facade.subscribe("BATTLE_ENEMY_OVER", this.onBattleSendend, this);
        facade.subscribe(r.playerProxy.PLAYER_USER_UPDATE, this.onUpdateArmy, this);
        r.playerProxy.loadPlayerSpinePrefab(this.roleSpine);
        r.playerProxy.loadPlayerSpinePrefab(this.nvzhuspine);
    },
    onUpdateArmy() {
        if (!this.isStart) {
            var t = r.fightProxy.battleData;
            t.leftArmy = r.playerProxy.userData.army;
            a.uiUtils.showNumChange(this.lblLeftPrg, 0, t.leftArmy);
        }
    },
    showBattleData() {
        if (null != r.fightProxy.battleData) {
            var t = r.fightProxy.battleData,
            e = t.rightArmy - r.playerProxy.userData.mkill;
            a.uiUtils.showPrgChange(this.prgLeft);
            a.uiUtils.showPrgChange(this.prgRight, 0, e / t.rightArmy);
            a.uiUtils.showNumChange(this.lblLeftPrg, 0, t.leftArmy);
            a.uiUtils.showNumChange(this.lblRightPrg, 0, e);
            this.isStart = this.isOver = !1;
            this.btnStart.node.active = !0;
            this.nodeEnemyTalk.active = !1;
            this.nodeServantTalk.active = !1;
            this.lblDamage.node.active = !1;
            this.rightHead.url = 0 != t.rightSex ? a.uiHelps.getServantHead(t.rightSex) : "";
            this.lblLeftWuli.string = i18n.t("COMMON_PROP1") + n.utils.formatMoney(t.leftEp);
            this.lblRightWuli.string = i18n.t("COMMON_PROP1") + n.utils.formatMoney(t.rightEp);
            this.lblLost.string = i18n.t("FIGHT_LOST") + this.getLostStr();
            this.lblName.string = t.bname;
            this.beginEffect.animation = "on";

            //动画监听
            this.beginEffect.setCompleteListener((trackEntry) => {
                var animationName = trackEntry.animation ? trackEntry.animation.name : "";
                if (animationName === 'on') {
                    // this.beginEffect.setAnimation(0, 'idle', true);
                    this.beginEffect.setAnimation(0, 'off', false);
                } else if (animationName === 'off') {
                    this.doBattle();
                }             
            });           

            this.createEnemy1();
            if (0 == this.fightType) {
                this.list.node.active = !0;
                var o = localcache.getItem(localdb.table_smallPve, parseInt(r.playerProxy.userData.smap + "") + 1),
                i = localcache.getGroup(localdb.table_smallPve, "mmap", o.mmap);
                i.sort(function(t, e) {
                    return t.id - e.id;
                });
                this.list.data = i;
            } else this.list.node.active = !1;
        }
    },
    getLostStr() {
        var t = r.fightProxy.battleData,
        e = t.leftEp / t.rightEp;
        return e > 2 ? i18n.t("FIGHT_LOST_1") : e > 1 ? i18n.t("FIGHT_LOST_2") : i18n.t("FIGHT_LOST_3");
    },
    createEnemy1() {
        for (var t = r.fightProxy.battleData.rightJob.split("|"), e = 0; e < this.enemyItems.length; e++) if (t.length > e) {
            var o = new c.EnemyDataItem();
            o.hp = 3 * Math.random() + 2;
            o.job = parseInt(t[e]);
            o.index = e + 1e3;
            o.atkRank = 60 + 100 * Math.random();
            o.isGray = 1 == e ? 1 : 3 == e || 4 == e ? 2 : 0;
            this.enemyItems[e].data = o;
            this.enemyItems[e].node.active = !0;
        } else this.enemyItems[e].node.active = !1;
    },

    onClickBattle() {
        if(this._beginEffectAnimation == null) {
            this.beginEffect.setAnimation(0, 'off', false);
            this._beginEffectAnimation = true;
        }        
    },

    doBattle() {
        this._beginEffectAnimation = null;
        if (r.fightProxy.isEnoughArmy()) {
            this.btnStart.node.active = !1;
            if (0 == this.fightType) r.fightProxy.sendEnemyFight();
            else switch (this.fightType) {
            case 1:
                r.fightProxy.battleData.leftKill = 0;
                r.fightProxy.battleData.rightKill = r.fightProxy.battleData.rightArmy;
                r.fightProxy.sendSpecBoss(1, this.fightType);
            }
        } else {
            // this.beginEffect.setAnimation(0, 'idle', true);
            n.alertUtil.alert18n("GAME_LEVER_NO_SOLDIER");
            n.alertUtil.alertItemLimit(4, r.fightProxy.needArmy());
        }
    },

    onBattleSendend() {
        this.isStart = !0;
        this.scheduleOnce(this.showDamage, 0.5);
    },
    onClickClost() {
        if (this.isStart) n.alertUtil.alert(i18n.t("FIGHT_IS_FIGHTING"));
        else {
            n.utils.closeView(this);
            facade.send("FIGHT_CLOES_VIEW");
        }
    },
    showDamage() {
        var t = r.fightProxy.battleData;
        this.context = localcache.getItem(localdb.table_wordsPve, t.context);
        if (0 == t.context) {
            var e = localcache.getList(localdb.table_wordsPve);
            this.context = e[Math.floor(Math.random() * e.length)];
        }
        this.nodeEnemyTalk.active = !0;
        n.utils.showNodeEffect(this.nodeEnemyTalk);
        this.lblEnemyTalk.string = this.context ? this.context.content: "";
        this.scheduleOnce(this.showRoleDamage, 1);
        r.fightProxy.playerRandomHit();
    },
    showRoleDamage() {
        var t = r.fightProxy.battleData;
        this.rightSp.node.active = !0;
        this.rightSp.animation = "animation";
        a.uiUtils.showShake(this.roleSpine);
        a.uiUtils.showShake(this.nvzhuspine);
        if (this.lblDamage) {
            var e = this;
            this.lblDamage.string = -t.leftKill + "";
            this.lblDamage.node.active = !0;
            n.utils.showEffect(this.lblDamage, 0,
            function() {
                e.lblDamage.node.active = !1;
            });
        }
        a.uiUtils.showNumChange(this.lblLeftPrg, t.leftArmy, r.playerProxy.userData.army);
        a.uiUtils.showPrgChange(this.prgLeft, 1, r.playerProxy.userData.army / t.leftArmy);
        this.scheduleOnce(this.showRightText, 1);
        r.fightProxy.playerRandomHit();
    },
    showRightText() {
        this.nodeEnemyTalk.active = !1;
        this.nodeServantTalk.active = !0;
        this.lblServantTalk.string = this.context ? this.context.player: "";
        n.utils.showNodeEffect(this.nodeServantTalk);
        this.scheduleOnce(this.showRightDamage, 1);
    },
    showRightDamage() {
        var t = r.fightProxy.battleData;
        this.leftSp.node.active = !0;
        this.leftSp.animation = "animation";
        var e = 0 == r.playerProxy.userData.mkill ? t.rightArmy - t.rightKill: t.rightArmy - r.playerProxy.userData.mkill;
        a.uiUtils.showNumChange(this.lblRightPrg, parseInt(this.lblRightPrg.string), e);
        a.uiUtils.showPrgChange(this.prgRight, this.prgRight.progress, e / t.rightArmy);
        for (var o = 0; o < this.enemyItems.length; o++) a.uiUtils.showShake(this.enemyItems[o]);
        this.scheduleOnce(this.fightOver, 1);
    },
    fightOver() {
        if (!this.isOver) {
            this.nodeEnemyTalk.active = !1;
            this.nodeServantTalk.active = !1;
            var t = r.fightProxy.battleData;
            this.isStart = !1;
            this.isOver = !0;
            var e = 0 == r.playerProxy.userData.mkill ? t.rightArmy - t.rightKill: t.rightArmy - r.playerProxy.userData.mkill;
            e = e < 0 ? 0 : e;
            this.lblLeftPrg.string = n.utils.formatMoney(r.playerProxy.userData.army);
            this.lblRightPrg.string = n.utils.formatMoney(e);
            this.prgLeft.progress = r.playerProxy.userData.army / t.leftArmy;
            this.prgRight.progress = e / t.rightArmy;
            this.scheduleOnce(this.clearObj, 1);
        }
    },
    clearObj() {
        for (var t = 0; t < this.enemyItems.length; t++) {
            this.enemyItems[t].data = null;
            this.enemyItems[t].node.active = !1;
        }
        var e = r.fightProxy.battleData;
        e.rightKill >= e.rightArmy ? n.utils.openPrefabView("battle/FightWinView") : e.leftKill >= e.leftArmy && n.utils.openPrefabView("battle/FightLostView");
        1 == this.fightType && this.clostWin();
    },
    clostWin() {
        var t = r.fightProxy.battleData,
        e = !1;
        if (!n.stringUtil.isBlank(t.storyId) && r.playerProxy.getStoryData(t.storyId)) {
            r.playerProxy.addStoryId(t.storyId);
            n.utils.openPrefabView("StoryView");
            e = !0;
        }
        if (r.fightProxy.isFirstmMap() || 0 != this.fightType || r.playerProxy.userData.army <= 0) {
            n.utils.closeView(this);
            e || facade.send("FIGHT_SHOW_GUIDE");
        } else {
            n.utils.showEffect(this, 0);
            this.showBattleData();
        }
    },
    hideEnemyItem() {
        for (var t = 0; t < this.enemyItems.length; t++) {
            this.enemyItems[t].data = null;
            this.enemyItems[t].node.active = !1;
        }
    },
    clostLost() {
        this.hideEnemyItem();
        r.taskProxy.setDelayShow(!1);
        n.utils.closeView(this);
        n.utils.closeNameView("battle/FightView");
    },
    onClickBack() {
        if (this.isStart) n.alertUtil.alert(i18n.t("FIGHT_IS_FIGHTING"));
        else {
            this.hideEnemyItem();
            n.utils.closeView(this);
        }
    },
});
