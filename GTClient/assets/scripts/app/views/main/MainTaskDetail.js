var i = require("Utils");
var n = require("List");
var l = require("Initializer");
var r = require("TimeProxy");
var a = require("Config");
cc.Class({
    extends: cc.Component,
    properties: {
        list: n,
        nodeBtn: cc.Node,
        nGoBtn: cc.Node,
        lblName: cc.Label,
        lblDes: cc.Label,
        lblTarget: cc.Label,
        prg: cc.ProgressBar,
        isTrigMain: true,
    },

    ctor() {
        this.list = null;
        this.nodeBtn = null;
        this.lblName = null;
        this.lblDes = null;
        this.lblTarget = null;
        this.prg = null;
        this.isTrigMain = !0;
    },

    onLoad() {
        this.isTrigMain = null == this.node.openParam;
        facade.subscribe(l.taskProxy.MAIN_TASK_REFESH, this.onRefesh, this);
        this.onRefesh();
        this.node.getComponent(cc.Animation).on("stop", () => {
            facade.send("GUIDE_ANI_FINISHED");
        });
    },

    onClickClose() {
        // if (l.taskProxy.mainTask.id == 40)
        // change new guide --2020.08.11
        //l.guideProxy.guideUI._isTrigger = false;
        i.utils.closeView(this);
        l.playerProxy.updateRoleLvupRed();
        // change new guide --2020.08.11
        // if(this.isTrigMain && l.guideProxy.guideUI._isTrigger)
        // {
        //     facade.send(l.guideProxy.UPDATE_TRIGGER_GUIDE, {
        //         type: 3,
        //         value: l.taskProxy.mainTask.id
        //     });
        // }
    },

    onClickRwd() {
        // change new guide --2020.08.11
        // facade.send(l.guideProxy.UPDATE_TRIGGER_GUIDE, {
        //     type: 7,
        //     value: l.taskProxy.mainTask.id
        // });
        l.taskProxy.sendRecvMain();
    },

    onClickGo() {
        this.onClickClose();
        var t = l.taskProxy.mainTask,
            e = localcache.getItem(localdb.table_mainTask, t.id + "");
        if (e) {
            r.funUtils.openView(e.jumpTo);
            e.jumpTo != r.funUtils.battleView.id &&
                facade.send("MAIN_TASK_OPEN");
        }
    },

    onRefesh() {
        var t = l.taskProxy.mainTask,
            e = localcache.getItem(localdb.table_mainTask, t.id + "");
        if (e) {
            var o = l.taskProxy.isFiltTaskType(e.type);
            this.lblName.string = i18n.t(
                a.Config.DEBUG ? "MAIN_TASK_TITLE" : "MAIN_TASK_UNID_TITLE",
                {
                    id: t.id,
                    t: e.name
                }
            );
            this.lblDes.string = e.msg;
            t.max = 0 == t.max ? 1 : t.max;
            this.lblTarget.string = o
                ? i18n.t("COMMON_NUM", {
                      f: t.num < t.max || t.num <= 0 ? 0 : 1,
                      s: 1
                  })
                : i18n.t("COMMON_NUM", {
                      f: t.num,
                      s: t.max
                  });
            this.list.data = this.getRwd(e.rwd);
            this.list.node.x = -this.list.node.width * this.list.node.scaleX / 2;
            this.nodeBtn.active = t.num >= t.max;
            this.nGoBtn.active = !this.nodeBtn.active;
            var i = o ? (t.num < t.max || t.num <= 0 ? 0 : 1) : t.num / t.max;
            i = i > 1 ? 1 : i;
            this.prg.progress = i;
        }
    },

    getRwd(t) {
        for (var e = [], o = 0; o < t.length; o++)
            (2 == t[o].kind && 100 == t[o].id) || e.push(t[o]);
        return e;
    },    
});