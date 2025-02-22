var i = require("RenderListItem");
var n = require("Initializer");
var l = require("Utils");
var r = require("ChildSpine");
cc.Class({
    extends: i,
    properties: {
        lblName: cc.Label,
        lblShuXing: cc.Label,
        lblFather: cc.Label,
        lblCost: cc.Label,
        childSpine: r,
        lblShenFen: cc.Label,
    },
    ctor() {
        this.itemSys = null;
    },
    showData() {
        var t = this._data;
        if (t) {
            this.lblName.string = t.sname;
            this.lblFather.string = t.fname;
            var e = localcache.getItem(localdb.table_adult, t.honor),
            o = localcache.getItem(localdb.table_item, e.itemid);
            this.lblCost.string = o.name
            this.childSpine.setKid(t.sonuid, t.sex);
            this.curData = t;
            var i = t.ep.e1 + t.ep.e2 + t.ep.e3 + t.ep.e4;
            this.lblShuXing.string = i + "";
            this.lblShenFen.string = n.sonProxy.getHonourStr(t.honor);
            this.itemSys = localcache.getItem(localdb.table_item, e.itemid);
        }
    },
    onClickJuJue() {
        n.sonProxy.sendJuJueTiQin(this.curData.fuid, this.curData.sonuid);
    },
    onClickSelect(t, e) {
        if ("1" == e) {
            if (0 == n.bagProxy.getItemCount(this.itemSys.id)) {
                l.alertUtil.alertItemLimit(this.itemSys.id);
                return;
            }
            1 == n.sonProxy.tiQinObj.marryType ? n.sonProxy.sendJieHun(this.curData.fuid, 2, this.curData.sonuid, n.sonProxy.tiQinObj.mySid) : 2 == n.sonProxy.tiQinObj.marryType && n.sonProxy.sendAgree(this.curData.fuid, 2, this.curData.sonuid, n.sonProxy.tiQinObj.mySid);
        } else "2" == e && l.utils.openPrefabView("marry/MySonListView", null, this._data);
    },
});
