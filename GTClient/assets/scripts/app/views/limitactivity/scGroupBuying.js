let scbaseAct = require("scActivityItem");
let uiUtils = require("UIUtils");
let scList = require("List");
let initializer = require("Initializer");

cc.Class({
    extends: scbaseAct,

    properties: {
        lbTitle: cc.Label,
        lbCurPrice: cc.RichText,
        lbLimitTime: cc.Label,
        nTab: cc.Node,
        nTgParent: cc.Node, 
        lbPeopleNum: cc.Label,
        pgPeople: cc.ProgressBar,
        contentList: scList,
    },

    ctor: function() {
        this.tabs = [];
        this.curIndex = 0;
        this.curPeople = 0;
    },

    setData: function(data) {
        this._super();
        this._data = data;
        this.rwdData = [];
        let tabArray = [];
        for(let i = 0, len = data.cfg.rwd.length; i < len; i++) {
            let tmpData = data.cfg.rwd[i];
            if(null == this.rwdData[tmpData.people]) {
                this.rwdData[tmpData.people] = [];
                tabArray.push(tmpData.people);
            }
            this.rwdData[tmpData.people].push(tmpData);
        }
        tabArray.sort((a, b) => {
            return a - b;
        });
        let self = this;
        if(this.tabs.length < tabArray.length) {
            let instantiate = (data) => {
                let node = cc.instantiate(self.nTab);
                node.parent = self.nTgParent;
                node.active = true;
                let script = node.getComponent('scActTab');
                script.setData(data);
                self.tabs.push(script);
            }
            for(let i = 0, len = tabArray.length; i < len; i++) {
                let tabData = tabArray[i];
                if(i < this.tabs.length) {
                    this.tabs[i].node.active = true;
                    this.tabs[i].setData(tabData);
                } else {
                    instantiate(tabData, i);
                }
            }
        } else {
            for(let i = 0, len = tabArray.length; i < len; i++) {
                let tabData = tabArray[i];
                if(i < this.tabs.length) {
                    this.tabs[i].node.active = true;
                    this.tabs[i].setData(tabData);
                } else {
                    this.tabs[i].node.active = false;
                    this.tabs[i].setData(null);
                }
            }
        }
        uiUtils.uiUtils.countDown(data.cfg.info.eTime, this.lbLimitTime, () => {
            if(null != self.lbTime) {
                self.lbLimitTime.string = i18n.t("ACTHD_OVERDUE");
            }
        });
        this.lbTitle.string = data.cfg.msg;
        this.lbCurPrice.string = i18n.t("ACT_CHARGE_NUM", { val: parseInt(data.cons.myPayMoney) });
        if(0 != this.curPeople) {
            this.updateData(this.curPeople);
        } else {
            this.tabs[0].tgSelf.check();
            this.tabs[0].tgSelf._emitToggleEvents();
        }     
        this.checkUnget();
    },

    onTgValueChange: function(tg, people) {
        let iPeople = parseInt(people);
        if(iPeople == this.curPeople) {
            if(null != tg && !tg.isChecked) {
                tg.check();
                tg._emitToggleEvents();
            }
            return;
        } else if(!tg.isChecked) {
            return;
        }
        this.curPeople = iPeople;
        if(null != this.lastTg) {
            this.lastTg.uncheck();
            this.lastTg._emitToggleEvents();
        }
        this.lastTg = tg;
        this.updateData(iPeople);
    },

    updateData: function(people) {
        this.lbPeopleNum.string = this._data.cons.rechargePeople + "/" + people;
        this.pgPeople.progress = this._data.cons.rechargePeople / people;
        this.contentList.data = this.rwdData[people].sort(this.sortList);
    },

    sortList: function(a, b) {
        let rwd = initializer.limitActivityProxy.curSelectData.rwd;
        let i = null == rwd[a.id.toString()] ? -1 : 1,
        j = null == rwd[b.id.toString()] ? -1 : 1;
        return i != j ? i - j : a.id - b.id;
    },

    checkUnget: function() {
        let curData = initializer.limitActivityProxy.curSelectData;
        for(let i = 0, len = this.tabs.length; i < len; i++) {
            let tab = this.tabs[i];
            let people = tab._data;
            if(people) {
                let array = this.rwdData[people];
                let bHas = false;
                for(let j = 0, jLen = array.length; j < jLen; j++) {
                    let data = array[j];
                    if(!curData.rwd[data.id.toString()] && people <= curData.cons.rechargePeople && parseInt(curData.cons.myPayMoney) >= data.need) {
                        bHas = true;
                        break;
                    }
                }
                tab.nRed.active = bHas;
            }
        }
    },
});
