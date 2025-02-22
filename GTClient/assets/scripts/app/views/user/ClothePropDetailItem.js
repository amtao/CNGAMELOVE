var i = require("RenderListItem");
let Initializer = require("Initializer");
let ShaderUtils = require("ShaderUtils");
cc.Class({
    extends: i,
    properties: {
    	sp: cc.Sprite,
        lblContext:cc.RichText,
    },
    ctor() {},
    showData() {
        var t = this._data;
        if (t){
            let brocadeData = Initializer.clotheProxy.brocadeInfoData;
            let curLevel = brocadeData.suitBrocadeLv[t.suit] ? brocadeData.suitBrocadeLv[t.suit] : 0;
            this.sp.node.active = (t.lv % 2 ==1);
            if (curLevel >= t.lv){
                //this.lblContext.node.color = cc.Color.WHITE.fromHEX("#8F5524");
                this.lblContext.string = i18n.t("USER_CLOTHE_CARD_TIPS62",{v1:i18n.t("USER_CLOTHE_CARD_TIPS59",{v1:t.lv})}) + Initializer.clotheProxy.getCutClotheLevelDes(t.type,t.rwd,true);
                ShaderUtils.shaderUtils.setImageGray(this.sp,false);
            }
            else{
                this.lblContext.string = "<color=#565455>" +  i18n.t("USER_CLOTHE_CARD_TIPS62",{v1:t.lv}) + Initializer.clotheProxy.getCutClotheLevelDes(t.type,t.rwd) + "</color>";
                ShaderUtils.shaderUtils.setImageGray(this.sp,true);
                //this.lblContext.node.color = cc.Color.WHITE.fromHEX("#565455");
            }           
        }
    },
});
