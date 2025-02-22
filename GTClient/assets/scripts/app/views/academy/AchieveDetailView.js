var i = require("List");
var n = require("Initializer");
var l = require("Utils");

cc.Class({
    extends: cc.Component,

    properties: {
        list: i,
    },

    ctor() {},

    onLoad() {
        let list = n.achievementProxy.getDetail();
        this.list.data = list.sort(this.sort);
    },

    onClickClose() {
        l.utils.closeView(this);
    },

    sort: function(a, b) {
        if(a.state == 3 && b.state == 3) {
            return a.id - b.id;
        } else if(a.state == 3) {
            return 1;
        } else if(b.state == 3) {
            return -1;
        } else {
            return a.id - b.id;
        }
    }
});
