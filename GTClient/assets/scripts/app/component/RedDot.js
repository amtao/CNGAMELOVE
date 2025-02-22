var RedDot = cc.Class({
    extends: cc.Component,
    properties: {
        binding: [cc.String],
        sprite: cc.Sprite,
    },
    ctor() {
        this._isOnLoad = !1;
    },

    onLoad() {
        this.updateData(null);
        this._isOnLoad = !0;
        facade.subscribe("RED_DOT", this.updateData, this);
    },

    addBinding(t, bClear) {
        if(bClear) {
            this.binding = [];
        }
        if (null != t) {
            for (var e = 0; e < t.length; e++) {
                if(null != t[e] && t[e] != "") {
                    this.binding.push(t[e]);
                }
            }
            this._isOnLoad && this.updateData(null);
        }
    },

    updateData(t) {
        if (0 != this.binding.length) {
            var e = this.binding;
            if (null == t || -1 != e.indexOf(t)) {
                for (var i = e.length,
                n = 0; n < i; n++) if (RedDot._MAP[e[n].toString()]) {
                    this.node.active = !0;
                    this.sprite && (this.sprite.node.active = !1);
                    return;
                }
                this.node.active = !1;
                this.sprite && (this.sprite.node.active = !0);
            }
        } else this.node.active = !1;
    },

});

RedDot._MAP = {};
RedDot.change = function(t, e) {
    if (RedDot._MAP[t] != e) {
        RedDot._MAP[t] = e;
        facade.send("RED_DOT", t);
    }
};
RedDot.clearData = function() {
    RedDot._MAP = {};
};  