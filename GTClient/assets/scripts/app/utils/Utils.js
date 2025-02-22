var config = require("Config");
var confirmView = require("ConfirmView");
var initializer = require("Initializer");
var TimeProxy = require("TimeProxy");

function Utils() {
    //-----------------------------------------------------------
    /***颜色品质的颜色*/
    this.BLACK = cc.Color.BLACK;
    this.WHITE = cc.Color.WHITE;
    this.GREEN = cc.Color.WHITE.fromHEX("#12F849");
    this.BLUE = cc.Color.WHITE.fromHEX("#0072f8");
    this.PURPLE = cc.Color.WHITE.fromHEX("#ea00ff");
    this.ORANGE = cc.Color.WHITE.fromHEX("#e55103");
    this.RED = cc.Color.WHITE.fromHEX("#DD1717");
    this.GOLDEN = cc.Color.WHITE.fromHEX("#FFC548");
    this.UNIQUE = cc.Color.WHITE.fromHEX("#FDFAF1");
    this.GRAY = cc.Color.WHITE.fromHEX("#AAAAAA");
    this.BLACK_GREEN = cc.Color.WHITE.fromHEX("#309c1b");
    this.BLACK_RED = cc.Color.WHITE.fromHEX("#9F3E51");
    this._uiMap = {};
    this._midLayer = null;
    this._topLayer = null;
    this._textCache = [];
    this._isExit = !1;
    this._loadPrefabs = {};
    this.isCollect = !1;
    this.releaseObjs = {};
    //获取等待网络连接的UI
    this.waitUI = null;
    //弹出获得提示
    this.poplist = [];
    //资源引用计数
    this.resCountMap = {};
    //正在加载的界面数量
    this._isLoadingNum = 0;
    //是否正在释放资源
    this._isReleasingNum = 0;
    //正在关闭的界面数量
    this._closingNum = 0;

    //界面打开的动画时间
    this.openViewAniDt = 0;

    //frame内存管理
    this._saveObjs = {};
    this._saveCache = [];
    this._saveDir = [];


    /**记录全屏界面，用来处理只有最上层的界面显示，其余的隐藏*/
    this._recordUIPrefabName = [];


    //返回随机数 可选取范围
    this.randomNumBoth = function(Min,Max){
        var Range = Max - Min;
        var Rand = Math.random();
        var num = Min + Math.round(Rand * Range); //四舍五入
        return num;
    }

    //返回随机数 可选取范围 并且支持小数
    this.randomNumCanf = function(Min,Max){
        var Range = Max - Min;
        var Rand = Math.random();
        var num = Min + Rand * Range; 
        return num;
    }

    this.fullZero = function(number,n){
        if(typeof number != 'number'){
            console.error("number must be number") 
            return
        }else if(typeof n != 'number'){
            console.error("n must be number") 
            return
        }
        return (Array(n).join(0) + number).slice(-n); 
    }

    //该url需要释放
    this.isFrameNeedSave = function(url){
        if (this._saveDir.length == 0) {
            return false;
        }
        for (var i = 0; i < this._saveDir.length; i++) {
            if (url.indexOf(this._saveDir[i]) != -1) {
                return true;
            }
        }
        return false;
    };


        this.index2color = function(t) {
            switch (t) {
                case 1:
                    return this.WHITE;

                case 2:
                    return this.BLACK_GREEN;

                case 3:
                    return this.BLUE;

                case 4:
                    return this.PURPLE;

                case 5:
                    return this.ORANGE;

                case 6:
                    return this.RED;

                case 7:
                    return this.GOLDEN;

                case 8:
                    return this.UNIQUE;
            }
            return t > 8 ? this.UNIQUE : this.GRAY;
        };
        this.formatMoney = function(t) {
            return null == t
                ? "0"
                : t < 1e5
                ? t.toString()
                : t < 1e8
                ? (t / 1e4).toFixed(2) + i18n.t("COMMON_WAN")
                : (t / 1e8).toFixed(2) + i18n.t("COMMON_YI");
        };
        this.copyData = function(t, e) {
            if (null != e && null != t)
                if (t instanceof Array && e instanceof Array)
                    this.copyList(t, e);
                else for (var o in e) t[o] = null != e[o] ? e[o] : t[o];
        };

        this.copyList = function(t, e, o, i, n) {
            void 0 === o && (o = "id");
            void 0 === i && (i = !1);
            void 0 === n && (n = "");
            if (null != t && null != e)
                if (0 == e.length) t = e;
                else
                    for (var l = 0; l < e.length; l++) {
                        for (var r = !1, a = 0; a < t.length; a++)
                            if (
                                null != t[a] &&
                                null != e[l] &&
                                t[a][o] &&
                                e[l][o] &&
                                t[a][o] == e[l][o]
                            ) {
                                i && t[a][n] < e[l][n] && (e[l].isNew = !0);
                                this.copyData(t[a], e[l]);
                                r = !0;
                            }
                        if (!r && null != e[l]) {
                            e[l] instanceof Object && (e[l].isNew = !0);
                            t.push(e[l]);
                        }
                    }
        };

        this.clone = function(data){
            var copy = [];
            if (data instanceof Array) {
                for (var i = 0; i < data.length; i++) {
                    copy[i] = data[i];
                }
            }
            return copy;
        };

        //判断两个对象是否等值
        this.isObjectValueEqual = function(a, b) {
            if(a == null && b == null) {
                return true;
            } else if(a == null || b == null) {
                return false;
            }
            var aProps = Object.getOwnPropertyNames(a);
            var bProps = Object.getOwnPropertyNames(b);   
            //判断属性名的length是否一致    
            if (aProps.length != bProps.length) {
                return false;
            }
            //循环取出属性名，再判断属性值是否一致
            for (var i = 0; i < aProps.length; i++) {
                var propName = aProps[i];
                if (a[propName] !== b[propName]) {
                    return false;
                }
            }
            return true;
        };

        this.getArea = function(t) {
            return t < 1e6 ? 999 : t % 1e6;
        };
        this.getUiTotalNum = function() {
            var t = [];
            for (var e in this._uiMap) t.push(e);
            return t.length
        };
        this.clearLayer = function() {
            this._midLayer = null;
            this._topLayer = null;
            this._recordUIPrefabName.length = 0;
            var t = [];
            for (var e in this._uiMap) t.push(e);
            for (var o = 0; o < t.length; o++) this.closeNameView(t[o]);
            this._uiMap = {};
        };
        this.openPrefabView = function(t, e, n, l, sort,checkOpen = true) {
            if(config.Config.DEBUG) {
                console.log(t);
            }
            void 0 === e && (e = !1);
            void 0 === n && (n = null);
            void 0 === l && (l = !1);
            if (checkOpen && !TimeProxy.funUtils.isCanOpenViewUrl(t)){
                return;
            }
            if (!this._isExit) {
                null == this._uiMap && (this._uiMap = {});
                if ("" != t) {
                    t = config.Config.skin + "/prefabs/ui/" + t;
                    if(null != this._uiMap[t]) {
                        this.setViewIndex(t);
                        this._uiMap[t].active = true;
                        this.refreshAllUIByAdd();
                        initializer.guideProxy.guideUI
                         && initializer.guideProxy.guideUI.checkGuide(t.replace(config.Config.skin + "/prefabs/ui/", ""), true);
                    } else {
                        this.loadPrefabView(t, e, n, l, sort);
                    }
                } else exports.alertUtil.alert(i18n.t("MAIN_FUN_UNOPEN"));
            }
        };

        this.setViewIndex = function(t, index) {
            var e = this._uiMap[t];
            e && e.parent && e.setSiblingIndex(null != index ? index : e.parent.childrenCount - 1);
        };

        this.getViewIndex = function (t) {
            var key = config.Config.skin + "/prefabs/ui/" + t;
            var e = this._uiMap[key];
            return e && e.parent ? e.getSiblingIndex() : -1;
        };

        this.isTopView = function (t) {
            var key = config.Config.skin + "/prefabs/ui/" + t;
            var e = this._uiMap[key];
            return (e && e.parent && this.getViewIndex(t) === e.parent.childrenCount - 1) ? true : false;
        };

        this.isLoadingPage = function () {
            return this._isLoadingNum != 0;
        };
    
        this.isReleasing = function () {
            return this._isReleasingNum != 0;
        };

        this.loadPrefabView = function(t, e, i, n, sort) {           
            console.error("loadPrefabView "+t);

            void 0 === e && (e = !1);
            void 0 === i && (i = null);
            void 0 === n && (n = !1);
            var l = this;

            // if (this.isReleasing()) {
            //     return;
            // }
            if (!this._loadPrefabs[t]) {
                this._loadPrefabs[t] = !0;
                this._isLoadingNum = this._isLoadingNum + 1;
                cc.resources.load(t, function(r, a) {
                    l._loadPrefabs[t] = !1;
                    if (null == r && a) {
                        MemoryMgr.saveAssets(a);
                        var s = cc.instantiate(a);
                        if (s) {
                            s.name = t.replace(config.Config.skin + "/prefabs/ui/", "").replace("/", ",");
                            s.__url = t;
                            s._uuid = a._uuid;
                            s.openParam = i;
                            s.openTime = cc.sys.now();
                            s.show_main_effect = n;
                            s.isSort = sort;
                            l._uiMap[t] = s;
                            l.findTopLayer();
                            l._midLayer && !e
                                ? l._midLayer.addChild(s)
                                : e && l._topLayer && l._topLayer.addChild(s);
                            exports.utils.showNodeEffect(s);
                            if (s.name == "StoryView") {
                                if (l._midLayer && !e)
                                    l.refreshAllUIByAdd();
                                l._isLoadingNum = l._isLoadingNum - 1;
                                return;
                            }
                            // s.opacity = 0;
                            // if (l.openViewAniDt == 0){
                            //     l.openViewAniDt = l.getParamInt("Uicomeout_time") + 0
                            // }
                            // let dt =  l.openViewAniDt / 1000;
                            // s.runAction(cc.sequence(cc.fadeIn(dt),cc.callFunc(()=>{
                            //     if (l._midLayer && !e)
                            //         l.refreshAllUIByAdd();
                            // })));
                            l.refreshAllUIByAdd();
                            l._isLoadingNum = l._isLoadingNum - 1;
                            initializer.guideProxy.guideUI
                             && initializer.guideProxy.guideUI.checkGuide(t.replace(config.Config.skin + "/prefabs/ui/", ""), true);
                        }
                    } else {
                        l._isLoadingNum = l._isLoadingNum - 1;
                    } cc.warn(r + " name load error!!!");
                });
            }
        };

        /**
        *刷新所有的UI 添加一个界面时
        *param name 新加的界面的名称
        *param _url 预制体的全路径
        */
        this.refreshAllUIByAdd = function(){
            let midLayer = cc.find("Canvas/midLayer");
            let isShowFullUI = false;
            if (midLayer && midLayer.childrenCount > 0){
                for (var ii = midLayer.childrenCount - 1; ii >= 0;ii--){
                    let child = midLayer.children[ii];
                    if (isShowFullUI){
                        child.active = false;
                        continue;
                    }
                    let nameArr = child.name.split(",");

                    let cg = localcache.getFilter(localdb.table_fullscreenname,"name",nameArr[nameArr.length-1]);
                    child.active = true;
                    if (cg != null){
                        isShowFullUI = true;
                    }
                }
            }
            let rootNode = cc.find("Canvas/homeview");
            if (rootNode){
                rootNode.active = !isShowFullUI;
            } 
        };

        /**
        *刷新所有的UI 删除一个界面时
        */
        this.refreshAllUIByClose = function(){
            let midLayer = cc.find("Canvas/midLayer");
            let isShowFullUI = false;
            if (midLayer && midLayer.childrenCount > 0){
                for (var ii = midLayer.childrenCount - 1; ii >= 0;ii--){
                    let child = midLayer.children[ii];
                    if (isShowFullUI){
                        child.active = false;
                        continue;
                    }
                    let nameArr = child.name.split(",");
                    let cg = localcache.getFilter(localdb.table_fullscreenname,"name",nameArr[nameArr.length-1]);
                    child.active = true;
                    if (cg != null){
                        isShowFullUI = true;
                    }
                }
            }
            let rootNode = cc.find("Canvas/homeview");
            if(this.getMiddleViewCount() == 0 && rootNode) {
                rootNode.getComponent("SoundItem").enabled = true;
            }
            if (!isShowFullUI && rootNode){
                if (!rootNode.active){
                    let mainscene = cc.find("scene/MainScene",rootNode);
                    if(mainscene) {
                        let scMainScene = mainscene.getComponent("MainScene");
                        if (scMainScene) {
                            scMainScene.onRevertScrollview();
                        }                     
                    }
                }
                rootNode.active = true;
            }       
        };

        this.findTopLayer = function() {
            if (null == this._midLayer) {
                var t = cc.director.getScene().getChildByName("Canvas");
                if (t) {
                    this._midLayer = t.getChildByName("midLayer");
                    this._topLayer = t.getChildByName("topLayer");
                }
            }
        };

        // 该界面是否存在
        this.findMiddleLayerName = function(name) {
            if(this._midLayer == null) return false;

            var count = this._midLayer.childrenCount;
            if(count > 0) {
                for(var i=0; i<count; i++) {
                    if(this._midLayer.children[i].name == name)
                        return true;
                }
            }

            return false;
        };

        //查找midlayer最上面的界面
        this.findMidLayerLastView = function() {
            return this.findLayerLastView(0);
        };

        this.findTopLayerLastView = function() {
            return this.findLayerLastView(1);
        };
        
        // 0: midLayer 1: topLayer
        this.findLayerLastView = function(type) {
            let layer = null;
            if(type == 0) {
                layer = this._midLayer;
            } else if(type == 1) {
                layer = this._topLayer;
            } else {
                return null;
            }
            if(layer == null) return null;

            let count = layer.childrenCount;
            if(count > 0) {
                let children = layer.children;
                
                let kvp = null;
                for(var i = 0; i < count; i++) {
                    let child = children[i], index = child.getSiblingIndex();
                    if(null == kvp) {
                        kvp = { name: child.name, index: index };
                        continue;
                    } else if(index > kvp.index) {
                        kvp = { name: child.name, index: index };
                    }
                }
                if(null != kvp) {
                    return kvp.name;
                }
                return null;
            }
            return null;
        };

        this.getMiddleViewCount = function() {
            let loadNum = this._isLoadingNum ? this._isLoadingNum : 0;
            return null != this._midLayer ? (this._midLayer.childrenCount + loadNum) : (0 + loadNum);
        };

        this.closeTopView = function() {
            if (!initializer.guideProxy.guideUI || initializer.guideProxy.guideUI.isHideShow()) {
                var t = null;
                null != this._topLayer &&
                    this._topLayer.childrenCount > 0 &&
                    (t = this._topLayer.children[
                        this._topLayer.childrenCount - 1
                    ]);
                "WaitHttp" == t.name && (t = null);
                null == t &&
                    null != this._midLayer &&
                    this._midLayer.childrenCount > 0 &&
                    (t = this._midLayer.children[
                        this._midLayer.childrenCount - 1
                    ]);
                if (null != t)
                    for (
                        var e = t.getComponents(cc.Component), o = 0;
                        o < e.length;
                        o++
                    ) {
                        cc.log(e[o].name);
                        if (0 == e[o].name.indexOf("StoryView")) break;
                        if (
                            "Widget" != e[o].name &&
                            "Animation" != e[o].name &&
                            "Button" != e[o].name
                        ) {
                            
                            this.closeView(e[o]);
                            break;
                        }
                    }
            }
        };

        this.closeView = function(t, e) {
            void 0 === e && (e = !1);
            if (t && t.node && t.node.openTime && cc.sys.now() - t.node.openTime < this.openViewAniDt)
                return !1;
            if (t && t.node && null == t.is_Show_Hide_Effect) {
                t.is_Show_Hide_Effect = !0;
                delete this._uiMap[t.node.__url];
                var n = this.showEffect(t, 1);
                let self = this;
                t.enabled = !1;
                e = e || t.node.show_main_effect;
                this._closingNum += 1;
                var l = function() {
                    if (t.node) {
                        config.Config.DEBUG &&
                            cc.log(t.node.__url + " prefab destory !!!");
                        -1 != t.node.__url.indexOf("StoryView") &&
                            facade.send("STORY_VIEW_DESOTRY");
                        let _uuid = t.node._uuid;
                        t.node.destroy();
                        t.node.removeFromParent(!0);
                        
                        MemoryMgr.releaseAssetPrefab({_uuid:_uuid});
                        self.refreshAllUIByClose();
                        if (t.node.__url.indexOf("ServantShow") || t.node.__url.indexOf("WifeShow")) {
                            facade.send("SHOW_VIEW_DESTROY");
                        }
                        self._closingNum -= 1;
                        initializer.guideProxy.guideUI
                         && initializer.guideProxy.guideUI.checkGuide(t.node.__url.replace(config.Config.skin + "/prefabs/ui/", ""));
                    } else {
                        self._closingNum -= 1;
                    }
                    (e && facade.send("SHOW_OPEN_EFFECT")) || facade.send("CHECK_IN_MAIN_VIEW");
                    facade.send("COMMON_CLOSE_VIEW");
                };
                "MainScene" == cc.director.getScene().name
                    ? facade.send("TIME_RUN_FUN", {
                          fun: l,
                          time: n
                      })
                    : l();
                return !0;
            }
            return !1;
        };

        this.closeNameView = function(t, e) {
            void 0 === e && (e = !0);
            t = config.Config.skin + "/prefabs/ui/" + t;
            var n = this._uiMap[t];
            if (!(n && n.openTime && cc.sys.now() - n.openTime < this.openViewAniDt) && n) {
                delete this._uiMap[t];
                config.Config.DEBUG && cc.log(t + " prefab destory !!!");
                var l = n.getComponent(cc.Component);
                if (null == l || l.is_Show_Hide_Effect) return;
                l.enabled = !1;
                l.is_Show_Hide_Effect = !0;
                let self = this;
                this._closingNum += 1;
                var r = this.showEffect(l, 1),
                    a = function() {
                        config.Config.DEBUG &&
                            cc.log(n.__url + " prefab destory !!!");
                        let _uuid = n._uuid;
                        n.destroy();
                        n.removeFromParent(!0);
                        MemoryMgr.releaseAssetPrefab({_uuid:_uuid});
                        self.refreshAllUIByClose();
                        (e && facade.send("SHOW_OPEN_EFFECT")) || facade.send("CHECK_IN_MAIN_VIEW");
                        facade.send("COMMON_CLOSE_VIEW");
                        self._closingNum -= 1;
                        initializer.guideProxy.guideUI
                         && initializer.guideProxy.guideUI.checkGuide(t.replace(config.Config.skin + "/prefabs/ui/", ""));
                    };
                "MainScene" == cc.director.getScene().name
                    ? facade.send("TIME_RUN_FUN", {
                          fun: a,
                          time: r
                      })
                    : a();
            }
        };

        this.closeCommonViewsByPath = function(name){
            let midLayer = cc.find("Canvas/midLayer");
            let isHas = false;
            if (midLayer && midLayer.childrenCount > 0){
                let idx = 0;
                do{
                    let child = midLayer.children[idx];
                    if (child == null) break;
                    let childname = child.name.replace(",","/")
                    childname = config.Config.skin + "/prefabs/ui/" +childname;
                    let nameArr = child.name.split(",");
                    if (nameArr[0] == "union"){
                        var n = this._uiMap[childname];
                        if (n){
                            delete this._uiMap[childname];
                            var l = n.getComponent(cc.Component);
                            l.enabled = !1;
                            l.is_Show_Hide_Effect = !0;
                            let _uuid = n._uuid;
                            n.destroy();
                            n.removeFromParent(!0);
                            MemoryMgr.releaseAssetPrefab({_uuid:_uuid});
                            this._closingNum -= 1;
                            isHas = true;
                        }                       
                    }
                    else{
                        idx++;
                    }
                }while(true)
            }
            if (isHas){
                this.refreshAllUIByClose();
            }
        }

        this.releaseCollect = function() {
            if (this.isCollect) {
                // cc.sys.isBrowser || cc.textureCache.removeUnusedTextures();
                this.isCollect = !1;
                this.releaseObjs = {};
                cc.sys.garbageCollect();
            }
        };


        this.isOpenView = function(t) {
            t = config.Config.skin + "/prefabs/ui/" + t;
            return null != this._uiMap[t];
        };
        this.showNodeEffect = function(t, e) {
            void 0 === e && (e = 0);
            if (null != t) {
                var o = t.getComponent(cc.Animation);
                if (o) {
                    var i = o.getClips();
                    -1 == e && (e = Math.floor(Math.random() * i.length));
                    -1 != e &&
                        i.length > 2 &&
                        i.length % 2 == 0 &&
                        (e += 2 * Math.floor((Math.random() * i.length) / 2));
                    var n = i[e];
                    n && o.play(n.name);
                }
            }
        };

        this.showNodeEffect2 = function(t, e,callback) {
            void 0 === e && (e = 0);
            if (null != t) {
                var o = t.getComponent(cc.Animation);
                if (o) {
                    var i = o.getClips();
                    -1 == e && (e = Math.floor(Math.random() * i.length));
                    var n = i[e];                 
                    if (n){
                        o.play(n.name);
                        var dur = n.duration;
                        callback && (dur > 0.05 ? o.scheduleOnce(callback, dur - 0.05) : callback.apply(t));
                    }                   
                }
            }
        };
        this.showEffect = function(t, e, o) {
            if (null != t) {
                var i = t.node.getComponent(cc.Animation),
                    n = 0;
                if (i) {
                    var l = i.getClips()[e];
                    if (l) {
                        i.play(l.name);
                        n = l.duration;
                    }
                }

                o && (n > 0.05 ? t.scheduleOnce(o, n - 0.05) : o.apply(t));
                return n;
            }
        };
        this.stopEffect = function(t, e) {
            if (null != t) {
                var i = t.node.getComponent(cc.Animation);
                if (i) {
                    var l = i.getClips()[e];
                    if (l) {
                        i.stop(l.name);
                    }
                }
            }
        };
        this.getParamStr = function(t) {
            var e = localcache.getItem(localdb.table_param, t);
            return e ? e.param + "" : "";
        };
        this.getParamInt = function(t) {
            var e = localcache.getItem(localdb.table_param, t);
            return e ? parseInt(e.param) : 0;
        };
        this.getParamStrs = function(t, e, i) {
            void 0 === e && (e = "|");
            void 0 === i && (i = ",");
            for (
                var n = this.getParamStr(t).split(e), l = [], r = 0;
                r < n.length;
                r++
            )
                if (exports.stringUtil.isBlank(i)) l.push(n[r]);
                else {
                    for (
                        var a = n[r].split(i), s = [], c = 0;
                        c < a.length;
                        c++
                    )
                        s.push(a[c]);
                    l.push(s);
                }
            return l;
        };
        this.showSingeConfirm = function(t, e, o, i, n, closeFunc) {
            void 0 === o && (o = null);
            void 0 === i && (i = null);
            void 0 === n && (n = null);
            var l = new a();
            l.txt = t;
            l.target = o;
            l.handler = e;
            l.color = i;
            l.left = n;
            l.close = closeFunc;
            this.openPrefabView("ConfirmRetry", !0, l);
        };
        this.showConfirm = function(t, e, o, i, n, l, m, closeFunc) {
            void 0 === o && (o = null);
            void 0 === i && (i = null);
            void 0 === n && (n = null);
            void 0 === l && (l = null);
            var r = new a();
            r.txt = t;
            r.target = o;
            r.handler = e;
            r.color = i;
            r.left = n;
            r.right = l;
            r.cancel = m;
            r.close = closeFunc;
            this.openPrefabView("ConfirmView", !0, r);
        };
        this.showConfirmItem = function(t, e, o, i, l, r, s, c, _,lost) {
            void 0 === l && (l = "");
            void 0 === r && (r = null);
            void 0 === s && (s = null);
            void 0 === c && (c = null);
            void 0 === _ && (_ = null);
            var d = new a();
            d.txt = t;
            d.itemId = e;
            d.count = o;
            d.target = r;
            d.handler = i;
            d.color = s;
            d.left = c;
            d.right = _;
            d.skip = l;
            if(lost) d.lost = lost;
            confirmView.isSkip(d) || this.openPrefabView("ConfirmItem", !1, d);
        };

        this.showConfirmItemMore = function(t, e, o, i, n, l, r, s, c) {
            void 0 === n && (n = null);
            void 0 === l && (l = null);
            void 0 === r && (r = null);
            void 0 === s && (s = null);
            void 0 === c && (c = 0);
            var _ = new a();
            _.txt = t;
            _.itemId = e;
            _.count = o < 1 ? 1 : o;
            _.target = n;
            _.handler = i;
            _.color = l;
            _.left = r;
            _.right = s;
            _.baseCount = c;
            this.openPrefabView("ConfirmItemMore", !1, _);
        };
        this.showConfirmInput = function(t, e, o, i, n, l) {
            void 0 === o && (o = null);
            void 0 === i && (i = null);
            void 0 === n && (n = null);
            void 0 === l && (l = null);
            var r = new a();
            r.txt = t;
            r.target = o;
            r.handler = e;
            r.color = i;
            r.left = n;
            r.right = l;
            this.openPrefabView("ConfirmInput", !1, r);
        };
        this.getHanzi = function(t) {
            var e = i18n.t("COMMON_HANZI").split("|");
            if (t > 10) var o = t % 10;
            else o = t;
            if (t > 19) {
                return (
                    e[Math.floor(t / 10) - 1] +
                    e[9] +
                    (t % 10 == 0 ? "" : e[(t % 10) - 1])
                );
            }
            return (t > 10 ? e[9] : "") + e[o - 1];
        };
        this.setClostBtn = function(t, e) {
            if (null != t && null != e) {
                var o = t.node.getComponent(cc.Animation);
                if (o) {
                    var i = o.getClips()[0].duration;
                    if (0 != i) {
                        e.interactable = !1;
                        t.scheduleOnce(function() {
                            e.interactable = !0;
                        }, i);
                    }
                }
            }
        };
        this.setWaitUI = function() {
            if (this.waitUI) {
                this.waitUI.removeFromParent(!0);
                this.waitUI.destroy();
                this.waitUI = null;
            }
            var t = config.Config.skin + "/prefabs/ui/WaitHttp",
                e = this;
            this.findTopLayer();
            cc.resources.load(t, function(t, o) {
                if (null != o) {
                    MemoryMgr.saveAssets(o);
                    var i = cc.instantiate(o);
                    e.waitUI = i;
                    if (null != e._topLayer) {
                        i.active = !1;
                        e._topLayer.addChild(i);
                    } else {
                        var n = cc.director.getScene().getChildByName("Canvas");
                        i.active = !1;
                        n.addChild(i);
                    }
                    JsonHttp.setWaitUI(i.getComponent(cc.Component));
                } else cc.warn(t + " name load error!!!");
            });
        };
        this.popView = function(t, e) {
            exports.stringUtil.isBlank(t) ||
                this.poplist.push({
                    url: t,
                    openParam: e
                });
        };
        this.popNext = function(t) {
            if (!this.isPop || !t) {
                this.isPop = !0;
                if (0 != this.poplist.length) {
                    for (
                        var e = 0, o = [], i = [], n = 0;
                        n < this.poplist.length;
                        n++
                    ) {
                        if ("AlertItemShow" == (l = this.poplist[n]).url) {
                            e++;
                            o.push(l.openParam);
                            i.push(n);
                        }
                    }
                    if (e > 1) {
                        for (n = i.length - 1; n >= 0; n--)
                            this.poplist.splice(i[n], 1);
                        this.openPrefabView("AlertItemMore", !1, o);
                    } else {
                        var l = this.poplist.shift();
                        this.openPrefabView(l.url, !1, l.openParam);
                    }
                } else this.isPop = !1;
            }
        };
        this.getSizeStr = function(t) {
            null == t && (t = 0);
            return t > 1048576
                ? (t / 1024 / 1024).toFixed(2) + "M"
                : (t / 1024).toFixed(1) + "KB";
        };
        this.getWorldPos = function(t, e, fixedPos) {
            void 0 === e && (e = null);
            for (var o = null == fixedPos ? cc.v2(t.x, t.y) : fixedPos; (t = t.parent) != e && null != t; ) {
                o.x += t.x;
                o.y += t.y;
            }
            return o;
        };
        this.fixAnchorPos = function(node) {
            if(null == node) {
                return null;
            }
            let fixAnchor = (anchor) => {
                return anchor < 0 ? 0 : (anchor > 1 ? 1 : anchor);
            };
            let anchorX = fixAnchor(node.anchorX);
            let anchorY = fixAnchor(node.anchorY);
            return cc.v2((node.x + ((0.5 - anchorX) * node.width)) * (node.parent ? node.parent.scaleX : 1), (node.y + ((0.5 - anchorY) * node.height)) * (node.parent ? node.parent.scaleY : 1));
        };
        this.randomArray = function(t) {
            t.sort(function(t, e) {
                return 10 * Math.random() < 5 ? 1 : -1;
            });
        };
        this.setCanvas = function() {
            var t = cc.winSize;
            if (t.width / t.height > 0.5625) {
                var e = cc.director
                    .getScene()
                    .getChildByName("Canvas")
                    .getComponent(cc.Canvas);
                e && (e.fitHeight = !0);
            }
        };

        this.doRepair = function(e){
            var t = (jsb.fileUtils ? jsb.fileUtils.getWritablePath() : "/") + "update-assets/";
            var path1 = t;
            var path2 = (jsb.fileUtils ? jsb.fileUtils.getWritablePath() : "/");

            if (cc.sys.os === cc.sys.OS_IOS) {
                jsb.fileUtils.isFileExist(path2+"main.js") && jsb.fileUtils.removeFile(path2+"main.js");
                jsb.fileUtils.isDirectoryExist(path2+"jsb-adapter") && jsb.fileUtils.removeDirectory(path2+"jsb-adapter");
                jsb.fileUtils.createDirectory(path2+"jsb-adapter");
                jsb.fileUtils.renameFile(path1+"main.js",path2+"main.js");
                jsb.fileUtils.renameFile(path1+"jsb-adapter/jsb-builtin.js",path2+"jsb-adapter/jsb-builtin.js");
                jsb.fileUtils.renameFile(path1+"jsb-adapter/jsb-engine.js",path2+"jsb-adapter/jsb-engine.js");
                // jsb.fileUtils.renameFile(path1)
            }
            if (jsb.fileUtils.isDirectoryExist(t)) {
                jsb.fileUtils.removeDirectory(t);
                if (e) {
                    exports.langManager.clearLang(e);
                }
            }
            if (cc.sys.os === cc.sys.OS_IOS) {
                if (!jsb.fileUtils.isDirectoryExist(t)) {
                    jsb.fileUtils.createDirectory(t + "jsb-adapter");
                }
                jsb.fileUtils.renameFile(path2+"main.js",path1+"main.js");
                jsb.fileUtils.renameFile(path2+"jsb-adapter/jsb-builtin.js",path1+"jsb-adapter/jsb-builtin.js");
                jsb.fileUtils.renameFile(path2+"jsb-adapter/jsb-engine.js",path1+"jsb-adapter/jsb-engine.js");
            }
            cc.game.restart();
        };

        this.randomNum = function(Min, Max) {
            return Min + Math.round(Math.random() * (Max - Min));
        };

        /**获取向量的模*/
        this.getVectorDistance = function(v1){
            let x = v1.x;
            let y = v1.y;
            return Math.sqrt(x*x + y*y);
        };

        /**获取两个向量的点积*/
        this.getVectorDot = function(v1,v2){
            return v1.x * v2.x + v1.y * v2.y;
        };

        /**计算向量的夹角*/
        this.getVectorRadius = function(v1,v2){
            let d1 = this.getVectorDistance(v1);
            let d2 = this.getVectorDistance(v2);
            let dot = this.getVectorDot(v1,v2);
            let radian = Math.acos(dot/(d1*d2));
            let radius = 180 * radian /Math.PI;
            if (v2.x- v1.x < 0){
                radius *= (-1);
            }
            return radius; 
        };

}

