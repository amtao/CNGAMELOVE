var i = require("RenderListItem");
var n = require("Initializer");
var l = require("Utils");
var r = require("UIUtils");
var a = require("ChildSpine");
var s = require("ServantStarShow");
var c = require("List");
cc.Class({
    extends: i,
    properties: {
        lblName: cc.Label,
        lblTime: cc.Label,
        lockNode: cc.Node,
        timeNode: cc.Node,
        rewardNode: cc.Node,
        selectNode: cc.Node,
        mailNode: cc.Node,
        roleImg: a,
        nameNode: cc.Node,
        yunNode: cc.Node,
        roleSmallImg: a,
        lblPrice: cc.Label,
        costNode: cc.Node,
        starNode: cc.Node,
        stars: s,
        list: c,
        nodeSpeed:cc.Node,
    },
    ctor() {},
    showData() {
        var t = this,
        e = this._data.data,
        o = parseInt(this._data.id);
        if (o <= n.sonProxy.lilianSeat.desk) {
            this.costNode.active = !1;
            this.lockNode.active = !1;
            if (null != e && 0 != e.sid) {
                var i = n.sonProxy.getSon(e.sid);
                this.lblName.string = i.name;
                r.uiUtils.countDown(e.cd.next, this.lblTime,
                function() {
                    i.cd.label = "lilian";
                    n.playerProxy.sendAdok(i.cd.label);
                    t.lblTime.string = "00:00:00";
                });
                this.rewardNode.active = 0 == e.cd.next;
                this.timeNode.active = e.cd.next > 0;
                this.nodeSpeed.active = e.cd.next > 0;
                this.selectNode.active = !1;
                //this.mailNode.active = 0 != e.msgId;
                this.roleImg.node.active = i.state > 3;
                this.roleSmallImg.node.active = i.state <= 3;
                i.state > 3 ? this.roleImg.setKid(i.id, i.sex) : this.roleSmallImg.setKid(i.id, i.sex, !1);
                //this.nameNode.active = this.yunNode.active = !0;
                this.starNode.active = !0;
                this.stars.setValue(i.talent);
                this.list.node.x = -this.list.node.width / 2;
            } else {
                this.timeNode.active = !1;
                this.selectNode.active = !0;
                //this.mailNode.active = !1;
                this.lblName.string = "";
                this.roleImg.clearKid();
                this.roleImg.node.active = !1;
                this.roleSmallImg.clearKid();
                this.roleSmallImg.node.active = !1;
                //this.nameNode.active = this.yunNode.active = !1;
                this.rewardNode.active = !1;
                this.starNode.active = !1;
                this.nodeSpeed.active = false;
            }
        } else {
            this.lblName.string = i18n.t("JINGYING_WEIJIESUO");
            this.lockNode.active = !0;
            this.timeNode.active = !1;
            this.selectNode.active = !1;
            //this.mailNode.active = !1;
            this.roleImg.clearKid();
            this.roleImg.node.active = !1;
            this.roleSmallImg.clearKid();
            this.roleSmallImg.node.active = !1;
            this.rewardNode.active = !1;
            var l = localcache.getItem(localdb.table_practiceSeat, n.sonProxy.lilianSeat.desk + 1);
            this.lblPrice.string = l.cost + "";
            //n.sonProxy.lilianList.length;
            this.costNode.active = o == n.sonProxy.lilianSeat.desk + 1;
            this.starNode.active = !1;
            this.nodeSpeed.active = false;
        }
    },
    onClickFeige() {
        l.utils.openPrefabView("feige/FeigeView", null, {
            flag: !0
        });
    },

    /**点击加速完成历练*/
    onClickSpeed(){
        let count = n.sonProxy.lilianCount;
        if (count == null) count = 0;
        let cfg = localcache.getFilter(localdb.table_cd,"type",2,"set",count + 1);
        if (cfg){
            let cdata =this._data.data;
            let num = cfg.cost[0].count
            let vipcfg = localcache.getItem(localdb.table_vip,n.playerProxy.userData.vip);
            let max = vipcfg.lilian;
            if (count >= max){
                // unlock recharge and vip --2020.07.21
                // l.alertUtil.alert(i18n.t("CHILD_LILIANSPEED"));
                l.utils.showConfirm(i18n.t("CHILD_LILIANSPEEDDES2"), () => {
                    l.utils.openPrefabView("welfare/RechargeView");
                }, null, null, i18n.t("COMMON_YES"), i18n.t("COMMON_NO"), () => {
                    
                });
                return;
            }
            l.utils.showConfirmItem(i18n.t("CHILD_LILIANSPEEDDES", {
                value: num,
                f:max - count,
                s:max,
            }), cfg.cost[0].id,num,
            function() {
                n.playerProxy.userData.cash < num ? l.alertUtil.alertItemLimit(1) : n.sonProxy.sendSpeedFinish(cdata.sid,cdata.id);
            },
            "COMMON_YES");
        }       
        
    },
});
