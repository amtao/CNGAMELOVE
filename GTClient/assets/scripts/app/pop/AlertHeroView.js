var i = require("Utils");
let scUrl = require("UrlLoad");
let scUIUtils = require("UIUtils");

cc.Class({
    extends: cc.Component,

    properties: {
        lblName: cc.Label,
        lblSp: cc.Label,
        lblOrg: cc.Label,
        lblAdd: cc.Label,
        nHeroHead: cc.Node,
        urlHeroHead: scUrl,
        nPlayerHead: cc.Node,
        //nodeRole: cc.Node,
    },

    ctor() {},

    onLoad() {
        var t = this.node.openParam;
        this.lblName.string = t.name;
        this.lblSp.string = t.sp;
        this.lblOrg.string = t.org;
        this.lblAdd.string = t.add;
        let bRole = null != t.bRole;
        this.nPlayerHead.active = bRole;
        this.nHeroHead.active = !bRole;
        if(!bRole) {
            this.urlHeroHead.url = scUIUtils.uiHelps.getServantHead(t.id);
        }
        //this.nodeRole.active = null != t.role;
    },

    onClickClost() {
        i.utils.closeView(this);
        i.utils.popNext(!1);
    },
});
