var i = require("../utils/Utils");
var n = require("../utils/UIUtils");
cc.Class({
    extends: cc.Component,
    properties: {
        delayTime:{ default:0.3, tooltip: "" },
    },
    onLoad(){
        this.haveClick = false;
        let touchBtn = this.node.getComponent(cc.Button);
        if(touchBtn){
            let clickEventHandler = new cc.Component.EventHandler();
            clickEventHandler.target = this.node;
            clickEventHandler.component = "DelayTouch";// 这个是代码文件名
            clickEventHandler.handler = "onClick";
            touchBtn.clickEvents.push(clickEventHandler);
            this.DelayBtn = touchBtn;
        }  
    },
    onClick(){
        if(this.DelayBtn){
            this.DelayBtn.interactable = false;
            this.scheduleOnce(()=>{
                this.DelayBtn.interactable = true;
            },this.delayTime);
            console.error('点击结束');
        }
    }
});
