var scUrlLoad = require("UrlLoad");
var scPanelCirle = require("PanelCircleRound");
var scUtils = require("Utils");
var scUIUtils = require("UIUtils");
var scInitializer = require("Initializer");
import { UNION_BOSS_CD, SERVER_CALLBACK_ERROR_CODE, FIGHTBATTLETYPE } from "GameDefine";

cc.Class({
    extends: cc.Component,

    properties: {
        urlProp: scUrlLoad,
        lbName: cc.Label,
        prgBlood: cc.ProgressBar,
        lbBlood: cc.Label,
        urlBoss: scUrlLoad,
        urlServant: scUrlLoad,
        lbDamage: cc.Label,
        panelCircle: scPanelCirle,
        attackSpineBoss: sp.Skeleton,
    },

    ctor() {
        this.isShow = false;
        this.bFinished = false;
        this.lastHp = 0;
        this.lastBoss = 0;
    },

    // LIFE-CYCLE CALLBACKS:
    onLoad () {
        let bossInfo = scInitializer.unionProxy.bossInfo;
        if(null == bossInfo) {
            return;
        }
        let self = this;
        let bossData = localcache.getItem(localdb.table_unionBoss, bossInfo.currentCbid);
        this.bossData = bossData;
        this.urlProp.url = scUIUtils.uiHelps.getUICardPic("kpsj_icon_" + bossData.ep);
        this.lbName.string = bossData.name;
        let bossHp = bossInfo.bosshp < 0 ? 0 : bossInfo.bosshp;
        this.lastHp = bossHp;
        this.lastBoss = bossInfo.currentCbid;
        this.prgBlood.progress = bossHp / bossData.hp;
        this.lbBlood.string = i18n.t("COMMON_NUM", { f: bossHp, s: bossData.hp });
        this.urlBoss.url = scUIUtils.uiHelps.getServantSpine(bossData.image);
        this.urlServant.url = "";
        this.urlServant.loadHandle = () => {
            self.anchorYPos(self.urlServant); 
        }
        this.showCurHero();

        facade.subscribe("UPDATE_BOSS_INFO", this.onBossAtk, this);
        facade.subscribe("UNION_FT_LIST_UPDATE", this.showCurHero, this);
    },

    onBossAtk(data) {
        this.lbDamage.string = "-" + scUtils.utils.formatMoney(scInitializer.unionProxy.fightBossInfo.hit);
        this.lbDamage.node.active = true;
        scUtils.utils.showEffect(this.lbDamage, 0);
        scUIUtils.uiUtils.showShake(this.urlBoss, -6, 12);
        this.attackSpineBoss.node.active = true;
        this.attackSpineBoss.setAnimation(0, 'animation', false);
        scUtils.audioManager.playEffect("5", true, true);

        let curPg = this.lastHp / this.bossData.hp;
        let bossInfo = scInitializer.unionProxy.bossInfo;
        let bossHp = bossInfo.bosshp <= 0 || this.lastBoss != bossInfo.currentCbid ? 0 : bossInfo.bosshp;
        this.bFinished = bossHp == 0;
        let newPg = bossHp / this.bossData.hp;
        let speed = Math.abs(newPg) / (0.1 / 1);
        let self = this;
        this.lastHp = bossHp;
        scUIUtils.uiUtils.showPrgChange(this.prgBlood, curPg, newPg, 1, speed, () => {
            self.lbBlood.string = i18n.t("COMMON_NUM", { f: self.lastHp, s: self.bossData.hp });
            self.prgBlood.progress = self.lastHp / self.bossData.hp;
        });  
        
        if(bossHp > 0) {
            this.scheduleOnce(this.showBossAtkEnd, 2);
        } else {
            this.scheduleOnce(this.checkEnd, 2);
        }
    },

    showBossAtkEnd: function(bNotShow) {
        this.lbDamage.node.active = false;
        this.attackSpineBoss.node.active = false;

        this.isShow = false;
        if(bNotShow != true) {
            scInitializer.timeProxy.floatReward();
        }
    },

    checkEnd: function() {
        this.showBossAtkEnd(true);
        scUtils.utils.openPrefabView("dalishi/FightWin", null, { type: FIGHTBATTLETYPE.COPY_BOSS });
    },

    showCurHero() {
        let data = scInitializer.unionProxy.getCanFightBoss(this.bossData.ep);
        let bAllDeath = true;
        for(let i = 0, len = data.length; i < len; i++) {
            if(null == data[i].fightInfo || data[i].fightInfo.h != 0) {
                bAllDeath = false;
                break;
            }
        }
        if(bAllDeath && !this.bFinished) {
            scUtils.alertUtil.alert18n("UNION_NO_HERO");
        }
        this.panelCircle.data = data;
    },

    onClickSelect(event, item) {
        let unionProxy = scInitializer.unionProxy;
        let bossInfo = unionProxy.bossInfo;
        if(null == bossInfo) {
            return;
        }
        if(scUtils.timeUtil.second > bossInfo.startBossTime + UNION_BOSS_CD) { //时间到了
            facade.send("SERVER_SPECIAL_CALLBACK_ERROR", { type: SERVER_CALLBACK_ERROR_CODE.NO_CLUB_BOSS });
            return;
        }    

        if(this.isShow) {
            return;
        }
        this.isShow = true;
        if(null != item && null != item.data) {
            let data = item.data;
            let heroId = Number(data.id);
            if(null != data.fightInfo && data.fightInfo.h == 0) { //该伙伴已死亡
                this.isShow = false;
                if(null != data.fightInfo.b) {
                    scUtils.alertUtil.alert18n("UNION_NOT_FUHUO");
                    return;
                }
                let itemId = 124;
                let itemCount = scInitializer.bagProxy.getItemCount(itemId);
                if(itemCount <= 0) {
                    scUtils.utils.showConfirm(i18n.t("UNION_NO_FUHUO2"), () => {
                        scInitializer.shopProxy.sendShopListMsg(1);
                    });
                } else {
                    let heroName = localcache.getItem(localdb.table_hero, item.data.id).name;
                    scUtils.utils.showConfirmItem(i18n.t("UNION_GO_FUHUO", { num: 1, hero: heroName }), itemId, itemCount, () => {
                        unionProxy.sendHeroFuhuo(heroId);
                    });
                } 
                return;     
            } else {
                unionProxy.sendFightBoss(bossInfo.currentCbid, heroId);
            }
            let random = Math.floor(5 * Math.random());
            this.urlServant.url = scUIUtils.uiHelps.getServantSpine(heroId);
            this.urlServant.node.active = true;
            scUtils.utils.showEffect(this.urlServant, random);
        }
    },

    anchorYPos(urlLoadComp) {
        if (urlLoadComp == null || urlLoadComp.content == null) 
            return;
        urlLoadComp.content.position = cc.v2(urlLoadComp.content.x, -urlLoadComp.content.height);  
    },

    onClickBack() {
        scUtils.utils.closeView(this);
    },

    onDestroy: function() {
        this.unscheduleAllCallbacks();
        if(null != scInitializer.playerProxy && null != scInitializer.playerProxy.userData) {
            scInitializer.unionProxy.sendBossList();
        }
    },
});
