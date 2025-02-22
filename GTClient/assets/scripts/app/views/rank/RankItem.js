var i = require("RenderListItem");
var n = require("Utils");
var l = require("Initializer");
var r = require("UserHeadItem");
var a = require("TimeProxy");
var s = require("ChengHaoItem");
var c = require("Config");
var UrlLoad = require("UrlLoad");
cc.Class({
    extends: i,
    properties: {
        lblName: cc.Label,
        lblLv: cc.Label,
        lblRank: cc.Label,
        lblContent: cc.Label,
        headImg: UrlLoad,
        lblNoChenghao: cc.Node,
        chengHao: s,
        lblJiBanNum:cc.Label,
    },
    ctor() {},
    onClickItem() {
        var t = this.data;
        t && (t.uid == l.playerProxy.userData.uid ? a.funUtils.openView(a.funUtils.userView.id) : l.playerProxy.sendGetOther(t.uid));
    },
    showData() {
        var t = this._data;
        if (t) {
            if (c.Config.isShowChengHao && a.funUtils.isOpenFun(a.funUtils.chenghao)) {
                var e = localcache.getItem(localdb.table_fashion, t.chenghao);
                this.chengHao.data = e;
                this.lblNoChenghao.active = !e;
            }
            var o = localcache.getItem(localdb.table_officer, t.level);
            this.lblName.string = t.name;
            this.lblLv.string = o ? o.name: "";
            this.lblLv.node.active = true;
            this.lblJiBanNum.node.active = false;
            l.rankProxy.isShowGuanKa ? (this.lblContent.string = l.rankProxy.getGuankaString(t.num)) : (this.lblContent.string = i18n.t("MAIN_SHILI", {
                d: n.utils.formatMoney(t.num)
            }));
            1 == l.rankProxy.showRankType ? (this.lblContent.string = i18n.t("MAIN_SHILI", {
                d: n.utils.formatMoney(t.num)
            })) : 2 == l.rankProxy.showRankType ? (this.lblContent.string = l.rankProxy.getGuankaString(t.num)) : 3 == l.rankProxy.showRankType && (this.lblContent.string = i18n.t("RANK_TIP_4",{v1:t.fetterlv}));
            this.lblRank.string = t.rid + "";
            if (3 == l.rankProxy.showRankType){
                this.lblJiBanNum.string = "" + t.num;
                this.lblLv.node.active = false;
                this.lblJiBanNum.node.active = true;
            }
            l.playerProxy.loadUserHeadPrefab(this.headImg,t.headavatar,{job:t.job,level:t.level,clothe:t.clothe},false); 
            //this.headImg && this.headImg.setUserHead(t.job, t.headavatar);
        }
    },
});
