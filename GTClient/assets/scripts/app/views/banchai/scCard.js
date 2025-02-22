let scUtils = require("Utils");

cc.Class({
    extends: cc.Component,

    properties: {
        nBg: cc.Node,
        lbContent: cc.Label,
        nYes: cc.Node,
        lbYes: cc.Label,
        nNo: cc.Node,
        lbNo: cc.Label,
    },

    ctor () {
        this.index = null;
        this.target = null;
        this.doChooseFunc = null;
        this.doMoveDragFunc = null;
        this.aniFinishFunc = null;
        this.times = 10;
        this.count = 0;
        this.flagStr = "";
    },
    
    onLoad () {     
        this.node.on(cc.Node.EventType.TOUCH_START, this.onDragStart, this);
        this.node.on(cc.Node.EventType.TOUCH_MOVE, this.onDrag, this);
        this.node.on(cc.Node.EventType.TOUCH_END, this.onDragEnd, this);
        this.node.on(cc.Node.EventType.TOUCH_CANCEL, this.onDragEnd, this);

        this.initWidth = this.node.width;
        this.initHeight = this.node.height;
        this.initPos = this.node.position;
    },

    setData: function(data, answer, bBack) {
        this.data = data;
        this.bAnswer = !scUtils.stringUtil.isBlank(answer);
        this.nBg.active = !this.bAnswer;
        
        if(this.bAnswer || null == data) {
            this.lbContent.string = this.index == 0 ? answer : " ";
            this.nYes.active = false;
            this.nNo.active = false;
        } else if(null != data && scUtils.stringUtil.isBlank(data.override_yes)) {
            this.nBg.active = false;
            this.lbContent.string = data.question;
            this.nYes.active = false;
            this.nNo.active = false;
        } else {
            this.lbContent.string = data.question;
            this.lbYes.string = data.override_yes;
            this.lbNo.string = data.override_no;
            this.nYes.active = true;
            this.nNo.active = true;
            this.nYes.opacity = 0;
            this.nNo.opacity = 0;
        }
        if(bBack) {
            this.lbContent.string = " ";
        }
    },

    onDragStart: function(event) {
        if(!this.bCanDrag) {
            return;
        }
        this.bDrag = true;
        this.startDragPosX = event.currentTouch._point.x;
        this.startDragPosY = event.currentTouch._point.y;
    },

    onDrag: function(event) {
        if(!this.bDrag) {
            return;
        }
        this.dragOffset = new cc.Vec2(this.startDragPosX - event.currentTouch._point.x,
         this.startDragPosY - event.currentTouch._point.y);
        this.startDragPosX = event.currentTouch._point.x;
        this.startDragPosY = event.currentTouch._point.y;
        this.node.angle += (this.dragOffset.x * 0.1);
        let opacity = Math.abs(this.node.angle) * 51 > 255 ? 255 : Math.abs(this.node.angle) * 51;
        if(this.node.angle > 0) {   
            this.nNo.opacity = opacity;
            this.nYes.opacity = 0;
            if (!this.bAnswer && this.flagStr != "no" && this.doMoveDragFunc) {
                this.doMoveDragFunc.call(this.target, "no", this.data)
                this.flagStr = "no";
            }
        } else if(this.node.angle < 0) {
            this.nYes.opacity = opacity;
            this.nNo.opacity = 0;
            if (!this.bAnswer && this.flagStr != "yes" && this.doMoveDragFunc) {
                this.doMoveDragFunc.call(this.target, "yes", this.data);
                this.flagStr = "yes";
            }
        } else if(this.node.angle == 0) {
            this.nYes.opacity = 0;
            this.nNo.opacity = 0;
        }
        let y = this.node.y - (this.dragOffset.y * 0.5);
        if(y > this.initPos.y + 50) {
            y = this.initPos.y + 50;
        } else if(y < this.initPos.y - 50) {
            y = this.initPos.y - 50;
        }
        this.node.setPosition(this.node.x - (this.dragOffset.x * 0.5), y);
    },

    onDragEnd: function() {
        if(!this.bDrag) {
            return;
        }
        this.bDrag = false;

        scUtils.audioManager.playEffect("5", true, true);

        if(this.node.angle > 7) { //left say No
            this.direction = 0;
        } else if(this.node.angle < -7) { //right say Yes
            this.direction = 1;
        } else { //滑的不够多, 当作没选择回到最初效果
            this.direction = -1;
            this.nYes.opacity = 0;
            this.nNo.opacity = 0;
        }
        this.flagStr = "normal";
        this.doMoveDragFunc && this.doMoveDragFunc.call(this.target, "normal", this.data);
        this.dragOffset = new cc.Vec2(this.node.angle / Math.abs(this.node.angle) * -40, 10);
        this.unschedule(this.toFinishAni);
        this.count = 0;
        if(this.direction == -1) {
            this.scheduleOnce(this.toFinishAni);
        } else {
            this.schedule(this.toFinishAni, 0.01, this.times);
        }
    },

    toFinishAni: function() {
        if(this.direction == -1) {
            this.node.angle = 0;
            this.node.position = this.initPos;
            this.nYes.opacity = 0;
            this.nNo.opacity = 0;
        } else {
            this.node.angle -= (this.dragOffset.x * 0.1);
            this.node.setPosition(this.node.x + this.dragOffset.x, this.node.y - this.dragOffset.y);
            if(++this.count >= this.times) {
                this.doChooseFunc.call(this.target, this.data, this.bAnswer, this.direction);
                this.unschedule(this.toFinishAni);
            }
        }
    },

});
