let List = require("List");
let Initializer = require("Initializer");
let Utils = require("Utils");
import { BonusViewType } from 'GameDefine';

cc.Class({
    extends: cc.Component,

    properties: {
        title: cc.Label,
        infoList: List,
    },

    onLoad () {
        this.data = this.node.openParam;
        this.title.string = null == this.data.title ? i18n.t("CRUSH_BONUS_TIP") : this.data.title;
        this.initList();
        facade.subscribe("Update_BonusView_Info", this.initList, this);
    },

    initList() {
        let listData = [];
        let itemList = [];
        switch(this.data.type) {
            case BonusViewType.FishScoreBonus:
                itemList = Initializer.fishingProxy.getFishScoreBonus();
                break;
            case BonusViewType.CrushPassBonus:
                itemList = Initializer.crushProxy.getCrushPassBonus();
                break;
            case BonusViewType.TofuPassBonus:
                itemList = Initializer.tofuProxy.getPassBonus();
                break;
            case BonusViewType.BeachTreasureTask:
                itemList = Initializer.beachTreasureProxy.getTaskBonusRwd();
                break;
            case BonusViewType.BeachTreasureAchieve:
                itemList = Initializer.beachTreasureProxy.getAchieveBonusRwd();
                break;
            case BonusViewType.MoonBattleTask:
                itemList = Initializer.moonBattleProxy.getTaskBonusRwd();
                break;
        }

        for(let i = 0; i < itemList.length; i++) {
            listData.push({
                data: itemList[i],
                type: this.data.type
            });
        }
        let sortFunc = (a, b) => {
            return a.data.id - b.data.id;
        };
        let result = listData.filter((data) => {
            return data.data.get == 0;
        });
        let noneArray = listData.filter((data2) => {
            return data2.data.get == 1;
        });
        result.sort(sortFunc);
        noneArray.sort(sortFunc);
        result = result.concat(noneArray);
        this.infoList.data = result;
    },

    onClickClose() {
        Utils.utils.closeView(this);
    },
});
