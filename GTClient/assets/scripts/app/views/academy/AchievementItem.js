var renderListItem = require("RenderListItem");
var n = require("Initializer");
var l = require("Utils");
var r = require("UrlLoad");
var a = require("UIUtils");
var ShaderUtils = require("ShaderUtils");

cc.Class({
    extends: renderListItem,

    properties: {
        node_Fishing:cc.Node,
        img:r,
        bar:cc.ProgressBar,
        label:cc.Label,
        lblName:cc.Label,
        sp:r,
        nodeRed:cc.Node,
        progresssp:r,
        spBg: cc.Sprite,
    },

    showData() {
        var t = this._data;
        if (t) {
            this.lblName.string = t.title;
            this.label.string = t.isOver ? "": i18n.t("COMMON_NUM", {
                f: t.num,
                s: t.need
            });
            this.bar.progress = t.isOver ? 1 : t.percent;
            this.node_Fishing.active = t.isOver;
            this.img.url = a.uiHelps.getAchieveIcon(t.icon);
            ShaderUtils.shaderUtils.setImageGray(this.img.getComponent(cc.Sprite), t.percent < 1);
            ShaderUtils.shaderUtils.setImageGray(this.sp.getComponent(cc.Sprite), t.percent < 1);
            // this.img.node.opacity = t.percent < 1 ? (255 * 0.3) : 255;
            // this.sp.node.opacity = t.percent < 1 ? (255 * 0.5) : 255;
            this.nodeRed.active = t.percent >= 1 && !t.isOver;
            //this.progresssp.url = t.percent < 1 ? a.uiHelps.getAchieveImg("mask_bg_cj_weiman") : a.uiHelps.getAchieveImg("mask_bg_cj_man")
        }
    },
    onClickGet() {
        if (this._data.percent < 1 || this._data.isOver) {
            this.onClickDetail();
            return;
        }
        var t = this._data;
        t && n.achievementProxy.sendGetRwd(t.id);
    },
    onClickDetail(t, e) {
        var o = this._data;
        if (o) {
            n.achievementProxy.setSelectInfo(o.id);
            n.achievementProxy.selectDetail && l.utils.openPrefabView("achieve/AchieveDetail");
        }
    },
    onClickGo() {
        this._data;
    },
});
