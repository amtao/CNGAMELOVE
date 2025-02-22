var UrlLoad = require("UrlLoad");
var UIUtils = require("UIUtils");
var Utils = require("Utils");
var Initializer = require("Initializer");
cc.Class({
    extends: cc.Component,
    properties: {
        imgHead: UrlLoad,        
        lblName: cc.Label,        
        btnBg: cc.Button,
        backgroundSprite: [cc.SpriteFrame],
        bgSprite: cc.Sprite,
    },
    ctor() {},
    onClick() {
        
    },
    onLoad() {
        if(this.imgHead)
            this.defaultImgHeadY = this.imgHead.node.position.y;
    },
    showData(data) {
        if (data) {
            this.lblName.string = data.name;
            // this.btnBg.interactable = !a.feigeProxy.isRead(i.id);
            // this.bgSprite.spriteFrame = !a.feigeProxy.isRead(i.id) ? this.backgroundSprite[0] : this.backgroundSprite[1];
            // this.imgHead.loadHandle = () => {
            //     this.servantAnchorYPos(this.imgHead);              
            // };
            // this.imgHead.url = UIUtils.uiHelps.getServantSmallSpine(data.heroid);
            this.imgHead.url = UIUtils.uiHelps.getServantHead(data.heroid);
        }
    },

    servantAnchorYPos(urlLoadComp) {
        if(urlLoadComp.node.anchorY == 1 && urlLoadComp.content != null) {
            urlLoadComp.node.position = cc.v2(urlLoadComp.node.position.x, this.defaultImgHeadY-urlLoadComp.content.height*urlLoadComp.node.scale);        
        } 
    },
});
