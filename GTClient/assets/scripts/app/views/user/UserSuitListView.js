let Utils = require("Utils");
let scList = require("List");
let Initializer = require("Initializer");

cc.Class({
    extends: cc.Component,

    properties: {
        itemList: scList,
        nodeNull: cc.Node,
        lblTitle:cc.Label,
    },

    ctor: function() {
        this.heroId = -1;
        this.qualityType = 0;
        this.propType = 0;
        this.sortType = 0;
    },

    onLoad () {

        this.lblTitle.string = i18n.t("USERCLOTHE_SUITTYPE" + this.node.openParam.idx);
        facade.subscribe("UPDATE_CLOTHE_EQUIPCARD", this.onRefeshList, this);
        facade.subscribe("UPDATE_CLOTHE_BROCADE", this.onRefeshList, this);
        facade.subscribe(Initializer.playerProxy.PLAYER_CLOTH_SUIT_LV,this.onRefeshList,this);
        this.onUpdateClotheList();
    },

    onRefeshList(){
        this.itemList.updateRenders();
    },

    onUpdateClotheList(){
        let type = this.node.openParam.idx;
        let listCfg = localcache.getList(localdb.table_usersuit);
        let listData = [];
        for (var ii = 0; ii < listCfg.length; ii++){
            if (listCfg[ii].type == type){
                listData.push(listCfg[ii]);
            }
        }
        let sortFunc = function(a){
            let flag = true;
            for (let ii = 0; ii < a.clother.length;ii++){
                if (!Initializer.playerProxy.isUnlockCloth(a.clother[ii])){
                    flag = false;
                    break;
                }
            }
            return flag ? 1 : 0;
        };
        listData.sort((a,b)=>{
            if (sortFunc(a) == sortFunc(b)){
                return a.id < b.id ? -1: 1;
            }
            else{
                return sortFunc(a) > sortFunc(b) ? -1: 1;
            }
        })
        Initializer.clotheProxy.clotheList.length = 0;
        Initializer.clotheProxy.clotheList = listData;
        this.itemList.data = listData;
    },

    onClickBack() {
        Utils.utils.closeView(this);
    },

});
