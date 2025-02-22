// 9/17 HZW
// 郊游副本节点
let urlLoad = require("UrlLoad");
let config = require("Config");
let Initializer = require("Initializer");
let Utils = require("Utils")
import { FIGHTBATTLETYPE } from "GameDefine";

cc.Class({
    extends: cc.Component,

    properties: {
        bgUrl:urlLoad,
        stageName:cc.Label,
        eftNode:cc.Component,
        fightEft:cc.Node,
        nodeSpine:cc.Node,
    },

    onLoad () {},

    start () {

    },

    /**
     * @param {*} type 1=>后退按钮   2=>前进按钮  3=>显示章节
     * stageCfg =>stage配置
     */
    onShow(type,stageCfg,servantId){
        this.rightLine = this.node.getChildByName("rightLine")
        this.leftLine = this.node.getChildByName("leftLine")

        if(this.node.x == -90){
            this.rightLine.rotation = 35
        }else{
            this.rightLine.height = 185
        }

        this.stageCfg = stageCfg
        this.type = type
        this.servantId = servantId

        var isFight = false
        this.nodeSpine.active = false;
        Utils.utils.stopEffect(this.eftNode, 0, () => {});
        this.fightEft.active = false
        if(type == 1){
            this.bgUrl.url = config.Config.skin + "/res/ui/jiaoyou/jy_an_gq_syz"
            this.stageName.string = ""
            this.rightLine.active = this.node.x <= 0
            this.leftLine.active = this.node.x > 0
        }else if(type == 2){
            this.bgUrl.url = config.Config.skin + "/res/ui/jiaoyou/jy_an_gq_xyz"
            this.rightLine.active = false
            this.leftLine.active = false
            this.stageName.string = ""
        }else{
            this.stageName.string = stageCfg.name;
            
            this.showType = Initializer.jiaoyouProxy.stageType(servantId,stageCfg.stage)
            if(this.showType == Initializer.jiaoyouProxy.STAGE_FIGHT){
                isFight = true
                this.bgUrl.url = config.Config.skin + "/res/ui/jiaoyou/jy_an_gq_tz"
                Utils.utils.showEffect(this.eftNode, 0, () => {});
                this.fightEft.active = true
                this.nodeSpine.active = true;
            }else if(this.showType == Initializer.jiaoyouProxy.STAGE_CLEAR){
                //已完成 有剧情的显示书没剧情的显示花
                if(this.stageCfg.openstory && this.stageCfg.openstory != "" && this.stageCfg.openstory!="0"){
                    this.bgUrl.url = config.Config.skin + "/res/ui/jiaoyou/jy_an_gq_jq_1"
                }else{
                    this.bgUrl.url = config.Config.skin + "/res/ui/jiaoyou/jy_an_gq_jb"
                }
                if (Initializer.jiaoyouProxy.getServantMaxStage(servantId) == stageCfg.stage){
                    isFight = true;
                }
            }else{
                this.bgUrl.url = config.Config.skin + "/res/ui/jiaoyou/jy_map_icon4"
            }
            this.rightLine.active = this.node.x <= 0
            this.leftLine.active = this.node.x > 0

            let condition = stageCfg.condition
            var jibanHero = Initializer.jibanProxy.getHeroJbLv(servantId);
            var jibanLv = jibanHero.level?jibanHero.level:0
            if(condition == 1 && jibanLv < stageCfg.set){
                this.jibanError = true
            }
        }
        return isFight
    },

    onClickStage(){
        if(this.type == 1){
            facade.send("jiaoyou_chapter_back")
        }else if(this.type == 2){
            facade.send("jiaoyou_chapter_forward")
        }else{
            //未达到羁绊等级
            if(this.jibanError){
                Utils.alertUtil.alert(i18n.t("JIBAN_LV_ERROR",{c:this.stageCfg.set % 1e3}));
            }else if(this.showType == Initializer.jiaoyouProxy.STAGE_CLEAR){
                if(this.stageCfg.openstory && this.stageCfg.openstory!="" && this.stageCfg.openstory!="0"){
                    Initializer.playerProxy.addStoryId(this.stageCfg.openstory);
                    Utils.utils.openPrefabView("StoryView", !1, {
                        type: 95,
                        extraParam: {onlineStory:true}
                    });
                }else{
                    Utils.alertUtil.alert(i18n.t("MAIN_TASK_OVER"));
                }
            } else if(this.showType == Initializer.jiaoyouProxy.STAGE_CLOSE) {
                Utils.alertUtil.alert(i18n.t("STAGE_CLEAR_OPEN"));
            } else if(!Initializer.fightProxy.checkTeamCanFight(FIGHTBATTLETYPE.JIAOYOU, this.servantId)) {
                let heroid = this.servantId;
                Utils.utils.openPrefabView("battle/BattleTeamView", null, { type: FIGHTBATTLETYPE.JIAOYOU, heroid: heroid });
                return;
            } else {
                Initializer.jiaoyouProxy.sendgetFightInfo(this.servantId,this.stageCfg);
            }
        }
    }
});
