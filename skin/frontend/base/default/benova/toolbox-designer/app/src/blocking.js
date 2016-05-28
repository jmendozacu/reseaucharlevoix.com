/*TODO: CHANGE TIME*/
    var from = jQuery('#fromDate');
    var fromDate, toDate;
    from.pickadate({
        today: '',
        min: new Date(),
//        clear: 'Clear selection',
        close: 'Cancel',
        weekdaysShort: ['Su', 'Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa'],
        showMonthsShort: true,
        onSet: function (context) {
//            console.log('Just set stuff:', context);
            var d = new Date(context.select);
            fromDate = d;
            console.log(d);
        }
    });
    var returnDate = jQuery('#returnDate');
    returnDate.pickadate({
        today: '',
        min: new Date(),
//        clear: 'Clear selection',
        close: 'Cancel',
        weekdaysShort: ['Su', 'Mo', 'Tu', 'We', 'Th', 'Fr', 'Sa'],
        showMonthsShort: true,
        onSet: function (context) {
//            console.log('Just set stuff:', context);
            var d = new Date(context.select);
            toDate = d;
            console.log(d);
        }
    });
    var rOaElem = jQuery('#rOa');
    rOaElem.change(function () {
        if (rOaElem.val() == 'all')
            returnDate.prop('disabled', true);
        else
            returnDate.prop('disabled', false);
    });
    /*TODO: CHANGE CITY*/
    var city = [{
        code: 'mal', city: 'Malbaie'
    }, {
        code: 'sti', city: 'Saint-Iréné'
    }, {
        code: 'sti',
        city: 'Les eboulements'
    }, {
        code: 'bsp', city: 'Baie-Saint-Paul'
    }, {
        code: 'prs', city: 'Petites rivière Saint-Francois'
    }, {
        code: 'mas',
        city: 'Le massif'
    }, {
        code: 'cap', city: 'Cap tourmente'
    }, {
        code: 'bsa', city: 'Basilique Sainte-Anne'
    }, {
        code: 'mmo',
        city: 'Chutes Montmorency'
    }];
    function getDestination(_origin) {
        var _destination;
        switch (_origin) {
            case 'mal':
                _destination = ['sti', 'sjo', 'bsp'];
                break;
            case 'sti':
                _destination = ['sjo', 'bsp', 'mal'];
                break;
            case 'sjo':
                _destination = ['bsp', 'sti', 'mal'];
                break;
            case 'bsp':
                _destination = ['prs', 'mas', 'cap', 'bsa', 'mmo', 'sjo', 'sti', 'mal'];
                break;
            case 'prs':
                _destination = ['mas', 'cap', 'bsa', 'mmo', 'bsp'];
                break;
            case 'mas':
                _destination = ['cap', 'bsa', 'mmo', 'prs', 'bsp'];
                break;
            case 'cap':
                _destination = ['bsa', 'mmo', 'mas', 'prs', 'bsp'];
                break;
                break;
            case 'mmo':
                _destination = ['bsa', 'cap', 'mas', 'prs', 'bsp'];
                break;
            case 'bsa':
                _destination = ['mmo', 'cap', 'mas', 'prs', 'bsp'];
                break;
        }
        return _destination;
    }

    _originCity = jQuery('#originCity');
    _destination = jQuery('#destinationCity');
    _originCity.change(function () {
            _destination.text('');
            var _ori = _originCity.val();
            if (_ori != '0') {
                var arrayDestination = getDestination(_ori);
                jQuery.each(arrayDestination, function (i, item) {
                    jQuery.each(city, function (_k, _v) {
                        if (_v.code == item)
                            _destination.append(jQuery('<option>', {
                                value: item,
                                text: _v.city
                            }));
                    });
                });
            } else {
                _destination.append(jQuery('<option>', {
                    value: 0,
                    text: 'Please select origin city'
                }));
            }
            console.log('Origin: ' + _ori);
        }
    );
    _destination.change(function () {
        console.log('Destination: ' + _destination.val());
    });
    /*Check btSearch*/
    var btSearch = jQuery('#btSearch');
    btSearch.click(function () {
        if (_originCity.val() == '0') {
            alert('Please select city');
            return false;
        }
        if (from.val() == '' || (returnDate.val() == '' && rOaElem.val() == 'ret')) {
            alert('Please select date');
            return false;
        }
        var url = window.urlWidget;
        var rOa = jQuery('#rOa').val();
        
        console.log('storeId:' + storeId);
        url += '?ra=' + rOa + '&from=' + fromDate + '&return=' + toDate + '&origin=' + _originCity.val() + '&destination=' + _destination.val() + '&storeId=' + storeId;
        var wResult = jQuery('#wResult');
        showLoad();
        jQuery.getJSON(url, function (json) {
            hideLoad();
            if (json.result == true) {
                openNewUrl(json.link);
            } else {
                wResult.removeClass(" none-display").addClass('y-display');
            }
        });
    });
    jQuery(window).load(function () {
        // Animate loader off screen
        hideLoad();
    });
    function showLoad() {
        jQuery(".se-pre-con").show();
    }
    function hideLoad() {
        jQuery(".se-pre-con").fadeOut("slow");
    }
    function openNewUrl(url) {
        window.location.assign(url);
    }
