var i = require("Utils");
var n = require("List");
var l = require("Initializer");
cc.Class({
    extends: cc.Component,
    properties: {
        btns: [cc.Button],
        nodes: [cc.Node],
        lists: [n],
        nodeInfo: cc.Node,
    },
    ctor() {
        this.curIndex = 1;
    },
    onLoad() {
        this.onClickTab();
        facade.subscribe("JIU_LOU_LOG_UPDATE", this.onClickTab, this);
    },
    onClickTab(t, e) {
        var o = e ? parseInt(e) : this.curIndex;
        this.curIndex = o;
        for (var i = 0; i < this.btns.length; i++) {
            this.btns[i].interactable = i != o - 1;
            this.nodes[i].active = i == o - 1;
            0 == i ? (this.btns[i].node.active = l.jiulouProxy.yhOldList && l.jiulouProxy.yhOldList.length > 0) : 1 == i ? (this.btns[i].node.active = l.jiulouProxy.yhBadList && l.jiulouProxy.yhBadList.length > 0) : 2 == i && (this.btns[i].node.active = l.jiulouProxy.lbList && l.jiulouProxy.lbList.length > 0);
        }
        switch (o) {
        case 1:
            this.lists[0].data = l.jiulouProxy.yhOldList;
            l.jiulouProxy.yhOldList;
            break;
        case 2:
            this.lists[1].data = l.jiulouProxy.yhBadList;
            l.jiulouProxy.yhBadList;
            break;
        case 3:
            this.lists[2].data = l.jiulouProxy.lbList;
            l.jiulouProxy.lbList;
        }
        this.nodeInfo.active = null == l.jiulouProxy.yhOldList || 0 == l.jiulouProxy.yhOldList.length;
    },
    onClickClost() {
        i.utils.closeView(this);
    },
});
