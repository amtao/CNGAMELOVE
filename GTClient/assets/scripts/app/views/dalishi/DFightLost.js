var i = require("Utils");
var n = require("Initializer");
var ItemSlotUI = require("ItemSlotUI");
let timeProxy = require("TimeProxy");
let scSpineAni = require("CommonSpAnimal");
import { FIGHTBATTLETYPE } from "GameDefine";

cc.Class({
    extends: cc.Component,

    properties: {
        scAni: scSpineAni,
        lblCount: cc.Label,
        nodeNormal: cc.Node,
        nodeFuYue: cc.Node,
        nodeFight: cc.Node,
        nodeTanhe: cc.Node,
        item: ItemSlotUI,
        lblItemNum: cc.Label,
        arrBtn: [cc.Node],
        lbFightLose: cc.Label,
        lbFightLose2: cc.Label,
    },

    ctor() {
        this.flag = !0;
    },

    onLoad() {
        let self = this;

        let param = this.node.openParam;
        let _type = 1;
        if (param && param.type != null) {
            _type = param.type;
        }
        this.nodeNormal.active = false;
        this.nodeFuYue.active = false;
        this.nodeFight.active = false;

        let aniFunc = (ani1, ani2) => {
            self.scAni.animails.push(ani1);
            self.scAni.animails.push(ani2);
            self.scAni.onLoad();
        };
        switch(_type){
            case FIGHTBATTLETYPE.FUYUE: { //赴约的战斗
                aniFunc("shibai", "shibai_idle");
                this.nodeFuYue.active = true;
                if (this.item) {
                    this.item.node.active = true;
                    this.item.data = param.item;
                }
                if (this.lblItemNum) {
                    this.lblItemNum.string = "：+" + param.item.count;
                }
                for(let i = 0, len = this.arrBtn.length; i < len; i++) {
                    this.arrBtn[i].active = i < 5;
                }
            }
            break;
            case FIGHTBATTLETYPE.MINIGAME: {
                aniFunc("shibai_gai", "shibai_idle_gai");
                for(let i = 0, len = this.arrBtn.length; i < len; i++) {
                    this.arrBtn[i].active = false;
                }
            } break;
            case FIGHTBATTLETYPE.TANHE:
            case FIGHTBATTLETYPE.JIAOYOU: {
                aniFunc("shibai", "shibai_idle");
                this.nodeTanhe.active = true;
                this.lbFightLose2.string = i18n.t("FIGHT_LOSE_REASON" + param.loseType);
                for(let i = 0, len = this.arrBtn.length; i < len; i++) {
                    this.arrBtn[i].active = i >= 4;
                }
            } break;
            case FIGHTBATTLETYPE.NONE:
            case FIGHTBATTLETYPE.SPECIAL_BOSS:
            {
                aniFunc("shibai", "shibai_idle");
                this.nodeFight.active = true;
                this.lbFightLose.string = i18n.t("FIGHT_LOSE_REASON" + param.loseType);
                for(let i = 0, len = this.arrBtn.length; i < len; i++) {
                    this.arrBtn[i].active = i >= 4;
                }
            } break;
            case FIGHTBATTLETYPE.FURNITURE:
            {
                aniFunc("shibai", "shibai_idle");
                this.nodeFight.active = true;
                this.lbFightLose.string = i18n.t("FIGHT_LOSE_REASON" + param.loseType);
                for(let i = 0, len = this.arrBtn.length; i < len; i++) {
                    this.arrBtn[i].active = i >= 4;
                }
            } break;
            default: {
                aniFunc("shibai", "shibai_idle");
                this.nodeNormal.active = true;
                var t = n.dalishiProxy.win.fight;
                t && (this.lblCount.string = " ");
                //i18n.t("DALISI_SCORE_MUL", {
                //    d: t.items[0].count
                //})
                for(let i = 0, len = this.arrBtn.length; i < len; i++) {
                    this.arrBtn[i].active = i < 5;
                }
            }
            break;
        }  

        this.scheduleOnce(this.onTimer, 0.5);
    },

    onClickClost() {    
        let param = this.node.openParam;
        let _type = 1;
        if (param && param.type != null){
            _type = param.type;
        }
        if (this.flag) return;
        switch(_type) {
            case FIGHTBATTLETYPE.FUYUE: { //赴约的战斗
                let fightResult = n.fuyueProxy.pFight.fightResult;
                n.fuyueProxy.refreshStoryView(fightResult.length + 1, fightResult[fightResult.length - 1], true);
                i.utils.closeView(this);
                i.utils.closeNameView("dalishi/FightView");
            }
            break;
            case FIGHTBATTLETYPE.JIAOYOU:
            case FIGHTBATTLETYPE.TANHE:
            case FIGHTBATTLETYPE.NONE: 
            case FIGHTBATTLETYPE.SPECIAL_BOSS:{
                i.utils.closeNameView("battle/BattleBaseView");
                i.utils.closeView(this);
            } break;
            case FIGHTBATTLETYPE.FURNITURE:{
                i.utils.closeNameView("battle/BattleBaseView");
                i.utils.closeView(this);
            } break;
            case FIGHTBATTLETYPE.MINIGAME: {
                facade.send("FIGHT_LOSE_CLOSE");
                i.utils.closeView(this);
            } break;
            default: {
                i.utils.closeView(this); (null != n.dalishiProxy.fight && 0 != n.dalishiProxy.fight.hid) || i.utils.closeNameView("dalishi/DalishiServant");
            }
            break;
        }
    },

    onTimer() {
        this.flag = !1;
    },

    onClickUp: function() {
        if (this.flag) return;
        this.closeView();
        i.utils.openPrefabView("servant/ServantLobbyView");
        this.onClickClost();
    },

    onClickGetCard: function() {
        if (this.flag) return;
        this.closeView();
        i.utils.openPrefabView("draw/drawMainView");
        this.onClickClost();
    },

    onClickCardLvUp: function() {
        if (this.flag) return;
        this.closeView();
        i.utils.openPrefabView("card/CardListView");
        this.onClickClost();
    },

    onClickGetClothe: function() {
        if (this.flag) return;
        this.closeView();
        timeProxy.funUtils.isOpenFun(timeProxy.funUtils.userClothe) &&
        i.utils.openPrefabView("user/UserClothe", null, {
            tab: 2
        });
        this.onClickClost();
    },

    /**卡牌编队*/
    onClickTeam(){
        if (this.flag) return;
        this.closeView();
        i.utils.openPrefabView("battle/BattleTeamView", null, { type: this.node.openParam.type,heroid: this.node.openParam.heroId });
        this.onClickClost();
    },

    closeView: function() {
        i.utils.closeNameView("battle/FightNew");
        i.utils.closeNameView("battle/FightView");
        i.utils.closeNameView("dalishi/FightView");
        i.utils.closeNameView("dalishi/DalishiServant");
        i.utils.closeNameView("dalishi/DalishiView");
        i.utils.closeNameView("main/LanTaiMain");
        i.utils.closeNameView("tanhe/MainTanHeView");
    }
});
