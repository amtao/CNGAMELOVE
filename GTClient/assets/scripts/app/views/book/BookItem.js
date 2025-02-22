var i = require("RenderListItem");
var n = require("UrlLoad");
var l = require("UIUtils");
var r = require("Utils");
var a = require("Initializer");
cc.Class({
    extends: i,
    properties: {
        nTimeBg: cc.Node,
        lblTime:cc.Label,
        nodeTip:cc.Node,
        urlLoad:n,
        nodeSpeed:cc.Node,
    },

    onClickSelect() {
        var t = this._data;
        t && (0 == t.hid || null == t.hid ? r.utils.openPrefabView("book/BookSelectView", !1, t) : t.cd && t.cd.next <= r.timeUtil.second ? a.bookProxy.sendOver(t.id) : r.alertUtil.alert18n("BOOK_TIME_LIMIT"));
    },

    onLoad() {
        this.defaultServantY = this.urlLoad.node.position.y;
    },

    showData() {
        var t = this._data;
        if (t) {            
            this.nodeTip.active = 0 == t.hid || null == t.hid;
            this.urlLoad.node.active = this.nTimeBg.active = this.lblTime.node.active = !this.nodeTip.active;
            this.lblTime.node.active && 0 != t.cd.next && t.cd.next > r.timeUtil.second ? l.uiUtils.countDown(t.cd.next, this.lblTime,
            function() {
                facade.send(a.bookProxy.UPDATE_BOOK_LIST);
            },
            !0) : this.lblTime.unscheduleAllCallbacks();
            t.cd.next <= r.timeUtil.second && (this.lblTime.string = i18n.t("ACHIEVE_OVER"));
            this.urlLoad.loadHandle = () => {
                this.servantAnchorYPos(this.urlLoad);              
            };
            this.urlLoad.url = 0 != t.hid ? l.uiHelps.getServantSmallSpine(t.hid) : "";
            this.nodeSpeed.active = 0 != t.cd.next && t.cd.next > r.timeUtil.second;
        }
    },

    servantAnchorYPos(urlLoadComp) {
        if(urlLoadComp.node.anchorY == 1 && urlLoadComp.content != null) {
            urlLoadComp.node.position = cc.v2(urlLoadComp.node.position.x, this.defaultServantY-urlLoadComp.content.height*urlLoadComp.node.scale);        
        } 
    },

    /**点击加速完成历练*/
    onClickSpeed(){
        let count = a.bookProxy.lilianCount;
        if (count == null) count = 0;
        let cfg = localcache.getFilter(localdb.table_cd,"type",1,"set",count + 1);
        if (cfg){
            let cdata =this._data;
            let num = cfg.cost[0].count
            let vipcfg = localcache.getItem(localdb.table_vip,a.playerProxy.userData.vip);
            let max = vipcfg.shuyuancd;
            if (count >= max){
                // unlock recharge and vip --2020.07.21
                // r.alertUtil.alert(i18n.t("CHILD_LILIANSPEED"));
                r.utils.showConfirm(i18n.t("CHILD_LILIANSPEEDDES2"), () => {
                    r.utils.openPrefabView("welfare/RechargeView");
                }, null, null, i18n.t("COMMON_YES"), i18n.t("COMMON_NO"), () => {
                    
                });
                return;
            }
            r.utils.showConfirmItem(i18n.t("CHILD_LILIANSPEEDDES3", {
                value: num,
                f:max - count,
                s:max,
            }), cfg.cost[0].id,num,
            function() {
                a.playerProxy.userData.cash < num ? r.alertUtil.alertItemLimit(1) : a.bookProxy.sendSpeedFinish(cdata.id);
            },
            "COMMON_YES");
        }       
        
    },
});