exports.Utils = Utils;
exports.utils = new Utils();

var a = function() {
    this.txt = "";
    this.handler = null;
    this.target = null;
    this.itemId = 0;
    this.count = 0;
    this.color = null;
    this.skip = "";
    this.left = "";
    this.right = "";
    this.baseCount = 0;
}
exports.ConfirmData = a;

function Alert() {
    this.isPlaying = false;
    this.alertPrefabmap = {};
    this.alertList = new Array();

    this.alert = function(t, e, o) {
        this.alertBy("AlertUI", {
            text: t,
            textOpt: e,
            textColor: o
        });
    };
    this.alert18n = function(t, e) {
        this.alert(t, {}, e);
    };
    this.alertItemLimit = function(t, e) {
        void 0 === e && (e = 0);
        var o = localcache.getItem(localdb.table_item, t);
        o &&
            this.alert("COMMON_LIMIT", {
                n: o.name
            });
        facade.send("ITEM_LIMIT_GO", {
            id: t,
            count: e
        });
    };
    this.alertIcon = function(t, e, o) {
        this.alertBy("AlertIcon", {
            text: t,
            url: e,
            textColor: o
        });
    };
    this.alertBy = function(t, e) {
        if (null != this.alertPrefabmap[t]) this.alertShow(t, e);
        else {
            var o = config.Config.skin + "/prefabs/ui/" + t,
                n = this;
            cc.resources.load(o, function(err, i) {
                if (null != i) {
                    MemoryMgr.saveAssets(o);
                    n.alertPrefabmap[t] = i;
                    n.alertShow(t, e);
                } else cc.warn(err + " name load error!!!");
            });
        }
    };
    this.alertShow = function(t, e) {
        var o = cc.instantiate(this.alertPrefabmap[t]);
        if (o) {
            o.y = 100;
            var i = o.getComponent(t);
            for (var n in e) i ? (i[n] = e[n]) : cc.log(t + " is not find");
            this.alertAddToQueue(o, i);
        } else cc.warn("alert show " + t + " is error!!!");
    };
    this.alertAddToQueue = function(t, e) {
        var o = this;
        e.endCall = function() {
            o.alertList.splice(o.alertList.indexOf(e), 1);
        };
        cc.director
            .getScene()
            .getChildByName("Canvas")
            .addChild(t);
        this.alertList.push(e);
        for (
            var i = t.height + 60, n = 0, l = this.alertList.length - 1;
            n < l;
            n++
        )
            this.alertList[n] &&
                this.alertList[n].node &&
                (this.alertList[n].node.y += i);
    };
}

