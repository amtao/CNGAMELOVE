var i = require("List");
var n = require("Initializer");
var l = require("Utils");
cc.Class({
    extends: cc.Component,
    properties: {
        list: i,
    },
    ctor() {
        this._curId = 0;
    },
    onLoad() {
        facade.subscribe(n.dalishiProxy.UPDATE_DALISHI_KILL20LOG, this.onUpdateMsg, this);
        facade.subscribe("DALISI_SERVANT_SELECT", this.onServantSelect, this);
        this.onUpdateMsg();
    },
    onServantSelect(t) {
        if (0 != this._curId) {
            var e = l.utils.getParamInt("gongdou_attack_id");
            if (n.bagProxy.getItemCount(e) < 1) {
                l.alertUtil.alertItemLimit(e);
                return;
            }
            n.dalishiProxy.sendTiaoZhan(this._curId, t);
            this._curId = 0;
            facade.send("BOT_EXTEND_HIDE");
        }
    },
    onClickChangle(t, e) {
        if (n.dalishiProxy.isHaveFight()) l.alertUtil.alert18n("YAMUN_HAVE_PLAYING_HERO");
        else {
            var o = e.data;
            if (o) {
                this._curId = o.user.uid;
                facade.send("BOT_EXTEND_HIDE");
                l.utils.openPrefabView("dalishi/DServantSelect", !1, {
                    id: l.utils.getParamInt("gongdou_attack_id")
                });
            }
        }
    },
    onUpdateMsg() {
        this.node.active = null != n.dalishiProxy.kill20Log && n.dalishiProxy.kill20Log.length > 0;
        this.list.data = n.dalishiProxy.kill20Log;
    },
});
