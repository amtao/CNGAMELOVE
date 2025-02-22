// 9/16 HZW
// 郊游选择伙伴界面 
var Initializer = require("Initializer");
var UIUtils = require("UIUtils");
var Utils = require("Utils");

cc.Class({
    extends: cc.Component,

    properties: {
        headArr: [cc.Node],
        randomNode: cc.Node,
        nodeRedArr:[cc.Node],
    },

    onLoad () {
    },

    start () {
        //伙伴显示顺序
        this.servantIds = [1,3,2,5,4,6]
        this.randomNode.active = false
        this.isRandom = false

        this.showServantArr()

        facade.subscribe("ON_GOTO_JIAOYOU",this.onClickBack,this)
    },

    showServantArr(){
        var dayCt = Initializer.jiaoyouProxy.getDayShouhuCt();
        for(let i = 0; i < this.headArr.length; i++) {
            var servantId = this.servantIds[i]
            var urlComp = this.headArr[i].getChildByName("mask").getChildByName("url").getComponent("UrlLoad");
            urlComp.url = UIUtils.uiHelps.getServantHead(servantId);

            let heroCfg = localcache.getItem(localdb.table_hero,servantId);
            this.headArr[i].getChildByName("heroName").getComponent(cc.Label).string = heroCfg.name;
            let hasFlag = Initializer.servantProxy.getHeroData(servantId) != null;
            this.headArr[i].getChildByName("cz_suo").active = !hasFlag;
            this.nodeRedArr[i].active = hasFlag && (Initializer.jiaoyouProxy.checkShouhuReward(servantId) || Initializer.jiaoyouProxy.checkBoxReward())
        }
    },

    onClickRendom(){
        let randAry = []
        for(var i=1;i<=6;i++){
            if(Initializer.servantProxy.getHeroData(i)){
                randAry.push(i)
            }
        }
        if(randAry.length <= 0){
            Utils.alertUtil.alert(i18n.t("NO_SERVANT"));
            return
        }
        if(this.isRandom){
            return
        }
        this.isRandom = true

        var servantId = randAry[Utils.utils.randomNum(0,randAry.length-1)]
        this.randomNum = this.servantIds.indexOf(servantId)+1;
        this.randomNode.active = true
        this.randomNode.rotation = 0
        this.randomTime = 0
        this.schedule(function() {
            this.randomNode.rotation = this.randomNode.rotation + 9 >360?0:this.randomNode.rotation + 9
            this.randomTime++
            var endRotation = (this.randomNum - 1) * 60
            if(this.randomTime > 80 && Math.abs(this.randomNode.rotation - endRotation) <= 3){
                this.unscheduleAllCallbacks()
                
                this.scheduleOnce(function() {
                    this.isRandom = false
                    this.onClickHero(null,this.randomNum)
                    this.randomNode.active = false
                }, 0.5);
            }
        });
    },

    onClickHero(event,servantId){
        if(this.isRandom){
            return
        }
        servantId = this.servantIds[servantId - 1]
        if(Initializer.servantProxy.getHeroData(servantId)){
            Utils.utils.openPrefabView("jiaoyou/JiaoyouServantRun",!1,{servantId:servantId});
        }
    },

    onClickBack: function() {
        if(this.isRandom){
            return
        }
        Utils.utils.closeView(this, !0);
    },
});
