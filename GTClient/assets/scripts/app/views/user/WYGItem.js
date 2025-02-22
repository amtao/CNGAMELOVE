var i = require("RenderListItem");
var n = require("Initializer");
var l = require("Utils");
var UrlLoad = require("UrlLoad");
var UIUtils = require("UIUtils");
cc.Class({
    extends: i,
    properties: {
        lblNum: cc.Label,
        spbac:UrlLoad,
        index:0,
        nodeLableName:cc.Node,
    },
    ctor() {
        this.items = null;
    },

    onLoad(){
        this.node.on(cc.Node.EventType.TOUCH_START, this.onDragStart, this);
        this.node.on(cc.Node.EventType.TOUCH_MOVE, this.onDrag, this);
        this.node.on(cc.Node.EventType.TOUCH_END, this.onDragEnd, this);
        this.node.on(cc.Node.EventType.TOUCH_CANCEL, this.onDragEnd, this);
    },
    showData() {
        var t = this._data;
        if (t) {
            this.spbac.url = UIUtils.uiHelps.getUserclothePic("huanzhuang_wyg_" + t.idx);
            this.lblNum.string = i18n.t("USERCLOTHE_SUITTYPE" + t.idx);
            this.node.opacity = t.isChoose ? 255 : 100;
            this.node.scaleX = t.isChoose ? 1 : 0.7;
            this.node.scaleY = t.isChoose ? 1 : 0.7;
            this.nodeLableName.active = t.isChoose;
        }
    },

    /**打开对应套装的类型*/
    onClick() {
        if (this._data == null || !this._data.isChoose) return;
		//l.utils.openPrefabView("user/UserSuitDetail", !1, { data: this._data });
        l.utils.openPrefabView("user/UserSuitListView", !1, { idx: this._data.idx });
    },

    onDragStart: function(event) {
        this.isMoving = false;
        this.startDragPosX = event.currentTouch._point.x;
    },

    onDrag: function(event) {
        this.isMoving = true;
    },

    onDragEnd: function(event) {
        if(!this.isMoving) {
            //this.onClick();
            return;
        }
        this.isMoving = false;
        let endDragPosX = event.currentTouch._point.x;
        if (Math.abs(endDragPosX - this.startDragPosX) > 100){
            (endDragPosX > this.startDragPosX) ? facade.send("WEIYANGGE_MOVERIGHT") : facade.send("WEIYANGGE_MOVELEFT");
        }
        this.startDragPosX = 0;
    },

    setChoose(flag){
        this._data.isChoose = flag;
        this.node.opacity = flag ? 255 : 100;
        this.node.scaleX = flag ? 1 : 0.7;
        this.node.scaleY = flag ? 1 : 0.7;
        this.nodeLableName.active = flag;
    },
});
