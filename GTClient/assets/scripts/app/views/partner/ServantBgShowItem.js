var i = require("RenderListItem");
var Initializer = require("Initializer");
var Utils= require("Utils");
var UrlLoad = require("UrlLoad");
var UIUtils = require("UIUtils");
cc.Class({
    extends: i,
    properties: {
        icon: UrlLoad,
        index:0,
    },
    ctor() {
        
    },

    onLoad(){
        // this.node.on(cc.Node.EventType.TOUCH_START, this.onDragStart, this);
        // this.node.on(cc.Node.EventType.TOUCH_MOVE, this.onDrag, this);
        // this.node.on(cc.Node.EventType.TOUCH_END, this.onDragEnd, this);
        // this.node.on(cc.Node.EventType.TOUCH_CANCEL, this.onDragEnd, this);
    },
    showData() {
        var t = this._data;
        if (t) {
            this.icon.url = UIUtils.uiHelps.getPartnerZoneBgImg(t.icon);
            //this.node.opacity = t.isChoose ? 255 : 100;
        }
    },



    setChoose(flag){
        // this._data.isChoose = flag;
        // this.nodezhuangshi.active = flag;
        // this.node.opacity = flag ? 255 : 100;
        // this.node.scaleX = flag ? 1 : 0.7;
        // this.node.scaleY = flag ? 1 : 0.7;
    },
});
