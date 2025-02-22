var utils = require("Utils");
var list = require("List");
let UrlLoad = require("UrlLoad");
let UIUtils = require("UIUtils");

cc.Class({
    extends: cc.Component,

    properties: {
        storyList: list,
        lbTitle: cc.Label,
        //spCard: UrlLoad,
    },

    ctor(){
        this._params = null;
    },

    onLoad () {
        facade.subscribe("CARD_STORY_UPDATE",this.updateStoryList,this)
        this._params = this.node.openParam;
        this.initlist();
    },

    start () {

    },

    updateStoryList(){
        this.initlist();
    },

    initlist(){
        let cardData = localcache.getItem(localdb.table_card,this._params.id);
        let storyIdStr = cardData.starstoryid;
        let storyNameStr = cardData.storyname;
        if(!storyIdStr) return;
        let storyIdArr = storyIdStr.split("|");
        let storyNameArr = storyNameStr.split("|");
        let storyIdArr1 = [];
        for(let i = 0 ; i < storyIdArr.length; i++)
        {
            let data = {}
            data.id = storyIdArr[i];
            data.name = storyNameArr[i];
            data.cardId = this._params.id;
            storyIdArr1.push(data);
        }
        this.storyList.data = storyIdArr1;
        this.lbTitle.string = cardData.title;
        //this.spCard.url = UIUtils.uiHelps.getCardFrame(cardData.picture);
    },

    onClickClost() {
        utils.utils.closeView(this,true);
    },
});
