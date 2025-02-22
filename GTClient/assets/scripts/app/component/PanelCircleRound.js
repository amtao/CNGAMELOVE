let renderListItem = require("RenderListItem");
var Utils = require("Utils");
cc.Class({
    extends:cc.Component,// use this for initialization
    properties: {
        data:{//list数据数组
            visible:false,
            get: function () {
                return this._data;
            },
            set: function (array) {
                this._data = array;
                if (this._data != null) {
                    for (var i = 0; i < this._data.length; i++) {
                        if (this._data[i])
                            this._data[i]['__index'] = i;
                    }
                }
                this.renderNext();
                if (this._renders && this._renders.length > 0 && this.isShowEffect) {
                    for (var i = 0; i < this._renders.length; i++) {
                        this._renders[i].showNodeAnimation();
                    }
                }
            },
        },
        item:renderListItem,
        bufferZone:0,
        startAngle:0,
        radius:100,
        durAngle:0,
    },

    ctor(){
        this._data = null;
        this._renders = null;
        this.maxRotation = 0;
        this.moveFlag = false;
    },

    onLoad : function () {
        this.node.on(cc.Node.EventType.TOUCH_START, this.onDragStart, this);
        this.node.on(cc.Node.EventType.TOUCH_MOVE, this.onDrag, this);
        this.node.on(cc.Node.EventType.TOUCH_END, this.onDragEnd, this);
        this.node.on(cc.Node.EventType.TOUCH_CANCEL, this.onDragEnd, this);

        if (this.item) {
            this.item.node.active = false;
        }
    },

    onDragStart(event) {
        this.beginRotation = this.node.rotation;
        this.moveFlag = false;
        //this.startDragPosX = event.currentTouch._point.x;
    },

    onDrag(event) {
        this.moveFlag = true;
        var self = this;
        if (self.node.rotation < self.maxRotation || self.node.rotation > 0) return;
        var touches = event.getTouches();
        //触摸刚开始的位置
        var oldPos = self.node.parent.convertToNodeSpaceAR(touches[0].getStartLocation());
        //触摸时不断变更的位置
        var newPos = self.node.parent.convertToNodeSpaceAR(touches[0].getLocation());
        var rad = Utils.utils.getVectorRadius(oldPos,newPos);

        self.node.rotation = this.beginRotation + rad;
        for (var i = 0; i < this._renders.length;i++){
            let node = this._renders[i].node;
            if (node.active)
                node.rotation = -self.node.rotation;
        }
    },

    onDragEnd(event) {
        // if (!this.moveFlag){
        //     for (var ii = 0; ii < this._renders.length;ii++){
        //         if (cc.rect(this._renders[ii].node.getBoundingBoxToWorld()).contains(event.getLocation())){
        //             facade.send("PANELCIRCLE_ITEMCLICK",this._renders[ii]);
        //             return;
        //         }
        //     }
        //     return;
        // }
        if (this.node.rotation > 0) this.node.rotation = 0;
        if (this.node.rotation < this.maxRotation && this.maxRotation < 0) this.node.rotation = this.maxRotation;
        for (var i = 0; i < this._renders.length;i++){
            let node = this._renders[i].node;
            if (node.active)
                node.rotation = -this.node.rotation;
        }
    },

    renderNext () {
        let length = this.data.length;
        if (this._renders == null){
            this._renders = [];
        }
        for (var i = 0; i < this._renders.length; i++) {
            this._renders[i].data = null;
            this._renders[i].node.active = false;
        }

        let dur = this.durAngle;
        let startAngle = this.startAngle
        let radius = this.radius;
        for (let i = 0;i < length;i++){
            if (i < this._renders.length){               
                let nodeitem = this._renders[i].node;
                nodeitem.active = true;
                let rad = (startAngle - (i * dur)) * Math.PI /180
                nodeitem.x = radius * Math.cos(rad);
                nodeitem.y = radius * Math.sin(rad);
                //nodeitem.rotation = 90-startAngle + (i * dur);
                nodeitem.rotation = 0;
                this._renders[i].data = this.data[i];
                continue;
            }
            var item = cc.instantiate(this.item.node);
            this.node.addChild(item);
            item.active = true;
            item.rotation = 0;
            let rad = (startAngle - (i * dur)) * Math.PI /180
            item.x = radius * Math.cos(rad);
            item.y = radius * Math.sin(rad);
            //item.rotation = 90-startAngle + (i * dur);
            var itemComp = item.getComponent(renderListItem);
            if (itemComp){
                itemComp.data = this.data[i];
                this._renders.push(itemComp);
            }
        }
        this.maxRotation = 2 * (startAngle - 90)-dur * (length - 1);
        this.node.rotation = 0;
    },

});