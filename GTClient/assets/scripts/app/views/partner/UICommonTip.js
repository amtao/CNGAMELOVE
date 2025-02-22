var i = require("Utils");

cc.Class({
    extends: cc.Component,
    properties: {
        lblcontent:cc.Label,
        pnode:cc.Node,
    },

    ctor() {
        
    },

    onLoad() {
        var ss = this.node.openParam;
        if (ss){
            this.lblcontent.string = ss.text;
            var h = this.lblcontent.node.getContentSize().height;
            if (h > 140){
                var r = this.pnode.getContentSize();
                this.pnode.setContentSize(r.width,h+40);
            }
        }
    },

   

    onClose() {
        i.utils.closeView(this);
    },



    // update (dt) {},
});
