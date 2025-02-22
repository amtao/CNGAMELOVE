var RenderListItem = require("RenderListItem");
var UrlLoad = require("UrlLoad");
var Initializer = require("Initializer");
var UIUtils = require("UIUtils");
var TimeProxy = require("TimeProxy");
var List = require("List");
cc.Class({
    extends: RenderListItem,
    properties: {
    	lblDes: cc.Label,
        lblTarget: cc.Label,
        nodeGo: cc.Node,
        nodeGet: cc.Node,
        nodeFin: cc.Node,
        spBg: UrlLoad,
        rwdGroup: List,
    },
    ctor() {},
    showData() {
        var t = this._data;
        if (t) {
            this.lblDes.string = t.name;
            this.lblTarget.string = t.msg;
            let listdata = [];
            for (let ii = 0; ii < t.get.length;ii++){
                let cg = t.get[ii];
                let num = 0
                if (cg.kind == 114){
                    num = Math.ceil(t.fund_buff * cg.count);
                }
                else if(cg.kind == 115){
                    num = Math.ceil(t.ctbt_buff * cg.count);
                }
                else if(cg.kind == 116){
                    num = Math.ceil(t.exp_buff * cg.count);
                }
                listdata.push({kind:cg.kind,count:num,id:cg.id});
            }
            this.rwdGroup.data = listdata;
            this.spBg.url = UIUtils.uiHelps.getUnionTaskIcon(t.icon);
            this.nodeGo.active = false;
            this.nodeGet.active = false;
            this.nodeFin.active = false;
            let cNum = Initializer.unionProxy.getUnionTaskFinishNumById(t.id);
            let isFinish = Initializer.unionProxy.isFinishedByTask(t.id);
            if (isFinish){
                this.nodeFin.active = true;
            }
            else{
                if (cNum >= t.set[0]){
                    this.nodeGet.active = true;
                }
                else{
                    this.nodeGo.active = true;
                }
            }

        }
    },

    onClickGo() {
        var t = this._data;
        t && TimeProxy.funUtils.openView(t.jumpTo);
    },
    onClickGet() {
        var t = this._data;
        // t && Initializer.achievementProxy.sendDailyTask(t.id);
        if (t == null) return;
        Initializer.unionProxy.sendGetTaskRwd(t.id);
    },

});
