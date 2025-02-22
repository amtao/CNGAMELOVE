
cc.Class({
    extends: cc.Component,
    properties: {
    },
    initMapInfo(){
        this.mapInfo = {
            1:
            {
                mapID: 1,
                step: 30,
                boss: 16,
                map: [
                    [0,0,0,0,0,0,0,],
                    [0,0,0,0,0,0,0,],
                    [0,0,0,0,0,0,0,],
                    [0,0,0,0,0,0,0,],
                    [0,0,0,0,0,0,0,],
                    [0,0,0,0,0,0,0,],
                    [0,0,0,0,0,0,0,]
                ]
            },
            2:
            {
                mapID: 2,
                step: 30,
                boss: 11,
                map: [
                    [ ,0,0,0,0,0, ,],
                    [0,0,0,0,0,0,0,],
                    [0,0,0,0,0,0,0,],
                    [0,0,0,0,0,0,0,],
                    [0,0,0,0,0,0,0,],
                    [0,0,0,0,0,0,0,],
                    [ ,0,0,0,0,0, ,]
                ]
            },
            3:
            {
                mapID: 3,
                step: 30,
                boss: 12,
                map: [
                    [0,0,0,0,0,0,0,],
                    [0,0,0,0,0,0,0,],
                    [0,0,0,0,0,0,0,],
                    [0,0,0, ,0,0,0,],
                    [0,0,0,0,0,0,0,],
                    [0,0,0,0,0,0,0,],
                    [0,0,0,0,0,0,0,]
                ]
            },
            4:
            {
                mapID: 4,
                step: 30,
                boss: 13,
                map: [
                    [ ,0,0,0,0,0, ,],
                    [0,0,0,0,0,0,0,],
                    [0,0,0,0,0,0,0,],
                    [0,0,0,0,0,0,0,],
                    [0,0,0,0,0,0,0,],
                    [0,0,0,0,0,0,0,],
                    [0,0,0, ,0,0,0,]
                ]
            },
            5:
            {
                mapID: 5,
                step: 30,
                boss: 10,
                map: [
                    [ ,0,0,0,0,0, ,],
                    [0,0,0,0,0,0,0,],
                    [0,0,0,0,0,0,0,],
                    [0,0,0, ,0,0,0,],
                    [0,0,0,0,0,0,0,],
                    [0,0,0,0,0,0,0,],
                    [ ,0,0,0,0,0, ,]
                ]
            },
        }
    },
    getCrushMap(mapID){
        (!this.mapInfo) && (this.initMapInfo());
        if(this.mapInfo[mapID]){
            return this.mapInfo[mapID];
        }
        let allMapLength = 0;
        for (let key in this.mapInfo) {
            allMapLength ++;
        }
        let realMapID = mapID%allMapLength;
        realMapID = (realMapID == 0)?allMapLength:realMapID;
        return this.mapInfo[realMapID];
    },
    getStageMapID(stageID){
        let allMapLength = 0;
        for (let key in this.mapInfo) {
            allMapLength ++;
        }
        let realMapID = stageID%allMapLength;
        realMapID = (realMapID == 0)?allMapLength:realMapID;
        return realMapID;
    }
});
