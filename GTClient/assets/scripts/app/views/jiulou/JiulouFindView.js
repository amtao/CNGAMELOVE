var i = require("Utils");
var n = require("Initializer");
var l = require("UIUtils");
cc.Class({
    extends: cc.Component,
    properties: {
        editbox: cc.EditBox,
        lblName: cc.Label,
        lblType: cc.Label,
        lblSeat: cc.Label,
        lblTime: cc.Label,
        nodeLbl: cc.Node,
        btnGo: cc.Button,
        lblAdd: cc.Label,
    },
    ctor() {},
    onLoad() {
        this.editbox.placeholder = i18n.t("COMMON_INPUT_TXT");
        n.jiulouProxy.yhBaseInfo = null;
        this.updateLook();
        facade.subscribe("JIU_LOU_BASE_INFO", this.updateLook, this);
    },
    updateLook() {
        var t = n.jiulouProxy.yhBaseInfo;
        this.nodeLbl.active = null != t;
        this.btnGo.interactable = null != t;
        if (this.nodeLbl.active) {
            this.lblName.string = t.fname;
            this.lblSeat.string = i18n.t("COMMON_NUM", {
                f: t.xiwei,
                s: t.maxXiWei
            });
            this.lblType.string = t.yhname;
            this.lblAdd.string = t.addPer / 100 + "%";
            0 != t.ltime.next ? l.uiUtils.countDown(t.ltime.next, this.lblTime) : this.lblTime.unscheduleAllCallbacks();
        }
    },
    onClickGo() {
        n.jiulouProxy.selectData = null;
        n.jiulouProxy.sendYhGo(parseInt(this.editbox.string));
        i.utils.openPrefabView("jiulou/JiulouDinnce");
    },
    onClickFind() {
        i.stringUtil.isBlank(this.editbox.string) ? i.alertUtil.alert(i18n.t("JIULOU_INPUT_NO")) : n.jiulouProxy.sendYhFind(parseInt(this.editbox.string));
    },
    onClickClost() {
        i.utils.closeView(this);
    },
});
