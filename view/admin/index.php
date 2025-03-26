<?php
session_start();
if (!isset($_SESSION["authUser"])) {
  header("Location: ../../../IT322/login.php");
  exit();
}
// Prevent browser caching
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Pragma: no-cache");
header("Expires: 0");


include("../../dB/config.php");
include("./includes/header.php");
include("./includes/topbar.php");
include("./includes/sidebar.php");
?>

<div class="col-lg-15">
          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Books borrowed per month</h5>

              <!-- Bar Chart -->
              <canvas id="barChart" style="max-height: 550px;"></canvas>
              <script>
                document.addEventListener("DOMContentLoaded", () => {
                  new Chart(document.querySelector('#barChart'), {
                    type: 'bar',
                    data: {
                      labels: ['January', 'February', 'March'],
                      datasets: [{
                        label: 'Total books borrowed',
                        data: [65, 59, 38],
                        backgroundColor: [
                          'rgba(255, 99, 132, 0.2)',
                          'rgba(255, 159, 64, 0.2)',
                          'rgba(255, 205, 86, 0.2)',
                        //   'rgba(75, 192, 192, 0.2)',
                        //   'rgba(54, 162, 235, 0.2)',
                        //   'rgba(153, 102, 255, 0.2)',
                        //   'rgba(201, 203, 207, 0.2)'
                        ],
                        borderColor: [
                          'rgb(255, 99, 132)',
                          'rgb(255, 159, 64)',
                          'rgb(255, 205, 86)',
                        //   'rgb(75, 192, 192)',
                        //   'rgb(54, 162, 235)',
                        //   'rgb(153, 102, 255)',
                        //   'rgb(201, 203, 207)'
                        ],
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
                });
              </script>
              <!-- End Bar CHart -->
            </div>
          </div>
</div>

<div class="col-lg-6">
          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Books borrowed by genre</h5>

              <!-- Pie Chart -->
              <canvas id="pieChart" style="max-height: 400px;"></canvas>
              <script>
                document.addEventListener("DOMContentLoaded", () => {
                  new Chart(document.querySelector('#pieChart'), {
                    type: 'pie',
                    data: {
                      labels: [
                        'Red',
                        'Blue',
                        'Yellow',
                        'Red',
                        'Blue',
                        'Yellow'
                      ],
                      datasets: [{
                        label: 'My First Dataset',
                        data: [300, 50, 100, 300, 50, 100],
                        backgroundColor: [
                          'rgb(255, 99, 132)',
                          'rgb(54, 162, 235)',
                          'rgb(255, 205, 86)',
                          'rgb(255, 99, 132)',
                          'rgb(54, 162, 235)',
                          'rgb(255, 205, 86)'
                        ],
                        hoverOffset: 4
                      }]
                    }
                  });
                });
              </script>
              <!-- End Pie CHart -->

            </div>
          </div>
</div>

<div class="col-lg-6">
          <div class="card">
            <div class="card-body">
              <h5 class="card-title">Overdue Books Count (Monthly)</h5>

              <!-- Vertical Bar Chart -->
              <div id="verticalBarChart" style="min-height: 400px;" class="echart"></div>

              <script>
                document.addEventListener("DOMContentLoaded", () => {
                  echarts.init(document.querySelector("#verticalBarChart")).setOption({
                    title: {
                    //   text: 'World Population'
                    },
                    tooltip: {
                      trigger: 'axis',
                      axisPointer: {
                        type: 'shadow'
                      }
                    },
                    legend: {},
                    grid: {
                      left: '3%',
                      right: '4%',
                      bottom: '3%',
                      containLabel: true
                    },
                    xAxis: {
                      type: 'value',
                      boundaryGap: [0, 0.01]
                    },
                    yAxis: {
                      type: 'category',
                      data: ['December (2024)', 'January', 'February', 'March']
                    },
                    series: [{
                        name: 'Books borrowed',
                        type: 'bar',
                        data: [81, 65, 59, 38]
                      },
                      {
                        name: 'Books returned',
                        type: 'bar',
                        data: [79, 65, 50, 24]
                      },
                      {
                        name: 'Books overdue',
                        type: 'bar',
                        data: [2, 0, 9, 14]
                      }
                    ]
                  });
                });
              </script>
              <!-- End Vertical Bar Chart -->

            </div>
          </div>
        </div>

<?php
include("./includes/footer.php");
?>