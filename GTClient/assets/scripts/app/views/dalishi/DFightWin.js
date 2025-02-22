var i = require("Utils");
var n = require("Initializer");
var List = require("List");
var ItemSlotUI = require("ItemSlotUI");
let scSpineAni = require("CommonSpAnimal");
import { FIGHTBATTLETYPE } from "GameDefine";

cc.Class({
    extends: cc.Component,

    properties: {
        scAni: scSpineAni,
        lblCount: cc.Label,
        lbCount2: cc.Label,
        lblExp: cc.Label,
        lblNum: cc.Label,
        lblWinCount: cc.Label,
        lblRemainCount: cc.Label,
        nodeNormal: cc.Node,
        nodeFuYue: cc.Node,
        listItem: List,
        lblProgress: cc.Label,
        icon: ItemSlotUI,
        nTanhe: cc.Node,
        lbTanhe: cc.Label,
        listTanheItem: List,
        nBtnNext: cc.Node,
    },

    ctor() {
        this.flag = !0;
    },

    onLoad() {
        let self = this;
        //动画监听
        let param = this.node.openParam;
        let _type = 1;
        if (param && param.type != null){
            _type = param.type;
        }
        this.nodeNormal.active = false;
        this.nodeFuYue.active = false;
        this.nTanhe.active = false;

        let aniFunc = (ani1, ani2) => {
            self.scAni.animails.push(ani1);
            self.scAni.animails.push(ani2);
            self.scAni.onLoad();
        };
        switch(_type){
            case FIGHTBATTLETYPE.FUYUE:{ //赴约的战斗
                aniFunc("shengli", "shengli_idle");
                let fightResult = n.fuyueProxy.pFight.fightResult;
                let storyCfg = localcache.getItem(localdb.table_zonggushi, n.fuyueProxy.pFuyueInfo.randSmallStoryId);
                let fuyueCfg = localcache.getItem(localdb.table_fuyue, fightResult.length + 1);
                this.nodeFuYue.active = true;
                this.lblProgress.string = i18n.t("COMMON_NUM",{f:i18n.t("CARD_CUR_PERCENT") + "：" + fightResult.length ,s:storyCfg.boss.length+1})
                this.listItem.data = fuyueCfg.reward;
            }
            break;
            case FIGHTBATTLETYPE.TANHE: {
                aniFunc("shengli", "shengli_idle");
                this.nTanhe.active = true;
                // let tanheRwd = localcache.getItem(localdb.table_tanhe, param.level);
                let tanheRwds = localcache.getList(localdb.table_tanhe);
                tanheRwds.sort((a, b) => {
                    return b.id - a.id;
                });
                let bHightestLevel = param.level == tanheRwds[0].id;
                if(param.isFirstPass) {
                    this.lbTanhe.string = i18n.t("TANHE_TIPS29");
                } else {
                    this.lbTanhe.string = bHightestLevel ? i18n.t("TANHE_TIPS30") : " ";
                }
                
                this.listTanheItem.data = n.timeProxy.itemReward;
                this.nBtnNext.active = !bHightestLevel;
            } break;
            case FIGHTBATTLETYPE.MINIGAME: {
                aniFunc("shengli_gai", "shengli_idle_gai");
            } break;
            case FIGHTBATTLETYPE.JIAOYOU: {
                aniFunc("shengli", "shengli_idle");
                this.nTanhe.active = true;
                var jiaoyouCfg = localcache.getItem(localdb.table_jiaoyou,param.jiaoyouId)
                this.lbTanhe.string = i18n.t("AWARD_STR");
                let listdata = [];
                var firstrwd = jiaoyouCfg.firstrwd
                let tmpDic = {};
                for (var ii = 0; ii < firstrwd.length;ii++){
                    let cg = firstrwd[ii];
                    tmpDic[cg.id] = {id:cg.id,kind:cg.kind,count:cg.count};
                }
                for (let key in tmpDic){
                    listdata.push(tmpDic[key])
                }
                this.listTanheItem.data = listdata;
            } break;
            case FIGHTBATTLETYPE.COPY_BOSS: {
                aniFunc("shengli", "shengli_idle");
                this.nTanhe.active = true;
                this.lbTanhe.string = " ";
                this.listTanheItem.data = n.unionProxy.fightBossInfo.items;
            } break;
            default:{
                aniFunc("shengli", "shengli_idle");
                this.nodeNormal.active = true;
                var t = n.dalishiProxy.win.fight;
                if (t) {
                    this.lblNum.string = i18n.t("DALISI_SCORE_ADD", {
                        d: t.items[0].count
                    });
                    this.lblExp.string = i18n.t("DALISI_SKILL_ADD_RWD", {
                        d: t.items[1].count
                    });
                    this.lblCount.string = t.items[2].count + "";
                    this.lbCount2.string = t.items[3].count + "";
                    this.icon.data = t.items[3];
                    this.lblWinCount.string = i18n.t("DALISI_LIAN_WIN", {
                        d: t.winnum
                    });
                    this.lblRemainCount.string = i18n.t("DALISI_WIN_RWD", {
                        d: t.nrwd
                    });
                }
            }
            break;
        }
        
        this.scheduleOnce(this.onTimer, 0.5);
    },

    onClost() {
        let param = this.node.openParam;
        let _type = 1;
        if (param && param.type != null){
            _type = param.type;
        }
        if (this.flag) return;
        switch(_type){
            case FIGHTBATTLETYPE.FUYUE:{ //赴约的战斗       
                let fightResult = n.fuyueProxy.pFight.fightResult;
                n.fuyueProxy.refreshStoryView(fightResult.length + 1, fightResult[fightResult.length - 1], false);
                i.utils.closeView(this);
                i.utils.closeNameView("dalishi/FightView");
            }
            break;
            case FIGHTBATTLETYPE.TANHE: {
                i.utils.closeNameView("battle/BattleBaseView");
                i.utils.closeView(this);
            } break;
            case FIGHTBATTLETYPE.MINIGAME: {
                facade.send("FIGHT_WIN_CLOSE");
                i.utils.closeView(this);
            } break;
            case FIGHTBATTLETYPE.JIAOYOU: {
                i.utils.closeNameView("battle/BattleBaseView");
                i.utils.closeView(this);
            } break;
            case FIGHTBATTLETYPE.COPY_BOSS: {
                i.utils.closeNameView("union/UnionBossView");
                i.utils.closeView(this);
            } break;
            default:
            {
                if (!this.flag) {
                    2 == n.dalishiProxy.fight.fstate ? i.utils.openPrefabView("dalishi/AwardDView") : n.dalishiProxy.openShop();
                    i.utils.closeView(this);
                }
            }
            break;
        }
    },

    onTimer() {
        this.flag = !1;
    },

    onClickNextLevel: function() {
        if(this.bClicked) {
            return;
        }
        this.bClicked = true;
        i.utils.closeView(this);
        facade.send("FIGHT_GAME_NEXTLEVEL");
    },

    onDestroy(){
        n.timeProxy.itemReward = null;
    },
});
