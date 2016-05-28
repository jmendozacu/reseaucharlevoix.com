/**
 * Created by hungnt on 4/7/16.
 */
var app = angular.module('Toolbox',['restmod','ngAnimate','ngSanitize'])
    .run(['Product', function(Product){

    }])
var MainController = function($scope, Product, $http, $httpParamSerializer){
    
    showLoad();
    $scope.message = '';
    $scope.state = 1;
    $scope.isLoading = false;
    $scope.currentSalles = [];
    $scope.fromSieges = [];
    $scope.returnSieges = [];
    $scope.xfromSieges = [];
    $scope.xreturnSieges = [];

    $scope.ngDepartDate = angular.element('#fromDate').pickadate('picker');
    $scope.ngArriveDate = angular.element('#returnDate').pickadate('picker');


    

    $scope.placesD = [
        {id: null, label: select_depart_station},
        {id:'mal', label: "Malbaie"},
        {id:'sti', label: "Saint-Iréné"},
        {id:'sjo', label: "Les eboulements"},
        {id:'bsp', label: "Baie-Saint-Paul"},
        {id:'prs', label: "Petites rivière Saint-Francois"},
        {id:'mas', label: "Le massif"},
        {id:'cap', label: "Cap tourmente"},
        {id:'bsa', label: "Basilique Sainte-Anne"},
        {id:'mmo', label: "Chutes Montmorency"},
    ];
    $scope.placesA = [
        {id: null, label: select_arrive_station},
        {id:'mal', label: "Malbaie"},
        {id:'sti', label: "Saint-Iréné"},
        {id:'sjo', label: "Les eboulements"},
        {id:'bsp', label: "Baie-Saint-Paul"},
        {id:'prs', label: "Petites rivière Saint-Francois"},
        {id:'mas', label: "Le massif"},
        {id:'cap', label: "Cap tourmente"},
        {id:'bsa', label: "Basilique Sainte-Anne"},
        {id:'mmo', label: "Chutes Montmorency"},
    ];
    $scope.qtys = [
        0,1,2,3,4,5,6,7,8,9
    ]

    $scope.rOas = [
        {id:'ret', label: round_trip}, {id:'all', label: one_way},
    ];
   
   

    $scope.bag =0;
    if(typeof salles != 'undefined'){

        $scope.salles = JSON.parse(salles);   
    }
    
    
    $scope.originPlaces = _.clone($scope.placesD);
    $scope.destinationPlaces = _.clone($scope.placesA);


    
    if(typeof sROa != 'undefined' && sROa.length > 0){
        $scope.rOa = _.find($scope.rOas, {id: sROa});
    }else{
        $scope.rOa = $scope.rOas[0];
    }

    if(typeof sOriginCity != 'undefined' && sOriginCity.length > 0){
         $scope.originCity = _.find($scope.originPlaces, {id: sOriginCity});
    }else{
        $scope.originCity = $scope.originPlaces[0];
    }

    if(typeof sDestinationCity != 'undefined' && sDestinationCity.length > 0){
         $scope.destinationCity = _.find($scope.destinationPlaces, {id: sDestinationCity});
    }else{
        $scope.destinationCity = $scope.destinationPlaces[0];
    }
    if(typeof sFromDate != 'undefined' && sFromDate.length > 0){

            $scope.fromDate = sFromDate;
        if(!$scope.ngDepartDate.get('select')){
            $scope.ngDepartDate.set('select', new Date(sFromDate));
        }
        
    }
    if(typeof sReturnDate != 'undefined' && sReturnDate.length > 0){
            $scope.returnDate = sReturnDate;
        if(!$scope.ngArriveDate.get('select')){
            $scope.ngArriveDate.set('select', new Date(sReturnDate));
        }
    }
    $scope.adult = sAdult;
    $scope.youth = sYouth;
    $scope.juvenile = sJuvenile;
    $scope.senior = sSenior;
   
    $scope.$watch('destinationCity', function(data){
         if(data.id){
             $scope.originPlaces = _.clone($scope.placesD);
             _.remove($scope.originPlaces,{id: data.id})
        }
        $scope.$emit('searchAvailableDates',data);
    })
    $scope.$watch('originCity', function(data){
        if(data.id){
             $scope.destinationPlaces = _.clone($scope.placesA);
             _.remove($scope.destinationPlaces,{id: data.id})
        }
        $scope.$emit('searchAvailableDates',data);
    })

    $scope.$watch('rOa', function(data){
        if(data.id == 'all'){
            $scope.returnDate = '';
        }
        $scope.$emit('searchAvailableDates',data);
    })
    $scope.$on('searchAvailableDates', function(evt,data){
        if(!$scope.originCity || !$scope.destinationCity.id || !$scope.destinationCity.id  || !$scope.originCity.id){
            return;
        }
        $scope.tickets = [];
        $scope.getAvailableDates();
    });
    $scope.canSearch = function () {
        return $scope.originCity.id && $scope.destinationCity && 
         $scope.ngDepartDate &&  $scope.ngDepartDate.get('select');
    }
    $scope.getDate = function(type, property){
        var selected = angular.element('#'+type+'Date').pickadate('picker').get('select');
        if(!selected){
            return '';
        };
        if(property == 'day'){
            return days[selected[property]];
        }
        if(property == 'month'){
            return months[selected[property]];
        }
        return selected[property];
    }
    $scope.isState = function(state){
        return $scope.state == state;
    }
    $scope.goState = function(state){
        $scope.state = state;
    }

    $scope.selectTicket = function(ticket){
        $scope.goState(3);
        $scope.curentTicket = ticket;
        $scope.curentTicket.$getReservations();
        $scope.currentSalles = [];
        $scope.getSalle('from',ticket.depart.info.booking);
        if(ticket.depart.info.xbooking){
            $scope.getSalle('xfrom',ticket.depart.info.xbooking);
        }
        if(ticket.arrive){
            $scope.getSalle('return',ticket.arrive.info.booking);
            if(ticket.arrive.info.xbooking){
                $scope.getSalle('xreturn',ticket.arrive.info.xbooking);
            }
        }

        $scope.currentTrain = $scope.currentSalles[0];
        console.log($scope.currentSalles)
    };
    $scope.getSalle = function(type, bookmeData){
            if(bookmeData.salleId){
                var salle = _.find($scope.salles, {entity_id:bookmeData.salleId})
                salle = _.clone(salle);
                salle.type = type;
                salle.booking = bookmeData;
                $scope.currentSalles.push(salle)
            }
    }

    // var defaultSearch = {
    //         'ra' : $scope.rOa.id,
    //         'from': $scope.fromDate,
    //         'return': $scope.returnDate,
    //         'origin': $scope.originCity,
    //         'destination': $scope.destinationCity,
    //         'storeId': storeId
    // };

    $scope.getPrice = function(direction,ticket){
        if(direction == null){
            var departP = $scope.getPrice('depart',ticket)*1;
            var arriveP = $scope.rOa.id=='ret'?$scope.getPrice('arrive',ticket)*1:0;
            return  departP + arriveP;
        }
        var adultPrice = $scope.adult * _.toNumber(ticket[direction][$scope.rOa.id+'_2664']);
        var youthPrice = $scope.youth * _.toNumber(ticket[direction][$scope.rOa.id+'_1825']);
        var juvenilePrice = $scope.juvenile * _.toNumber(ticket[direction][$scope.rOa.id+'_0307']);
        var seniorPrice = $scope.senior * _.toNumber(ticket[direction][$scope.rOa.id+'_6599']);
        if(ticket[direction].info.xbooking){
            //adultPrice += $scope.adult * _.toNumber(ticket[direction][$scope.rOa.id+'_2664']);
            //youthPrice += $scope.youth * _.toNumber(ticket[direction][$scope.rOa.id+'_1825']);
            //juvenilePrice += $scope.juvenile * _.toNumber(ticket[direction][$scope.rOa.id+'_0307']);
            //seniorPrice += $scope.senior * _.toNumber(ticket[direction][$scope.rOa.id+'_6599']);
        }
        return adultPrice + youthPrice + juvenilePrice + seniorPrice;
    }

    $scope.sumTicket = function(){
        var qty = $scope.adult*1  + $scope.youth*1 +  $scope.juvenile*1  + $scope.senior*1;
        if($scope.curentTicket.arrive.info.xbooking){
            qty *=2;
        }
        return qty;
    }

    $scope.countTicket = function(){
        var qty = $scope.adult*1  + $scope.youth*1 +  $scope.juvenile*1  + $scope.senior*1;
        return qty;
    }

    $scope.getSeats = function(sieges){
        var seats = '';
        _.each(sieges, function(siege){
            seats+='s'+siege.entity_id+',';
        })
        return seats;
    };


    $scope.getAvailableDates = function () {
        var origin = $scope.originCity.id;
        var destination = $scope.destinationCity.id;
        var rOa = $scope.rOa.id;
        $scope.isLoading = true;
        var dateAvailableStore = JSON.parse(localStorage.getItem('dateAvailableStore'));
        if(dateAvailableStore && dateAvailableStore[origin+rOa+destination]){
            return $scope.processAvailableDate(dateAvailableStore[origin+rOa+destination],origin,rOa,destination);
        }
        var url = URL + 'toolbox-designer/index/getAvailableDates';
        $http.get(url, {params: {
            'ra':  rOa,
            'origin': origin,
            'destination':destination,
        }}).success(function (data,status) {
            if(status == 200){
                $scope.processAvailableDate(data,origin,rOa,destination);
            }
        });
    };
    $scope.processAvailableDate = function(data, origin,rOa,destination){
        var dateAvailableStore = JSON.parse(localStorage.getItem('dateAvailableStore'));
        if(!dateAvailableStore){
            dateAvailableStore = {};
        }
        dateAvailableStore[origin+rOa+destination] = data;
        localStorage.setItem('dateAvailableStore',JSON.stringify(dateAvailableStore));
        $scope.isLoading = false;
        if (data.depart.length == 0) {
            $scope.message = no_result_found;
            return;
        }
        var date = data.depart[0];
        $scope.message = you_can_select_ticket_from + " "+ date[0]+'/'+(date[1]+1)+'/'+date[2];
        var available4Depart = [true];
        var available4Arrive = [true];
        _.each(data.depart, function(date){
            available4Depart.push(date);
        })
        _.each(data.arrive, function(date){
            available4Arrive.push(date);
        })
        var pickerD = jQuery('#fromDate').pickadate('picker');
        var pickerA = jQuery('#returnDate').pickadate('picker');
        var available4ArriveClone = _.clone(available4Arrive);
        var available4DepartClone = _.clone(available4Depart);
        available4ArriveClone.splice(0,1);
        available4DepartClone.splice(0,1);

        if(!_.isEqual(available4ArriveClone,pickerA.get('disable'))){
            pickerA.set('disable', available4Arrive);
        }
        if(!_.isEqual(available4DepartClone,pickerD.get('disable'))){
            pickerD.set('disable', available4Depart);
        }
        if($scope.rOa.id == 'ret'){
            pickerA.set('view', new Date(data.arrive[0]));
        }
        pickerD.set('view', new Date(data.depart[0]));
    };

    
    $scope.search = function () {
        //hideLoad();
        $scope.goState(2);
        $scope.isLoading = true;
        
        
        var dataSearch = {
                'ra' : $scope.rOa.id,
                'from': angular.element('#fromDate').val(),
                'return': angular.element('#returnDate').val(),
                'origin': $scope.originCity.id,
                'destination':$scope.destinationCity.id,
                'storeId': storeId,
                'adult': $scope.adult,
                'youth': $scope.youth,
                'juvenile': $scope.juvenile,
                'senior': $scope.senior,
        }
        
        if(mode == 'lite'){
            window.location.href = URL_X +'?'+ $httpParamSerializer(dataSearch);
            return;
        }

         Product.$search(dataSearch).$then(function (res) {
            var resultLabel = result;
            if(res.length>1){
                resultLabel = results;
            }
            $scope.message = found + " "+ res.length+ " "+resultLabel+".";
            $scope.isLoading = false;
            hideLoad();
            $scope.tickets = res;

                    }, function (err) {
            $scope.isLoading = false;
            hideLoad();
                        console.log(err)
                    })
    }
    //if($scope.originCity){
    //    $scope.search(defaultSearch);
    //}
    $scope.canPrev = function () {
        var index = _.indexOf($scope.currentSalles, $scope.currentTrain);
        if(index != 0){
            return true;
        }
        return false;
    }
    $scope.canNext = function () {
        var index = _.indexOf($scope.currentSalles, $scope.currentTrain);
        if(index != $scope.currentSalles.length -1){
            if($scope.isEnoughedSeat($scope.currentTrain)){
                return true;
            }
            return false;
        }
        return false;
    }
    $scope.isEnoughedSeat =function (train) {
        if(!train) return;
        return _.size(train.selected) == $scope.countTicket()
    }
    $scope.canAdd = function () {
        var index = _.indexOf($scope.currentSalles, $scope.currentTrain);
        if(index == $scope.currentSalles.length -1 && $scope.isEnoughedSeat($scope.currentTrain)){
            return true;
        }
        return false;
    }
    if($scope.canSearch()){
        $scope.search();
    }
    $scope.navTrain= function(type){
        var index = _.indexOf($scope.currentSalles, $scope.currentTrain);
        console.log(index);
        if(index == -1) return;
        if(type == 'next'){
            $scope.currentTrain = $scope.currentSalles[index+1]
            return;
        }
        $scope.currentTrain = $scope.currentSalles[index-1]
    }
    $scope.getStyleSiege = function(salle, siege){
        var color = 'grey';
        var cursor = 'none';
        if(_.isObject($scope.curentTicket.reservations)){
            color = 'green';
            cursor = 'pointer';
            var cItem = _.find($scope.curentTicket.reservations[salle.type].reservations, {siege_id : siege.entity_id});
            var nItem = _.find($scope.curentTicket.reservations[salle.type].reservationsNon, {siege_id : siege.entity_id});
            if(cItem){
                color = 'red';
                cursor = 'none';
            }
            if(nItem){
                color = 'yellow';
            }
        }
        var res = 'background-color: '+color+';cursor:'+cursor+';left:'+siege.posx +'px; top:'+siege.posy+'px';
        return res;
    }
    $scope.selectSiege = function(salle, siege){
        var cItem = _.find($scope.curentTicket.reservations[salle.type].reservations, {siege_id : siege.entity_id});
        if(cItem){
            alert('Cannot select this seat');
            return;
        }

        var indexItem;
        var item = _.find($scope[salle.type+'Sieges'], function(Fseige, index){
            if(Fseige.entity_id == siege.entity_id){
                indexItem= index;
                return true;
            }

            return false;
        });
        if(!item){
            if(_.size($scope[salle.type+'Sieges']) >= $scope.countTicket()){
                alert('You have just only ordered '+$scope.countTicket()+' seat(s)');
                return;
            }
            $scope[salle.type+'Sieges'].push(siege);
            if(!$scope.currentTrain.selected){
                $scope.currentTrain.selected = [];
            }
            $scope.currentTrain.selected.push(siege)
            angular.element('#'+salle.type+siege.entity_id).text("✓");
            return;
        }
        $scope[salle.type+'Sieges'].splice(indexItem, 1);
        $scope.currentTrain.selected.splice(indexItem, 1);
        angular.element('#'+salle.type+siege.entity_id).text("");
    }
    $scope.resetSiege = function(){
        _.each($scope.fromSieges, function (siege) {
            angular.element('#from'+siege.entity_id).text("");    
        });
        _.each($scope.returnSieges, function (siege) {
            angular.element('#return'+siege.entity_id).text("");    
        });

         _.each($scope.xfromSieges, function (siege) {
            angular.element('#xfrom'+siege.entity_id).text("");    
        });
        _.each($scope.xreturnSieges, function (siege) {
            angular.element('#xreturn'+siege.entity_id).text("");    
        });

        $scope.fromSieges = [];
        $scope.returnSieges = [];
        $scope.xfromSieges = [];
        $scope.xreturnSieges = [];
        $scope.currentTrainIndex = 0;
    };

    var offset = jstz.determine().name();

    $scope.processBookingParam = function(dataPost, typeDirection, bookingPrefix){
        var bookingData  = $scope.curentTicket[typeDirection].info[bookingPrefix+'booking'];
        var options  = bookingData.customOptions;
        var optionData  = {};
        var sessionId = bookingData.sessionId;
        _.each(options, function(option){
            if(option.type == "multidate_type"){
                optionData[option.option_id] = {val:{}};
                optionData[option.option_id]['val']['value'] = sessionId+'#';
                optionData[option.option_id]['val']['offset'] = offset;
                return;
            }
            if(option.type == "selectionsiege_type"){
                var typeSiege = 'from';
                if(typeDirection == 'arrive'){
                    typeSiege = 'return';
                }
                optionData[option.option_id] = {val: $scope.getSeats($scope[bookingPrefix+typeSiege+'Sieges'])};
                return;
            }
            optionData[option.option_id] = '';
        });
        var data = {
            id: bookingData.id,
            options : optionData,
            //qty : $scope.countTicket(),
            qty : 1,
        };
        dataPost[bookingPrefix+ typeDirection] = data;

    };
    $scope.processSimplesParam = function(simples, skuIndex){
        var types = ['depart'];
		var end = "_1";
        if($scope.rOa.id == 'ret'){
            types.push('arrive');
        }
		if(skuIndex == '2_'){
			end = "_2";
		}
		
        _.each(types, function(type){
            if($scope.youth > 0){
                simples[$scope.curentTicket[type]['sku_'+skuIndex+'1825']] = {
					'qty' :$scope.youth,
					'price' :$scope.curentTicket[type][$scope.rOa.id+'_1825'+end]
				};
            }if($scope.adult > 0){
                simples[$scope.curentTicket[type]['sku_'+skuIndex+'2664']] = {
					'qty' :$scope.adult,
					'price' :$scope.curentTicket[type][$scope.rOa.id+'_2664'+end]
				};
            }if($scope.senior > 0){
                simples[$scope.curentTicket[type]['sku_'+skuIndex+'6599']] = {
					'qty' :$scope.senior,
					'price' :$scope.curentTicket[type][$scope.rOa.id+'_6599'+end]
				};
            }
            if($scope.juvenile > 0){
                simples[$scope.curentTicket[type]['sku_'+skuIndex+'0307']] = {
					'qty' : $scope.juvenile,
					'price' :$scope.curentTicket[type][$scope.rOa.id+'_0307'+end]
				};
            }
        });

    };

    $scope.add = function () {
        var dataPost = {
			trajets:{}
		};
        var simples = {};

		if(!$scope.curentTicket){
			return;
		}
		dataPost.station = 1;
		//dataPost.trajets['depart'] = $scope.curentTicket.depart.id_trajet;
        if($scope.curentTicket.depart.info.xbooking){
            $scope.processBookingParam(dataPost, 'depart', 'x');
            $scope.processSimplesParam(simples, '2_');
			dataPost.station = 2;
        }
        if($scope.curentTicket.arrive){
			dataPost.trajets['arrive'] = $scope.curentTicket.arrive.id_trajet;
            $scope.processBookingParam(dataPost, 'arrive', '');
            if($scope.curentTicket.arrive.info.xbooking){

                $scope.processBookingParam(dataPost, 'arrive', 'x');
            }
        }
        $scope.processBookingParam(dataPost, 'depart', '');
        $scope.processSimplesParam(simples, '');
        if($scope.bag > 0){
            dataPost.bag = {};
            dataPost.bag[Bag] = $scope.bag;
        }
        dataPost.simples = simples;
		
        $scope.post(dataPost);

    };
    $scope.post = function(data){
        $scope.isLoading = true;
        $scope.goState(4);

        $http.post(URL + 'toolbox-designer/index/add', $httpParamSerializer(data),
            {
                headers: {'Content-Type': 'application/x-www-form-urlencoded'}
            }).then(function (res) {
                if (res.data.success) {
                    window.location.href = URL+'checkout/cart';
                    //$scope.successedAddToCart = true;
                    //angular.element('.header-minicart').html(res.data.cart)
                    //angular.element('#header-cart').html(res.data.cartItems)
                }
                if (!res.data.success) {
                    $scope.isLoading = false;
                    alert(res.data.message);
                    $scope.goState(3);
                }
            }, function (err) {
                console.log(err)
                $scope.isLoading = false;
                $scope.goState(3);
            })
    }
};
app.controller('MainController', MainController)
 angular.element(document).ready(function() {
      angular.bootstrap(document, ['Toolbox']);
    });
