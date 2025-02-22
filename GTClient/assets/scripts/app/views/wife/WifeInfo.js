var i = require("JiBanShow");
var n = require("UrlLoad");
var l = require("Utils");
var r = require("Initializer");
var a = require("UIUtils");
cc.Class({
    extends: cc.Component,
    properties: {
        lblName:cc.Label,
        lblTxt:cc.Label,
        luckImg:i,
        lblJiBan:cc.Label,
        iconArr:[cc.SpriteFrame],
        roleImg:n,
        icon_1:cc.Sprite,
        lblhuoqu:cc.Label,
        lblTitle:cc.Label,
    },
    onLoad() {
        facade.subscribe("UPDATE_WIFE_JB", this.onWifeJb, this);
        this.onWifeJb();
    },
    onWifeJb() {
        var t = this.node.openParam;
        if (t) {
            this.lblName.string = t.wname2;
            var e = t.info2.split("|");
            this.lblTxt.string = e.length > 1 ? e[0] + e[1] : e[0];
            var o = r.jibanProxy.getWifeJbLv(t.wid).level % 1e3;
            this.luckImg.setValue(5, o);
            this.icon_1.spriteFrame = this.iconArr[t.type - 1];
            this.roleImg.url = a.uiHelps.getWifeSmallBody(t.res);
            var i = r.jibanProxy.getWifeNextJb(o),
            n = r.jibanProxy.getWifeJB(t.wid);
            this.lblJiBan.string = i18n.t("COMMON_NUM", {
                f: n,
                s: i ? i.yoke + "": "5000"
            });
            this.lblhuoqu.string = t.unlock;
            var l = r.wifeProxy.getWifeData(t.wid);
            this.lblTitle.string = l ? i18n.t("WIFE_WEI_YI_JIE_SHI") : i18n.t("WIFE_WEI_JIE_SHI");
        }
    },
    onClickClose() {
        l.utils.closeView(this);
    },
    onClickGift() {
        var t = this.node.openParam;
        r.wifeProxy.wifeGiftId = t.wid;
        l.utils.openPrefabView("wife/GiftView");
    },
});
