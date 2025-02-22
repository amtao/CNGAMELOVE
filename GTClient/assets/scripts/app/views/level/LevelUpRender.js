var i = require("RenderListItem");
var n = require("TimeProxy");
var l = require("Utils");
var Initializer = require("Initializer");
cc.Class({
    extends: i,
    properties: {
        lblTitle: cc.Label,
        lblDes: cc.Label,
        lblBtnTitle:cc.Label,
        btn:cc.Button,
    },
    ctor() {},
    showData() {
        var t = this.data;
        if (t) {
            this.lblTitle.string = t.title;
            this.lblDes.string = t.text;
            var i = localcache.getItem(localdb.table_iconOpen, t.iconopenid);
            if (n.funUtils.isOpen(i) && Initializer.playerProxy.userData.level >= t.lv){
                this.lblBtnTitle.string = i18n.t("COMMON_GO");
                this.btn.interactable = true;
            }
            else{
                this.lblBtnTitle.string = i18n.t("COMMON_WEIKAIQI");
                this.btn.interactable = false;
            }

        }
    },
    onClickGo() {
        var t = this.data;
        if (t) {
            if (t.iconopenid == n.funUtils.childLilian.id){
                if (Initializer.sonProxy.childList == null || Initializer.sonProxy.childList.length <= 0){
                    l.alertUtil.alert18n("CHILD_LILIANTIPS");
                    return;
                }                    
            }
            n.funUtils.openView(t.iconopenid);
            l.utils.closeNameView("stronger/LevelUpView");
        }
    },
});
