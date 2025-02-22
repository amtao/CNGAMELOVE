var formula = (function () {
    function formula() {}
    formula.linklink_times = function(t) {
        return (t + 1) * (1 + Math.ceil((t + 1) / 5)) * 5;
    };
    formula.linklink_right = function(t) {
        return Math.ceil(t / 10);
    };
    formula.prentice_prop_add = function(t, e) {
        return Math.min(10, Math.ceil((t / 10 + 1) * (1e-4 * e + 0.5)));
    };
    formula.partner_prop = function(t, e, o) {
        return (9 + t) * e + o * e * (Math.floor(t / 50) + 1);
    };
    formula.club_boss_gx = function(t) {
        return t / 250;
    };
    formula.gongdou_attk = function(t) {
        return 4 * t;
    };
    formula.gongdou_hp = function(t) {
        return t;
    };
    formula.tidy_chance_price = function(t) {
        return 5;
    };
    formula.gongdou_cost = function(t) {
        return 100 * (t + 1);
    };
    formula.xianli_haogan = function(t) {
        return Math.max(1, Math.min(Math.round(t / 10), 1e3));
    };
    formula.kitchen_exp = function(t) {
        return t / 30;
    };
    formula.jingying_time = function(t) {
        return Math.min(60 * Math.ceil(t / (1e3 * (1 + t / 5e4))), 1800);
    };
    formula.city_lucky = function(t, e) {
        return Math.ceil(0.3 * Math.min(e, 15) * t);
    };
    formula.flower_cost = function(t) {
        return 2e3 * Math.pow(Math.floor(t / 10), 2) + 1e3 * t;
    };
    formula.wife_meet_cost = function(t,e) {
        return Math.ceil(Math.min(
            0.025*e+10,1000
        ));
    };
    formula.wife_chuyou_cost = function(t) {
        return Math.min(5 * Math.ceil(Math.pow(t, 0.585)) + 10, 3e3);
    };
    formula.tree_yb = function(t) {
        return 30 * Math.floor((t - 1) / 10) + 30;
    };
    formula.tree_ms1 = function(t) {
        return 3e4 * Math.floor((t - 1) / 10) + 3e4;
    };
    formula.tree_ms2 = function(t) {
        return (
            1e4 *
            Math.ceil(
                (15e3 * Math.pow(Math.floor((t - 1) / 10), 1.5) + 5e4) / 1e4
            )
        );
    };
    formula.tree_ms3 = function(t) {
        return (
            1e4 *
            Math.ceil(
                (15e3 * Math.pow(Math.floor((t - 1) / 10), 1.5) + 5e4) / 1e4
            )
        );
    };
    return formula;
})();
exports.formula = formula;
