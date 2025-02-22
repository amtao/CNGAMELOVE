// 购买每日次数
let Utils = require("Utils")
let Initializer = require("Initializer")

cc.Class({
    extends: cc.Component,

    properties: {
        dayCt:cc.Label,
        monthCt:cc.Label,
        itemBtnLbl:cc.Label,
        monthBtn:cc.Node,
        itemBtn:cc.Node,

        monthOpen:cc.Node,
        itemOpen:cc.Node
    },

    onLoad () {

    },

    start () {
        this.refreshUI()

        facade.subscribe(Initializer.monthCardProxy.MOON_CARD_UPDATE, this.refreshUI, this);
        facade.subscribe("ON_JIAOYOU_INFO", this.refreshUI, this);
    },

    refreshUI(){
        this.dayCt.string = i18n.t("WELFARE_COUNT_TIP",{c:Initializer.jiaoyouProxy.getDayShouhuCt()})
        this.monthCt.string = "+"+Utils.utils.getParamInt("jiaoyou_guaji_yueka");

        var yuanbao = Utils.utils.getParamStrs("jiaoyou_guaji_yuanbao")
        var yuanbaoCfg = localcache.getItem(localdb.table_item,1)
        this.itemBtnLbl.string = yuanbao[Initializer.jiaoyouProxy.cashBuy]?yuanbaoCfg.name+yuanbao[Initializer.jiaoyouProxy.cashBuy][1]:yuanbaoCfg.name+"0"
        this.itemBtn.active = yuanbao[Initializer.jiaoyouProxy.cashBuy]

        let buyInfo = Initializer.monthCardProxy.getCardData(1);
        if(buyInfo && buyInfo.type > 0){
            this.monthBtn.active = false
        }else{
            this.monthBtn.active = true
        }

        this.monthOpen.active = !this.monthBtn.active
        this.itemOpen.active = !this.itemBtn.active
    },

    //购买月卡
    onClickMonth(){
        Utils.utils.openPrefabView("welfare/MonthCard");
    },

    //道具购买次数
    onClickItemBuy(){
        Initializer.jiaoyouProxy.sendCashBuyCount()
    },

    onClickBack: function() {
        Utils.utils.closeView(this);
    },
});