exports.Alert = Alert;
exports.alertUtil = new Alert();

function StringUtil() {

    this.trim = function(t) {
        return t.replace(/(^\s*)|(\s*$)/g, "");
    };
    this.isBlank = function(t) {
        return (
            null == t ||
            "" == t ||
            " " == t ||
            "0" == t ||
            "null" == t ||
            "undefined" == t
        );
    };
    this.hasLimit = function(t) {
        for (
            var e = ["|", "#", "<", ">", "%", "*", "/", "\\", "="],
                o = 0,
                i = e.length;
            o < i;
            o++
        )
            if (t.indexOf(e[o]) >= 0) return !0;
        return !1;
    };
    this.hasBlank = function(t) {
        for (
            var e = ["\n", "\r", "\t", "\f", " ", "　"], o = 0, i = e.length;
            o < i;
            o++
        )
            if (t.indexOf(e[o]) >= 0) return !0;
        return !1;
    };
    this.hasEmoji = function(t) {
        return t.indexOf("\ud83c") >= 0 || t.indexOf("\ud83d") >= 0;
    };
}

exports.StringUtil = StringUtil;
exports.stringUtil = new StringUtil();

function TimeUtil() {
    this._timezoneServer = 8;
    this._timezoneClient = 8;
    this._timezoneOffset = 0;
    this._timeServer = 0;
    this._timeClient = 0;
    this._timeOfMonday = 0;
    this._countArray = [];

    this.init = function(t, e) {
        this.setServerTime(e);
        this._timezoneServer = t;
        this._timezoneClient = -new Date().getTimezoneOffset() / 60;
        this._timezoneOffset =
            36e5 * (this._timezoneClient - this._timezoneServer);
        this._timeOfMonday = this.timeAtHMS(Date.UTC(2015, 11, 28) / 1e3);
    };
    this.setServerTime = function(t) {
        this._timeServer = t;
        this._timeClient = Math.floor(cc.sys.now() / 1e3);
    };
    Object.defineProperty(TimeUtil.prototype, "second", {
        get: function() {
            return (
                this._timeServer +
                Math.floor(cc.sys.now() / 1e3) -
                this._timeClient
            );
        },
        enumerable: !0,
        configurable: !0
    });
    this.getCurSceond = function() {
        return (
            this._timeServer + Math.floor(cc.sys.now() / 1e3) - this._timeClient
        );
    };
    this.getTodaySecond = function(t, e, o) {
        void 0 === t && (t = 0);
        void 0 === e && (e = 0);
        void 0 === o && (o = 0);
        null == t && (t = 0);
        null == e && (e = 0);
        null == o && (o = 0);
        return this.timeAtHMS(this.second, t, e, o);
    };

    // 七日盛典活动开放倒计时
    this.getDayAndHour = function(t) {
        var diff = t - this._timeServer;
        if(diff < 0)    return 0;
        else {
            var day = Math.floor(diff / 86400);
            var hour = Math.floor((diff % 86400) / 3600);
            return {day:day, hour:hour};
        }
    }

    this.timeAtHMS = function(t, e, o, i) {
        e = e || 0;
        o = o || 0;
        i = i || 0;
        var n = t % 86400,
            l = t - n,
            r = Math.floor(n / 3600);
        r + this._timezoneServer < 0
            ? (l -= 86400)
            : r + this._timezoneServer >= 24 && (l += 86400);
        return l + 3600 * (e - this._timezoneServer) + 60 * o + i;
    };
    this.isSameWeek = function(t, e) {
        return (
            !(t - e >= 604800) &&
            (t - this._timeOfMonday) / 604800 ==
                (e - this._timeOfMonday) / 604800
        );
    };
    this.hms2second = function(t) {
        var e = t.split(":"),
            o = e.length,
            i = 0;
        o > 0 && (i += 3600 * parseInt(e[0]));
        o > 1 && (i += 60 * parseInt(e[1]));
        o > 2 && (i += parseInt(e[2]));
        return i;
    };
    this.second2hms = function(t, e) {
        if (t > 86400 && null == e) {
            var o = t % 86400;
            o = Math.floor(o / 3600);
            return (
                i18n.t("COMMON_DAY", {
                    d: Math.floor(t / 86400)
                }) +
                (o > 0
                    ? i18n.t("COMMON_HOUR", {
                          d: o
                      })
                    : "")
            );
        }
        var i = Math.floor(t / 3600),
            n = Math.floor((t - 3600 * i) / 60),
            l = t % 60,
            r = e || "HH:mm:ss";
        "HH:mm" == r && t < 60 && (r = "ss");
        return (
            (r = (r = (r = r.replace("HH", this.fix2(i))).replace(
                "mm",
                this.fix2(n)
            )).replace("ss", this.fix2(l))) + ("ss" == r ? "s" : "")
        );
    };
    this.str2Second = function(t) {
        var e = t.split(" "),
            o = e[0].split("-"),
            i = e[1].split(":");
        return (
            (new Date(
                Math.floor(parseInt(o[0])),
                Math.floor(parseInt(o[1])) - 1,
                Math.floor(parseInt(o[2])),
                Math.floor(parseInt(i[0])),
                Math.floor(parseInt(i[1])),
                Math.floor(parseInt(i[2]))
            ).getTime() +
                this._timezoneOffset) /
            1e3
        );
    };
    this.format = function(t, e) {
        var o = new Date();
        o.setTime(1e3 * t - this._timezoneOffset);
        var i = e || "yyyy-MM-dd HH:mm:ss";
        return (i = (i = (i = (i = (i = (i = i.replace(
            "yyyy",
            o.getFullYear() + ""
        )).replace("MM", this.fix2(o.getMonth() + 1))).replace(
            "dd",
            this.fix2(o.getDate())
        )).replace("HH", this.fix2(o.getHours()))).replace(
            "mm",
            this.fix2(o.getMinutes())
        )).replace("ss", this.fix2(o.getSeconds())));
    };
    this.fix2 = function(t) {
        return t < 10 ? "0" + t : "" + t;
    };
    this.getCurMonth = function() {
        var t = new Date();
        t.setTime(1e3 * this.second - this._timezoneOffset);
        var e = this.fix2(t.getMonth() + 1);
        return parseInt(e);
    };
    this.getCurData = function() {
        var t = (this.second - this._timeOfMonday) % 604800;
        return Math.floor(t / 86400) + 1;
    };
    this.getDateDiff = function(t) {
        var e = this.second - t;
        return e < 0 || e < 60
            ? i18n.t("TIME_MOMENT_AGO")
            : e < 3600
            ? i18n.t("TIME_SECOND_AGO", {
                  s: Math.floor(e / 60)
              })
            : e < 86400
            ? i18n.t("TIME_HOUR_AGO", {
                  s: Math.floor(e / 3600)
              })
            : e < 2592e3
            ? i18n.t("TIME_DAY_AGO", {
                  s: Math.floor(e / 86400)
              })
            : i18n.t("TIME_MONTH_AGO", {
                  s: Math.floor(e / 2592e3)
              });
    };

    this.countDown = function(time, script, callback) {
        void 0 === time && (time = !0);
        let self = this;
        if (null != script && null != callback && 0 != time) {
            script.unschedule(callback, script);
            script.schedule(count, 1);
        }
        function count() {
            var remain = time - self.second;
            if (remain <= 0) {
                callback && callback();
                script.unschedule(callback, script);
            }
        }
    };

    this.addCountEvent = function(check, time, tag, callback) {
        if(check && time > 0 && tag && callback) {
            let data = {
                time: time,
                cb: callback,
                tag: tag,
            }
            for (let ii = 0; ii < this._countArray.length;ii++){
                let info = this._countArray[ii];
                if (info.tag == tag){
                    return;
                }
            }
            this._countArray.push(data);
        }
    };

    this.toCountEvent = function() {
        if(this._countArray.length > 0) {
            return this._countArray.splice(0, 1)[0];
        }
        return null;
    };

    /**根据当前的时间戳取出到当日0点的时间差值*/
    this.getSecondToZeroByFixTime = function(time){
        let str = this.format(time);
        str = str.substring(11);
        let arr = str.split(":");
        let sum = 1;
        sum += (23 - Number(arr[0])) * 3600;
        sum += (59 - Number(arr[1])) * 60;
        sum += (59 - Number(arr[2]));
        return sum;
    }
}

