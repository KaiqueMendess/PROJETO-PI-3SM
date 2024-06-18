window.onload = function() {
    var ctx1 = document.getElementById('chart1').getContext('2d');
    var ctx2 = document.getElementById('chart2').getContext('2d');

    var data = {
        datasets: [{
            data: [10, 20, 30, 40],
            backgroundColor: ['#ff9999', '#66b3ff', '#99ff99', '#ffcc99']
        }],
        labels: ['Red', 'Blue', 'Green', 'Yellow']
    };

    var options = {
        responsive: true,
        maintainAspectRatio: false
    };

    new Chart(ctx1, {
        type: 'pie',
        data: data,
        options: options
    });

    new Chart(ctx2, {
        type: 'doughnut',
        data: data,
        options: options
    });
};
