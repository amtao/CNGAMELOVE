var i = require("Utils");
var n = require("List");
var l = require("Initializer");
cc.Class({
    extends: cc.Component,
    properties: {
        list: n,
    },
    ctor() {
        this.curData = null;
    },
    onLoad() {
        facade.subscribe(l.sonProxy.UPDATE_SON_SHOW_ERRECT, this.onClickClose, this);
        this.curData = this.node.openParam;
        if (this.curData) {
            this.list.data = l.sonProxy.getUnMarryBySex(1 == this.curData.sex ? 2 : 1);
            l.sonProxy.tiQinObj.tUid = this.curData.fuid;
            l.sonProxy.tiQinObj.tSid = this.curData.sonuid;
        }
    },
    onClickClose() {
        i.utils.closeView(this);
    },
});
