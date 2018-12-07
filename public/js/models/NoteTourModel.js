define(['underscore','backbone','moment'],function (_,Backbone,moment) {
    return Backbone.Model.extend({
       send:function () {

           var model = this;
           var ref_tour = this.get('ref_tour'),
               message = this.get('message');




           $.ajax({
               url:gl_page_info.baseUrl + "/tour/"+ref_tour+"/note",
               type: "POST",
               data : {
                   ref_tour:ref_tour,
                   message:message,

               },
               success:function(result){

               }
           });
       },
    });
});