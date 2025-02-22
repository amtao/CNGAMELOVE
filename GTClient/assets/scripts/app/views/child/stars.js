cc.Class({
    extends: cc.Component,
    properties: {
        lists:[cc.Node],
    },
    ctor() {},
    onLoad() {
    },
    setValue(t) {
        for (let i = 0; i < 5; i++) {
            if(i<t){
                this.lists[i].active = true
            }else{
                this.lists[i].active = false
            }
        }
    },
});
