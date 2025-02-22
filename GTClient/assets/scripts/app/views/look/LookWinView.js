var i = require("Utils");
var n = require("UrlLoad");
var l = require("Initializer");
var r = require("UIUtils");
cc.Class({
    extends: cc.Component,
    properties: {
        urlload: n,
        img: n,
        lblName: cc.Label,
        lblDes: cc.Label,
        lblGet: cc.Label,
        lblBuild: cc.Label,
    },
    ctor() {},
    onLoad() {
        var t = this.node.openParam.id;
        1 == this.node.openParam.isSkip && this.scheduleOnce(this.onClickClost, 1);
        var e = l.lookProxy.win.xfAll[0];
        if (e) {
            var o = localcache.getItem(localdb.table_look, e.npcid);
            if (o) {
                // var n = o.wfid;
                // if (1 == o.type) {
                //     var a = localcache.getItem(localdb.table_wife, n);
                //     n = a ? a.res: n;
                //     l.wifeProxy.getWifeData(o.wfid) && i.alertUtil.alert(l.lookProxy.getString(e));
                // }
                // this.urlload.url = 1 == o.type ? r.uiHelps.getWifeBody(n) : r.uiHelps.getServantSpine(n);
                // this.lblName.string = o.name;
                
                var s = localcache.getItem(localdb.table_lookBuild, 0 == o.build ? t: o.build);
                r.uiUtils.showText(this.lblDes, o.nodesc);
                this.lblName.string = s ? s.name: "";
                //this.lblBuild.string = s ? s.name: "";
                this.lblGet.string = l.lookProxy.getString(e);
            }
        }
    },
    onClickClost() {
        i.utils.closeView(this);
        facade.send("LOOK_CLOST_WIN_WIN");
    },
});
