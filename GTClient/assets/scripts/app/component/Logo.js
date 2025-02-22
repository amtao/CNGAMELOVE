var i = require("UrlLoad");
var n = require("Utils");
var l = require("Config");
var r = require("UIUtils");
cc.Class({
    extends: cc.Component,
    properties: {
        urllogo: i,
    },
    ctor() {},
    onLoad() {
        n.stringUtil.isBlank(l.Config.logo) || (this.urllogo.url = r.uiHelps.getLogo());
    },
});
