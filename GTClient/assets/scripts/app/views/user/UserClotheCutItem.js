var i = require("RenderListItem");
var n = require("UrlLoad");
var UIUtils = require("UIUtils");
var Initializer = require("Initializer");

cc.Class({
    extends: i,
    properties: {
        lblName: cc.Label,
        icon: n,
        upLine:cc.Node,
        downLine:cc.Node,
        nodePoint:cc.Node,
    },
    ctor() {},

    onLoad() {
        this.icon.node.on(cc.Node.EventType.TOUCH_START, this.onDragStart, this);
        this.icon.node.on(cc.Node.EventType.TOUCH_END, this.onDragEnd, this);
        this.icon.node.on(cc.Node.EventType.TOUCH_CANCEL, this.onDragEnd, this);
    },

    showData() {
        var t = this._data;
        if (t) {
            let idx = t.idx;
            this.upLine.active = false;
            this.downLine.active = false;
            if (idx % 2 == 1){
                this.nodePoint.y = 0;
                this.upLine.active = !t.isEnd;
            }
            else{
                this.nodePoint.y = 70;
                this.downLine.active = !t.isEnd;
            }          
            this.lblName.node.active = t.cfg.isSpecial == 1;
            if (t.cfg.isSpecial == 1){
                this.lblName.string = Initializer.clotheProxy.getCutClotheLevelDes(t.cfg.type,t.cfg.rwd);
            }
            this.icon.node.setScale(1)
            if (t.curLevel > t.cfg.lv){
                this.icon.url = t.cfg.isSpecial == 1 ? UIUtils.uiHelps.getUserclothePic("sz_yx5") : UIUtils.uiHelps.getUserclothePic("sz_yx4");
            }
            else if(t.curLevel == t.cfg.lv){
                this.icon.url = UIUtils.uiHelps.getUserclothePic("sz_yx1");
            }
            else{
                this.icon.url = UIUtils.uiHelps.getUserclothePic("sz_yx3");
                if (t.cfg.isSpecial == 0){
                    this.icon.node.setScale(0.8);
                }
            }
        }

    },

    onDragStart: function(event) {
        if (this._data == null) return;
        let t = this._data.cfg
        let str = Initializer.clotheProxy.getCutClotheLevelDes(t.type,t.rwd,this._data.curLevel >= t.lv);
        let pos = this.icon.node.convertToWorldSpaceAR(cc.Vec2.ZERO)
        let isDark = this._data.curLevel < t.lv;
        facade.send("SHOW_CARD_CUT_PROP_TIPS",{str:str,pos:pos,isDark:isDark});
    },


    onDragEnd: function(event) {
        facade.send("HIDE_CARD_CUT_PROP_TIPS");
    },
});
