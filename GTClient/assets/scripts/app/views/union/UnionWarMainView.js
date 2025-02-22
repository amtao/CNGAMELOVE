var i = require("Utils");
cc.Class({
    extends: cc.Component,
    properties: {

    },
    ctor() {
        
    },
    onLoad() {},
    eventClose() {
        i.utils.closeView(this);
    },
});