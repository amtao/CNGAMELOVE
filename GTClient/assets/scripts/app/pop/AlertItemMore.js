var i = require("Utils");
var n = require("List");
var initializer = require("Initializer");

cc.Class({
    extends: cc.Component,

    properties: {
        skGet: sp.Skeleton,
        list: n,
    },

    ctor() {

    },

    onLoad() {
        facade.subscribe("CLOST_ITEM_SHOW", this.onClickClost, this);
        var t = this.node.openParam;
        null != t && (this.list.data = t);

        let self = this;
        this.skGet.setCompleteListener((trackEntry) => {
            let aniName = trackEntry.animation ? trackEntry.animation.name : "";
            if (aniName === 'get_on') {
                if (null != self.skGet) {
                    self.timeScale = 1;
                    self.skGet.setAnimation(0, 'get_idle', true);
                    self.scheduleOnce(self.onClickClost, 1);
                }
            }
        });
    },

    onClickClost() {
        i.utils.closeView(this);
        i.utils.popNext(!1);
    },

    onDestroy(){
        initializer.baowuProxy.clearSettlementData();
    },
});
