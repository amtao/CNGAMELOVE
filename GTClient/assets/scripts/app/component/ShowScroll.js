var i = require("UIUtils");
cc.Class({
    extends: cc.Component,
    properties: {
        scroll: cc.ScrollView,
        showLeftNode: cc.Node,
        showRightNode: cc.Node,
    },
    ctor() {
        this.showRang = 50;
    },
    onLoad() {
        if (null != this.scroll && (null != this.showLeftNode || null != this.showRightNode)) {
            i.uiUtils.scaleRepeat(this.showLeftNode, 0.9, 1.2);
            i.uiUtils.scaleRepeat(this.showRightNode, 0.9, 1.2);
            this.showLeftNode.active = !1;
            this.showRightNode.active = !1;
            var t = new cc.Component.EventHandler();
            t.component = "ShowScroll";
            t.target = this.node;
            t.handler = "onScroll";
            this.scroll.scrollEvents.push(t);
            this.onScroll();
            facade.subscribe("GUIDE_MOVE_ITEM", this.onMoveItem, this);
        }
    },
    onMoveItem(t) {
        var e = this.scroll.getScrollOffset();
        this.scroll.scrollToOffset(new cc.Vec2(Math.abs(e.x) + t, e.y));
        this.onScroll();
    },
    onClickMove(t, e) {
        var o = parseInt(e);
        this.scroll.horizontal ? 1 == o ? this.scroll.scrollToRight() : this.scroll.scrollToLeft() : 1 == o ? this.scroll.scrollToBottom() : this.scroll.scrollToTop();
        this.onScroll();
    },
    onScroll() {
        var t = this.scroll.getScrollOffset();
        if (this.scroll.horizontal) {
            this.showLeftNode.active = Math.abs(t.x) > this.showRang;
            this.showRightNode.active = Math.abs(t.x) + this.showRang < Math.abs(this.scroll.getMaxScrollOffset().x);
        } else {
            this.showLeftNode.active = Math.abs(t.y) > this.showRang;
            this.showRightNode.active = Math.abs(t.y) + this.showRang < Math.abs(this.scroll.getMaxScrollOffset().y);
        }
    },
});
