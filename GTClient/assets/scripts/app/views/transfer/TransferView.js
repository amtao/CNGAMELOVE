var i = require("List");
var n = require("Utils");
var l = require("Initializer");
cc.Class({
    extends: cc.Component,
    properties: {
        //unionWeath: cc.Label,
        list: i,
        lbString:cc.Node,
    },
    ctor() {},
    onLoad() {
        this.UPDATE_SEARCH_INFO();
        facade.subscribe("UPDATE_SEARCH_INFO", this.UPDATE_SEARCH_INFO, this);
        facade.subscribe("UPDATE_TRANS_LIST", this.UPDATE_TRANS_LIST, this);
        l.unionProxy.sendTranList();
    },
    eventClose() {
        n.utils.closeView(this);
    },
    onClickTran(t, e) {
        var o = e.data;
        n.utils.openPrefabView("union/TransferWind",null,o);
        this.eventClose()
        //openParam
        // if (o) {
        //     l.unionProxy.dialogParam = {
        //         type: "tran",
        //         id: o.id
        //     };
        //     l.unionProxy.sendTran("", o.id);
        //     n.utils.closeView(this);
        // }
    },
    UPDATE_SEARCH_INFO() {
        //this.unionWeath.string = l.unionProxy.clubInfo.fund + "";
        this.UPDATE_TRANS_LIST();
    },
    UPDATE_TRANS_LIST() {
        let ar = []
        let memberlist = l.unionProxy.clubInfo?l.unionProxy.clubInfo.members:[]
        memberlist = memberlist?memberlist:[];
        let len = memberlist.length
        for (let i = 0; i < len; i++) {
            if(memberlist[i].post === 2){
                ar.push(memberlist[i])
            }
        }
        this.list.data = ar
        this.lbString.active = ar.length === 0
    },
});
