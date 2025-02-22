var i = require("Utils");
var n = require("Initializer");
var l = require("TimeProxy");
var r = require("Config");
var a = require("UIUtils");
var s = require("ShaderUtils");
cc.Class({
    extends: cc.Component,
    properties: {
        scroll: cc.ScrollView,
        nodeBook: cc.Button,
        nodeKit: cc.Button,
        nodeYanhui: cc.Button,
        nodeXianli: cc.Button,
        nodeTianlao: cc.Button,
        nodeQifu: cc.Button,
        nodeUnion: cc.Button,
        nodeGongdou: cc.Button,
        nodeFlower: cc.Button,
        nodeWishingtree: cc.Button,
        nodeFengxianDian: cc.Button,
        nodeInfo: cc.Node,
        lblName: cc.Label,
        lblDes: cc.Label,
        lblUnlock: cc.Label,
        nodeXianYun: cc.Button,
    },
    ctor() {
        this._lastX = 999;
        this._curGoId = 0;
    },
    onLoad() {
        this.scroll.content.height > r.Config.showHeight + 120 && (this.scroll.content.height = r.Config.showHeight + 120);
        n.bookProxy.updateRed();
        n.kitchenProxy.updateDot();
        n.flowerProxy.updateRed();
        this.setItemShow(this.nodeBook, 2, l.funUtils.bookView);
        this.setItemShow(this.nodeKit, 3, l.funUtils.kitchenView);
        this.setItemShow(this.nodeYanhui, 2, l.funUtils.jiulouView);
        this.setItemShow(this.nodeXianli, 3, l.funUtils.xianli);
        this.setItemShow(this.nodeTianlao, 2, l.funUtils.prisonView);
        this.setItemShow(this.nodeQifu, 3, l.funUtils.qifu);
        this.setItemShow(this.nodeUnion, 2, l.funUtils.unionView);
        this.setItemShow(this.nodeGongdou, 3, l.funUtils.yamenView);
        this.setItemShow(this.nodeFlower, 3, l.funUtils.flower);
        this.setItemShow(this.nodeWishingtree, 2, l.funUtils.wishingTree);
        this.setItemShow(this.nodeXianYun, 2, l.funUtils.xianyun);
        this.setItemShow(this.nodeFengxianDian, 2, l.funUtils.fengxiandian);
        this.nodeInfo.active = !1;
        facade.subscribe("UI_TOUCH_MOVE_LEFT", this.onCheckClost, this);
        if (n.unionProxy.memberInfo && n.unionProxy.memberInfo.cid) {
            n.unionProxy.sendGetMemberInfo(n.unionProxy.memberInfo.cid); ! n.unionProxy.memberInfo || (1 != n.unionProxy.memberInfo.post && 2 != n.unionProxy.memberInfo.post) || n.unionProxy.sendApplyList();
        }
    },
    onCheckClost() {
        var t = Math.abs(this.scroll.getScrollOffset().x);
        Math.abs(this.scroll.getScrollOffset().x) < 10 && this._lastX < 10 && this.onClickClost();
        this._lastX = t;
    },
    setItemShow(t, e, o) {
        l.funUtils.isOpenFun(o) ? a.uiUtils.floatPos(t.node, 0, 10, e) : s.shaderUtils.setNodeGray(t.node);
    },
    onClickGo() {
        if (this._curGoId) {
            var t = localcache.getItem(localdb.table_iconOpen, this._curGoId);
            if (null != t && l.funUtils.isOpen(t)) {
                l.funUtils.openView(this._curGoId);
                this.onClickClost();
            } else i.alertUtil.alert(t.errmsg);
        }
    },
    onClickTab(t, e) {
        console.log("wqinfo onClickTab")
        console.log(e)
        console.log("wqinfo --------------1")
        if (i.stringUtil.isBlank(e)) {
            console.log("wqinfo --------------2")
            this.nodeInfo.active = !1;
            "0" != e && i.alertUtil.alert(i18n.t("MAIN_FUN_UNOPEN"));
        } else {
            console.log("wqinfo --------------3")
            if (l.funUtils.isCanOpenViewUrl(e)) 
            {
                console.log("wqinfo --------------4")
                if ("union/UnionView" == e) 
                {
                    console.log("wqinfo --------------5")
                    n.unionProxy.enterUnion();
                }
                else {
                    console.log("wqinfo --------------6")
                    this.nodeInfo.active = !1;
                    l.funUtils.openViewUrl(e + "");
                } 
            }else {
                console.log("wqinfo --------------7")
                var o = l.funUtils.getOpenFun(e);
                this.nodeInfo.active = null != o;
                this.lblDes.string = o.text;
                this.lblUnlock.string = o.errmsg;
                this.lblName.string = o.title;
                this._curGoId = o.way;
                var a = t.target;
                if (a) {
                    console.log("wqinfo --------------8")
                    var s = a.node;
                    null == s && (s = a);
                    if (s) {
                        console.log("wqinfo --------------9")

                        var c = i.utils.getWorldPos(s, this.node);
                        facade.send("GUIDE_MOVE_ITEM", c.x);

                    }
                }
                console.log("wqinfo --------------10")
                r.Config.DEBUG && l.funUtils.openViewUrl(e + "");
            }
        }
    },
    onClickUnion() {
        l.funUtils.isOpenFun(l.funUtils.unionView) && n.unionProxy.enterUnion();
    },
    onClickMail() {
        n.mailProxy.sendGetMail();
    },
    onClickClost() {
        i.utils.closeView(this, !0);
    },
    onClickAcademy() {
        n.academyProxy.info.ruid && 0 != n.academyProxy.info.ruid ? n.academyProxy.sendInto(n.academyProxy.info.ruid) : n.academyProxy.sendRefreshList();
    },
});
