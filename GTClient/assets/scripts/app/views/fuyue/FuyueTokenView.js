let scList = require("List");
let scInitializer = require("Initializer");
let scUtils = require("Utils");

cc.Class({
    extends: cc.Component,

    properties: {
        tokenList: scList,
        nTips: cc.Node,
        lbTips: cc.Label,
    },

    onLoad: function() {
        let openParam = this.node.openParam;
        this.iOpen = openParam.open;
        this.id1 = openParam.id1;
        this.id2 = openParam.id2; //仅初始化用, 后面需要再用需要重新赋值
        this.checkCondition({ set: scInitializer.fuyueProxy.conditionType.token, id: this.iOpen == 1 ? this.id1 : this.id2 });
        this.showList();
        facade.subscribe(scInitializer.fuyueProxy.TEMP_REFRESH_SELECT, this.checkCondition, this);
    },

    showList() {
        let friendId = this.node.openParam.friendId;
        let list = scInitializer.servantProxy.getXinWuItemListByHeroid(friendId);
        let tokens = scInitializer.servantProxy.getTokensInfo(friendId);
        let hasList = [];
        for(let k in tokens) {
            if(tokens[k].isActivation) {
                for(let i = 0; i < list.length; i++) {
                    if(k == list[i].id) {
                        if(this.iOpen == 1 && k != this.id2)
                            hasList.push(list[i]);
                        else if(this.iOpen == 2 && k != this.id1)
                            hasList.push(list[i]);
                    }
                }
            }
        }
        // for (var i = 0; i < tokenList.length; i++){
        //     var _m = tokenList[i];
        //     _data.push({cfg:_m,sdata:a != null ? a[_m.id] : null ,heroid:this._curHero.id});
        // }
        this.tokenList.iOpen = this.iOpen;
        this.tokenList.data = hasList;
    },

    checkCondition: function(data) {
        let fuyueProxy = scInitializer.fuyueProxy;
        fuyueProxy.checkConditionUI(data, data.set, this.tokenList, this.nTips, this.lbTips);
    },

    onClickBack: function() {        
        scUtils.utils.closeView(this);
    },
    
});
