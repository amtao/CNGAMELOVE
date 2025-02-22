
cc.Class({
    extends: cc.Component,

    properties: {
        img_line:cc.Sprite,
        lbltitle:cc.Label,
        selectSp:cc.Sprite,
        par_idx:0,
    },

    // LIFE-CYCLE CALLBACKS:

    onLoad () {

    },

    // update (dt) {},

    onButtonClick:function(){
        var parent = this.node.parent;
        var com = parent.getComponent('Compment_Toggle');
        com.onButtonClick(this.node);
    },

    SetSelect:function(value,color){
        this.selectSp.node.active = value;
        this.lbltitle.node.color = color;
    },

    SetImageLineVisible:function(value){
        this.img_line.node.active = value;
    },

    getParIndex:function(){
        return this.par_idx;
    },
});
