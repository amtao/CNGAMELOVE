
cc.Class({
    extends: cc.Component,
    properties: {
        lblContext: cc.Label,
        //spine: n,
    },
    ctor() {
        this.maxWidth = 420;
    },

    addLabaData(data){
        let msg =  `${data.user.name}:${data.msg}`;
        this.lblContext.node.active = true;
        this.lblContext.string = msg;
        this.lblContext.node.stopAllActions();
        this.lblContext.node.x = 0;
        let w = this.lblContext.node.getContentSize().width;
        if (w > this.maxWidth){
            this.playAnimaltion();
        }
    },

    playAnimaltion(){
        let w = this.lblContext.node.getContentSize().width;
        let dt = w/100;
        this.lblContext.node.runAction(cc.sequence(cc.moveBy(dt,cc.p(-w,0)),cc.callFunc(()=>{
            this.playnextAnimaltion();
        })));
    },

    playnextAnimaltion(){
        let w = this.lblContext.node.getContentSize().width;
        let dt = (this.maxWidth)/100;
        this.lblContext.node.x = this.maxWidth;
        this.lblContext.node.runAction(cc.sequence(cc.moveBy(dt,cc.p(-this.maxWidth,0)),cc.callFunc(()=>{
            this.playAnimaltion();
        })));
    },

});
