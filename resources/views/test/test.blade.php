<x-app-layout>
    <div class="flex justify-center">
        <div class="container py-12">
            <canvas id="efficiencyChart"></canvas>
        </div>
    </div>
</x-app-layout>

<script>
    // Data for different input sizes and corresponding time taken
    const inputData = JSON.parse('{!! addslashes($inputData) !!}'); // Input sizes
    const timeData = JSON.parse('{!! addslashes($timeData) !!}'); // Time taken in milliseconds

    // Get the canvas element
    const ctx = document.getElementById('efficiencyChart').getContext('2d');

    // 0.001, 0.018, 0.045, 0.068, 0.108
    // Create the chart
    const efficiencyChart = new Chart(ctx, {
        type: 'line',
        data: {
            labels: inputData,
            datasets: [{
                label: 'Time taken to run the algorithm',
                data: timeData,
                borderColor: 'blue',
                backgroundColor: 'rgba(0, 0, 255, 0.2)',
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                x: {
                    title: {
                        display: true,
                        text: 'Input Size'
                    }
                },
                y: {
                    title: {
                        display: true,
                        text: 'Time (s)'
                    }
                }
            }
        }
    });
</script>