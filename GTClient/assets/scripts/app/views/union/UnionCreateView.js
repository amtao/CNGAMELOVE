var i = require("Utils");
var n = require("Initializer");
cc.Class({
    extends: cc.Component,
    properties: {
        editorName: cc.EditBox,
        //editorPs: cc.EditBox,
        editorDes: cc.EditBox,
        lblCost: cc.Label,
        //toggle: cc.Toggle,
        select:cc.Node,
    },
    ctor() {},
    onLoad() {
        this.editorName.placeholder = this.editorDes.placeholder = i18n.t("COMMON_INPUT_TXT");
        facade.subscribe("UNION_CREATE_SUCESS", this.eventClose, this);
        var t = i.utils.getParamInt("union_build_cost");
        this.lblCost.string = t + "";
    },
    selectBtnBack(){
        if(!this.select){return}
        this.select.active = !this.select.active
    },
    eventClose() {
        i.utils.closeView(this);
    },
    eventCreate() {
        n.unionProxy.sendCreateUnion(this.editorName.string, "", "", "", this.editorDes.string, this.select.active);
    },
});
