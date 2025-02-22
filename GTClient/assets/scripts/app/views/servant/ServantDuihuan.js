var i = require("UrlLoad");
var n = require("ItemSlotUI");
var l = require("List");
var r = require("Utils");
var a = require("ServantStarShow");
var s = require("Initializer");
var c = require("UIUtils");
var _ = require("TimeProxy");
cc.Class({
    extends: cc.Component,
    properties: {
        lblName: cc.Label,
        stateImg: a,
        url: i,
        itemslot: n,
        lblCount: cc.Label,
        lblRemain: cc.Label,
        lblZZ: cc.Label,
        list: l,
        nodeLeft: cc.Node,
        nodeRight: cc.Node,
        imgSpe1: i,
        imgSpe2: i,
        nodeBtn: cc.Node,
        nodeGo: cc.Node,
        nodeGeted: cc.Node,
        nodeLeader: cc.Node,
        lblLeader: cc.Label,
    },
    ctor() {
        this._curIndex = 0;
    },
    onLoad() {
        null == s.limitActivityProxy.duihuan ? s.limitActivityProxy.sendLookActivityData(s.limitActivityProxy.DUIHUAN_ID) : this.updateShow();
        var t = s.limitActivityProxy.getHuodongList(s.limitActivityProxy.DUIHUAN_ID)[0];
        c.uiUtils.countDown(t.eTime, this.lblRemain, null, !0, "USER_REMAIN_TIME", "d");
        facade.subscribe(s.limitActivityProxy.UPDATE_DUIHUAN_HUODONG, this.updateShow, this);
        facade.subscribe(s.bagProxy.UPDATE_BAG_ITEM, this.updateShow, this);
        facade.subscribe(s.servantProxy.SERVANT_UP, this.updateShow, this);
        s.trunTableProxy.sendOpen();
    },
    updateShow() {
        this.showSelect();
    },
    onClickSelect(t, e) {
        var o = parseInt(e),
        i = s.limitActivityProxy.duihuan.rwd.length;
        this._curIndex += o;
        this._curIndex = this._curIndex < 0 ? 0 : this._curIndex;
        this._curIndex = this._curIndex > i - 1 ? i - 1 : this._curIndex;
        this.showSelect();
    },
    onClickDui() {
        if (null != s.limitActivityProxy.duihuan) {
            var t = s.limitActivityProxy.duihuan.rwd[this._curIndex];
            if (t) {
                if (s.bagProxy.getItemCount(t.itemid) < t.need) {
                    r.alertUtil.alertItemLimit(t.itemid);
                    return;
                }
                s.limitActivityProxy.sendGetActivityReward(s.limitActivityProxy.DUIHUAN_ID, 0 == t.heroid ? t.wifeid + 200 : t.heroid);
            }
        }
    },
    onClickGo() {
        s.limitActivityProxy.duihuan.open != _.funUtils.trunTable.id || s.limitActivityProxy.isHaveTypeActive(s.limitActivityProxy.TRUN_TABLE_ID) ? _.funUtils.openView(s.limitActivityProxy.duihuan.open) : r.alertUtil.alert18n("TRUN_TABLE_IS_END");
    },
    showSelect() {
        if (null != s.limitActivityProxy.duihuan) {
            var t = s.limitActivityProxy.duihuan.rwd.length;
            this.nodeLeft.active = this._curIndex > 0;
            this.nodeRight.active = this._curIndex < t - 1;
            var e = s.limitActivityProxy.duihuan.rwd[this._curIndex];
            this.itemslot.data = {
                id: e.itemid
            };
            var o = s.bagProxy.getItemCount(e.itemid);
            this.lblCount.string = i18n.t("COMMON_NUM", {
                f: o,
                s: e.need
            });
            if (e) {
                var i = e.heroid,
                n = !1;
                if (0 != i) {
                    var l = localcache.getItem(localdb.table_hero, i);
                    if (l) {
                        n = null != s.servantProxy.getHeroData(i);
                        this.url.url = c.uiHelps.getServantSpine(i);
                        this.lblName.string = l.name;
                        for (var r = 0,
                        a = {
                            p1: 0,
                            p2: 0,
                            p3: 0,
                            p4: 0
                        },
                        _ = [], d = 0; d < l.skills.length; d++) {
                            var u = localcache.getItem(localdb.table_epSkill, l.skills[d].id);
                            a["p" + u.ep] += 10 * u.star;
                            r += u.star;
                            var p = {};
                            p.id = u.sid;
                            p.level = p.hlv = 0;
                            _.push(p);
                        }
                        this.lblZZ.string = i18n.t("SERVANT_PROP_TOTAL", {
                            value: r
                        });
                        this.stateImg.node.active = !0;
                        this.stateImg.setValue(l.star);
                        this.imgSpe1.node.active = !0;
                        this.imgSpe1.url = c.uiHelps.getLangSp(l.spec[0]);
                        this.imgSpe2.node.active = l.spec.length > 1;
                        l.spec.length > 1 && (this.imgSpe2.url = c.uiHelps.getLangSp(l.spec[1]));
                        this.list.data = _;
                        this.nodeLeader.active = 0 != l.leaderid;
                        this.lblLeader.string = s.servantProxy.getLeadActivieStr(l.heroid);
                    }
                }
                var h = e.wifeid;
                if (0 != h) {
                    var y = localcache.getItem(localdb.table_wife, h);
                    if (y) {
                        n = null != s.wifeProxy.getWifeData(h);
                        this.url.url = c.uiHelps.getWifeBody(y.res);
                        this.lblName.string = y.wname2;
                        this.lblZZ.string = "";
                        this.stateImg.node.active = !1;
                        this.imgSpe1.node.active = !1;
                        this.imgSpe2.node.active = !1;
                        this.list.data = [];
                    }
                }
                this.nodeBtn.active = this.itemslot.node.active = !n;
                this.nodeGo.active = s.limitActivityProxy.duihuan.open && 0 != s.limitActivityProxy.duihuan.open;
                this.nodeBtn.x = this.nodeGeted.x = this.nodeGo.active ? -100 : 0;
            }
        }
    },
    onClickClost() {
        r.utils.closeView(this);
    },
});
