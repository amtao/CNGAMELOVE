var l = require("Initializer");
var r = require("UIUtils");
var s = require("List");
var i = require("Utils");
let ItemSlotUI = require("ItemSlotUI");
cc.Class({
    extends: cc.Component,
    properties: {
        lblname:cc.Label,
        lbllevel:cc.Label,
        lblPropName1:cc.Label,
        lblPropName2:cc.Label,
        lblPropNum1:cc.Label,
        lblPropNum2:cc.Label,
        lblContext1:cc.Label,
        lblContext2:cc.Label,
        btn_active:cc.Button,
        listview:s,
        activedSp:cc.Node,
        item:ItemSlotUI,
    },

    ctor() {
        this.currentJibanid = null;
    },

    onLoad() {     
        facade.subscribe("SERVANT_TOKENFETTER_UPDATE", this.onUpdateButtonState, this);
        var t = this.node.openParam;
        if (t){
            var cg = t.cfg;
            var info = t.sdata;
            this.lblname.string = cg.name;
            this.lbllevel.string = info.lv;
            var lv = info.lv;
            var rad = 1;
            if (lv > 1){
                for (var kk = 2; kk <= lv;kk++ ){
                    var _mm = localcache.getItem(localdb.table_tokenlvup,kk);
                    if (_mm == null){
                        console.error("kk:",kk);
                    }
                    else
                        rad *= (1+_mm.attri/100);
                }            
            }
            var proplist = cg.type[2];
            if (proplist.length == 2){
                this.lblPropName1.string = l.servantProxy.getPropName(proplist[0].prop);
                this.lblPropNum1.string = Math.ceil(proplist[0].value * rad);
                this.lblPropName2.string = l.servantProxy.getPropName(proplist[1].prop);
                this.lblPropNum2.string = Math.ceil(proplist[1].value * rad);
            }
            else{
                this.lblPropName1.string = l.servantProxy.getPropName(proplist[0].prop);
                this.lblPropNum1.string = Math.ceil(proplist[0].value * rad);
                this.lblPropNum2.string = "";
                this.lblPropName2.string = "";
            }
            this.item.data = {id:cg.id,kind:200,count:1}
            this.lblContext1.string = cg.explain;
            this.activedSp.active = false;
            this.btn_active.node.active = false;
            this.lblContext2.string = "";
            // var st = localcache.getList(localdb.table_tokenfetters);
            // for (let info of st){
            //     for (let ii of info.xinwuid){
            //         if (ii == cg.id){                     
            //             var cstr_ = info.name + "\n\n" + i18n.t("EFFECT_JIBAN")+"\n";
            //             var pp = info.attri;
            //             var _tpdata = [];
            //             for (let hh of pp){
            //                 cstr_ += l.servantProxy.getPropName(hh.prop) + " +" + hh.value + "%         ";
            //             }
            //             this.lblContext2.string = cstr_;
            //             var flag = true;
            //             for (let ee of info.xinwuid){
            //                 var jj = l.servantProxy.isActiveToken(ee);
            //                 if (!jj) flag = false;
            //                 _tpdata.push({itemid:ee,active:jj})
            //             }
            //             this.currentJibanid = info.id;                 
            //             this.listview.data = _tpdata;
            //             var isf = l.servantProxy.isActiveFetter(t.heroid,info.id);
            //             this.btn_active.interactable = flag && !isf
            //             this.activedSp.active = isf;
            //             this.btn_active.node.active = !isf;
            //             return;
            //         }
            //     }
            // }
        }
        
    },

    onClickJiBan(){
        l.servantProxy.sendFetterActivation(this.node.openParam.heroid,this.currentJibanid);
    },

    onUpdateButtonState(){
        var t = this.node.openParam;
        var isf = l.servantProxy.isActiveFetter(t.heroid,this.currentJibanid);
        this.activedSp.active = isf;
        this.btn_active.node.active = !isf;
    },

    onClose() {
        i.utils.closeView(this);
    },



    // update (dt) {},
});
