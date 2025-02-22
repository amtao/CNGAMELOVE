var i = require("Utils");
var n = require("List");
var l = require("Initializer");
var r = require("UrlLoad");
var a = require("UIUtils");
cc.Class({
    extends: cc.Component,
    properties: {
        list:n,
        rwd:r,
        itemRwds:n,
        lbl:r,
        btn:cc.Button,
    },

    ctor(){

        this.curItem = null;
    },
    onLoad() {
        l.limitActivityProxy.sendLookActivityData(l.limitActivityProxy.SEVEN_DAY_ID);
        facade.subscribe(l.limitActivityProxy.UPDATE_LIMIT_ACTIVE_SEVEN, this.onUpdateSHow, this);
        this.onUpdateSHow();
    },
    onUpdateSHow() {
        this.curItem = null;
        this.list.data = l.limitActivityProxy.sevenSign ? l.limitActivityProxy.sevenSign.level: [];
        if (l.limitActivityProxy.sevenSign) {
            for (var t = l.limitActivityProxy.sevenSign.level.length,
            e = 0; e < t; e++) {
                var o = l.limitActivityProxy.sevenSign.level[e];
                if (2 != o.type) {
                    this.curItem = o;
                    break;
                }
            }
            null == this.curItem && (this.curItem = l.limitActivityProxy.sevenSign.level[t - 1]);
        }
        this.showItem(this.curItem);
    },
    showItem(t) {
        if (t) {
            this.curItem = t;
            this.rwd.url = a.uiHelps.getSevenDay(t.day);
            this.itemRwds.data = l.limitActivityProxy.sevenSign.rwd[t.day - 1].items;
            this.lbl.url = a.uiHelps.getSevenDayLbl(t.day);
            this.btn.interactable = 1 == t.type;
            this.btn.node.active = 2 != t.type;
        }
    },
    onClickItem(t, e) {
        var o = e.data;
        this.showItem(o);
    },
    onClickRwd() {
        this.curItem && 1 != this.curItem.type ? 0 == this.curItem.type ? i.alertUtil.alert18n("SEVEN_DAY_RWD_LIMIT") : 2 == this.curItem.type && i.alertUtil.alert18n("SEVEN_DAY_RWDED") : l.limitActivityProxy.sendSevenRwd(this.curItem.day);
    },
    onClickClost() {
        i.utils.closeView(this);
    },
});
