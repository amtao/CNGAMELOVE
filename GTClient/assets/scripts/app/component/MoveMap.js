var i = require("Config");
var n = require("Utils");
cc.Class({
    extends: cc.Component,
    properties: {
        nodes: [cc.Node],
        objLayer: cc.Node,
        speedstr:{ default:"", tooltip: "速度字符串以'|'分隔" },
        speedstrY:{ default:"", tooltip: "速度字符串以'|'分隔" },
        statrX:{ default:169, tooltip: "初始位移X位置" },
        statrY:{ default:0, tooltip: "初始位移Y位置" },
        scroll: cc.ScrollView,
        bAccelerometerEnabled: false,
    },

    ctor() {
        this._off = 0;
        this._offY = 0;
        this._last = 0;
        this._lastY = 0;
        this.speeds = [];
        this.speedYs = [];
        this.offH = 0;
    },

    onLoad() {
        if(this.scroll.enabled) {
            for (var t = this.speedstr.split("|"), e = 0; e < t.length; e++) this.speeds.push(parseFloat(t[e]));
            t = n.stringUtil.isBlank(this.speedstrY) ? [] : this.speedstrY.split("|");
            for (e = 0; e < t.length; e++) this.speedYs.push(parseFloat(t[e]));
            if (this.scroll) {
                this.scroll.scrollToOffset(new cc.Vec2(this.statrX, this.statrY));
                this._last = this.statrX;
                this._lastY = this.statrY;
            }
            //this.moveOff(this.statrX, this.statrY);
            if (this.scroll) {
                this.offH = this.node.height - i.Config.showHeight;
                this.schedule(this.onTimer, 0.03);
            }

            // if(cc.sys.isMobile && this.bAccelerometerEnabled) {
            //     cc.systemEvent.setAccelerometerInterval(1 / 30);
            //     cc.systemEvent.setAccelerometerEnabled(true); 
            //     cc.systemEvent.on(cc.SystemEvent.EventType.DEVICEMOTION, this.onDeviceMotionEvent, this);
            // }
        }
    },

    onTimer() {
        if (this._last != -this.scroll.getScrollOffset().x || this._lastY != -this.scroll.getScrollOffset().y) {
            this._last = -this.scroll.getScrollOffset().x;
            this._lastY = -this.scroll.getScrollOffset().y;
            this.moveTo(this._last, this._lastY);
        }
    },

    addObject(t, e, o) {
        void 0 === e && (e = 0);
        void 0 === o && (o = 0);
        if (this.objLayer) {
            this.objLayer.addChild(t);
            t.x = e;
            t.y = o;
        }
    },

    moveOff(t, e) {
        this._off += t;
        this._offY += e;
        this._off = this._off + cc.winSize.width > this.node.width ? this.node.width - cc.winSize.width: this._off;
        this._offY = this._offY + i.Config.showHeight > this.node.height ? this.node.height - i.Config.showHeight: this._offY;
        for (var o = 0; o < this.nodes.length; o++) {
            var n = this._off * this.speeds[o];
            this.nodes[o].x = this.scroll ? this._last - n: -n;
            if (0 != this.speedYs.length) {
                var l = this.speedYs.length > o ? this._offY * this.speedYs[o] : 0;
                this.nodes[o].y = this.scroll ? this._lastY - l: -l;
                this.nodes[o].y = 0 == this.speedYs[o] ? Math.floor(this.offH + this._lastY) : this.nodes[o].y;
            }
        }
    },

    moveTo(t, e) {
        this._off = t;
        this._offY = e;
        this.moveOff(0, 0);
    },

    onDeviceMotionEvent: function(event) {
        if(this.scroll.isScrolling()) {
            return;
        }
        let offset = this.scroll.getScrollOffset();
        this.scroll.scrollToOffset(new cc.Vec2(-offset.x + event.acc.x, offset.y + event.acc.y), 0.1);
    },

    // onDestroy: function() {
    //     if(cc.sys.isMobile && this.bAccelerometerEnabled) {
    //         cc.systemEvent.setAccelerometerEnabled(false);
    //         cc.systemEvent.off(cc.SystemEvent.EventType.ACCELERATION, this.onDeviceMotionEvent, this);
    //     }
    // }
});
