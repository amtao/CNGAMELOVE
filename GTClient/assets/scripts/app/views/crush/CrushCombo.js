
let UIUtils = require("UIUtils");
let UrlLoad = require("UrlLoad");
cc.Class({
    extends: cc.Component,

    properties: {
        combonum1:UrlLoad,
        combonum2:UrlLoad,
        effect:sp.Skeleton
    },
    onLoad(){
        this.node.active = false;
        this.effect.setCompleteListener(() => {
            this.node.active = false;
        })
    },
    setComboCount(count){
        if(count > 1){
            let num2 = Math.floor(count%10);
            let num1 = Math.floor((count-num2)/10);
            if(num1 > 0){
                this.combonum1.url = UIUtils.uiHelps.getCrushComboNum(num1);
                this.combonum2.node.active = true;
                this.combonum2.url = UIUtils.uiHelps.getCrushComboNum(num2);
            }else{
                this.combonum1.url = UIUtils.uiHelps.getCrushComboNum(num2);
                this.combonum2.node.active = false;
            }
            this.node.active = true;
            this.effect.setAnimation(1,"animation",false);
        }
    }
});
