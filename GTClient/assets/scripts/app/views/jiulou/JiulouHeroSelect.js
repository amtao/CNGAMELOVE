var i = require("List");
var n = require("Initializer");
var l = require("Utils");
cc.Class({
    extends: cc.Component,
    properties: {
        heroList: i,
    },
    ctor() {},
    onLoad() {
        this.heroList.data = n.jiulouProxy.getYhHeroList();
    },
    onClickClose() {
        l.utils.closeView(this);
    },
});
