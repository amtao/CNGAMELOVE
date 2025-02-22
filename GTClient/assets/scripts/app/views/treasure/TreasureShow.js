var i = require("UrlLoad");
var n = require("Utils");
var l = require("ApiUtils");
var r = require("UIUtils");
var a = require("ItemSlotUI");
var s = require("Initializer");
var c = require("ShaderUtils");
var _ = require("List");
cc.Class({
    extends: cc.Component,
    properties: {
        bg: i,
        lblName: cc.Label,
        lblDes: cc.Label,
        lblGroup: cc.Label,
        nodeLeft: cc.Node,
        nodeRight: cc.Node,
        itemSlot: a,
        lblCount: cc.Label,
        nodeUp: cc.Node,
        ndoeFind: cc.Node,
        lblScore: cc.Label,
        list: _,
        nodeUnlock: cc.Node,
    },
    ctor() {
        this.curindex = 0;
        this.curGroup = null;
        this.maxIndex = 0;
        this.tresSprite = null;
    },
    onLoad() {
        this.curGroup = this.node.openParam;
        this.maxIndex = this.curGroup.items.length - 1;
        this.lblGroup.string = this.curGroup && this.curGroup.data ? this.curGroup.data.name: "";
        for (var t = 0; t < this.curGroup.items.length; t++) if (this.curGroup.data.photo == this.curGroup.items[t].id) {
            this.curindex = t;
            break;
        }
        this.updateShow();
        facade.subscribe("UI_TOUCH_MOVE_LEFT", this.onMoveLeft, this);
        facade.subscribe("UI_TOUCH_MOVE_RIGHT", this.onMoveRight, this);
        facade.subscribe(s.bagProxy.UPDATE_BAG_ITEM, this.updateShow, this);
        r.uiUtils.scaleRepeat(this.nodeRight, 0.95, 1.05);
        r.uiUtils.scaleRepeat(this.nodeLeft, 0.95, 1.05);
    },
    updateShow() {
        var t = this.curGroup.items[this.curindex];
        this.bg.url = r.uiHelps.getTreasure(t.data.phot);
        null == this.tresSprite && (this.tresSprite = this.bg.node.getComponent(cc.Sprite));
        c.shaderUtils.setImageGray(this.tresSprite, 1 != t.rwd);
        this.ndoeFind.active = this.nodeUp.active = !1;
        var e = s.bagProxy.getItemCount(t.data.itemid);
        if (e > 0) {
            this.nodeUp.active = !0;
            this.itemSlot.data = {
                id: t.data.itemid
            };
            this.lblCount.string = i18n.t("COMMON_NUM", {
                f: e,
                s: 1
            });
        } else {
            this.ndoeFind.active = !0;
            e = s.bagProxy.getItemCount(t.data.tagid);
            this.itemSlot.data = {
                id: t.data.tagid
            };
            this.lblCount.string = i18n.t("COMMON_NUM", {
                f: e,
                s: t.data.tagnum
            });
        }
        this.lblName.string = t.data.name;
        this.lblDes.string = t.data.text;
        this.nodeUnlock.active = 1 == t.rwd;
        this.list.data = 1 == t.rwd ? t.data.tworeward: t.data.reward;
        this.list.node.x = this.lblScore.node.x - this.list.node.width;
        this.lblScore.string = i18n.t("TREASURE_SCORE_ADD", {
            v: 1 == t.rwd ? t.data.twopoints: t.data.points
        });
    },
    onClickTrun(t, e) {
        var o = parseInt(e);
        this.curindex += o;
        this.curindex = this.curindex < 0 ? this.maxIndex: this.curindex;
        this.curindex = this.curindex > this.maxIndex ? 0 : this.curindex;
        this.updateShow();
    },
    onClickClost() {
        n.utils.closeView(this);
        n.utils.closeNameView("treasure/TreasureView");
    },
    onClickBack() {
        n.utils.closeView(this);
    },
    onClickShare() {
        cc.sys.isNative ? l.apiUtils.share_game("treasure") : n.alertUtil.alert18n("TREASURE_SHARE_LIMIT");
    },
    onClickUp() {
        var t = this.curGroup.items[this.curindex];
        s.bagProxy.getItemCount(t.data.itemid) < 1 ? n.alertUtil.alertItemLimit(t.data.itemid) : s.treasureProxy.sendTreasure(t.id);
    },
    onClickClip() {
        var t = this.curGroup.items[this.curindex];
        s.bagProxy.getItemCount(t.data.tagid) < t.data.tagnum ? n.alertUtil.alertItemLimit(t.data.tagid) : s.treasureProxy.sendClipTrea(t.id);
    },
    onMoveLeft() {
        this.onClickTrun(null, -1);
    },
    onMoveRight() {
        this.onClickTrun(null, 1);
    },
    onClickReward() {},
});
