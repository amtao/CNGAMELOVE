var i = require("RenderListItem");
var n = require("UrlLoad");
var l = require("UIUtils");
var r = require("Utils");
var a = require("Initializer");
var s = require("ChildSpine");
cc.Class({
    extends: i,
    properties: {
        imgHead: n,
        lblName: cc.Label,
        lblChapt: cc.Label,
        nodeReaded: cc.Node,
        nodeUnread: cc.Node,
        childSpine: s,
        childSpineSmall: s,
        btnBg: cc.Button,
        backgroundSprite: [cc.SpriteFrame],
        bgSprite: cc.Sprite,
    },
    ctor() {},
    onClick() {
        if (a.feigeProxy.readingSonMail) r.alertUtil.alert18n("SON_IS_READING_MAIL");
        else if (a.feigeProxy.lookSonFeige) {
            var t = this._data;
            r.utils.openPrefabView("feige/FeigeDetail", !1, t);
            a.feigeProxy.sonFeigeData = t;
        } else {
            var e = this._data;
            e && r.utils.openPrefabView("feige/FeigeDetail", !1, e);
        }
    },
    onLoad() {
        if(this.imgHead)
            this.defaultImgHeadY = this.imgHead.node.position.y;
    },
    showData() {
        var t = this._data;
        if (a.feigeProxy.lookSonFeige && t && null != t.sid) {
            var e = a.sonProxy.getSon(t.sid);
            this.lblName.string = e.name;
            var o = localcache.getItem(localdb.table_lookBuild, t.city);
            this.lblChapt.string = i18n.t("SON_LI_LIAN_MAIL_TXT", {
                son: e.name,
                city: o ? o.name: ""
            });
            // this.lblName.node.color = t.select.length > 0 ? cc.color(91, 74, 78) : cc.color(226, 0, 53);
            //this.lblChapt.node.color = t.select.length > 0 ? cc.color(96, 87, 87) : cc.color(135, 49, 49);
            this.nodeReaded.active = t.select.length > 0;
            this.nodeUnread.active = t.select.length > 0 //0 == t.select.length;
            // this.btnBg.interactable = 0 == t.select.length;
            //this.bgSprite.spriteFrame = 0 == t.select.length ? this.backgroundSprite[0] : this.backgroundSprite[1];
            e.state > 3 ? this.childSpine.setKid(e.id, e.sex) : this.childSpineSmall.setKid(e.id, e.sex, !1);
            this.childSpine.node.active = e.state > 3;
            this.childSpineSmall.node.active = e.state <= 3;
            this.imgHead && (this.imgHead.node.active = !1);
        } else {
            this.childSpine.node.active = !1;
            this.childSpineSmall.node.active = !1;
            var i = this._data;
            if (i && null != i.title) {
                this.lblName.string = i.title;
                this.lblChapt.string = i.des;
                this.nodeReaded.active = a.feigeProxy.isRead(i.id);
                this.nodeUnread.active = a.feigeProxy.isRead(i.id) //!this.nodeReaded.active;
                // this.btnBg.interactable = !a.feigeProxy.isRead(i.id);
                //this.bgSprite.spriteFrame = !a.feigeProxy.isRead(i.id) ? this.backgroundSprite[0] : this.backgroundSprite[1];
                // this.lblName.node.color = a.feigeProxy.isRead(i.id) ? cc.color(91, 74, 78) : cc.color(226, 0, 53);
                //this.lblChapt.node.color = a.feigeProxy.isRead(i.id) ? cc.color(96, 87, 87) : cc.color(135, 49, 49);
                // switch (i.fromtype) {
                // case 1:
                    this.imgHead.node.active = !0;

                    this.imgHead.loadHandle = () => {
                        this.servantAnchorYPos(this.imgHead);              
                    };
                    this.imgHead.url = l.uiHelps.getServantHead(i.heroid);
                //     break;
                // }
            }
        }
    },

    servantAnchorYPos(urlLoadComp) {
        if(urlLoadComp.node.anchorY == 1 && urlLoadComp.content != null) {
            urlLoadComp.node.position = cc.v2(urlLoadComp.node.position.x, this.defaultImgHeadY-urlLoadComp.content.height*urlLoadComp.node.scale);        
        } 
    },
});
