var Utils = require("Utils");
var UIUtils = require("UIUtils");
var UrlLoad = require("UrlLoad");
var Initializer = require("Initializer");

cc.Class({
    extends: cc.Component,

    properties: {
        lblTime:cc.Label,
        lblCount:cc.Label,
        npc:UrlLoad,
        itemSpArr:[UrlLoad],
        lblBtnTitle:cc.Label,
        lblSpeak:cc.RichText,
        nodeRemainTime:cc.Node,
        nodeRed:cc.Node,
        spine:sp.Skeleton,
    },

    ctor() {
        this.lastRecordBaoWu = {}
        this.isplaying = false;
    },

    onLoad() {
        Initializer.businessProxy.selectBaoWuDic = {};
        facade.subscribe(Initializer.fuyueProxy.REFRESH_BAOWU, this.refreshBaowu, this);
        facade.subscribe("BUSINESS_UPDATEENTERNUM", this.onUpdateStartInfo, this);        
        this.onInfo();
        this.lblBtnTitle.string = i18n.t("BUSINESS_TIPS12");
        this.spine.node.parent.active = false;
        this.spine.setCompleteListener((trackEntry) => {
            var animationName = trackEntry.animation ? trackEntry.animation.name : "";
            if (animationName == "animation2"){
                let chooseinfo = [];
                for (let key in Initializer.businessProxy.selectBaoWuDic) {
                    chooseinfo.push(Number(key));
                }
                Initializer.businessProxy.sendStartBusiness(chooseinfo, 0);
                Initializer.businessProxy.businessStoryFinished = false;
                Utils.utils.closeView(this, !0);
                Utils.utils.openPrefabView("xingshang/UIBusinessCityInfo", null, {idx:Initializer.businessProxy.businessInfo.currentCity});
            } 
        });
    },

    onInfo() {
        let data = Initializer.businessProxy.businessInfo;
        let cfg = localcache.getItem(localdb.table_chengshi, data.businessManId);
        if (cfg == null) return;
        this.npc.url = UIUtils.uiHelps.getWifeBody(cfg.model);
        this.lblSpeak.string = i18n.t("BUSINESS_TIPS14", { v: cfg.chengshi });
        this.onUpdateStartInfo();
    },

    onUpdateStartInfo() {
        let startInfo = Initializer.businessProxy.startInfo;
        let freeMax = Utils.utils.getParamInt("xingshang_freetime")
        let max = freeMax + startInfo.buyBusinessCount;
        if (startInfo.consumeBusinessCount >= freeMax) {
            this.lblCount.string = (max - startInfo.consumeBusinessCount) + "";
        } else {
            this.lblCount.string = (max - startInfo.consumeBusinessCount) + "/" +  (max - startInfo.buyBusinessCount);
        }       
        this.nodeRemainTime.active = (max <= startInfo.consumeBusinessCount);
    },
    
    /**添加单个宝物*/
    refreshBaowu(data) {
        //console.error("data.type:",data.type);
        let lastId = this.lastRecordBaoWu[data.type];
        if (lastId != null) {
            if (Initializer.businessProxy.selectBaoWuDic[lastId]) {
                delete Initializer.businessProxy.selectBaoWuDic[lastId];
            }
        }
        this.lastRecordBaoWu[data.type] = data.id;
        Initializer.businessProxy.selectBaoWuDic[data.id] = true;
        let baowuInfo = localcache.getItem(localdb.table_baowu, data.id);
        this.itemSpArr[data.type - 1].url = UIUtils.uiHelps.getBaowuIcon(baowuInfo.picture);
        let agTicket= Utils.utils.getParamInt("xingshang_shangpiaochushi");
        let goldLeaf = Utils.utils.getParamInt("xingshang_jinyezichushi");
        let cfg = localcache.getItem(localdb.table_chengshi, Initializer.businessProxy.businessInfo.businessManId);
        for (let key in Initializer.businessProxy.selectBaoWuDic) {
            let info = Initializer.baowuProxy.getBaoWuServerData(Number(key));
            if (info && cfg.qizhen.indexOf(info.id) != -1){
                let cg = localcache.getItem(localdb.table_baowu, key);
                agTicket += (cg.quality - 1);               
            }
            if (info)
                goldLeaf += (info.star * 1000);
        }
        this.lblSpeak.string = i18n.t("BUSINESS_TIPS13", { v2: agTicket, v3: goldLeaf });
        if (goldLeaf > 0 && agTicket > 0){
            this.lblBtnTitle.string = i18n.t("BUSINESS_TIPS3");
            this.nodeRed.active = false;
        }
    },

    /**一键添加宝物*/
    onKeyAddBaowu(){
        let agTicket= Utils.utils.getParamInt("xingshang_shangpiaochushi");
        let goldLeaf = Utils.utils.getParamInt("xingshang_jinyezichushi");
        let cfg = localcache.getItem(localdb.table_chengshi, Initializer.businessProxy.businessInfo.businessManId);
        if (cfg == null) return;
        for (var ii = 0;ii < this.itemSpArr.length;ii++){
            this.itemSpArr[ii].url = "";
        }
        this.lastRecordBaoWu = {}
        let index = 0;
        if (Initializer.baowuProxy.baowuList != null){
            let listdata = Utils.utils.clone(Initializer.baowuProxy.baowuList);
            listdata.sort((a,b)=>{
                return a.star > b.star ? -1 : 1;
            })
            for (let info of listdata){
                if (cfg.qizhen.indexOf(info.id) != -1){
                    let baowuInfo = localcache.getItem(localdb.table_baowu, info.id);
                    this.itemSpArr[index].url = UIUtils.uiHelps.getBaowuIcon(baowuInfo.picture);
                    agTicket += (baowuInfo.quality - 1);
                    goldLeaf += (info.star * 1000);
                    Initializer.businessProxy.selectBaoWuDic[info.id] = true;
                    this.lastRecordBaoWu[index+1] = info.id
                    index++;
                }
            }
            for (let info of listdata){
                if (cfg.qizhen.indexOf(info.id) == -1){
                    let baowuInfo = localcache.getItem(localdb.table_baowu, info.id);
                    this.itemSpArr[index].url = UIUtils.uiHelps.getBaowuIcon(baowuInfo.picture);
                    goldLeaf += (info.star * 1000);
                    Initializer.businessProxy.selectBaoWuDic[info.id] = true;
                    this.lastRecordBaoWu[index+1] = info.id
                    index++;
                    if (index >= 5){
                        break
                    }
                }
            }
        }
        this.lblSpeak.string = i18n.t("BUSINESS_TIPS13",{v2:agTicket,v3:goldLeaf});
        if (goldLeaf > 0 && agTicket > 0){
            this.lblBtnTitle.string = i18n.t("BUSINESS_TIPS3");
            this.nodeRed.active = false;
        }       
    },

    onClickClost() {      
        Initializer.businessProxy.businessStoryFinished = false;
        let dt = Utils.utils.getParamInt("Uicomeout_time");
        if(this.node.openTime && cc.sys.now() - this.node.openTime < dt) {
            return;
        }
        Utils.utils.closeNameView("xingshang/UIBusinessWorld", !0);
        Utils.utils.closeView(this, !0);
    },


    onClickShop(){
        facade.send("ITEM_LIMIT_GO", {
            id: 121,
            count: 1
        });
    },

    /**订单内容*/
    onClickOrderDetail() {
        Utils.utils.openPrefabView("xingshang/BusinessOrderView", null, { hideBtn: true } );
    },


    /**点击奇珍*/
    onClickQiZhen(sender,e){
        Utils.utils.openPrefabView("fuyue/FuyueBaowuListView", null, { open:Number(e), type: Initializer.businessProxy.BAOWULIST_TYPE.BUSINESS });
    },

    /**一键选择或开始行商*/
    onClickBegan(){
        if (this.isplaying) return;
        let chooseinfo = [];
        for (let key in Initializer.businessProxy.selectBaoWuDic) {
            chooseinfo.push(Number(key));
        }
        if (chooseinfo.length > 0 && this.lblBtnTitle.string == i18n.t("BUSINESS_TIPS3")) {//开始行商
            let startInfo = Initializer.businessProxy.startInfo;
            let max = Utils.utils.getParamInt("xingshang_freetime") + startInfo.buyBusinessCount;
            if (startInfo.consumeBusinessCount < max){
                this.spine.node.parent.active = true;
                this.spine.animation = "animation2";
                this.isplaying = true;
                return;
            }
            this.onClickAddTimes();
        }
        else{
            //一键选择
            this.onKeyAddBaowu();
        }
    },

    update(dt){
        if (this.nodeRemainTime.active){
            let remaintime = Initializer.playerProxy.nextDayZeroTimeStamp - Utils.timeUtil.second;
            this.lblTime.string = Utils.timeUtil.second2hms(Math.ceil(remaintime));
        }       
    },

    onClickAddTimes(){
        let startInfo = Initializer.businessProxy.startInfo;
        let vipCfg = localcache.getItem(localdb.table_vip,Initializer.playerProxy.userData.vip);
        let xingshangling = Utils.utils.getParamInt("zw_cost_item_id");
        if (startInfo.buyBusinessCount < vipCfg.xingshangtime){
            Utils.utils.showConfirmItem(
                i18n.t("BUSINESS_TIPS21"),
                121,
                Initializer.bagProxy.getItemCount(xingshangling),
                function() {
                    if (Initializer.bagProxy.getItemCount(xingshangling) < 1){
                        Utils.alertUtil.alertItemLimit(xingshangling);
                        return;
                    }
                    Initializer.businessProxy.sendBuyCount();
                },
                "COMMON_YES"
            );
            return;
        }
        // unlock recharge and vip --2020.07.21
        Utils.utils.showConfirm(i18n.t("BUSINESS_TIPS22"), () => {
            Utils.utils.openPrefabView("welfare/RechargeView");
        });
    },

    onClickHelp(){
        Utils.utils.openPrefabView("xingshang/BusinessHelpView");
    },
    
    
});
