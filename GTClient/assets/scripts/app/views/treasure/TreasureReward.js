var i = require("ItemSlotUI");
var n = require("UIUtils");
var l = require("Utils");
var r = require("Initializer");
cc.Class({
    extends: cc.Component,
    properties: {
        item: i,
        nodeTake: cc.Node,
        nodeLook: cc.Node,
        nodeGroup: cc.Node,
        lblName: cc.Label,
        lblScore: cc.Label,
    },
    ctor() {
        this.curItem = null;
    },
    onLoad() {
        facade.subscribe("TREASURE_NEXT_REWARD", this.showNext, this);
        this.showNext();
    },
    getNextData() {
        for (var t = null,
        e = l.utils.getParamInt("treasure_com_item"), o = 0; o < r.treasureProxy.tGroupList.length; o++) {
            for (var i = r.treasureProxy.tGroupList[o], n = 0, a = 0; a < i.items.length; a++) {
                var s = i.items[a];
                if (r.bagProxy.getItemCount(s.data.itemid) > 0) {
                    t = s.data;
                    break;
                }
                if (r.bagProxy.getItemCount(s.data.tagid) >= s.data.tagnum && (0 == s.rwd || s.data.tagid != e)) {
                    t = s.data;
                    break;
                }
                n += 1 == s.rwd ? 1 : 0;
            }
            n >= i.items.length && 0 == i.rwd && (t = i.data);
        }
        return t;
    },
    showNext() {
        this.curItem = this.getNextData();
        if (null != this.curItem) {
            l.utils.showEffect(this, 0);
            if (this.curItem) {
                var t = new n.ItemSlotData();
                if (this.curItem.number) {
                    this.nodeLook.active = this.nodeTake.active = !1;
                    this.nodeGroup.active = !0;
                    t.id = this.curItem.photo;
                    this.item.data = t;
                    this.lblName.string = this.curItem.name;
                    this.lblScore.string = "";
                } else {
                    var e = r.bagProxy.getItemCount(this.curItem.itemid) <= 0;
                    this.nodeTake.active = !e;
                    this.nodeLook.active = e;
                    this.nodeGroup.active = !1;
                    t.id = this.curItem.itemid;
                    this.item.data = t;
                    var o = r.treasureProxy.tItems[this.curItem.itemid],
                    i = o && 1 == o.rwd;
                    this.lblScore.string = i18n.t("TREASURE_SCORE_ADD", {
                        v: i ? this.curItem.twopoints: this.curItem.points
                    });
                }
            }
        } else l.utils.closeView(this);
    },
    onClickGroup() {
        r.treasureProxy.sendRwd(this.curItem.id);
    },
    onClickPost() {
        r.treasureProxy.sendTreasure(this.curItem.id);
    },
    onClickLook() {
        r.treasureProxy.sendClipTrea(this.curItem.id);
    },
});
