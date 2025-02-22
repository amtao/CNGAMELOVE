let scbaseAct = require("scActivityItem");
let scList = require("List");
let uiUtils = require("UIUtils");
let initializer = require("Initializer");
var Utils = require("Utils");
var TimeProxy = require("TimeProxy");
var UrlLoad = require("UrlLoad");

cc.Class({
    extends: scbaseAct,

    properties: {
        nTab: cc.Node,
        tabParent: cc.Node,
        lbTime: cc.Label,
        lbMyRank: cc.Label,
        contentList: scList,
        nBtnGo: cc.Node,
        urlBtn: UrlLoad,
        lbTitle: cc.Label,
    },

    ctor() {
        this.tabList = [];
        this.curActId = 0;
        this.actList = [];  
        this.myRankList = [];
        this.cbRankList = [];
        this.exchangeList = [];      
        this.curData = null;
    },

    onLoad() {
        facade.subscribe("AT_LIST_UPDATE", this.onDataUpdate, this);
        facade.subscribe("AT_LIST_RANK_UPDATE", this.onRankUpdate, this);
        facade.subscribe("AT_LIST_MY_RANK_UPDATE", this.onMyRankUpdate, this);        
        facade.subscribe("RUSH2LIST_GIFTBOX_UPDATE", this.onGiftboxUpdate, this);
        // facade.subscribe("AT_LIST_MY_RANK_UPDATE", this.onMyRankUpdate, this);        
        this.initCBTab();
        //this.initView();
    },

    // initView() {
             
    // },

    onGiftboxUpdate: function(data) {
        this.exchangeList[this.curData.cfg.info.id] = data;
    },

    onDataUpdate: function(data) {
        let self = this;
        this.curData = data;
        let id = data.cfg.info.id;
        this.actList[id] = data;   
        let actProxy = initializer.limitActivityProxy;  
        actProxy.curRushSelectData = data;   
        uiUtils.uiUtils.countDown(data.cfg.info.eTime, this.lbTime, () => {
            if(null != self.lbTime) {
                self.lbTime.string = i18n.t("ACTHD_OVERDUE");
            }
        });
        this.lbTitle && (this.lbTitle.string = data.cfg.msg);
        this.rwdList = data.cfg.rwd.sort(this.sortList);
        var myRank = this.myRankList[id];
        if(myRank)
            this.lbMyRank.string = myRank.rid;

        let imgComp = this.node.getChildByName("nTop").getChildByName("spTitle").getComponent("UrlLoad");
         null != imgComp && (data.skin && 0 != data.skin ? (imgComp.url = uiUtils.uiHelps.getLimitActivityBg(data.skin))
         : (imgComp.url = uiUtils.uiHelps.getLimitActivityBg(id)));
        this.urlBtn.url = uiUtils.uiHelps.getRankActIcon(id);
        let bannerData = localcache.getGroup(localdb.table_banner_title, "pindex", id);
        this.nBtnGo.active = bannerData && bannerData[0] && !Utils.stringUtil.isBlank(bannerData[0].jump_to);
    },

    onMyRankUpdate: function() {
        if(initializer.limitActivityProxy.cbMyRank != null) {
            this.lbMyRank.string = initializer.limitActivityProxy.cbMyRank.rid;  
            this.myRankList[this.curActId] = initializer.limitActivityProxy.cbMyRank;
            this.lbMyRank.string = initializer.limitActivityProxy.cbMyRank.rid;   
        }            
    },
    
    onRankUpdate: function() {
        if(this.rwdList != null) {
            if(this.cbRankList[this.curActId] == null)
                this.cbRankList[this.curActId] = initializer.limitActivityProxy.cbRankList;
            else
                initializer.limitActivityProxy.cbRankList = this.cbRankList[this.curActId];
            //console.error("this.rwdList:",this.rwdList)
            let listdata = []
            for (var ii = 0; ii < this.rwdList.length;ii++){
                let cg = this.rwdList[ii];
                listdata.push(cg);
                listdata[listdata.length-1].isActive = true;
            }
            //console.error("listdata:",listdata)
            this.contentList.data = listdata;    
        }
    },

    onClickBd() {
        this.curData.index = this.curActId;
        this.curData.cbMyRank = this.myRankList[this.curActId];
        this.curData.cbRankList = this.cbRankList[this.curActId];
        Utils.utils.openPrefabView("limitactivity/AtListRankView", null, this.curData);
    },    

    initCBTab() {
        let array = this.getCBHodongList();
        //console.error("array:",array)
        for(let i = 0, len = array.length; i < len; i++) {
            let tab = cc.instantiate(this.nTab);
            tab.parent = this.tabParent;
            tab.active = true;        
            let script = tab.getComponent('scActTab');
            script.showData(array[i]);
            this.tabList.push(script);            
        }
        //console.error("this.tabList:",this.tabList)
        if(array.length > 0) {
            this.tabList[0].tgSelf.check();
            this.tabList[0].tgSelf._emitToggleEvents();
        }
    },

    getCBHodongList: function() {
        let result = [];
        let actProxy = initializer.limitActivityProxy;
        let listArr = actProxy.getHuodongListByTypeTab(1,2);
        //console.error("listArr:",listArr)
        let act = listArr.filter((data) => {
            return (data.type == 1) && actProxy.isHaveIdActive(data.id);
        });
        if(act && act.length > 0) {
            for(var i = 0; i < act.length; i++) {
                result.push(actProxy.getActivityData(act[i].id)); 
            }            
        }
        return result;
    },

    onToggleValueChange: function(tg, param) {
        let actId = parseInt(param);
        if(actId == this.curActId) {
            if(null != tg && !tg.isChecked) {
                tg.check();
                tg._emitToggleEvents();
            }
            return;
        } else if(!tg.isChecked) {
            return;
        }
        this.curActId = actId;
        if(null != this.lastTg) {
            this.lastTg.uncheck();
            this.lastTg._emitToggleEvents();
        }
        this.lastTg = tg;
        if(null == this.actList[actId]) {
            initializer.limitActivityProxy.sendLookActivityData(this.curActId);
        } else {
           this.onDataUpdate(this.actList[actId]);
           this.onRankUpdate();
        }
    },

    onClickGo() {   
        let hudong = localcache.getGroup(localdb.table_banner_title, "pindex", this.curActId);
        hudong[0] && TimeProxy.funUtils.openView(hudong[0].jump_to);
    },

    onClickGiftbox() {
        Utils.utils.openPrefabView("limitactivity/Rush2ListGiftbox", null, this.exchangeList[this.curActId]);
    },
});
