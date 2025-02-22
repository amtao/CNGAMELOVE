var i = require("RenderListItem");
var n = require("Initializer");
var l = require("UrlLoad");
var r = require("UIUtils");
cc.Class({
    extends: i,
    properties: {
        lblName: cc.Label,
        lblDes: cc.Label,
        lblCount: cc.Label,
        bg: cc.Sprite,
        stars: [cc.Node],
        bgs: [cc.SpriteFrame],
        starLay: cc.Layout,
        btn: cc.Button,
        lblLv: cc.Label,
        redNode: cc.Node,
        proUrl: l,
        lbProp: cc.Label,
    },

    ctor() {},
    onLoad() {
        this.btn && this.addBtnEvent(this.btn);
    },
    showData() {
        var t = this._data;
        if (t) {
            var e = localcache.getItem(localdb.table_epSkill, t.id + "");
            this.lblName.string = e.name;
            if(null != this.lblDes) {
                this.lblDes.string = i18n.t("SERVANT_ZZ" + e.ep);
            }
            this.bg.spriteFrame = this.bgs[e.ep - 1];
            this.starLay.spacingX = 2 - e.star;
            for (var o = 0; o < this.stars.length; o++) this.stars[o].active = o < e.star;
            var i = t.level + (t.hlv ? t.hlv: 0);
            i = i < 1 ? 1 : i;
            if(null != this.lblCount) {
                this.lblCount.string = e.star * i + "";
            }
            this.lblLv.string = 0 == t.level ? "": t.level + "";
            if (n.servantProxy.curSelectId && 0 != n.servantProxy.curSelectId) {
                var l = n.servantProxy.getHeroData(n.servantProxy.curSelectId);
                this.redNode.active = n.servantProxy.tanlentIsEnoughUp(l, t);
            }
            var a = localcache.getItem(localdb.table_epSkill, t.id);
            let val = a ? a.ep: 1;
            this.proUrl && (this.proUrl.url = r.uiHelps.getLangSp(val));
            this.lbProp && (this.lbProp.string = r.uiHelps.getPinzhiStr(val));
        }
    },
});
