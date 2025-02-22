var i = require("RenderListItem");
var Utils = require("Utils");
var ItemSlotUI = require("ItemSlotUI");
var UrlLoad = require("UrlLoad");
var UIUtils = require("UIUtils");
var Initializer = require("Initializer");
var List = require("List");
cc.Class({
    extends: i,
    properties: {
        lblCity: cc.Label,
        lblTitle: cc.Label,
        icon:UrlLoad,
        progress:cc.ProgressBar,
        lblNum:cc.Label,
        listAward:List,
        nodeBtn:cc.Node,
        nodeGot:cc.Node,
        nodeLock:cc.Node,
    },
    ctor() {},
    showData() {
        var data = this._data;
        if (data) {
            let t = data.cfg;
            let cg = data.info;
            this.lblCity.string = t.name;
            this.lblTitle.string = t.txt;
            this.nodeLock.active = false;
            this.nodeGot.active = false;
            this.nodeBtn.active = false;
            if (cg != null){
                this.lblNum.string = i18n.t("COMMON_NUM",{f:cg.count,s:t.need[t.need.length - 1]});
                let progressvalue = cg.count / t.need[t.need.length - 1];
                if (progressvalue > 1) progressvalue = 1;
                if (progressvalue <= 0) progressvalue = 0.01;
                this.progress.progress = progressvalue;
                if (progressvalue >= 1){
                    if (cg.isPick == 0){
                        this.nodeBtn.active = true;
                    }
                    else{
                        this.nodeGot.active = true;
                    }
                }
            }
            else{
                this.progress.progress = 0.01;
                this.lblNum.string = i18n.t("COMMON_NUM",{f:0,s:t.need[t.need.length - 1]});
            }
            if (t.type == 6){
                this.icon.url = UIUtils.uiHelps.getXunfangIcon(t.icon);
                this.icon.node.scale = 0.8;
            }
            else{
                this.icon.url = UIUtils.uiHelps.getAchieveIcon(t.icon);
                this.icon.node.scale = 1;
            }
            this.listAward.data = t.rwd;
        }
    },
    onClickGet() {
        Initializer.miniGameProxy.sendPickTaskAward(this._data.cfg.id);
    },

});
