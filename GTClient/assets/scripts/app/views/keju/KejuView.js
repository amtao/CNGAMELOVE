var i = require("KejuItem");
var n = require("Initializer");
cc.Class({
    extends: cc.Component,
    properties: {
        items: [i],
        lblLv: cc.Label,
        lblExp: cc.Label,
        prg: cc.ProgressBar,
    },
    ctor() {},
    onLoad() {
        facade.subscribe(n.achievementProxy.UPDATE_KEJU_LEVEL, this.updateLevel, this);
        facade.subscribe(n.achievementProxy.UPDATE_KEJU, this.updateShow, this);
        facade.subscribe(n.achievementProxy.UPDATE_SCORE, this.updateShow, this);
        this.updateLevel();
        this.updateShow();
    },
    updateLevel() {
        var t = n.achievementProxy.level.level,
        e = n.achievementProxy.level.exp,
        o = localcache.getItem(localdb.table_exam_lv, t),
        i = o ? o.exp: 1;
        this.lblLv.string = i18n.t("COMMON_LEVEL_TIP", {
            d: t,
            n: o ? o.name: ""
        });
        this.lblExp.string = null == o || 0 == i ? i18n.t("COMMON_MAX") : i18n.t("COMMON_NUM", {
            f: e,
            s: i
        });
        this.prg.progress = null == o || 0 == i ? 1 : e / i;
    },
    updateShow() {
        for (var t = localcache.getList(localdb.table_exam_type), e = 0; e < this.items.length; e++) this.items[e].data = t[e];
    },
});
