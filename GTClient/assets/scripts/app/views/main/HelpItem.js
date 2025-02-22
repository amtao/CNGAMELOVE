var UIUtils = require("UIUtils")
cc.Class({
    extends: cc.Component,
    properties: {
        helpId:0
    },
    // ctor() {
    //     this.helpId = 0;
    // },
    onLoad() {
        let urlnode = this.node.getComponent("UrlLoad")
        if (urlnode){
            urlnode.url = UIUtils.uiHelps.getHelpPrefab();
        }
    },

    onClickHelp() {
        0 != this.helpId && facade.send("GUIDE_HELP", this.helpId);
    },
});
