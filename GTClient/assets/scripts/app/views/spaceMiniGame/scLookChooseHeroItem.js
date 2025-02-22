let UrlLoad = require("UrlLoad");
let scInitializer = require("Initializer");
let scUIUtils = require("UIUtils");
import { MINIGAMETYPE } from "GameDefine";

cc.Class({
    extends: cc.Component,

    properties: {
        spHead: UrlLoad,
        nHas: cc.Node,
        nSelected: cc.Node,
        lbLevel: cc.Label,
        lbPropAdd: cc.Label,
        lbChooseName: cc.Label,
        nNoHas: cc.Node,
        lbCondition: cc.Label,
    },

    onLoad: function() {
        facade.subscribe("MINI_HERO_SELECT", this.updateSelect, this);
    },

    setData: function(data, event) {
        this.data = data;
        this.bShow = data.bHas && data.bCan;
        this.nHas.active = this.bShow;
        this.nNoHas.active = !this.bShow;

        if(this.bShow) {
            this.spHead.url = scUIUtils.uiHelps.getServantHead(data.heroid);
            let jibanLevelData = scInitializer.jibanProxy.getHeroJbLv(data.heroid);
            this.lbLevel.string = i18n.t("LOOK_JB_LEVEL", { num: jibanLevelData.level % 1e3 });
            this.lbPropAdd.string = i18n.t("LOOK_PROP_ADD", { num: jibanLevelData.gamebuff });
            this.lbChooseName.string = i18n.t("LOOK_SELECT_HERO", { hero: data.name });

            this.nSelected.active = data.heroid == event.heroid;
        } else {
            if(!data.bHas) {
                this.lbCondition.string = i18n.t("FUYUE_NO_HERO", { hero: data.name, content: data.unlock });
            } else {
                let list = localcache.getList(localdb.table_yoke);
                if(event.type == MINIGAMETYPE.FISH) {
                    for(let i = 0, len = list.length; i < len; i++) {
                        if(i != len - 1 && list[i].fish == 0 && list[i + 1].fish == 1) {
                            this.lbCondition.string = i18n.t("SMG_OPEN_CONDITION", { name: data.name, num: list[i + 1].level % 1e3 });
                            break;
                        }
                    }  
                } else {
                    for(let i = 0, len = list.length; i < len; i++) {
                        if(i != len - 1 && list[i].food == 0 && list[i + 1].food == 1) {
                            this.lbCondition.string = i18n.t("SMG_OPEN_CONDITION", { name: data.name, num: list[i + 1].level % 1e3 });
                            break;
                        }
                    }  
                }
            }
            this.nSelected.active = false;
        }
    },

    updateSelect: function(id) {
        if(this && this.data && this.nSelected) {
            this.nSelected.active = this.data.heroid == id;
        }
    },

    onClickSelf: function() {
        if(!this.bShow) {
            return;
        }
        facade.send("MINI_HERO_SELECT", this.data.heroid);
    },
});
