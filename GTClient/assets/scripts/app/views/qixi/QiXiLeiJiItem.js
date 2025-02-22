var i = require("RenderListItem");
var n = require("List");
var l = require("Initializer");
var r = require("ShaderUtils");
cc.Class({
    extends: i,
    properties: {
        list: n,
        bg: cc.Node,
        lblFuQi: cc.Label,
        btnLinQu: cc.Button,
        btnYiLinQu: cc.Button,
        btnNoLinQu: cc.Node,
        btnNoLinQuImg: cc.Sprite,
        lblJBjuqing: cc.Label,
    },
    ctor() {},
    showData() {
        var t = this._data;
        if (null != t) {
            var e = l.qixiProxy.data.rwds[l.qixiProxy.data.rwds.length - 1],
            o = e.rwd[e.rwd.length - 1];
            if (o && t.id == o.id) {
                this.list.content.width = 100;
                this.list.node.x = (this.node.width - 100) >> 1;
                this.lblJBjuqing.node.active = !0;
                for (var i = 0,
                n = 0; n < l.qixiProxy.data.rwds.length; n++) if (l.qixiProxy.data.rwds[n].hid == l.qixiProxy.selectHeroId) {
                    var a = l.qixiProxy.data.rwds[n],
                    s = a.rwd.length,
                    c = a.rwd[s - 1].items.length;
                    i = a.rwd[s - 1].items[c - 1].id;
                }
                var _ = localcache.getItem(localdb.table_heropve, i);
                this.lblJBjuqing.string = _.name + i18n.t("WISHING_JB_JU_QING");
            }
            this.list.data = t.items;
            var d = localcache.getItem(localdb.table_hero, l.qixiProxy.selectHeroId),
            u = l.qixiProxy.getHeroRwd(l.qixiProxy.selectHeroId),
            p = i18n.t("QIXI_LEI_JI_JI_FEN", {
                name: d ? d.name: "",
                num1: u.cons,
                num2: t.need
            });
            this.lblFuQi.string = p;
            this.btnLinQu.node.active = u.cons >= t.need && !t.get;
            this.btnNoLinQu.active = u.cons < t.need;
            this.btnYiLinQu.node.active = u.cons >= t.need && 1 == t.get;
            r.shaderUtils.setImageGray(this.btnNoLinQuImg);
        }
    },
    onClickLingQu() {
        var t = this._data;
        null != t && l.qixiProxy.sendLingQu(t.id, l.qixiProxy.selectHeroId);
    },
    setWidthHeigth(t, e) {
        this.node.height = e;
        this.bg.height = e;
        this.btnLinQu.node.y = this.btnLinQu.node.height - e;
        this.btnYiLinQu.node.y = this.btnYiLinQu.node.height - e;
        this.btnNoLinQu.y = this.btnNoLinQu.height - e;
    },
});
