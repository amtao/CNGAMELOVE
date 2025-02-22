var i = require("Utils");
var AcademyProxy = function () {

    this.deskList = [];
    this.info = null;
    this.deskInfo = null;

    this.ctor = function() {
        JsonHttp.subscribe(proto_sc.hanlin.ting, this.onDeskList, this);
        JsonHttp.subscribe(proto_sc.hanlin.info, this.onInfo, this);
        JsonHttp.subscribe(proto_sc.hanlin.desk, this.onDesk, this);
        JsonHttp.subscribe(proto_sc.hanlin.win, this.onWin, this);
    };
    this.clearData = function() {
        this.deskList = null;
        this.info = null;
        this.deskInfo = null;
    };
    this.onDeskList = function(t) {
        null == this.deskList
            ? (this.deskList = t)
            : i.utils.copyList(this.deskList, t);
        facade.send("ACADEMY_LIST_UPDATE");
    };
    this.onInfo = function(t) {
        this.info = t;
        facade.send("ACADEMY_PERSON_INFO_UPDATE");
    };
    this.onDesk = function(t) {
        this.deskInfo = t;
        facade.send("ACADEMY_DESK_INFO_UPDATE");
    };
    this.onWin = function(t) {
        t.tif &&
            (0 == t.tif.win
                ? i.utils.openPrefabView("academy/FailPop")
                : 1 == t.tif.win &&
                  i.utils.openPrefabView("academy/WinPop"));
        t.tim && i.utils.openPrefabView("academy/AcademyPopView");
        t.fang && i.utils.openPrefabView("acdemy/AcdemyEnd", null, t.fang);
        t.find && facade.send("ACADEMY_FIND_UNDATE", t.find);
        if (t.upskill) {
            i.utils.openPrefabView("academy/UpSkillView", null, t.upskill);
            facade.send("ACADEMY_SKILL_UPDATE");
        }
    };
    this.sendRefreshList = function() {
        JsonHttp.send(new proto_cs.hanlin.listinfo(), function() {
            i.utils.openPrefabView("academy/AcademyView");
        });
    };
    this.sendCreate = function() {
        JsonHttp.send(new proto_cs.hanlin.opendesk(), function() {
            i.utils.openPrefabView("academy/AcademyInside");
        });
    };
    this.sendInto = function(t) {
        var e = new proto_cs.hanlin.comein();
        e.fuid = t;
        JsonHttp.send(e, function() {
            i.utils.openPrefabView("academy/AcademyInside");
        });
    };
    this.sendJoin = function(t, e) {
        var o = new proto_cs.hanlin.sitdown();
        o.fuid = t;
        o.rid = e;
        JsonHttp.send(o);
    };
    this.sendKick = function(t, e, o) {
        var i = new proto_cs.hanlin.ti();
        i.fuid = t;
        i.rid = e;
        i.uid = o;
        JsonHttp.send(i);
    };
    this.sendFind = function(t) {
        var e = new proto_cs.hanlin.find();
        e.fuid = t;
        JsonHttp.send(e);
    };
    this.sendUpSkill = function() {
        JsonHttp.send(new proto_cs.hanlin.upskill());
    };
    this.sendProtect = function(t, e, o) {
        var i = new proto_cs.hanlin.suoding();
        i.fuid = t;
        i.rid = e;
        i.uid = o;
        JsonHttp.send(i);
    };
}
exports.AcademyProxy = AcademyProxy;
