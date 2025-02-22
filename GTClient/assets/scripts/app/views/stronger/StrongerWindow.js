var i = require("Utils");
var n = require("List");
var l = require("Initializer");
cc.Class({
    extends: cc.Component,
    properties: {
        list: n,
    },
    ctor() {},
    onLoad() {
        var t = this.node.openParam;
        if (t) {
            var e = localcache.getGroup(localdb.table_wifeSkill, "heroid", t.id),
            o = null;
            if (e && e.length > 0) {
                var i = e[0].wid;
                o = l.wifeProxy.getWifeData(i);
            }
            this.list.data = o ? [{
                type: 1,
                hero: t
            },
            {
                type: 2,
                hero: t
            },
            {
                type: 3,
                hero: t
            },
            {
                type: 4,
                hero: t
            }] : [{
                type: 1,
                hero: t
            },
            {
                type: 2,
                hero: t
            },
            {
                type: 3,
                hero: t
            }];
        }
    },
    onClickClose() {
        i.utils.closeView(this);
    },
});
