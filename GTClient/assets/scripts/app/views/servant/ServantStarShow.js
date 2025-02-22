var i = require("List");
cc.Class({
    extends: cc.Component,
    properties: {
        starList: i,
    },
    ctor() {},
    onLoad() {},
    setValue(t) {
        for (var e = [], o = 0; o < t; o++) {
            e.push({});
        }
        this.starList.data = e;
    },
});
