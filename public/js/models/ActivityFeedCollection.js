define(['underscore', 'backbone', 'moment', 'models/ActivityFeedModel'], function (_, Backbone, moment, ActivityFeedModel) {
    return Backbone.Collection.extend({
        model:ActivityFeedModel,
        url:function(){
            return code_base_url+'feed';
        },
        getTwigParams:function(){
            var params = [];
            this.each(function(model){
               params.push(model.getTwigParams())
            });
            return params;
        }
    });
});