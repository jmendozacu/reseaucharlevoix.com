var DateFormatLang = new Array();
DateFormatLang.Populate = function (F, D, C, B, A, E) {
    DateFormatLang[F] = DateFormatLang.Populate.arguments
};
DateFormatLang.Populate("fr_CA", "%m/%d/%Y", ["Janvier", "Fevrier", "Mars", "Avril", "Mai", "Juin", "Juillet", "Aout", "Septembre", "Octobre", "Novembre", "Decembre"], ["Jan", "Fev", "Mar", "Avr", "Mai", "Jun", "Jui", "Aou", "Sep", "Oct", "Nov", "Dec"], ["Dimanche", "Lundi", "Mardi", "Mercredi", "Jeudi", "Vendredi", "Samedi"], ["Dim", "Lun", "Mar", "Mer", "Jeu", "Ven", "Sam"]);
DateFormatLang.Populate("en", "%m/%d/%Y", ["January", "February", "March", "April", "May", "June", "July", "August", "September", "October", "November", "December"], ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul", "Aug", "Sep", "Oct", "Nov", "Dec"], ["Sunday", "Monday", "Tuesday", "Wednesday", "Thursday", "Friday", "Saturday"], ["Sun", "Mon", "Tue", "Wed", "Thu", "Fri", "Sat"]);
DateFormatLang.Populate("id", "%d/%m/%Y", ["Januari", "Februari", "Maret", "April", "Mei", "Juni", "Juli", "Agustus", "September", "Oktober", "Nopember", "Desember"], ["Jan", "Feb", "Mar", "Apr", "Mei", "Jun", "Jul", "Agu", "Sep", "Okt", "Nov", "Des"], ["Minggu", "Senin", "Selasa", "Rabu", "Kamis", "Jumat", "Sabtu"], ["Ming", "Sen", "Sel", "Rab", "Kam", "Jum", "Sab"]);
Date.prototype.getDayOfYear = function () {
    var A = new Date(this.getFullYear(), 0, 1);
    return Math.ceil((this - A) / 86400000)
};
Date.prototype.getWeekOfYear = function () {
    var A = new Date(this.getFullYear(), 0, 1);
    return Math.ceil((((this - A) / 86400000) + A.getDay()) / 7)
};
Date.prototype.getWeekOfMonth = function () {
    return Math.ceil((this.getDate() + (6 - this.getDay())) / 7)
};
var DateFormat = Class.create({
    defaultLang: "fr",
    defaultFormat: "%m/%d/%Y",
    property: {
        lang: "en",
        utc: false,
        format: "%m/%d/%Y",
        months: [],
        monthsAbbr: [],
        days: [],
        daysAbbr: []
    },
    initialize: function (A) {
        A = $H(A);
        var B = A.get("lang") || this.defaultLang;
        if (typeof DateFormatLang[B] == "undefined") {
            alert('Can not find language "' + B + '" configuration for DateFormat.\nUsing default language:"' + this.defaultLang + '"');
            B = this.defaultLang
        }
        this.oDate = null;
        this.hDate = null;
        this.property.lang = B;
        this.property.utc = A.get("utc") || false;
        this.property.format = A.get("format") || DateFormatLang[B][1];
        this.property.months = DateFormatLang[B][2];
        this.property.monthsAbbr = DateFormatLang[B][3];
        this.property.days = DateFormatLang[B][4];
        this.property.daysAbbr = DateFormatLang[B][5]
    },
    valid: function (A, B) {
        return
    },
    compare: function (A, B) {
        return
    },
    toFormat: function (E) {
        E = E ? E : this.property.format;
        var C = "",
            A = "",
            B = this;
        var D = E;
        E.scan(/%[a-zA-Z%]/, function (F) {
            C = B.formatCallback(F[0]);
            A = new RegExp(F[0]);
            D = D.replace(A, C)
        });
        return D
    },
    getDate: function (A, B) {
        this.setDate(A);
        return this.toFormat(B)
    },
    setUTC: function (A) {
        return this.property.utc = ((A === false || A == 0) ? false : true)
    },
    setDate: function (A, C) {
        if (!A && this.hDate) {
            return
        }
        var E = $H(A);
        if (E.get("hour")) {
            E.set("hours", E.get("hour"))
        }
        if (E.get("minute")) {
            E.set("minutes", E.get("minute"))
        }
        if (E.get("second")) {
            E.set("seconds", E.get("second"))
        }
        var D = new Date();
        var B = false;
        if (E.get("format")) {
            this.property.format = E.get("format")
        }
        if (E.get("time") && E.get("time") > 0) {
            D.setTime(E.get("time"));
            B = true
        } else {
            E.set("time", D.getTime())
        } if (E.get("year") && E.get("year") > 0) {
            D.setFullYear(E.get("year"))
        } else {
            E.set("year", D.getFullYear())
        } if (E.get("month") && E.get("month") > 0) {
            D.setMonth(this.unTZ(E.get("month")) - 1)
        }
        E.set("month", D.getMonth());
        if (E.get("date") && E.get("date") > 0) {
            D.setDate(E.get("date"))
        } else {
            E.set("date", D.getDate())
        }
        if (E.get("hours") && E.get("hours") > 0) {
            D.setHours(this.unTZ(E.get("hours")))
        } else {
            E.set("hours", D.getHours())
        } if (E.get("minutes") && E.get("minutes") > 0) {
            D.setMinutes(this.unTZ(E.get("minutes")))
        } else {
            E.set("minutes", D.getMinutes())
        } if (E.get("seconds") && E.get("seconds") > 0) {
            D.setSeconds(this.unTZ(E.get("seconds")))
        } else {
            E.set("seconds", D.getSeconds())
        } if (E.get("milliseconds") && E.get("milliseconds") > 0) {
            D.setMilliseconds(this.unTZ(E.get("milliseconds")))
        } else {
            E.set("milliseconds", D.getMilliseconds())
        }
        E.set("dayofweek", D.getDay());
        this.oDate = D;
        this.hDate = E;
        if (C == "hash") {
            return E
        } else {
            if (C == "object") {
                return D
            } else {
                return
            }
        }
    },
    toLocaleDateString: function (A) {
        this.setDate(A);
        return this.oDate.toLocaleDateString()
    },
    toString: function (A) {
        this.setDate(A);
        return this.oDate.toString()
    },
    toJSON: function (A) {
        this.setDate(A);
        return this.oDate.toJSON()
    },
    toObject: function (A) {
        this.setDate(A);
        return this.oDate
    },
    toDate: function (A, B) {
        return
    },
    formatCallback: function (G) {
        var B = this.oDate;
        var A = this.property;
        var H = A.utc && A.utc === true ? true : false;
        if (!B) {
            return G
        }
        switch (G) {
        case "%a":
            return (H ? A.daysAbbr[B.getUTCDay()] : A.daysAbbr[B.getDay()]);
        case "%A":
            return (H ? A.days[B.getUTCDay()] : A.days[B.getDay()]);
        case "%b":
        case "%h":
            return (H ? A.monthsAbbr[B.getUTCMonth()] : A.monthsAbbr[B.getMonth()]);
        case "%B":
            return (H ? A.months[B.getUTCMonth()] : A.months[B.getMonth()]);
        case "%d":
            return this.TZ(H ? B.getUTCDate() : B.getDate());
        case "%e":
            return (H ? B.getUTCDate() : B.getDate());
        case "%F":
            return B.getWeekOfMonth();
        case "%H":
            return this.TZ(H ? B.getUTCHours() : B.getHours());
        case "%I":
        case "%p":
            var C = (H ? B.getUTCHours() : B.getHours());
            if (G == "%p") {
                return (C >= 12 ? "PM" : "AM")
            }
            var D = this.TZ(C % 12);
            if (D == "00") {
                D = 12
            }
            return D;
        case "%j":
            return B.getDayOfYear();
        case "%m":
            return this.TZ(H ? B.getUTCMonth() + 1 : B.getMonth() + 1);
        case "%M":
            return this.TZ(H ? B.getUTCMinutes() : B.getMinutes());
        case "%n":
            return "<br />";
        case "%S":
            return this.TZ(H ? B.getUTCSeconds() : B.getSeconds());
        case "%u":
        case "%w":
            var E = (H ? B.getUTCDay() : B.getDay());
            if (G == "%u" && E == 0) {
                E = 7
            }
            return E;
        case "%U":
        case "%W":
            return B.getWeekOfYear();
        case "%y":
        case "%Y":
        case "%G":
        case "%C":
        case "%g":
            var F = (H ? B.getUTCFullYear() : B.getFullYear()) + "";
            if (G === "%y" || G === "%g") {
                return F.substr(2, 2)
            }
            if (G == "%C") {
                return this.TZ(Math.ceil(F / 100) + "")
            }
            return F;
        case "%Z":
            var I = (B.toString()).split(":");
            var I = (B.toString().split(":"));
            if (I[2]) {
                I = I[2].split(" ");
                I = I[1] ? I[1] : false
            } else {
                I = false
            }
            return (I ? I : "TZ:" + B.getTimezoneOffset());
        case "%z":
            return B.getTimezoneOffset();
        case "%%":
            return "&#37;";
        default:
            return G
        }
    },
    populate: function (A) {
        return DateFormatLang.Populate(A.get("lang"), (A.get("format") || DateFormatLang[this.defaultLang][1]), (A.get("months") || DateFormatLang[this.defaultLang][2]), (A.get("monthsAbbr") || DateFormatLang[this.defaultLang][3]), (A.get("days") || DateFormatLang[this.defaultLang][4]), (A.get("daysAbbr") || DateFormatLang[this.defaultLang][5]))
    },
    TZ: function (A) {
        return (A < 0 || A > 9 ? "" : "0") + A
    },
    unTZ: function (A) {
        return parseInt(A)
    }
});