var i = require("Utils");
cc.Class({
    extends: cc.Component,
    properties: {
        lblSize: cc.Label,
        pro: cc.ProgressBar,
    },
    ctor() {
        this.totalSize = 1;
        this.sizeStr = "";
    },
    onLoad() {
        this.totalSize = this.node.openParam ? this.node.openParam.total: 1;
        var t = this.node.openParam ? this.node.openParam.size: 1;
        1 != t && this.onUpdatePro(t);
        this.sizeStr = i.utils.getSizeStr(this.totalSize);
        facade.subscribe("DWON_SHOW_PROGRESS", this.onUpdatePro, this);
        facade.subscribe("SOUND_DOWN_LOAD_OVER", this.onLoadOver, this);
    },
    onUpdatePro(t) {
        this.lblSize.string = i18n.t("COMMON_NUM", {
            f: i.utils.getSizeStr(t),
            s: this.sizeStr
        });
        this.totalSize = 0 == this.totalSize || null == this.totalSize ? 1 : this.totalSize;
        var e = t / this.totalSize;
        e = e > 1 ? 1 : e;
        this.pro.progress = e;
        t >= this.totalSize && i.utils.closeView(this);
    },
    onLoadOver() {
        i.utils.closeView(this);
    },
    onClickClost() {
        facade.send("DOWN_SHOW_CLOST");
        i.utils.closeView(this);
    },
    onClickCancel() {
        facade.send("DOWN_SHOW_CANCEL");
        i.utils.closeView(this);
    },
});
