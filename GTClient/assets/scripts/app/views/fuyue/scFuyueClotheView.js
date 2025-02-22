let scUtils = require("Utils");
let scList = require("List");
let scInitializer = require("Initializer");

cc.Class({
    extends: cc.Component,

    properties: {
        clothesList: scList,
        nBtnLeft: cc.Node,
        nBtnRight: cc.Node,
        nTips: cc.Node,
        lbTips: cc.Label,
    },

    onLoad: function() {        
        let chooseDressID = this.node.openParam.dress;

        let heroDress = scInitializer.servantProxy.getHeroAllDress(scInitializer.fuyueProxy.getFriendID());
        if(heroDress) {
            let index = -1;
            let listData = new Array();
            listData.push({ have: true, cfg: null }); //默认非时装
            for(let i = 0, len = heroDress['ownerDress'].length; i < len; i++) {
                listData.push({
                    have: true,
                    cfg: heroDress['ownerDress'][i]
                });
                if(heroDress['ownerDress'][i].id == chooseDressID) {
                    index = i + 1;
                }
            }            
            this.clothesList.data = listData;
            if(index > 0) {
                this.clothesList.selectIndex = index;
                this.onClickChoose(null, { data: listData[index] });
                this.checkCondition(listData[index].id, listData[index]['cfg']);
            } else {
                this.clothesList.selectIndex = 0;
                this.onClickChoose(null, { data: listData[index] });
                this.checkCondition(0, null);
            }
        }
    },

    onClickClose: function() {
        scUtils.utils.closeView(this);
    },

    onClickChoose: function(event, param) {
        let chooseData = param.data;
        if (chooseData) {            
            this.checkCondition(null != chooseData['cfg'] ? chooseData['cfg'].id : 0, chooseData['cfg']);
        }
    },

    checkCondition: function(id, data) {
        let fuyueProxy = scInitializer.fuyueProxy;
        let type = fuyueProxy.conditionType.herodress;
        let condition = fuyueProxy.checkCondition(type, id);
        let isNum = typeof(condition) === 'number';
        let tips = fuyueProxy.getConditionStr(type, isNum ? condition : condition.val, isNum ? null : condition.id);
        if(tips == null) {
            tips = fuyueProxy.getNoneConditionTip(data, type);
        }
        let bHasTip = tips != null;
        this.nTips && (this.nTips.active = bHasTip);
        if(bHasTip) {
            let ani = this.nTips.getComponent(cc.Animation);
            ani && ani.play("fuyue_tip_ani");
        }
        this.lbTips && (this.lbTips.string = bHasTip ? tips : " ");
        facade.send(fuyueProxy.TEMP_REFRESH_SELECT, { set: fuyueProxy.conditionType.herodress, data: data, id: id });
    },
});
