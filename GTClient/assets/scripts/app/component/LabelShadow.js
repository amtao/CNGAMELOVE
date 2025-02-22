cc.Class({
    extends: cc.Component,
    properties: {
        string:{
            get: function() {
                return this._str;
            },
            set: function(t) {
                this.targetLabel && (this.targetLabel.string = t);
                this.shadowLabel && (this.shadowLabel.string = t);
                this._str = t;
            },
            enumerable: !0,
            configurable: !0
        },
        color:{
            set: function(t) {
                this.shadowLabel ? (this.shadowLabel.node.color = t) : this.targetLabel && (this.targetLabel.node.color = t);
                this._color = t;
            },
            enumerable: !0,
            configurable: !0
        },        
    },
    ctor() {
        this.targetLabel = null;
        this.shadowLabel = null;
        this._str = null;
        this._color = null;
    },

    onLoad() {
        if (this.node.parent.getComponent("LabelShadow")) this.destroy();
        else {
            if (null == this.targetLabel) {
                this.targetLabel = this.node.getComponent(cc.Label);
                if (null == this.targetLabel) return;
            }
            if (null == this.shadowLabel) {
                var t = cc.instantiate(this.targetLabel.node);
                this.shadowLabel = t.getComponent(cc.Label);
                this.targetLabel.node.addChild(t);
                t.x = -1;
                t.y = 1;
                this.targetLabel.node.color = cc.Color.WHITE.fromHEX("#8D2939");
            }
            null != this._str && (this.string = this._str);
            null != this._color && (this.color = this._color);
        }
    },
});
