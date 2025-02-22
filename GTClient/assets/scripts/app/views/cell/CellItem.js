var i = require("RenderListItem");
var n = require("UrlLoad");
var l = require("UIUtils");
var r = require("Initializer");
var a = require("ShaderUtils");
cc.Class({
    extends: i,
    properties: {
        select:{
            set: function(t) {
                this.eff.active = t;
            },
            enumerable: !0,
            configurable: !0
        },
        lblLv:cc.Label,
        lock:cc.Node,
        eff:cc.Node,
        item:cc.Node,
        url:n,
        btnClick:cc.Button,        
    },
    ctor() {
        this.lblLv = null;
        this.lock = null;
        this.eff = null;
        this.item = null;
        this.url = null;
        this.btnClick = null;
    },
    onLoad() {
        this.addBtnEvent(this.btnClick);
    },
    showData() {
        var t = this.data;
        if (t) {
            this.url.url = l.uiHelps.getCellHeadIcon(t.mod1);
            var e = r.cellProxy.getPestInfo(t.id);
            this.lblLv.string = i18n.t("COMMON_LV", {
                lv: e ? e.lv: 1
            });
            this.lock.active = null == e;
            null == e && a.shaderUtils.setNodeGray(this.item);
        }
    },
});
