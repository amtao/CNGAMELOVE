var i = require("RenderListItem");
var n = require("UrlLoad");
var l = require("Initializer");
var r = require("Utils");
var a = require("UIUtils");
cc.Class({
    extends: i,
    properties: {
        nameText:cc.Label,
        fatherText:cc.Label,
        honorText:cc.Label,
        slText:cc.Label,
        childImg:n,
    },

    showData() {
        var t = this._data;
        if (t) {
            this.nameText.string = t.sname;
            this.fatherText.string = i18n.t("SON_FATHER_NAME", {
                name: t.fname
            });
            this.honorText.string = i18n.t("SON_HONOUR_TEXT", {
                str: l.sonProxy.getHonourStr(t.honor)
            });
            var e = t.ep.e1 + t.ep.e2 + t.ep.e3 + t.ep.e4;
            this.slText.string = i18n.t("SON_TOTAL_PROP", {
                value: e
            });
            this.childImg.url = a.uiHelps.getKejuBody(t.honor, t.sex);
            l.sonProxy.tiQinObj.tUid = t.fuid;
            l.sonProxy.tiQinObj.tSid = t.sonuid;
        }
    },
    onClickSelect() {
        r.utils.openPrefabView("marry/MySonListView", null, this._data);
    },
});
