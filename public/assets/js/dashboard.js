/**
 * Custom
 */

'use strict';
$.ajaxSetup({
    headers: {
        'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
    }
});

var options = {
    series: [44, 55, 13, 43, 22],
    chart: {
        width: 380,
        height: 250,
        type: 'pie',
    },
    labels: ['Team A', 'Team B', 'Team C', 'Team D', 'Team E'],
    responsive: [{
        breakpoint: 480,
        options: {
            chart: {
                width: 200
            },
            legend: {
                position: 'bottom'
            }
        }
    }]
};

var optionsASD = {

    chart: {
        height: 350,
        type: 'bar',
        zoom: {
            enabled: true
        }
    },
    series: [{
        data: [58257, 42712, 26738, 57973, 50764, 13627]
    }],
    dataLabels: {
        enabled: false
    },
    stroke: {
        curve: 'straight'
    },
    title: {
        text: 'Günlük Aksesuar Ciro Grafiği',
        align: 'left'
    },
    grid: {
        row: {
            colors: ['#f3f3f3', 'transparent'], // takes an array which will be repeated on columns
            opacity: 0.5
        },
    },
};

var chart = new ApexCharts(document.querySelector("#chart"), optionsASD);
chart.render();

var dailyCover = new ApexCharts(document.querySelector("#dailyCover"), options);
dailyCover.render();

var phoneChart = new ApexCharts(document.querySelector("#phoneChart"), options);
phoneChart.render();

var technicalChart = new ApexCharts(document.querySelector("#technicalChart"), options);
technicalChart.render();

var totalAylik = new ApexCharts(document.querySelector("#totalAylik"), optionsASD);
totalAylik.render();



var newChartOptions = {
    series: [
        {
            name: 'Aksesuar',
            group: 'budget',
            data: [44000, 55000, 41000, 67000, 22000]
        },
        {
            name: 'Telefon',
            group: 'budget',
            data: [48000, 50000, 40000, 65000, 25000]
        },
        {
            name: 'Kaplama',
            group: 'budget',
            data: [13000, 36000, 20000, 8000, 13000]
        },
        {
            name: 'Teknik Servis',
            group: 'budget',
            data: [20000, 40000, 25000, 10000, 12000]
        }
    ],
    chart: {
        type: 'bar',
        height: 700,
        stacked: true,
    },
    stroke: {
        width: 1,
        colors: ['#fff']
    },
    dataLabels: {
       /* formatter: (val) => {
            return val / 1000 + 'K'
        }

        */
    },
    plotOptions: {
        bar: {
            horizontal: true
        }
    },
    xaxis: {
        categories: [
            'Online advertising',
            'Sales Training',
            'Print advertising',
            'Catalogs',
            'Meetings'
        ],
        labels: {
          /*  formatter: (val) => {
                return val / 1000 + 'K'
            } */
        }
    },
    fill: {
        opacity: 1,
    },
    colors: ['#80c7fd', '#008FFB', '#80f1cb', '#00E396'],
    legend: {
        position: 'top',
        horizontalAlign: 'left'
    }
};

var newChart = new ApexCharts(document.querySelector("#newChart"), newChartOptions);
newChart.render();


$(document).ready(function () {
    var postNewUrl = window.location.origin + '/dashboardNewReport';   // Returns base URL (https://example.com)
    $.ajax({
        type: "GET",
        url: postNewUrl,
        encode: true,
    }).done(function (response) {

        newChart.updateOptions({
            xaxis: {
                categories: response.data.users,
            },
            series: [
                {
                    name: 'Aksesuar',
                    group: 'budget',
                    data: response.data.aksesuar,
                },
                {
                    name: 'Telefon',
                    group: 'budget1',
                    data: response.data.telefon,

                },
                {
                    name: 'Kaplama',
                    group: 'budget2',
                    data: response.data.kaplama,

                },
                {
                    name: 'Teknik Servis',
                    group: 'budget3',
                    data: response.data.teknikservis,

                }
            ]
        })
    });
});



$(document).ready(function () {


    var postUrl = window.location.origin + '/dashboardReport';   // Returns base URL (https://example.com)
    $.ajax({
        type: "GET",
        url: postUrl,
        encode: true,
    }).done(function (response) {

        /*  if (response.aksesuar.hasOwnProperty('total')) {
              chart.updateOptions({
                  series: response.aksesuar.total,
                  labels: response.aksesuar.username
              })
          }else{
              chart.updateOptions({
                  series: [0],
                  labels: ['SATIŞ YOK']
              })
          }

         */

        chart.updateOptions({
            xaxis: {
                categories: response.aksesuar.username,
            },
            series: [{
                name: "GÜNLÜK",
                data: response.aksesuar.total
            }]
        })


        if (response.phone.hasOwnProperty('total')) {
            phoneChart.updateOptions({
                series: response.phone.total,
                labels: response.phone.username
            })
        } else {
            phoneChart.updateOptions({
                series: [0],
                labels: ['SATIŞ YOK']
            })
        }


        if (response.cover.hasOwnProperty('total')) {

            dailyCover.updateOptions({
                series: response.cover.total,
                labels: response.cover.username
            })
        } else {
            dailyCover.updateOptions({
                series: [0],
                labels: ['SATIŞ YOK']
            })
        }


        if (response.technical.hasOwnProperty('total')) {

            technicalChart.updateOptions({
                series: response.technical.total,
                labels: response.technical.username
            })
        } else {
            technicalChart.updateOptions({
                series: [0],
                labels: ['SATIŞ YOK']
            })
        }


        totalAylik.updateOptions({
            xaxis: {
                categories: response.dates,
            },
            series: [{
                name: "AYLIK",
                data: response.alldata.total
            }]
        });
        });

    });

