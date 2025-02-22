var i = require("List");
var n = require("Initializer");
var l = require("UIUtils");
var r = require("Utils");
var a = require("ChildSpine");
cc.Class({
    extends: cc.Component,
    properties: {
        lblName:cc.Label,
        lblFather:cc.Label,
        lblShenFen:cc.Label,
        lblShuXing:cc.Label,
        lblTime:cc.Label,
        list:i,
        chidSpine:a,
        tipNode:cc.Node,
    },

    ctor(){
        this.curData = null;
    },
    onLoad() {
        facade.subscribe("UPDATE_SON_ZHAO_QIN", this.onRefreshData, this);
        this.curData = this.node.openParam;
        if (this.curData) {
            n.sonProxy.sendShuaXinZQ(this.curData.id, this.curData.honor);
            this.lblName.string = this.curData.name;
            localcache.getItem(localdb.table_wife, this.curData.mom);
            this.lblFather.string = n.playerProxy.userData.name;
            this.lblShenFen.string = n.sonProxy.getHonourStr(this.curData.honor);
            var t = this.curData.ep.e1 + this.curData.ep.e2 + this.curData.ep.e3 + this.curData.ep.e4;
            this.lblShuXing.string = t + "";
            this.chidSpine.setKid(this.curData.id, this.curData.sex);
            localcache.getItem(localdb.table_adult, this.curData.honor);
        }
        n.sonProxy.tiQinObj.marryType = 1;
        this.onRefreshData();
    },
    onRefreshData() {
        if (n.sonProxy.zhaoQinData) {
            this.tipNode.active = 0 == n.sonProxy.zhaoQinData.list.length;
            this.list.data = n.sonProxy.zhaoQinData.list;
            l.uiUtils.countDown(n.sonProxy.zhaoQinData.otime.next, this.lblTime,
            function() {
                n.playerProxy.sendAdok(n.sonProxy.zhaoQinData.otime.label);
            });
        }
    },
    onClickRefresh() {
        var t = this;
        r.utils.showConfirmItem(i18n.t("MARRY_REFERSH_QUICKLY", {
            num: 100
        }), 1, n.playerProxy.userData.cash,
        function() {
            n.playerProxy.userData.cash >= 100 ? n.sonProxy.sendRefreshZhaoQin(t.curData.id, t.curData.honor) : r.alertUtil.alertItemLimit(1);
        },
        "MARRY_REFERSH_QUICKLY");
    },
    onClickClose() {
        r.utils.closeView(this);
    },
});
