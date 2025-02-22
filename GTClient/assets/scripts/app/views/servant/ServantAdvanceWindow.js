var i = require("UrlLoad");
var n = require("Utils");
var l = require("Initializer");
var r = require("UIUtils");
cc.Class({
    extends: cc.Component,
    properties: {
        lblName: cc.Label,
        lblShenFen: cc.Label,
        lblLv: cc.Label,
        lblShuJi: cc.Label,
        lblTiBa: cc.Label,
        lblZhiWEI: cc.Label,
        roleImg: i,
    },
    ctor() {},
    onLoad() {
        var t = l.servantProxy.getHeroData(l.servantProxy.curSelectId);
        if (t) {
            var e = localcache.getItem(localdb.table_nobility, t.senior);
            this.lblName.string = e.name;
            var o = localcache.getItem(localdb.table_nobility, t.senior - 1);
            if (o) {
                this.lblShenFen.string = o.name + " → " + e.name;
                this.lblLv.string = o.max_level + " → " + e.max_level;
                this.lblShuJi.string = o.maxeplv + " → " + e.maxeplv;
                this.lblTiBa.string = i18n.t("COMMON_SJJY") + "+" + o.zzexp;
            }
            // this.roleImg.loadHandle = () => {
            //     if (this.roleImg == null || this.roleImg.content == null) return;
            //     //urlLoadComp.content.position = cc.v2(urlLoadComp.content.x,-urlLoadComp.content.height*urlLoadComp.node.scale * 0.5);
            //     this.roleImg.content.position = cc.v2(this.roleImg.content.x,-100-this.roleImg.content.height); 
            // };
            this.roleImg.url = r.uiHelps.getServantSpine(t.id);
            let cfg = localcache.getItem(localdb.table_hero,t.id);
            n.audioManager.playEffect(cfg.getHeroCV, !0, !0);
        }
    },
    onClickClose() {
        n.utils.closeView(this);
    },
});
