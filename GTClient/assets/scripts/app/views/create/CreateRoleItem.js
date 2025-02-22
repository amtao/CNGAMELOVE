var i = require("RenderListItem");
var UrlLoad = require("UrlLoad");
var Initializer = require("Initializer");
cc.Class({
    extends: i,
    properties: {
        role: UrlLoad,
        selectNode: cc.Node,
        select:{
            set: function(t) {
                this.selectNode && (this.selectNode.active = t);
            },
            enumerable: !0,
            configurable: !0
        },
    },
    ctor() {},

    showData() {
        var t = this.data;
        //t && this.role.setCreateClothes(t.sex, t.job, 0);
        if (t){
            Initializer.playerProxy.loadPlayerSpinePrefab(this.role,{creatorjob:t.job});
        }
    },
});
