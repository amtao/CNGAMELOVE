var i = require("List");
cc.Class({
    extends: cc.Component,
    properties: {
        itemList: i,
        flower: cc.Node,
        lblNum: cc.Label,
    },
    ctor() {
        this._total = 0;
        this._value = 0;
    },
    onLoad() {},
    setValue(t, e) {
        if (e > 5) this.lblNum.string = i18n.t("SERVANT_JI_BAN_FLOWER", {
            num: e
        });
        else {
            this._total = t;
            this._value = e;
            var o = [];
            if (this._total > 0) for (var i = 0; i < this._total; i++) {
                var n = {
                    isOpen: i < this._value
                };
                o.push(n);
            }
            this.itemList.data = o;
        }
        this.itemList.node.active = e <= 5;
        this.flower.active = e > 5;
    },
});
