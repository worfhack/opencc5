define(['twig','app'],function (Twig,app) {

    Twig.extendFilter('trans',function(){
        // TODO traductions
        return arguments[0];
    });

});