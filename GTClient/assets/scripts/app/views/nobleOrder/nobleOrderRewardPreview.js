

var utils = require("Utils");
var initializer = require("Initializer");
var list = require("List");

cc.Class({
    extends: cc.Component,

    properties: {
        content: cc.Node,
        groupItem: cc.Prefab,
    },

    // LIFE-CYCLE CALLBACKS:

    onLoad () {
        var levelList = initializer.nobleOrderProxy.getSpecialRewardLevelList();
        var normalList = [];
        var specialList = [];
        for (var i = 0; i < levelList.length; i++) {
            var level = levelList[i];
            var data;
            if(initializer.nobleOrderProxy.OrderActID == initializer.limitActivityProxy.NOBLE_ORDER_ID){
                data = localcache.getItem(localdb.table_magnate_rwd, level);
            }else{
                data = localcache.getItem(localdb.table_magnate_new_rwd, level);
            }
            if (!data) continue;
            if (data.pt_rwd) {
                data.pt_rwd.forEach((rwd) => {
                    var obj = {
                        isSpecial: false,
                        level: level,
                        item: rwd
                    }
                    normalList.push(obj);
                })
            }
            if (data.jj_rwd) {
                var rwd = data.jj_rwd[0];
                var obj = {
                    isSpecial: true,
                    level: level,
                    item: rwd
                }
                specialList.push(obj);
            }
        }

        this.createList(normalList, specialList)
    },

    start () {

    },

    onClickClose () {
        utils.utils.closeView(this);
    },

    createList (normalList, specialList) {
        var groupNode1 = cc.instantiate(this.groupItem);
        groupNode1.parent = this.content;
        var group1 = groupNode1.getComponent("nobleOrderRwdPreviewGroup");
        group1.showData(normalList, false);
        var groupNode2 = cc.instantiate(this.groupItem);
        groupNode2.parent = this.content;
        var group2 = groupNode2.getComponent("nobleOrderRwdPreviewGroup");
        group2.showData(specialList, true);
    }


    // update (dt) {},
});
