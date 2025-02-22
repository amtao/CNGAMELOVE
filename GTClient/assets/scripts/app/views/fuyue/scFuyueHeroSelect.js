let scUtils = require("Utils");
let scUrlLoad = require("UrlLoad");
let scInitializer = require("Initializer");
let scHeroItem = require("scFuyueHeroItem");

cc.Class({
    extends: cc.Component,

    properties: {
        spNuzhu: scUrlLoad,
        scItems: [scHeroItem],
    },

    onLoad () {
        if (scInitializer.fuyueProxy.pSelectUserClothe == null) {
            scInitializer.playerProxy.loadPlayerSpinePrefab(this.spNuzhu);
        } else {
            scInitializer.playerProxy.loadPlayerSpinePrefab(this.spNuzhu, null, scInitializer.fuyueProxy.pSelectUserClothe);
        }

        facade.subscribe("SERVANT_UP", this.updateHeroLetters, this);
        this.updateHeroLetters();
    },

    updateHeroLetters: function() {
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
        noArray.sort(sortFunc);
        heroArray = heroArray.concat(noArray);
        for(let j = 0, jLen = this.scItems.length; j < jLen; j++) {
            this.scItems[j].setData(heroArray[j]);
        }
    },

    onClickClose: function() {
        scUtils.utils.closeView(this);
    },
    
});
