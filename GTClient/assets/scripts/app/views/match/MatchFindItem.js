var i = require("RenderListItem");
var n = require("UrlLoad");
var l = require("UIUtils");
var r = require("Utils");
cc.Class({
    extends: i,
    properties: {
        nodeBei: cc.Node,
        nodeShow: cc.Node,
        urlload: n,
        btn: cc.Button,
        nodeItem: cc.Node,
    },
    ctor() {
        this.lastShow = !1;
        this.lastId = 0;
    },
    onLoad() {
        this.addBtnEvent(this.btn);
    },
    showData() {
        var t = this._data;
        if (t) if (0 != t.id) {
            this.nodeBei.active = this.nodeShow.active = !0;
            if (t.isShow != this.lastShow) {
                this.lastId = t.id;
                r.utils.showNodeEffect(this.nodeItem, t.isShow ? 0 : 1);
            } else if (t.id != this.lastId) {
                this.lastId = t.id;
                r.utils.showNodeEffect(this.nodeItem, 1);
            }
            this.lastShow = t.isShow;
            this.urlload.url = l.uiHelps.getMatchFind(t.id);
            this.urlload.node.active = t.isShow;
        } else {
            this.nodeBei.active = this.nodeShow.active = this.urlload.node.active = !1;
            this.urlload.url = "";
        }
    },
    setWidthHeigth(t, e) {
        this.node.width = this.btn.node.width = this.nodeBei.width = this.nodeShow.width = t;
        this.node.height = this.btn.node.height = this.nodeBei.height = this.nodeShow.height = e;
        this.nodeItem.x = t / 2;
        this.nodeItem.y = -e / 2;
        this.urlload.node.scaleX = this.urlload.node.scaleY = t / 550;
    },
});
