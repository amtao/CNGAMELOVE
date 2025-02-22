var i = require("Initializer");
var n = require("UrlLoad");
var l = require("Utils");
var r = require("UIUtils");
var a = require("TimeProxy");
cc.Class({
    extends: cc.Component,
    properties: {
        lblTitle_1:cc.Label,
        lblTitle_2:cc.Label,
        lblCur:cc.Label,
        lblTotal:cc.Label,
        lblDes:cc.Label,
        btnNode:cc.Node,
        btnLeft:cc.Node,
        btnRight:cc.Node,
        loadIcon:cc.Node,
        iconUrl:n,
    },

    ctor(){
        this.curIndex = 0;
    },

    onLoad() {
        r.uiUtils.scaleRepeat(this.btnLeft, 0.9, 1.2);
        r.uiUtils.scaleRepeat(this.btnRight, 0.9, 1.2);
        facade.subscribe("UI_TOUCH_MOVE_LEFT", this.onClickLeft, this);
        facade.subscribe("UI_TOUCH_MOVE_RIGHT", this.onClickRight, this);
        i.timeProxy.getActivityNotice() && this.showNotice();
    },
    showNotice() {
        var t = this,
        e = i.timeProxy.getActivityNotice(),
        o = e[this.curIndex];
        if (o) {
            cc.resources.load(o.pictureAddress, cc.SpriteFrame,
            (o, i)=> {
                if (null == o && null != i) {
                    MemoryMgr.saveAssets(i);
                    this.iconUrl.getComponent(cc.Sprite).spriteFrame = i;
                }
            })
            // this.iconUrl.url = "https://zjfhkorea-test-1251697691.cos.ap-shanghai.myqcloud.com/activity1/activity1.png";
            // this.iconUrl.loadHandle = function() {
            //     // t.loadIcon.active = !1;
            // };
            this.lblTitle_1.string = o.title1;
            this.lblTitle_2.string = o.title2;
            this.lblCur.string = this.curIndex + 1 + "";
            this.lblTotal.string = e.length + "";
            this.lblDes.string = o.cuntent;
            this.btnLeft.active = this.curIndex > 0;
            this.btnRight.active = this.curIndex < e.length - 1;
        }
    },
    onClickLeft() {
        if (this.curIndex > 0) {
            this.curIndex--;
            this.showNotice();
        }
    },
    onClickRight() {
        if (this.curIndex < i.timeProxy.getActivityNotice().length - 1) {
            this.curIndex++;
            this.showNotice();
        }
    },
    onClickGo() {
        var t = i.timeProxy.getActivityNotice()[this.curIndex];
        // 1 == t.isOpenUrl ? cc.sys.openURL(t.openUrl) : a.funUtils.openView(t.iconOpenID);
        this.onClickClose();
    },
    onClickClose() {
        l.utils.closeView(this);
        i.flowerProxy.showAutoShow();
    },
});
