var i = require("RenderListItem");
var n = require("Initializer");
var l = require("Utils");
var r = require("UrlLoad");
var a = require("UIUtils");
var s = require("ShaderUtils");
cc.Class({
    extends: i,
    properties: {
        lblname: cc.Label,
        lbllv: cc.Label,
        lblpro: cc.Label,
        btnSelect: cc.Node,
        btnHuifu: cc.Node,
        head: r,
        roleUrl: r,
        itemNode: cc.Sprite,
    },
    ctor() {},
    showData() {
        var t = this,
        e = this._data;
        if (e) {
            localcache.getItem(localdb.table_hero, e.id);
            this.lblpro.string = l.utils.formatMoney(e.aep.e1);
            var o = n.unionProxy.getHeroFightData(e.id);
            this.btnHuifu.active = null != o && 0 == o.h && 1 == o.f;
            this.roleUrl.url = a.uiHelps.getServantSmallSpine(e.id);
            s.shaderUtils.setImageGray(this.itemNode, o && 0 == o.h);
            o && 0 == o.h ? s.shaderUtils.setNodeGray(this.roleUrl.node) : (null != o && 1 != o.h) || s.shaderUtils.clearNodeShader(this.roleUrl.node);
            this.roleUrl.loadHandle = function() {
                o && 0 == o.h ? s.shaderUtils.setNodeGray(t.roleUrl.node) : (null != o && 1 != o.h) || s.shaderUtils.clearNodeShader(t.roleUrl.node);
            };
        }
    },
    onClickSelect() {
        var t = this._data,
        e = n.unionProxy.getHeroFightData(t.id);
        if (!n.unionProxy.fighting && (null == e || 1 == e.h)) {
            n.unionProxy.curSelectId = t.id;
            facade.send("UNION_COPY_HERO_CHANGE");
            l.utils.closeNameView("union/UnionHeroSelect");
        }
    },
    onClickHuifu() {
        var t = this,
        e = localcache.getItem(localdb.table_item, 124);
        l.utils.showConfirmItem(i18n.t("UNION_COST_CHU_ZHAN", {
            name: e.name,
            num: 1
        }), 124, n.bagProxy.getItemCount(124),
        function() {
            if (n.bagProxy.getItemCount(124) <= 0) l.alertUtil.alertItemLimit(124);
            else {
                var e = t._data;
                n.unionProxy.sendHeroFuhuo(e.id);
            }
        },
        "UNION_COST_CHU_ZHAN");
    },
});
