var i = require("Initializer");
var n = require("RenderListItem");
var l = require("Utils");
var r = require("UserHeadItem");
var a = require("ChengHaoItem");
cc.Class({
    extends: n,
    properties: {
        lblName: cc.Label,
        lblShili: cc.Label,
        lblLv: cc.Label,
        lblGX: cc.Label,
        lblWeekGX: cc.Label,
        lblTime: cc.Label,
        headBgSpr: cc.Sprite,
        headBgFrames: [cc.SpriteFrame],
        btnApply: cc.Button,
        nodeChange: cc.Node,
        head: r,
        chengHao: a,
    },
    ctor() {},
    onLoad() {
        this.btnApply && this.btnApply.clickEvents && this.btnApply.clickEvents.length > 0 && (this.btnApply.clickEvents[0].customEventData = this);
    },
    showData() {
        var t = this._data;
        if (t) {
            // if (s.Config.isShowChengHao && c.funUtils.isOpenFun(c.funUtils.chenghao)) {
            //     var e = localcache.getItem(localdb.table_fashion, t.chenghao);
            //     this.chengHao.data = e;
            // }
            this.nodeChange && (this.nodeChange.active = i.unionProxy.memberInfo.post <= 2 && i.unionProxy.memberInfo.post < t.post);
            this.chengHao.node.active = false;
            this.headBgSpr.spriteFrame = this.headBgFrames[t.post == 1 ? 0 : 1];
            0 == t.sex || t.sex,
            parseInt(t.job + "");
            this.lblName.string = t.name + "(" + i.unionProxy.getPostion(t.post) + ")";
            this.lblShili.string = l.utils.formatMoney(t.shili);
            var o = localcache.getItem(localdb.table_officer, t.level);
            this.lblLv.string = o ? o.name: "";
            this.lblGX.string = l.utils.formatMoney(t.allGx);
            this.lblWeekGX.string = l.utils.formatMoney(t.gx);
            this.lblTime.string = i18n.t("LOGIN_TIME") + l.timeUtil.getDateDiff(t.loginTime);
            this.head.setUserHead(t.job, t.headavatar);
        }
    },
    onClickHead() {
        var t = this._data;
        t && i.playerProxy.sendGetOther(t.id);
    },
});
