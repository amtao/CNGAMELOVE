var i = require("Initializer");
var n = require("Utils");
var l = require("List");
cc.Class({
    extends: cc.Component,
    properties: {
        editAccount: cc.EditBox,
        editPs: cc.EditBox,
        editRegAccount: cc.EditBox,
        editRegPs: cc.EditBox,
        nodeReg: cc.Node,
        nodeLogin: cc.Node,
        list: l,
        nodeAct: cc.Node,
        actToggle: cc.Toggle,
        psToggle: cc.Toggle,
        psRegToggle: cc.Toggle,
    },
    ctor() {},
    onLoad() {
        this.editAccount.placeholder = this.editPs.placeholder = this.editRegAccount.placeholder = this.editRegPs.placeholder = i18n.t("COMMON_INPUT_TXT");
        this.nodeReg.active = !1;
        var t = i.loginProxy.accountList,
        e = t.length > 0 ? t[0] : null;
        this.editAccount.string = null != e ? e.account: "test" + Math.ceil(1e6 * Math.random());
        this.editPs.string = null != e ? e.password: "123456";
        this.actToggle.node.active = t.length > 1;
        this.list.data = t;
        facade.subscribe(i.loginProxy.LOGIN_CLOST_LOGIN, this.closeBtn, this);
    },
    onClickLogin() {
        null != i.loginProxy.quList && 0 != i.loginProxy.quList.length && null != i.loginProxy.pickServer ? i.loginProxy.sendInGame(this.editAccount.string, this.editPs.string) : n.alertUtil.alert(i18n.t("LOGIN_SERVER_DELAY"));
    },
    onClickRegist() {
        this.nodeReg.active = !0;
        this.nodeLogin.active = !1;
        this.editRegAccount.string = "test" + Math.ceil(1e6 * Math.random());
        this.editRegPs.string = "123456";
    },
    onClickBack() {
        this.nodeReg.active = !1;
        this.nodeLogin.active = !0;
    },
    onClickRegOk() {
        i.loginProxy.sendRegister(this.editRegAccount.string, this.editRegPs.string);
        this.editAccount.string = this.editRegAccount.string;
        this.editPs.string = this.editRegPs.string;
        this.closeBtn();
    },
    closeBtn() {
        n.utils.closeView(this);
    },
    onClickActTol() {
        this.nodeAct.active = !this.actToggle.isChecked;
    },
    onClickAct(t, e) {
        var o = e.data;
        if (o) {
            this.editAccount.string = null != o ? o.account: "test" + Math.ceil(1e6 * Math.random());
            this.editPs.string = null != o ? o.password: "123456";
        }
        this.nodeAct.active = !1;
        this.actToggle.isChecked = !0;
    },
    onClickPs() {
        this.editPs.inputFlag = this.psToggle.isChecked ? cc.EditBox.InputFlag.PASSWORD: cc.EditBox.InputFlag.SENSITIVE;
        this.editPs.string = this.editPs.string;
    },
    onClickPsReg() {
        this.editRegPs.inputFlag = this.psRegToggle.isChecked ? cc.EditBox.InputFlag.PASSWORD: cc.EditBox.InputFlag.SENSITIVE;
        this.editRegPs.string = this.editRegPs.string;
    },
});
