let scList = require("List");
let initializer = require("Initializer");
let scUtils = require("Utils");
var Utils = require("Utils");

cc.Class({
    extends: cc.Component,

    properties: {
        baowuList: scList,
        spBtnBG: [cc.Node],
        ndRedPot: [cc.Node], //红点提示
        nTitles: [cc.Node],
        seColor: cc.Color,
        norColor: cc.Color,
    },

    onLoad: function() {
        this.updateRedPot();
        this.onToggleValueChange(null, "0");

        facade.subscribe(initializer.baowuProxy.DRAW_TREASURE_SETTLEMENT, this.updateRedPot, this);
        facade.subscribe(initializer.baowuProxy.UPDATE_BAOWU_STAR, this.updateData, this);
    },

    onClickBack: function() {
        Utils.utils.openPrefabView("card/ArchiveView");
        scUtils.utils.closeView(this);
    },

    updateRedPot() {
        for(let i = 0, len = this.ndRedPot.length; i < len; i++) {
            let ndRedPot = this.ndRedPot[i];
            ndRedPot.active = i == 0 ? initializer.baowuProxy.checkBaowuAllRedPot()
             : initializer.baowuProxy.checkBaowuRedDotByTitle(i);
            if(ndRedPot.active) {
                scUtils.utils.showNodeEffect(ndRedPot, 0);
            }
        }
    },

    updateData: function() {
        this.onToggleValueChange(null, this.index);
        this.updateRedPot();
    },

    onToggleValueChange: function(tg, index) {
        this.index = index;
        let pIndex = parseInt(index);
        for(let i = 0, len = this.spBtnBG.length; i < len; i++) {
            let bSelected = i == pIndex;
            this.spBtnBG[i].active = bSelected;
            this.nTitles[i].color = bSelected ? this.seColor : this.norColor;
        }
        let dataList = index == 0 ? localcache.getList(localdb.table_baowu)
         : localcache.getFilters(localdb.table_baowu, 'fenye', pIndex);
        dataList = initializer.baowuProxy.resortList(dataList);
        this.baowuList.data = dataList;
    },
    
});
