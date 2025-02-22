let scRenderItem = require("RenderListItem");
let scUrlLoad = require("UrlLoad");
let scUtils = require("Utils");
let scUIUtils = require("UIUtils");
let scInitializer = require("Initializer");

cc.Class({
    extends: scRenderItem,

    properties: {
        lbStoryName: cc.Label,
        lbProgress: cc.Label,
        lbHeroName: cc.Label,
        nBtnReplace: cc.Node,
        nBtnDelete: cc.Node,
        urlServant: scUrlLoad,
    },

    showData: function() {
        let data = this.data;
        if(data) {
            let storyCfgData = localcache.getItem(localdb.table_zonggushi, data.storyId);
            this.lbStoryName.string = storyCfgData.name;
            this.lbProgress.string = i18n.t("FUYUE_PROGRESS", { num: parseInt(data.perfect) });
            let heroData = localcache.getItem(localdb.table_hero, storyCfgData.hero_id);
            this.lbHeroName.string = heroData.name;
            let bReplace = !!data.param;
            this.nBtnDelete.active = !bReplace;
            this.nBtnReplace.active = bReplace;
            // this.urlServant.url = data.herodress > 0 ? scUIUtils.uiHelps.getServantSkinSpine(localcache.getItem(localdb.table_heroDress, data.herodress).model)
            //  : scUIUtils.uiHelps.getServantSpine(storyCfgData.hero_id);
            this.urlServant.url = scUIUtils.uiHelps.getServantHead(storyCfgData.hero_id);
        }
    },

    onClickLook: function() {
        let data = this.data;
        if(data) {
            if(data.param) {
                scInitializer.fuyueProxy.reqSaveNoStory();
            }
            let arrStory = [];
            scUtils.utils.copyData(arrStory, data.storyArr);
            scInitializer.playerProxy.addStoryId(arrStory.shift());
            scUtils.utils.openPrefabView("StoryView", !1, {
                type: 93,
                extraParam: {
                    addStoryIds: arrStory,
                    data: data,
                },
                canSkip: true
            });
        }
    },

    onClickDelete: function() {
        let data = this.data;
        if(data) {
            scUtils.utils.showConfirm(i18n.t("FUYUE_DESC32"), () => {
                scInitializer.fuyueProxy.reqDeleteStory(data.uniId);
            });               
        }
    },

});