exports.TimeUtil = TimeUtil;
exports.timeUtil = new TimeUtil();

function AudioManager() {
    //-----------------------------------------------------------
    this._bgm = -1;
    this._bgmCurrent = null; //当前BGM
    this._bgmBase = null; //基础BGM
    this._soundLoads = {};
    this._isSoundOff = false;
    this._isSayOff = false;
    this._isBlank = false;
    this._isRole = false;
    this._isNpc = false;
    this._bgmVolume = 1;
    this._lastSound = -1;

    this._getSoundPath = function(t) {
        if (-1 != t.indexOf("/") && -1 != t.indexOf("/res/")) return t;
        if (null != localcache.getItem(localdb.table_voiceDown, t))
            return this._getResPath("audio_down", t);
        var info = localcache.getItem(localdb.table_shengyin_effect, Number(t));
        if (null != info)
            return this._getResPath("audio_effect", info.res);
        /*
        if (null != localcache.getItem(localdb.table_voiceDown, t)) {
            if (cc.sys.isBrowser)
                return cc.url.raw(
                    "resources/" +
                        config.Config.skin +
                        "/res/audio_down/" +
                        t +
                        ".mp3"
                );
            var e =
                this.storagePath +
                "/res/raw-assets/resources/" +
                config.Config.skin +
                "/res/audio_down/" +
                t +
                ".mp3";
            return jsb.fileUtils.isFileExist(e)
                ? e
                : cc.url.raw(
                      "resources/" +
                          config.Config.skin +
                          "/res/audio_down/" +
                          t +
                          ".mp3"
                  );
        }
        */
        return cc.url.raw(
            "resources/" + config.Config.skin + "/res/audio/" + t + ".mp3"
        );
    };

    this._getResPath = function(folderName, t) {        
        if (cc.sys.isBrowser)
            return cc.url.raw(
                "resources/" +
                    config.Config.skin +
                    "/res/"+folderName+"/" +
                    t +
                    ".mp3"
            );
        var e =
            this.storagePath +
            "/res/raw-assets/resources/" +
            config.Config.skin +
            "/res/"+folderName+"/" +
            t +
            ".mp3";
        return jsb.fileUtils.isFileExist(e)
            ? e
            : cc.url.raw(
                    "resources/" +
                        config.Config.skin +
                        "/res/"+folderName+"/" +
                        t +
                        ".mp3"
                );
    };

    this.preloadSound = function(t) {
        if (
            !this._isSoundOff &&
            !exports.stringUtil.isBlank(t) &&
            !this._soundLoads[t]
        ) {
            this._soundLoads[t] = !0;
            cc.audioEngine.preload(this._getSoundPath(t));
        }
    };
    this.sound_json = null;
    this.storagePath = "";

    this.setBGMVolume = function(t) {
        this._bgmVolume = t;
    };
    this.setSoundOff = function(t) {
        this._isSoundOff = t;
        -1 != this._bgm &&
            cc.audioEngine.setVolume(
                this._bgm,
                this._isSoundOff ? 0 : this._bgmVolume
            );
    };
    this.playBGM = function(t, e) {
        void 0 === e && (e = !1);
        e && (this._bgmBase = t);
        if (this._bgmCurrent != t) {
            exports.stringUtil.isBlank(this._bgmCurrent) ||
                this._bgmCurrent == this._bgmBase ||
                cc.sys.isBrowser ||
                cc.audioEngine.uncache(this._getSoundPath(this._bgmCurrent));
            this._bgmCurrent = t;
            if (this._bgm >= 0) {
                cc.audioEngine.stop(this._bgm);
                this._bgm = -1;
            }
            if (!exports.stringUtil.isBlank(t)){
                let self = this;
                cc.AudioClip._loadByUrl(self._getSoundPath(t), function (err, clip) {
                    if (clip) {
                        if (self._bgm >= 0) {
                            cc.audioEngine.stop(self._bgm);
                            self._bgm = -1;
                        }
                        self._bgm = cc.audioEngine.play(
                            clip,
                            !0,
                            self._isSoundOff ? 0 : self._bgmVolume
                        )
                    }
                });
            }
            // exports.stringUtil.isBlank(t) ||
            //     (this._bgm = cc.audioEngine.play(
            //         this._getSoundPath(t),
            //         !0,
            //         this._isSoundOff ? 0 : this._bgmVolume
            //     ));
        }
    };
    this.stopBGM = function(t) {
        if (t) this.playBGM(this._bgmBase);
        else if (-1 != this._bgm) {
            cc.audioEngine.stop(this._bgm);
            this._bgm = 0;
            this._bgmCurrent = null;
            this._bgmBase = null;
        }
    };
    this.isPlayLastSound = function() {
        return -1 != this._lastSound;
    };

    this.playEffect = function(t, e, i, n) {
        var l = this;
        void 0 === e && (e = !1);
        void 0 === i && (i = !1);
        if (!exports.stringUtil.isBlank(t)) {
            if (e && -1 != this._lastSound) {
                cc.audioEngine.stop(this._lastSound);
                0 != this._bgm &&
                    cc.audioEngine.setVolume(
                        this._bgm,
                        this._isSoundOff ? 0 : this._bgmVolume
                    );
            }
            if (!this._isSayOff) {
                var r = this._getSoundPath(t);
                let self = this;
                cc.AudioClip._loadByUrl(r, function (err, clip) {
                    if (clip) {
                        self._lastSound = cc.audioEngine.play(
                            clip,
                            !1,
                            self._isSayOff ? 0 : self._bgmVolume
                        )
                        if (i && -1 != self._bgm) {
                            cc.audioEngine.setVolume(
                                self._bgm,
                                self._isSoundOff ? 0 : self._bgmVolume / 2
                            );
                            // cc.audioEngine.setFinishCallback(
                            //     self._lastSound,
                            //     function() {
                            //         -1 != self._bgm &&
                            //             cc.audioEngine.setVolume(
                            //                 self._bgm,
                            //                 self._isSoundOff ? 0 : self._bgmVolume / 2
                            //             );
                            //         null != n && n();
                            //         cc.sys.isBrowser || cc.audioEngine.uncache(r);
                            //         self._lastSound = -1;
                            //     }
                            // );
                        }
                        cc.audioEngine.setFinishCallback(
                            self._lastSound,
                            function() {
                                -1 != self._bgm &&
                                    cc.audioEngine.setVolume(
                                        self._bgm,
                                        self._isSoundOff ? 0 : self._bgmVolume / 2
                                    );
                                null != n && n();
                                cc.sys.isBrowser || cc.audioEngine.uncache(r);
                                self._lastSound = -1;
                            }
                        );
                    }
                });
                // this._lastSound = cc.audioEngine.play(
                //     r,
                //     !1,
                //     this._isSayOff ? 0 : this._bgmVolume
                // );
                // if (i && -1 != this._bgm) {
                //     cc.audioEngine.setVolume(
                //         this._bgm,
                //         this._isSoundOff ? 0 : this._bgmVolume / 2
                //     );
                //     var a = this;
                //     cc.audioEngine.setFinishCallback(
                //         this._lastSound,
                //         function() {
                //             -1 != a._bgm &&
                //                 cc.audioEngine.setVolume(
                //                     a._bgm,
                //                     l._isSoundOff ? 0 : l._bgmVolume / 2
                //                 );
                //             null != n && n();
                //             cc.sys.isBrowser || cc.audioEngine.uncache(r);
                //             a._lastSound = -1;
                //         }
                //     );
                // }
            }
        }
    };

    this.playSound = function(t, e, i, n) {
        // var l = this;
        // void 0 === e && (e = !1);
        // void 0 === i && (i = !1);
        // if (!exports.stringUtil.isBlank(t)) {
        //     if (e && -1 != this._lastSound) {
        //         cc.audioEngine.stop(this._lastSound);
        //         0 != this._bgm &&
        //             cc.audioEngine.setVolume(
        //                 this._bgm,
        //                 this._isSoundOff ? 0 : this._bgmVolume
        //             );
        //     }
        //     if (!this._isSayOff) {
        //         var r = this._getSoundPath(t);
        //         this._lastSound = cc.audioEngine.play(
        //             r,
        //             !1,
        //             this._isSayOff ? 0 : this._bgmVolume
        //         );
        //         if (i && -1 != this._bgm) {
        //             cc.audioEngine.setVolume(
        //                 this._bgm,
        //                 this._isSoundOff ? 0 : this._bgmVolume / 2
        //             );
        //             var a = this;
        //             cc.audioEngine.setFinishCallback(
        //                 this._lastSound,
        //                 function() {
        //                     -1 != a._bgm &&
        //                         cc.audioEngine.setVolume(
        //                             a._bgm,
        //                             l._isSoundOff ? 0 : l._bgmVolume / 2
        //                         );
        //                     null != n && n();
        //                     cc.sys.isBrowser || cc.audioEngine.uncache(r);
        //                     a._lastSound = -1;
        //                 }
        //             );
        //         }
        //     }
        // }
    };

    this.stopLastSound = function () {
        if(-1 != this._lastSound) {
            cc.audioEngine.stop(this._lastSound);
        }
    };
    this.playClickSound = function() {
        let self = this;
        this._isSoundOff ||
        cc.AudioClip._loadByUrl(this._getSoundPath(config.Config.clickBtnSound), function (err, clip) {
            if(clip) {
                cc.audioEngine.play(
                    clip,
                    !1,
                    self._isSayOff ? 0 : self._bgmVolume
                )
            }
        })       
    };
    this.getStoragePath = function() {
        "" == this.storagePath &&
            (this.storagePath =
                (jsb.fileUtils ? jsb.fileUtils.getWritablePath() : "/") +
                "update-assets");
        return this.storagePath;
    };
    this.isExitManifest = function() {
        return null != this.sound_json;
    };

    this.loadMainifest = function() {
        if (!cc.sys.isBrowser)
            if (null == this.sound_json) {
                console.log("开始下载语音")
                this.storagePath =
                    (jsb.fileUtils ? jsb.fileUtils.getWritablePath() : "/") +
                    "update-assets";
                var t =
                        this.storagePath +
                        "/res/raw-assets/resources/" +
                        config.Config.skin +
                        "/res/sound.json",
                    e = this;
                if (jsb.fileUtils.isFileExist(t))
                    cc.assetManager.loadRemote(t, function(t, i) {
                        if (null == t) {
                            e.sound_json = i;
                            console.log("e.sound_json is "+e.sound_json);
                            facade.send("LOAD_MANIFEST_OVER", null, !0);
                        } else exports.alertUtil.alert(t.toString());
                    });
                else {
                    t = config.Config.skin + "/res/sound";
                    cc.resources.load(t, function(t, i) {
                        if (null == t) {
                            e.sound_json = i;
                            console.log("e.sound_json is "+e.sound_json);
                            facade.send("LOAD_MANIFEST_OVER", null, !0);
                        } else exports.alertUtil.alert(t.toString());
                    });
                }
            } else facade.send("LOAD_MANIFEST_OVER");
    };
    this.isNeedDown = function() {
        if (cc.sys.isBrowser) return !1;
        if ("1" == cc.sys.localStorage.getItem("DOWN_SOUND")) return !0;
        if (
            jsb.fileUtils.isDirectoryExist(
                cc.url.raw("resources/" + config.Config.skin + "/res/audio_down/")
            )
        )
            return !1;
        this.storagePath =
            (jsb.fileUtils ? jsb.fileUtils.getWritablePath() : "/") +
            "update-assets";
        return !jsb.fileUtils.isDirectoryExist(
            this.storagePath +
                "/res/raw-assets/resources/" +
                config.Config.skin +
                "/res/audio_down/"
        );
    };
    this.isNeedDownType = function(t, e) {
        if (cc.sys.isBrowser) return !1;
        if (null == this.sound_json) {
            this.loadMainifest();
            return !1;
        }
        var o = this.getLoadItems(t, e);
        return o && o.length > 0;
    };
    
    this.getLoadItems = function(t, e) {
        var o = localcache.getList(localdb.table_voiceDown),
            n = [];
        if (cc.sys.isBrowser) return null;
        cc.log(t, e);
        var l = jsb.fileUtils.isDirectoryExist(
            cc.url.raw("resources/" + config.Config.skin + "/res/audio_down/")
        );
        if (0 == t)
            for (var r in this.sound_json.assets) {
                var a = this.storagePath + "/" + r,
                    s = this.sound_json.assets[r],
                    c = r.replace("res/raw-assets/", ""),
                    _ = cc.url.raw(c);
                jsb.fileUtils.isFileExist(a) ||
                    null == s ||
                    (l && jsb.fileUtils.isFileExist(_)) ||
                    n.push({
                        key: r,
                        item: s
                    });
            }
        else
            for (var d = 0; d < o.length; d++)
                if (o[d].type == t && e == o[d].para) {
                    (r =
                        "res/raw-assets/resources/" +
                        config.Config.skin +
                        "/res/audio_down/" +
                        o[d].id +
                        ".mp3"),
                        (a = this.storagePath + "/" + r),
                        (_ = cc.url.raw(
                            "resources/" +
                                config.Config.skin +
                                "/res/audio_down/" +
                                o[d].id +
                                ".mp3"
                        )),
                        (s = this.sound_json.assets[r]);
                    jsb.fileUtils.isFileExist(a) ||
                        null == s ||
                        (l && jsb.fileUtils.isFileExist(_)) ||
                        n.push({
                            key: r,
                            item: s
                        });
                }
        return n;
    };
}

