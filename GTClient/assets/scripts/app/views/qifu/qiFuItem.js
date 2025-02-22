
var initializer = require("Initializer");
var i = require("RenderListItem");
var UrlLoad = require("UrlLoad");
var UIUtils = require("UIUtils");
cc.Class({
    extends: i,

    properties: {
        iconSprite: cc.Sprite,
        iconSpriteFrame: [cc.SpriteFrame],
        lbltitle:cc.Label,
        index:0,
        spbac:UrlLoad,
        nodelbl:cc.Node,
        nodeIcon:cc.Node,
    },

    // LIFE-CYCLE CALLBACKS:
    ctor(){
        this.isMoving = false;
    },

    onLoad () {
        facade.subscribe("RESET_QIFU_ITEM", this.setUnSelectedState, this);
        this.node.on(cc.Node.EventType.TOUCH_START, this.onDragStart, this);
        this.node.on(cc.Node.EventType.TOUCH_MOVE, this.onDrag, this);
        this.node.on(cc.Node.EventType.TOUCH_END, this.onDragEnd, this);
        this.node.on(cc.Node.EventType.TOUCH_CANCEL, this.onDragEnd, this);
    },

    showData() {
        let t = this.data;
        if (t){
            let ischoose = t.idx == this.index;
            this.node.opacity = ischoose ? 255 : 255 * 0.85;
            // this.node.scaleX = ischoose ? 1 : 0.8;
            // this.node.scaleY = ischoose ? 1 : 0.8;          
            this.spbac.url = ischoose ? UIUtils.uiHelps.getQiFuPic("qf_bg_big") : UIUtils.uiHelps.getQiFuPic("qf_bg_small");
            this.spbac.node.x = ischoose ? 0 : 2;
            this.nodelbl.y = ischoose ? 186 : 114;
            this.nodeIcon.y = ischoose ? 42 : 0;
            ischoose ? this.setSelectedState() : this.setUnSelectedState();
        }
    },

    setSelectedState () {
        this.iconSprite.spriteFrame = this.iconSpriteFrame[1];
        this.lbltitle.string = i18n.t("STORY_SELECTED");
    },

    setUnSelectedState () {
        this.iconSprite.spriteFrame = this.iconSpriteFrame[0];
        this.lbltitle.string = i18n.t("COMMON_SELECT");
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
            return;
        }
        this.isMoving = false;
        let endDragPosX = event.currentTouch._point.x;
        if (Math.abs(endDragPosX - this.startDragPosX) > 100){
            (endDragPosX > this.startDragPosX) ? facade.send("WEIYANGGE_MOVERIGHT") : facade.send("WEIYANGGE_MOVELEFT");
        }
        this.startDragPosX = 0;
    },

    // update (dt) {},
});
