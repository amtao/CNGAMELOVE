var i = require("Utils");
cc.Class({
    extends: cc.Component,
    properties: {
        btnChenLu: cc.Button,
        btnYuanBao: cc.Button,
    },
    ctor() {},
    onLoad() {},
    onChenLu() {
        i.utils.openPrefabView("flower/FlowerProtect", !1, {
            type: 1
        });
        i.utils.closeView(this);
    },
    onYuanBao() {
        i.utils.openPrefabView("flower/FlowerProtect", !1, {
            type: 2
        });
        i.utils.closeView(this);
    },
    onCloseSelect() {
        i.utils.closeView(this);
    },
});
