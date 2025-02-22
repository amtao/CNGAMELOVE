var i = require("RenderListItem");
var n = require("Initializer");
cc.Class({
    extends: i,
    properties: {
        wifeImages: cc.SpriteAtlas,
        wifeTitles: cc.SpriteAtlas,
        wifeD: cc.Node,
        mark: cc.Node,
        roleImage: cc.Sprite,
        nameImage: cc.Sprite,
        txt_love: cc.Label,
        txt_charm: cc.Label,
        txt_info: cc.Label,
        txt_condition: cc.Label,
    },
    ctor() {},
    showData() {
        var t = this._data;
        if (t) {
            var e = n.wifeProxy.wifeMaps.get(t.wid.toString());
            if (e) {
                this.wifeD.active = !0;
                this.mark.active = !1;
                this.txt_charm.string = e.flower.toString();
                this.txt_love.string = e.love.toString();
            } else {
                this.wifeD.active = !1;
                this.mark.active = !0;
                this.txt_info.string = 2 == n.playerProxy.userData.sex ? t.wname2 + "：" + t.info2: t.wname + "：" + t.info;
                var o = "";
                "juqing" == t.from ? (o = i18n.t("WIFE_GET_WAY_JU_QING")) : "xf" == t.from ? (o = i18n.t("WIFE_GET_WAY_XUN_FANG")) : "shili" == t.from ? (o = i18n.t("WIFE_GET_WAY_SHI_LI", {
                    value: t.condition
                })) : "vip" == t.from ? (o = i18n.t("WIFE_GET_WAY_VIP", {
                    value: t.condition
                })) : "huodong" == t.from && (o = i18n.t("WIFE_GET_WAY_HUO_DONG"));
                this.txt_condition.string = o;
            }
        }
    },
});
