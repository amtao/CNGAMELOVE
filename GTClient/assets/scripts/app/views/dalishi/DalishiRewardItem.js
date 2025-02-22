var i = require("RenderListItem");
var n = require("ItemSlotUI");

cc.Class({
    extends: i,

    properties: {
        slot: n,
        btn: cc.Button,
        spAni: sp.Skeleton,
    },

    ctor() {
        this._isShow = !1;
        this.bShow = false;
    },

    onLoad() {
        this.addBtnEvent(this.btn);
        let self = this;
        //动画监听
        this.spAni.setCompleteListener((trackEntry) => {
            let aniName = trackEntry.animation ? trackEntry.animation.name : "";
            if (aniName === 'on1' && !self.bShow) {
                self.bShow = true;
                self.btn.node.active = true;
            } else if(aniName === 'on2') {
                self.slot.node.active = true;
                self.spAni.setAnimation(0, 'idle', true);
            }
        });
        // this.spAni.setEventListener((trackEntry, event) => {
        //     if(event.data.name === "card") {
        //         self.slot.node.active = true;
        //     }
        // });
    },

    showData() {
        let t = this._data;
       
        if (t && null != t.id) {
            this.spAni.setAnimation(0, 'on2', false);
            this.slot.data = t;
            this.slot.node.active = false;
        } else if(t && !this._isShow) {
            this._isShow = true;
            this.spAni.node.active = true;
            //this.spAni.setAnimation(0, 'on1', false);
        }
    },
});
