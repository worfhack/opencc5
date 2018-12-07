define(['TabPageView','twigjs!front/templates/pages/chat'],function(TabPageView,template){
    var ChatTabPageView = TabPageView.extend({
        name:'chat',
        template:template
    });
    return ChatTabPageView;
});