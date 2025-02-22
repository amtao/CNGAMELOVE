var i = require("Utils");
var n = require("UrlLoad");
var l = require("UIUtils");
var r = require("Initializer");
cc.Class({
    extends: cc.Component,
    properties: {
        Image_talent: cc.Label,
        Image_Baby: n,
    },
    ctor() {},
    onLoad() {
        this.Image_Baby.url = l.uiHelps.getBabyBody();
        var t = this.node.openParam;
        if (t) {
            var e = r.sonProxy.getSon(t.id);
            this.Image_talent.string = i18n.t("SON_ZIZHI_" + e.talent);
        }
    },
    onClickYes() {
        i.utils.openPrefabView("child/ChildView");
        i.utils.closeView(this);
    },
    onClickNo() {
        facade.send("CLOSE_XXOO");
        i.utils.closeView(this);
    },
    onClickClose() {
        i.utils.closeView(this);
    },
});
