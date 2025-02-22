
let initializer = require("Initializer");

cc.Class({

    extends: cc.Component,

    setData: function(data) {

    },

    sortList: function(a, b) {
        let i = a.id > initializer.limitActivityProxy.curSelectData.rwd ? -1 : 1,
        j = b.id > initializer.limitActivityProxy.curSelectData.rwd ? -1 : 1;
        return i != j ? i - j: a.id - b.id;
    },
});
