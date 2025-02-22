var n = require("Utils");
var l = require("UrlLoad");
var r = require("Initializer");
var a = require("UIUtils");
var s = require("ConfirmView");
var c = require("TimeProxy");
var PanelCirle = require("PanelCircleRound");
import { USER_CUT_LEVELUP_TYPE } from "GameDefine";
cc.Class({
    extends: cc.Component,
    properties: {
        boss: l,
        prg: cc.ProgressBar,
        lblPer: cc.Label,
        lblName: cc.Label,
        servantUrl: l,
        lblDamge1: cc.Label,
        lblDamge2: cc.Label,
        lblBoss: cc.Label,
        lblServant: cc.Label,
        nodeBossText: cc.Node,
        nodeServantText: cc.Node,
        nodeFight: cc.Node,
        lblFight: cc.Label,
        lblQishi: cc.Label,
        beginEffect:sp.Skeleton,
        panelCircle:PanelCirle,
        attackSpineBoss:sp.Skeleton,
        attackSpineMine:sp.Skeleton,
    },
    ctor() {
        this.lastMap = -1;
        this.fightType = 0;
        this.isShow = !1;
        this.damage = 0;
        this.lastHp = 0;
        this.heroid = 0;
    },
    onLoad() {
        this.fightType = this.node.openParam ? this.node.openParam.id: 0;
        this.servantUrl.url = "";
        r.taskProxy.setDelayShow(!0);
        this.servantUrl.loadHandle = () => {
            this.anchorYPos(this.servantUrl); 
        }
        this.showCurInfo();
        this.lastHp = this.getRemain();
        this.damage = 0;
        this.lblFight.string = i18n.t("FIGHT_NEED_FIGHT", {
            t: n.utils.formatMoney(r.fightProxy.bossData.maxHp)
        });
        this.lblQishi.string = i18n.t("FIGHT_ALL_QISHI", {
            t: n.utils.formatMoney(r.playerProxy.userEp.e1)
        });
        facade.subscribe("BATTLE_BOSS_OVER", this.onBossAtk, this);
        facade.subscribe("BATTLE_BACK_HID", this.showCurInfo, this);
        facade.subscribe("FIGHT_CLOST_WIN_VIEW", this.onClickBack, this);
        facade.subscribe("FIGHT_CLOST_LOST_VIEW", this.onClickBack, this);
        facade.subscribe("FIGHT_LOST_CLICK", this.onClickLost, this);

        
        this.beginEffect.animation = "animation";
        this.beginEffect.loop = false;
        //动画监听
        this.beginEffect.setCompleteListener((trackEntry) => {
            var animationName = trackEntry.animation ? trackEntry.animation.name : "";
            if (animationName === 'animation') {
                this.doFight();
            }        
        });

    },

    anchorYPos(urlLoadComp) {
        if (urlLoadComp == null || urlLoadComp.content == null) return;
        urlLoadComp.content.position = cc.v2(urlLoadComp.content.x,-urlLoadComp.content.height);  
    },

    onClickLeft(t) {
        t < 340 || this.onClickBack();
    },
    onClickLost(t) {
        if (0 == t) {
            this.nodeFight.active = !1;
            this.checkEnd();
        } else this.onClickBack();
    },

    doFight() {
        this._beginEffectAnimation = null;
        //if (r.playerProxy.userEp.e1 < this.getRemain()) n.utils.openPrefabView("battle/FightLostView");
        if (this.herosDamage() < this.getRemain()) n.utils.openPrefabView("battle/FightLostView");
        else {
            this.nodeFight.active = !1;
            this.checkEnd();
        }
    },

    herosDamage() {
        // var damage = r.servantProxy.servantDamage();
        // damage += r.playerProxy.getUserEpData(3).e1;
        // damage += r.playerProxy.getUserEpData(4).e1;
        // damage = damage / r.servantProxy.getServantList().length;
        // return damage;

        var damage = 0;
        var servantCount = r.servantProxy.getServantList().length;
        for(var i=0; i<servantCount; i++) {
            damage += r.servantProxy.getServantList()[i].aep["e1"] + r.clotheProxy.getServantFightEp1AddValue(USER_CUT_LEVELUP_TYPE.ADD_SERVANT_PROP) +(r.playerProxy.getUserEpData(3).e1+r.playerProxy.getUserEpData(4).e1)/servantCount;
        } 
        return damage;
    },

    onBossAtk(t) {
        var e = this.getRemain();
        this.damage = this.lastHp - e;
        this.lastHp = e;
        t && 0 != t ? this.showDamage(1) : this.showCurInfo();
    },
    getRemain() {
        var t = r.fightProxy.bossData,
        e = r.playerProxy.userData.mkill;
        this.lastMap != r.playerProxy.userData.bmap && 0 == this.fightType && (e = t.maxHp);
        return t.maxHp - e;
    },
    showCurInfo() { - 1 == this.lastMap && (this.lastMap = r.fightProxy.bossData.id);
        var t = r.fightProxy.bossData;
        if (t) {
            this.boss.url = a.uiHelps.getServantSpine(t.photo);
            this.lblName.string = t.bossName ? t.bossName : t.bname;
            var e = this.getRemain(),
            o = e / t.maxHp;
            o = o < 0 ? 0 : o;
            this.prg.progress = o;
            this.lblPer.string = i18n.t("COMMON_NUM", {
                f: e,
                s: t.maxHp
            });
        }
        this.showCurHero();
    },
    showCurHero() {
        var t = r.fightProxy.getCanFight();
        this.panelCircle.data = t;
    },
    
    checkEnd() {
        for (var t, e = r.fightProxy.getCanFight(), o = this.getRemain(), i = this, l = r.fightProxy.getMaxHid(), a = 0, _ = 0; _ < r.fightProxy.pvbList.length; _++) if (r.fightProxy.pvbList[_].id == l) {
            a = null == (a = r.fightProxy.pvbList[_].b) ? 0 : a;
            break;
        }
        t = Math.floor(100 * Math.pow(1.2, a));
        if (o <= 0) {
            r.fightProxy.isBoss = !0;
            n.utils.openPrefabView("battle/FightWinView");
        } else {
            var self = this;
            if(e==null || e.length ==0) {
                n.utils.showConfirmItem(i18n.t("FIGHT_LOST_CONFIRM", {
                    v: t
                }), 1, r.playerProxy.userData.cash,
                function(e) {                    
                    if (e == s.NO) {
                        self.herosAllback();
                        c.funUtils.openView(c.funUtils.servantView.id);
                        i.onClickClost();
                    } else {
                        if (r.playerProxy.userData.cash < t) {
                            self.herosAllback();
                            n.alertUtil.alertItemLimit(1);
                            n.utils.closeView(i);                            
                            return;
                        }
                        r.fightProxy.sendBackHid(r.fightProxy.getMaxHid());
                    }
                },
                null, null, null, i18n.t("FIGHT_LOST_REBIRTH"), i18n.t("FIGHT_LOST_UP"),true);
            }
        }
    },

    herosAllback() {
        var comebackall = new proto_cs.user.comebackall();
        JsonHttp.send(comebackall, ()=> {
            console.log("comebackall receive");
        });
    },

    onClickClost() {
        r.taskProxy.setDelayShow(!1);
        this.checkStory();
        n.utils.closeView(this);
        n.utils.closeNameView("battle/FightView");
    },
    checkStory() {
        var t = r.fightProxy.bossData;
        if (this.getRemain() <= 0 && !n.stringUtil.isBlank(t.storyId) && r.playerProxy.getStoryData(t.storyId)) {
            r.playerProxy.addStoryId(t.storyId);
            n.utils.openPrefabView("StoryView");
        } else facade.send("FIGHT_SHOW_GUIDE");
    },
    onClickBack() {
        this.checkStory();
        n.utils.closeView(this);
    },
    onClickSelect(t,e) {
        if (!this.isShow && null != e && null != e.data && !this.nodeFight.active) {
            this.isShow = !0;
            var o = e,
            i = Math.floor(5 * Math.random()),
            l = o.data;
            this.heroid = l.id;           
            this.servantUrl.url = a.uiHelps.getServantSpine(l.id);
            this.servantUrl.node.active = !0;
            o.node.active = !1;
            n.utils.showEffect(this.servantUrl, i);
            // console.error("sendBossFight:",l.id)
            // console.error("this.fightType:",this.fightType)
            if (0 == this.fightType) r.fightProxy.sendBossFight(l.id);
            else switch (this.fightType) {
            case 1:
                r.playerProxy.userData.mkill = r.fightProxy.bossData.maxHp;
                r.fightProxy.sendSpecBoss(l.id, this.fightType);
            }
        }
    },
    showDamage(t) {
        var e = r.fightProxy.bossData;
        r.fightProxy.playerRandomHit();
        this.nodeBossText.active = !1;
        this.lblDamge1.string = "-" + n.utils.formatMoney(Math.floor(this.damage / 2));
        this.lblDamge1.node.active = !0;
        n.utils.showEffect(this.lblDamge1, 0);
        //a.uiUtils.showShake(this.boss);
        a.uiUtils.showShake(this.boss, -6, 12);
        //this.node.getComponent(cc.Animation).play("Camera_15");
        this.attackSpineBoss.node.active = true;
        this.attackSpineBoss.setAnimation(0, 'animation', false);
        n.audioManager.playEffect("5", true, true);
        var o = this.lastHp + (1 == t ? this.damage: Math.floor(this.damage / 2)),
        i = this.lastHp + (1 == t ? Math.floor(this.damage / 2) : 0);
        i = i < 0 ? 0 : i;
        var l = (o = o > e.maxHp ? e.maxHp: o) / e.maxHp,
        s = i / e.maxHp;
        a.uiUtils.showPrgChange(this.prg, l, s,1,3);
        a.uiUtils.showNumChange(this.lblPer, o, i);
        if (1 == t) this.showServantDialog();
        else if (2 == t) if (this.lastHp > 0) {
            this.nodeServantText.active = !0;
            var c = 0 != this.heroid ? this.getShowString(1, this.heroid) : null;
            c && (this.lblServant.string = c.losdialog);
            this.scheduleOnce(this.showBossAtkEnd, 2);
        } else {
            this.isShow = !1;
            this.scheduleOnce(this.checkEnd, 2);
        }
    },
    showServantDialog() {
        this.nodeServantText.active = !0;
        var t = 0 != this.heroid ? this.getShowString(1, this.heroid) : null;
        t && (this.lblServant.string = t.atkdialog1);
        this.scheduleOnce(this.showBossDialog, 2);
    },
    showDamage2() {
        this.nodeServantText.active = !1;
        this.showDamage(2);
    },
    getShowString(t, e) {
        for (var o = localcache.getGroup(localdb.table_battledialog, "type", t), i = 0; i < o.length; i++) if (o[i].param == e) return o[i];
        return null;
    },
    showBossDialog() {
        this.nodeServantText.active = !1;
        this.nodeBossText.active = !0;
        var t = r.fightProxy.bossData,
        e = this.getShowString(2, t.id);
        e && (this.lblBoss.string = e.atkdialog1);
        this.showServantDamage();
    },
    showServantDamage() {
        var t = r.servantProxy.getHeroData(this.heroid);
        if (t) {
            var e = t.aep.e1 + t.aep.e3 + t.aep.e2 + t.aep.e4;
            e *= 0.5 * Math.random() + 1.5;
            this.lblDamge2.string = "-" + n.utils.formatMoney(Math.floor(e));
            this.lblDamge2.node.active = !0;
            n.utils.showEffect(this.lblDamge2, 0);
            a.uiUtils.showShake(this.servantUrl,-6, 12);
            this.attackSpineMine.node.active = true;
            this.attackSpineMine.setAnimation(0, 'animation', false);
            n.audioManager.playEffect("5", true, true);
        }
        r.fightProxy.playerRandomHit();
        this.scheduleOnce(this.showDamage2, 2);
    },
    showBossAtkEnd() {
        n.utils.showEffect(this.servantUrl, 5);
        this.nodeServantText.active = !1;
        this.isShow = !1;
        this.showCurInfo();
        this.checkEnd();
    },
});
