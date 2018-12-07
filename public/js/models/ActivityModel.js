define(['underscore','jquery','backbone','moment','models/WeatherModel'],function (_,$,Backbone,moment,WeatherModel) {
    var activity, feedCollection, weather, customer_detail;
    return Backbone.Model.extend({
        defaults:{
            'location':'Paris, France',
        },
        initialize:function(){
            activity = this;
            //
            customer_detail = JSON.parse($('#customer_detail_json').text());
            //
            weather = new WeatherModel({
                lat: activity.getLat(),
                lon: activity.getLng(),
                moment: activity.getMoment(),
                diff: activity.getMoment().diff(new Date()),
                dayDiff: activity.getMoment().diff(new Date(),'days')
            });
        },
        getTwigParams:function(){
            var params = {};
            _(this.attributes).each(function(value,key){
                params['activity_'+key] = value;
            });
            var moment = this.getMoment();
            params.activity_date = moment.format('LL');;
            params.activity_start_hour = moment.format('LT');
            params.from_now_label = this.getFromNowLabel();
            params.activity_feed = feedCollection.getTwigParams();
            params.activity_temperature = weather.formatTemp();
            params.thumb = thumb;
            params.customer_detail = customer_detail;
            console.log(params);
            return params
        },
        getWeather:function(){
            return weather;
        },
        setFeed:function(feed){
            feedCollection = feed;
        },
        getFromNowLabel:function(){
            return this.getMoment().from(new Date());
        },
        getMoment:function(){
            return moment(this.get('date'));
        },
        getLat:function(){
            return parseFloat(this.get('meeting_point_latitude'))
        },
        getLng:function(){
            return parseFloat(this.get('meeting_point_longitude'))
        },
        getLatLng:function(){
            var model = this;
            return {lat:model.getLat(),lng:model.getLng()};
        }
    });
});