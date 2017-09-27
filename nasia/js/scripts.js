jQuery(function ($) {

    'use strict';

    // --------------------------------------------------------------------
    // PreLoader
    // --------------------------------------------------------------------

    (function () {
        $('#preloader').delay(200).fadeOut('slow');
    }());
    (function () {
        new WOW().init();
    }());
    (function () {
        $("a.youtube").YouTubePopup({ autoplay: 0, draggable: true });
    }());
    (function () {
        $('html').smoothScroll(500);
    }());
    (function () {
        $.goup({
            bottomOffset: 100,
        });
    }());
    (function () {

        var datePicker = window.datePicker = $('.date').datePicker({
            weekDays: ['So', 'Mo', 'Di', 'Mi', 'Do', 'Fr', 'Sa'],
            months: ['Januar', 'Februar', 'März', 'April', 'Mai', 'Juni',
                'Juli', 'August', 'September', 'Oktober', 'November', 'Dezember'],

            sundayBased: false,
            renderWeekNo: true,
            equalHight: true,
            weekDayClass: 'week-day', // not standard: used in template.start
            weekDayRowHead: '',
            template: {
                row: '<td class=""><span class=""{{event}} data-day=\'{"day":"{{day}}", "month":"{{month}}", "year":"{{year}}"}\'>{{day-event}}{{today}}</span></td>',
                start: function(month, year) { // rendering week days in table header
                    var options = this.options,
                        weekDayRow = '<th class="">{{day}}</th>',
                        row = [];

                    if (options.renderWeekNo) { // week number head
                        row.push(weekDayRow.replace(/{{day}}/g, options.weekDayRowHead));
                    }

                    for (var n = 0, dayOfWeek = 0; n < 7; n++) { // week days
                        dayOfWeek = n + (options.sundayBased ? 0 : (n === 6 ? -6 : 1));
                        row.push(weekDayRow.replace(/class="(.*?)"/, function($1, $2) {
                            return 'class="' + options.weekDayClass + ' ' +
                                (options.workingDays.indexOf(dayOfWeek) === -1 ?
                                    options.weekEndClass : '') + '"';
                        }).replace(/{{day}}/g, options.weekDays[dayOfWeek]));
                    }
                    return '<table class="cal-month"><thead><tr>' +
                        row.join('') +
                        '</tr></thead><tbody><tr>';
                },
                event: function(day, date, event) { // rendering events
                    var text = [],
                        uuids = [],
                        someExtra = '';

                    for (var n = 0, m = event.length; n < m; n++) {
                        event[n].text && text.push('- ' + event[n].text);

                        uuids.push(event[n]._id);

                        if (event[n].extra) { // extend functionality...
                            someExtra = event[n].extra;
                        }
                    }
                    text = text.join("\n");

                    return text ? ' title="' + text + '"' +
                    ' data-uuids=\'[' + uuids.join(', ') + ']\'' +
                    (someExtra ? ' data-name="' + someExtra + '"' : '') : '';
                },
                today: function(day, date) { // rendering today; just for fun...
                    return '<span class="today-icon">+</span>';
                },
                day: function(day, date, event) { // rendering every day
                    var length = event.length;

                    for (var n = length; n--; ) { // check if it's only a 'disabled' event
                        if (event[n].type && event[n].type === 'disabled') { // or event[n].disabled
                            length--;
                        }
                    }
                    if (length > 1) {
                        return day + '<span class="count-icon">' + length + '</span>';
                    }
                }
            }
        });


    }());
    (function () {

        function create_graph_chuan_hoa($self){

            $self.attr('disabled',true);
            var key=$self.attr('data-key');
            $('button[data-tangchuanhoa="'+key+'"]').removeClass('hide');


            $.ajax({url: "http://banhangonline88.com/nasia/data_chuan_hoa.php?key="+key, success: function(data_car){
                var current_options = {

                    chart: {
                        renderTo: '',
                        width:'1000',
                        zoomType: 'x'


                    },
                    rangeSelector: {
                        selected: 1
                    },
                    title: {
                        text: 'Mức tiêu hao nhiên liệu xe c-'+key
                    },
                    xAxis: {
                        type: 'datetime',
                        labels:{
                            formatter: function() {
                                var time = moment.unix(this.value).format("DD-MM-YYYY H:mm:ss");
                                return time;
                            },
                        }

                    },
                    yAxis: {
                        labels: {
                            formatter: function () {
                                return this.value;
                            }
                        }
                    },
                    series: [],
                    tooltip: {
                        formatter: function () {

                            var time = moment.unix(this.x).format("DD-MM-YYYY H:mm:ss");
                            return '<b>car-' + key + '</b><br/>' +
                                time+ '<br/>' +
                                this.y;
                        }
                    },
                    events:{
                        selection: function(event) {
                            // log the min and max of the primary, datetime x-axis
                            console.log(
                                Highcharts.dateFormat('%Y-%m-%d %H:%M:%S', event.xAxis[0].min),
                                Highcharts.dateFormat('%Y-%m-%d %H:%M:%S', event.xAxis[0].max)
                            );
                            // log the min and max of the y axis
                            console.log(event.yAxis[0].min, event.yAxis[0].max);
                        }
                    },
                    credits: {
                        enabled: false
                    },
                    plotOptions: {
                        series: {
                            cursor: 'pointer',
                            point: {
                                events: {

                                }
                            }
                        }
                    }
                };

                data_car=JSON.parse(data_car);
                data_car=data_car[key];

                var car = {
                    name: '',
                    type: 'spline',
                    data: []
                };
                car.name=key;
                $.each(data_car, function(index, item) {
                    car.data.push(item);
                });
                current_options.chart.renderTo='chuan_hoa_xe_'+key;
                current_options.series.push(car);

                var car_chuan_hoa = {
                    name: 'chuanhoa',
                    type: 'spline',
                    data: []
                };
                var first_item=data_car[0];
                first_item[1]=0;
                car_chuan_hoa.data.push(first_item);
                var last_item=data_car[data_car.length-1];
                last_item[1]=0;
                car_chuan_hoa.data.push(last_item);
                console.log(car_chuan_hoa);
                current_options.series.push(car_chuan_hoa);


                var stockChart=Highcharts.stockChart('chuan_hoa_xe_'+key,current_options);
                $('button[data-tangchuanhoa="'+key+'"]').click(function(){
                    tang_chuan_hoa(stockChart,car,key);
                });

            }});

        }
        function loai_bo_tin_hieu_loi($self,stockChart,car,key){

            var key=$self.attr('data-key');
            var extremes = stockChart.xAxis[0].getExtremes();
            var start = moment.unix(extremes.min).format("DD-MM-YYYY H:mm:ss");
            var end = moment.unix(extremes.max).format("DD-MM-YYYY H:mm:ss");
            console.log(start);
            console.log(end);
            var a_list=[];
            car.data.forEach(function(item, index) {
                var time=item[0];
                var value=item[1];
                if(time>=extremes.min && time<=extremes.max && value==0){
                    delete car.data[key];
                }else{
                    a_list.push(item);
                }
            });



            //console.log(car);
            //stockChart.series[0].setData(car,true);

            stockChart.series[2].setData(a_list,true);
        }
        function tang_chuan_hoa(stockChart,car,key){
            var data= car.data;
            var x=0;
            var y=0;
            var current_item=[0,0];
            car.data.forEach(function(item, index) {
                if(index>0) {
                    var time = item[0];
                    var value = item[1];
                    if (value > y) {
                        x = time;
                        y = value;
                    }
                }
            });
            stockChart.series[1].addPoint(x,y);



        }
        var da_lay_mau_from=0;
        $.ajax({url: "http://banhangonline88.com/nasia/data.php", success: function(data){

            data=JSON.parse(data);
            $.each(data, function(key, data_car) {
                var current_options = {

                    chart: {
                        renderTo: '',
                        width:'1000',
                        height:'600',
                        zoomType: 'x'

                    },
                    title: {
                        text: 'Mức tiêu hao nhiên liệu xe: c-'+key
                    },
                    rangeSelector: {
                        selected: 1
                    },
                    xAxis: {
                        type: 'datetime',
                        labels:{
                            formatter: function() {
                                var time = moment.unix(this.value).format("DD-MM-YYYY H:mm:ss");
                                return time;
                            },
                        }

                    },
                    yAxis: {
                        labels: {
                            formatter: function () {
                                return this.value;
                            }
                        }
                    },

                    series: [],
                   /* tooltip: {
                        formatter: function () {

                            var time = moment.unix(this.x).format("DD-MM-YYYY H:mm:ss");
                            return '<b>car-'+key+'</b><br/>' +
                                time+ '<br/>' +
                                this.y;
                        }
                    },*/
                    tooltip: {
                        formatter: function () {
                            var time = moment.unix(this.x).format("DD-MM-YYYY H:mm:ss");
                            var s='';
                            $.each(this.points, function () {
                                s += '<br/>' + Highcharts.numberFormat(this.y,0);
                            });

                            return '<b>car-'+key+'</b>-<b>'+time+'</b><br/><b>'+s+'</b>';
                        },
                        //pointFormat: 'Time:{time},<span style="color:{series.color}"> {series.name}</span>: <b>{point.y}</b><br/>',
                        valueDecimals: 2,
                    },
                    events:{
                        selection: function(event) {
                            // log the min and max of the primary, datetime x-axis
                            console.log(
                                Highcharts.dateFormat('%Y-%m-%d %H:%M:%S', event.xAxis[0].min),
                                Highcharts.dateFormat('%Y-%m-%d %H:%M:%S', event.xAxis[0].max)
                            );
                            // log the min and max of the y axis
                            console.log(event.yAxis[0].min, event.yAxis[0].max);
                        }
                    },
                    credits: {
                        enabled: false
                    },
                    plotOptions: {
                        series: {
                            cursor: 'pointer',
                            point: {
                                events: {

                                }
                            }
                        }
                    }
                };

                var $graph=$('<hr/><div class="row"><div class="col-md-12"><div id="graph_'+key+'"></div></div></div><div class="row"><div class="col-md-12"><button data-key="'+key+'" class="btn btn-primary chuan-hoa-xe" data-timeout="2000"  data-toggle="popover" title="Hướng dẫn" data-content="Nhấn vào đây để hệ thống sẽ sinh ra một biểu đồ đã được chuẩn hóa cấp 1 bên dưới">Chuẩn hóa cấp độ 1 xe : c-'+key+'</button> <button data-key="'+key+'" class="btn btn-primary loai-bo-tin-hieu-loi" data-toggle="popover" title="Hướng dẫn" data-content="Lựa chọn một vùng nhỏ trên biểu đồ sau đó nhấn nút này để loại bỏ xung nhiễu">chuẩn hóa cấp độ 2 Loại bỏ tin hiệu lỗi bằng tay: c-'+key+'</button><div id="chuan_hoa_xe_'+key+'"></div><div><button data-tangchuanhoa="'+key+'" class="btn btn-primary tang-chuan-hoa hide">Tăng chuẩn hóa</button></div></div></div>');
                $graph.appendTo($('#graphs'));
                $graph.find('.chuan-hoa-xe').click(function(){
                    create_graph_chuan_hoa($(this));
                });
                $graph.find('.chuan-hoa-xe').popover({
                    container: 'body',
                    trigger: 'hover'
                });
                $graph.find('.loai-bo-tin-hieu-loi').popover({
                    container: 'body',
                    trigger: 'hover'
                });
                var car3 = {
                    name: '',
                    step: true,
                    type: 'spline',
                    data: []
                };
                car3.name='c-'+key+':3';
                $.each(data_car[3], function(index, item) {
                    car3.data.push(item);
                });
                current_options.series.push(car3);
                var car5 = {
                    name: '',
                    type: 'spline',
                    step: true,
                    data: []
                };
                car5.name='c-'+key+':5';
                $.each(data_car[5], function(index, item) {
                    car5.data.push(item);
                });
                current_options.series.push(car5);
                var car6 = {
                    name: '',
                    type: 'spline',
                    step: true,
                    data: []
                };
                car6.name='c-'+key+':6';
                $.each(data_car[6], function(index, item) {
                    car6.data.push(item);
                });
                current_options.series.push(car6);

                current_options.chart.renderTo='graph_'+key;
                var stockChart=Highcharts.stockChart('graph_'+key,current_options,
                    function (stockChart) {
                        setTimeout(function () {
                            $('input.highcharts-range-selector', $(stockChart.container).parent())
                                .datepicker({
                                    dateFormat: 'DD-MM-YYYY H:mm:ss',
                                    onSelect: function () {
                                        this.onchange();
                                        this.onblur();
                                    }
                                });
                        },0);
                    }
                );
                $graph.find('.loai-bo-tin-hieu-loi').click(function(){
                    loai_bo_tin_hieu_loi($(this),stockChart,car6,key);
                });
            });

        }});
        $.datepicker.setDefaults({
            dateFormat: 'DD-MM-YYYY H:mm:ss',
            onSelect: function () {
                this.onchange();
                this.onblur();
            }
        });

    }());



    // --------------------------------------------------------------------
    // Owl Carousal
    // --------------------------------------------------------------------

    (function () {
        $("#review").owlCarousel({
            autoPlay: 3000, //Set AutoPlay to 3 seconds
            items: 2,
            itemsDesktop: [1199, 3],
            itemsDesktopSmall: [979, 3]

        });
    }());



}); // JQuery end