var i = require("RenderListItem");
var n = require("UrlLoad");
var r = require("Initializer");
let config = require("Config");

cc.Class({
    extends: i,

    properties: {
        spNum: n,
        spBg: n,
        nodeOver: cc.Node,
        lblName: cc.Label,
        lblKey: cc.Label,
        nodeBoss: cc.Node,
        btn: cc.Button,
        lb_army: cc.Label,
        node_army: cc.Node,
    },

    ctor() {},

    onLoad() {
        //this.addBtnEvent(this.btn);
    },

    showData() {
        var t = this._data,
        e = r.playerProxy.userData;
        if (t && t.bmap) {
            var o = t;
            this.lblKey.string = i18n.t("COMMON_NUM" + o.mdtext);
            this.lblName.string = o.mname;
            this.nodeBoss.active = !1;
            let bOver1 = e.mmap > o.id;
            this.nodeOver.active = e.mmap < o.id;
            this.spBg.url = config.Config.skin + "/res/ui/fight/juqing_dimian_" + (bOver1 ? "3" : e.mmap == o.id ? "5" : "7");
            //this.spNum.url = config.Config.skin + "/res/ui/fight/juqing_dimian_" + (bOver1 ? "2" : "4");
            let white = cc.Color.WHITE;
            this.lblName.node.color = e.mmap == o.id ? cc.Color.WHITE : white.fromHEX("#EAD7AE");
            this.lblKey.node.color = e.mmap == o.id ? cc.Color.WHITE : white.fromHEX("#EAD7AE");
            // var sInfo = localcache.getItem(localdb.table_smallPve,t.id);
            this.node_army.active = false; //!bOver1 
            // this.lb_army.string = bOver1 ? " " : sInfo.army;
            this.lb_army.string = !bOver1 ? r.fightProxy.needArmyByMap(t.id):" "      
        }
        if (t && t.bossname) {
            var n = t,
            c = localcache.getGroup(localdb.table_midPve, "bmap", n.id);
            this.nodeBoss.active = !0;
            this.lblKey.string = i18n.t("COMMON_NUM" + (c.length + 1));
            this.lblName.string = n.bossname;
            let bOver2 = e.bmap > t.id;
            this.nodeOver.active = e.bmap < t.id;
            this.spBg.url = config.Config.skin + "/res/ui/fight/juqing_dimian_" + (bOver2 ? "3" : r.fightProxy.checkIsBoss() ? "5" : "7");
            //this.spNum.url = config.Config.skin + "/res/ui/fight/juqing_dimian_" + (bOver2 ? "2" : "4");
            let white = cc.Color.WHITE;
            this.lblName.node.color = e.mmap == t.id ? cc.Color.WHITE : white.fromHEX("#EAD7AE");
            this.lblKey.node.color = e.mmap == t.id ? cc.Color.WHITE : white.fromHEX("#EAD7AE");
            this.lb_army.string = " ";
            this.node_army.active = false;
        }
    },
});
