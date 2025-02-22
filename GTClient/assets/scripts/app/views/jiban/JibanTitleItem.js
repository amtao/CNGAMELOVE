var i = require("RenderListItem");
var l = require("UIUtils");
var urlLoad = require("UrlLoad");

cc.Class({
    extends: i,

    properties: {
        urlBg: urlLoad,
        wordUrl: cc.Label,
    },

    ctor() {},

    showData() {
        var t = this._data,
        e = t.unlocktype;
        let titlestr = l.uiHelps.getJbTitleTxt(e);
        this.wordUrl.string = i18n.t("JIBAN_STORY_TIPS",{v1:titlestr,v2:t.num});
        // this.wordUrl.node.color = l.uiHelps.getJbColor(e);
        // this.lblNum.node.color = l.uiHelps.getJbColor(e);
        // this.nDot.color = l.uiHelps.p(e);
        this.urlBg.url = l.uiHelps.getJbBg(e);
    },
});
