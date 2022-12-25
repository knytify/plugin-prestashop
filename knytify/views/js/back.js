

/**
 * Page stats
 *
 */


function loadChartUtm(data) {

    const canvas = document.getElementById('chart-utm');

    new Chart(canvas, {
        type: 'bar',
        data: {
            labels: data.labels,
            datasets: [{
                label: 'Fraud average [0, 1]',
                data: data.values,
                borderWidth: 1
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
}


const numberDisplayFormatter = function (value) {
    if (value > 1000) {
        return value / 1000 + "K";
    } else {
        return value;
    }
}

function loadChartEvolution(data) {

    const canvas = document.getElementById('chart-evolution');

    new Chart(canvas, {
        type: 'line',
        data: {
            labels: [1,2,3,4,5,6,7],
            datasets: [{
              label: 'My First Dataset',
              data: [65, 59, 80, 81, 56, 55, 40],
              fill: false,
              borderColor: 'rgb(75, 192, 192)',
              tension: 0.1
            }]
          },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });

}


function loadChartPercentage(data) {

    const canvas = document.getElementById('chart-percentage');

    new Chart(canvas, {
        type: 'doughnut',
        data: {
            labels: [
                'Red',
                'Blue',
                'Yellow'
            ],
            datasets: [{
                label: 'My First Dataset',
                data: [300, 50, 100],
                backgroundColor: [
                    'rgb(255, 99, 132)',
                    'rgb(54, 162, 235)',
                    'rgb(255, 205, 86)'
                ],
                hoverOffset: 4
            }]
        },
        options: {
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
}