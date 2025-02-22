

var utils = require("Utils");
var List = require("List");
var Initializer = require("Initializer");
var com_toggle = require("Compment_Toggle");

cc.Class({
    extends: cc.Component,

    properties: {
        sortList: List,
        comtoggle:com_toggle,
        lbltitle:cc.Label,
        scroll:cc.ScrollView,
    },

    onLoad () {
        var params = this.node.openParam;
        this._params = params;
        this.showAllList();
        //this.onClickTab(null, 1);
        //this.tabs1.active = !isShow;
        //this.tabs2.active = isShow;
        //var isShow = params && params.isTianCi || false;
        this.lbltitle.string = this._params.title;
        // this.comtoggle.node.active = !isShow
        // this.comtoggle2.node.active = isShow
        // if (!isShow){
            this.comtoggle.onInitBtnCallback(this,this.onComToggleCallback)
            this.comtoggle.onClickDefault();
        // }
        // else{
        //     this.comtoggle2.onInitBtnCallback(this,this.onComToggleCallback)
        //     this.comtoggle2.onClickDefault();
        // }  
    },

    start () {

    },

    onClickClose() {
        utils.utils.closeView(this);
    },

    onComToggleCallback:function(par){
        var index = 1;
        var isShow = this._params && this._params.isTianCi || false;
        if(isShow) index = 2;
        switch (String(par)) {
            case "1":
                this.showAllList();
                break;
            case "2":
                var xsList
                if (this._params && this._params.isTreasure)
                    xsList = Initializer.baowuProxy.getPoolQualityCard(3);
                else
                    xsList = Initializer.cardProxy.getPoolQualityCard(3,index);
                this.sortList.data = xsList;
                break;
            case "3":
                var ffList
                if (this._params && this._params.isTreasure)
                    ffList = Initializer.baowuProxy.getPoolQualityCard(2);
                else
                    ffList = Initializer.cardProxy.getPoolQualityCard(2,index);
                this.sortList.data = ffList;
                break;
            case "4":
                var ptList
                if (this._params && this._params.isTreasure)
                    ptList = Initializer.baowuProxy.getPoolQualityCard(1);
                else
                    ptList = Initializer.cardProxy.getPoolQualityCard(1,index);
                this.sortList.data = ptList;
                break;
            case "5":
                var ptList = Initializer.cardProxy.getPoolQualityCard(4,2);
                this.sortList.data = ptList;
                break;
        }
        this.scroll.stopAutoScroll();
        this.scroll.scrollToTop();
    },


    showAllList () {
        let index = 1;
        var isShow = this._params && this._params.isTianCi || false;
        if(isShow) index = 2;
        var list = [];
        if (this._params && this._params.isTreasure){
            var xsList = Initializer.baowuProxy.getPoolQualityCard(3);
            var ffList = Initializer.baowuProxy.getPoolQualityCard(2);
            var ptList = Initializer.baowuProxy.getPoolQualityCard(1);
            for (var ii = 0;ii < xsList.length;ii++){
                list.push(xsList[ii]);
            }
            for (var ii = 0;ii < ffList.length;ii++){
                list.push(ffList[ii]);
            }
            for (var ii = 0;ii < ptList.length;ii++){
                list.push(ptList[ii]);
            }
        }
        else{
            let tcList = Initializer.cardProxy.getPoolQualityCard(4,index);
            var xsList = Initializer.cardProxy.getPoolQualityCard(3,index);
            var ffList = Initializer.cardProxy.getPoolQualityCard(2,index);
            var ptList = Initializer.cardProxy.getPoolQualityCard(1,index);

            for (var ii = 0;ii < tcList.length;ii++){
                list.push(tcList[ii]);
            }

            for (var ii = 0;ii < xsList.length;ii++){
                list.push(xsList[ii]);
            }
            for (var ii = 0;ii < ffList.length;ii++){
                list.push(ffList[ii]);
            }
            for (var ii = 0;ii < ptList.length;ii++){
                list.push(ptList[ii]);
            }
        }       
        this.sortList.data = list;
    },

    cutList (list) {
        var groupList = [];
        var infoList = [];
        var temp = 0;
        for (var j = 0; j < list.length; j ++) {
            var childList = list[j];
            var info = {
                startIndex: temp,
                qualityID: list.length - j
            };
            infoList.push(info);
            for (var i = 0; i < childList.length; i++) {
                var groupIndex = temp + Math.floor(i / 3);
                groupList[groupIndex] = groupList[groupIndex] ? groupList[groupIndex] : [];
                groupList[groupIndex].push(childList[i]);
                if (i === childList.length - 1) {
                    temp = groupIndex + 1;
                }
            }
        }
        var groupData = [];
        for (var i = 0; i < groupList.length; i++) {
            var data = {
                list: groupList[i],
                index: i,
                info: infoList
            }
            groupData.push(data);
        }
        return groupData;
    },
    // update (dt) {},
});
