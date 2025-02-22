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
// let scUIUtils = require("UIUtils");
// let playerProxy = require("PlayerProxy");

cc.Class({
    extends: cc.Component,

    properties: {
        nTab: cc.Node,
        tabParent: cc.Node,
        scTotalRecharge: scActivityItem,
        scGroupBuying: scActivityItem,
        //lbGold: cc.Label,
    },

    ctor() {
        this.curActId = 0;
        this.actList = [];
        this.tabList = [];
        //this.lastData = new playerProxy.RoleData();
    },

    onLoad() {
        //this.updateUserData();
        facade.subscribe("LIMIT_ACTIVITY_UPDATE", this.onActUpdate, this);
        //facade.subscribe(initializer.playerProxy.PLAYER_USER_UPDATE, this.updateUserData, this);

        let array = this.checkActList();
        for(let i = array.length - 1; i >= 0; i--) {
            let data = array[i];
            let tab = cc.instantiate(this.nTab);
            tab.parent = this.tabParent;
            tab.active = true;
            let script = tab.getComponent('scActTab');
            script.showData(data);
            let val = localcache.getFilter(localdb.table_banner_title, "pindex", data.id);
            if(null != val && null != val.binding) {
                script.scRed.addBinding(JSON.parse(val.binding));
            }
            this.tabList.push(script);
        }
        let index = this.tabList.length - 1;
        this.tabList[index].tgSelf.check();
        this.tabList[index].tgSelf._emitToggleEvents();
    },

    // updateUserData: function() {
    //     scUIUtils.uiUtils.showNumChange(this.lbGold, this.lastData.cash, initializer.playerProxy.userData.cash);
    //     this.lastData.cash = initializer.playerProxy.userData.cash;
    // },

    checkActList: function() {
        //现在全是活动, 如果后面加非活动功能需要特殊处理
        let result = [];
        let actProxy = initializer.limitActivityProxy;
        let list = actProxy.getHuodongList(actProxy.SUPERBUY_TYPE);
        for(let i = 0, len = list.length; i < len; i++) {
            let actIdData = list[i];
            if(actProxy.isHaveIdActive(actIdData.id)) {
                result.push(actProxy.getActivityData(actIdData.id)); 
            }
        }
        return result;
    },

    onToggleValueChange: function(tg, param) {
        let actId = parseInt(param);
        if(actId == this.curActId) {
            if(null != tg && !tg.isChecked) {
                let index = this.tabList.length - 1;
                this.tabList[index].tgSelf.check();
                this.tabList[index].tgSelf._emitToggleEvents();
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

    onActUpdate: function(act) {
        let id = act.cfg.info.id;
        this.actList[id] = act;
        if(id != this.curActId) {
            return;
        }
        let actProxy = initializer.limitActivityProxy;
        actProxy.curSelectData = act;  

        switch(id) {
            case actProxy.TOTAL_CHARGE: {
                this.scGroupBuying.node.active = false;
                this.scTotalRecharge.node.active = true;
                this.scTotalRecharge.setData(act);
            } break;
            case actProxy.GROUP_BUYING: {
                this.scTotalRecharge.node.active = false;
                this.scGroupBuying.node.active = true;
                this.scGroupBuying.setData(act);
            } break;
        }
    },

    onClickRecharge: function() {
        let funUtils = timeProxy.funUtils;
        funUtils.openView(funUtils.recharge.id);
    },

    onClickClose() {
        scUtils.utils.closeView(this);
    },
});
