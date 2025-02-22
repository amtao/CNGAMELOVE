var i = require("List");
var n = require("Config");
var initializer = require("Initializer");
var r = require("Utils");

cc.Class({
    extends: cc.Component,
    properties: {
        list: i,
    },
    ctor() {},
    onLoad() {
        // for (var t = localcache.getList(localdb.table_userjob), e = [], o = 0; o < t.length; o++) t[o].id != l.playerProxy.userData.job && (n.Config.DEBUG || null == t[o].display || 0 == t[o].display.length || -1 != t[o].display.indexOf(n.Config.pf)) && e.push(t[o]);
        var t = localcache.getList(localdb.table_userjob);
        var e = [];
        for (var  o = 0; o < t.length; o++) {
            // if (t[o].id == initializer.playerProxy.userData.job) {
            //     // 使用中
            //
            // }

            // if (n.Config.DEBUG || null == t[o].display || 0 == t[o].display.length || -1 != t[o].display.indexOf(n.Config.pf)) {
            //     e.push(t[o])
            // }

            e.push(t[o])

        }
        this.list.data = e;
        facade.subscribe("USER_JOB_CHANGE_CLOST", this.onClickClost, this);
        // facade.subscribe(initializer.playerProxy.PLAYER_ALLJOBS_UPDATE, this.onListChange, this);
    },

    // onListChange () {
    //     var arr = initializer.playerProxy.getAllJobs();
    //     this.list.data = arr;
    // },

    onClickClost() {
        r.utils.closeView(this);
    },
});
