
/**
 * Page stats
 *
 */

if (document.querySelector("#knytify.knytify-stats")) {

    var data = {
        labels: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul"],
        datasets: [{
            label: "Dataset #1",
            backgroundColor: "rgba(255,99,132,0.2)",
            borderColor: "rgba(255,99,132,1)",
            borderWidth: 2,
            hoverBackgroundColor: "rgba(255,99,132,0.4)",
            hoverBorderColor: "rgba(255,99,132,1)",
            data: [65, 59, 20, 81, 56, 55, 40],
        }]
    };

    var options = {
        maintainAspectRatio: false,
        scales: {
            y: {
                stacked: true,
                grid: {
                    display: true,
                    color: "rgba(255,99,132,0.2)"
                }
            },
            x: {
                grid: {
                    display: false
                }
            }
        }
    };

    new Chart('chart-utm', {
        type: 'bar',
        options: options,
        data: data
    });

    // const data = [
    //     {
    //         key: "Cumulative Return",
    //         values: [
    //             {
    //                 "label": "B Label",
    //                 "value": 0
    //             },
    //             {
    //                 "label": "C Label",
    //                 "value": 32.807804682612
    //             },
    //             {
    //                 "label": "D Label",
    //                 "value": 196.45946739256
    //             },
    //             {
    //                 "label": "E Label",
    //                 "value": 0.19434030906893
    //             },
    //         ]
    //     }
    // ]

    // nv.addGraph(function () {
    //     var chart = nv.models.discreteBarChart()
    //         .x(function (d) { return d.label })
    //         .y(function (d) { return d.value })
    //         .staggerLabels(true)

    //     d3.select('#chart-utm svg')
    //         .datum(data)
    //         .call(chart);


    //     return chart;
    // });

}
