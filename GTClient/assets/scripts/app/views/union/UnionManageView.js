var n = require("Initializer");
var l = require("Utils");

cc.Class({
    extends: cc.Component,

    properties: {
        lostLbel: cc.Label,
        btnBossChange: cc.Button,
        btnDissolve: cc.Button,
    },

    ctor() {},

    onLoad() {
        let myInfo = n.unionProxy.clubInfo.members.filter((data)=>{
            return data.id == n.playerProxy.userData.uid;
        });
        this.btnDissolve.interactable = this.btnBossChange.interactable = null != myInfo && myInfo.length > 0 && myInfo[0].post == 1;
        this.lostLbel.string = n.unionProxy.clubInfo.dissolutionTime === 0?i18n.t("UNION_MANAGER_DISMISS"):i18n.t("UNION_MANAGER_CDISMISS")
    },

    eventClose() {
        l.utils.closeView(this);
    },

    onClickBtn(t, e) {
        var o = e.data;
        e = parseInt(e)
        if (e) {
            switch (e) {
            case 1:
                l.utils.openPrefabView("union/UnionModify");
                break;
            case 2:
                l.utils.openPrefabView("union/UnionApply");
                break;
            case 3:
                l.utils.openPrefabView("union/TransferView");
                break;
            case 4:
                n.unionProxy.dialogParam = {
                    type: "dimss"
                };
                if(n.unionProxy.clubInfo.dissolutionTime === 0){
                    l.utils.openPrefabView("union/UnionDismiss");
                }else{
                    n.unionProxy.sendJiesan()
                    this.eventClose()
                }
                
            }
            this.eventClose();
        }
    },
});
