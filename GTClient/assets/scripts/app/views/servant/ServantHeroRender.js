var i = require("RenderListItem");
var n = require("UrlLoad");
var l = require("Initializer");
var r = require("UIUtils");
var a = require("ShaderUtils");
var s = require("Utils");
var c = require("ServantStarShow");
var _ = require("GuideItem");
cc.Class({
    extends: i,
    properties: {
        icon_1: n,
        icon_2: n,
        iconArr: [cc.SpriteFrame],
        lblName: cc.Label,
        roleImg: n,
        mask: cc.Sprite,
        buttons: [cc.Button],
        lblLv: cc.Label,
        iconNode: cc.Node,
        itemNode: cc.Node,
        starShow: c,
        redNode: cc.Node,
        leader: cc.Node,
        btnXianYun: cc.Node,
    },
    ctor() {
        this.isGuideID = !1;
        this.heroSys = null;
        this.roleSp = null;
    },
    onLoad() {},
    showData() {
        var t = this._data;
        this.heroSys = t;
        this.leader.active = 0 != this.heroSys.leaderid;
        if (t) {
            var e = l.servantProxy.getHeroData(t.heroid);
            this.roleImg.url = r.uiHelps.getServantSmallSpine(t.heroid);
            this.starShow.setValue(t.star);
            if (e) {
                this.lblName.string = t.name;
                this.lblLv.string = i18n.t("COMMON_LV_TXT") + e.level;
                this.mask.node.active = !1;
                this.iconNode.active = !0;
                this.redNode.active = l.servantProxy.getLevelUp(e) || l.servantProxy.getTanlentUp(e) || l.servantProxy.getSkillUp(e) || l.servantProxy.isCanTiBa(e);
            } else {
                this.lblName.string = t.name;
                this.mask.node.active = !0;
                this.lblLv.string = i18n.t("COMMON_LV_TXT") + 1;
                this.iconNode.active = !1;
                a.shaderUtils.setNodeGray(this.itemNode);
                var o = this;
                this.roleImg.loadHandle = function() {
                    o.roleSp = o.roleImg.getComponentInChildren(sp.Skeleton);
                    o.roleSp.animation = "";
                    if (o.roleSp.node) {
                        a.shaderUtils.setSpineGray(o.roleSp);
                    }
                };
                this.redNode.active = !1;
            }
        }
        t.spec[0] ? (this.icon_1.url = r.uiHelps.getLangSp(t.spec[0])) : (this.icon_1.node.active = !1);
        t.spec[1] ? (this.icon_2.url = r.uiHelps.getLangSp(t.spec[1])) : (this.icon_2.node.active = !1);
        this.isGuideID && this.setGuideId();
        this.btnXianYun && (this.btnXianYun.active = l.xianyunProxy.isXianYun(t.heroid));
    },
    onClickItem() {
        if (l.servantProxy.getHeroData(this.heroSys.heroid)) {
            s.utils.openPrefabView("servant/ServantView", !1, {
                hero: this.heroSys,
                tab: 4
            });
            s.utils.closeNameView("servant/ServantListView", !1);
        } else s.utils.openPrefabView("servant/ServantInfo", !1, this.heroSys);
    },
    setGuideId() {
        var t = this.node.getComponentInChildren(_),
        e = this._data;
        t && e && (t.key = e.heroid.toString());
    },
    onClickXianYun() {
        var t = l.xianyunProxy.getDeskInfoByHid(this.heroSys.heroid);
        if (t) {
            var e = l.xianyunProxy.getDeskInfo(t.id);
            if (e && 0 != e.cd.next && 0 != e.hid) if (s.timeUtil.second >= e.cd.next) s.utils.showConfirm(i18n.t("XIAN_YUN_YI_GUI_LAI_ZHAO_HUI"),
            function() {
                l.xianyunProxy.sendZhaohui(t.id, 0);
            });
            else {
                var o = e.cd.next - s.timeUtil.second,
                i = Math.ceil(o / 86400),
                n = l.xianyunProxy.recall.cash * i;
                s.utils.showConfirmItem(i18n.t("XIAN_YUN_TI_QIAN_TXT", {
                    num: n
                }), 1, l.playerProxy.userData.cash,
                function() {
                    l.playerProxy.userData.cash < n ? s.alertUtil.alertItemLimit(1) : l.xianyunProxy.sendZhaohui(t.id, 1);
                });
            } else s.utils.openPrefabView("xianyun/XianYunSelect");
        }
    },
});
