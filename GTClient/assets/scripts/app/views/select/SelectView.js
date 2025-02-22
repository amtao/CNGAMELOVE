var i = require("Utils");
cc.Class({
    extends: cc.Component, 
    properties: {},
    ctor() {
        this.curData = null;
    },
    onLoad() {
        this.curData = this.node.openParam;
    },
    onClickZhaoQin() {
        this.curData && i.utils.openPrefabView("marry/MarryGetView", null, this.curData);
        this.onClickClose();
    },
    onClickTiQin() {
        this.curData && i.utils.openPrefabView("marry/BringUpMarryView", null, this.curData);
        this.onClickClose();
    },
    onClickClose() {
        i.utils.closeView(this);
    },
});
