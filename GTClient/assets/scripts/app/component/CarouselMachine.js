var Utils = require("Utils");
var RenderListItem = require("RenderListItem");
cc.Class({
    extends: cc.Component,
    properties: {
        itemArr:[RenderListItem],
        curDegree:0,
        totalTime:0,
        radiusX:0,
        radiusY:0,
        IsScale:true,
    },
    ctor() {
        this.startTime = 0;
        this.isPlay = false
        this.angleOffset = 0
        this.temAngle = 0;
    },

    onLoad() {          
        this.perAngle = 360 / this.itemArr.length;
        this.Rotation(this.curDegree, true)
    },


    update(dt) {
        if (this.isPlay) {
            this.startTime += dt
            if (this.startTime > this.totalTime) {
                this.startTime = this.totalTime
                this.isPlay = false
            }
            this.temAngle = this.startTime / this.totalTime * this.angleOffset + this.curDegree
            this.Rotation(this.temAngle, !this.isPlay);
        }
    },


    ClampAngleToZeroTo360(degree){
        if (degree < 0){
            return 360 - (Math.abs(degree) % 360)
        }
        else{
            return degree % 360;
        }
    },

    Rotation(curDegree, isEnd) {
        let x = 0
        let y = 0
        let radiusX = this.radiusX;
        let radiusY = this.radiusY;
        let degree = 0

        let slotList = [];

        for (let i = 0; i < this.itemArr.length; i++) {
            degree = i * this.perAngle + curDegree
            let radian = degree * Math.PI/180.0
            let x1 = Math.sin(radian) * radiusX
            let y1 = Math.cos(radian) * radiusY

            this.itemArr[i].node.x = x1
            this.itemArr[i].node.y = y1

            let d = this.ClampAngleToZeroTo360(degree)
            this.itemArr[i].order = Math.abs(d - 180);
            this.itemArr[i].degree = degree;
            slotList.push(this.itemArr[i])
        }

        slotList.sort((a, b) => {
            return a.order > b.order ? 1 : -1
        })
        for (let i = 0; i < slotList.length; i++) {
            slotList[i].node.zIndex = i;
            if (this.IsScale) {
                let scale = (slotList[i].order / 180) * 0.5 + 0.5
                slotList[i].node.scaleX = scale
                slotList[i].node.scaleY = scale
            }
        }

        if (isEnd) {
            let orderNum = 0;
            let sNum = slotList.length % 2 == 1 ? 180 : 360
            for (let i = 0; i < slotList.length; i++) {
                if (slotList[i].degree % sNum == 0) {
                    this.curDegree = curDegree / 360;
                    slotList[i].node.zIndex = slotList.length - 1;
                    facade.send("CAROUSE_SELECTEDFUNCTION",{index:slotList[i].index})
                }
                else{
                    slotList[i].node.zIndex = orderNum;
                    orderNum++;
                }
            }
            this.curDegree = curDegree
        }
    },

    MoveLeft() {
        if (!this.isPlay) {
            this.startTime = 0;
            this.angleOffset = -this.perAngle
            this.isPlay = true
        }

    },

    MoveRight() {
        if (!this.isPlay) {
            this.startTime = 0;
            this.isPlay = true
            this.angleOffset = this.perAngle
        }
    },
    
});
