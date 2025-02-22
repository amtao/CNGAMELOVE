var i = require("Utils");
var n = require("UrlLoad");
var l = require("UIUtils");
var r = require("DalishiServantItem");
var a = require("Initializer");
import { FIGHTBATTLETYPE } from "GameDefine";

cc.Class({
    extends: cc.Component,

    properties: {
        lblEnemy:cc.Label,
        lblEnemyZZ:cc.Label,
        lblEnemyPer:cc.Label,
        prgEnemy:cc.ProgressBar,
        enemyUrl:n,
        enemy:r,
        lblEnemyTalk:cc.Label,
        nodeEnemyTalk:cc.Node,
        rightSp:sp.Skeleton,
        lblDamge2:cc.Label,
        servant:r,
        lblName:cc.Label,
        lblZZ:cc.Label,
        lblPer:cc.Label,
        prg:cc.ProgressBar,
        url:n,
        lblTalk:cc.Label,
        nodeTalk:cc.Node,
        leftSp:sp.Skeleton,
        lblDamge1:cc.Label,
        nodeSkip:cc.Node,
        nodeFightButton:cc.Node,
        beginEffect:sp.Skeleton,
        //bgUrl:n,
    },

    ctor(){
        this._curIndex = 0;
        this._enemyHp = 0;
        this._meHp = 0;
        this._enemyMax = 0;
        this._meMax = 0;
        this._type = 1;
        this._maxIndex = 3;
        this._bossId = 0;
        this._fightFlag = false;
    },

    onLoad() {
        let param = this.node.openParam;
        let _type = 1;
        if (param && param.type != null){
            _type = param.type;
        }
        this._type = _type;
        this.timer = null;
        var t;
        var e;
        this.nodeFightButton.active = false;
        switch(_type){
            case FIGHTBATTLETYPE.FUYUE:{ //赴约的战斗
                t = a.fuyueProxy.pFight;
                e = t.base[0];
                let cg = localcache.getItem(localdb.table_zonggushi,a.fuyueProxy.pFuyueInfo.randSmallStoryId);
                if (cg == null){
                    console.error("无效的故事id:",a.fuyueProxy.pFuyueInfo.randSmallStoryId)
                    return;
                }
                e.hid = cg.hero_id;
                if (t.isWin == 1){
                    this._maxIndex = 3;
                }
                else{
                    this._maxIndex = 2;
                }
                this._bossId = cg.boss[t.fightResult.length-1];
                if (this.nodeSkip)
                    this.nodeSkip.active = false; 
                let self = this;
                this.beginEffect.setCompleteListener((trackEntry) => {
                    var animationName = trackEntry.animation ? trackEntry.animation.name : "";
                    if (animationName === 'animation') {
                        self._fightFlag = true;
                        self.showFuYueCurIndex();
                        self.nodeFightButton.active = false;
                    }
                });
                this.nodeFightButton.active = true;    
                this.nodeEnemyTalk.active = false;
                this.nodeTalk.active = false;
                //this.bgUrl.url = l.uiHelps.getStory(a.fuyueProxy.lastStoryBg);
            }
            break;
            default:
                t = a.dalishiProxy.win.fight;
                e = t.base[0];
            break;
        }
        
        var o = localcache.getItem(localdb.table_hero, e.hid);
        
        this.url.url = l.uiHelps.getServantHead(e.hid);
        this.servant.data = {
            id: e.hid,
            myHero:true
        };
        this.lblName.string = i18n.t("DALISI_NAME_SERVANT", {
            n: o ? o.name: "",
            d: e.level
        });
        this.lblZZ.string = i18n.t("SERVANT_ZHZZ", {
            zz: e.azz
        });
        this.lblPer.string = i18n.t("COMMON_NUM", {
            f: e.hp,
            s: e.hpmax
        });
        this.prg.progress = e.hp / e.hpmax;
        this._meHp = e.hp;
        this._meMax = e.hpmax;
        var i = t.base[1];
        switch(_type){
            case FIGHTBATTLETYPE.FUYUE:{ //赴约的战斗
                i.hid = this._bossId + 0;
            }
            break;
        }
        var n = localcache.getItem(localdb.table_hero, i.hid);
        switch(this._type){
            case FIGHTBATTLETYPE.FUYUE:{ //赴约的战斗
                n = localcache.getItem(localdb.table_zuipao, i.hid);
            }
            break;
            default:      
            break;
        };
        this.enemyUrl.url = l.uiHelps.getServantHead(i.hid);
        this.enemy.data = {
            id: i.hid,
            isFuYue:this._type == FIGHTBATTLETYPE.FUYUE
        };
        this.lblEnemy.string = i18n.t("DALISI_NAME_SERVANT", {
            n: n ? n.name: "",
            d: i.level
        });
        this.lblEnemyZZ.string = i18n.t("SERVANT_ZHZZ", {
            zz: i.azz
        });
        this.lblEnemyPer.string = i18n.t("COMMON_NUM", {
            f: i.hpmax,
            s: i.hpmax
        });
        this.prgEnemy.progress = i.hp / i.hpmax;
        this._enemyHp = this._enemyMax = i.hpmax;
        this._curIndex = 0;
        switch(this._type){
            case FIGHTBATTLETYPE.FUYUE:{ //赴约的战斗
                //this.showFuYueCurIndex();
            }
            break;
            default:{
                this.showCurIndex();
            }
            break;
        };
        //动画监听
        // this.beginEffect.setCompleteListener((trackEntry) => {
        //     var animationName = trackEntry.animation ? trackEntry.animation.name : "";
        //     if (animationName === 'on') {
        //         // this.beginEffect.setAnimation(0, 'idle', true);
        //         this.beginEffect.setAnimation(0, 'off', false);
        //     } else if (animationName === 'off') {
        //         this.showFuYueCurIndex();
        //     }             
        // });           
    },

    showFuYueCurIndex() {
        if(this.node == null || !this.node.isValid) {
            return;
        }
        var t = a.fuyueProxy.pFight;
        let idx = this._curIndex % 2;
        switch(idx) {
            case 0:{//我攻击boss
                let damage = t.prop[0].attack;
                var o = this._enemyHp / this._enemyMax;
                if (this._type == FIGHTBATTLETYPE.FUYUE && this._curIndex + 1 >= this._maxIndex) {
                    damage = this._enemyHp;
                    this._enemyHp = 0;
                } else {
                    if(t.prop[0].attack >= this._enemyHp) {
                        this._enemyHp = Math.floor(this._enemyHp / 2); 
                    } else {
                        this._enemyHp -= t.prop[0].attack;
                    }                   
                    this._enemyHp = this._enemyHp < 0 ? 0 : this._enemyHp;
                }
                this.lblEnemyPer.string = i18n.t("COMMON_NUM", {
                    f: this._enemyHp,
                    s: this._enemyMax
                });
                l.uiUtils.showPrgChange(this.prgEnemy, o, this._enemyHp / this._enemyMax);
                this.nodeTalk.active = !0;
                this.nodeEnemyTalk.active = !1;
                i.utils.showNodeEffect(this.nodeTalk, 0);
                this.lblTalk.string = a.fuyueProxy.getServantOrEnemyTalk(this._bossId,true);
                l.uiUtils.showShake(this.enemy);
                i.audioManager.playEffect("5", true, true);
                this.rightSp.node.active = !0;
                this.rightSp.animation = "animation";
                this.lblDamge2.string = "-" + i.utils.formatMoney(damage);
                this.lblDamge2.node.active = !0;
                i.utils.showEffect(this.lblDamge2, 0);
            }
            break;
            case 1:{//boss攻击我
                let damage2 = t.prop[1].attack;
                if (this._type == FIGHTBATTLETYPE.FUYUE && this._curIndex + 1 >= this._maxIndex) {
                    damage2 = this._meHp;
                    this._meHp = 0;
                }
                var o = this._meHp / this._meMax;
                this._meHp -= t.prop[1].attack;
                this._meHp = this._meHp < 0 ? 0 : this._meHp;
                this.lblPer.string = i18n.t("COMMON_NUM", {
                    f: this._meHp,
                    s: this._meMax
                });
                l.uiUtils.showPrgChange(this.prg, o, this._meHp / this._meMax);
                this.nodeTalk.active = !1;
                this.nodeEnemyTalk.active = !0;
                i.utils.showNodeEffect(this.nodeEnemyTalk, 0);
                this.lblEnemyTalk.string = a.fuyueProxy.getServantOrEnemyTalk(this._bossId,false);
                l.uiUtils.showShake(this.servant);
                i.audioManager.playEffect("5", true, true);
                this.leftSp.node.active = !0;
                this.leftSp.animation = "animation";
                this.lblDamge1.string = "-" + i.utils.formatMoney(damage2);
                this.lblDamge1.node.active = !0;
                i.utils.showEffect(this.lblDamge1, 0);
            }
            break;
        }
        this._curIndex += 1;
        if (this._curIndex >= this._maxIndex){
            this.onClickSkip();
            return;
        }
        if (this.timer !== null) {
            clearTimeout(this.timer);
        }
        this.timer = setTimeout(this.showFuYueCurIndex.bind(this), 2000);
    },

    showCurIndex() {
        var t = a.dalishiProxy.win.fight,
        e = t ? t.log[this._curIndex] : null;
        if (null != e) {
            if (1 == e.aid) {
                var o = this._meHp / this._meMax;
                this._meHp -= e.damge;
                this._meHp = this._meHp < 0 ? 0 : this._meHp;
                this.lblPer.string = i18n.t("COMMON_NUM", {
                    f: this._meHp,
                    s: this._meMax
                });
                l.uiUtils.showPrgChange(this.prg, o, this._meHp / this._meMax);
                this.nodeTalk.active = !1;
                this.nodeEnemyTalk.active = !0;
                i.utils.showNodeEffect(this.nodeEnemyTalk, 0);
                this.lblEnemyTalk.string = a.dalishiProxy.getTalkType(4);
                l.uiUtils.showShake(this.servant,-6,12);
                i.audioManager.playEffect("5", true, true);
                this.leftSp.node.active = !0;
                this.leftSp.animation = "animation";
                this.lblDamge1.string = "-" + i.utils.formatMoney(e.damge);
                this.lblDamge1.node.active = !0;
                i.utils.showEffect(this.lblDamge1, 0);
            } else if (0 == e.aid) {
                o = this._enemyHp / this._enemyMax;
                this._enemyHp -= e.damge;
                this._enemyHp = this._enemyHp < 0 ? 0 : this._enemyHp;
                this.lblEnemyPer.string = i18n.t("COMMON_NUM", {
                    f: this._enemyHp,
                    s: this._enemyMax
                });
                l.uiUtils.showPrgChange(this.prgEnemy, o, this._enemyHp / this._enemyMax);
                this.nodeTalk.active = !0;
                this.nodeEnemyTalk.active = !1;
                i.utils.showNodeEffect(this.nodeTalk, 0);
                this.lblTalk.string = a.dalishiProxy.getTalkType(3);
                l.uiUtils.showShake(this.enemy,-6,12);
                i.audioManager.playEffect("5", true, true);
                this.rightSp.node.active = !0;
                this.rightSp.animation = "animation";
                this.lblDamge2.string = "-" + i.utils.formatMoney(e.damge);
                this.lblDamge2.node.active = !0;
                i.utils.showEffect(this.lblDamge2, 0);
            }
            this._curIndex += 1;
            // this.scheduleOnce(this.showCurIndex, 2);
            if (this.timer !== null) {
                clearTimeout(this.timer);
            }
            this.timer = setTimeout(this.showCurIndex.bind(this), 2000);
        } else this.onClickSkip();
    },
    onClickSkip() {
        switch(this._type){
            case FIGHTBATTLETYPE.FUYUE:{ //赴约的战斗               
                this.scheduleOnce(()=>{
                    var t = a.fuyueProxy.pFight;
                    if (t){
                        if (t.isWin == 1){
                            i.utils.openPrefabView("dalishi/FightWin",null,{type:this._type});
                        }
                        else if(t.isWin == 0){
                            let cg = localcache.getItem(localdb.table_fuyue,1);
                            if (cg == null){
                                console.error("赴约失败奖励未配置")
                                return;
                            }
                            i.utils.openPrefabView("dalishi/FightLost",null,{type:this._type,item:cg.reward[0]});
                        }
                    }
                    if (this.timer !== null) {
                        clearTimeout(this.timer);
                    }
                    //this.onClickClost();
                },2)
            }
            break;
            default:{
                var t = a.dalishiProxy.win.fight;
                t && 1 == t.win ? i.utils.openPrefabView("dalishi/FightWin") : t && 0 == t.win && i.utils.openPrefabView("dalishi/FightLost");
                this.onClickClost();
            }
            break;
        }
    },
    onClickClost() {
        if (this.timer !== null) {
            clearTimeout(this.timer);
        }
        i.utils.closeView(this);
    },

    // onClickFight() {
    //     if (this._fightFlag) return;
    //     this._fightFlag = true;
    //     // if (this.beginEffect.animation != "on"){
    //     //     this.beginEffect.animation = "on";
    //     // }
    // },
});
