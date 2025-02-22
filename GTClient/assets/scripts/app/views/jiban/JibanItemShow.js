var i = require("UrlLoad");
var n = require("Utils");
var l = require("UIUtils");
var r = require("Initializer");
cc.Class({
    extends: cc.Component,
    properties: {
        roleUrl: i,
        proUrl: i,
        title_bg: i,
        jb_title: i,
        lblName: cc.Label,
        lblStory: cc.Label,
        lblServant: cc.Label,
        lblPro: cc.Label,
        lblprotitle:cc.Label,
    },
    ctor() {
        this.list = [];
        this._index = 0;
    },
    onLoad() {
        this.list = this.node.openParam.list;
        this.showData();
    },
    showData() {
        var t = this.list[this._index];
        if (t) {
            var e = localcache.getItem(localdb.table_heropve, t);
            if (e) {
                this.roleUrl.url = l.uiHelps.getServantSpine(e.roleid);
                this.lblName.string = e.name;
                this.title_bg.url = l.uiHelps.getJbTitleBg(e.star);
                //this.jb_title.url = l.uiHelps.getJbTitle(e.star);
                this.proUrl.url = l.uiHelps.getLangSp(e.specMsg);
                this.lblprotitle.string = r.servantProxy.getPropName(e.specMsg)
                var o = localcache.getItem(localdb.table_hero, e.roleid);
                this.lblServant.string = o.name;
                this.lblName.string = e.name;
                var i = r.jibanProxy.getJbItemLvData(e.roleid, t),
                n = i ? i.level: 0,
                a = localcache.getItem(localdb.table_heropveJbProp, n),
                s = 0;
                if (a) for (var c = 0; c < a.count.length; c++) if (a.count[c].star == e.star) {
                    s = a.count[c].count;
                    break;
                }
                this.lblPro.string = i18n.t("SERVANT_JI_BAN_JIA_HAO_TXT", {
                    num: s
                });
                // this.lblLv.string = i18n.t("SERVANT_JI_BAN_ITEM_LEVEL_TXT", {
                //     lv: n
                // });
                // var _ = localcache.getItem(localdb.table_heropveJbLevel, n);
                // if (_) {
                //     var d = i ? i.exp: 0;
                //     this.bar.progress = d / _.story_num;
                //     this.lblExp.string = i18n.t("COMMON_NUM", {
                //         f: d,
                //         s: _.story_num
                //     });
                // }
                // e.star <= 1 ? (this.lblLv.node.color = cc.color(10, 53, 64)) : 2 == e.star ? (this.lblLv.node.color = cc.color(42, 10, 64)) : 3 == e.star ? (this.lblLv.node.color = cc.color(87, 9, 9)) : e.star >= 4 && (this.lblLv.node.color = cc.color(104, 43, 9));
            }
        }
        this._index++;
    },
    onClickClose() {
        this._index <= this.list.length - 1 ? this.showData() : n.utils.closeView(this);
    },
});