exports.AudioManager = AudioManager;
exports.audioManager = new AudioManager();

function LangManager() {
    
    this.lang_json = {};
    this.lang_ojson = {};
    this.storagePath = "";
    this.loadHandler = null;

    this.getStoragePath = function() {
        "" == this.storagePath &&
            (this.storagePath =
                (jsb.fileUtils ? jsb.fileUtils.getWritablePath() : "/") +
                "update-assets");
        return this.storagePath;
    };
    
    this.loadMainifest = function(t, e) {
        void 0 === e && (e = null);
        if (!cc.sys.isBrowser) {
            this.loadHandler = e;
            if (null == this.lang_json[t] || null == this.lang_ojson[t]) {

                this.getStoragePath();
                console.log("this.storagePath is "+this.storagePath);
                var o =
                        this.storagePath +
                        "/res/raw-assets/resources/" +
                        config.Config.skin +
                        "/res/" +
                        t +
                        ".json",
                    n = this;
                cc.assetManager.loadRemote(o, function(e, o) {
                    if (null == e && null != o) {
                        n.lang_json[t] = o;
                        n.loadOver(t);
                    } else {
                        cc.log(e);
                        n.lang_json[t] = {};
                        n.loadOver(t);
                    }
                });

                o = config.Config.skin + "/res/" + t;
                cc.resources.load(o, function(e, o) {
                    if (null == e && null != o) {
                        n.lang_ojson[t] = o;
                        n.loadOver(t);
                    } else {
                        cc.log(e);
                        n.lang_ojson[t] = {};
                        n.loadOver(t);
                    }
                });
            } else {
                facade.send("LOAD_LANG_MANIFEST_OVER", t);
                null != this.loadHandler && this.loadHandler(t);
            }
        }
    };
    this.loadOver = function(t) {
        if (null != this.lang_json[t] && null != this.lang_ojson[t]) {
            facade.send("LOAD_LANG_MANIFEST_OVER", t);
            null != this.loadHandler && this.loadHandler(t);
        }
    };
    this.getLoadItems = function(t) {
        if (cc.sys.isBrowser) return null;
        var e = this.lang_json[t],
            o = this.lang_ojson[t];
        if (null == e && null == o) return [];
        var i = [],
            n = e.assets ? e.assets : o.assets;
        for (var l in n) {
            var r = e && e.assets ? e.assets[l] : null,
                a = o && o.assets ? o.assets[l] : null,
                s = this.storagePath + "/" + l,
                c = !1,
                _ = r || a;
            if (null != r && null == a)
                c =
                    cc.sys.localStorage.getItem(l) != r.md5 ||
                    !jsb.fileUtils.isFileExist(s);
            else if (null == r && null != a) {
                var d = l.replace("res/raw-assets/", ""),
                    u = cc.url.raw(d);
                c =
                    cc.sys.localStorage.getItem(l) != a.md5 ||
                    !jsb.fileUtils.isFileExist(u);
            } else if (null != r && null != a) {
                (d = l.replace("res/raw-assets/", "")), (u = cc.url.raw(d));
                c =
                    cc.sys.localStorage.getItem(l) != r.md5 ||
                    !jsb.fileUtils.isFileExist(u) ||
                    !jsb.fileUtils.isFileExist(s);
            }
            c &&
                i.push({
                    key: l,
                    item: _
                });
        }
        return i;
    };
    this.clearLang = function(t) {
        if (cc.sys.isBrowser) return null;
        var e = this.lang_json[t];
        if (null != e) for (var o in e) cc.sys.localStorage.setItem(o, "");
    };
}

exports.LangManager = LangManager;
exports.langManager = new LangManager();
