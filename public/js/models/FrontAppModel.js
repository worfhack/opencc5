define(['backbone','jquery','models/ActivityModel','models/ActivityFeedCollection'],function (Backbone,$,ActivityModel,ActivityFeedCollection) {
    var app, activityModel, activityFeedCollection;
    var FrontAppModel = Backbone.Model.extend({
        initialize:function(){
            // initialize Activity model from json in DOM
            var activityJSON = JSON.parse($('#activity_json').text());
            activityModel = new ActivityModel(activityJSON);
            
            // initialize Activity feed collection from json in DOM
            var activityFeedJSON = JSON.parse($('#activity_feed_json').text());
            activityFeedCollection = new ActivityFeedCollection(activityFeedJSON);
            activityModel.setFeed(activityFeedCollection);
            
        },
        isMobile:function(){
            return window.innerWidth < 1024;
        },
        getActivity:function(){
            return activityModel;
        },
        getFeed:function(){
            return activityFeedCollection;
        }
    });
    app = new FrontAppModel();
    return app
});