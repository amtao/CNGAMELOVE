var i = require("Utils");
cc.Class({
    extends: cc.Component,
    properties: {
        lblcontent: cc.Label,
    },
    ctor() {},
    onLoad() {
        for (var t = this.node.openParam,
        e = localcache.getGroup(localdb.table_tips, "type", t.type), o = "", n = 0; n < e.length; n++) {
            o += (i.stringUtil.isBlank(e[n].title) ? "": e[n].title) + "\n";
            for (var l = e[n].des.split("|"), r = 0; r < l.length; r++) {
                o += l[r] + "\n";
                r == l.length - 1 && (o += "\n");
            }
        }
        this.lblcontent.string = o;
    },
    onClickClose() {
        i.utils.closeView(this);
    },
});
