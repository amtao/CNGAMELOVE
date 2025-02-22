
var List = require("List");
var Initializer = require("Initializer");
var TimeProxy = require("TimeProxy");
let Utils = require("Utils");
var UrlLoad = require("UrlLoad");
let UIUtils = require("UIUtils");
cc.Class({
    extends: cc.Component,
    properties: {
        listItem:List,
        nodeBuild:cc.Node,
        nodeDonate:cc.Node,
        nBtnBgs: [cc.Node],
        btns: [cc.Button],
        lblClubMoney:cc.Label,
        lblClubDonate:cc.Label,
        lblNameArr:[cc.Label],
        lblDesArr:[cc.RichText],
        lblNumArr:[cc.Label],
        spIconArr:[UrlLoad],
        lblDonateNumArr:[cc.RichText],
        btnDoanteArr:[cc.Button],
        nodeLevelUp:cc.Node,
        lblLevelUpDes:cc.Label,
    },
    ctor() {
        this.lastLsjLevel = -1;
        this.lastSfLevel = -1;
    },
    onLoad() {        
        facade.subscribe("UPDATE_SEARCH_INFO", this.refreshView, this);
        facade.subscribe("UPDATE_MEMBER_INFO", this.onUpdateDonateData, this);
        this.onHideLevelUpView();
        this.refreshView();
        this.onClickTab(null,"1");
    },
   

    onClickClose: function() {
        Utils.utils.closeView(this);
    },

    refreshView(){
        this.onUpdateDonateData();
        this.onUpdateBuildLevelUp();
    },

    /**刷新捐献界面*/
    onUpdateDonateData(){
        for (let ii = 0; ii < this.lblNameArr.length;ii++){
            let cfg = localcache.getItem(localdb.table_donate, ii+1);
            this.lblNameArr[ii].string = cfg.msg_cn;
            let itemcfg = localcache.getItem(localdb.table_item,cfg.pay[0].id)
            this.spIconArr[ii].url = UIUtils.uiHelps.getItemSlot(itemcfg.icon);
            this.lblNumArr[ii].string = `${cfg.pay[0].count}`;
            this.lblDesArr[ii].string = this.getDonateDes(cfg.get);
            let curnum = Initializer.unionProxy.getCurrentDonateTimesById(cfg.id);
            let renum = cfg.time - curnum;
            this.lblDonateNumArr[ii].string = i18n.t("CLOTHE_PVP_COUNT",{d:renum > 0 ? i18n.t("USER_CLOTHE_CARD_TIPS59",{v1:renum}) : `<color=#red>${renum}</color>`,s:cfg.time});
            this.btnDoanteArr[ii].interactable = renum > 0;
        }
        let clubInfoData = Initializer.unionProxy.clubInfo;
        if (clubInfoData){
            this.lblClubMoney.string = clubInfoData.fund;
            this.lblClubDonate.string = Initializer.bagProxy.getItemCount(118);
        }
        if (this.lastLsjLevel != -1 && clubInfoData.lsjLv > this.lastLsjLevel){
            let cfg = localcache.getFilter(localdb.table_building_up,"building_type",1,"lv",Initializer.unionProxy.clubInfo.lsjLv)
            this.onShowLevelUpView(cfg);
        }
        else if(this.lastSfLevel != -1 && clubInfoData.spLv > this.lastSfLevel){
            let cfg = localcache.getFilter(localdb.table_building_up,"building_type",2,"lv",Initializer.unionProxy.clubInfo.spLv)
            this.onShowLevelUpView(cfg);
        }
        this.lastLsjLevel = clubInfoData.lsjLv;
        this.lastSfLevel = clubInfoData.spLv;
    },

    onShowLevelUpView(cfg) {
        this.nodeLevelUp.active = true;
        this.lblLevelUpDes.string = cfg.msg;
    },

    onHideLevelUpView(){
        this.nodeLevelUp.active = false;
    },

    /**刷新建设界面**/
    onUpdateBuildLevelUp(){
        let listCfg = localcache.getList(localdb.table_building_up);
        let tmpDic = {}
        for (let ii = 0; ii < listCfg.length; ii++){
            let cg = listCfg[ii];
            if (tmpDic[cg.building_type] == null){
                tmpDic[cg.building_type] = 1;
            }
        }
        let listdata = [];
        for (let key in tmpDic){
            listdata.push({ key: Number(key) });
        }
        this.listItem.data = listdata;
    },

    /**获取捐献获得的奖励描述*/
    getDonateDes(data){
        let str = "";
        for (let ii = 0; ii < data.length;ii++){
            let itemcfg = localcache.getItem(localdb.table_item,data[ii].id);
            str += `${itemcfg.name} <color=#278F6E>+${data[ii].count}</color><br/>`;
        }
        return str;
    },


    onClickTab(t, strIndex) {
        let index = parseInt(strIndex) - 1;
        for (let i = 0; i < this.btns.length; i++) {
            let bCur = index == i;
            this.btns[i].interactable = !bCur;
            this.nBtnBgs[i].active = bCur;
        }
        this.nodeDonate.active = index == 0;
        this.nodeBuild.active = index == 1;
    },

    /**点击捐献*/
    onClickDonate(t,strIndex){
        let index = parseInt(strIndex);
        Initializer.unionProxy.sendDayGongXian(index);
    },

    onClickRecord(){
        Utils.utils.openPrefabView("union/UnionDonateRecordView");
    },
    
});
