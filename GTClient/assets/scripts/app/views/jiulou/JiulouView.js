var i = require("Utils");
var n = require("Initializer");
var l = require("List");
cc.Class({
    extends: cc.Component,
    properties: {
        jiaList: l,
        guanList: l,
        btnJuban: cc.Node,
        btnJinru: cc.Node,
    },
    ctor() {},
    onLoad() {
        facade.subscribe("JIU_LOU_TYPE_CHANGE", this.onYhType, this);
        facade.subscribe("JIU_LOU_YH_LIST", this.onYhList, this);
        facade.subscribe("UI_TOUCH_MOVE_LEFT", this.onClickClose, this);
        n.jiulouProxy.sendJlInfo();
        this.onYhList();
        this.jiaList.selectHandle = function(t) {
            var e = t;
            if (e) {
                n.jiulouProxy.selectData = e;
                n.jiulouProxy.sendYhGo(e.uid);
            }
        };
    },
    onClickEnter() {
        n.jiulouProxy.yhType && (0 == n.jiulouProxy.yhType.type ? i.utils.openPrefabView("jiulou/JiulouCreate") : i.utils.showConfirm(i18n.t("JIU_LOU_INTO_MYSELF"),
        function() {
            n.jiulouProxy.selectData = null;
            n.jiulouProxy.sendYhGo(n.playerProxy.userData.uid);
        }));
    },
    onClickBianHao() {
        i.utils.openPrefabView("jiulou/JiulouFindView");
    },
    onClickRank() {
        n.jiulouProxy.sendJlRank();
    },
    onClickShop() {
        i.utils.openPrefabView("jiulou/JiulouExchange");
    },
    onYhType() {
        this.btnJuban.active = 0 == n.jiulouProxy.yhType.type;
        this.btnJinru.active = 0 != n.jiulouProxy.yhType.type;
    },
    onClickXiaoXi() {
        i.utils.openPrefabView("jiulou/JiulouLog");
    },
    onYhList() {
        this.jiaList.data = n.jiulouProxy.yhList;
    },
    onClickClose() {
        i.utils.closeView(this);
    },
});
