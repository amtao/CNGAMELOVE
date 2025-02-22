/**
 * button点击辅助类
 * 1.延迟一定时间才可点击
 * 2,每次点击延迟时间才可第二次点击
 */
cc.Class({
    extends: cc.Component,
    properties: {
        delayTime:{
            default:      0,
            displayName:"首次延迟点击时间"
        },
        gapTime:{
            default:      2,
            displayName:"点击间隔时间"
        },
        clickGray:{
            default:      false,
            displayName:"点击后置灰"
        },
    },
    onLoad(){
        let clickBtn = this.node.getComponent(cc.Button);
        if(clickBtn && this.delayTime > 0){
            clickBtn.interactable = false;
            this.scheduleOnce(()=>{
                clickBtn.interactable = true;
            },this.delayTime);
        }
        if(clickBtn && (this.gapTime > 0)){
            let clickHandler = new cc.Component.EventHandler();
            clickHandler.target = this.node;
            clickHandler.component = "TouchMgr";// 这个是代码文件名
            clickHandler.handler = "onClickEvent";
            clickBtn.clickEvents.push(clickHandler);
        }
    },
    onClickEvent(){
        let clickBtn = this.node.getComponent(cc.Button);
        if(clickBtn && (this.gapTime > 0)){
            clickBtn.interactable = false;
            this.scheduleOnce(()=>{
                clickBtn.interactable = true;
            },this.gapTime);
        }
    }
});
