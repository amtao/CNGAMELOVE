let UrlLoad = require("UrlLoad");
let UIUtils = require("UIUtils");

cc.Class({
    extends: cc.Component,

    properties: {
        allNumber:[UrlLoad],
        lbLevel: cc.Label,
    },

    setNumberInfo(info, isRed) { 
        if(this.lbLevel) {
            this.lbLevel.string = info;
        } else {
            for(let i = 0; i < this.allNumber.length; i++) {
                this.allNumber[i].node.active = false;
                if(i < info.length) {
                    this.allNumber[i].node.active = true;
                    this.allNumber[i].url = UIUtils.uiHelps.getCardPveNumber(info.charAt(i), isRed);
                }
            }
        }
    }
});
