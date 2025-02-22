var i = require("Utils");
var n = require("Initializer");
var l = require("RedDot");
var MailProxy = function() {

    this.mailList = [];

    this.ctor = function() {
        JsonHttp.subscribe(proto_sc.mail.mailList, this.onMailList, this);
    };
    this.clearData = function() {
        this.mailList = null;
    };
    this.onMailList = function(t) {
        null == this.mailList
            ? (this.mailList = t)
            : i.utils.copyList(this.mailList, t);
        l.change("mail", this.hasNewMail());
        facade.send("MAIL_UPDATE");
    };
    this.sendGetMail = function() {
        JsonHttp.send(new proto_cs.mail.getMail(), function() {
            i.utils.openPrefabView("mail/Mail");
        });
    };
    this.sendReadMail = function(t) {
        var e = new proto_cs.mail.redMails();
        e.mid = t;
        JsonHttp.send(e, function() {
            n.timeProxy.floatReward();
        });
    };
    this.sendDelete = function(t) {
        var e = new proto_cs.mail.delMail();
        e.mid = t;
        var o = this;
        JsonHttp.send(e, function() {
            var t = o.getMail(e.mid),
                i = o.mailList.indexOf(t);
            o.mailList.splice(i, 1);
            facade.send("MAIL_UPDATE");
        });
    };
    this.sendDeleteAll = function() {
        var t = this;
        JsonHttp.send(new proto_cs.mail.delMails(), function() {
            for (var e = [], o = 0; o < t.mailList.length; o++)
                0 == t.mailList[o].rts && e.push(t.mailList[o]);
            t.mailList = e;
            facade.send("MAIL_UPDATE");
        });
    };

    /**一键领取所有的邮件*/
    this.sendOneKeyPickMails = function(){
        var e = new proto_cs.mail.oneKeyPickMails();
        JsonHttp.send(e, function() {
            n.timeProxy.floatReward();
        });       
    };

    this.sendOpenMail = function(t) {
        var e = new proto_cs.mail.openMails();
        e.mid = t;
        JsonHttp.send(e);
    };
    this.getMail = function(t) {
        for (var e = 0; e < this.mailList.length; e++)
            if (this.mailList[e].id == t) return this.mailList[e];
        return null;
    };
    this.getMailNum = function(t) {
        for (var e = 0, o = 0; o < this.mailList.length; o++)
            t
                ? this.mailList[o].rts > 0 && e++
                : (this.mailList[o].rts <= 0 ||
                      null == this.mailList[o].rts) &&
                  e++;
        return e;
    };
    this.sortList = function(t, e) {
        var o = t.rts > 0 ? 1 : 0,
            i = e.rts > 0 ? 1 : 0;
        return o != i ? o - i : e.fts - t.fts;
    };
    this.getMailContent = function(t) {
        for (var e = "", o = (t + "").split("|"), i = 0; i < o.length; i++)
            i18n.has(o[i]) ? (e += i18n.t(o[i])) : (e += o[i]);
        return e;
    };
    this.hasNewMail = function() {
        for (var t = !1, e = 0; e < this.mailList.length; e++)
            if (0 == this.mailList[e].rts) {
                t = !0;
                break;
            }
        return t;
    };
}
exports.MailProxy = MailProxy;
