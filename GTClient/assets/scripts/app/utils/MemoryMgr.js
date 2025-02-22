
let MemoryMgr = function () {
    this.init();
};

MemoryMgr.prototype.init = function () {

    /**记录依赖资源的引用次数*/
    this.resCountMap = {};

    /**记录的需要删除的资源*/
    this._textCache = {};

    /**缓存的界面，不释放*/
    this.memoryResidentArr = [];

    /**材质的uuid*/
    this.materUUIDArr = ["eca5d2f2-8ef6-41c2-bbe6-f9c79d09c432",
						    "2874f8dd-416c-4440-81b7-555975426e93",
						    "144c3297-af63-49e8-b8ef-1cfa29b3be28",
						    "0e93aeaa-0b53-4e40-b8e0-6268b4e07bd7",
						    "c0040c95-c57f-49cd-9cbc-12316b73d0d4",
						    "6d91e591-4ce0-465c-809f-610ec95019c6",
						    "79eafaef-b7ef-45d9-9c3f-591dc836fc7a",
						    "6f801092-0c37-4f30-89ef-c8d960825b36",
						    "3a7bb79f-32fd-422e-ada2-96f518fed422",
						    "7afd064b-113f-480e-b793-8817d19f63c3",
						    "cf7e0bb8-a81c-44a9-ad79-d28d43991032",
						    "2a296057-247c-4a1c-bbeb-0548b6c98650",
						    "a9f6a9c9-3339-4cc9-b0bb-0dea55527acf",
						    "0275e94c-56a7-410f-bd1a-fc7483f7d14a",
						    "3c8cf882-6da3-4c5c-9507-8c4b6d85178a"
						    ]
};


/**检测哪些资源需要被删除*/
MemoryMgr.prototype.onUpdateCheckRemove = function(dt){
	let keys = Object.keys(this._textCache)
    for (let ii = 0; ii < keys.length;ii++){
        let k = keys[ii];
        if (this._textCache[k] != null && this._textCache[k] == 1){
            let asset = cc.resources.getAssetInfo(k);
            delete this._textCache[k];
            if (asset != null ){
                cc.assetManager.releaseAsset(asset);
                break;
            }                        
        }
    }
};

/**是否需要释放*/
MemoryMgr.prototype.isCanRelease = function(name){
	if (this.memoryResidentArr.indexOf(name) != -1){
		return false;
	}
	return true;
};


/**界面预设体的依赖引用*/
MemoryMgr.prototype.releaseAssetPrefab = function(url){
	let name = url.name;
	if (this.isCanRelease(name)){
		this.releaseAsset(url);
	}
};

/**获取资源的依赖*/
MemoryMgr.prototype.releaseAsset = function (url,noRelease) {
    var deps = cc.assetManager.dependUtil.getDepsRecursively(url._uuid ? url._uuid : url.uuid);
    this._releaseDepend(deps,noRelease);
};

//如果是material不处理
MemoryMgr.prototype.isMaterial = function(url) {
    return this.materUUIDArr.indexOf(url) != -1;
};

/**对其引用减1*/
MemoryMgr.prototype._releaseDepend = function(deps,noRelease){
    if (deps != null) {
        for (var i = 0; i < deps.length; i++) {
            if (deps[i] == null || this.isMaterial(deps[i]))
                continue;
            var count = this.resCountMap[deps[i]];
            if (count) {
                if (count <= 1) {
                    this._textCache[deps[i]] = 1;
                    delete this.resCountMap[deps[i]];
                }else{
                    this.resCountMap[deps[i]] = count - 1;
                }
            }
        };
        
    };
}

/**依赖资源添加引用*/
MemoryMgr.prototype.saveAssets = function (url) {
    if (url == null) return;
    var deps = cc.assetManager.dependUtil.getDepsRecursively(url._uuid ? url._uuid : url.uuid);
    this._addDepend(deps);
};

/**引用+1*/
MemoryMgr.prototype._addDepend = function(deps){
    if (deps != null) {
        for (var i = 0; i < deps.length; i++) {
            if (deps[i] == null || this.isMaterial(deps[i]))
                continue;
            if (this.resCountMap[deps[i]]) {
                this.resCountMap[deps[i]] = this.resCountMap[deps[i]] + 1;
            }else{
                if (this._textCache[deps[i]]) {
                    delete this._textCache[deps[i]];
                }
                this.resCountMap[deps[i]] = 1
            }
        };
    }
}



window.MemoryMgr = new MemoryMgr();