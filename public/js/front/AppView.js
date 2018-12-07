define(['backbone', 'jquery', 'router', 'TabPageFactory'], function (Backbone, $, router, TabPageFactory) {
    // AppView is used as singleton
    var view, tabPageView,
        navArrowLeft,
        navArrowRight,
        chatVisible = false;
    //
    //
    return Backbone.View.extend({
        el: 'body', events: {
            'click #nav a': 'clickNav',
            'click .site_nav': 'clickSlideNav',
            'click #nav .chat': 'clickChat',
        },
        initialize: function () {
            view = this;
            view.listenTo(router, 'route:page', view.loadPage);
            view.listenTo(router, 'route:pageDefault', view.loadPageDefault);
            Backbone.history.start({pushState: true});
            //
            navArrowLeft = view.$('.glyphicon-menu-left');
            navArrowRight = view.$('.glyphicon-menu-right');
            //
            view.updateNavArrowsVisibility();
            //
            function LoadZopim(){
                try {
                    $zopim.livechat.window.onHide(function () {
                        view.hideChat();
                    });
                }catch(err){
                    setTimeout(LoadZopim,1000);
                }
            }
            LoadZopim();
        },
        hideChat:function(){
            chatVisible = false;
            view.$('#nav button').removeClass('active');
            view.$('#nav a.'+tabPageView.name).addClass('active');
        },
        updateNavArrowsVisibility:function(){
            if (view.$('#nav a.active').parent('li').prev().length == 0) {
                navArrowLeft.hide();
            }else {
                navArrowLeft.show();
            }
            if (view.$('#nav a.active').parent('li').next().next().length == 0) {
                navArrowRight.hide();
            } else {
                navArrowRight.show();
            }
        },
        clickNav: function (e) {
            e.preventDefault();
            e.stopPropagation();
            var currentTarget = $(e.currentTarget);
            router.go(currentTarget.attr('href'));
            if(chatVisible){
                $zopim.livechat.window.hide();
                view.hideChat();
            }
        },
        clickChat:function(){
            chatVisible = !chatVisible;
            if(chatVisible){
                view.$('#nav a').removeClass('active');
                view.$('#nav .chat').addClass('active');
                $zopim.livechat.window.show();
            }else{
                $zopim.livechat.window.hide();
            }
        },
        clickSlideNav: function (e) {
            e.preventDefault();
            e.stopPropagation();
            if ($(e.currentTarget).hasClass('glyphicon-menu-left')){
                if ($('#nav a.active').parent('li').prev().length > 0) {
                    var target = $('#nav a.active').parent('li').prev().children('a').attr('href');
                }
            } else {
                if ($('#nav a.active').parent('li').next().length > 0) {
                    var target = $('#nav a.active').parent('li').next().children('a').attr('href');
                }
            }
            router.go(target);
        },
        loadPageDefault:function(language, code){
            view.loadPage(language, code, 'detail');
        },
        loadPage: function (language, code, pageName) {
            var constructor = TabPageFactory.getTabConstructor(pageName), options = {}, previousTabPageView;
            if (tabPageView) {
                previousTabPageView = tabPageView;
            } else {
                options.el = view.$('.page').get(0);
            }
            view.$('#nav a, #nav button').removeClass('active');
            view.$('#nav a.' + pageName).addClass('active');
            tabPageView = new constructor(options);
            if(previousTabPageView){
                var direction = TabPageFactory.getTabIndex(previousTabPageView.name) - TabPageFactory.getTabIndex(pageName) > 0,
                    fromClass = 'slide-from-'+(direction?'right':'left'),
                    toClass = 'slide-to-'+(direction?'left':'right');
                tabPageView.$el.addClass(fromClass);
                previousTabPageView.$el.addClass(toClass);
                view.$el.addClass('slide');
                view.updateNavArrowsVisibility();

                previousTabPageView.$el.one('animationend',function(){
                    tabPageView.$el.removeClass(fromClass);
                    view.$el.removeClass('slide');
                    previousTabPageView.remove();
                });
                view.$('.site').append(tabPageView.el);
            }
            tabPageView.trigger('afterAppend');
        }
    });
});