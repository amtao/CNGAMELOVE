var CreateProxy = function() {

    this.CREATE_RANDOM_NAME = "CREATE_RANDOM_NAME";
    this.randomName = "";

    this.ctor = function() {
        JsonHttp.subscribe(proto_sc.system.randname, this.onRandomName, this);
    };
    this.clearData = function() {
        this.randomName = "";
    };
    this.onRandomName = function(t) {
        this.randomName = t.name;
        facade.send(this.CREATE_RANDOM_NAME, this.randomName);
    };
    this.sendRandomName = function() {
        var t = new proto_cs.guide.randName();
        JsonHttp.send(t);
    };
    this.sendCreate = function(t, e, o) {
        var i = new proto_cs.guide.setUinfo();
        i.sex = t;
        i.job = e;
        i.name = o;
        JsonHttp.send(i, function() {
            facade.send("USER_DATA_OVER");
        });
    };
}
exports.CreateProxy = CreateProxy;
var CreateData = function(t, e, o) {
    this.sex = t;
    this.job = e;
    this.skin = o;
}
exports.CreateData = CreateData;
