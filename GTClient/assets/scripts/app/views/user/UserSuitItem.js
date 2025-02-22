var i = require("RenderListItem");
var n = require("UrlLoad");
var l = require("UIUtils");
var r = require("Initializer");
cc.Class({
    extends: i,
    properties: {
        lblName: cc.Label,
        prop: n,
        nodeSelect: cc.Node,
        select:{
            set: function(t) {
                this.nodeSelect.active = t;
            },
            enumerable: !0,
            configurable: !0
        },
    },
    ctor() {},
    showData() {
        var t = this._data;
        if (t) {
            this.lblName.string = i18n.t("COMMON_ADD_2", {
                n: t.name,
                c: r.playerProxy.getSuitCount(t.id).str
            });
            for (var e = 0; e < t.clother.length; e++) {
                var o = localcache.getItem(localdb.table_userClothe, t.clother[e]);
                switch (o.part) {
                case 2:
                    var i = o.model.split("|");
                    this.prop.url = l.uiHelps.getRolePart(i[0]);
                }
            }
        }
    },
});
