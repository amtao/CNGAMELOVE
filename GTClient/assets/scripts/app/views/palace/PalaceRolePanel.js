var i = require("RenderListItem");
var n = require("UrlLoad");
var l = require("UIUtils");
var r = require("Utils");
cc.Class({
    extends: i,
    properties: {
        fashion: n,
        jobImg: n,
        lblName: cc.Label,
        lblTalk: cc.Label,
        talkNode: cc.Node,
        headImg: n,
    },
    ctor() {
        this.curDta = null;
    },
    showData() {
        var t = this._data;
        if (t) {
            this.lblName.string = t.name;
            this.headImg.url = l.uiHelps.getHead(t.sex, t.job);
            this.lblTalk.string = t.xuanyan;
            this.curDta = t;
        }
    },
    onClickRole() {
        r.utils.openPrefabView("palace/Palace/5", null, this.curDta);
    },
});
