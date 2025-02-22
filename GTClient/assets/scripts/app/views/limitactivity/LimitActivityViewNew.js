/* explain:
 前期就两个活动, 所以都放在预设体里调用,
 后期活动增加多了之后保存成不同的预设体,
 切换不同的标签页讲不同的预设体生成到这里
 每个脚本都继承scActivityItem进行处理
 --Diamanta 2020.04.02
*/

let scUtils = require("Utils");
let initializer = require("Initializer");
let scActivityItem = require("scActivityItem");
let timeProxy = require('TimeProxy');
var redDot = require("RedDot");

cc.Class({
    extends: cc.Component,

    properties: {
        nTab: cc.Node,
        tabParent: cc.Node,
        scTotalRecharge: scActivityItem,
        scRush2List: scActivityItem,
        scFreeBuy: scActivityItem,
        //lbGold: cc.Label,
        typeTab: cc.Node,
        typeTabParent: cc.Node,
    },

    ctor() {
        this.activityType = 0;

        let actProxy = initializer.limitActivityProxy;
        this.curActId = 0;
        this.curTypeId = 0;
        this.actTypeList = [
            { id: actProxy.LIMIT_ACTIVITY_TYPE, title: i18n.t("WHITE_DAY_LIMIT_ACTIVITY"), red:["limitactivity"]},
            { id: actProxy.RUSH2LIST_TYPE, title: i18n.t("AT_LIST_TITLE"), red:["rush2list"]},
            { id: actProxy.FREEBUY_TYPE, title: i18n.t("AT_LIST_TITLE2"), red:["freeBuy"]},
        ];

        this.actList = [];
        this.tabList = [];

        this.typeTabList = [];
        //this.lastData = new playerProxy.RoleData();
    },

    onLoad() {
        this.activityType = initializer.limitActivityProxy.LIMIT_ACTIVITY_TYPE;
        var e = this.node.openParam;
        e && e.type && (this.activityType = e.type);
        //this.updateUserData();
        facade.subscribe("LIMIT_ACTIVITY_UPDATE", this.onActUpdate, this);
        //facade.subscribe(initializer.playerProxy.PLAYER_USER_UPDATE, this.updateUserData, this);
        facade.subscribe(initializer.limitActivityProxy.UPDATE_FREE_BUY, this.checkTgStatus, this);

        facade.subscribe("LIMIT_ACTIVITY_HUO_DONG_LIST", this.onHuoDongList, this);
        this.onHuoDongList();

        this.initTypeTab();
        this.initActTab();
    },

    getActType(id) {
        for(var i=0; i<this.actTypeList.length; i++) {
            if(this.actTypeList[i].id == id)
                return this.actTypeList[i];
        }
        return null;
    },

    checkTgStatus: function() {
        if(!this.checkFreeBuyShow()) {
            let freeId = initializer.limitActivityProxy.FREEBUY_TYPE;
            let index = -1;
            for(let i = 0, len = this.typeTabList.length; i < len; i++) {
                if(this.typeTabList[i].data.id == freeId) {
                    this.typeTabList[i].node.active = false;
                    index = i;
                    break;
                }
            }
            if(this.curTypeId == freeId && index > -1) {
                this.typeTabList[index - 1].tgSelf.check();
                this.typeTabList[index - 1].tgSelf._emitToggleEvents();
            }
        } 
    },

    initTypeTab() {
        for(let i = this.actTypeList.length - 1; i >= 0; i--) {
            let actData = this.actTypeList[i];
            if(initializer.limitActivityProxy.FREEBUY_TYPE == actData.id) {
               if(!this.checkFreeBuyShow() || !this.checkLimitActivityShow() || !this.checkRush2ListShow()) {
                   continue;
               } 
            }
            let tab = cc.instantiate(this.typeTab);
            tab.parent = this.typeTabParent;
            tab.active = true;
            let script = tab.getComponent('scActTab');
            script.showData(actData);
            actData.red && script.scRed.addBinding(actData.red);
            this.typeTabList.push(script);
        }
        for (var ii = this.typeTabList.length - 1; ii >= 0; ii--) {
            let actData = this.typeTabList[ii];
            if (actData.data.id == this.activityType) {
                actData.tgSelf.check();
                actData.tgSelf._emitToggleEvents();
                break;
            }
        }
        // this.typeTabList[0].tgSelf.check();
        // this.typeTabList[0].tgSelf._emitToggleEvents();
    },

    checkFreeBuyShow: function() {
        let bOpen = timeProxy.funUtils.isOpenFun(timeProxy.funUtils.freeBuy);
        if(!bOpen)
            return false;
        let freeDatas = localcache.getFilters(localdb.table_giftpack, "type", 1);
        for(let j = 0, jLen = freeDatas.length; j < jLen; j++) {
            let data = initializer.limitActivityProxy.freeBuyData[freeDatas[j].id];
            if(null == data) {
                return true;
            } else if(null == data.pickTime || data.pickTime <= 0) {
                return true;
            }
        }
        return false;
    },

    checkRush2ListShow: function() {
        let bOpen = timeProxy.funUtils.isOpenFun(timeProxy.funUtils.atList);
        return bOpen;
    },

    checkLimitActivityShow: function() {
        let bOpen = timeProxy.funUtils.isOpenFun(timeProxy.funUtils.limitActivity);
        return bOpen;
    },

    initActTab() {
        let array = this.getActList();
        for(let i = 0, len = array.length; i < len; i++) {
            let tab = cc.instantiate(this.nTab);
            tab.parent = this.tabParent;
            tab.active = true;
            let script = tab.getComponent('scActTab');
            script.showData(array[i]);
            this.tabList.push(script);
            script.scRed.addBinding([array[i].pindex]);
        }
        if(array.length > 0) {
            this.tabList[0].tgSelf.check();
            this.tabList[0].tgSelf._emitToggleEvents();
        }
    },

    onHuoDongList() {
        var data = initializer.limitActivityProxy.getHuodongList(initializer.limitActivityProxy.ACTIVITY_TYPE, this.activityType-10);
        var typReddot = false;
        for(var i=0; i<data.length; i++) {
            if(data[i].news == 1) {
                typReddot = true;
            }
            redDot.change(data[i].pindex, data[i].news);
        }
        redDot.change(this.getActType(this.activityType).red[0], typReddot);
    },

    // updateUserData: function() {
    //     scUIUtils.uiUtils.showNumChange(this.lbGold, this.lastData.cash, initializer.playerProxy.userData.cash);
    //     this.lastData.cash = initializer.playerProxy.userData.cash;
    // },

    getActList: function() {
        let result = [];
        let actProxy = initializer.limitActivityProxy;
        let listArr = actProxy.getHuodongListByTypeTab(1,1);
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

    checkActList: function() {
        let result = [];
        let actProxy = initializer.limitActivityProxy;
        let listArr = actProxy.huodongList;
        for(let i = 0, len = this.actIdList.length; i < len; i++) {
            let actIdData = this.actIdList[i];
            let act = listArr.filter((data) => {
                return data.id == actIdData.id;
            });
            if(act && act.length > 0 && actProxy.isHaveIdActive(actIdData.id)) {
                result.push(actProxy.getActivityData(actIdData.id)); 
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
           this.onActUpdate(this.actList[actId]);
        }
    },

    onTypeToggleValueChange: function(tg, param) {
        let typeId = parseInt(param);
        if(typeId == this.curTypeId) {
            if(null != tg && !tg.isChecked) {
                tg.check();
                tg._emitToggleEvents();
            }
            return;
        } else if(!tg.isChecked) {
            return;
        }
        this.curTypeId = typeId;
        if(null != this.lastTypeTg) {
            this.lastTypeTg.uncheck();
            this.lastTypeTg._emitToggleEvents();
        }
        this.lastTypeTg = tg;
        let actProxy = initializer.limitActivityProxy;
        switch(typeId) {
            case actProxy.LIMIT_ACTIVITY_TYPE: {
                this.scFreeBuy.node.active = false;
                this.scRush2List.node.active = false;
                this.scTotalRecharge.node.active = true;                
            } break;
            case actProxy.RUSH2LIST_TYPE: {
                this.scFreeBuy.node.active = false;
                this.scRush2List.node.active = true;
                this.scTotalRecharge.node.active = false;                
            } break;
            case actProxy.FREEBUY_TYPE: {
                this.scFreeBuy.node.active = true;
                this.scRush2List.node.active = false;
                this.scTotalRecharge.node.active = false;
                this.scFreeBuy.setData();
            } break;
        }
    },

    onActUpdate: function(act) {
        let id = act.cfg.info.id;
        this.actList[id] = act;
        if(id != this.curActId) {
            return;
        }
        let actProxy = initializer.limitActivityProxy;
        actProxy.curSelectData = act;        
        if(this.scTotalRecharge.node.active) {
            this.scTotalRecharge.setData(act);
        } else if(this.scRush2List.node.active) {
            this.scRush2List.setData(act);
        } else {
            this.scFreeBuy.setData(act);
        }        
        
    },

    onClickRecharge: function() {
        let funUtils = timeProxy.funUtils;
        funUtils.openView(funUtils.recharge.id);
    },

    onClickClose() {
        scUtils.utils.closeView(this);
    },

    onClickGo() {   
        let hudong = localcache.getGroup(localdb.table_banner_title, "pindex", this.curActId);
        hudong[0] && timeProxy.funUtils.openView(hudong[0].jump_to);
    },
});
