define(['underscore','backbone'],function(_,Backbone){
   var Router = Backbone.Router.extend({
      routes:{
         ':language/:code/:page':'page',
         ':language/:code':'pageDefault'
      },
      go:function(route,options){
         if(!options) options = {};
         options = _.defaults(options,{trigger:true});
         router.navigate(route,options);
      }
   });
   // Singleton
   var router = new Router();
   return router;
});