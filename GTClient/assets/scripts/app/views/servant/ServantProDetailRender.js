var i = require("RenderListItem");
var n = require("UrlLoad");
var l = require("UIUtils");
cc.Class({
    extends: i,
    properties: {
        lblTotal: cc.Label,
        lblZz: cc.Label,
        lblZj: cc.Label,
        lblDan: cc.Label,
        lblJb: cc.Label,
        lblClothe: cc.Label,
        lblLep: cc.Label,
        bg: n,
    },
    ctor() {},
    showData() {
        var t = this._data,
        e = i18n.t("COMMON_PROP" + t.type);
        this.lblTotal.string = i18n.t("SERVANT_SHU_XING_MING", {
            name: e,
            num: t.pro
        });
        this.lblZz.string = i18n.t("SERVANT_ZI_ZHI_JIA_CHENG", {
            num: t.zzAdd
        });
        this.lblZj.string = i18n.t("SERVANT_ZHI_JI_JI_NENG", {
            num: t.wifeAdd ? t.wifeAdd: 0
        });
        this.lblDan.string = i18n.t("SERVANT_DAN_YAO_JIA_CHENG", {
            num: t.dan ? t.dan: 0
        });
        this.lblJb.string = i18n.t("SERVNAT_JI_BAN_JIA_CHENG", {
            num: t.jiBan ? t.jiBan: 0
        });
        this.lblClothe.string = i18n.t("SERVANT_CLOTHE_JIA_CHENG", {
            num: t.clothe ? t.clothe: 0
        });
        this.lblLep.string = i18n.t("SERVANT_LEADER_JIA_CHENG", {
            num: t.lep ? t.lep: 0
        });
        1 == t.type ? (this.lblTotal.node.color = cc.color(202, 101, 105)) : 2 == t.type ? (this.lblTotal.node.color = cc.color(101, 158, 202)) : 3 == t.type ? (this.lblTotal.node.color = cc.color(217, 131, 30)) : 4 == t.type && (this.lblTotal.node.color = cc.color(137, 101, 202));
        this.bg.url = l.uiHelps.getDeatilBg(t.type);
    },
});
