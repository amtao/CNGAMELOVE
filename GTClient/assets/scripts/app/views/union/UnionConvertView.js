var i = require("List");
var n = require("Utils");
var l = require("Initializer");
cc.Class({
    extends: cc.Component,
    properties: {
        lblGx: cc.Label,
        list: i,
    },
    ctor() {},
    onLoad() {
        facade.subscribe("UNION_SHOP_UPDATE", this.onDataUodate, this);
        this.onDataUodate();
    },
    onDataUodate() {
        this.UPDATE_SHOP_LIST();
        this.UPDATE_MEMBER_INFO();
    },
    eventClose() {
        n.utils.closeView(this);
    },
    onClickConver(t, e) {
        var o = e.data;
        if (o) {
            if (l.unionProxy.memberInfo.leftgx < o.payGX) {
                n.alertUtil.alert(i18n.t("union_gx_limit"));
                return;
            }
            l.unionProxy.sendCovert(o.id);
        }
    },
    UPDATE_SHOP_LIST() {
        this.list.data = l.unionProxy.shopList.sort(function(t, e) {
            return t.lock - e.lock;
        });
    },
    UPDATE_MEMBER_INFO() {
        var t = l.unionProxy.memberInfo;
        t && (this.lblGx.string = i18n.t("UNION_CUR_GONG_XIAN") + t.leftgx);
    },
});
