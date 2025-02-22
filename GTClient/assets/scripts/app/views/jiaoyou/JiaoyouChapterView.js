// 9/17 HZW
// 郊游副本ui
// this.node.openParam:{servantId:id}
let urlLoad = require("UrlLoad");
let UIUtils = require("UIUtils");
let Utils = require("Utils");
let Initializer = require("Initializer");
let JiaoyouStageNode = require("JiaoyouStageNode");
import { FIGHTBATTLETYPE } from "GameDefine";

cc.Class({
    extends: cc.Component,

    properties: {
        servantShow: urlLoad,
        stageNode: cc.Node,
        scrollContent: cc.Node,
        chapterName:cc.Label,
        protectCt:cc.Label,
        scrollNode:cc.Node,
        sc:cc.ScrollView,
        nodeShouHuRed:cc.Node,
    },

    onLoad () {
        this.showContentPos = 0;
        this.isFirst = true;
        //存储小关卡的节点
        this.stagePool = [this.stageNode]

        facade.subscribe("jiaoyou_chapter_back",this.onChapterBack,this)
        facade.subscribe("jiaoyou_chapter_forward",this.onChapterForward,this)
        facade.subscribe("ON_JIAOYOU_INFO",this.onJiaoyouInfo,this);
        facade.subscribe("COMMON_CLOSE_VIEW",this.colseCommonView,this);

        this.servantId = this.node.openParam.servantId
        this.servantShow.url = UIUtils.uiHelps.getServantSpine(this.servantId);

        this.showChapterId = Initializer.jiaoyouProxy.getServantChapter(this.servantId)
        this.reflishAllStage()
        this.reflishWeek()
        this.setContentPos()
        this.nodeShouHuRed.active = Initializer.jiaoyouProxy.checkShouhuReward(this.servantId);
        Initializer.playerProxy.updateTeamRed(this.servantId);
    },


    reflishWeek(){
        this.protectCt.string = Initializer.jiaoyouProxy.weekdefendCount
    },

    //收到郊游更新消息
    onJiaoyouInfo(){
        this.showChapterId = Initializer.jiaoyouProxy.getServantChapter(this.servantId)
        this.reflishAllStage()
        this.reflishWeek()
        this.nodeShouHuRed.active = Initializer.jiaoyouProxy.checkShouhuReward(this.servantId);
        
    },

    //刷新小关卡
    reflishAllStage(){
        this.showContentPos = 0
        this.allStage = Initializer.jiaoyouProxy.getChapterAllStage(this.servantId,this.showChapterId)
        this.chapterName.string = this.allStage[0].chapterName
        var stageIndex = 0
        var showIndex = 0
        //每个章节显示多少个节点 第一章加一个前进按钮 别的章节增加前进和后退
        var showLen = this.showChapterId == 1?this.allStage.length+1:this.allStage.length+2
        for(var stageIndex=0;stageIndex<showLen;stageIndex++){
            if(!this.stagePool[stageIndex]){
                var newStage = cc.instantiate(this.stageNode);
                this.stagePool.push(newStage)
                newStage.y = stageIndex*220
                if(this.stagePool.length%2 ==0){
                    newStage.x = 10
                }else{
                    newStage.x = -160
                }
                this.scrollContent.addChild(newStage)
            }
            this.setStageNode(this.stagePool[stageIndex],stageIndex,showIndex) && showIndex++;
        }
        //隐藏关卡池子里多余的
        if(this.stagePool.length > showLen){
            var len = this.stagePool.length - showLen
            for(var z=0;z<len;z++){
                this.stagePool[z+showLen].active = false
            }
        }
        this.scrollContent.height = showLen*220 - 50
        this.setContentPos()
    },

    setContentPos(){
        this.scheduleOnce(()=>{
            if (this.node == null || this.scrollContent == null) return;
            if(this.showContentPos > this.sc.getMaxScrollOffset().y){
                this.showContentPos = this.sc.getMaxScrollOffset().y
            }
            if (this.isFirst){
                this.scrollContent.y = this.showContentPos * -1;
                this.isFirst = false;
            }               
        },0.1)
    },

    /**设置每个节点的显示
     * stageNode：关卡节点
     * stageIndex：该节点列表显示的位置
     * **/
    setStageNode(stageNode,stageIndex,showIndex){
        stageNode.active = true
        if(stageIndex == 0){
            if(this.showChapterId == 1){
                if(stageNode.getComponent(JiaoyouStageNode).onShow(3,this.allStage[showIndex],this.servantId)){
                    this.showContentPos = stageNode.y
                }
                return true
            }else{
                stageNode.getComponent(JiaoyouStageNode).onShow(1)
            }
        }else if(this.allStage[showIndex]){
            if(stageNode.getComponent(JiaoyouStageNode).onShow(3,this.allStage[showIndex],this.servantId)){
                this.showContentPos = stageNode.y
            }
            return true
        }else{
            stageNode.getComponent(JiaoyouStageNode).onShow(2)
        }
        return false
    },

    //去前面一个章节
    onChapterBack(){
        if(this.showChapterId == 1){
            return
        }
        this.showChapterId -- 
        this.reflishAllStage()
    },

    //去后面一个章节
    onChapterForward(){
        var allStage = Initializer.jiaoyouProxy.getChapterAllStage(this.servantId,this.showChapterId + 1)
        if(allStage.length > 0){
            if(this.showChapterId+1 > Initializer.jiaoyouProxy.getServantChapter(this.servantId)){
                Utils.alertUtil.alert(i18n.t("CHAPTER_CLEAR_OPEN"));
                return
            }
            this.showChapterId ++
            this.reflishAllStage()
        }else{
            Utils.alertUtil.alert(i18n.t("JIAOYOU_CHAPTER_END"));
        }
    },

    onClickBack() {
        Utils.utils.closeView(this);
    },

    onClickShouhu(){
        Utils.utils.openPrefabView("jiaoyou/JiaoyouShouhuView");
    },

    colseCommonView(){
        if (this.node == null) return;
        let midLayer = cc.find("Canvas/midLayer");
        if (midLayer.childrenCount > 0){
            let child = midLayer.children[midLayer.childrenCount-1];
            if (child.name == this.node.name){
                this.onShowAni();
            }
        }
    },

    onShowAni(){
        let endY = this.showContentPos * -1;
        let dt = Math.abs(endY - this.scrollContent.y)/300
        if (dt == 0) return;
        this.scrollContent.runAction(cc.moveTo(dt,cc.v2(this.scrollContent.x,endY)))
    },

    onClickTeam: function() {
        let heroid = this.servantId;
        Utils.utils.openPrefabView("battle/BattleTeamView", null, { type: FIGHTBATTLETYPE.JIAOYOU, heroid: heroid });
    },

});
