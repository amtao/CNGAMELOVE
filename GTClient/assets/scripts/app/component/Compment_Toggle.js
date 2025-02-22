var item = require('ToggleItem')
cc.Class({
    extends: cc.Component,
    properties: {
        Nums:{
            get () {
                return this.m_num ? this.m_num:0;
            },
            set (value){
                if (value < 1) return;
                this.m_num = value;
                this.createItem();
            }
        },
        pNode:cc.Node,
        toggleitem:item,
        normalcolor:cc.Color,
        selectcolor:cc.Color,
    },

    // LIFE-CYCLE CALLBACKS:

    ctor(){
        this.m_num = 1;
        this.listItem = [];
        this.btncallback = null;
        this.target = null;
    },

    onLoad () {

    },

    createItem:function(){
        var count = this.pNode.childrenCount;
        if (count < 1) return;
        while(this.pNode.childrenCount > 1){
            var ll = this.pNode.children[0];
            ll.removeFromParent(true);
        }
        for (var mm = 0;mm < this.m_num-1;mm++){
            var instance = cc.instantiate(this.toggleitem.node);
            instance.active = true;
            this.pNode.addChild(instance);
        }
    },

    onInitBtnCallback:function(target,callback){
        this.target = target;
        this.btncallback = callback;
        var num = this.node.childrenCount;
        for (var jj = 0;jj < num;jj++){
            this.listItem.push(this.node.children[jj])    
        }
    },

    onClickDefault:function(){
        for (var m = 0;m < this.listItem.length;m++){
            var ins = this.listItem[m];
            if (ins.active){
                this.onButtonClick(ins);
                return;
            }
        }
    },

    onButtonClick:function(sender){
        var temparr = []
        var chooseidx = 0;
        for (var mm = 0,len = this.listItem.length;mm < len;mm++){
            var ll = this.listItem[mm];
            var titem = ll.getComponent("ToggleItem");
            var flag = (ll == sender);
            titem.SetSelect(flag,flag ? this.selectcolor:this.normalcolor)
            if (ll == sender){
                if (this.btncallback != null){
                    this.btncallback.apply(this.target,[titem.getParIndex()]);
                }
            }
            if (ll.active){
                temparr.push(ll)
                if (flag){
                    chooseidx = temparr.length -1
                }
            }
        }
        for (var o = 0;o < temparr.length;o++){
            var ll = temparr[o];
            var flag = (ll == sender);
            var titem = ll.getComponent("ToggleItem");
            if (o == temparr.length - 1){
                titem.SetImageLineVisible(false);
            }
            else{
                titem.SetImageLineVisible(!flag && chooseidx-1 != o)
            }
        }
    },


    // update (dt) {},
});
