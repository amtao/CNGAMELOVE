
var n = require("Utils");
var l = require("Initializer");
cc.Class({
    extends: cc.Component,
    properties: {
        money: cc.Label,
    },
    ctor() {},
    onLoad() {
        console.log(this.node.openParam)
        console.log()
        let ta = localcache.getList("club_param")
        this.needfund = parseInt(ta[1].param)
        this.money.string = this.needfund + ""
    },
    eventClose() {
        n.utils.closeView(this);
    },
    onClickTran(t, e) {
        var t = l.unionProxy.clubInfo;
        if (t && t.fund >= this.needfund) {
            l.unionProxy.dialogParam = {
                type: "tran",
                id: this.node.openParam.id
            };
            l.unionProxy.sendTran("", this.node.openParam.id);
            this.eventClose()
            return
        }
        n.alertUtil.alert18n("UNION_MANAGER_FUNTNE")
        this.eventClose()

    },
});
