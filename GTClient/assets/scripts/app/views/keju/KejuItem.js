var i = require("RenderListItem");
var n = require("Initializer");
var l = require("ShaderUtils");
var r = require("Utils");
cc.Class({
    extends: i,
    properties: {
        lblName: cc.Label,
        lblCount: cc.Label,
        lblNext: cc.Label,
        lblParam: cc.Label,
        bg: cc.Sprite,
        nodeAnswer: cc.Node,
        nodeRwd: cc.Node,
        nodeUnlock: cc.Node,
    },
    ctor() {},
    onClickRwd() {
        var t = this._data;
        t && r.utils.openPrefabView("achieve/TaskDayRwdView", !1, t);
    },
    onClickAnswer() {
        var t = this._data;
        if (t) {
            var e = n.achievementProxy.getKejuType(t.id);
            if (null == e || (e.num < 1 && 0 == e.answer)) {
                r.alertUtil.alert18n("KEJU_COUNT_TIP");
                return;
            }
            0 == e.answer && n.achievementProxy.sendAnswer(1e4 * t.id);
            r.utils.openPrefabView("achieve/KejuAnswer", !1, t);
        }
    },
    showData() {
        var t = this._data;
        if (t) {
            var e = n.achievementProxy.getKejuType(t.id);
            this.lblName.string = t.name;
            this.nodeRwd.active = this.nodeAnswer.active = 1 == t.id || 2 == t.id;
            l.shaderUtils.setImageGray(this.bg, !this.nodeRwd.active);
            this.nodeUnlock.active = !this.nodeRwd.active;
            if (1 == t.id) {
                this.lblCount.string = i18n.t("KEJU_REMAIN_DAY_COUNT", {
                    d: e ? e.num: 0
                });
                for (var o = n.achievementProxy.score,
                i = 0,
                r = 0,
                a = 0; a < t.ticket.length; a++) {
                    var s = t.ticket[a];
                    i = i < s.value ? s.value: i;
                    if (s.value > o) {
                        r = s.value;
                        break;
                    }
                }
                this.lblNext.string = 0 != r ? i18n.t("KEJU_NEXT_DES1", {
                    d: r
                }) : i18n.t("KEJU_MAX_HUOYUE");
                this.lblParam.string = i18n.t("KEJU_NEXT_PARAM1", {
                    d: o
                });
            } else {
                this.lblCount.string = i18n.t("KEJU_REMAIN_COUNT", {
                    d: e ? e.num: 0
                });
                var c = localcache.getItem(localdb.table_exam_type, t.ticket[0].type - 1),
                _ = n.achievementProxy.getKejuType(t.ticket[0].type - 1),
                d = _ ? t.ticket[0].value * Math.ceil((_.count + 1) / t.ticket[0].value) : t.ticket[0].value;
                this.lblNext.string = i18n.t("KEJU_NEXT_DES2", {
                    d: d,
                    n: c ? c.name: ""
                });
                this.lblParam.string = i18n.t("KEJU_NEXT_PARAM2", {
                    d: _ ? _.count: 0,
                    n: c ? c.name: ""
                });
                1 != t.id && 2 != t.id && (this.lblNext.string = this.lblParam.string = "");
            }
        }
    },
});
