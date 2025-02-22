

var n = require("Initializer");
var l = require("Utils");
cc.Class({
    extends: cc.Component,

    properties: {

        listSpine:[sp.Skeleton],
        itemNumber:cc.Label,
        time:cc.Label,
        numberList:[cc.Label],


        // foo: {
        //     // ATTRIBUTES:
        //     default: null,        // The default value will be used only when the component attaching
        //                           // to a node for the first time
        //     type: cc.SpriteFrame, // optional, default is typeof default
        //     serializable: true,   // optional, default is true
        // },
        // bar: {
        //     get () {
        //         return this._bar;
        //     },
        //     set (value) {
        //         this._bar = value;
        //     }
        // },
    },

    // LIFE-CYCLE CALLBACKS:

    onLoad () {},

    start () {
        this.index = 0
        this.ist = false
        this.endtime = n.unionProxy.partyEndRemTime()+2
        this.reshowTime()
        this.updateView()
    },
    onClickPotBack(t,id){
        id = parseInt(id)
        if(this.ist){
            return
        }
        n.unionProxy.sendThrowPot(id,()=>{
            this.updateView()
        })
    },
    updateView(){
        let potdata = n.unionProxy.throwPotData.potInfo
        let mid = n.playerProxy.userData.uid
        for (let i = 0; i < 3; i++) {
            if(potdata[i+1]){
                let arrays = potdata[i+1]
                let len = arrays.length
                this.numberList[i].string = len
                if(this.ist){continue}
                for (let j = 0; j < len; j++) {
                    if(mid === arrays[j]){
                        this.setAnimate(i)
                        break;
                    }
                }
            }
        }
        if(this.ist){
            this.itemNumber.string = "0"
        }
    },
    eventClose() {
        l.utils.closeView(this);
    },
    setAnimate(index){
        this.ist = true
        this.listSpine[index].setAnimation(0, "animation", false);
    },
    reshowTime(){
        let timem = this.endtime
        if(this.endtime <= 0){
            n.unionProxy.getPotAward()
            l.utils.closeView(this);
            return
        }
        let h = l.utils.fullZero(parseInt(timem/60/60),2) 
        let s = l.utils.fullZero(parseInt(timem%60),2)
        let m = l.utils.fullZero(parseInt((timem/60)%60),2)
        let strings = ""+h+":"+m+":"+s
        this.time.string = strings
    },
    update (dt) {
        if(this.endtime>=0){
            this.index+=dt
            if(this.index>=1){
                this.index-=1
                this.endtime--
                this.reshowTime()
            }
        }
    },
});
