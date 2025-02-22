// 守护刷新确认界面
//node.openParam = {heroid:id}
let Utils = require("Utils");
let Initializer = require("Initializer")
cc.Class({
    extends: cc.Component,

    properties: {
        needMingsheng:cc.Label,
        nameLbl:cc.Label,
        check:cc.Toggle
    },

    onLoad () {

    },

    start () {
        var heroid = this.node.openParam.heroid
        var heroCfg = localcache.getItem(localdb.table_hero,heroid);
        this.nameLbl.string = i18n.t("REFRESH_SHOUHU_1",{name:heroCfg.name})
        this.needMingsheng.string = Utils.utils.getParamInt("jiaoyou_guaji_shuaxin");
    },

    onClickRefresh(){
        if(this.check.isChecked){
            Initializer.jiaoyouProxy.saveOpenRefreshView()
        }
        Initializer.jiaoyouProxy.sendRefreshGuardList(this.node.openParam.heroid)
        Utils.utils.closeView(this);
    },

    onClickBack() {
        Utils.utils.closeView(this);
    },
});
