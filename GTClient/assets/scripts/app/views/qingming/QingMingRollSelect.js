var i = require("Utils");
var n = require("List");
var l = require("Initializer");
var r = require("TimeProxy");
cc.Class({
    extends: cc.Component,
    properties: {
        list: n,
    },
    ctor() {
        this.curSelect = 0;
    },
    onLoad() {
        var t = this;
        this.list.data = l.qingMingProxy.pointArr;
        this.list.selectHandle = function(e) {
            e && e.id && (t.curSelect = e.id);
        };
    },
    onClickYes() {
        if (0 != this.curSelect) {
            if (0 != l.bagProxy.getItemCount(this.curSelect)) {
                i.utils.closeView(this);
                l.qingMingProxy.sendRoll(this.curSelect, 1);
            } else i.alertUtil.alertItemLimit(this.curSelect);
        } else i.alertUtil.alert18n("QING_MING_SELECT_POINT");
    },
    onClickGoGet() {
        r.funUtils.openView(r.funUtils.continuityrecharge.id);
        i.utils.closeView(this);
    },
    onCliclClose() {
        i.utils.closeView(this);
    },
});
