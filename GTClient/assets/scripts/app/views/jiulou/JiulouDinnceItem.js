var i = require("RenderListItem");
var n = require("Utils");
var l = require("UrlLoad");
var r = require("Initializer");
var a = require("UIUtils");
cc.Class({
    extends: i,
    properties: {
        head_jia:l,
        head_guan:l,
        letfKuai_jia:cc.Node,
        letfShao_jia:cc.Node,
        rightKuai_jia:cc.Node,
        rightShao_jia:cc.Node,
        letfKuai_Guan:cc.Node,
        letfShao_Guan:cc.Node,
        rightKuai_Guan:cc.Node,
        rightShao_Guan:cc.Node,
        nameNode:cc.Node,
        lblname:cc.Label,
        guan_node:cc.Node,
        jia_node:cc.Node,
    },
    showData() {
        var t = this._data;
        if (t) {
            this.guan_node.active = 2 == r.jiulouProxy.yhInfo.id;
            this.jia_node.active = 1 == r.jiulouProxy.yhInfo.id;
            this.head_jia.url = this.head_guan.url = 0 == t.hid ? null: a.uiHelps.getServantSmallSpine(t.hid);
            this.head_jia.node.active = this.head_guan.node.active = 0 != t.hid;
            this.letfKuai_jia.active = t.id % 2 == 1 && 1 == r.jiulouProxy.yhInfo.id;
            this.letfShao_jia.active = t.id % 2 == 1 && 0 == t.hid && 1 == r.jiulouProxy.yhInfo.id;
            this.letfKuai_Guan.active = t.id % 2 == 1 && 2 == r.jiulouProxy.yhInfo.id;
            this.letfShao_Guan.active = t.id % 2 == 1 && 0 == t.hid && 2 == r.jiulouProxy.yhInfo.id;
            this.rightKuai_jia.active = t.id % 2 == 0 && 1 == r.jiulouProxy.yhInfo.id;
            this.rightShao_jia.active = t.id % 2 == 0 && 0 == t.hid && 1 == r.jiulouProxy.yhInfo.id;
            this.rightKuai_Guan.active = t.id % 2 == 0 && 2 == r.jiulouProxy.yhInfo.id;
            this.rightShao_Guan.active = t.id % 2 == 0 && 0 == t.hid && 2 == r.jiulouProxy.yhInfo.id;
            this.nameNode.active = 0 != t.hid;
            this.lblname.string = t.name;
        }
    },
    onClickRender() {
        var t = this._data;
        if (0 == t.hid) {
            r.jiulouProxy.xwId = t.id;
            n.utils.openPrefabView("jiulou/JiulouHeroSelect");
        } else t.uid != r.playerProxy.userData.uid && r.playerProxy.sendGetOther(t.uid);
    },
});
