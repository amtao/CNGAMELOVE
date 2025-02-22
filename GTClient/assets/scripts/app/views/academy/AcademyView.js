var i = require("List");
var n = require("Initializer");
var l = require("Utils");
cc.Class({
    extends: cc.Component,
    properties: {
        deskList: i,
        roleNode: cc.Node,
    },
    ctor() {},
    onLoad() {
        JsonHttp.subscribe("ACADEMY_LIST_UPDATE", this.onDeskList, this);
        this.onDeskList();
    },
    onDeskList() {
        this.roleNode.active = null == n.academyProxy.deskList || 0 == n.academyProxy.deskList.length;
        this.deskList.data = n.academyProxy.deskList;
    },
    onClickSkill() {
        l.utils.openPrefabView("academy/UpSkillView");
    },
    onClickCreate() {
        l.utils.openPrefabView("academy/AcademyAddDesk");
    },
    onClickFind() {
        l.utils.openPrefabView("academy/FindView");
    },
    onClickClost() {
        l.utils.closeView(this);
    },
});
