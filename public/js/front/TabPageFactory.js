define(['TabPageView',
    'front/DetailTabPageView',
    'front/FeedsTabPageView',
    'front/MeetingTabPageView',
    'twigjs!front/templates/pages/layout'
],
    function(TabPageView,
        DetailTabPageView,
        FeedsTabPageView,
        MeetingTabPageView
    ){
    var TabPageFactory = {
        getTabConstructor : function(pageName){
            var constructor = _(tabs).findWhere({name:pageName});
            if(constructor){
                constructor = constructor.constructor;
            }else{
                constructor = TabPageView;
            }
            return constructor;
        },
        getTabIndex:function(pageName){
            var tab = _(tabs).findWhere({name:pageName});
            return _(tabs).indexOf(tab);
        }
    },tabs = [];
    function registerTab(name,constructor) {
        tabs.push({
            name:name,
            constructor:constructor
        })
    }
    registerTab('detail',DetailTabPageView);
    registerTab('meeting',MeetingTabPageView);
    registerTab('feeds',FeedsTabPageView);


    return TabPageFactory;
});