let scUtils = require("Utils");
let scList = require("List");
let scInitializer = require("Initializer");

cc.Class({
    extends: cc.Component,

    properties: {
        lbCurTicket: cc.Label,
        lbCurLeaf: cc.Label,
        processList: scList,
        nodeBtn:cc.Node,
    },

    onLoad () {
        var openParam = this.node.openParam;
        if (openParam && openParam.hideBtn){
            this.nodeBtn.active = false;
        }
        let businessInfo = scInitializer.businessProxy.businessInfo;
        this.lbCurTicket.string = businessInfo.AgTicket;
        this.lbCurLeaf.string = businessInfo.goldLeaf;
        this.processList.data = localcache.getList(localdb.table_jiangli);
    },

    onClickClost: function() {
        scUtils.utils.closeView(this, !0);
    },

    onClickSubmitOrder() {
        let proxy = scInitializer.businessProxy;
        let self = this;

        let getLeaf = proxy.getCurLeafNum(),
            list = localcache.getList(localdb.table_jiangli),
            bHasTicked = proxy.businessInfo.AgTicket > 0,
            bNotFinished = list[list.length - 1].set >= getLeaf;

        let submitFunc = () => {
            proxy.isFinished = true;
            proxy.sendPickFinalAward((data) => {
                scUtils.utils.closeNameView("xingshang/UIBusinessCityInfo");
                scUtils.utils.openPrefabView("xingshang/BusinessOverView", !1, {
                    leaf: getLeaf,
                    data: null != data.a.msgwin && null != data.a.msgwin.items ? data.a.msgwin.items : null
                });
            });
            self.onClickClost();
        }

        if(bHasTicked && bNotFinished) {
            scUtils.utils.showConfirm(i18n.t(bHasTicked ? "BUSINESS_HAVE_TICKET" : "BUSINESS_NOT_FINISHED"), submitFunc);
        } else {
            submitFunc();
        }
    },
    
});
