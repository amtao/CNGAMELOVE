let RenderListItem = require("RenderListItem");
let urlLoad = require("UrlLoad");
let ItemSlotUI = require("ItemSlotUI");
let Utils = require("Utils");
var UIUtils = require("UIUtils");
let Initializer = require("Initializer");

cc.Class({
    extends: RenderListItem,

    properties: {
        shouhuName:cc.Label,
        itemSlotUI:ItemSlotUI,
        shouhuBtn:cc.Node,
        awardBtn:cc.Node,
        starAry:[cc.Node],
        progress:cc.ProgressBar,
        timeProgress:cc.Label,
        needTime:cc.Label,
        nodeNormal:cc.Node,
        nodeLock:cc.Node,
    },

    onLoad () {
        this.shouhuData = null;
    },

    start () {

    },

    onClickShouhu(){
        Utils.utils.openPrefabView("jiaoyou/JiaoyouShouhuChooseView", !1, {
            jiaoyouData: this.shouhuData
        });
    },

    onClickAward(){
        //var jiaoyouCfg = localcache.getItem(localdb.table_jiaoyou,this._data.id)
        var jiaoyouCfg = this._data;
        Initializer.jiaoyouProxy.sendPickGuardAward(jiaoyouCfg.heroType,jiaoyouCfg.stage)
    },
    
    showData() {
        var jiaoyouCfg = this._data;
        this.nodeLock.active = false;
        this.nodeNormal.active = false;
        if (jiaoyouCfg) {
            this.shouhuName.string = jiaoyouCfg.chapterName + jiaoyouCfg.name;
            let t = Initializer.jiaoyouProxy.getShouHuDataById(jiaoyouCfg.heroType,jiaoyouCfg.stage);
            this.shouhuData = t;
            if (t == null){
                this.nodeLock.active = true;
                return;
            }
            this.shouhuData.shouhuServerId = t.stage;
            this.nodeNormal.active = true;
            t.star = t.star >5?5:t.star
            //var jiaoyouCfg = localcache.getItem(localdb.table_jiaoyou,t.id)
            var awardCfg = localcache.getItem(localdb.table_jiaoyouGuaji,t.award)
            var starCfg = localcache.getItem(localdb.table_jiaoyouStar,t.star)

            

            var needItem = {}
            needItem.id = awardCfg.item
            needItem.kind = awardCfg.kind
            needItem.count = Initializer.jiaoyouProxy.getShouhuAwdNum(awardCfg,jiaoyouCfg,starCfg,t.equipCard)
            this.itemSlotUI.data = needItem

            this.shouhuBtn.active = t.refreshTime == 0;
            this.awardBtn.active = t.refreshTime != 0 && (t.refreshTime + starCfg.shijian) - Utils.timeUtil.second <= 0;

            for(var i=1;i<=this.starAry.length;i++){
                this.starAry[i-1].active = i <= t.star
            }

            this.progress.node.active = t.refreshTime != 0;
            this.timeProgress.node.active = t.refreshTime != 0;

            UIUtils.uiUtils.countDown(t.refreshTime + starCfg.shijian, this.timeProgress, () => {
                this.awardBtn.active = t.refreshTime != 0 && (t.refreshTime + starCfg.shijian) - Utils.timeUtil.second <= 0;
                this.timeProgress.string = ""
                this.progress.node.active = false
            })

            this.needTime.node.active = t.refreshTime == 0;

            if(t.refreshTime != 0 && (t.refreshTime + starCfg.shijian) - Utils.timeUtil.second <= 0){
                this.needTime.node.active = true
                this.needTime.string = i18n.t("SEVEN_CAN_GET")
            }else{
                this.needTime.string = i18n.t("TIME_LANG")+":"+Utils.timeUtil.second2hms(starCfg.shijian)
            }
            
        }
    },
    
    update(){
        if (this.shouhuData == null) return;
        var starCfg = localcache.getItem(localdb.table_jiaoyouStar,this.shouhuData.star)
        var maxTime = this.shouhuData.refreshTime + starCfg.shijian
        this.progress.progress = 1 - (maxTime - Utils.timeUtil.second) / starCfg.shijian
    }
});
