let scList = require("List");
let scInitializer = require("Initializer");
let scUtils = require("Utils");

cc.Class({
    extends: cc.Component,

    properties: {
        rtTitle: cc.RichText,
        storyList: scList,
    },

    onLoad: function() {
        facade.subscribe(scInitializer.fuyueProxy.UPDATE_STORY_MEMORY, this.initData, this);
        facade.subscribe(scInitializer.fuyueProxy.GET_FUYUE_INFO, this.reData, this);
        this.initData(true);
    },

    reData: function() {
        this.node.openParam = null;
        this.initData();
    },

    initData: function(bNotDelete) {
        if(!bNotDelete && this.node.openParam) { //故事替换
            this.node.openParam = null;
            scInitializer.fuyueProxy.reqSaveStory();
            this.onClickBack();
        }
        let memoryData = scInitializer.fuyueProxy.pMemory;
        let vipData = localcache.getItem(localdb.table_vip, scInitializer.playerProxy.userData.vip);
        this.rtTitle.string = i18n.t("FUYUE_STORY_NUM", { val1: memoryData.saveCount ? memoryData.saveCount : 0, val2: vipData.gushi });
        let arrStory = [];
        for(let i in memoryData.cStory) {
            let tmpData = memoryData.cStory[i];
            tmpData.uniId = parseInt(i);
            tmpData.param = this.node.openParam;
            arrStory.push(tmpData);
        }
        arrStory.sort((a, b) => {
            return b.uniId - a.uniId;
        });
        this.storyList.data = arrStory;
    },

    onClickBack: function() {
        if(this.node.openParam) {
            scInitializer.fuyueProxy.reqSaveNoStory();
        }
        scUtils.utils.closeView(this);
    },
});
