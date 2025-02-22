var i = require("RenderListItem");
var n = require("Initializer");
var l = require("Utils");
var r = require("ChildSpine");
var a = require("ServantStarShow");
cc.Class({
    extends: i,
    properties: {
        lblname:cc.Label,
        lblCost:cc.Label,
        icon:r,
        iconSmall:r,
        stars:a,
    },
    showData() {
        var t = this._data;
        if (t) {
            this.lblname.string = t.name;
            var e = t.ep.e1 + t.ep.e2 + t.ep.e3 + t.ep.e4;
            this.lblCost.string = e;
            t.state > 3 ? this.icon.setKid(t.id, t.sex) : this.iconSmall.setKid(t.id, t.sex, !1);
            this.icon.node.active = t.state > 3;
            this.iconSmall.node.active = t.state <= 3;
            this.stars.setValue(t.talent);
        }
    },
    onClickRender() {
        var t = this._data,
        e = n.timeProxy.getLoacalValue("CHILD_LI_LIAN_DATA"),
        o = null;
        if (null != e) {
            var i = JSON.parse(e + "");
            for (var r in i) if (r == t.id.toString()) {
                o = i[r];
                break;
            }
        }
        if (o) {
            n.sonProxy.lilianData.luggage = o.luggage;
            n.sonProxy.lilianData.travel = o.travel;
            n.sonProxy.lilianData.sid = o.sid;
            var a = localcache.getItem(localdb.table_practiceTravel, o.travel),
            s = !1;
            1 == a.type ? (s = n.playerProxy.userData.cash >= a.money) : 2 == a.type && (s = n.playerProxy.userData.food >= a.money);
            s || (n.sonProxy.lilianData.travel = 0);
            var c = localcache.getItem(localdb.table_practiceItem, o.luggage),
            _ = !1;
            if (0 == c.itemid) {
                var d = n.sonProxy.getSon(n.sonProxy.lilianData.sid),
                u = Math.ceil(((30 * c.max) / Math.ceil(n.playerProxy.userEp.e2 / 800)) * 0.5 * n.playerProxy.userEp.e2 * d.talent * 0.3);
                _ = n.playerProxy.userData.food >= u;
            } else {
                _ = n.bagProxy.getItemCount(c.itemid) >= c.num;
            }
            _ || (n.sonProxy.lilianData.luggage = 0);
        } else n.sonProxy.lilianData.sid = t.id;
        n.sonProxy.lilianSonData = t;
        facade.send("CHILD_LI_LIAN_SELECT_UPDATE");
        l.utils.closeNameView("child/ChildLilianSonSelect");
    },
});
