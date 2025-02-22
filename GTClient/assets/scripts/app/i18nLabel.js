var i = require("i18n");
cc.Class({
    extends: cc.Component,
    properties: {
        textKey: {
            default: "TEXT_KEY",
            multiline: !0,
            tooltip: "Enter i18n key here",
            notify: function() {
                var t = this.node.getComponent(cc.Label);
                if (t) {
                    t.string = this.string;
                    // t._updateNodeSize();
                }
                // if (this._sgNode) {
                //     this._sgNode.setString(this.string);
                //     this._updateNodeSize();
                // }
            }
        },
        string: {
            override: !0,
            tooltip: "Here shows the localized string of Text Key",
            get: function() {
                return i.has(this.textKey) ? i.t(this.textKey) : this.textKey;
            },
            set: function(t) {
                cc.warn("Please set label text key in Text Key property.");
            }
        }
    },
    onLoad: function() {
        this.textKey = this.textKey;
    },
});
