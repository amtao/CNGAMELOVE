var i = require("RenderListItem");
var n = require("UrlLoad");
var l = require("Utils");
var r = require("Initializer");
var a = require("UIUtils");
cc.Class({
    extends: i,
    properties: {
        lblname:cc.Label,
        lblCost:cc.Label,
        icon:n,
        btnSelect:cc.Button,
        nameNode:cc.Node,
        lblPrice:cc.Label,
        priceNode:cc.Node,
        juli:cc.Label,
    },
    showData() {
        var t = this._data;
        if (t) {
            this.lblname.string = t.name;
            this.priceNode.active = t.id<=1000;
            //this.lblCost.node.active = t.id>=1000;
            if (t.id<=1000) {
                let lmin = t.min*100
                let lmax = t.max*100
                this.juli.string = i18n.t("GONGLI_GONGLI2",{
                    mi: lmin,ma:lmax
                });
                var e = r.sonProxy.getSon(r.sonProxy.lilianData.sid),
                o = Math.ceil(((30 * t.max) / Math.ceil(r.playerProxy.userEp.e2 / 800)) * 0.5 * r.playerProxy.userEp.e2 * e.talent * 0.3);
                this.lblPrice.string = "" + o;
                this.btnSelect.interactable = r.playerProxy.userData.food >= o;
                this.icon.url = a.uiHelps.getXingLiIcon(t.icon);
            } else {
                // this.lblCost.string = r.bagProxy.getItemCount(t.itemid) + "/" + t.num;
                // this.btnSelect.interactable = r.bagProxy.getItemCount(t.itemid) >= t.num;
                // this.icon.url = a.uiHelps.getItemSlot(t.itemid);
            }
        }
    },
    onClickRender() {
        var t = this._data;
        r.sonProxy.lilianData.luggage = t.id;
        l.utils.closeNameView("child/ChildLilianXingliSelect");
        facade.send("CHILD_LI_LIAN_SELECT_UPDATE");
    },
});
