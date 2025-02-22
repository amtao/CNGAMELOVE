let scRenderItem = require("RenderListItem");
let Initializer = require("Initializer");
var UrlLoad = require("UrlLoad");
var UIUtils = require("UIUtils");
var Utils = require("Utils")
cc.Class({
    extends: scRenderItem,

    properties: {
        lbName: cc.Label,
        icon: UrlLoad,
    },

    ctor() {},

    showData() {
        let data = this._data;
        if (data) { 
            let cfg = localcache.getItem(localdb.table_chengshi, data.id);
            this.lbName.string = cfg.chengshi;
            this.icon.url = UIUtils.uiHelps.getXunfangIcon(cfg.pic);
        }
    },

    onClickButton(){
        if(null == this._data) {
            return;
        }
        let idx = this._data.id;
        if (idx == null) return;
        let businessInfo = Initializer.businessProxy.businessInfo;
        if (businessInfo.AgTicket <= 0){
            Utils.alertUtil.alert18n("BUSINESS_TIPS23");           
            return;
        }
        if (idx == Initializer.businessProxy.businessInfo.currentCity && Utils.utils.isOpenView("xingshang/UIBusinessCityInfo")){
            Utils.alertUtil.alert18n("BUSINESS_TIPS30");    
            return;
        }
        if (idx == Initializer.businessProxy.businessInfo.currentCity){
            Utils.utils.closeNameView("ItemInfo");
            Utils.utils.closeNameView("xingshang/BusinessBagView");
            Utils.utils.openPrefabView("xingshang/UIBusinessCityInfo", null, {idx: idx});
            return;
        }
        Utils.utils.showConfirm(i18n.t("BUSINESS_TIPS29",{v1:this.lbName.string}), () => {
            Utils.utils.closeNameView("ItemInfo");
            Utils.utils.closeNameView("xingshang/BusinessBagView");
            Utils.utils.closeNameView("xingshang/UIBusinessCityInfo");
            Initializer.businessProxy.sendNextTravel(idx, (data) => {
                if(null != data.a.system && null != data.a.system.errror) {
                    return;
                }
                facade.send("BUSINESS_CLICKCITY",idx)
            });
        });
    },

});
