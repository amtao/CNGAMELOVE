var i = require("Utils");
var n = require("UIUtils");
cc.Class({
    extends: cc.Component,
    properties: {
        toolTip:{ default:"", tooltip: "显示备注" },
        itemId:{ default:0, tooltip: "显示物品id" },
    },
    ctor() {
        this.btn = null;
    },
    onLoad() {
        null == this.btn && (this.btn = this.node.getComponent(cc.Button));
        if (null == this.btn) cc.sys.isMobile ? this.node.on(cc.Node.EventType.TOUCH_START, this.onClickBtn) : this.node.on(cc.Node.EventType.MOUSE_DOWN, this.onClickBtn);
        else {
            var t = new cc.Component.EventHandler();
            t.component = "Tip";
            t.target = this.node;
            t.handler = "onClickBtn";
            this.btn.clickEvents.push(t);
        }
    },
    onClickBtn() {
        if (0 != this.itemId) {
            var t = new n.ItemSlotData();
            t.id = this.itemId;
            t.kind = 1;
            i.utils.openPrefabView("ItemInfo", !1, t);
        } else i.stringUtil.isBlank(this.toolTip) || i.alertUtil.alert18n(this.toolTip);
    },

    onRefreshItem(itemid){
        this.itemId = itemid;
    },
});
