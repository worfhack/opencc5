define(['TabPageView','twigjs!front/templates/pages/feeds','app','moment'],function(TabPageView,template,app,moment){
    //
    //
    var refreshInterval;
    var FeedsTabPageView = TabPageView.extend({
        name:'feeds',
        template:template,
        afterAppend:function(){
            var view = this,
                feedCollection = app.getFeed();
            view.$('.side .date').text(moment().format('LL'));
            view.listenTo(feedCollection,'sync',view.render);
            feedCollection.fetch();
            refreshInterval = setInterval(function(){
                feedCollection.fetch();
            }, 60000);
            view.listenTo(app.getActivity().getWeather(),'sync',view.onWeatherSync);
        },
        onWeatherSync:function(){
            console.log('onWeatherSync');
            this.$('.meteo').html(app.getActivity().getWeather().formatTemp());
        },
        remove:function(){
            TabPageView.prototype.remove.call(this);
            clearInterval(refreshInterval);
            console.log('remove FeedsTabPageView');
        }
    });
    return FeedsTabPageView;
});