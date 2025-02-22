var i = require("Initializer");
var n = require("Config");
var l = require("Utils");
cc.Class({
    extends: cc.Component,
    properties: {
        textLabel: cc.Label,
        textLabel1: cc.Label,
        nodeOver: cc.Node,
        nodePer: cc.Node,
    },
    ctor() {},
    onLoad() {
        this.updateShow();
        facade.subscribe(i.taskProxy.MAIN_TASK_REFESH, this.updateShow, this);
    },
    updateShow() {
        this.unscheduleAllCallbacks();
        l.utils.showEffect(this, 0);
        var t = i.taskProxy.mainTask,
        e = localcache.getItem(localdb.table_mainTask, t.id + "");
        this.nodeOver.active = t.num >= t.max;
        this.nodePer.active = t.num < t.max;
        e && i.taskProxy.isFiltTaskType(e.type) ? (this.textLabel1.string = this.textLabel.string = e ? i18n.t(n.Config.DEBUG ? "MAIN_TASK_SHOW": "MAIN_TASK_UNID_SHOW", {
            id: t.id,
            t: e.name,
            c: t.num < t.max ? 0 : 1,
            m: 1
        }) : i18n.t("MAIN_TASK_OVER")) : (this.textLabel1.string = this.textLabel.string = e ? i18n.t(n.Config.DEBUG ? "MAIN_TASK_SHOW": "MAIN_TASK_UNID_SHOW", {
            id: t.id,
            t: e.name,
            c: t.num,
            m: t.max
        }) : i18n.t("MAIN_TASK_OVER"));
        this.scheduleOnce(this.onTimer, 2);
    },
    onTimer() {
        l.utils.closeView(this);
    },
});
