let scbaseAct = require("scActivityItem");
let scList = require("List");
let uiUtils = require("UIUtils");
let scUtils = require("Utils");

cc.Class({
    extends: scbaseAct,

    properties: {
        lbTitle: cc.Label,
        lbTime: cc.Label,
        contentList: scList,
        nBtnGo: cc.Node,    
    },

    setData: function(data) {
        this._super();
        let self = this;
        uiUtils.uiUtils.countDown(data.cfg.info.eTime, this.lbTime, () => {
            if(null != self.lbTime) {
                self.lbTime.string = i18n.t("ACTHD_OVERDUE");
            }
        });
        this.lbTitle && (this.lbTitle.string = data.cfg.msg);
        this.contentList.data = data.cfg.rwd.sort(this.sortList);
        var imgComp = this.node.getChildByName("nTop").getChildByName("spTitle").getComponent("UrlLoad");
        null != imgComp && (data.skin && 0 != data.skin ? (imgComp.url = uiUtils.uiHelps.getLimitActivityBg(data.skin)) : (imgComp.url = uiUtils.uiHelps.getLimitActivityBg(data.cfg.info.id)));
        let bannerData = localcache.getGroup(localdb.table_banner_title, "pindex", data.cfg.info.id);
        this.nBtnGo && (this.nBtnGo.active = bannerData && bannerData[0] && !scUtils.stringUtil.isBlank(bannerData[0].jump_to));
    }
});
