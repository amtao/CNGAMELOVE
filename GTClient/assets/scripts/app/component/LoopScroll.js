let renderListItem = require("RenderListItem");
var Utils = require("Utils");
cc.Class({
    extends:cc.Component,// use this for initialization
    properties: {
        director:{ default:0, tooltip: "0 竖直滚动 1水平滚动" },
        maxNum:0,
        minNum:0,
        nodeArr:[renderListItem],
        speedNum:{ default:1000, tooltip: "滚动速度" },
    },

    ctor(){
        this.bStop = true;
        this.bPrepare = false;
        /**是否已经滚动过*/
        this.isRounded = false;
    },

    /**开始循环滑动*/
    onRun(){
        this.bPrepare = false;
        this.bStop = false;
    },

    /**结束滑动*/
    onEnd(){
        this.bPrepare = true;
    },

    /**暂停*/
    onPause(){
        this.bStop = true;
    },

    /**继续开始*/
    onReStart(){
        this.bStop = false;
    },

    update(dt){
        if (this.bStop) return;
        if (this.director == 0){
            let detal = dt * this.speedNum;
            this.nodeArr.forEach(element => {
                element.node.y += detal;
            });
            for (var ii = 0; ii < this.nodeArr.length;ii++){
                let item = this.nodeArr[ii];
                if (item.node.y >= this.maxNum){
                    item.node.y = this.minNum;                  
                    if (this.bPrepare && this.isRounded){
                        this.bStop = true;
                        this.bPrepare = false;
                        this.isRounded = false;
                        facade.send("NOTICE_FINISH_LOOPSCROLL");
                    }
                    else{
                        this.isRounded = true;
                        facade.send("NOTICE_REFRESH_LOOPSCROLL_ITEM",{idx:item.index});
                    }
                    break;
                }
            }
        }
        else if(this.director == 1){
            let detal = dt * this.speedNum
            this.nodeArr.forEach(element => {
                element.node.x -= detal;
            });
            for (var ii = 0; ii < this.nodeArr.length;ii++){
                let item = this.nodeArr[ii];
                if (item.node.x <= this.minNum){
                    item.node.x = this.maxNum;
                    if (this.bPrepare && this.isRounded){
                        this.bStop = true;
                        this.bPrepare = false;
                        this.isRounded = false;
                        facade.send("NOTICE_FINISH_LOOPSCROLL");
                    }
                    else{
                        this.isRounded = true;
                        facade.send("NOTICE_REFRESH_LOOPSCROLL_ITEM",{idx:item.index});
                    }
                    break;
                }
            }
        }
    },


});