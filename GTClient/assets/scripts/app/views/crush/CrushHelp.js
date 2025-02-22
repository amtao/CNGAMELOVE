let CrushItem = require('CrushItem');

cc.Class({
    extends: cc.Component,

    properties: {
        battleFloor:cc.Node,
        battleTile:cc.Node,
        battleLock:cc.Node,
        battleNode:cc.Node,
        crushItem:cc.Node,
        hintItem:cc.Node,
    },
    onLoad(){
        this.crushItem.active = false;
        this.hintItem.active = false;
    },
    destroyAll(){
        this.battleFloor.destroyAllChildren();
        this.battleTile.destroyAllChildren();
        this.battleLock.destroyAllChildren();
    },
    addFloorNode(floorIndex,posx,posy){
        let crushNode = cc.instantiate(this.crushItem);
        crushNode.active = true;
        let crushItem = crushNode.getComponent('CrushItem');
        crushItem.initCrushFloor('floor_'+floorIndex);//floor_0--floor_1
        this.battleFloor.addChild(crushNode);
        crushNode.setPosition(posx,posy);
        return crushItem;
    },
    resetFloorNode(item,floorIndex){
        item.initCrushFloor('floor_'+floorIndex);
    },
    addEffectNode(posx,posy){
        let crushNode = cc.instantiate(this.crushItem);
        crushNode.active = true;
        let crushItem = crushNode.getComponent('CrushItem');
        this.battleNode.addChild(crushNode);
        crushItem.initEffectNode();
        crushNode.setPosition(posx,posy);
        return crushItem;
    },
    addTileNode(posx,posy){
        let crushNode = cc.instantiate(this.crushItem);
        crushNode.active = true;
        let crushItem = crushNode.getComponent('CrushItem');
        this.battleTile.addChild(crushNode);
        crushNode.setPosition(posx,posy);
        return crushItem;
    },
    resetTileNode(item,tileIndex){
        item.initCrushItem('tile_'+tileIndex);//tile_0-tile_1....5
    },
    resetMoveTileNode(item,tileIndex,posx,posy){
        item.initCrushItem('tile_'+tileIndex);
        item.node.setPosition(posx,posy);
    },
    //置空并播放爆炸动画
    emptyTileNode(item,combo,num){
        item.emptyCrushItem(combo,num);
    },
    addLockNode(lockIndex,posx,posy){
        let crushNode = cc.instantiate(this.crushItem);
        crushNode.active = true;
        let crushItem = crushNode.getComponent('CrushItem');
        crushItem.initCrushItem('lock_'+lockIndex);
        GHelp.LockNode.addChild(crushNode);
        crushNode.setPosition(posx,posy);
        return crushItem;
    },
    resetLockNode(item,lockIndex){
        item.initCrushItem('lock_'+lockIndex);
    },
    addHintNode(hintNode,convert,posx,posy){
        if(null == hintNode){
            let crushNode = cc.instantiate(this.hintItem);
            crushNode.active = true;
            this.battleNode.addChild(crushNode);
            hintNode = crushNode;
        }
        let crushItem = hintNode.getComponent('CrushItem');
        crushItem.showHint(convert);//0-上下--1-左右
        if(convert == 0){
            hintNode.setPosition(posx,posy-32);
        }else{
            hintNode.setPosition(posx+32,posy);
        }
        return hintNode;
    },
    getRandTileIndex(){
        let a = 5;
        return parseInt(Math.random() * (a + 1));
    }
});
