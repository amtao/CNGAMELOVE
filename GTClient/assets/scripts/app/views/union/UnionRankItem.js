var i = require("Initializer");
var n = require("RenderListItem");
var l = require("Utils");
cc.Class({
    extends: n,
    properties: {
        lblName: cc.Label,
        lblLead: cc.Label,
        lblCount: cc.Label,
        lblShili: cc.Label,
        lblRank: cc.Label,
        imgRank: cc.Sprite,
        ranks: [cc.SpriteFrame],
        nodeApplyed: cc.Node,
        btnApply: cc.Button,
        btnName: cc.Button,
    },
    ctor() {},
    onLoad() {
        this.btnApply && this.btnApply.clickEvents && this.btnApply.clickEvents.length > 0 && (this.btnApply.clickEvents[0].customEventData = this);
        this.btnName && this.btnName.clickEvents && this.btnName.clickEvents.length > 0 && (this.btnName.clickEvents[0].customEventData = this);
    },
    showData() {
        var t = this._data;
        if (t) {
            var e = i.unionProxy.getMengzhu(t.members);
            this.lblLead.string = e ? e.name: "";
            this.lblName.string = i18n.t("UNION_LEVEL_TXT_2", {
                name: t.name,
                lv: t.level
            });
            this.lblShili.string = l.utils.formatMoney(t.allShiLi);
            this.lblCount.string = t.members.length + "/" + i.unionProxy.getUnionLvMaxCount(t.level);
            this.imgRank.node.active = t.rid < 4;
            this.lblRank.node.active = t.rid >= 4;
            this.lblRank.string = t.rid + "";
            t.rid < 4 && (this.imgRank.spriteFrame = this.ranks[t.rid - 1 < 0 ? 0 : t.rid - 1]);
        }
    },
    onClickItem() {
        l.utils.openPrefabView("union/UnionInfo", null, this._data);
    },
});
