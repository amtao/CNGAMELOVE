var i = require("RenderListItem");
var n = require("Initializer");
cc.Class({
    extends: i,
    properties: {
        lblName: cc.Label,
        lblChapt: cc.Label,
        lblJiban: cc.Label,
        btnClick: cc.Button,
        btnBg: cc.Button,
        nodeLock: cc.Node,
    },
    ctor() {},
    onLoad() {
        this.addBtnEvent(this.btnClick);
    },
    showData() {
        var t = this.data;
        if (t) {
            var e = n.jibanProxy.isOverStory(t.id),
            o = n.jibanProxy.getJibanIsOpen(t);
            this.btnBg.interactable = !e;
            this.lblName.string = t.name;
            this.lblChapt.string = n.playerProxy.getReplaceName(t.msg);
            this.lblJiban.string = o ? "": this.getUnlockString();
            this.nodeLock.active = !o;
            this.lblName.node.color = e ? cc.color(91, 74, 78) : cc.color(226, 0, 53);
            this.lblChapt.node.color = e ? cc.color(91, 74, 78) : cc.color(226, 0, 53);
            this.lblJiban.node.color = e ? cc.color(91, 74, 78) : cc.color(226, 0, 53);
        }
    },
    getUnlockString() {
        var t = this.data,
        e = "";
        if (t) {
            if (2 == t.type) e = n.playerProxy.getWifeName(t.roleid);
            else if (1 == t.type) {
                e = localcache.getItem(localdb.table_hero, t.roleid).name;
            }
            switch (t.unlocktype) {
            case 1:
            case 2:
                return i18n.t("SERVANT_JIBAN_NEED" + t.unlocktype, {
                    n: e,
                    c: t.unlock
                });
            case 3:
                var o = localcache.getItem(localdb.table_mainTask, t.unlock);
                return i18n.t("FIGHT_TASK_LIMIT", {
                    n: o.name
                });
            }
        }
        return "";
    },
});
