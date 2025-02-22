let scUtils = require("Utils");
let timeProxy = require("TimeProxy");
let initializer = require("Initializer");

cc.Class({
    extends: cc.Component,

    properties: {
    },

    onLoad: function() {
        // let cardList = localcache.getList(localdb.table_card);
        // this.lbCount1.string = initializer.cardProxy.getCardCount(cardList) + '/' + cardList.length;

        // let heroList = initializer.jibanProxy.getJibanFirst(1);
        // let storyCount = 0;
        // for(let i = 0, len = heroList.length; i < len; i++) {
        //     for (let n = 0; n <= 7; n++) {
        //         storyCount += initializer.jibanProxy.getJbItemCount(heroList[i].roleid, n);
        //     }
        // }
        // this.lbCount2.string = storyCount + '/' + localcache.getList(localdb.table_heropve).length;

        // let baowuList = localcache.getList(localdb.table_baowu);
        // let baowuCount = null != initializer.baowuProxy.baowuList ? initializer.baowuProxy.baowuList.length : 0;
        // this.lbCount3.string = baowuCount + '/' + baowuList.length;

        // this.lbCount4.string = initializer.feigeProxy.getOpenFeige().length + '/' + localcache.getFilters(localdb.table_emailgroup, 'fromtype', 1).length;
    },

    onClickItem: function(node, param) {
        let index = parseInt(param);
        let viewName = "";
        let openFunc = null;
        switch(index) {
            case 1:
                openFunc = timeProxy.funUtils.starHome;
                viewName = "card/CardListView";
                break;
            case 2: {
                openFunc = timeProxy.funUtils.jibanView;
                viewName = "jiban/JibanSelect";
            } break;
            case 3:
                viewName = "card/BaowuView";
                break;
            case 4:
                openFunc = timeProxy.funUtils.feigeView;
                viewName = "feige/FeigeView";
                break;
            case 5:
                //openFunc = timeProxy.funUtils.feigeView;
                viewName = "card/FengwuzhiView";
                break;
        }
        if(index == 3) {
            if(null == initializer.baowuProxy.baowuList || initializer.baowuProxy.baowuList.length <= 0) {
                scUtils.alertUtil.alert(i18n.t("NO_BAOWU_TIP"));
                return;
            }
        } else if(null != openFunc && !timeProxy.funUtils.isCanOpenViewUrl(viewName)) {
            return;
        }
        if(viewName != "") {
            scUtils.utils.openPrefabView(viewName);
        }
    },

    onClickBack: function() {
        // change new guide --2020.08.11
        // let max = initializer.taskProxy.mainTask.max;
        // let num = initializer.taskProxy.mainTask.num;
        // if(max == num) {
        //     facade.send(initializer.guideProxy.UPDATE_TRIGGER_GUIDE, {
        //         type: 4,
        //         value: initializer.taskProxy.mainTask.id
        //     });
        // }
        scUtils.utils.closeView(this, !0);
    },
});
