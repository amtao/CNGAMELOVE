var i = require("UrlLoad");
var n = require("Utils");
var GetEffectItem = cc.Class({
    extends: cc.Component,
    properties: {
        urlLoad: i,
        url:{
            // visible:false,
            set: function (url) {
                this.urlLoad.url = url;
            },
            enumerable: !0,
            configurable: !0            
        },
        des:{
            set: function(t) {
                var e = this;
                if (0 == GetEffectItem.count) {
                    var i = this.urlLoad.node.getComponent(cc.Animation);
                    i && (GetEffectItem.count = i.getClips().length);
                }
                var l = this;
                n.utils.showEffect(this.urlLoad, Math.floor(Math.random() * GetEffectItem.count),
                function() {
                    e.node.runAction(cc.sequence(cc.delayTime(0.5), cc.spawn(cc.moveTo(0.5, t), cc.fadeTo(0.5, 0), cc.scaleTo(0.5, 0)), cc.callFunc(function() {
                        l.node.removeFromParent(!0);
                        l.node.destroy();
                    })));
                });
            },
            enumerable: !0,
            configurable: !0
        },
    },
    ctor() {},
});
GetEffectItem.count = 0;