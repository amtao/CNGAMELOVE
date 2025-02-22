var RenderListItem = require("RenderListItem");
var UrlLoad = require("UrlLoad");
var UIUtils = require("UIUtils");
var Utils = require("Utils")
cc.Class({
    extends: RenderListItem,
    properties: {
        boss: UrlLoad,
        lblWuli: cc.Label,
        btn: cc.Button,
    },
    ctor() {},
    showData() {
        var data = this._data;
        if (data) {
            // this.boss.loadHandle = () => {
            //     this.anchorYPos(this.boss);  
            // }
            //this.boss.url = UIUtils.uiHelps.getServantSmallSpine(data.id);
            this.boss.url = UIUtils.uiHelps.getServantHead(data.id);            
            // 特殊处理 【战斗这里中间那个太监不在正中间】
            // if(data.id == 3)
            //     this.boss.node.position = cc.v2(this.boss.node.position.x-10, this.boss.node.position.y);
            this.lblWuli && (this.lblWuli.string = Utils.utils.formatMoney(data.aep.e1));
        }
    },

    anchorYPos(urlLoadComp) {
        if(urlLoadComp.node.anchorY == 1 && urlLoadComp.content != null) {
            urlLoadComp.node.position = cc.v2(urlLoadComp.node.position.x, this.defaultServantY-urlLoadComp.content.height*urlLoadComp.node.scale);        
        } 
    },

    
    onLoad() {
        //this.defaultServantY = this.boss.node.position.y;
        this.addBtnEvent(this.btn);
    },
});
