
var List = require("List");
var Initializer = require("Initializer");
var TimeProxy = require("TimeProxy");
let Utils = require("Utils");
var UrlLoad = require("UrlLoad");
let UIUtils = require("UIUtils");
let ItemSlotUI = require("ItemSlotUI");
cc.Class({
    extends: cc.Component,
    properties: {
        listItem:List,
        prg: cc.ProgressBar,
        lblTodayActive:cc.Label,
        boxUrl:[UrlLoad],
        lblNumArr:[cc.Label],
        nodePersonTask:cc.Node,
        nBtnBgs: [cc.Node],
        btns: [cc.Button],
        btnGetReward:cc.Button,
        btnGetRewardArr:[cc.Button],
        nodeRedArr:[cc.Node],
        nodeTaskTop:cc.Node,
        nodeBanquet:cc.Node,
        item:ItemSlotUI,
        lblBanquetNum:cc.Label,
        lblRefreshTime:cc.Label,
        progressBar:cc.ProgressBar,
        itemList:List,
        lblCostRefresh:cc.Label,
        lblLeftTime:cc.RichText,
        lblRewardDes:cc.RichText,
    },
    ctor() {
        this.todayZeroTime = 0;
    },
    onLoad() {       
        this.updateTask();
        this.updateTodayActive();
        this.todayZeroTime = Utils.timeUtil.getTodaySecond(24, 0, 0);
        facade.subscribe("UPDATE_CLUB_TASK", this.updateTask, this);
        facade.subscribe("UPDATE_CLUB_TODAYACTIVE", this.updateTodayActive, this);
        facade.subscribe("UNION_RESOURCEBASE", this.onRefeshBanquet, this);
        facade.subscribe("UNION_PARTY", this.onRefeshResourceList, this);

        Initializer.unionProxy.sendGetResourceBaseInfo();
        //Initializer.unionProxy.sendGetPartyBaseInfo();
        this.onClickTab(null,1)       
    },



    updateTodayActive(){
        let clubActive = Initializer.unionProxy.clubActive;
        let score = clubActive ? clubActive.score : 0;
        let listGet = clubActive ? clubActive.get : [];
        this.lblTodayActive.string = score;
        let canGetRewad = false;
        for (let ii = 0; ii < this.lblNumArr.length;ii++){
            let cg = localcache.getItem(localdb.table_union_dailyRwd,ii+1);
            this.lblNumArr[ii].string = cg.need;
            if (ii == this.lblNumArr.length - 1){
                this.prg.progress = score / cg.need;
            }
            this.nodeRedArr[ii].active = false;
            //this.btnGetRewardArr[ii].interactable = false;
            if (score >= cg.need && listGet.indexOf(cg.id) == -1){
                canGetRewad = true;
                //this.btnGetRewardArr[ii].interactable = true;
                this.nodeRedArr[ii].active = true;
            }
            if (listGet.indexOf(cg.id) != -1){
                this.boxUrl[ii].url = UIUtils.uiHelps.getJiaoyouImg("ty_icon_box1");
            }
            else{
                this.boxUrl[ii].url = UIUtils.uiHelps.getJiaoyouImg("ty_icon_box2");
            }
        }
        this.btnGetReward.interactable = canGetRewad;

    },
   
    updateTask() {
        var t = localcache.getList(localdb.table_union_task);
        let sortFuc = function(a){
            let isFinshed = Initializer.unionProxy.isFinishedByTask(a.id);
            if (isFinshed){
                return 3;
            }
            let cNum = Initializer.unionProxy.getUnionTaskFinishNumById(a.id);
            if (cNum >= a.set[0]){
                return 1;
            }
            return 2;
        }
        t.sort(function(a, b) {
            if (sortFuc(a) == sortFuc(b)){
                return a.paixu - b.paixu;
            }
            else{
                return sortFuc(a) < sortFuc(b) ? -1 : 1;
            }
        });
        let cfg = localcache.getFilter(localdb.table_building_up,"building_type",1,"lv",Initializer.unionProxy.clubInfo.lsjLv);
        let listdata = [];
        for (let ii = 0; ii < t.length;ii++){
            let cg = t[ii];
            listdata.push(cg);
            listdata[listdata.length - 1].ctbt_buff = cfg.ctbt_buff;
            listdata[listdata.length - 1].exp_buff = cfg.exp_buff;
            listdata[listdata.length - 1].fund_buff = cfg.fund_buff;
        }
        this.listItem.data = listdata;
    },

    onClickClose: function() {
        Utils.utils.closeView(this);
    },

    /**一键领取奖励*/
    onClickGetAllReward(){
        Initializer.unionProxy.sendPickActiveAward(0);
    },

    onClickSingleReward(t,strIndex){
        let cg = localcache.getItem(localdb.table_union_dailyRwd, strIndex);
        let clubActive = Initializer.unionProxy.clubActive;
        let score = clubActive ? clubActive.score : 0;
        let listGet = clubActive ? clubActive.get : [];
        if (score >= cg.need && listGet.indexOf(cg.id) == -1) { //可以领取
            Initializer.unionProxy.sendPickActiveAward(Number(strIndex));
        } else {
            cg.rwd_q = cg.rwd;
            Utils.utils.openPrefabView("achieve/TaskDayRwdView", !1, cg);
        } 
    },

    onClickTab(t, strIndex) {
        let index = parseInt(strIndex) - 1;
        for (let i = 0; i < this.btns.length; i++) {
            let bCur = index == i;
            this.btns[i].interactable = !bCur;
            this.nBtnBgs[i].active = bCur;
        }
        this.nodePersonTask.active = index == 0;
        this.nodeTaskTop.active = index == 0;
        this.nodeBanquet.active = index == 1;
    },  

    update(dt){
        let remain = this.todayZeroTime - Utils.timeUtil.second;
        if (remain < 0) remain = 0;
        this.lblRefreshTime.string = Utils.timeUtil.second2hms(remain);
    },

    /**刷新筹备宴会*/
    onRefeshBanquet(){
        let count = Initializer.bagProxy.getItemCount(Initializer.unionProxy.banquetId);
        this.item.data = {id:Initializer.unionProxy.banquetId,kind:1,count:1};
        this.lblBanquetNum.string = `${count}/${Utils.utils.getParamStr("club_partyRes")}`;
        let progressNum = count/Utils.utils.getParamInt("club_partyRes");
        if (progressNum > 1) progressNum = 1;
        this.progressBar.progress = progressNum;
    },

    onRefeshResourceList(){
        let data = Initializer.unionProxy.partyData;
        let listdata = []
        let tmpMap = {};
        for (let key in data.resourceList){
            let cg = data.resourceList[key];
            let cfg = localcache.getItem(localdb.table_party_task,key);
            listdata.push({id:Number(key),kind:1,count:cg.count * cfg.unit})
            for (let ii = 0; ii < cg.getRwd.length;ii++){
                if (tmpMap[cg.getRwd[ii].itemid] == null){
                    tmpMap[cg.getRwd[ii].itemid] = cg.getRwd[ii]
                }
                else{
                    tmpMap[cg.getRwd[ii].itemid].count += cg.getRwd[ii].count;
                }
            }
        }
        this.itemList.data = listdata;
        this.itemList.node.x = this.itemList.node.width * (-0.5);
        let str = "";
        for (let key in tmpMap){
            let cg = tmpMap[key];
            let itemcfg = localcache.getItem(localdb.table_item,key);
            str += (i18n.t("UNION_TIPS29",{v1:itemcfg.name,v2:cg.count}) + "          ")
        }
        this.lblRewardDes.string = str;
        let baseCost = Utils.utils.getParamInt("club_partyrefresh")
        this.lblCostRefresh.string = `x${baseCost * (data.refreshTimes + 1)}`;
        let vipCfg = localcache.getItem(localdb.table_vip,Initializer.playerProxy.userData.vip);
        let leftNum = data.buyTimes + vipCfg.partytask - data.submitTimes;       
        this.lblLeftTime.string = i18n.t("UNION_TIPS28",{v1:leftNum,v2:vipCfg.partytask});
    },

    /**刷新提交列表*/
    onClickRefresh(){
        let data = Initializer.unionProxy.partyData;
        let baseCost = Utils.utils.getParamInt("club_partyrefresh");
        if (Initializer.bagProxy.getItemCount(1) < baseCost * (data.refreshTimes + 1)){
            Utils.alertUtil.alertItemLimit(1);
            return;
        }
        Initializer.unionProxy.sendRefreshList();
    },

    /**提交*/
    onClickSubmit(){
        if (this.progressBar.progress >= 1){
            Utils.alertUtil.alert18n("UNION_TIPS32");
            return;
        }
        let data = Initializer.unionProxy.partyData;
        let vipCfg = localcache.getItem(localdb.table_vip,Initializer.playerProxy.userData.vip);
        let leftNum = data.buyTimes + vipCfg.partytask - data.submitTimes;
        if (leftNum <= 0){
            let baseCost = Utils.utils.getParamInt("club_partytask");
            if (data.buyTimes < vipCfg.partytask_buy){
                Utils.utils.showConfirm(i18n.t("UNION_TIPS30", { v1:baseCost * (data.buyTimes + 1)}), () => {
                    if (Initializer.bagProxy.getItemCount(1) < baseCost * (data.buyTimes + 1)){
                        Utils.alertUtil.alertItemLimit(1);
                        return;
                    }
                    Initializer.unionProxy.sendBuyCount();
                });       
                return;
            }
            Utils.alertUtil.alert18n("UNION_TIPS31");
            return;
        }
        Initializer.unionProxy.sendSubmitResource();
    },
});
