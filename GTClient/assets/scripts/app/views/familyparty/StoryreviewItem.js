

let RenderListItem = require("RenderListItem");

var UrlLoad = require("UrlLoad");
var Initializer = require("Initializer");
var UIUtils = require("UIUtils");
let ItemSlotUI = require("ItemSlotUI")
var utils = require("Utils");
cc.Class({
    extends: RenderListItem,

    properties: {

        btns:cc.Button,

        titles:cc.Label,
        spine:UrlLoad,
        time:cc.Label,

    },
    //205   家具

    //206  图纸

    // 207 材料

    // 208  积分

    // LIFE-CYCLE CALLBACKS:

    onLoad () {
        this.btns && this.btns.clickEvents && this.btns.clickEvents.length > 0 && (this.btns.clickEvents[0].customEventData = this);
    },


    //
    start () {
        
    },

    showData() {
        let data = this._data;
        if(data) {
            //HOMEPART_HOMEEDITOR_STORYREVIEW_NAME:"与%{name}的家宴",
            let hname = localcache.getItem(localdb.table_hero, parseInt(data.heroid)).name
            this.titles.string = i18n.t("HOMEPART_HOMEEDITOR_STORYREVIEW_NAME", {
                name: hname
            });
            Initializer.playerProxy.loadPlayerSpinePrefab(this.spine);
            this.time.string = utils.timeUtil.format(data.sTime, "yyyy-MM-dd");
        }
    },

    // update (dt) {},
});
