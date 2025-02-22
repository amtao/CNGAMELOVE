let CrushMap = require('CrushMap');
let CrushHelp = require('CrushHelp');
let CrushCombo = require('CrushCombo');
let UrlLoad = require("UrlLoad");
let UIUtils = require("UIUtils");
let Utils = require("Utils");
let Initializer = require("Initializer");
import { BlockWidth, EndViewType } from 'GameDefine';

cc.Class({
    extends: cc.Component,
    properties: {
        hero: UrlLoad,
        boss: UrlLoad,
        leftRound: cc.Label,
        mapCfg: CrushMap,
        GHelp: CrushHelp,
        crushCombo: CrushCombo,
        touchBG: cc.Node,
        skAttacked: sp.Skeleton,
    },

    onLoad() {
        this.addTouchMoveEvent();

        let self = this;
        this.skAttacked.setCompleteListener((e) => {         
            self.skAttacked.node.active = false;           
        })
    },

    checkBattleEnd(){
        let endTag = Initializer.crushProxy.checkMapEnd();
        if(endTag == 1){
            Utils.utils.openPrefabView("common/ComWinView", null, {
                type:EndViewType.CrushEnd
            });
        }else if(endTag == -1){//步数为0了，
            Utils.utils.openPrefabView("common/ComLostView", null, {
                type:EndViewType.CrushEnd
            });
        }
    },

    //刷新棋盘
    refreshChessInfo() {
        this.hideHint();
        this.hideFadeAni(() => {
            do this.autoFillMapTile();
            while (null == this.findNext());
            this.showFadeAni();
            this.showHint();
            this.saveChessInfo();
            Initializer.crushProxy.resetRefreshCrush();
        });
    },

    checkCanBack() {
        return this.isCrushing;
    },

    hideFadeAni(cb) {
        for (let i = 0,len = this.mapGraph.length;i < len; i++) {
            for (let j = this.mapGraph[i].length-1; j >= 0; j--) {
                let block = this.mapGraph[i][j];
                if(block && block[5]){
                    block[5].hideChessAni();
                }
            }
        }
        this.scheduleOnce(() => {
            cb && cb();
        },1);
    },

    showFadeAni(){
        for (let i = 0,len = this.mapGraph.length;i < len; i++){
            for (let j = this.mapGraph[i].length-1;j >= 0;j--){
                let block = this.mapGraph[i][j];
                if(block && block[5]){
                    block[5].resetChessAni();
                }
            }
        }
    },

    resetLeftRound(){
        let leftRound = Initializer.crushProxy.getLeftStepRound();
        this.leftRound.string =  leftRound >= 0?leftRound:0;
    },

    resetMapInfo(){
        let mapStageID = Initializer.crushProxy.getMapID();
        if(this.mapConfig){
            if(this.mapConfig.mapID != this.mapCfg.getStageMapID(mapStageID)) {
                this.GHelp.destroyAll();
                this.initMapInfo(mapStageID,true);
            }
        } else {
            this.initMapInfo(mapStageID,false);
        }
        this.resetLeftRound();
    },

    initMapInfo(mapStageID,isNewMap){
        this.isCrushing = false;
        this.checkBattleEnd();
        let refreshTag = Initializer.crushProxy.checkRefreshCrush();
        let chessInfo = Initializer.crushProxy.getChessInfo();
        let mapConfig = this.mapCfg.getCrushMap(mapStageID);
        let heroID = Initializer.crushProxy.getHeroID(mapStageID);
        this.hero.url = UIUtils.uiHelps.getServantSpine(heroID);
        this.boss.url = UIUtils.uiHelps.getServantSpine(mapConfig.boss);
        this.combo = 0;
        this.mapConfig = mapConfig;
        let graph = [];
        if(isNewMap || (refreshTag==1)){
            chessInfo = [];
        }
        for(let i = mapConfig.map.length - 1;i >= 0;i--){
            graph[i] = [];
            for(let j = mapConfig.map[i].length - 1;j >= 0;j--){
                if(chessInfo && chessInfo.length > 0){
                    let chessData = chessInfo[i][j];
                    if(chessData){
                        let block = graph[i][j] =
                        [ chessData[0], chessData[1], chessData[2], chessData[3], null, null, null,
                          chessData[7], chessData[8], chessData[9], chessData[10], chessData[11], chessData[12]];
                        block[4] = this.GHelp.addFloorNode(block[0], block[7], block[8]);
                        block[5] = this.GHelp.addTileNode(block[7], block[8]);
                        if(0 < block[1]){
                            block[6] = this.GHelp.addLockNode(block[1]-1,block[7],block[8]);
                        }
                        block[13] = this.GHelp.addEffectNode(block[7],block[8]);
                    }
                }else{
                    if(mapConfig.map[i][j] != null){
                        let mapData = mapConfig.map[i][j];
                        let block = graph[i][j] =
                        [   mapData & 3,mapData >>> 2,null,0,null,null,null,
                            BlockWidth * j, -BlockWidth * i,
                            0,0,0,0
                        ];
                        block[4] = this.GHelp.addFloorNode(block[0],block[7],block[8]);
                        block[5] = this.GHelp.addTileNode(block[7],block[8]);
                        if(0 < block[1]){
                            block[6] = this.GHelp.addLockNode(block[1]-1,block[7],block[8]);
                        }
                        block[13] = this.GHelp.addEffectNode(block[7],block[8]);
                    }
                }
            }
        }
        this.mapGraph = graph;
        this.resetMapFloor();
        if(chessInfo && chessInfo.length > 0){
            this.initMapTile();
            this.checkSwap();
        }else{
            do this.autoFillMapTile();
            while (null == this.findNext()); //确保第一步有可消除的
            this.saveChessInfo();
        }
        this.showHint();
        Initializer.crushProxy.resetRefreshCrush();
    },

    initMapTile(){
        for(let i = this.mapGraph.length; i--;){
            for (let j = this.mapGraph[i].length; j--;){
                let block = this.mapGraph[i][j];
                if(block && block[2] != null){
                    if(block[3] === 0){
                        this.GHelp.resetTileNode(block[5],block[2]);
                    }
                }
            }
        }
    },

    resetMapFloor(){
        for (let i = 0,len = this.mapGraph.length;i < len; i++){
            for (let j = this.mapGraph[i].length-1;j >= 0;j--){
                let block = this.mapGraph[i][j];
                if(block && block[4]){
                    block[4].resetFloorState(i,j,this.mapGraph);
                }
            }
        }
    },

    autoFillMapTile(){
        this.hideHint();
        for (let i = 0,len = this.mapGraph.length;i < len; i++){
            for (let j = this.mapGraph[i].length-1;j >= 0;j--){
                let block = this.mapGraph[i][j];
                if(!block) continue;
                let leftUpTile = true,midUpTile = true,rightUpTile = true;
                let lastRow = this.mapGraph[i - 1];
                if(lastRow && lastRow.length > 0){
                    let lastBlock = lastRow[j - 1];
                    leftUpTile = lastBlock && (0 == lastBlock[1]) && (null != lastBlock[2]);
                    lastBlock = lastRow[j];
                    midUpTile = lastBlock && (0 == lastBlock[1]) && (null != lastBlock[2]);
                    lastBlock = lastRow[j + 1];
                    rightUpTile = lastBlock && (0 == lastBlock[1]) && (null != lastBlock[2]);
                }
                if (leftUpTile || midUpTile || rightUpTile) {
                    block[2] = this.GHelp.getRandTileIndex();
                    let blockRow = this.mapGraph[i];
                    let orignalTileIndex = null;
                    for(let n = 3; n--;){
                        let checkBlock = blockRow[j + n];
                        if(checkBlock == null || checkBlock[2] !== block[2]){
                            break;
                        }else if(0 === n){
                            orignalTileIndex = block[2];
                            let newTitleIndex = 0;
                            do newTitleIndex = this.GHelp.getRandTileIndex();
                            while (block[2] === newTitleIndex);
                            block[2] = newTitleIndex;
                        } 
                    }
                    for(let n = 3; n--;){
                        let blockRow = this.mapGraph[i-n];
                        if((!blockRow) || (!blockRow[j])|| (blockRow[j][2] != block[2])){
                            break;
                        }else if(0 === n){
                            let newTitleIndex = 0;
                            do newTitleIndex = this.GHelp.getRandTileIndex();
                            while (block[2] === newTitleIndex || block[2] == orignalTileIndex);
                            block[2] = newTitleIndex;
                        } 
                    }
                }
            }
        }
        for(let i = this.mapGraph.length; i--;){
            for (let j = this.mapGraph[i].length; j--;){
                let block = this.mapGraph[i][j];
                if(block && block[2] != null){
                    if(block[3] === 0){
                        this.GHelp.resetTileNode(block[5],block[2]);
                    }
                }
            }
        }
    },

    findNext() {
        let tileArray = [];
        let lockArray = [];
        for (let i = this.mapGraph.length; i--;){
            tileArray[i] = [], lockArray[i] = [];
            for (let j = this.mapGraph[i].length; j--;){
                let block = this.mapGraph[i][j];
                tileArray[i][j] = (!block) ? null : block[2];
                lockArray[i][j] = !((!block) || (0 == block[1]));
            }
        }
        for (let i = tileArray.length; i--;){
            for (let j = tileArray[i].length,len = j; j--;){
                let tile = tileArray[i][j];
                if (null != tile && !lockArray[i][j]){
                    for (let r = 4; r--;) {
                        let convert = -1;
                        let row = i;
                        let col = j;
                        switch (r) {
                            case 0:{
                                if(0 <= j - 1 && null != tileArray[i][j - 1]){
                                    if(!lockArray[i][j - 1]){
                                        convert = 1;
                                        row = i;
                                        col = j - 1;
                                        let temp = tileArray[i][j];
                                        tileArray[i][j] = tileArray[i][j - 1];
                                        tileArray[i][j - 1] = temp;
                                    }
                                }
                            }break;
                            case 1:{
                                if(tileArray[i - 1] &&  null != tileArray[i - 1][j]){
                                    if(!lockArray[i - 1][j]){
                                        convert = 0;
                                        row = i - 1;
                                        col = j;
                                        let temp = tileArray[i][j];
                                        tileArray[i][j] = tileArray[i - 1][j];
                                        tileArray[i - 1][j] = temp;
                                    }
                                }
                            }break;
                            case 2:{
                                if(j + 1 < len && null != tileArray[i][j + 1]){
                                    if(!lockArray[i][j + 1]){
                                        convert = 1;
                                        row = i;
                                        col = j + 1;
                                        let temp = tileArray[i][j];
                                        tileArray[i][j] = tileArray[i][j+1];
                                        tileArray[i][j+1] = temp;
                                    }
                                }
                            }break;
                            case 3:{
                                if(tileArray[i + 1] && null != tileArray[i + 1][j]){
                                    if(!lockArray[i + 1][j]){
                                        convert = 0;
                                        row = i + 1;
                                        col = j;
                                        let temp = tileArray[i][j];
                                        tileArray[i][j] = tileArray[i + 1][j];
                                        tileArray[i + 1][j] = temp;
                                    }
                                }
                            }break;
                        }
                        if (convert != -1) {
                            if (!0 === this.autoCheck(tileArray)) return {
                                convert: convert,
                                row: row,
                                col: col,
                                reject: [
                                    [i, j],
                                    [row, col]
                                ]
                            };
                            switch (r) {
                                case 0:{
                                    let temp = tileArray[i][j];
                                    tileArray[i][j] = tileArray[i][j - 1];
                                    tileArray[i][j - 1] = temp;
                                }break;
                                case 1:{
                                    let temp = tileArray[i][j];
                                    tileArray[i][j] = tileArray[i - 1][j];
                                    tileArray[i - 1][j] = temp;
                                }break;
                                case 2:{
                                    let temp = tileArray[i][j];
                                    tileArray[i][j] = tileArray[i][j+1];
                                    tileArray[i][j+1] = temp;
                                }break;
                                case 3:{
                                    let temp = tileArray[i][j];
                                    tileArray[i][j] = tileArray[i + 1][j];
                                    tileArray[i + 1][j] = temp;
                                }break;
                            }
                        }
                    }
                }
            }
        }
        return null;
    },

    autoCheck(tileArray) {
        for (let i = tileArray.length; i--;){
            for (let j = tileArray[i].length; j--;){
                if (null != tileArray[i][j]) {
                    for (let e = 3; e--;) {
                        if (0 == e) return true;
                        if ((null == tileArray[i][j + e]) || (
                            tileArray[i][j + e] != tileArray[i][j] )) break
                    }
                    for (let e = 3; e--;) {
                        if (0 === e) return true;
                        if ((null == tileArray[i-e]) || 
                            (null == tileArray[i-e][j])|| (
                                tileArray[i - e][j] != tileArray[i][j]  
                            )) break
                    }
                }
            }
        }
        return false;
    },

    showHint(){
        this.leftRound.unscheduleAllCallbacks();
        this.leftRound.scheduleOnce(()=>{
            this.hintNext();
        },3);
    },

    hideHint(){
        this.leftRound.unscheduleAllCallbacks();
        this.hintNode && (this.hintNode.active = false);
    },

    hintNext() {
        let next = this.findNext();
        if(null != next){//
            let block = this.mapGraph[next.row][next.col];
            if(block){
                this.hintNode && (this.hintNode.active = true);
                this.hintNode = this.GHelp.addHintNode(this.hintNode, next.convert, block[7], block[8]);
            }
        }
    },

    addTouchMoveEvent() {
        let self = this;
        this.touchBG.on(cc.Node.EventType.TOUCH_START, (event) => {
            self.bEnd = false;
            self.bDrag = false;
            self.lastDragPos = event.touch.getLocation();
            let point = self.node.convertToNodeSpaceAR(event.touch.getLocation())
            self.mousedown(point);
        }, this);
        this.touchBG.on(cc.Node.EventType.TOUCH_MOVE, (event) => {
            if(self.bEnd) {
                return;
            }
            let point = event.touch.getLocation();
            if(Math.abs(point.x - self.lastDragPos.x) > 15 || Math.abs(point.y - self.lastDragPos.y) > 15) {
                self.bDrag = true;
            }
        }, this);
        this.touchBG.on(cc.Node.EventType.TOUCH_END, (event) => {
            let point = self.node.convertToNodeSpaceAR(event.touch.getLocation())
            self.mouseUp(point);
        }, this);
        this.touchBG.on(cc.Node.EventType.TOUCH_CANCEL, (event) => {
            self.bDrag = false;
        }, this);
    },

    mousedown(point) {
        if(this.isCrushing) return;
        if(Initializer.crushProxy.checkLifeCount()) {
            this.hideHint();
            let row = parseInt(Math.abs(point.y / BlockWidth));
            let col = parseInt((point.x / BlockWidth));
            //if(null == this.srcRow && null == this.srcCol) {
            if(this.mapGraph[row] && this.mapGraph[row][col]) {
                let block = this.mapGraph[row][col];
                if(block[1] == 0 && null != block[2]) {
                    if(null == this.srcRow && null == this.srcCol) {
                        // 如果当前没有选择的情况
                        this.srcRow = row;
                        this.srcCol = col;
                        this.srcBlock = this.mapGraph[this.srcRow][this.srcCol];
                        this.srcBlock[13].showSelect(true);
                    } else {
                        if((row == this.srcRow && Math.abs(col - this.srcCol) == 1)
                         || (Math.abs(row - this.srcRow) == 1 && col == this.srcCol)) {
                            // 如果相邻直接结束
                            this.mouseUp(point);
                            this.bEnd = true;
                        } else { // 如果点击的不是之前相邻的就改成新点击的
                            this.srcRow = row;
                            this.srcCol = col;
                            this.srcBlock && this.srcBlock[13].showSelect(false);
                            this.srcBlock = this.mapGraph[this.srcRow][this.srcCol];
                            this.srcBlock[13].showSelect(true);
                        } 
                    }
                }
            }
            //}
        }
    },

    mouseUp(point) {
        if(this.bEnd) {
            return;
        }
        if(this.isCrushing) return;
        if(null != this.srcRow && null != this.srcCol) {
            this.tarRow = parseInt(Math.abs(point.y / BlockWidth));
            this.tarCol = parseInt((point.x / BlockWidth));
            let endBlock = null;
            // --fixed issue 三消点击一个icon，再点击与之不相邻的其他地方，这个icon会自动和相邻的icon交换 2020.07.30
            if(!this.bDrag) { //点击手感
                if(this.tarRow == this.srcRow && Math.abs(this.tarCol - this.srcCol) == 1) {
                    if(this.tarCol > this.srcCol) {
                        this.mapGraph[this.srcRow][this.srcCol + 1] && (endBlock = this.mapGraph[this.srcRow][this.srcCol + 1]);
                    } else if(this.tarCol < this.srcCol) {
                        this.mapGraph[this.srcRow][this.srcCol - 1] && (endBlock = this.mapGraph[this.srcRow][this.srcCol - 1]);
                    }
                } else if(Math.abs(this.tarRow - this.srcRow) == 1 && this.tarCol == this.srcCol) {
                    if(this.tarRow > this.srcRow) {
                        this.mapGraph[this.srcRow + 1] && (endBlock = this.mapGraph[this.srcRow + 1][this.srcCol]);
                    } else if(this.tarRow < this.srcRow) {
                        this.mapGraph[this.srcRow - 1] && (endBlock = this.mapGraph[this.srcRow - 1][this.srcCol]);
                    }
                }
            } else { //滑动手感            
                if(this.tarCol > this.srcCol) {
                    endBlock = this.mapGraph[this.srcRow][this.srcCol + 1];
                } else if(this.tarCol < this.srcCol) {
                    endBlock = this.mapGraph[this.srcRow][this.srcCol - 1];
                } else {
                    if(this.tarRow > this.srcRow) {
                        this.mapGraph[this.srcRow + 1] && (endBlock = this.mapGraph[this.srcRow + 1][this.srcCol]);
                    } else if(this.tarRow < this.srcRow) {
                        this.mapGraph[this.srcRow - 1] && (endBlock = this.mapGraph[this.srcRow - 1][this.srcCol]);
                    }
                }
            }

            if(this.srcBlock && endBlock && (0 == endBlock[1]) && (null != endBlock[2])) {
                this.isCrushing = true;
                this.tarBlock = endBlock;
                this.srcBlock[13].showSelect(false);
                this.startSwap(this.srcBlock, this.tarBlock);
                this.srcBlock = null;
                this.srcRow = null;
                this.srcCol = null;
                this.tarRow = null;
                this.tarCol = null;
            } else if(!this.bDrag && this.tarRow != this.srcRow || this.tarCol != this.srcCol) {
                this.srcBlock[13].showSelect(false);
                let tmpSrcBlock = this.mapGraph[this.tarRow][this.tarCol];
                this.srcBlock = tmpSrcBlock ? tmpSrcBlock : null;
                if(this.srcBlock) {
                    this.srcBlock[13].showSelect(true);
                    this.srcRow = this.tarRow;
                    this.srcCol = this.tarCol;
                } else {
                    this.srcRow = null;
                    this.srcCol = null;
                }
                this.tarRow = null;
                this.tarCol = null;
            }
        }
        this.bDrag = false;
        this.bEnd = false;
    },

    clearTarget: function(node) {
        if(this.srcBlock && this.srcBlock[13]) {
            this.srcBlock[13].showSelect(false);
        }
        this.srcRow = null;
        this.srcCol = null;
    },

    convert(a, b) {
        let c;
        c = a[2];
        a[2] = b[2];
        b[2] = c;
        c = a[3];
        a[3] = b[3];
        b[3] = c;
        c = a[5];
        a[5] = b[5];
        b[5] = c;
        c = a[11];
        a[11] = b[11];
        b[11] = c
    },
    swapBlock(srcBlock, tarBlock,cb){
        let swapIndex = 0;
        this.convert(srcBlock, tarBlock);
        {
            let tarPos = tarBlock[5].node.getPosition();
            let sequence = cc.sequence(cc.moveTo(0.2,tarPos),cc.callFunc(()=>{
                swapIndex++;
                cb && cb(swapIndex);
            }));
            srcBlock[5].node.runAction(sequence);
        }
        {
            let tarPos = srcBlock[5].node.getPosition();
            let sequence = cc.sequence(cc.moveTo(0.2,tarPos),cc.callFunc(()=>{
                swapIndex++;
                cb && cb(swapIndex);
            }));
            tarBlock[5].node.runAction(sequence);
        }
    },
    startSwap(srcBlock, tarBlock){
        this.swapBlock(srcBlock, tarBlock,(swapIndex)=>{
            if(swapIndex >= 2){
                if(this.isCrushing && !this.checkSwap()){
                    this.swapBlock(srcBlock,tarBlock, (newIndex) => {
                        (newIndex >= 2) && (this.isCrushing = false);
                    });
                }
            }
        });
    },
    checkSwap() {
        let allCrushBlock = {};
        let allTileArray = [];
        for (let i = this.mapGraph.length; i--;) allTileArray[i] = [];
        for (let i = this.mapGraph.length; i--;){
            for (let j = this.mapGraph[i].length; j--;){
                let block = this.mapGraph[i][j];
                if (block && null != block[2]) {
                    block[12] = block[11] = 0;
                    let crushBlock = [];
                    for (let c = i; 0 <= --c;){
                        if(!this.mapGraph[c]) break;
                        if(!this.mapGraph[c][j]) break;
                        let testBlock = this.mapGraph[c][j];
                        if(block[2] != testBlock[2]){
                            break;
                        }
                        crushBlock[crushBlock.length] = [c + "," + j,testBlock, c, j];
                    }
                    if (1 < crushBlock.length){
                        crushBlock[crushBlock.length] = [i + "," + j,block, i,j];
                        for (let c = crushBlock.length; c--;){
                            let e = crushBlock[c];
                            allCrushBlock[e[0]] = e[1];
                            allTileArray[e[2]][e[3]] = e[1][2];
                        }
                    }
                    crushBlock = [];
                    for (let c = j; 0 <= --c;){
                        if(!this.mapGraph[i][c]) break;
                        let testBlock = this.mapGraph[i][c];
                        if(block[2] != testBlock[2]){
                            break;
                        }
                        crushBlock[crushBlock.length] = [i + "," + c,testBlock, i, c];
                    }
                    if (1 < crushBlock.length){
                        crushBlock[crushBlock.length] = [i + "," + j,block, i,j];
                        for (let c = crushBlock.length; c--;){
                            let e = crushBlock[c];
                            allCrushBlock[e[0]] = e[1];
                            allTileArray[e[2]][e[3]] = e[1][2];
                        }
                    }
                }
            }
        }
        let crushCount = 0;
        for (let key in allCrushBlock) crushCount++;
        if (2 < crushCount) {
            this.combo++;
            let playData = this.getPlayData(allCrushBlock);
            this.eliminate(allCrushBlock);
            this.scheduleOnce(()=>{
                this.crushCombo.setComboCount(this.combo);
                Initializer.crushProxy.playCrush(playData,this.combo,()=>{
                    if(!this.isCrushing){
                        this.checkBattleEnd();
                    }
                });
                this.dropBlock();
            },0.35);
            return true;
        }
        this.combo = 0;
        return false;
    },
    eliminate(allCrushBlock) {
        let num = 0;
        for (let key in allCrushBlock){
            let block = allCrushBlock[key];
            if (block[1] <= 0){
                num++;
            }
        }
        for (let key in allCrushBlock){
            let block = allCrushBlock[key];
            if(0 < block[1]){
                this.GHelp.resetLockNode(block[6],--block[1]);
            }else{
                if(0 < block[0]){
                    this.GHelp.resetFloorNode(block[4],--block[0]);
                }
                let crushPos = key.split(",");
                this.isProperty(block, parseInt(crushPos[0]), parseInt(crushPos[1]),num);
            }
        }
    },

    /***
    *@param num记录当前消除了几个，用来播放特效
    */
    isProperty(block, row, col,num) {
        if(this.mapGraph[row]){
            let rowBlockArray = this.mapGraph[row];
            if(rowBlockArray[col - 1] && (null != rowBlockArray[col - 1][2])){
                rowBlockArray[col - 1][11] = 2;
            }
            if(rowBlockArray[col + 1] && (null != rowBlockArray[col + 1][2])){
                rowBlockArray[col + 1][11] = 1;
            }
            if(rowBlockArray[col] && (null != rowBlockArray[col][2])){
                rowBlockArray[col][11] = 0;
            }
        }
        switch (block[3]) {
            case 1:{
                block[3] = 0;
            }break;
            case 2:{
                block[3] = 0;
            }break;
            case 3:{
                block[3] = 0;
            }break;
            case 4:{
                block[3] = 0;
            }break;
        }
        switch (block[12]) {
            case 1:{
            }break;
            case 2:{
            }break;
        }
        block[2] = null;
        this.GHelp.emptyTileNode(block[5],this.combo,num);
        block[13].onShootBoss();
        this.skAttacked.node.active = true;
        this.skAttacked.setAnimation(0, 'animation', false);
    },
    dropBlock() {
        let moveList = this.hitab();
        if(moveList.length == 0){
            if (!this.checkSwap()) {
                if(null != this.findNext()){
                    this.isCrushing = false;
                    this.showHint();
                    this.saveChessInfo();
                    this.checkBattleEnd();
                }else{
                    this.hideHint();
                    this.hideFadeAni(()=>{
                        do this.autoFillMapTile();
                        while (null == this.findNext());
                        this.showFadeAni();
                        this.showHint();
                        this.isCrushing = false;
                        this.saveChessInfo();
                        this.checkBattleEnd();
                    });
                }
            }else{
                this.showHint();
            }
        }else{
            for(let i = moveList.length;i--;){
                let moveBlock = moveList[i];
                let tarPos = new cc.Vec2(moveBlock[7],moveBlock[8]);
                let sequence = cc.sequence(cc.moveTo(0.1,tarPos),cc.callFunc(()=>{
                    moveList.pop();
                    if(moveList.length == 0){
                        this.dropBlock();
                        return;
                    }
                }));
                moveBlock[5].node.runAction(sequence);
            }
        }
    },
    hitab() {
        let moveList = [];
        for (let i = this.mapGraph.length; i--;){
            for (let j = -1,len = this.mapGraph[i].length; j++ < len;){
                let block = this.mapGraph[i][j];
                if (block && null == block[2]) {
                    let upBlock = 0 == i ? [] : this.mapGraph[i - 1];
                    do{
                        if (upBlock[j] && (0 == upBlock[j][1]) && (null != upBlock[j][2])){
                            this.convert(block, upBlock[j]);
                            moveList[moveList.length] = block;
                            break;
                        }
                        let rightBlock = upBlock[j + 1];
                        if(rightBlock && (0 == rightBlock[1]) && (1 != rightBlock[11]) && (null != rightBlock[2])){
                            if((null == this.mapGraph[i][j+1]) || (null != this.mapGraph[i][j+1][2])){
                                this.convert(block, upBlock[j+1]);
                                moveList[moveList.length] = block;
                                break;
                            }
                        }
                        let leftBlock = upBlock[j-1];
                        if(leftBlock && (0 == leftBlock[1]) && (2 != leftBlock[11]) && (null != leftBlock[2])){
                            if((null == this.mapGraph[i][j-1]) || (null != this.mapGraph[i][j-1][2])){
                                this.convert(block, upBlock[j-1]);
                                moveList[moveList.length] = block;
                                break;
                            }
                        }
                        if(null == upBlock[j]){
                            if((null == upBlock[j-1]) && (null == upBlock[j+1])){
                                moveList[moveList.length] = block;
                                block[11] = 0;
                                block[2] = this.GHelp.getRandTileIndex();
                                this.GHelp.resetMoveTileNode(block[5],block[2],block[7],block[8]+BlockWidth);
                                break;
                            }
                        }
                    }while(0);
                }
            }
        }
        return moveList;
    },
    saveChessInfo(){
        Initializer.crushProxy.saveChessInfo(this.getChessInfo());
    },
    getChessInfo(){
        let chessInfo = [];
        for (let i = 0,i_len = this.mapGraph.length;i < i_len;i++){
            chessInfo[i] = [];
            for (let j = 0,j_len = this.mapGraph[i].length;j < j_len;j++){
                let block = this.mapGraph[i][j];
                if(block){
                    chessInfo[i][j] =
                    [
                        block[0],
                        block[1],
                        block[2],
                        block[3],null,null,null,
                        block[7],
                        block[8],
                        block[9],
                        block[10],
                        block[11],
                        block[12],
                    ];
                }
            }
        }
        return chessInfo;
    },
    getPlayData(allCrushBlock){
        let playData = {};
        for (let key in allCrushBlock){
            let block = allCrushBlock[key];
            let color = block[2]+1;
            if(null == playData[color]){
                playData[color] = 0;
            }
            playData[color] ++;
        }
        let list = [];
        for (let key in playData) {
            list.push({
                color:Number(key),
                num:playData[key]
            });
        }
        return list;
    }
});
