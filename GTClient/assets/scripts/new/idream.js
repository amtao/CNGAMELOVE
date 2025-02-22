var e = module,
    o = exports;
var CryptoJS = require("crypto-js");
var config = require("./../app/Config")
  
var i =
    "function" == typeof Symbol && "symbol" == typeof Symbol.iterator
        ? function(t) {
              return typeof t;
          }
        : function(t) {
              return t &&
                  "function" == typeof Symbol &&
                  t.constructor === Symbol &&
                  t !== Symbol.prototype
                  ? "symbol"
                  : typeof t;
          };
window.JsonHttp = new (function() {
    var t = !1,
        e = !1,
        o = null,
        n = {},
        l = {},
        r = null,
        a = /^\d+$/,
        s = !1,
        c = "",
        _ = null,
        d = "",
        failCount = 0,
        lastReqData = null;

    this.setDebug = function(e) {
        t = e;
    };
    this.setRSN = function(t) {
        e = t;
    };
    this.getUrl = function() {
        return o ? o() : "";
    };
    this.setSecondHandler = function(t) {
        _ = t;
    };
    this.setGetUrl = function(t) {
        o = t;
    };
    this.setWaitUI = function(t) {
        r = t;
    };
    this.subscribe = function(t, e, o) {
        var i = t.key.split("."),
            l = i[1],
            r = i[2],
            a = l + "_" + r;
        null != l && null != r
            ? (n[a] = {
                  mod: l,
                  type: r,
                  handler: e,
                  target: o
              })
            : cc.error("proto class is error!!!" + t.key);
    };
    this.sendWaitUIShow = function(t) {
        if (s == t) return;
        s = t;
        if (null != r && r.node && null != r.node.parent) {
            r.isShow = t;
            if (t) {

                r.node.getChildByName("img_jiazai").active = false;
                r.node.active = r.isShow;
                
                r.scheduleOnce(function() {
                    if (r && r.node)
                        r.node.getChildByName("img_jiazai").active = true;
                }, 0.5);
                r.scheduleOnce(function() {
                    facade && facade.send("SHOW_RETRY_SEND");
                    r.unscheduleAllCallbacks();
                    s = false;
                    r.node.active = false;
                    failCount = 0;
                }, 30);
            } else {
                r.unscheduleAllCallbacks();
                r.node.active = t;
                failCount = 0;
            }
        }
    };

    this.DelaySendLastUrl = function (callback, target) {
        if (null != r && r.node && null != r.node.parent && r.node.active){
            failCount++;
            if (failCount < 50){
                r.scheduleOnce(function() {
                    if (null != r && r.node && null != r.node.parent && r.node.active) {
                        JsonHttp.sendLast(callback, target);
                    }                   
                }, 0.4);
            }
        }   
    }
    this.httpRequest = function(url, reqData, callback, target) {
        let isNeedEncry = function(e)
        {
            if(typeof(e) == "undefined" )
            {
                return false
            }
            if(!e){
                return false
            }
           if(!e) return false
           if(e.indexOf("fastLogin")>0) return false
           if(e.indexOf("php?")<=0) return false
           return config.Config.isVerify;

        }

        let getDecryStr = function(encryptedBase64Str)
        {
            var encryptedBase64Str = encryptedBase64Str;
            var options = {
            mode: CryptoJS.mode.ECB,
            padding: CryptoJS.pad.Pkcs7
            };
            var key = CryptoJS.enc.Utf8.parse(config.Config.CRYPTOJSKEY);
            // 解密
            var decryptedData = CryptoJS.AES.decrypt(encryptedBase64Str, key, options);
            // 解密后，需要按照Utf8的方式将明文转位字符串
            var decryptedStr = decryptedData.toString(CryptoJS.enc.Utf8);
            return decryptedStr;
        }
        let getEncryStr = function(comonStr)
        {
            var comonStr = comonStr;
            var options = {
                mode: CryptoJS.mode.ECB,
                padding: CryptoJS.pad.Pkcs7
            };
            var key = CryptoJS.enc.Utf8.parse(config.Config.CRYPTOJSKEY);
            var encryptedData = CryptoJS.AES.encrypt(comonStr, key, options);
            var encryptedBase64Str = encryptedData.toString();
            return encryptedBase64Str;
        }
        let needEncry= isNeedEncry(url)
        //包头处理
        var start = url.indexOf("php?")+4;
        if(needEncry){
            var railStr = url.substr(start);
            let headStr = url.substr(0,start);
            let encodeRail = getEncryStr(railStr)
            url = headStr + "encstr=" + encodeRail + "&enc=1"
        }

        var isDebug = t;
        var l =!(arguments.length > 4 && void 0 !== arguments[4]) || arguments[4],
            r = (arguments.length > 5 && void 0 !== arguments[5] && arguments[5],this),
            a = null == reqData || -1 == reqData.indexOf("adok");
        if (a) {
            // if(null == reqData) {
            //     lastReqData = null;
            // } else {
            //     lastReqData = {};
            //     this.copyData(lastReqData, reqData);
            // }
            this.sendWaitUIShow(true);
        }
        var xhr  = cc.loader.getXMLHttpRequest();
        xhr.onloadend = function() {
            if (null == xhr || null == xhr.status){
                cc.warn(url + " request is error!!!"); 
                if (a){
                    JsonHttp.DelaySendLastUrl(callback, target)
                }
            }
            else if (200 == xhr.status) {
                if (a){
                    // if(r.isObjectValueEqual(lastReqData, reqData)) {
                    //     r.sendWaitUIShow(!1);
                    // }
                    r.sendWaitUIShow(!1);
                    failCount = 0;
                }               
                if (null == xhr.response || "" == xhr.response || " " == xhr.response)
                    cc.warn(url + " request is error!!!");
                else {
                    let response = xhr.response;
                    if (isNeedEncry(url)) {
                        response = getDecryStr(response)
                    }
                    if (null != response && 0 == response.indexOf("{")) {
                        let rspData = JSON.parse(response);
                        if (null != rspData && null != rspData.a && null != rspData.a.system && null != rspData.a.system.errror){
                            if (rspData.a.system.errror.type > 10000){
                                facade && facade.send("SERVER_SPECIAL_CALLBACK_ERROR",rspData.a.system.errror);
                                return;
                            }
                        }
                        if(config.Config.DEBUG) {
                            if(null != reqData) {
                                console.error(reqData);
                            }
                            console.warn(rspData);
                        }
                        l && r.dealSub(rspData);
                        null != callback && null != target
                            ? callback.apply(target, [rspData])
                            : null != callback && callback(rspData);
                    } else cc.warn(url + " request is error!!!" + response);
                }
            } else{
                cc.warn(url + " request is error!!!");
                if (xhr.status == 404){
                    if (a)
                        r.sendWaitUIShow(!1);
                }
                else{
                    if (a)
                        JsonHttp.DelaySendLastUrl(callback, target);
                }
            } 
        };
        xhr.open("POST", url);
        if (null != reqData && "" != reqData) {
            isDebug && -1 == reqData.indexOf("adok") && cc.log("open url:" + url + " ##send data##:" + reqData);
            if (needEncry) {
                reqData = getEncryStr(reqData)
            }
            xhr.send(reqData);
        } else xhr.send();
    };
    this.dealSub = function(t) {
        for (var e in t) {
            var o = t[e];
            if (o) {
                if (!this.isLinkMyUrl() && 10 * Math.random() < 5) return;
                this.dealItem(o, e);
            }
        }
    };
    this.dealItem = function(e, o) {
        for (var i in e)
            for (var l in e[i]) {
                var r = this.dealNumber(e[i][l]),
                    a = i + "_" + l,
                    s = n[a];
                if (null != s) {
                    t &&
                        cc.warn(
                            "[JosnHttp]MOD:" + i + " type:" + l + " accept"
                        );
                    if (t) s.handler.apply(s.target, [r]);
                    else {
                        try {
                            s.handler.apply(s.target, [r]);
                        } catch (t) {
                            cc.warn(t.toString());
                        }
                    }
                } else
                    t &&
                        cc.warn(
                            "[JosnHttp][NOT-EXISTS]MOD:" + i + " type:" + l
                        );
            }
    };
    this.dealNumber = function(t) {
        if (!this.isLinkMyUrl()) return t;
        for (var e in t) {
            var o = t[e],
                n = "undefined" == typeof o ? "undefined" : i(o);
            null != o &&
                "number" != n &&
                ("object" == n
                    ? (t[e] = this.dealNumber(o))
                    : "string" == n &&
                      "" != n &&
                      null != n &&
                      o.length < 11 &&
                      a.test(o) &&
                      e != "name" &&
                      e != "msg" &&
                      (t[e] = parseInt(o)));
        }
        return t;
    };
    this.record = function(t, e, o) {
        if ("a" == e) l[o] = t;
        else if ("u" == e) {
            var i = l[o];
            if (t instanceof Array)
                if (0 == t.length) i = t;
                else
                    for (var n = 0; n < t.length; n++) {
                        for (var r = !1, a = 0; a < i.length; a++)
                            if (i[a].id && t[n].id && i[a].id == t[n].id) {
                                i[a] = sList[n];
                                r = !0;
                            }
                        r || i.push(t[n]);
                    }
            else if (null != i && null != t) for (var e in t) i[e] = t[e];
            l[o] = i;
        }
        return l[o];
    };
    this.getServerUrl = function(t, e) {
        d = t;
        this.isLinkMyUrl() && this.httpRequest(t,e);
    };
    this.isLinkMyUrl = function() {
        var e = t && !cc.sys.isMobile;
        // if (!e) {
            // if (null == d && 10 * Math.random() < 5) return !1;
            // if (
            //     -1 == d.indexOf("kkk-game.com") &&
            //     -1 == d.indexOf("amoykkk.com") &&
            //     -1 == d.indexOf("xm-kkk.com") &&
            //     -1 == d.indexOf("kkkp4f.com") &&
            //     -1 == d.indexOf("id-g.com") &&
            //     -1 == d.indexOf("14817b.pathx.ucloudgda.com") &&
            //     -1 == d.indexOf("80d741.pathx.ucloudgda.com") &&
            //     -1 == d.indexOf("xianyuyouxi.com") &&
            //     d.indexOf("f90f87.pathx.ucloudgda.com")
            // )
            //     return !1;
        // }
        return !0;
    };
    //数据请求
    this.send = function(data, callback, target) {
        var n = arguments.length > 3 && void 0 !== arguments[3] && arguments[3],
            l = !(arguments.length > 4 && void 0 !== arguments[4]) || arguments[4];
        try {
            var reqData = null != data ? data.getJson() : "";
            if (s && c == reqData) return;
            null != r && r.node && null != r.node.parent &&
                n && (r.node.active = !0);
            var d = reqData.indexOf("adok");
            if (-1 == d) {
                c = reqData;
                if (e && l) {
                    var u = this.encryptTime(_());
                    "" != u && "" != reqData && (reqData = this.encryptJson(reqData, u));
                }
            }
            this.httpRequest(this.getUrl(), reqData, callback, target, !0, !0);
        } catch (data) {
            cc.log(data.toString());
        }
    };
    this.sendLast = function(callback=null, target=null) {
        try {
            var t = c;
            if (e) {
                var o = this.encryptTime(_());
                "" != o && "" != t && (t = this.encryptJson(t, o));
            }
            this.httpRequest(this.getUrl(), t,callback, target);
        } catch (t) {
            cc.log(t.toString());
        }
    };
    var u = [
        ["3", "1", "5", "8", "9", "7", "4", "2", "0", "6"],
        ["0", "5", "3", "2", "1", "7", "9", "4", "8", "6"],
        ["1", "0", "6", "7", "3", "8", "2", "5", "4", "9"],
        ["6", "1", "5", "4", "2", "9", "0", "3", "8", "7"],
        ["7", "6", "0", "2", "5", "8", "1", "4", "9", "3"],
        ["6", "5", "3", "4", "0", "2", "8", "1", "7", "9"],
        ["9", "6", "1", "4", "0", "5", "3", "2", "8", "7"],
        ["8", "9", "3", "1", "5", "7", "0", "6", "4", "2"],
        ["6", "2", "4", "9", "1", "5", "3", "8", "0", "7"]
    ];
    this.encryptTime = function(t) {
        for (
            var e = Math.floor(9 * Math.random()),
                t = t + "",
                o = "",
                i = t.length,
                n = 0;
            n < i;
            n++
        )
            for (var l = 0; l < u[e].length; l++)
                u[e][l] == t.substr(n, 1) && (o += l + "");
        return e + "" + o;
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
    this.encryptJson = function(t, e) {
        if (this.isBlank(t)) return "";
        for (var o = !1, i = 0; i < t.length; i++)
            if (t.charCodeAt(i) > 127) {
                o = !0;
                t = encodeURI(t);
                break;
            }
        var n = "",
            l = Math.ceil(t.length / 20);
        l = (l = Math.ceil(Math.random() * l + l / 2)) > 9 ? 9 : l;
        for (var r = 0, a = 0, i = 0; i < t.length; i++) {
            n += t[r * l + a];
            if (++r * l + a >= t.length) {
                a++;
                r = 0;
            }
        }
        return n + "#" + l + (o ? "1" : "0") + e;
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
})();

window.facade = new (function() {
    var isDebug = false,
        e = {},
        allObject = [],
        i = 0,
        n = !0;
    this.setDebug = function(e) {
        isDebug = e;
    };
    this.setSubscribeEnable = function(t) {
        n = t;
    };
    this.send = function(o, i) {
        var l = arguments.length > 2 && void 0 !== arguments[2] && arguments[2];
        if (n) {
            isDebug && cc.log("[FACADE]send:" + o);
            var r = null,
                a = 0,
                s = null;
            for (var c in e) {
                var _ = e[c],
                    d = _[o];
                if (null != d){
                    if (l) {
                        var u = parseInt(c.replace("__", ""));
                        if (u > a) {
                            a = u;
                            s = _.__target;
                            r = d;
                        }
                    } else d.apply(_.__target, null != i ? [i] : null);
                }
            }
            l && null != r && r.apply(s, null != i ? [i] : null);
        }
        return true;
    };

    this.subscribe = function(eventKey, eventCallBack, target) {
        var r = !(arguments.length > 3 && void 0 !== arguments[3]) || arguments[3],
            targetName = target.__name;
        if (null == targetName) {
            target.__name = targetName = "__" + i++;
            var s = this;
            let oldFun = target["onDestroy"];
            r && (target.onDestroy = function() {
                isDebug && cc.log("[FACADE]remove:" + eventKey + " id:" + target.__name);
                oldFun && oldFun.apply(target);
                s.remove(target);
            });
        }
        if (null == e[targetName]) {
            e[targetName] = {
                __target: target
            };
        }
        e[targetName][eventKey] = eventCallBack;
    };
    this.remove = function(t) {
        var o = t.__name;
        e[o] = null;
        delete e[o];
    };
    this.removeAll = function() {
        e = {};
    };
    this.clostView = function(t) {
        if (t) {
            this.remove(t);
            t.node.destroy();
        }
    };

    //添加调用对象
    this.addBean = function(e) {
        isDebug && cc.log("[FACADE]addBean:" + e.constructor.name);
        allObject.push(e);
    };
    //每个对象调用e方法
    this.eachBean = function(e, i) {
        isDebug && cc.log("[FACADE]eachBean:" + e);
        for (var n = 0, l = allObject.length; n < l; n++) {
            var r = allObject[n];
            if (e in r)
                try {
                    r[e].apply(r, i);
                } catch (t) {
                    cc.error(
                        "[FACADE]eachBean error: " +
                            r.constructor.name +
                            " " +
                            t.toString()
                    );
                }
        }
    };
})();
window.localcache = new (function() {
    var t = null,
        e = null,
        o = {},
        i = {};
    this.clearData = function() {
        t = null;
        e = null;
        o = {};
        i = {};
    };
    this.init = function(o, i) {
        if (!t) {
            t = o;
            e = i;
        }
    };
    this.getItem = function(i, n) {
        if (null == t) return null;
        var l = o[i];
        if (null == l || (null != l && null == l[n])) {
            var r = e[i];
            if (null == r) return null;
            l = {};
            o[i] = l;
            var a = t[i];
            if (null == a) {
                cc.warn("loacal data table " + i + " is not find!!");
                return null;
            }
            for (var s = 0, c = a.length; s < c; s++) {
                var _ = a[s];
                l[_[r]] = _;
            }
        }
        return l[n];
    };
    this.getList = function(e) {
        return null == t ? null : t[e];
    };

    // 通过多个key来获取表单元
    this.getItemByGroupKeys = function(tableName, group) {
        if(null == t) return null;
        var l = o[tableName];
        if (null == l) {
            var r = e[tableName];
            if (null == r) return null;
            l = {};
            o[tableName] = l;
            var a = t[tableName];
            if (null == a) {
                cc.warn("loacal data table " + tableName + " is not find!!");
                return null;
            }
            for (var s = 0, c = a.length; s < c; s++) {
                var _ = a[s];
                l[_[r]] = _;
            }
        }
        
        for(var obj in l) {
            var flag = true;
            for(var g in group) {                 
                if (l[obj][g] instanceof Array) {
                    if(l[obj][g][0] != group[g]) {
                        flag = false;
                        break;
                    }
                } else {
                    if(l[obj][g] != group[g]) {
                        flag = false;
                        break;
                    }
                }                
            }
            if(flag)
                return l[obj];                              
        }    
        
        return null;
    };

    //表格中选取相同value的项
    this.getFilters = function (filename, keyName, value) {
        let cfgDatas = this.getList(filename);
        if (cfgDatas) {
            let datas = cfgDatas.filter((data) => {
                if(data[keyName]){
                    return data[keyName] == value;
                }else{
                    return false;
                }
            });
            if(datas && datas.length > 0) {
                return datas;
            }
        }
        return null;
    };
    this.getFilter = function (filename, keyName, value, keyName2, value2) {
        let cfgDatas = this.getList(filename);
        if (cfgDatas) {
            let datas = cfgDatas.filter((data) => {
                if(data[keyName]){
                    return data[keyName] == value;
                }else{
                    return false;
                }
            });
            if(datas && datas.length > 0) {
                if(keyName2 != null && value2 != null){
                    for(let i = 0;i < datas.length;i++){
                        if(datas[i][keyName2] == value2){
                            return datas[i];
                        }
                    }
                }else{
                    return datas[0];
                }
            }
        }
        return null;
    };
    this.getGroup = function(e, o, n) {
        if (null == t) return null;
        var l = i[e];
        if (null == l) {
            l = {};
            i[e] = l;
        }
        var r = l[o];
        if (null == r) {
            r = {};
            l[o] = r;
            var a = t[e];
            if (null == a) {
                cc.warn("loacal data table " + e + " is not find!!");
                return null;
            }
            for (var s = 0, c = a.length; s < c; s++) {
                var _ = a[s],
                    d = _[o];
                null == r[d] ? (r[d] = [_]) : r[d].push(_);
            }
        }
        return r[n];
    };
    // 获取多个相同字段的表数据
    this.getGroupByKeys = function(tableName, group) {
        if(null == t) return null;
        var l = o[tableName];
        if (null == l) {
            var r = e[tableName];
            if (null == r) return null;
            l = {};
            o[tableName] = l;
            var a = t[tableName];
            if (null == a) {
                cc.warn("loacal data table " + tableName + " is not find!!");
                return null;
            }
            for (var s = 0, c = a.length; s < c; s++) {
                var _ = a[s];
                l[_[r]] = _;
            }
        }
        
        var arr = [];
        for(var obj in l) {
            var flag = true;
            for(var g in group) {               
                if(l[obj][g] != group[g]) {
                    flag = false;
                    break;
                }              
            }
            if(flag)
                arr.push(l[obj]);                              
        }    
        
        return arr;

    };
    this.save = function(e, n) {
        t[e] = n;
        o[e] = null;
        i[e] = null;
        cc.log("LocalCache.save: " + e + " Size:" + (null != n ? n.length : 0));
    };
    this.addData = function(e) {
        // 修复在加载界面切换账号出的问题
        if(t==null) return;
        for (var o in e) {
            t[o] = e[o];
        }
    };
})();
var n = (function() {
    function t() {}
    t.prototype.init = function(t, e) {
        var o =
            arguments.length > 2 && void 0 !== arguments[2]
                ? arguments[2]
                : null;
        this.endHand = t;
        this.errorHand = e;
        this.progressHand = o;
        this.cur_load_items = [];
        if (null == this.jsb_load_item) {
            var i = new jsb.Downloader();
            i.setOnFileTaskSuccess(this.onLoadEnd.bind(this));
            i.setOnTaskProgress(this.onLoadProgress.bind(this));
            i.setOnTaskError(this.onLoadError.bind(this));
            this.jsb_load_item = i;
        }
    };
    t.prototype.createDownloadFileTask = function(t, e, o) {
        var i =
                arguments.length > 3 && void 0 !== arguments[3]
                    ? arguments[3]
                    : 0,
            n =
                !(arguments.length > 4 && void 0 !== arguments[4]) ||
                arguments[4],
            l = {};
        l.url = t;
        l.storeUrl = e;
        l.key = o;
        l.size = i;
        l.isReload = n;
        n && this.addLoadItem(l);
        this.jsb_load_item.createDownloadFileTask(t, e, o);
    };
    t.prototype.clearJSBDownload = function() {
        if (null != this.jsb_load_item) {
            var t = this.jsb_load_item;
            t.setOnFileTaskSuccess(null);
            t.setOnTaskProgress(null);
            t.setOnTaskError(null);
            this.jsb_load_item = null;
        }
    };
    t.prototype.updateSecond = function() {};
    t.prototype.addLoadItem = function(t) {
        for (var e = 0; e < this.cur_load_items.length; e++)
            if (null == this.cur_load_items[e]) {
                this.cur_load_items[e] = t;
                return;
            }
        this.cur_load_items.push(t);
    };
    t.prototype.clearLoadItems = function(t) {
        for (var e = 0; e < this.cur_load_items.length; e++) {
            var o = this.cur_load_items[e];
            o && o.key == t && (this.cur_load_items[e] = null);
        }
    };
    t.prototype.onLoadProgress = function(t, e, o, i) {
        var n = t ? t.identifier : "";
        null != this.progressHand && this.progressHand(n, e, o, i);
    };
    t.prototype.isFileSizeEquip = function(t) {
        for (var e = 0; e < this.cur_load_items.length; e++) {
            var o = this.cur_load_items[e];
            if (o && o.key == t) {
                return (
                    0 == o.size ||
                    jsb.fileUtils.getFileSize(o.storeUrl) == o.size
                );
            }
        }
        return !0;
    };
    t.prototype.clearTempFile = function(file) {
        var storagePath = (jsb.fileUtils ? jsb.fileUtils.getWritablePath() : "/") + "update-assets";
        var tempFile = storagePath + "/" + file + ".tmp";
        if (jsb.fileUtils.isFileExist(tempFile)) {
            jsb.fileUtils.removeFile(tempFile);
        }
        tempFile = storagePath + "/" + file + ".temp";
        if (jsb.fileUtils.isFileExist(tempFile)) {
            jsb.fileUtils.removeFile(tempFile);
        }
    };
    t.prototype.onLoadEnd = function(t) {
        var e = t ? t.identifier : "";
        if (this.isFileSizeEquip(e)) {
            this.clearLoadItems(e);
            this.endHand && this.endHand(e);
        } else if (this.errorHand) {
            console.log("load item size is not equip!!!!!", e);
            this.clearTempFile(e);
            // cc.log("load item size is not equip!!!!!", e);
            this.errorHand(e);
        }
    };
    t.prototype.onLoadError = function(t, e, o, i) {
        var n = t.identifier;
        console.log("down Error: " + i, " fiel name:" + n, e, o);
        this.clearTempFile(n);
        // cc.log("down Error: " + i, " fiel name:" + n, e, o);
        this.clearLoadItems(n);
        this.errorHand && this.errorHand(n);
    };
    return t;
})();
(window.idream || (window.idream = {})).MyDownloader = n;
