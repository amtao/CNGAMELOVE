var Utils = require("Utils");
var UIUtils = require("UIUtils");
var UrlLoad = require("UrlLoad");
var Initializer = require("Initializer");
var List = require("List");
var BagProxy = require("BagProxy");

cc.Class({
    extends: cc.Component,
    properties: {
        lblCount:cc.Label,
        pageView:cc.PageView,
        nodeLeft:cc.Node,
        nodeRight:cc.Node,
        lbContent: cc.Label,
    },
    ctor() {
        this.maxPageNums = 0;
    },
    onLoad() {
        this.maxPageNums = this.pageView.content.childrenCount;
        this.pageView.setCurrentPageIndex(0);
        this.lbContent.string = i18n.t("BUSINESS_HELP0");
        this.nodeLeft.active = false;
    },

    onClickClost() {
        Utils.utils.closeView(this, !0);
    },

    
    onClickLeft(){
        this.nodeRight.active = true;
        let curNum = this.pageView.getCurrentPageIndex();
        curNum--;
        if (curNum <= 0){
            curNum = 0;
        }
        this.pageView.setCurrentPageIndex(curNum);
    },

    onClickRight(){
        this.nodeLeft.active = true;
        let curNum = this.pageView.getCurrentPageIndex();
        curNum++;
        if (curNum >= this.maxPageNums - 1) {
            curNum = this.maxPageNums - 1;
        }
        this.pageView.setCurrentPageIndex(curNum);
    },

    onPageViewEvent(sender, type) {
        let curNum = this.pageView.getCurrentPageIndex();
        this.lbContent.string = i18n.t("BUSINESS_HELP" + curNum);
        if (curNum <= 0) {
            this.nodeLeft.active = false;
        }
        else{
            this.nodeLeft.active = true;
        }
        if (curNum >= this.maxPageNums -1) {
            this.nodeRight.active = false;
        } else{
            this.nodeRight.active = true;
        }
        this.lblCount.string = (curNum + 1) + "/" + this.maxPageNums;
    }

    
    
});
