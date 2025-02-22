var i = require("UrlLoad");
var n = require("Config");
var l = require("UIUtils");
var r = require("Utils");
cc.Class({
    extends: i,
    properties: {
        pNode: cc.Node,
        animation:{
            set: function(t) {
                null == this.sp && (this.sp = this.node.getComponentInChildren(sp.Skeleton));
                this.sp && (this.sp.animation = t);
            },
            enumerable: !0,
            configurable: !0
        },
        setActive:{
            set: function(t) {
                this._isShow = t;
                this.pNode ? (this.pNode.active = t) : this.node.children[0] && (this.node.children[0].active = t);
            },
            enumerable: !0,
            configurable: !0
        }
    },
    ctor() {
        this.key = "";
        this.sp = null;
        this._isShow = !0;
    },

    onLoad() {
        var t = this;
        if ("zh-ch" != n.Config.lang) {
            var e = "" == this.key ? this.getSpKey() : this.key;
            if (!r.stringUtil.isBlank(e)) if (null != this.pNode) {
                this._isShow = this.pNode.active;
                this.sp = null;
                this.pNode.destroy();
                this.pNode.removeFromParent();
                this.pNode = null;
                var o = l.uiHelps.getLangPrefab(e);
                if (!this._isShow) {
                    var i = this;
                    this.loadHandle = function() {
                        i.setActive = t._isShow;
                    };
                }
                r.stringUtil.isBlank(o) || (this.url = o);
            } else {
                o = l.uiHelps.getLangSprite(e);
                r.stringUtil.isBlank(o) || (this.url = o);
            }
        }
    },
    getSpKey() {
        var t = this.node.getComponent(cc.Sprite);
        return null != t ? t.spriteFrame ? t.spriteFrame._name: "": null != this.pNode ? this.pNode.name: "";
    },
});
