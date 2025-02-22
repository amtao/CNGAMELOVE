var i = require("RenderListItem");
var n = require("Initializer");
cc.Class({
    extends: i,
    properties: {
        btn_lvUp: cc.Button,
        txt_name: cc.Label,
        txt_nowState: cc.Label,
        txt_upState: cc.Label,
        txt_lvUpNeed: cc.Label,
        lbljichu: cc.Label,
    },
    ctor() {
        this.skillId = 0;
    },
    showData() {
        var t = this._data,
        e = localcache.getItem(localdb.table_wifeSkill, t.id);
        this.skillId = t.id;
        if (e) {
            this.btn_lvUp && (this.btn_lvUp.node.active = !0);
            this.txt_name.string = e.wsname;
            var o = 1 == e.type ? 300 : 200,
            i = localcache.getItem(localdb.table_hero, e.heroid);
            if (t.level > 0) {
                this.txt_name.string = i18n.t("WIFE_SKILL_NAME", {
                    wsid: e.wsid,
                    name: e.wsname,
                    level: t.level
                });
                this.txt_lvUpNeed.string = i18n.t("WIFE_SKILL_UP_NEED", {
                    exp: t.exp
                });
                this.txt_nowState.string = i18n.t("WIFE_CUR_EFFECT", {
                    name1: i.name,
                    name2: this.getEpStr(e, t.level)
                });
                if (t.level >= o) {
                    this.txt_name.string = i18n.t("WIFE_SKILL_MAX_LEVEL", {
                        name: e.wsname
                    });
                    this.txt_upState.node.active = !1;
                    this.btn_lvUp && (this.btn_lvUp.node.active = !1);
                    this.txt_lvUpNeed.node.active = !1;
                } else this.txt_upState.string = i18n.t("WIFE_SKILL_NEXT_EFFECT", {
                    name1: i.name,
                    name2: this.getEpStr(e, t.level + 1)
                });
                var l = n.servantProxy.getHeroData(i.heroid);
                if (l && 2 == e.type) {
                    var r = e.epid[0];
                    this.lbljichu.string = 5 == r ? "": i18n.t("WIFE_SKILL_PROP_ADD_3", {
                        str: i18n.t("COMMON_PROP" + r),
                        num: l.zep["e" + r]
                    });
                }
            } else {
                this.txt_lvUpNeed.node.active = !1;
                this.btn_lvUp && (this.btn_lvUp.node.active = !1);
                this.txt_name.string = e.wsid + "." + e.wsname;
                this.txt_upState.string = i18n.t("WIFE_SKILL_LOCK", {
                    value: e.love
                });
                this.txt_nowState.string = i18n.t("WIFE_SKILL_LOCK_EFFECT", {
                    name1: i.name,
                    name2: this.getEpStr(e, 1)
                });
            }
        }
    },
    onUpClick() {
        n.wifeProxy.sendUpSkill(n.wifeProxy.curSelectWife.id, this.skillId);
    },
    getEpStr(t, e) {
        for (var o = "",
        i = 0; i < t.epid.length; i++) {
            var l = i18n.t("COMMON_PROP" + t.epid[i]);
            o = "" == o ? l: i18n.t("COMMON_CONTEXT_NUM", {
                c: o,
                n: l
            });
        }
        var r = n.jibanProxy.getWifeJbLv(n.wifeProxy.skillWifeId),
        a = 1 + r.prop / 1e4,
        s = null;
        if (1 == t.type) if (5 == t.epid[0]) for (var c = 0; c < 4; c++) s += Math.ceil(t.base * e * a);
        else s = Math.ceil(t.base * e * a);
        else s = ((t.base / 100) * e * (1 + r.prop / 1e4)).toFixed(1) + "%";
        return 1 == t.type ? i18n.t("WIFE_SKILL_PROP_ADD", {
            str: o,
            value: s
        }) : i18n.t("WIFE_SKILL_PROP_ADD_2", {
            str: o,
            value: s
        });
    },
});
