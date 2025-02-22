let renderListItem = require("RenderListItem");

cc.Class({
    extends:cc.Component,// use this for initialization
    properties: {
        data:{//list数据数组
            visible:false,
            get: function () {
                return this._data;
            },
            set: function (array) {
                this._data = array;
                if (this._data != null) {
                    for (var i = 0; i < this._data.length; i++) {
                        if (this._data[i])
                            this._data[i]['__index'] = i;
                    }
                }

                if (this.bufferZone > 0) {
                    this.reallBufferZone = this._data ? (this._data.length > this.bufferZone ? this.bufferZone : this._data.length) : 0;
                }else{
                    this.reallBufferZone = (this._data && this._data.length > 0 ? this._data.length : 0);
                }
                
                this.renderNext();
                if (this._renders && this._renders.length > 0 && this.isShowEffect) {
                    for (var i = 0; i < this._renders.length; i++) {
                        this._renders[i].showNodeAnimation();
                    }
                }
            },
        },

        selectHandle:{
            visible:false,
            set: function (handle) {
                this._selectHandle = handle;
            },
        },

        selectIndex:{
            visible:false,
            get: function () {
                return this._selectIndex;
            },
            set: function (index) {
                this._selectIndex = index;
                if (this._renders == null)
                    return;
                for (var i = 0; i < this._renders.length; i++) {
                    this._renders[i].select = this._renders[i].data == this.selectData;
                }
                if (this._selectHandle != null) {
                    this._selectHandle(this.selectData);
                }
            },
        },

        selectData:{
            visible:false,
            get: function () {
                if (this._data == null || this._selectIndex >= this._data.length ||
                    this._selectIndex < 0)
                    return null;
                return this._data[this._selectIndex];
            },
            set: function (d) {
                if (this._data == null)
                    return;
                this.selectIndex = this._data.indexOf(d);
            },
        },

        content:cc.Node,
        item:renderListItem,
        itemPrefab:cc.Prefab,
        scrollView:cc.ScrollView,
        bufferZone:0,
        repeatX:1,
        spaceX:0,
        spaceY:0,
        paddingTop:0,
        paddingBottom:0,
        isDelayCreate:{ default:false, tooltip: "是否延迟创建" },
        isReverY:{ default:false, tooltip: "是否从下到上反转Y" },
        isHorizonList:{ default:false, tooltip: "是否只是横向列表" },
        isShowEffect:{ default:true, tooltip: "设置新数据是否显示子对象动画" },
    },

    ctor(){
        this._data = null;
        this._renders = null;
        this.lastIndex = 0;
        this._itemHeight = 0;
        this._itemWidth = 0;
        this._selectHandle = null;
        this._selectIndex = -1;
        this.reallBufferZone = 0;//JSHS 2020/6/2 用于存储真实缓存个数 1、data.length > bufferZone 2、data.length <= bufferZone 第二种情况 不需要过多实例化 Item
        this._optNodeArr = [];
        this._optRenderArr = null;    },

    onLoad : function () {
        if (this.item == null && this.itemPrefab == null) {
            cc.error("List not set item!!!!!!!!!!!!!!!!!!!!!!");
            return;
        }
        this.doOptimize();

        if (this.item) {
            this.item.node.active = false;
        }
        this.schedule(this.onTimer, 0.05);
    },

    doOptimize : function(){
        if (this._optNodeArr.length > 0) {
            return;
        }
        var item = this.item ? cc.instantiate(this.item.node) : cc.instantiate(this.itemPrefab);
        var itemComp = item.getComponent(renderListItem);
        if (itemComp) {
            if (itemComp.hasOwnProperty("optimizeArr")) {
                for (var i = 0; i < itemComp.optimizeArr.length; i++) {
                    var node = new cc.Node('nodeOptimize'+i);
                    node.parent = this.node;
                    node.zIndex = 900+i;
                    this._optNodeArr.push(node);
                }
            }
        }
    },

    onDestroy : function () {
        this.unscheduleAllCallbacks();
        this.selectHandle = null;
        this._data = null;
        this._renders = null;
		this._optRenderArr = null;
    },

    updateRenders(){
        if (this._renders && this._renders.length > 0) {
            for (var i = 0; i < this._renders.length; i++) {
                this._renders[i].showData();
            }
        }
    },

    onTimer : function () {
        if (this._data == null || this._renders == null) {
            return;
        }
        if (this.scrollView == null && !this.isDelayCreate) {
            this.unscheduleAllCallbacks();
            return;
        }
        if (this.isDelayCreate && this._renders.length < this.data.length) {
            this.createBuffer();
            return;
        }
        if (this.isDelayCreate && this._renders.length >= this.data.length && this.bufferZone == 0) {
            this.unscheduleAllCallbacks();
            return;
        }
        this.updateShow();
    },

    //每帧更新item位置
    updateShow : function () {
        var point = 0;
        var index = 0;
        if (this.isHorizonList) {
            point = this.scrollView ? -this.scrollView.getScrollOffset().x : 0;
            index = Math.floor((point - this.paddingTop) / (this._itemWidth + this.spaceX));
        } else {
            point = this.scrollView ? this.scrollView.getScrollOffset().y : 0;
            index = Math.floor((point - this.paddingTop) / (this._itemHeight + this.spaceY));
        }
        index = this.isHorizonList ? Math.min(index, (this._data.length - this.bufferZone))
         : Math.min(index, (this._data.length - this.bufferZone) / this.repeatX);
        index = Math.max(index, 0);
        if (index == this.lastIndex) {
            return;
        }
        //优化：减少render.data变化数量
        var length = this._renders.length;
        this.lastIndex = index;
        index = this.isHorizonList ? Math.ceil(index) : index * this.repeatX;
        //缓存
        var lIndex = this.getLastIndexs(index);
        for (var i = 0; i < length; i++) {
            var d = this._data.length > i + index ? this._data[i + index] : null;
            if (d == null)
                continue;
            var ri = lIndex[d['__index']] != null ? lIndex[d['__index']] : this.getNullIndex();
            if (ri == -1)
                continue;
            var r = this._renders[ri];
            if (r == null)
                continue;
            if (this._itemHeight != r.node.height && this._itemHeight != 0) {
                r.setWidthHeigth(this._itemWidth, this._itemHeight);
            }
            if (this.isHorizonList) {
                r.node.x = (this._itemWidth + this.spaceX) * (i + index);
                r.node.y = 0;
            } else {
                r.node.x = (this._itemWidth + this.spaceX) * ((i + index) % this.repeatX);
                r.node.y = -(this._itemHeight + this.spaceY) * Math.floor((i + index) / this.repeatX) - this.paddingTop;
                if (this.isReverY) {
                    r.node.y = -r.node.y;
                }
            }
            r.node.active = this._data.length > i + index;
            r.node["data"] = r.data = d;
            r.select = i + index == this._selectIndex;

            var infoArr = this._optRenderArr[ri.toString()];
            if (infoArr) {
                for (var j = 0; j < infoArr.length; j++) {
                    var info = infoArr[j];
                    info.node.position = info.pos.add(cc.v2(r.node.x,r.node.y));
                    info.node.active = this._data.length > i + index;
        }
            }
        }
    },

    getLastIndexs : function (index) {
        var lIndex = {};
        var length = this._renders.length;
        for (var i = 0; i < length; i++) {
            var rd = this._renders[i].data;
            if (rd != null && (rd['__index'] < index || rd['__index'] >= index + length)) {
                this._renders[i].data = null;
            }else if (rd != null) {
                lIndex[rd['__index']] = i;
            }
        }
        return lIndex;
    },

    getNullIndex : function () {
        for (var i = 0; i < this._renders.length; i++) {
            if (this._renders[i].data == null) {
                return i;
            }
        }
        return -1;
    },

    renderNext : function () {
        if (this._renders == null){
            this._renders = new Array();
            this._optRenderArr = {};
        }
        this.lastIndex = -1;
        if (this._data == null || this._data.length == 0) {
            let length = this._renders.length;
            for (let i = 0; i < length; i++) {
                let r = this._renders[i];
                r.node["data"] = r.data = null;
            }
            return;
        }
        this.createBuffer(true);
        for (var i = 0; i < this._renders.length; i++) {
            this._renders[i].data = null;
            this._renders[i].node.active = false;
        }

        for (var infoKey in this._optRenderArr) {
            var infoArr = this._optRenderArr[infoKey];
            for (var j = 0; j < infoArr.length; j++) {
                var info = infoArr[j];
                info.node.active = false;
            }
        }

        var length = Math.ceil(this._data.length / this.repeatX);
        var rx = Math.min(this._data.length, this.repeatX);
        if (this.isHorizonList) {
            this.node.height = this._itemHeight + this.spaceY;
            this.node.width = length * (this._itemWidth + this.spaceX) + this.paddingTop + this.paddingBottom;
            if (this.content != null && this.content != this.node) {
                this.content.width = this.node.width - this.node.x;
            }
        } else {
            this.node.height = length * (this._itemHeight + this.spaceY) + this.paddingTop + this.paddingBottom;
            this.node.width = rx * (this._itemWidth + this.spaceX);
            if (this.content != null && this.content != this.node) {
                this.content.height = this.node.height - this.node.y;
            }
        }
        this.updateShow();
    },

    //创建buffer对象池
    createBuffer : function (bNotSet) {
        // let length = this.bufferZone != 0 ? this.bufferZone : (this._data && this._data.length > 0 ? this._data.length : 0);
        let length = this.reallBufferZone;
        if (this._renders.length >= length) {
            this.isDelayCreate = false;
            return;
        }
        this.repeatX = this.repeatX == 0 ? 1 : this.repeatX;
        //_renders长度不够才增加
        for (var i = this._renders.length; i < length; i++) {
            var item = this.item ? cc.instantiate(this.item.node) : cc.instantiate(this.itemPrefab);
            item.active = true;
            var itemComp = item.getComponent(renderListItem);
            if (itemComp) {
                itemComp.select = i == this._selectIndex;
                this._itemHeight = this._itemHeight == 0 || this._itemHeight == null ? itemComp.node.height : this._itemHeight;
                this._itemWidth = this._itemWidth == 0 || this._itemWidth == null ? itemComp.node.width : this._itemWidth;
                this._renders.push(itemComp);
                this.node.addChild(item);
                if (this.isHorizonList) {
                    itemComp.node.x = (this._itemWidth + this.spaceX) * i;
                    itemComp.node.y = 0;
                } else {
                    itemComp.node.x = (this._itemWidth + this.spaceX) * (i % this.repeatX);
                    itemComp.node.y = -(this._itemHeight + this.spaceY) * Math.floor(i / this.repeatX) - this.paddingTop;
                    if (this.isReverY) {
                        itemComp.node.y = -itemComp.node.y;
                    }
                }
                this.doOptRender(itemComp,i);
                
            } else {
                cc.error("List UI class is base ListItem find");
            }
            var button = item.getComponent(cc.Button);
            if (button && button.clickEvents && button.clickEvents.length > 0) {
                button.clickEvents[0].customEventData = itemComp;
            }
            if (button && button.clickEvents) {
                var event = new cc.Component.EventHandler();
                event.component = "List";
                event.target = this.node;
                event.handler = "selectItem";
                event.customEventData = itemComp;
                button.clickEvents.push(event);
            }
            if (this.isDelayCreate && !bNotSet) {
                itemComp.node["data"] = itemComp.data = this._data.length > i ? this._data[i] : null;
                return;
            }
            if (this._renders.length >= length)
                return;
        }
    },

    doOptRender: function(itemComp,idx){
        // let idx = this.addOptRender(itemComp);
        var anim = itemComp.node.getComponent(cc.Animation);
        if (anim) {
            anim.on('finished',()=>{
                this.addOptRender(itemComp,idx);

            });
        }else{
            this.addOptRender(itemComp,idx);
        }
    },

    addOptRender: function(itemComp,idx){
        this.doOptimize();
        if (itemComp.hasOwnProperty("optimizeArr")) {
            var optimizeArr = itemComp.optimizeArr;
            var infoArr = [];
            for (var i = 0; i < optimizeArr.length; i++) {
                let pos = itemComp.node.convertToNodeSpaceAR(optimizeArr[i].convertToWorldSpaceAR(cc.Vec2.ZERO));
                optimizeArr[i].parent = this._optNodeArr[i];
                optimizeArr[i].position = pos.add(cc.v2(itemComp.node.x,itemComp.node.y));
                infoArr.push({node:optimizeArr[i],pos:pos});
            }
            this._optRenderArr[idx.toString()] = infoArr;
        }
    },

    updateItemShow : function () {
        if (this._renders == null)
            return;
        for (var i = 0; i < this._renders.length; i++) {
            this._renders[i].data = this._renders[i].data;
        }
    },

    selectItem : function (event, param) {
        var item = param;
        if (item && item.data) {
            this.selectData = item.data;
        }
    },

    setWidthHeight : function (w, h) {
        this._itemHeight = h;
        this._itemWidth = w;
        if (this._renders == null)
            return;
        for (var i = 0; i < this._renders.length; i++) {
            this._renders[i].setWidthHeigth(w, h);
        }
    },

    resetScroll : function () {
        if (this.scrollView != null) {
            this.scrollView.stopAutoScroll();
            this.scrollView.scrollToTopLeft();
        }
    },

});