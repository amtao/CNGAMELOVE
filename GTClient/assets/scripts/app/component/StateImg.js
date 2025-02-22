cc.Class({
    extends: cc.Component,
    properties: {
        item: cc.Node,
        total:{
            get: function() {
                return this._total;
            },
            set: function(t) {
                this._total = t;
                this.updateShow();
            },
            enumerable: !0,
            configurable: !0
        },
        value:{
            get: function() {
                return this._value;
            },
            set: function(t) {
                this._value = t;
                this.updateShow();
            },
            enumerable: !0,
            configurable: !0
        },
    },
    ctor() {
        this.spaceX = 10;
        this._total = 0;
        this._value = 0;
        this._itemName = "";
    },
    onLoad() {
        this._itemName = this.item.name;
        this.item.name = this._itemName + "_0";
        this.updateShow();
    },
    updateShow() {
        for (var t = this.node.childrenCount,
        e = this.node.children,
        o = 0; o < t; o++) { (i = e[o]).active = o < this._total;
            i.children && i.childrenCount > 0 && (i.children[0].active = o < this.value);
            i.x = this.item.width * (o + 0.5) + this.spaceX * o;
        }
        if (t < this._total) for (o = 0; o < this._total; o++) {
            var i = cc.instantiate(this.item);
            this.node.addChild(i);
            i.children && i.childrenCount > 0 && (i.children[0].active = o < this.value);
            i.x = this.item.width * (o + 0.5) + this.spaceX * o;
        }
        this.node.width = this.item.width * this._total + this.spaceX * (this._total > 0 ? this._total - 1 : 0);
    },
});
