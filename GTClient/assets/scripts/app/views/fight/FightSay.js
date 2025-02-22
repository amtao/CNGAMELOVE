var i = require("Utils");
var n = require("UrlLoad");
var l = require("Initializer");
var r = require("UIUtils");
cc.Class({
    extends: cc.Component,
    properties: {
        boss:n,
        lblPer:cc.Label,
        prg:cc.ProgressBar,
        lblName:cc.Label,
        nodeEffect:cc.Node,
        lblDamge2:cc.Label,
        nodeBossText:cc.Node,
        lblBoss:cc.Label,
        leftlblPer:cc.Label,
        leftprg:cc.ProgressBar,
        nodeServantText:cc.Node,
        nodeServantEffect:cc.Node,
        lblDamge1:cc.Label,
        lblServant:cc.Label,
        btnClost:cc.Button,
        btnBack:cc.Button,
        btnStart:cc.Button,
        roleSpine:n,
        beginEffect:sp.Skeleton,
    },

    ctor(){
        this.fightType = 0;
        this.id = 1;
        this.isShow = false;
        this.curList = null;
        this.curIndex = 0;
        this.leftKill = 0;
        this.rightKill = 0;
    },
    onLoad() {
        this.fightType = this.node.openParam ? this.node.openParam.type: 0;
        this.id = this.node.openParam ? this.node.openParam.id: 1;
        this.showBatInfo();
        facade.subscribe("BATTLE_ENEMY_OVER", this.onBattleEnd, this);
        facade.subscribe("FIGHT_CLOST_WIN_VIEW", this.clostWin, this);
        facade.subscribe("FIGHT_CLOST_LOST_VIEW", this.onClickBack, this);
        facade.subscribe("UI_TOUCH_MOVE_LEFT", this.onClickBack, this);
        this.beginEffect.animation = "animation";
        l.playerProxy.loadPlayerSpinePrefab(this.roleSpine);
    },
    showBatInfo() {
        var t = l.fightProxy.battleData;
        this.curList = localcache.getGroup(localdb.table_lunZhanSingle, "groupid", this.id);
        this.curList.sort(function(t, e) {
            return t.order - e.order;
        });
        r.uiUtils.showPrgChange(this.leftprg);
        r.uiUtils.showNumChange(this.leftlblPer, 0, t.leftArmy);
        for (var e = 0; e < this.curList.length; e++) if (0 != this.curList[e].opponent) {
            t.rightSex = this.curList[e].opponent;
            t.bname = this.curList[e].op_name;
            break;
        }
        this.boss.url = r.uiHelps.getServantSpine(t.rightSex);
        this.lblName.string = t.bname;
        r.uiUtils.showPrgChange(this.prg);
        r.uiUtils.showNumChange(this.lblPer, 0, t.rightArmy);
    },
    onClickStart() {
        this.curIndex = 0;
        this.btnStart.node.active = !1;
        this.btnBack.interactable = this.btnClost.interactable = !1;
        if (0 == this.fightType) l.fightProxy.sendEnemyFight();
        else switch (this.fightType) {
        case 1:
            l.fightProxy.battleData.leftKill = 0;
            l.fightProxy.battleData.rightKill = l.fightProxy.battleData.rightArmy;
            l.fightProxy.sendSpecBoss(1, this.fightType);
        }
    },
    onBattleEnd() {
        this.curIndex = 0;
        this.playCurIndex();
    },
    playCurIndex() {
        var t = this.curList[this.curIndex];
        if (t) {
            var e = l.fightProxy.battleData;
            this.nodeBossText.active = 2 == t.speaker;
            this.nodeServantText.active = 1 == t.speaker;
            switch (t.speaker) {
            case 1:
                this.lblServant.string = t.content;
                break;
            case 2:
                this.lblBoss.string = t.content;
            }
            this.curIndex++;
            switch (t.handle) {
            case 1:
            case 2:
                var o = (a = 1 - ((this.leftKill / 1e4) * e.leftKill) / e.leftArmy) * e.leftArmy;
                this.leftKill += 2 == t.handle ? -parseInt(t.value) : parseInt(t.value);
                var i = (n = 1 - ((this.leftKill / 1e4) * e.leftKill) / e.leftArmy) * e.leftArmy;
                r.uiUtils.showPrgChange(this.leftprg, a, n);
                r.uiUtils.showNumChange(this.leftlblPer, o, i);
                this.scheduleOnce(this.playCurIndex, 2);
                l.fightProxy.playerRandomHit();
                break;
            case 3:
            case 4:
                o = (a = 1 - ((this.rightKill / 1e4) * e.rightKill) / e.rightArmy) * e.rightArmy;
                this.rightKill += 4 == t.handle ? -parseInt(t.value) : parseInt(t.value);
                var n;
                i = (n = 1 - ((this.rightKill / 1e4) * e.rightKill) / e.rightArmy) * e.rightArmy;
                r.uiUtils.showPrgChange(this.prg, a, n);
                r.uiUtils.showNumChange(this.lblPer, o, i);
                this.scheduleOnce(this.playCurIndex, 2);
                l.fightProxy.playerRandomHit();
                break;
            case 5:
                o = (a = 1 - ((this.leftKill / 1e4) * e.leftKill) / e.leftArmy) * e.leftArmy;
                r.uiUtils.showPrgChange(this.leftprg, a, 0);
                r.uiUtils.showNumChange(this.leftlblPer, o, 0);
                this.scheduleOnce(this.fightOver, 2);
                l.fightProxy.playerRandomHit();
                break;
            case 6:
                var a;
                o = (a = 1 - ((this.rightKill / 1e4) * e.rightKill) / e.rightArmy) * e.rightArmy;
                r.uiUtils.showPrgChange(this.prg, a, n);
                r.uiUtils.showNumChange(this.lblPer, o, i);
                this.scheduleOnce(this.fightOver, 2);
                l.fightProxy.playerRandomHit();
                break;
            case 7:
                //this.roleSpine && this.roleSpine.actionString(t.value);
                this.scheduleOnce(this.playCurIndex, 2);
            }
        }
    },
    fightOver() {
        var t = l.fightProxy.battleData,
        e = t.leftArmy - t.leftKill,
        o = t.rightArmy - t.rightKill;
        this.leftlblPer.string = i.utils.formatMoney(e);
        this.lblPer.string = i.utils.formatMoney(o);
        this.leftprg.progress = e / t.leftArmy;
        this.prg.progress = o / t.rightArmy;
        t.leftKill == t.leftArmy ? i.utils.openPrefabView("battle/FightLostView") : t.rightArmy == t.rightKill && i.utils.openPrefabView("battle/FightWinView");
        1 == this.fightType && this.clostWin();
    },
    clostWin() {
        var t = l.fightProxy.battleData;
        if (!i.stringUtil.isBlank(t.storyId) && l.playerProxy.getStoryData(t.storyId)) {
            l.playerProxy.addStoryId(t.storyId);
            i.utils.openPrefabView("StoryView");
        } else {
            i.utils.closeView(this);
            facade.send("FIGHT_SHOW_GUIDE");
        }
    },
    clostLost() {
        if (this.btnClost.interactable) {
            i.utils.closeView(this);
            i.utils.closeNameView("battle/FightView");
        }
    },
    onClickBack() {
        this.btnBack.interactable && i.utils.closeView(this);
    },
});
