var i = require("Initializer");
var n = require("RenderListItem");
var l = require("Utils");
var r = require("UserHeadItem");
var a = require("ChengHaoItem");
var s = require("Config");
var c = require("TimeProxy");
var UrlLoad = require("UrlLoad");
cc.Class({
    extends: n,
    properties: {
        lblName: cc.Label,
        lblShili: cc.Label,
        lblLv: cc.Label,
        lblGX: cc.Label,
        lbAlllGX: cc.Label,
        lblTime: cc.Label,
        imgRank: cc.Sprite,
        ranks: [cc.SpriteFrame],
        btnApply: cc.Button,
        nodeChange: cc.Node,
        head: UrlLoad,
        chengHao: a,
        image1:cc.Node,
        image2:cc.Node,
        postn:cc.Label,
    },
    ctor() {},
    onLoad() {
        this.btnApply && this.btnApply.clickEvents && this.btnApply.clickEvents.length > 0 && (this.btnApply.clickEvents[0].customEventData = this);
    },
    showData() {
        var t = this._data;
        if (t) {
            if (s.Config.isShowChengHao && c.funUtils.isOpenFun(c.funUtils.chenghao)) {
                var e = localcache.getItem(localdb.table_fashion, t.chenghao);
                //this.chengHao.data = e;
            }
            i.unionProxy.tmp
            this.nodeChange && (this.nodeChange.active = i.unionProxy.memberInfo.post <= 2 && i.unionProxy.memberInfo.post < t.post);
            0 == t.sex || t.sex,
            parseInt(t.job + "");
            this.lblName.string = t.name  
            this.postn.string = t.post<=3?"【" + i.unionProxy.getPostion(t.post) + "】":"";
            this.lblShili.string = l.utils.formatMoney(t.shili);
            var o = localcache.getItem(localdb.table_officer, t.level);
            ////this.lblLv.string = o ? o.name: "";
            this.lblGX.string = t.allGx + "";
            this.lbAlllGX.string = t.allGx + "";
            this.lblTime.string = l.timeUtil.getDateDiff(t.loginTime);
            i.playerProxy.loadUserHeadPrefab(this.head,t.headavatar,{job:t.job,level:t.level,clothe:t.clothe},false); 

            this.image1.active = t.post === 1
            this.image2.active = t.post === 2 && i.unionProxy.tmp[i.playerProxy.userData.uid].post > 1
            this.btnApply.node.active = (this.image1.active || this.image2.active)? false:i.unionProxy.tmp[i.playerProxy.userData.uid].post>2?false:true
        }
    },
    onClickHead() {
        var t = this._data;
        t && i.playerProxy.sendGetOther(t.id);
    },
});
