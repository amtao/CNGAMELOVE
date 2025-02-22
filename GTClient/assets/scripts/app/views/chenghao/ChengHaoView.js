var i = require("Utils");
var n = require("ChengHaoItem");
var l = require("List");
var r = require("Initializer");
cc.Class({
    extends: cc.Component,
    properties: {
        curChengHao: n,
        list: l,
        wuNode: cc.Node,
        chenghaoNode: cc.Node,
        btnDel: cc.Node,
    },
    ctor() {
        this.curChengHaoId = 0;
        this.chlist = [];
    },
    onLoad() {
        facade.subscribe(r.chengHaoProxy.UPDATE_CHENGHAO_INFO, this.onChInfo, this);
        facade.subscribe(r.playerProxy.PLAYER_USER_UPDATE, this.updateCurrent, this);
        this.onChInfo();
    },
    onClickClost() {
        i.utils.closeView(this, !0);
    },
    onOffChengHao() {
        this.curChengHaoId > 0 ? r.chengHaoProxy.offChengHao(this.curChengHaoId) : i.alertUtil.alert18n("TITLE_RESET_LOSE");
    },
    onChInfo() {
        this.chlist = [];
        for (var t = 0; t < r.chengHaoProxy.chList.length; t++) {
            var e = r.chengHaoProxy.chList[t],
            o = localcache.getItem(localdb.table_fashion, e.chid + "");
            null != o && (r.chengHaoProxy.isShow(o.show_time, o.show_avid) && this.chlist.push(e));
        }
        this.chlist.sort(r.chengHaoProxy.sortCh);
        this.list.data = this.chlist;
        this.updateCurrent();
    },
    updateCurrent() {
        var t = localcache.getItem(localdb.table_fashion, r.playerProxy.userData.chenghao);
        this.curChengHao.data = t;
        this.curChengHaoId = t ? t.id: 0;
        this.chenghaoNode.active = this.curChengHaoId > 0;
        this.wuNode.active = !this.chenghaoNode.active;
        this.list.data = this.chlist;
        this.btnDel.active = this.curChengHaoId > 0;
    },
});
