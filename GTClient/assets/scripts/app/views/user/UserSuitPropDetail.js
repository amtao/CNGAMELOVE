var Utils = require("Utils");
var Initializer = require("Initializer");
var List = require("List");

cc.Class({
    extends: cc.Component,

    properties: {
        nodenull: cc.Node,
        listItem: List,
        nodeList: cc.Node,
    },

    ctor() { },

    onLoad() {
        let param = this.node.openParam;
        this.initView(param);
    },

    initView(lvData) {
        if(null != lvData) {
            let array = [];
            let cfgDatas = localcache.getList(localdb.table_userSuitLv);
            for (var ii = 0; ii < cfgDatas.length; ii++) {
                let data = cfgDatas[ii];
                if(Math.floor(data.id / 1000) / lvData == 1) {
                    array.push(data);
                }
            }
            this.listItem.data = array;
        } else {
            this.nodenull.active = false;
            this.nodeList.active = false;
            let listdata = [];
            let listcfg = localcache.getList(localdb.table_usersuit);
            for (var ii = 0; ii < listcfg.length; ii++) {
                let cg = listcfg[ii];
                let flag = true;
                for (let clothid of cg.clother) {
                    if (!Initializer.playerProxy.isUnlockCloth(clothid)) {
                        flag = false;
                        break;
                    }
                }
                if (flag) {
                    listdata.push(cg);
                }
            }
    
            if (listdata.length > 0) {
                this.nodeList.active = true;
                this.listItem.data = listdata;
            } else {
                this.nodenull.active = true;
            }
        }
    },
   
    onClickClost() {
        Utils.utils.closeView(this, !0);
    },
  
});
