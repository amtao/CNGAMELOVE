var i = require("UrlLoad");
var n = require("Config");
cc.Class({
    extends: cc.Component,
    properties: {

    },
    ctor() {
        
    },
    onLoad() {
        if ("" != n.Config.share_meta_url) {
            var t = this.node.getComponent('UrlLoad');
            null == t && (t = this.node.addComponent('UrlLoad'));
            t.url = n.Config.share_meta_url;
        }
    },
});
