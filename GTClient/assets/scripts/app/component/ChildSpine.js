var i = require("UrlLoad");
var n = require("UIUtils");
cc.Class({
    extends: cc.Component,
    properties: {
        //head: i,
        body: i,
        isSmall:{ default:false, tooltip: "是否使用小套资源" },
        isBottom: { default: false, tooltip: "是否用脚底是锚点" },
    },
    ctor() {},
    onLoad() {
        let self = this;
        this.body.loadHandle = () => {
            if (!self.isSmall && self.body && self.body.content && !self.isBottom) {
                self.body.content.position = cc.v2(self.body.content.x, -self.body.content.height);        
            }
        }
    },
    setKid(t, e, o) {
        void 0 === o && (o = !0);
        var l = t % 2 == 0 ? 2 : (t % 2)
        let url = o ? n.uiHelps.getKidChengBody(l, e) : n.uiHelps.getKidSmallBody(l, e);
        if (this.isSmall) {
            //this.head.url = o ? n.uiHelps.getKidChengHead_2(i, e) : n.uiHelps.getKidSmallHead_2(i, e);
            this.body.url = o ? n.uiHelps.getKidChengBody_2(l, e) : n.uiHelps.getKidSmallBody_2(l, e);
        } else {
            //this.head.url = o ? n.uiHelps.getKidChengHead(i, e) : n.uiHelps.getKidSmallHead(i, e);
            this.body.url = o ? n.uiHelps.getKidChengBody(l, e) : n.uiHelps.getKidSmallBody(l, e);
        }
    },

    clearKid() {
        //this.head.url = "";
        this.body.url = "";
    },
    setMarry(t, e) {
        var o = e % 2 == 0 ? 2 : e % 2;
        //this.head.url = n.uiHelps.getKidChengHead(2 == e ? 0 : o, e);
        this.body.url = n.uiHelps.getKidMarryBody(o, e);
    },
});
