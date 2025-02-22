var i = require("Utils");
var n = require("List");
cc.Class({
    extends: cc.Component,
    properties: {
        lblPro: cc.Label,
        list: n,
    },
    ctor() {},
    onLoad() {
        var t = this.node.openParam;
        if (t) {
            for (var e = t.aep.e1 + t.aep.e2 + t.aep.e3 + t.aep.e4,
            o = [], i = 1; i < 5; i++) {
                var n = {};
                n.type = i;
                n.pro = t.aep["e" + i];
                n.zzAdd = t.zep["e" + i];
                n.wifeAdd = t.wep["e" + i];
                n.dan = t.hep["e" + i];
                n.jiBan = t.jep ? t.jep["e" + i] : 0;
                let depValue = t.dep ? t.dep["e" + i] : 0;
                n.clothe = t.cep ? (t.cep["e" + i]+depValue) : (0+depValue);
                n.lep = t.lep ? t.lep["e" + i] : 0;
                o.push(n);
            }
            this.lblPro.string = i18n.t("SERVANT_ZONG_HE_SHU_XING", {
                num: e
            });
            this.list.data = o;
        }
    },
    onClickClose() {
        i.utils.closeView(this);
    },
});
