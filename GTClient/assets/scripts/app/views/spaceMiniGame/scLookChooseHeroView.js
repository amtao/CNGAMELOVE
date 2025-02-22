let Utils = require("Utils");
let scLookChooseHeroItem = require("scLookChooseHeroItem");
let scInitializer = require("Initializer");
import { MINIGAMETYPE } from "GameDefine";

cc.Class({
    extends: cc.Component,

    properties: {
        arrItem: [scLookChooseHeroItem],
    },

    onLoad () {
        let param = this.node.openParam;

        let heroDataList = localcache.getList(localdb.table_hero);
        let hasData = scInitializer.servantProxy.servantList;

        let heroArray = [];
        let noArray = [];
        for(let i = 0, len = heroDataList.length; i < len; i++) {
            let cfgData = heroDataList[i];
            let curId = cfgData.heroid;
            let tmpData = hasData.filter((data) => {
                return data.id == curId;
            });
            if(tmpData && tmpData.length > 0) {
                cfgData.bHas = true;
                heroArray.push(cfgData);
            } else {
                cfgData.bHas = false;
                noArray.push(cfgData);
            }
        }
        let sortFunc = (a, b) => {
            return a.heroid - b.heroid;
        };
        heroArray.sort(sortFunc);

        let canArray = heroArray.filter((data) => {
            let jbLvData = scInitializer.jibanProxy.getHeroJbLv(data.heroid);
            let bCan = param.type == MINIGAMETYPE.FISH ? jbLvData.fish == 1 : jbLvData.food == 1;
            data.bCan = bCan;
            return bCan;
        });

        let cantArray = heroArray.filter((data) => {
            let jbLvData2 = scInitializer.jibanProxy.getHeroJbLv(data.heroid);
            let bCan = param.type == MINIGAMETYPE.FISH ? jbLvData2.fish == 1 : jbLvData2.food == 1;
            data.bCan = bCan;
            return !bCan;
        });

        canArray.sort(sortFunc);
        cantArray.sort(sortFunc);
        canArray = canArray.concat(cantArray);
        noArray.sort(sortFunc);
        canArray = canArray.concat(noArray);
        for(let j = 0, jLen = this.arrItem.length; j < jLen; j++) {
            this.arrItem[j].setData(canArray[j], param);
        }

        if(null != param.heroid) {
            facade.send("MINI_HERO_SELECT", param.heroid);
        }
    },

    onClickClose: function() {
        Utils.utils.closeView(this);
    },
});
