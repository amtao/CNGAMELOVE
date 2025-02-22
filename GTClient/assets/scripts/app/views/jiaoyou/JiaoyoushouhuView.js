//9-17 HZW
//守护任务ui
let Utils = require("Utils")
let List = require("List")
let Initializer = require("Initializer");
cc.Class({
    extends: cc.Component,

    properties: {
        tags:[cc.Toggle],
        showList:List,
        dayCtLbl:cc.Label,
        refreshBtn:cc.Button,
        nodeRedArr:[cc.Node],
    },

    onLoad () {
        this.curIndex = 0

        facade.subscribe("REFRESH_JIAOYOU_GUARD",this.setTags,this)
        facade.subscribe("ON_JIAOYOU_INFO",this.setTags,this)
    },

    start () {
        for(var i=0;i<this.tags.length;i++){
            var tagNode = this.tags[i].node;
            var servantId = i+1;
            var servantCfg = localcache.getItem(localdb.table_hero,servantId);
            tagNode.getChildByName("spBg").getChildByName("lbTitle").getComponent(cc.Label).string = servantCfg.name
            this.nodeRedArr[i].active = Initializer.jiaoyouProxy.checkShouhuReward(servantId);
        }
        this.setTags()
    },

    setTags(){
        for(var i=0;i<this.tags.length;i++){
            this.tags[i].isChecked = i == this.curIndex
        }
        this.curServantId = parseInt(this.curIndex)+1
        //var showAry = Initializer.jiaoyouProxy.getShouhuList(this.curServantId)
        
        var dayCt = Initializer.jiaoyouProxy.getDayShouhuCt()
        this.dayCtLbl.string = i18n.t("BOSS_SHENG_YU_CI_SHU")+ (dayCt-Initializer.jiaoyouProxy.defendCount)

        //this.shouhuNoneLbl.active = showAry.length == 0
        //this.showList.data = showAry
        let listCfg = localcache.getList(localdb.table_jiaoyou);
        let servantId = this.curServantId;
        let listdata = listCfg.filter((data) => {
                if(data["heroType"] == servantId && data.keguaji == 1){
                    return true;
                }else{
                    return false;
                }
            });
        let shouhuList = [];
        let rIdx = 0;
        for (var i = 0; i < listdata.length; i++) {
            let cg = listdata[i];
            if (Initializer.jiaoyouProxy.getShouHuDataById(cg.heroType,cg.stage) == null){
                rIdx++;
            }
            if (rIdx > 1){
                break;
            }
            shouhuList.push(cg);
        }
        this.showList.data = shouhuList;

        this.refreshBtn.interactable = Initializer.servantProxy.getHeroData(this.curServantId)

        this.nodeRedArr[this.curIndex].active = Initializer.jiaoyouProxy.checkShouhuReward(this.curServantId);
    },

    onClickTab(e,index){
        this.curIndex = index;
        this.setTags()
    },

    onClickAddCt(){
        Utils.utils.openPrefabView("jiaoyou/JiaoyouBuyCt");
    },

    //刷新列表
    onClickReflish(){
        if(Initializer.jiaoyouProxy.isOpenRefreshView()){
            Utils.utils.openPrefabView("jiaoyou/JiaoyouShouhuRefresh",null,{heroid:this.curServantId});
        }else{
            Initializer.jiaoyouProxy.sendRefreshGuardList(this.curServantId)
        }
    },

    onClickBack() {
        Utils.utils.closeView(this);
    },
});
