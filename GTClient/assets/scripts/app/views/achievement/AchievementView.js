var i = require("List");
var n = require("Initializer");
var l = require("Utils");
var r = require("RedDot");
cc.Class({
    extends: cc.Component,
    properties: {
        list: i,
        nodes: [cc.Node],
        lblName: cc.Label,
        lblTitles: [cc.Label],
        seColor: cc.Color,
        norColor: cc.Color,
        selectImg: cc.SpriteFrame,
        kejuSelectImg: cc.SpriteFrame,
        dailyImg: cc.Sprite,
        //kejuImg: cc.Sprite,
        chengjiuImg: cc.Sprite,
    },
    ctor() {},
    onLoad() {
        this.UPDATE_ACHIEVE();
        facade.subscribe(n.achievementProxy.UPDATE_ACHIEVE, this.UPDATE_ACHIEVE, this);
        facade.subscribe("UI_TOUCH_MOVE_LEFT", this.onClickClose, this);
        var t;
        t = !r._MAP.achieve || r._MAP.dailyrwd || r._MAP.dailytask ? 2 : 1;
        this.onClickTab(null, t);
    },
    UPDATE_ACHIEVE() {
        this.list.data = n.achievementProxy.achieveList;
    },
    onClickClose() {
        l.utils.closeView(this, !0);
    },
    onClickTab(t, e) {
        var o = parseInt(e) - 1;
        this.lblName.string = i18n.t(0 == o ? "ACHIEVE_TIP": 1 == o ? "ACHIEVE_DAILY_TIP": "KEJU_TIP");
        for (var i = 0; i < this.nodes.length; i++) {
            this.nodes[i].active = o == i;
            this.lblTitles[i].node.color = o == i ? this.seColor: this.norColor;
        }
        this.chengjiuImg.node.active = 0 == o;
        this.dailyImg.node.active = 1 == o;
        // if (2 == o) {
        //     this.kejuImg.spriteFrame = this.kejuSelectImg;
        //     this.kejuImg.node.active = true;
        // }else {
        //     this.kejuImg.node.active = false;
        // }

        // this.kejuImg.spriteFrame = 2 == o ? this.kejuSelectImg: null;

    },
});
