var i = require("Utils");
var n = require("TreasureGroups");
var l = require("Initializer");
var r = require("ShaderUtils");
var a = require("UIUtils");
var s = require("TimeProxy");
cc.Class({
    extends: cc.Component,
    properties: {
        groups: n,
        clear: cc.Sprite,
        nodeLeft: cc.Node,
        ndoeRight: cc.Node,
        lblScore: cc.Label,
        lblAdd: cc.Label,
    },
    ctor() {
        this.curIndex = 0;
        this.maxIndex = 0;
    },
    onLoad() {
        this.maxIndex = Math.ceil(l.treasureProxy.tGroupList.length / this.groups.items.length) - 1;
        this.updateClear();
        this.showCur();
        facade.subscribe(l.treasureProxy.UPDATE_TREASURE_TREASURE, this.showCur, this);
        facade.subscribe(l.treasureProxy.UPDATE_TREASURE_CLEAR, this.updateClear, this);
        facade.subscribe("UI_TOUCH_MOVE_LEFT", this.onMoveLeft, this);
        facade.subscribe("UI_TOUCH_MOVE_RIGHT", this.onMoveRight, this);
        this.ndoeRight.active = this.nodeLeft.active = l.treasureProxy.tGroupList.length > this.groups.items.length;
        a.uiUtils.scaleRepeat(this.ndoeRight, 0.95, 1.05);
        a.uiUtils.scaleRepeat(this.nodeLeft, 0.95, 1.05);
        this.checkOpenGet();
    },
    getAdd() {
        var t = l.treasureProxy.treasure ? l.treasureProxy.treasure.length: 1,
        e = localcache.getItem(localdb.table_treasureDay, t);
        return e ? e.reward[0].count: 0;
    },
    checkOpenGet() {
        for (var t = !1,
        e = i.utils.getParamInt("treasure_com_item"), o = 0; o < l.treasureProxy.tGroupList.length; o++) {
            for (var n = l.treasureProxy.tGroupList[o], r = 0, a = 0; a < n.items.length; a++) {
                var s = n.items[a];
                if (l.bagProxy.getItemCount(s.data.itemid) > 0) {
                    t = !0;
                    break;
                }
                if (l.bagProxy.getItemCount(s.data.tagid) >= s.data.tagnum && (0 == s.rwd || s.data.tagid != e)) {
                    t = !0;
                    break;
                }
                r += 1 == s.rwd ? 1 : 0;
            }
            r >= n.items.length && 0 == n.rwd && (t = !0);
        }
        t && i.utils.openPrefabView("treasure/TreasureGet", !0);
    },
    onMoveLeft() {
        this.onClickShow(null, -1);
    },
    onMoveRight() {
        this.onClickShow(null, 1);
    },
    onClickShow(t, e) {
        var o = parseInt(e);
        if (0 != this.curIndex || -1 != o) {
            this.curIndex += o;
            this.curIndex = this.curIndex < 0 ? this.maxIndex: this.curIndex;
            this.curIndex = this.curIndex > this.maxIndex ? 0 : this.curIndex;
            this.showCur();
        } else this.onClickClost();
    },
    showCur() {
        for (var t = [], e = this.groups.items.length, o = 0; o < e; o++) {
            var n = this.curIndex * e + o;
            l.treasureProxy.tGroupList.length > n && t.push(l.treasureProxy.tGroupList[n]);
        }
        this.lblAdd.string = i18n.t("TREASURE_CLEAR_TIP", {
            v: i.utils.formatMoney(this.getAdd())
        });
        this.groups.data = t;
    },
    updateClear() {
        r.shaderUtils.setImageGray(this.clear, l.treasureProxy.isClear);
        this.lblScore.string = l.treasureProxy.score + "";
    },
    onClickClost() {
        i.utils.closeView(this, !0);
    },
    onClickClear() {
        0 != l.treasureProxy.treasure.length ? l.treasureProxy.isClear || l.treasureProxy.sendClear() : i.alertUtil.alert18n("TREASURE_CLEAR_LIMIT");
    },
    onClickTidy() {
        s.funUtils.openView(s.funUtils.treasureTidy.id);
    },
    onClickRank() {
        l.treasureProxy.sendRank();
    },
});
