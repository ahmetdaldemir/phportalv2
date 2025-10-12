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
        text: 'Aylik Ciro Grafiği',
        align: 'left'
    },
    grid: {
        row: {
            colors: ['#f3f3f3', 'transparent'], // takes an array which will be repeated on columns
            opacity: 0.5
        },
    },
};


var totalAylik = new ApexCharts(document.querySelector("#totalAylik"), optionsASD);
totalAylik.render();



var newChartOptions = {
    series: [],
    chart: {
        type: 'bar',
        height: 500,
        stacked: true,
    },
    stroke: {
        width: 1,
        colors: ['#fff']
    },
    dataLabels: {
        enabled: true
    },
    plotOptions: {
        bar: {
            horizontal: true
        }
    },
    xaxis: {
        categories: [],
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


var newMonthChart = new ApexCharts(document.querySelector("#newMonthChart"), newChartOptions);
newMonthChart.render();

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
            title: {
                text: 'Gunluk Personel Grafiği',
                align: 'left'
            },
            series: [
                {
                    name: 'Aksesuar',
                    group: 'budget',
                    data: response.data.aksesuar,
                },
                {
                    name: 'Telefon',
                    group: 'budget',
                    data: response.data.telefon,

                },
                {
                    name: 'Kaplama',
                    group: 'budget',
                    data: response.data.kaplama,

                },
                {
                    name: 'Teknik Servis',
                    group: 'budget',
                    data: response.data.teknikservis,
                    visible: false // Teknik Servis verilerini kapalı olarak getir
                }
            ]
        })
    });
});


$(document).ready(function () {
    var postNewUrl = window.location.origin + '/dashboardMounthNewReport';   // Returns base URL (https://example.com)
    $.ajax({
        type: "GET",
        url: postNewUrl,
        encode: true,
    }).done(function (response) {

        newMonthChart.updateOptions({
            xaxis: {
                categories: response.data.users,
            },
            title: {
                text: 'Aylik Personel Grafiği',
                align: 'left'
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
                    visible: false
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

