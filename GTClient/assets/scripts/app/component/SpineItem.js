cc.Class({
    extends: cc.Component,
    properties: {
        spine: sp.Skeleton,
        shadow: sp.Skeleton,
    },
    ctor() {
        this.aStr = "";
        this.skin = "";
    },
    actionString(t) {
        if (this.aStr != t) {
            this.aStr = t;
            this.spine && (this.spine.animation = t);
            this.shadow && (this.shadow.animation = t);
        }
    },
    actionSkinString(t, e) {
        if (this.skin != t || this.aStr != e) {
            this.skin = t;
            this.aStr = e;
            if (this.spine) {
                this.spine.animation = e;
                this.spine.setSkin(t);
            }
            if (this.shadow) {
                this.shadow.animation = e;
                this.shadow.setSkin(t);
            }
        }
    },
});
