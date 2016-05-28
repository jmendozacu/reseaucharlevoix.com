/**
 * Created by hungnt on 4/7/16.
 */
 var TRAJET_EST_MAP_D ={
    1: 'h_mmo',
    2: 'h_bsa',
    3: 'h_prs',
    4: 'h_bsp',
};
var TRAJET_EST_MAP_A ={
    1: 'h_bsp',
    2: 'h_prs',
    3: 'h_bsa',
    4: 'h_mmo',
};
var TRAJET_OUEST_MAP_D ={
    1: 'h_bsp',
    2: 'h_sjo',
    3: 'h_sti',
    4: 'h_mal',
};
var TRAJET_OUEST_MAP_A ={
    1: 'h_mal',
    2: 'h_sti',
    3: 'h_sjo',
    4: 'h_bsp',
};

var TRAJET_EST_HAS_X_MAP_D ={
    1: 'h_mmo',
    2: 'h_bsp',
    3: 'h_bsp',
    4: 'h_mal',
};
var TRAJET_EST_HAS_X_MAP_A ={
    1: 'h_mal',
    2: 'h_bsp',
    3: 'h_bsp',
    4: 'h_mmo',
};

app.factory('Product', function (restmod) {
    return restmod.model(URL + 'toolbox-designer/index/products').mix({
        $extend: {
            Record: {
                getDelay: function (direction) {
                    if(!this[direction]) return;
                    var delayTime = this[direction].delais;
                    if(delayTime && delayTime != 'null'){
                        return "Arrêt "+ delayTime + " minutes BSP"
                    }
                    return '';
                },
                getLabel: function (direction) {
                    if(!this[direction]) return;
                    var html =''
                    var self = this;
                    var points = [
                        {pos:0, id:null,html:''},
                        {pos:0, id:null,html:''},
                        {pos:0, id:null,html:''},
                        {pos:0, id:null,html:''},
                    ]
                    _.each(points, function (point, index) {
                        points[index].pos = index;
                        var cursor = index+1;
                        points[index].id = self.getPointPlace(direction, cursor);
                        points[index].html = self.getPointTime(direction, cursor)+' '+self.getPointPlace(direction, cursor);
                        
                    })
                    _.each(points, function (point, index) {
                        
                        if(self[direction].reservation_2 == '0'){
                            if(self[direction].station_depart == _.toLower(point.id)){
                                    html += "<p><strong>"+ point.html +' - ';  
                            }
                            if(self[direction].station_arrive == _.toLower(point.id)){
                                    html += point.html + "</strong></p>";
                            }
                            if(index == 4){
                                html += '<p class="delay">'+self.getDelay(direction)+'</p>' 
                            }
                        }else{
                            if(index == 2){
                                html += '<p class="delay">'+self.getDelay(direction)+'</p>' 
                            }
                            if(index == 0 || index == 2){
                                html += "<p><strong>"+ point.html +' - ';  
                            }else{
                                html += point.html + "</strong></p>";
                            }
                        }
                    })
                    return html
                    
                },
                getPointPlace: function (direction, point) {
                    return this.getPointSpecific(direction, point, 'place');
                },
                getPointTime: function (direction, point) {
                    var self = this;
                    return self.getFormatTime(self.getPointSpecific(direction, point, 'time'));
                },
                getFormatTime: function (timeString) {
                    var timeArray = timeString.split(':');
                    return timeArray[0]+'h'+timeArray[1];
                },
                getPointSpecific: function (direction, point, type) {
                    var tripDirection = this[direction].info.trips.direction;
                    var tripTrajet = this[direction].info.trips.trajet;
                    if(this[direction].info.xtrips){
                            if(point == 1){
                                if(type == 'place'){
                                    return _.toUpper(this[direction].station_depart);
                                }
                                return this[direction].info.trips['h_'+this[direction].station_depart];   
                            }
                            if(point == 2){
                                 if(type == 'place'){
                                        return 'BSP';
                                }
                                return this[direction].info.trips.h_bsp;   
                            }
                            if(point == 3){
                                 if(type == 'place'){
                                        return 'BSP';
                                }
                                return this[direction].info.xtrips.h_bsp;   
                            }
                            if(point == 4){
                                if(type == 'place'){
                                    return _.toUpper(this[direction].station_arrive);
                                }
                                return this[direction].info.xtrips['h_'+this[direction].station_arrive];   
                            }

                    }

                    if(tripDirection == 'est' && tripTrajet == 'ouest'){
                            if(type == 'place'){
                                return _.toUpper(TRAJET_EST_MAP_D[point].substring(2));
                            }
                            return this[direction].info.trips[TRAJET_EST_MAP_D[point]];
                    }
                    if(tripDirection == 'est' && tripTrajet == 'est'){
                        if(type == 'place'){
                            return _.toUpper(TRAJET_OUEST_MAP_D[point].substring(2));
                        }
                        return this[direction].info.trips[TRAJET_OUEST_MAP_D[point]];
                    }
                    if(tripDirection == 'ouest' && tripTrajet == 'est'){
                        if(type == 'place'){
                            return _.toUpper(TRAJET_OUEST_MAP_A[point].substring(2));
                        }
                        return this[direction].info.trips[TRAJET_OUEST_MAP_A[point]];
                    }
                    if(type == 'place'){
                        return _.toUpper(TRAJET_EST_MAP_A[point].substring(2));
                    }
                    return this[direction].info.trips[TRAJET_EST_MAP_A[point]];
                },
                $getReservations: function () {
                    var self = this;
                    var url = URL + 'toolbox-designer/index/reservations';
                    var data = {
                        from_salles_id: self.depart.info.booking.salleId,
                        depart_blocking_id: self.depart.info.booking.id,
                        from_depart_session_id: self.depart.info.booking.sessionId,
						trajets: {}
                    };
					data.trajets[self.depart.id_trajet] = self.depart.id_trajet;
                    if(self.depart.info.xbooking){
                        data.xfrom_salles_id = self.depart.info.xbooking.salleId;
                        data.xdepart_blocking_id = self.depart.info.xbooking.id;
                        data.xfrom_depart_session_id = self.depart.info.xbooking.sessionId;
                    }
                    if(self.arrive){
                        data.return_salles_id = self.arrive.info.booking.salleId;
                        data.arrive_blocking_id = self.arrive.info.booking.id;
                        data.from_arrive_session_id = self.arrive.info.booking.sessionId;
						//data.trajets[self.arrive.id_trajet] = self.arrive.id_trajet;
						if(self.arrive.info.xbooking){
                            data.xreturn_salles_id = self.arrive.info.xbooking.salleId;
                            data.xarrive_blocking_id = self.arrive.info.xbooking.id;
                            data.xfrom_arrive_session_id = self.arrive.info.xbooking.sessionId;
                        }
                    }

                    var request = {method: 'GET', url: url, params: data};

                    return self.$send(request, function (_response) {
                        self.reservations = _response.data.reservations;
                        console.log(self)
                    });
                },
        }
    }
})
})