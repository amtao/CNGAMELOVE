var renderListItem = require("RenderListItem");
var initializer = require("Initializer");
var utils = require("Utils");

cc.Class({
    extends: renderListItem,

    properties: {
        spBgs: [cc.SpriteFrame],
        itemNode: cc.Sprite,
        lblTitle: cc.Label,
        lblDesc: cc.Label,
        newNode: cc.Node,
    },

    ctor() {
        this._index = 0;
    },

    showData() {
        let storyData = this._data;
        let index = storyData.__index;
        let name = storyData.name;
        this._index = index;
        let showTxt = "";
        if(index == 0) {
            showTxt = i18n.t("CARD_UNLOCK_COND");
        } else {
            showTxt = i18n.t("STAR_UNLOCK_STORY", {
                num: index
            });
        }
        this.lblDesc.string = showTxt;
        this.lblDesc.node.active = !this.isCanStory();
        this.newNode.active = !initializer.cardProxy.storyMap[this._data.id] && this.isCanStory();
        this.lblTitle.string = name;
        if(this.isCanStory()) {
            this.itemNode.spriteFrame = this.spBgs[0];
            this.lblTitle.node.color = cc.color(235, 86, 98);
            this.lblTitle.node.y = -43;
        } else {
            this.itemNode.spriteFrame = this.spBgs[1];
            this.lblTitle.node.color = cc.color(77, 98, 122);
            this.lblTitle.node.y = -24;
        } 
    },

    isCanStory(){
        let cardData = initializer.cardProxy.getCardInfo(this._data.cardId);
        let star = 0;
        if(cardData)
            star = cardData.star;
        else
            return false;
        return star >= this._index;
    },

    onClickStory() {
        if(!this.isCanStory()) {
            return;
        }
        if (!utils.stringUtil.isBlank(this._data.id) && initializer.playerProxy.getStoryData(this._data.id)) {
            let readStory = new proto_cs.card.read_story();
            readStory.storyid = this._data.id;
            JsonHttp.send(readStory);
            initializer.playerProxy.addStoryId(this._data.id);
            utils.utils.openPrefabView("StoryView", !1, {
                // type: initializer.cardProxy.storyMap[this._data.id] ? 3 : 0,
                type: 3,
            });
        }
    },
});
