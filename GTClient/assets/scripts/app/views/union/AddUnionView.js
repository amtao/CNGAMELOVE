var i = require("Utils");
var n = require("Initializer");
var l = require("UrlLoad");
var r = require("UIUtils");
cc.Class({
    extends: cc.Component,
    properties: {
        role: l,
        tlabel:cc.Label
    },
    ctor() {},
    onLoad() {
        facade.subscribe("UNION_CREATE_SUCESS", this.eventClose, this);
        facade.subscribe("UI_TOUCH_MOVE_LEFT", this.eventClose, this);
        var t = r.uiHelps.getServantSpine(3);
        let arrays = localcache.getList("iconOpen")
        this.tlabel.string = arrays[6].text
        // if (n.playerProxy.heroShow > 200) {
        //     var e = localcache.getItem(localdb.table_wife, n.playerProxy.heroShow - 200);
        //     t = r.uiHelps.getWifeBody(e.res);
        // } else t = r.uiHelps.getServantSpine(n.playerProxy.heroShow);
        this.role.url = t;
    },
    eventClose() {
        i.utils.closeView(this);
    },
    eventCreateUnion() {
        i.utils.openPrefabView("union/UnionCreate");
    },
    eventRandomUnion() {
        n.unionProxy.sendRandomAdd();
    },
    eventLookUpUnion() {
        i.utils.openPrefabView("union/UnionSearch");
    },
    eventListUnion() {
        n.unionProxy.sendRankList(n.unionProxy.memberInfo.cid);
    },
});
