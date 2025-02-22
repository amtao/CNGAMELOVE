import { TofuType } from 'GameDefine';

cc.Class({
    extends: cc.Component,
    properties: {
        tofuParentType: {
            default:      TofuType.NoType,
            type:         cc.Enum(TofuType),
        },
        tofuType: {
            default:      TofuType.NoType,
            type:         cc.Enum(TofuType),
        },
    },
    onCollisionEnter(other, self) {
        let otherCollison = other.node.getComponent('TofuGirlCollision');
        let selfCollison = self.node.getComponent('TofuGirlCollision');
        let otherType = otherCollison.tofuType;
        let selfType = selfCollison.tofuType;
        if(otherType != selfType && (TofuType.TofuGirl == selfType || TofuType.TofuGirl == otherType)){
            let girlType = (TofuType.TofuGirl == selfType)?selfType:otherType;
            let blockType = (TofuType.TofuGirl == selfType)?otherType:selfType;
            let parentType = (TofuType.TofuGirl == selfType)?otherCollison.tofuParentType:selfCollison.tofuParentType;
            facade.send("TofuCollisionCheck",{
                blockType:blockType,
                girlType:girlType,
                parentType:parentType
            })
        }
    }
});
