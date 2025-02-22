var i = require("RenderListItem");
var n = require("UrlLoad");
var l = require("UIUtils");
var r = require("Initializer");
let scUtils = require("Utils");

cc.Class({

    extends: i,

    properties: {
        lblLock:cc.Label,
        lblInfo:cc.Label,
        icon:n,
        lockNode:cc.Node,
        lblName:cc.Label,
    },

    showData() {
        var t = this._data;
        if (t) {
            this.lockNode.active = !r.lookProxy.isLock(t);
            let name = "";
            switch(t.unlock) {
                case 0:
                    return;
                case 1:
                    name = localcache.getItem(localdb.table_lookBuild, t.uk_para).name;
                    break;
                case 2:
                    name = localcache.getItem(localdb.table_mainTask, t.uk_para).name;
                    break;
                case 3:
                    name = localcache.getItem(localdb.table_bigPve, t.uk_para).name;
                    break;
                case 4:
                    name = t.uk_para + "";
                    break;
            }
            if(this.lockNode.active) {
                this.lblInfo.string = i18n.t("LOOK_LOCK_TEXT_" + t.unlock, {
                    name: name
                });
            } else {
                this.lblInfo.string = t.text;
            } 
            this.lblName.string = t.name;
            this.icon.url = t.pic > 100 && t.pic < 200 ? l.uiHelps.getXunfangIcon(t.pic) : l.uiHelps.getServantHead(t.pic);
            let scale = t.pic > 100 && t.pic < 200 ? 0.75 : 0.37;
            this.icon.node.setScale(scale, scale);  
        }
    },

    onClickItem() {
        let data = this._data;
        if(!r.lookProxy.isLock(data) || scUtils.stringUtil.isBlank(data.storyid)) {
            return;
        }
        r.playerProxy.addStoryId(data.storyid);
        scUtils.utils.openPrefabView("StoryView", !1, {
            type: 5,
            isSkip: 0
        });
    },
});
