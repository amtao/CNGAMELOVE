let UrlLoad = require("UrlLoad");
let UIUtils = require("UIUtils");
let Initializer = require("Initializer");
let Utils = require("Utils");
var RenderListItem = require("RenderListItem");
cc.Class({
    extends: RenderListItem,
    properties: {
        spCard: UrlLoad,//卡图
        frameImg: UrlLoad,
        qualityImg: UrlLoad,
        nameLabel: cc.Label,        
        //ndStar:cc.Node,//星级节点
        //spStar:[UrlLoad],//星级图        
        //ndAni: cc.Node,
    },
    onLoad(){
        facade.subscribe(Initializer.cardProxy.CARD_DATA_UPDATE, this.showInfo, this);
    },


    showData() {
        var t = this._data;
        if (t) {
            this.cfgData = t;
            this.allListData = t;
            this.showInfo();
        }
    },

    showEffect() {
        //this.ndAni.active = true;
        //Utils.utils.showNodeEffect(this.ndAni, 20);
    },

    showInfo () {
        let cardData = Initializer.cardProxy.getCardInfo(this.cfgData.id);
        this.spCard.url = UIUtils.uiHelps.getCardSmallFrame(this.cfgData.picture);
        //this.frameImg.url = UIUtils.uiHelps.getQualityFrame(this.cfgData.quality, 0);
        //this.qualityImg.url = UIUtils.uiHelps.getQualitySp(this.cfgData.quality,0);
        //this.qualityImg.node.active = true;
        // if(cardData){
        //     this.qualityImg.node.active = this.cfgData.quality != 4 && this.cfgData.quality != 3;
        // }
        
        this.nameLabel.string = this.cfgData.name;
        //this.ndStar.active = false;                
        //this.ndAni.active = false;
        if(cardData){            
            // this.ndStar.active = (cardData.star > 0);
            // if(this.ndStar.active) {
            //     for(let i = 0; i < this.spStar.length; i++) {
            //         this.spStar[i].url = UIUtils.uiHelps.getStarFrame(i < cardData.star);
            //     }
            // }
            // if(this.cfgData.quality == 4 || this.cfgData.quality == 3) {
            //     this.ndAni.active = true;
            //     Utils.utils.showNodeEffect(this.ndAni, 0);
            // }

            // this.ndRedPot.active = Initializer.cardProxy.checkCardRedPot(this.cfgData, cardData);
            // if(this.ndRedPot.active) {
            //     Utils.utils.showNodeEffect(this.ndRedPot, 0);
            // }
            this.cardData = cardData;
        }
        this.showEffect();
    },

    onClickShowCardDetail() {
        if(Initializer.sevenDaysProxy.isSevenDaysComeIn())
        {
            var ss = new proto_cs.sevendays.pickFinalAward();
            JsonHttp.send(ss, function(data) {
                Initializer.timeProxy.floatReward();
            });

        } else {
            if(this.cardData) {
                Utils.utils.openPrefabView("card/showCardDetail", null, {
                    cardData: this.cardData,
                    cfgData: this.cfgData,
                    listData: this.allListData
                });
            } else {
                Utils.utils.openPrefabView("card/UnGetCardDetail", null, this.cfgData);
            }
        }        
    },
});
