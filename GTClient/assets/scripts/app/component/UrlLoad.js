var utils = require("Utils");
cc.Class({
    extends: cc.Component,
    properties: {
        url:{
            get: function() {
                return this._url;
            },
            set: function(t) {
                if (this._url != t) {
                    this._url = t;
                    this._lastUrl = t;
                    this.onChangeUrl();
                } else {
                    null != this.loadHandle && this.loadHandle.apply(this.target);
                }
            },
            enumerable: !0,
            configurable: !0
        },
    },

    ctor() {
        this._url = "";
        this._lastUrl = "";
        this._res = null;
        this._resframe = null;
        this.content = null;
        this.loadHandle = null;
        this.target = null;
        this.pRes = null;
    },

    onDestroy() {
        this.loadHandle = null;
        this.target = null;
        this.reset();
        this.clearRes();
        // if(this.pRes != null && this.node.isValid)
        //     this.pRes.decRef();
    },

    reset() {
        this._url = null;
        if (null != this.content) {
            this.content.removeFromParent(!0);
            this.content.destroy();
            this.content = null;
        } else {
            if (null == this.node) return;
            var t = this.node.getComponent(cc.Sprite);
            null != t && null != t.spriteFrame && (t.spriteFrame = null);
        }
        this.clearRes();
    },

    clearRes() {
        if (this._resframe) {
            MemoryMgr.releaseAsset(this._resframe,true);
            this._res = null;
            this._resframe = null;
        }else if (this._res) {
            MemoryMgr.releaseAsset(this._res,true);
            this._res = null;
        }
    },

    onChangeUrl() {
        var t = this,
        e = this._url;
        if (null != e && 0 != e.length) {
            this._url = e;
            this.reset(); 
            - 1 != e.indexOf("res/") ? cc.resources.load(e, cc.SpriteFrame,
            function(o, i) {
                if (null == o && null != i && t._lastUrl == e) {
                    t._res = t._url = e;
                    t._resframe = i;
                    MemoryMgr.saveAssets(i);
                    //t.pRes = i;
                    //t.pRes.addRef();
                    t.node && (t.node.getComponent(cc.Sprite).spriteFrame = i);
                    null != t.loadHandle && t.loadHandle.apply(t.target);
                } else cc.warn(o);
            }) : -1 != e.indexOf("prefabs/") ? cc.resources.load(e,
            function(o, i) {
                if (null == o && null != i && t._lastUrl == e) {
                    t.node && t.node.childrenCount > 0 && t.reset();
                    t._url = e;
                    t._res = i;
                    MemoryMgr.saveAssets(i);
                    //t.pRes = i;
                    //t.pRes.addRef();
                    var n = cc.instantiate(i);
                    t.content = n;
                    t.node && t.node.addChild(n);
                    null != t.loadHandle && t.loadHandle.apply(t.target);
                } else cc.warn(o);
            }) : -1 != e.indexOf("http") && cc.assetManager.loadRemote(e,
            function(o, i) {
                if (null != i && t._lastUrl == e) {
                    t._res = t._url = e;
                    //t.pRes = i;
                    //t.pRes.addRef();
                    var n = new cc.SpriteFrame(i);
                    t.node && (t.node.getComponent(cc.Sprite).spriteFrame = n);
                    null != t.loadHandle && t.loadHandle.apply(t.target);
                } else cc.warn(o);
            });
        } else this.reset();
    },
});
