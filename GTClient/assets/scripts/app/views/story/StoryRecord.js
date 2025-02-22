var l = require("Utils");
var r = require("Initializer");
let list = require("List");
var storyBigItem = require("StoryBigItem");
var storysmallitem = require("StoryMidItem");

cc.Class({
    extends: cc.Component,

    properties: {
        scorll: cc.ScrollView,
        bigItem:storyBigItem,
        smallitem:storysmallitem,
        nodeSmall:cc.Node,
    },

    ctor(){
        this.curIndex = 1;
        this.bmaps = [];
        this._isShowStory = false;
        this.smallItemList = [];
        this.chooseId = 0;
    },

    onLoad() {
        this.curIndex = 1;
        facade.subscribe("STORY_END_RECORD", this.onStoryEnd, this);
        facade.subscribe("STORY_RECORD_CLICK", this.onClickBig, this);
        this.bigItem.node.active = false;
        this.smallitem.node.active = false;
        this.smallItemList.push(this.smallitem);
        this.nodeSmall.active = false;
        this.showIndex();
        // let self = this;
        // this.listChapter.selectHandle = (data) => {
        //     self.onClickBig(data, self);
        // }
    },

    onStoryEnd() {
        this._isShowStory = false;
    },

    showIndex() {
        this.bmaps = [];
        let maps = r.playerProxy.userData.bmap;
        for (let i = 1; i <= maps; i++) {
            let mapData = localcache.getItem(localdb.table_bigPve, i);
            this.bmaps.push(mapData);
            if (i == 1){
                this.bigItem.node.active = true;
                this.bigItem.data = mapData;
                continue;
            }
            let item = cc.instantiate(this.bigItem.node);
            item.active = true;
            item.getComponent(storyBigItem).data = mapData;
            this.bigItem.node.parent.addChild(item);
        }
        //this.listChapter.data = this.bmaps;
        this.scheduleOnce(this.onDelayScroll, 0.3);
    },

    onDelayScroll() {
        this.scorll.scrollToTop();
    },

    onClickBig(data) {
        if (this.chooseId == data.id && this.nodeSmall.active){
            this.nodeSmall.active = false;
            return;
        }
        this.chooseId = data.id
        let array = [];
        for (let pve = localcache.getGroup(localdb.table_midPve, "bmap", data.id), i = 0; i < pve.length; i++) {
            array.push(pve[i]);
            array.sort(function(a, b) {
                return a.id - b.id;
            });
        }
        array.push(data);
        for (var ii = 0;ii < this.smallItemList.length;ii++){
            this.smallItemList[ii].node.active = false;
        }
        for (var ii = 0;ii < array.length;ii++){
            if (ii >= this.smallItemList.length){
                let item = cc.instantiate(this.smallitem.node);
                this.nodeSmall.addChild(item);
                item.active = true;
                let sItem = item.getComponent(storysmallitem)
                sItem.data = array[ii];
                this.smallItemList.push(sItem);
            }
            else{
                this.smallItemList[ii].node.active = true;
                this.smallItemList[ii].data = array[ii];
            }
        }
        this.nodeSmall.active = true;
        let idx = this.bmaps.indexOf(data);
        this.nodeSmall.setSiblingIndex(idx+1);
        //this.nodeSmall.zIndex = idx+1;
        // this.listLitterChapter.data = array;
        // this.listLitterChapter.node.x = -array.length / 2 * 70;
    },

    onClickCurIndex(t, e) {
        var o = parseInt(e),
        i = r.playerProxy.userData.bmap;
        i = Math.floor((i - 1) / 10);
        this.showIndex();
    },

    onClickMid(t, e) {
        if (!this._isShowStory) {
            r.playerProxy.storyIds = [];
            this._isShowStory = true;
            var o = e.data;
            if (null == o.mname) {
                var i = e.data;
                0 != (n = i.bossStoryId) && r.playerProxy.getStoryData(n) && r.playerProxy.addStoryId(n);
                0 != (n = i.endStoryId) && r.playerProxy.getStoryData(n) && r.playerProxy.addStoryId(n);
            } else {
                var n;
                0 != (n = o.storyId) && r.playerProxy.getStoryData(n) && r.playerProxy.addStoryId(n);
                0 != (n = localcache.getItem(localdb.table_smallPve, o.id).endStoryId) && r.playerProxy.getStoryData(n) && r.playerProxy.addStoryId(n);
            }
            r.playerProxy.storyIds.length > 0 && l.utils.openPrefabView("StoryView", !1, {
                type: 99
            });
        }
    },

    onClickClost() {
        r.playerProxy.storyIds = [];
        l.utils.closeView(this);
    },
});
