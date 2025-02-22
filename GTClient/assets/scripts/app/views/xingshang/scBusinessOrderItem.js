let scRenderItem = require("RenderListItem");
let scList = require("List");
let scInitializer = require("Initializer");

cc.Class({
    extends: scRenderItem,

    properties: {
        lbName: cc.Label,
        rewardList: scList,
        nNotFinish: cc.Node,
        nFinished: cc.Node,
    },

    ctor() {},

    showData() {
        let data = this._data;
        if (data) { 
            let getLeaf = scInitializer.businessProxy.getCurLeafNum(),
                bFinish = getLeaf >= data.set;
            this.lbName.string = i18n.t("BUSINESS_LEAF", {num: data.set});
            this.rewardList.data = data.rwd;
            this.nNotFinish.active = !bFinish;
            this.nFinished.active = bFinish;
        }
    },

});
