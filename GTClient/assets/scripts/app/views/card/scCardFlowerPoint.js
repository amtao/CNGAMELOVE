let UrlLoad = require("UrlLoad");
let UIUtils = require("UIUtils");
let Initializer = require("Initializer");

cc.Class({
    extends: cc.Component,

    properties: {
        btnSelf: cc.Button,
        nUnlocked: cc.Node,
        urlSprite: UrlLoad,
        nSelected: cc.Node,
    },

    setData(pointId, flowerPoints, cfgData, cardData, curSelect) {
        this.pointId = pointId;
        this.flowerPoints = flowerPoints;
        this.cfgData = cfgData;
        this.cardData = cardData;

        let canChoiceList = Initializer.cardProxy.getCanFlowerUpPoint(cardData.cfgData.quality, cardData.flowerPoint);
        let bCan = canChoiceList.filter((data) => {
            return data.flower_point == pointId;
        })
        this.btnSelf.interactable = bCan && bCan.length > 0;
        
        let propId = 0;
        for(let i = 1; i <= 4; i++) {
            if(cfgData["ep" + i] > 0) {
                propId = i;
                break;
            }
        }
        if(null != flowerPoints) {
            let bHas = flowerPoints.filter((data) => {
                return data == pointId;
            });
            this.nUnlocked && (this.nUnlocked.active = bHas && bHas.length > 0);
            this.urlSprite && (this.urlSprite.url = UIUtils.uiHelps.getFlowerProp(bHas && bHas.length > 0, propId));
        } else {  
            this.nUnlocked && (this.nUnlocked.active = false);
            this.urlSprite && (this.urlSprite.url = UIUtils.uiHelps.getFlowerProp(false, propId));
        }

        this.nSelected.active = curSelect == this.pointId;
    },

});
