define(['underscore','backbone','moment'],function (_,Backbone,moment) {
    return Backbone.Model.extend({
       send:function () {

           var model = this;
           var ref_tour = this.get('ref_tour'),
               message = this.get('message'),
               refdetail = this.get('refdetail');




           $.ajax({
               url:gl_page_info.baseUrl + "/tour/"+ref_tour+"/ticketdetail",
               type: "POST",
               data : {
                   ref_tour:ref_tour,
                   message:message,
                   refdetail:refdetail,

               },
               success:function(result){
                   model.trigger("zendeskDetailend")
               }
           });
       },
    });
});