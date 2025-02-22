var i = require("RenderListItem");
var n = require("UrlLoad");
var l = require("UIUtils");
var r = require("JingYingWeiPai");
var a = require("Initializer");
cc.Class({
    extends: i,
    properties: {
        lblProp:cc.Label,
        face:n,
        nodeLock:cc.Node,
        nodeProp:cc.Node,
        lblName:cc.Label,
        nodeUnselect:cc.Node,
        btn:cc.Button,
    },
    ctor() {
        this.lblProp = null;
        this.face = null;
        this.nodeLock = null;
        this.nodeProp = null;
        this.lblName = null;
        this.nodeUnselect = null;
        this.btn = null;
    },
    onLoad() {
        this.addBtnEvent(this.btn);
    },
    showData() {
        var t = this._data;
        if (null != t.islock) {
            this.nodeLock.active = 1 == t.islock;
            this.nodeProp.active = !1;
            this.face.url = "";
        } else if (null != t.heroid) {
            var e = r.curSelect;
            this.nodeLock && (this.nodeLock.active = !1);
            this.nodeProp && (this.nodeProp.active = !0);
            var o = a.jingyingProxy.isWeipai(t.heroid, r.curSelect);
            this.lblName && (this.lblName.string = t.name);
            var i = a.servantProxy.getHeroData(t.heroid);
            this.lblProp.string = i18n.t("COMMON_PROP" + e) + (i ? i.aep["e" + e] : 0);
            this.face.url = l.uiHelps.getServantHead(t.heroid);
            this.nodeUnselect && (this.nodeUnselect.active = o);
        }
    },
});
