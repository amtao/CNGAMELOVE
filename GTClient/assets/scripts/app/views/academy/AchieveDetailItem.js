var renderListItem = require("RenderListItem");
var n = require("List");
cc.Class({
    extends: renderListItem,
    properties: {
        node_Doing: cc.Node,
        node_Fishing: cc.Node,
        nodeGet: cc.Node,
        nodeUnover: cc.Node,
        list: n,
        lblName: cc.Label,
    },
    ctor() {},
    showData() {
        var t = this._data;
        if (t) {
            this.node_Doing.active = 1 == t.state;
            this.nodeUnover.active = 0 == t.state;
            this.node_Fishing.active = 2 == t.state;
            this.nodeGet.active = 3 == t.state;
            this.lblName.string = i18n.t("MAIN_TASK_SHOW", {
                id: t.id,
                t: t.dAchieve.title,
                c: t.dAchieve.num,
                m: t.data.need
            });
            this.list.data = t.rwd;
        }
    },
});
