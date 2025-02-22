cc.Class({
    extends: cc.Component,

    properties: {
        isCheck:false,
        chooseColor:cc.color(92,0,0),
        noColor:cc.color(255,255,255),
    },
    onLoad () {
        this.chooseNode = this.node.getChildByName("chooseNode")
        this.oneLabel = this.node.getChildByName("oneLabel")
        this.setByType();
    },

    start () {

    },
    setHandler(handler,target){
        this.handler = handler
        this.target = target
    },
    setByType(){
        if(this.isCheck){
            this.oneLabel.color = this.chooseColor
        }else{
            this.oneLabel.color = this.noColor
        }
        this.chooseNode.active = this.isCheck
    },
    setCheck(isCheck){
        this.isCheck = isCheck;''
        this.setByType();
    }
    // update (dt) {},
});
