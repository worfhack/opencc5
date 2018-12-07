define(['backbone','twig','app'],function(Backbone,Twig,app){

   var TabPageView = Backbone.View.extend({
       name:'default',
       template:Twig.twig({data:'-'}).render,
       className:'page',
       initialize:function(options){
           var view = this;
           console.log('initialize Tab',view.name);
           view.$el.addClass('page-'+view.name)
           if(!options.el){
               view.render();
           }else{
               view.afterRender();
           }
           view.on('afterAppend',view.afterAppend,view);
       },
       render:function(){
           var view = this,
               template = view.template,
               twigParams = app.getActivity().getTwigParams();
           view.$el.html(template(twigParams));
           view.afterRender();
       },
       afterRender:function(){
           // to be extended...
       },
       afterAppend:function(){
           // to be extended...
       }

   });
    return TabPageView;
});