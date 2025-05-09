// Get the Sidebar
var mySidebar = document.getElementById("mySidebar");

// Get the DIV with overlay effect
var overlayBg = document.getElementById("myOverlay");

// Toggle between showing and hiding the sidebar, and add overlay effect
function w3_open() {
  if (mySidebar.style.display === 'block') {
    mySidebar.style.display = 'none';
    overlayBg.style.display = "none";
  } else {
    mySidebar.style.display = 'block';
    overlayBg.style.display = "block";
  }
}

// Close the sidebar with the close button
function w3_close() {
  mySidebar.style.display = "none";
  overlayBg.style.display = "none";
}

var dataContainer = null;
var dataFromServer = null;

dataContainer = document.getElementById('data-container');
dataFromServer = JSON.parse(dataContainer.getAttribute('data-server-data'));

console.log(dataFromServer);

/*
const xValues = dataFromServer.map(item => item.timestamp);
const yValues = dataFromServer.map(item => item.level);

new Chart("myChart", {
  type: "line",
  data: {
    labels: xValues, // Timestamp sebagai sumbu X
    datasets: [{
      label: "Level",
      fill: false,
      lineTension: 0,
      backgroundColor: "rgba(0,0,255,1.0)",
      borderColor: "rgba(0,0,255,0.1)",
      data: yValues // Level sebagai sumbu Y
    }]
  },
  options: {
    legend: {display: true},
    scales: {
      xAxes: [{
        type: 'time', // Gunakan time axis untuk timestamp
        time: {
          unit: 'minute', // Atur unit waktu yang sesuai, misalnya 'minute', 'hour', dll.
        },
        ticks: {
          autoSkip: true,
          maxTicksLimit: 10
        }
      }],
      yAxes: [{
        ticks: {min: Math.min(...yValues), max: Math.max(...yValues)},
        scaleLabel: {
          display: true,
          labelString: 'Level'
        }
      }]
    }
  }
});*/