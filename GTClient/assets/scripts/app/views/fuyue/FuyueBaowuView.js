let List = require("List");
let Initializer = require("Initializer");
var Utils = require("Utils");

cc.Class({
    extends: cc.Component,

    properties: {
        baowuList: List,
        spBtnBG: [cc.Node],
        // nTitles: [cc.Node],
        // seColor: cc.Color,
        // norColor: cc.Color,
        nTips: cc.Node,
        lbTips: cc.Label,
        nodeNullBaowu:cc.Node,
        nodeBtn:cc.Node,
    },

    ctor(){
        this.m_type = 1;
    },

    onLoad: function() {
        let param = this.node.openParam;
        let type = Initializer.businessProxy.BAOWULIST_TYPE.FUYUE;
        if (param.type != null) {
            type = param.type;
        }
        this.m_type = type;
        this.nodeNullBaowu.active = false;
        if (this.m_type == Initializer.businessProxy.BAOWULIST_TYPE.BUSINESS){
            this.nodeBtn.active = false;
        }
        this.iOpen = param.open;
        this.id1 = param.id1;
        this.id2 = param.id2; //仅初始化用, 后面需要再用需要重新赋值
        this.checkCondition({ set: Initializer.fuyueProxy.conditionType.baowu, id: this.iOpen == 1 ? this.id1 : this.id2 });
        this.onToggleValueChange(null, "0");
        facade.subscribe(Initializer.fuyueProxy.TEMP_REFRESH_SELECT, this.checkCondition, this);
        facade.subscribe("UPDATE_BAOWU_LIST",this.updateData,this);

    },

    checkCondition: function(data) {
        if (this.m_type == Initializer.businessProxy.BAOWULIST_TYPE.BUSINESS){
            this.nTips.active = false;
            return;
        }
        let fuyueProxy = Initializer.fuyueProxy;
        fuyueProxy.checkConditionUI(data, data.set, this.baowuList, this.nTips, this.lbTips);
    },

    onClickEnter: function() {
        if (this.m_type == Initializer.businessProxy.BAOWULIST_TYPE.BUSINESS){
            this.onClickBack();
            return;
        }
        this.iOpen == 1 ? (Initializer.fuyueProxy.iSelectBaowu = this.baowuList.chooseId)
         : (Initializer.fuyueProxy.iSelectBaowu1 = this.baowuList.chooseId);
        facade.send(Initializer.fuyueProxy.REFRESH_SELECT_INFO, [Initializer.fuyueProxy.conditionType.baowu]);
        facade.send(Initializer.fuyueProxy.REFRESH_BAOWU);
        this.onClickBack();

    },

    onClickBack: function() {        
        Utils.utils.closeView(this);
    },

    updateData: function() {
        this.onToggleValueChange(null, this.index);
    },

    onToggleValueChange: function(tg, index) {
        this.index = index;
        let pIndex = parseInt(index);
        for(let i = 0, len = this.spBtnBG.length; i < len; i++) {
            let bSelected = i == pIndex;
            this.spBtnBG[i].active = bSelected;
            //this.nTitles[i].color = bSelected ? this.seColor : this.norColor;
        }
        let dataList = index == 0 ? localcache.getList(localdb.table_baowu)
         : localcache.getFilters(localdb.table_baowu, 'fenye', pIndex);
        dataList = Initializer.baowuProxy.resortList(dataList);
        switch(this.m_type) {
            case Initializer.businessProxy.BAOWULIST_TYPE.BUSINESS:{
                dataList = dataList.filter((tmpData) => {
                    return tmpData.bHas && Initializer.businessProxy.selectBaoWuDic[tmpData.id] == null;
                });               
            }
            break;
            default: {
                let self = this;
                dataList = dataList.filter((tmpData) => {
                    if(self.iOpen == 2)
                        return tmpData.bHas && tmpData.id != Initializer.fuyueProxy.iSelectBaowu;
                    else
                        return tmpData.bHas && tmpData.id != Initializer.fuyueProxy.iSelectBaowu1;
                });
            }
            break;
        };
        this.nodeNullBaowu.active = dataList.length == 0;
        this.baowuList.data = dataList;
        this.baowuList.iOpen = this.iOpen;
        this.baowuList.m_type = this.m_type;
    },

    onClickXuYuan(){
        Utils.utils.openPrefabView("xuyuan/MainVowView");
    },
    
});
