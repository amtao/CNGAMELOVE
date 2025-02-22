//9-19
//守护选择伙伴卡牌
//this.node.openParam:{id:郊游配置的id,  award:jiaoyouGuaji的id  star:jiaoyouStar的id}

let ItemSlotUI = require("ItemSlotUI")
let List = require("List")
let Initializer = require("Initializer");
let Utils = require("Utils")
let urlLoad = require("UrlLoad");
let UIUtils = require("UIUtils")
cc.Class({
    extends: cc.Component,

    properties: {
        shouhuName:cc.Label,
        awardItem:ItemSlotUI,
        awardTime:cc.Label,
        addProgress:cc.Label,
        chooseCardNode:[urlLoad],
        allCard:[cc.Node],
        bigList:List,   //总显示滚动列表
    },

    onLoad () {
        Initializer.jiaoyouProxy.shouhuChooseCard = []
        facade.subscribe("JIAOYOU_SHOUHU_CARD",this.refreshUI,this)
    },

    start () {
        this.jiaoyouData = this.node.openParam.jiaoyouData
        this.refreshUI()
    },

    refreshUI(){
        var jiaoyouCfg = localcache.getItem(localdb.table_jiaoyou,this.jiaoyouData.id)
        var awardCfg = localcache.getItem(localdb.table_jiaoyouGuaji,this.jiaoyouData.award)
        var starCfg = localcache.getItem(localdb.table_jiaoyouStar,this.jiaoyouData.star)

        this.shouhuName.string = jiaoyouCfg.name;
        var itemData = {}
        itemData.id = awardCfg.item
        itemData.kind = awardCfg.kdin
        itemData.count = Initializer.jiaoyouProxy.getShouhuAwdNum(awardCfg,jiaoyouCfg,starCfg,Initializer.jiaoyouProxy.shouhuChooseCard)
        this.awardItem.data = itemData

        this.awardTime.string = Utils.timeUtil.second2hms(starCfg.shijian)
        this.addProgress.string = Initializer.jiaoyouProxy.shouhuXiaolv(Initializer.jiaoyouProxy.shouhuChooseCard) + "%"

        var allCard = Initializer.cardProxy.getHeroCardsByJiaoyou(jiaoyouCfg.heroType)
        console.error("allCard:",allCard)
        var showAry = []
        var showIndex = 0
        for(var i=1;i<=allCard.length;i++){
            if(!showAry[showIndex]){
                showAry[showIndex] = []
            }
            showAry[showIndex].push({jiaoyouId:this.jiaoyouData.id,cardid:allCard[i-1]})
            if(i%4 == 0){
                showIndex++
            }
        }
        console.error("showAry:",showAry)
        this.bigList.data = showAry

        var cardNum = jiaoyouCfg.cardNum
        for(var z=0;z<this.chooseCardNode.length;z++){
            this.allCard[z].active = cardNum>=(z+1)

            var cardId = Initializer.jiaoyouProxy.shouhuChooseCard[z]?Initializer.jiaoyouProxy.shouhuChooseCard[z]:0
            if(cardId > 0){
                this.chooseCardNode[z].url = UIUtils.uiHelps.getItemSlot(cardId);
            }else{
                this.chooseCardNode[z].url = null
            }
        }
    },

    //点击已选择的card
    onClickChooseIndex(e,index){
        Initializer.jiaoyouProxy.removeChooseCardByIndex(parseInt(index))
    },

    onClickBack() {
        Utils.utils.closeView(this);
    },

    onClickShouhu(){
        var jiaoyouCfg = localcache.getItem(localdb.table_jiaoyou,this.jiaoyouData.id)
        if(Initializer.jiaoyouProxy.sendStartGuard(jiaoyouCfg.heroType,jiaoyouCfg.stage,jiaoyouCfg.cardNum)){
            this.onClickBack()
        }
    }
});
