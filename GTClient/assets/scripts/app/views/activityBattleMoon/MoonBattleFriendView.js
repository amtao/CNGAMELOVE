var list = require("List");
var utils = require("Utils");
var initializer = require("Initializer");
cc.Class({
    extends: cc.Component,

    properties: {
        toggleCon: cc.ToggleContainer,
        btn: cc.Button,
        btnLab: cc.Label,
        tipLab: cc.Label,
        remainSendCountLab: cc.Label,
        remainSendNode: cc.Node,
        list: list,
    },

    onLoad(){
        facade.subscribe(initializer.moonBattleProxy.MOON_BATTLE_UPDATE_DATE, this.onUpdateData, this);
        facade.subscribe("UPDATE_FRIEND_LIST", this.onUpdateData, this);
    },

    onEnable(){
        this.index = 0;
        this.onUpdateData()
    },

    onToggle(toggle, event){
        this.index = this.toggleCon.toggleItems.indexOf(toggle);
        this.onUpdateData();
    },

    onUpdateData(){
        let index = this.index;
        // this.remainSendNode.active = index == 0;
        this.remainSendCountLab.string = index == 0 ? 
                                            i18n.t("MOON_BATTLE_FRIEND_REMAIN_COUNT", {num: initializer.moonBattleProxy.getRwMainFriendSendTimes()}) : 
                                            i18n.t("MOON_BATTLE_FRIEND_REMAIN_GET_COUNT", {num1: initializer.moonBattleProxy.getRemainFriendGetTimes(), num2: initializer.moonBattleProxy.getAllFriendGetTimes()})
        this.btnLab.string = index == 0 ? i18n.t("COMMON_SEND_FREE_ALL") : i18n.t("COMMON_GET_ALL");
        this.tipLab.string = index == 0 ? i18n.t("MOON_BATTLE_FRIEND_TIP_1") : i18n.t("MOON_BATTLE_FRIEND_TIP_2", {num: initializer.moonBattleProxy.getAllFriendGetTimes()});
        // this.list.data = index == 0 ? initializer.moonBattleProxy.getFriendSendList() : initializer.moonBattleProxy.getFriendReceiveList();
        if (index == 0) {
            this.list.data = initializer.moonBattleProxy.getFriendSendList();
            let list = initializer.moonBattleProxy.getValidFriendSendList();
            this.btn.interactable = list.length > 0;
        }else{
            let listData = initializer.moonBattleProxy.getFriendReceiveList();
            this.list.data = listData;
            this.btn.interactable = initializer.moonBattleProxy.canGetFriendGift() && listData.length > 0;
        }
    },

    onClickBtn(){
        if (this.index == 0) {
            initializer.moonBattleProxy.sendShellOneKey(0);
        }else if(this.index == 1){
            initializer.moonBattleProxy.sendGetShellRwd(-1);
        }
    },

    onClickClose() {
        utils.utils.closeView(this);
    },

});
