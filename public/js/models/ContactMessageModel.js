define(['underscore','backbone','moment'],function (_,Backbone,moment) {
    return Backbone.Model.extend({

       send:function () {

           var model = this;
           var ref_tour = this.get('ref_tour'),
               message = this.get('message'),
               mode = this.get('mode'),
               all = this.get('all');
           directCustomer = this.get('directCustomer');


           $.ajax({
               url:gl_page_info.baseUrl + "/tour/"+ref_tour+"/contact",
               type: "POST",
               data : {
                   ref_tour:ref_tour,
                   message:message,
                   mode:mode,
                   directCustomer:directCustomer,
                   all:all,
               },
               success:function(result){
                 model.trigger("messageSend")

               }
           });
       },
    });
});
