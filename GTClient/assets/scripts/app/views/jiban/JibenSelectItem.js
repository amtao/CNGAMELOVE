var i = require("RenderListItem");
var n = require("UIUtils");
var l = require("UrlLoad");
var r = require("Initializer");
var a = require("StateImg");
cc.Class({
    extends: i,
    properties: {
        lblName:cc.Label,
        lblChapt:cc.Label,
        btnShow:cc.Button,
        state:a,
        imgHead:l,
        imgWife:l,
    },
    onLoad() {
        this.addBtnEvent(this.btnShow);
    },
    showData() {
        var t = this.data;
        if (t) {
            var e = r.jibanProxy.getJibanType(t.roleid, t.type);
            this.state.total = e.length;
            if (2 == t.type) {
                this.imgHead.node.active = !1;
                this.imgWife.node.active = !0;
                var o = localcache.getItem(localdb.table_wife, t.roleid);
                this.imgWife.url = n.uiHelps.getWifeSmallBody(o.res);
            } else if (1 == t.type) {
                this.imgHead.node.active = !0;
                this.imgWife.node.active = !1;
                this.imgHead.url = n.uiHelps.getServantSmallSpine(t.roleid);
            }
            for (var i = 0,
            l = 0; l < e.length; l++) r.jibanProxy.isOverStory(e[l].id) && i++;
            this.state.value = i;
            if (2 == t.type) {
                var a = localcache.getItem(localdb.table_wife, t.roleid);
                this.lblName.string = 2 == r.playerProxy.userData.sex ? a.wname2: a.wname;
                var s = (2 == r.playerProxy.userData.sex ? a.info2: a.info).split("|");
                this.lblChapt.string = s.length >= 2 ? s[0] + s[1] : s[0];
            } else if (1 == t.type) {
                var c = localcache.getItem(localdb.table_hero, t.roleid),
                _ = localcache.getItem(localdb.table_heroinfo, t.roleid);
                this.lblName.string = c.name;
                var d = _.recruit.split("|");
                this.lblChapt.string = d.length >= 2 ? d[0] + d[1] : d[0];
            }
        }
    },
});
