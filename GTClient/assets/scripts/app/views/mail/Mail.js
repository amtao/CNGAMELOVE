var i = require("Utils");
var n = require("List");
var l = require("Initializer");
cc.Class({
    extends: cc.Component,
    properties: {
        lblMailNum: cc.Label,
        list: n,
        btnGet: cc.Node,
        btnDelete: cc.Node,
        mailTip: cc.Node,
    },
    ctor() {},
    onLoad() {
        facade.subscribe("MAIL_UPDATE", this.onMailUpdate, this);
        this.list.selectHandle = function(t) {
            var e = t; (null != e.rts && 0 != e.rts) || l.mailProxy.sendOpenMail(e.id);
            i.utils.openPrefabView("mail/MailDetail", null, e);
        };
        this.onMailUpdate();
    },
    onMailUpdate() {
        if(this.node == null || !this.node.isValid) {
            return;
        }
        l.mailProxy.mailList.sort(l.mailProxy.sortList);
        this.list.data = l.mailProxy.mailList;
        this.lblMailNum.string = i18n.t("MAIL_NUM", {
            cur: l.mailProxy.getMailNum(!1),
            total: l.mailProxy.mailList.length
        });
        this.btnGet.active = this.btnDelete.active = l.mailProxy.mailList.length > 0;
        this.mailTip.active = 0 == l.mailProxy.mailList.length;
    },
    onClickDeleteAll() {
        i.utils.showConfirm(i18n.t("MAIN_DELETE_ALL"),
        function() {
            l.mailProxy.sendDeleteAll();
        });
    },
    onClickClose() {
        i.utils.closeView(this);
    },
    onOneKeyGet() {
        //for (var t = 0; t < l.mailProxy.mailList.length; t++) 1 == l.mailProxy.mailList[t].mtype && 0 == l.mailProxy.mailList[t].rts && l.mailProxy.sendReadMail(l.mailProxy.mailList[t].id);
        l.mailProxy.sendOneKeyPickMails();
    },
});
