let scItem = require("scEventRwdItem");
let Initializer = require("Initializer");

cc.Class({
    extends: cc.Component,

    properties: {
        lbTitle: cc.Label,
        nParent: cc.Node,
        nRwdParent: cc.Node,
        nItem: cc.Node,
    },

    setData: function(cityId) {
        let result = 0;
        this.lbTitle.string = i18n.t("LOOK_SPECIALTY");

        let nRwdParent = cc.instantiate(this.nRwdParent);
        nRwdParent.parent = this.nParent;
        nRwdParent.active = true;

        let game_items = localcache.getList(localdb.table_game_item);

        let fishDatas = game_items.filter((data) => {
            return data.type == 1 && (data.city == cityId
             || data.city == "0" || data.city.indexOf(cityId) > -1);
        });
        fishDatas = this.sortFunc(fishDatas);
        result += 52 + (120 * Math.ceil(fishDatas.length / 4));
        this.setRwd(this.nRwdParent, "LOOK_FISH_RWD", fishDatas);

        let foodDatas = game_items.filter((data) => {
            return data.type == 2 && (data.city == cityId
             || data.city == "0" || data.city.indexOf(cityId) > -1);
        });
        foodDatas = this.sortFunc(foodDatas);
        result += 52 + (120 * Math.ceil(foodDatas.length / 4));
        this.setRwd(nRwdParent, "LOOK_FOOD_RWD", foodDatas);

        this.nParent.getComponent(cc.Layout).updateLayout();
        result += 111;
        this.node.height = result;
        return result;
    },

    sortFunc: function(array) {
        let result = array.filter((data) => {
            return null != Initializer.servantProxy.collectInfo.things[data.id];
        });
        if(result && result.length > 0) {
            result = this.limitFunc(result);

            let noArray = array.filter((data) => {
                return null == Initializer.servantProxy.collectInfo.things[data.id];
            });
            result = result.concat(this.limitFunc(noArray));
        } else {
            result = this.limitFunc(array);
        }
        return result;
    },

    limitFunc: function(array) {
        let limitArray = array.filter((data) => {
            return data.timelimit == 1;
        });
        if(limitArray && limitArray.length > 0) { 
            limitArray.sort(this.sortById);
        }
        let noLimitArray = array.filter((data) => {
            return data.timelimit == 0;
        });
        if(noLimitArray && noLimitArray.length > 0) { 
            noLimitArray.sort(this.sortById);
        }
        let result = limitArray;
        result = result.concat(noLimitArray);
        return result;
    },

    sortById(a, b) {
        return a.id - b.id;
    },

    setRwd: function(node, title, array) {
        node.getComponentInChildren(cc.Label).string = i18n.t(title);
        for(let i = 0, len = array.length; i < len; i++) {
            let nRwd = cc.instantiate(this.nItem);
            nRwd.parent = node;
            nRwd.getComponent(scItem).setData(array[i]);     
            nRwd.active = true;   
        }
    },
});
