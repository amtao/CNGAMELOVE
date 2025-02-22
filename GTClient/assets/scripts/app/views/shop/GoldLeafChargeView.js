let List = require("List");
let Utils = require("Utils");
let Initializer = require("Initializer");
let UserHeadItem = require("UserHeadItem");
cc.Class({
    extends: cc.Component,
    properties: {
        list: List,
        vipInfo:cc.Label,
        nameInfo:cc.Label,
        userItem: UserHeadItem,
    },
    onLoad() {
        this.onShowData();
        facade.subscribe(Initializer.welfareProxy.UPDATE_CHARGE_ORDER, this.onShowData, this);
    },
    updateUser() {
        this.userItem.updateUserHead();
    },
    onShowData() {
        this.vipInfo.string = 'VIP'+(0 == Initializer.playerProxy.userData.vip ? 1 : Initializer.playerProxy.userData.vip);
        this.nameInfo.string = Initializer.playerProxy.userData.name;
        this.updateUser();
        let bagList = Initializer.limitActivityProxy.getGoldLeafBagList();
        if(bagList){
            let listData = [];
            for (let key in bagList) {
                listData.push(bagList[key]);
            }
            this.list.data = listData;
        }
    },
    onClickClost() {
        Utils.utils.closeView(this);
    },
});
