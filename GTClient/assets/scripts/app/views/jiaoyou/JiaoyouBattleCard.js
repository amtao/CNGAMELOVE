// 郊游战斗里的card展示
//this.node.openParam = {heroId:id,stageCfg:jiaoyou配置}
let Utils = require("Utils")
let List = require("List");
let Initializer = require("Initializer");
cc.Class({
    extends: cc.Component,

    properties: {
        titleLbl1:cc.Label,
        titleLbl2:cc.Label,
        attrAry:[cc.Node],
        attrLbl:[cc.Label],
        itemList: List,
    },

    onLoad () {

    },

    start () {
        var heroId = this.node.openParam.heroId
        var heroCfg = localcache.getItem(localdb.table_hero,heroId)
        this.titleLbl1.string = i18n.t("CLOTHE_PVE_GATE",{d:this.node.openParam.stageCfg.stage})
        this.titleLbl2.string = i18n.t("JIAOYOU_BATTLE_CARD_2",{name:heroCfg.name})

        //需要显示的属性
        var epnum = this.node.openParam.stageCfg.epnum
        for(var z=0;z<this.attrAry.length;z++){
            if(epnum.indexOf(z+1) >=0 ){
                this.attrAry[z].active = true
                if(Initializer.playerProxy.allEpData && Initializer.playerProxy.allEpData.cardHeroEp && Initializer.playerProxy.allEpData.cardHeroEp[heroId]){
                    var attr = Initializer.playerProxy.allEpData.cardHeroEp[heroId]
                    this.attrLbl[z].string = attr[z+1]?attr[z+1]:"0"
                }else{
                    this.attrLbl[z].string = "0"
                }
            }else{
                this.attrAry[z].active = false
            }
        }

        // let cardList = localcache.getList(localdb.table_card)
        // cardList = Initializer.cardProxy.resortCardList(cardList);
        // console.error("cardList:",cardList)
        let cardList = Initializer.cardProxy.getNewCardList(heroId,0,0)
        let listdata = [];
        let showIndex = 0
        for (var ii = 0; ii < cardList.length;ii++){
            //if(!cardList[ii].hero || cardList[ii].hero == heroId){
                let idx = Math.floor(showIndex/3);
                if (listdata[idx] == null){
                    listdata[idx] = []
                }
                listdata[idx].push(cardList[ii]);
                showIndex++
            //}
        }



        this.itemList.data = listdata

        // Initializer.cardProxy.currentCardList.length = 0;
        // for (var ii = 0; ii < cardList.length;ii++){
        //     if (Initializer.cardProxy.getCardInfo(cardList[ii].id) != null){
        //         Initializer.cardProxy.currentCardList.push(cardList[ii])
        //     }
        // }

    },    
    onClickBack: function() {
        facade.send("CLOSE_JIAOYOU_BATTLE_CARD")
        Utils.utils.closeView(this);
    },
});
