define(['TabPageView', 'app', 'twigjs!front/templates/pages/detail'], function (TabPageView, app, template) {

    var refreshInterval,
        _DAY_IN_MS_ = 1000 * 60 * 60 * 24,
        _HOUR_IN_MS_ = 1000 * 60 * 60,
        _MINUTE_IN_MS_ = 1000 * 60;
    var DetailTagPageView = TabPageView.extend({
        name: 'detail',
        template: template,
        events:{
            'click .view-more, .close-inclusions':'toggleInclusions',
        },
        afterAppend: function () {
            var view = this,
                activity = app.getActivity(),
                countdown = view.$('#countdown');

            function UpdateFromNowLabel() {
                var diff = activity.getMoment().diff(),
                    refresh_timeout = _HOUR_IN_MS_;
			console.log(diff);	
		console.log(diff);


	var total_secondes = diff / 1000;

 var prefixe = "Compte à rebours terminé dans ";
     if (total_secondes < 0)
     {
         var t = "Tour dépassé";
         total_secondes = Math.abs(total_secondes); // On ne garde que la valeur absolue
     }
 
     else if (total_secondes > 0)
     {
         var jours = Math.floor(total_secondes / (60 * 60 * 24));
         var heures = Math.floor((total_secondes - (jours * 60 * 60 * 24)) / (60 * 60));
         minutes = Math.floor((total_secondes - ((jours * 60 * 60 * 24 + heures * 60 * 60))) / 60);
         secondes = Math.floor(total_secondes - ((jours * 60 * 60 * 24 + heures * 60 * 60 + minutes * 60)));
 
         var et = "et";
         var mot_jour = "jours,";
         var mot_heure = "heures,";
         var mot_minute = "minutes,";
         var mot_seconde = "secondes";
 
         if (jours == 0)
         {
             jours = '';
             mot_jour = '';
         }
         else if (jours == 1)
         {
             mot_jour = "jour,";
         }
 
         if (heures == 0)
        {
             heures = '';
             mot_heure = '';
         }
         else if (heures == 1)
         {
             mot_heure = "heure,";
         }
 
         if (minutes == 0)
         {
             minutes = '';
             mot_minute = '';
         }
         else if (minutes == 1)
         {
             mot_minute = "minute,";
        }
 
         if (secondes == 0)
         {
             secondes = '';
             mot_seconde = '';
             et = '';
         }
         else if (secondes == 1)
         {
             mot_seconde = "seconde";
         }
 
         if (minutes == 0 && heures == 0 && jours == 0)
         {
             et = "";
         }
 
         console.log(prefixe + jours + ' ' + mot_jour + ' ' + heures + ' ' + mot_heure + ' ' + minutes + ' ' + mot_minute + ' ' + et + ' ' + secondes + ' ' + mot_seconde);

		var t = jours + ' ' + mot_jour + ' ' + heures + ' ' + mot_heure + ' ' + minutes + ' ' + mot_minute + ' ' + et + ' ' + secondes + ' ' + mot_seconde; 
    }
     else
     {
 //        compte_a_rebours.innerHTML = 'Compte à rebours terminé.';
	var t = "00:00:00"; 
  }
 
//     var actualisation = setTimeout("compte_a_rebours();", 1000);
 
                countdown.text(t);//activity.getFromNowLabel());
                console.log(activity.getFromNowLabel());
		if(diff<=0){
                    return;
                }else if(diff<_MINUTE_IN_MS_*5){
                    refresh_timeout = 1000;
                }else if(diff<_HOUR_IN_MS_ * 6){
                    refresh_timeout = _MINUTE_IN_MS_;
                }
               setTimeout(UpdateFromNowLabel,refresh_timeout)
            };
            UpdateFromNowLabel();

        }, remove: function () {
            TabPageView.prototype.remove.call(this);
            clearInterval(refreshInterval);
        },
        toggleInclusions:function () {
            this.$('.inclusions').toggleClass('visible');
        }
    });

    return DetailTagPageView;
});
