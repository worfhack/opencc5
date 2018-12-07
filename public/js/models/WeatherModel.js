define(['backbone','underscore'],function (Backbone,_) {
    //
    // OpenWeatherMap Model
    //

    var WeatherModel = Backbone.Model.extend({
        url:function(){
            var base = 'http://api.openweathermap.org/data/2.5/',
                dayDiff = 1+Math.ceil(Math.max(this.get('dayDiff'),0)),
                isDaily = dayDiff >= 5,
                method = 'forecast'+ (isDaily ? '/daily':''),
                cnt = isDaily ? dayDiff : dayDiff*8 ;
            return base+method+'?appid='+OPENWEATHERMAP_API_KEY+'&lat='+this.options.lat+'&lon='+this.options.lon+'&cnt='+cnt;
        },
        initialize:function(options){
            this.options = options;
            var m = options.moment,
                diff = m.diff(new Date()),
                dayDiffInt = m.diff(new Date(),'days');
                dayDiff = m.diff(new Date(),'days',true);
            this.set({
                diff:diff,
                dayDiffInt:dayDiffInt,
                dayDiff:dayDiff
            });
            this.url();
            if(options.dayDiff >= 0 && options.dayDiff <= 5){
                this.on('sync',this.onSync,this);
                this.fetch();
            }
            //this.fetch();
        },
        onSync:function(){
            var moment = this.get('moment'),
                list = this.get('list'),
                sortedList = _(list).sortBy(function(el,index){
                    //console.log(moment.diff(el.dt_txt),el,index);
                    return Math.abs(moment.diff(el.dt_txt));
                }),
                bestInList = _(sortedList).first();
            this.set(bestInList);
            console.log(bestInList);
            console.log(this.getTemp('celsius'));
        },
        getMain:function(key){
            var main = this.get('main');
            return !main ? NaN : main[key];
        },
        getTemp:function(unit,toFixed){
            var kelvin = this.getMain('temp');
            toFixed = toFixed>=0 ? toFixed:2;
            console.log(kelvin+'°K');
            switch(unit){
                case 'C': // Celsius
                    return (kelvin - 273.15).toFixed(toFixed);
                case 'F': // Fahrenheit
                    return ((kelvin - 273.15)  * 1.8 + 32).toFixed(toFixed);
                case 'K': // Kelvin
                default:
                    return kelvin;
                    break;
            }
        },
        formatTemp:function(unit,toFixed){
            if(!unit) unit = iso_lang=='en'?'F':'C';
            if(!(toFixed>=0)) toFixed = 0;
            var temp = this.getTemp(unit,toFixed);
            console.log(temp,temp == 'NaN');
            if(temp == 'NaN'){
                return ' '; // nobreak space
            }else{
                return temp.toString()+'°'+unit;
            }
        }
    });
    return WeatherModel;
});