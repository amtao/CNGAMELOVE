var scRenderListItem = require("RenderListItem");
var scUrlLoad = require("UrlLoad");
var scInitializer = require("Initializer");
var scUIUtils = require("UIUtils");

cc.Class({
    extends: scRenderListItem,

    properties: {
        urlAvatar: scUrlLoad,
        urlRank: scUrlLoad,
        lblRank: cc.Label,
        lblName: cc.Label,
        lblHurt: cc.Label,
        lblPos: cc.Label,
    },

    ctor() {},

    showData() {
        var data = this._data;
        if (data) {
            scInitializer.playerProxy.loadUserHeadPrefab(this.urlAvatar, data.headavatar, {
                job: data.job, level: data.level, clothe: data.clothe }, false);
            this.urlRank.url = scUIUtils.uiHelps.getRankBg(data.rank);
            this.lblRank.string = data.rank;
            this.lblName.string = data.name;
            this.lblHurt.string = data.hit;
            this.lblPos.string = scInitializer.unionProxy.getPostion(data.post);
        }
    },
});
