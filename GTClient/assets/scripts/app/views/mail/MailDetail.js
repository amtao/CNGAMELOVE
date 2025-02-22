var i = require("List");
var n = require("Utils");
var l = require("Initializer");
cc.Class({
    extends: cc.Component,
    properties: {
        lblTitle: cc.Label,
        lblTime: cc.Label,
        lblContent: cc.Label,
        list: i,
        btnGet: cc.Node,
        btnDelete: cc.Node,
        //bottomImg: cc.Node,
        scroll: cc.ScrollView,
        itemNode: cc.Node,
        gotoBtn:cc.Node
    },
    ctor() {},

    clickOpenUrl(){
        cc.sys.openURL(this.node.openParam.link)
    },

    onLoad() {
        facade.subscribe("MAIL_UPDATE", this.mailUpdate, this);
        var t = this.node.openParam;
        0 == t.mtype && null == t.rts && 0 == t.rts && l.mailProxy.sendOpenMail(t.id);
        if (t) {
            this.gotoBtn.active = t.link
            this.lblTitle.string = l.mailProxy.getMailContent(t.mtitle);
            this.lblTime.string = n.timeUtil.format(t.fts, "yyyy-MM-dd");
            this.lblContent.string = l.mailProxy.getMailContent(t.mcontent);
            if (1 == t.mtype && 0 == t.rts) {
                this.list.node.active = !0;
                this.list.data = t.items;
                // this.itemNode.x = -this.list.node.width / 2 + 10;
                //if (t.items.length > 4) {
                    //this.bottomImg.height = 300;
                    //this.lblTime.node.y = -20;
                    //this.itemNode.y = 7;
                    //this.scroll.node.height = 220;
                //} else {
                    //this.itemNode.y = -95;
                    //this.bottomImg.height = 400;
                    //this.lblTime.node.y = -120;
                    //this.scroll.node.height = 320;
                //}
            } else {
                // this.bottomImg.height = 510;
                // this.lblTime.node.y = -232;
                this.list.node.active = !1;
                //this.scroll.node.height = 440;
            }
            this.btnGet.active = 1 == t.mtype && (t.rts <= 0 || null == t.rts);
            this.curId = t.id;
            this.mailUpdate();
        }
    },
    mailUpdate() {
        var t = l.mailProxy.getMail(this.curId);
        if (t) {
            this.btnGet.active = 1 == t.mtype && (t.rts <= 0 || null == t.rts);
            this.btnDelete.active = 0 == t.mtype || 0 != t.rts;
        }
    },
    onClickDelete() {
        var t = this;
        n.utils.showConfirm(i18n.t("MAIN_DELETE_CUR"),
        function() {
            l.mailProxy.sendDelete(t.curId);
            n.utils.closeView(t);
        });
    },
    onClickGet() {
        l.mailProxy.sendReadMail(this.curId);
    },
    onClickClose() {
        n.utils.closeView(this);
    },
});
