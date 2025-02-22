var i = require("List");
var l = require("Utils");
var r = require("Initializer");
var a = require("UserSuitPart");
var s = require("UIUtils");
var c = require("BagProxy");
var _ = require("UrlLoad");
var d = require("ItemSlotUI");
cc.Class({
    extends: cc.Component,
    properties: {
        list: i,
        listProp: i,
        role: _,
        lblName: cc.Label,
        parts: [a],
        nodeBtn: cc.Node,
        nodeParts: cc.Node,
        bgurl: _,
        nodeMax: cc.Node,
        nodeUp: cc.Node,
        listNext: i,
        lblNext: cc.Label,
        cost: d,
    },
    ctor() {},
    onLoad() {
        facade.subscribe("UI_TOUCH_MOVE_LEFT", this.onClickLeft, this);
        facade.subscribe("UI_TOUCH_MOVE_RIGHT", this.onClickRight, this);
        facade.subscribe(r.playerProxy.PLAYER_CLOTH_SUIT_LV, this.updateShow, this);
        var t = this;
        this.list.selectHandle = function() {
            t.updateShow();
        };
        for (var e = localcache.getList(localdb.table_usersuit), o = [], i = 0; i < e.length; i++) {
            if (e[i].show_time && "0" != e[i].show_time) {
                if (l.timeUtil.str2Second(e[i].show_time) > l.timeUtil.second && !r.limitActivityProxy.isHaveTypeActive(e[i].show_avid)) continue;
            } else if (!r.limitActivityProxy.isHaveTypeActive(e[i].show_avid)) continue;
            o.push(e[i]);
        }
        this.list.data = o;
        this.list.selectIndex = 0;
        this.nodeBtn.active = this.list.data && this.list.data.length > 1;
        for (i = 0; i < this.parts.length; i++) s.uiUtils.floatPos(this.parts[i].node, 0, 10, 2 * Math.random() + 2);
        this.updateShow();
    },
    onClickLeft(t) {
        this.onClickAdd(null, -1);
    },
    onClickRight(t) {
        this.onClickAdd(null, 1);
    },
    onClickAdd(t, e) {
        if (this.nodeBtn.active) {
            var o = parseInt(e),
            i = this.list.selectIndex,
            n = this.list.data.length;
            i = (i = (i += o) < 0 ? n - 1 : i) >= n ? 0 : i;
            this.list.selectIndex = i;
        }
    },
    onClickBody() {
        this.nodeParts.active = !this.nodeParts.active;
    },
    updateShow() {
        var t = this.list.selectData;
        if (t) {
            var e = r.playerProxy.userData,
            o = {};
            this.lblName.string = t.name;
            this.nodeParts.active = !1;
            for (var i = 0; i < this.parts.length; i++) this.parts[i].data = null;
            var n = !0;
            for (i = 0; i < t.clother.length; i++) {
                var a = localcache.getItem(localdb.table_userClothe, t.clother[i]),
                _ = new s.ItemSlotData();
                _.kind = c.DataType.CLOTHE;
                _.id = a.id;
                n = n && r.playerProxy.isUnlockCloth(a.id);
                switch (a.part) {
                case 1:
                    o.head = a.id;
                    this.parts[0].data = _;
                    break;
                case 2:
                    o.body = a.id;
                    this.parts[1].data = _;
                    break;
                case 3:
                    o.ear = a.id;
                    this.parts[2].data = _;
                    break;
                case 4:
                    o.background = a.id;
                    this.parts[3].data = _;
                    break;
                case 5:
                    o.effect = a.id;
                    this.parts[4].data = _;
                    break;
                case 6:
                    o.animal = a.id;
                    this.parts[5].data = _;
                }
            }
            this.bgurl.node.active = null != this.parts[3].data;
            if (this.bgurl.node.active) {
                var d = localcache.getItem(localdb.table_userClothe, this.parts[3].data.id);
                d && (this.bgurl.url = s.uiHelps.getStoryBg(d.model));
            }
            r.playerProxy.loadPlayerSpinePrefab(this.role,{job:e.job,level:e.level,o});
            var u = r.playerProxy.getSuitLv(t.id),
            p = localcache.getItem(localdb.table_userSuitLv, 1e3 * t.lvup + u),
            h = localcache.getItem(localdb.table_userSuitLv, 1e3 * t.lvup + u + 1);
            this.listProp.data = p ? p.ep: null;
            this.nodeMax.active = null == h;
            this.nodeUp.active = null != h && n;
            if (this.nodeUp.active && null != p) {
                this.lblNext.string = i18n.t("USER_SUIT_UP_ADD", {
                    d: u
                });
                this.listNext.data = h ? h.ep: null;
                this.cost.data = {
                    id: l.utils.getParamInt("clother_item"),
                    count: p.cost
                };
            }
        }
    },
    onClickUp() {
        var t = this.list.selectData;
        if (t) {
            var e = l.utils.getParamInt("clother_item"),
            o = r.playerProxy.getSuitLv(t.id),
            i = localcache.getItem(localdb.table_userSuitLv, 1e3 * t.lvup + o),
            n = r.bagProxy.getItemCount(e);
            l.utils.showConfirmItem(i18n.t("USER_SUIT_LV_CONFIRM", {
                n: r.playerProxy.getKindIdName(1, e),
                d: i.cost
            }), e, n,
            function() {
                n < i.cost ? l.alertUtil.alertItemLimit(e) : r.playerProxy.sendSuitLv(t.id);
            },
            "USER_SUIT_LV_CONFIRM");
        }
    },
    onClickClost() {
        l.utils.closeView(this);
    },
});
