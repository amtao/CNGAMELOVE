var scInitializer = require("Initializer");
var scItemSlotUI = require("ItemSlotUI");
var scRenderListItem = require("RenderListItem");
var scUtils = require("Utils");

cc.Class({
    extends: scRenderListItem,

    properties: {
        lblRich1: cc.Label,
        lblRich2: cc.Label,
        lblNeedRich1: cc.Label,
        lblNeedRich2: cc.Label,
        itemSlot1: scItemSlotUI,
        itemSlot2: scItemSlotUI,
    },

    ctor() {},

    onLoad() {
        let data = localcache.getItem(localdb.table_unionBoss, 1);
        this.lblNeedRich1.string = data.payFund;
        this.lblNeedRich2.string = data.payDia;
        this.payFund = data.payFund;
        this.payDia = data.payDia;

        this.updateItem();

        this.itemSlot1.data = { id: 117, count: data.payFund };
        this.itemSlot1.showData();

        this.itemSlot2.data = { id: 1, count: data.payDia };
        this.itemSlot2.showData();

        facade.subscribe(scInitializer.bagProxy.UPDATE_BAG_ITEM, this.updateItem, this);
    },

    updateItem: function() {
        this.lblRich1.string = scInitializer.unionProxy.clubInfo.fund + "";
        this.lblRich2.string = scInitializer.playerProxy.userData.cash + "";
    },

    eventClose: function() {
        scUtils.utils.closeView(this);
    },

    //type: 1.资金开启 2.元宝开启
    onClickOpen: function(event, type) {
        var iType = parseInt(type);
        if (1 == iType && scInitializer.playerProxy.userData.cash < this.payDia) {
            scUtils.alertUtil.alertItemLimit(1);
            return;
        } else if (2 == iType && scInitializer.unionProxy.clubInfo.fund < this.payFund) {
            scUtils.alertUtil.alert(i18n.t("union_nofund"));
            return;
        }
        let openTime = scUtils.utils.getParamStr("club_bossOpenTime").split("|");
        let endTime = scUtils.utils.getParamStr("club_bossEndTime").split("|");
        let now = new Date(scUtils.timeUtil.second * 1000);
        let nowHour = now.getHours();
        if(nowHour < openTime[0] || nowHour >= endTime[0]) {
            scUtils.alertUtil.alert(i18n.t("UNION_COPY_TIME_PASS"));
            return;
        }
        scInitializer.unionProxy.sendReqOpen(iType);
        this.eventClose();
    },
});
