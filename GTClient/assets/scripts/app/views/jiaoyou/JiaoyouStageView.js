//进入关卡ui
//9-18 HZW
//this.node.openParam:{fightInfo: 战斗角色数据,stageCfg: 战斗stage配置}
let Utils = require("Utils")
let ItemSlotUI = require("ItemSlotUI")
let Initializer = require("Initializer");
import { FIGHTBATTLETYPE } from "GameDefine";
cc.Class({
    extends: cc.Component,

    properties: {
        heroName:cc.Label,
        stageName:cc.Label,
        attrAry:[cc.Node],
        attrLbl:[cc.Label],
        rwdAry:[ItemSlotUI],
        needCt:cc.Label
    },
    onLoad () {

    },

    start () {
        var openParam = this.node.openParam;
        var heroid = openParam.fightInfo.heroId
        var heroCfg = localcache.getItem(localdb.table_hero, heroid)
        this.heroName.string = i18n.t("SERVANT_ATTR_TITLE",{name:heroCfg.name})
        this.stageName.string = openParam.stageCfg.name
        this.needCt.string = openParam.stageCfg.mingsheng

        var rwdItem = openParam.stageCfg.firstrwd
        for(var i=0;i<this.rwdAry.length;i++){
            if(rwdItem[i]){
                this.rwdAry[i].node.active = true
                this.rwdAry[i].data = rwdItem[i]
            }else{
                this.rwdAry[i].node.active = false
            }
        }

        //需要显示的属性
        var epnum = openParam.stageCfg.epnum
        for(var z=0;z<this.attrAry.length;z++){
            if(epnum.indexOf(z+1) >=0 ){
                this.attrAry[z].active = true
                if(Initializer.playerProxy.allEpData && Initializer.playerProxy.allEpData.cardHeroEp && Initializer.playerProxy.allEpData.cardHeroEp[openParam.fightInfo.heroId]){
                    var attr = Initializer.playerProxy.allEpData.cardHeroEp[openParam.fightInfo.heroId]
                    this.attrLbl[z].string = attr[z+1]?attr[z+1]:"0"
                }else{
                    this.attrLbl[z].string = "0"
                }
            }else{
                this.attrAry[z].active = false
            }
        }
    },
    onClickBack: function() {
        Utils.utils.closeView(this);
    },

    onClickFight(){
        if(this.node.openParam.stageCfg.mingsheng > Initializer.playerProxy.userData.army){
            Utils.alertUtil.alert(i18n.t("GAME_LEVER_NO_SOLDIER"));
            return
        }

        let heroId = this.node.openParam.fightInfo.heroId;
        if(this.node.openParam.stageCfg.openstory != null && this.node.openParam.stageCfg.openstory != "0") {
            Initializer.playerProxy.addStoryId(this.node.openParam.stageCfg.openstory);
            Utils.utils.openPrefabView("StoryView", !1, {
                type: 95,
                extraParam: { type: FIGHTBATTLETYPE.JIAOYOU, jiaoyouId: this.node.openParam.stageCfg.id, heroId: heroId }
            });
            this.onClickBack()
        }else{
            //Utils.utils.openPrefabView("battle/FightGameJiaoyou", null, {type: 95, jiaoyouId:this.node.openParam.stageCfg.id}); 
            Utils.utils.openPrefabView("battle/BattleBaseView", null
             , { type: FIGHTBATTLETYPE.JIAOYOU, jiaoyouId: this.node.openParam.stageCfg.id, heroId: heroId }); 
            this.onClickBack()
        }
    }
});
