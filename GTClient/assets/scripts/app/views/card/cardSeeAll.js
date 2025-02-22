let Utils = require("Utils");
let List = require("List");
let scInitializer = require("Initializer");

cc.Class({
    extends: cc.Component,

    properties: {
        list: List,
    },

    onLoad () {
        let unlock = this.node.openParam.unlock;
        let clist = localcache.getFilters(localdb.table_card_skill, "unlock", unlock);
        if(unlock == 2) { //编队有羁绊的放在最前面
            let listData = [];
            Utils.utils.copyList(listData, clist);
            for(let i = 0, len = listData.length; i < len; i++) {
                let tmpData = listData[i];
                let bJiban = true;
                for(let j = 0, jLen = tmpData.card.length; j < jLen; j++) {
                    if(scInitializer.cardProxy.tmpTeamList.indexOf(tmpData.card[j]) < 0) {
                        bJiban = false;
                        break;
                    }
                }
                tmpData.bJiban = bJiban;
            }
            let sortFunc = (a, b) => {
                return a.id - b.id;
            };
            let jibans = listData.filter((data) => {
                return data.bJiban;
            });
            if(jibans && jibans.length > 0) {
                jibans.sort(sortFunc);
            }
            if(null == jibans) {
                jibans = [];
            }

            let noJibans = listData.filter((data) => {
                return !data.bJiban;
            });
            if(noJibans && noJibans.length > 0) {
                noJibans.sort(sortFunc);
            }
            if(null == noJibans) {
                noJibans = [];
            }

            jibans = jibans.concat(noJibans);
            this.list.data = jibans;
        } else {
            this.list.data = clist;
        }    
    },

    onClickBack() {
        Utils.utils.closeView(this);
    },
});
