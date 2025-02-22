var i = require("Utils");
cc.Class({
    extends: cc.Component,
    properties: {
        data:{
            visible:false,
            get: function () {
                return this._data;
            },
            set: function (d) {
                this._data = d;
                if (this._data == null) {
                    this.node.active = false;
                    return;
                }
                this.node.active = true;
                this.showData();
            },
        },

        select:{
            visible:false,
            set: function (flag) {
            },
        },
        optimizeArr:{displayName:"优化节点",tooltip:"优化节点",type:[cc.Node],default:[],},
    },

    ctor() {
        this._data = null;
    },


    showNodeAnimation() {
        this.node.getComponent(cc.Animation) && i.utils.showNodeEffect(this.node, -1);
    },
    showData() {},
    addBtnEvent(t) {
        t && t.clickEvents && t.clickEvents.length > 0 && (t.clickEvents[0].customEventData = this);
    },
    onDestroy() {
        this._data = null;
    },
    
    setWidthHeigth(t, e) {},
});
