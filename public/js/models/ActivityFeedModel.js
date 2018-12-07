define(['underscore', 'backbone', 'moment'], function (_, Backbone, moment) {
    return Backbone.Model.extend({
        getTwigParams:function(){
            var json = this.toJSON(),
                moment = this.getMoment();
            json.timing_label = moment.format('LLL');
            return json;
        },
        getMoment:function(){
            return moment(this.get('date_add'));
        }
    });
});
