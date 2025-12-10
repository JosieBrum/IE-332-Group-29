<?php
//https://www.geeksforgeeks.org/php/how-to-redirect-a-user-to-the-registration-if-he-has-not-logged-in
//https://codeswithpankaj.com/php-session-tutorial-with-login-and-logout-example
//https://www.phphelp.com/t/redirect-if-not-logged-in
//https://stackoverflow.com/questions/31031344/php-how-to-check-if-user-is-already-logged-in-and-otherwise-redirect-to-login-p
session_start(); // Start the session
// Check if the user is logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    // Redirect to login page if not logged in
    header("Location: index.php"); // Change this to your login page
    exit;
}
?>
<html>

<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@picocss/pico@2/css/pico.min.css"> <!-- input form style -->
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/css/bootstrap.min.css"/>
  <link href="https://fonts.googleapis.com/css?family=Montserrat" rel="stylesheet"/>
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css"/>

  <link rel="stylesheet" href="styles.css?v=5" />  <!-- general css style -->

  <script src="https://cdn.plot.ly/plotly-2.35.2.min.js" charset="utf-8"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js@latest/dist/chart.umd.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
  <script src="chart-utils.js"></script>
  
  <style>
    /* Remove list style from nav tabs */
    .nav-tabs {
      list-style: none;
      padding-left: 0;
    }
    .nav-tabs .nav-item {
      list-style: none;
    }
    /* Add padding at bottom to create buffer */
    #wrapper {
      padding-bottom: 50px;
    }
  </style>

  <title>Distributor Transactions</title>

</head>

<body>

  <!-- Modular Navbar -->
  <div id="navbar"></div>
  <script>
    fetch('navbar.html')
      .then(response => response.text())
      .then(data => {
        document.getElementById('navbar').innerHTML = data;
        // Highlight current page in navbar
        const links = document.querySelectorAll('#navbar .nav-links a');
        const currentPage = location.pathname.split('/').pop();
        links.forEach(link => {
          if (link.getAttribute('href') === currentPage) {
            link.classList.add('active');
          } else {
            link.classList.remove('active');
          }
        });
        // Execute the navbar script
        const scripts = document.getElementById('navbar').getElementsByTagName('script');
        for (let script of scripts) {
          eval(script.innerHTML);
        }
      });
  </script>

  <!-- Main Content -->
  <div class="main-content">
    <div id="wrapper">
      <div class="content-area">
        <div class="container-fluid">

     <!-- Page Title --> 
    <h3>Distributor Transactions</h3>

  <div class="box shadow">
    <!-- This form is used to get user input for the distributor name and date range -->
    <form action= "" onsubmit="showCustomer()">
        <div style="display: grid; grid-template-columns: 3fr 1fr 1fr auto; gap: 10px; align-items: end;">
          <input type= "text" name= "Company_name" id= "company" placeholder= "Enter a distributor name" >
          <input type="date" name="date1" id="date1" placeholder = "Enter starting date">
          <input type= "date" name ="date2"id="date2" placeholder = "Enter ending date">
          <input type= "submit" name = "submit-btn" value="Search">
        </div>
        <div id="errorMessage" class="mt-3" style="color: red; display: none;"></div>
    </form>
  </div>

  <script>
    // Load default company on page load
    window.addEventListener('DOMContentLoaded', function() {
      // Set default values
      document.getElementById("company").value = "Burns LLC";
      
      const today = new Date();
      const dbStartDate = new Date('2019-01-01');
      
      // Format date as YYYY-MM-DD in local timezone
      const year = today.getFullYear();
      const month = String(today.getMonth() + 1).padStart(2, '0');
      const day = String(today.getDate()).padStart(2, '0');
      document.getElementById("date2").value = `${year}-${month}-${day}`;
      document.getElementById("date1").value = '2019-01-01';
      
      // Trigger the search automatically
      showCustomer();
    });
    
    // Track pending requests to prevent race conditions
    let pendingRequest = null;
    let pendingRequest2 = null;
    let currentRequestId = 0;
    
    function showCustomer() {
      // Abort any pending requests
      if (pendingRequest) {
        pendingRequest.abort();
        pendingRequest = null;
      }
      if (pendingRequest2) {
        pendingRequest2.abort();
        pendingRequest2 = null;
      }
      
      // Clear previous content immediately
      document.getElementById("temp").innerHTML = "";
      
      // Clear previous error message
      const errorMessageDiv = document.getElementById("errorMessage");
      errorMessageDiv.style.display = "none";
      errorMessageDiv.textContent = "";

      // Process form inputs using a unified field list
      var feilds = ["company", "tier", "country", "continent", "date1", "date2"];
      var inputs = {};
      for (var key = 0; key < feilds.length; key++) {
        if (document.getElementById(feilds[key])) {
          if (document.getElementById(feilds[key]).value != "") {
            inputs[feilds[key]] = document.getElementById(feilds[key]).value;
          }
        }
      }

      // Get company and date values for further logic
      var str = document.getElementById("company") ? document.getElementById("company").value.trim() : "";
      var xt = document.getElementById("date1") ? document.getElementById("date1").value : "";
      var yt = document.getElementById("date2") ? document.getElementById("date2").value : "";

      // Set default dates if not provided
      const today = new Date();
      today.setHours(0, 0, 0, 0); // Reset time to midnight for accurate comparison
      
      // If no end date provided, use today
      if (!yt) {
        yt = today.toISOString().split('T')[0];
        document.getElementById("date2").value = yt;
      }
      
      // If no start date provided, use 2019-01-01 (start of database)
      if (!xt) {
        const dbStartDate = '2019-01-01';
        xt = dbStartDate;
        document.getElementById("date1").value = dbStartDate;
      }
      
      const startDate = new Date(xt);
      const endDate = new Date(yt);

      // Check if end date is before start date
      if (endDate < startDate) {
        errorMessageDiv.textContent = "The ending date cannot be before the starting date.";
        errorMessageDiv.style.display = "block";
        event.preventDefault();
        return;
      }

      // Check if either date is after today
      if (startDate > today) {
        errorMessageDiv.textContent = "The starting date cannot be after today's date.";
        errorMessageDiv.style.display = "block";
        event.preventDefault();
        return;
      }

      if (endDate > today) {
        errorMessageDiv.textContent = "The ending date cannot be after today's date.";
        errorMessageDiv.style.display = "block";
        event.preventDefault();
        return;
      }
      
      // Update inputs object with the dates (in case defaults were used)
      inputs["date1"] = xt;
      inputs["date2"] = yt;

      // This is how you join the strings into one big one.
      str = xt + "|" + yt + "|" + str;

      
      event.preventDefault();

      var xhttp;
      if (str == "") {
        document.getElementById("temp").innerHTML = "";
        return;
      }
      
      // Increment request ID for this search
      currentRequestId++;
      const thisRequestId = currentRequestId;
      
      xhttp = new XMLHttpRequest();
      pendingRequest = xhttp;

      xhttp.onload = function () {
        if (this.readyState == 4 && this.status == 200) {
          // Check if this is still the current request
          if (thisRequestId !== currentRequestId) {
            console.log("Ignoring stale request");
            return;
          }
          
          pendingRequest = null;
          var temp = JSON.parse(this.responseText);
          console.log(temp);
          
          // Validate company name against distributor list
          const companyListAll = temp["Company Name List All: "];
          const enteredCompany = document.getElementById("company").value.trim();
          
          if (companyListAll && companyListAll.length > 0 && !companyListAll.includes(enteredCompany)) {
            const errorMessageDiv = document.getElementById("errorMessage");
            errorMessageDiv.textContent = "Company '" + enteredCompany + "' is not a distributor or not found in database. Please check the spelling and try again.";
            errorMessageDiv.style.display = "block";
            document.getElementById("temp").innerHTML = "";
            return;
          }
          
          // Fetch individual shipment data from transaction_V2.php for Shipment tab
          var xhttp2 = new XMLHttpRequest();
          pendingRequest2 = xhttp2;
          xhttp2.onload = function() {
            if (this.readyState == 4 && this.status == 200) {
              // Check if this is still the current request
              if (thisRequestId !== currentRequestId) {
                console.log("Ignoring stale shipment data request");
                return;
              }
              
              pendingRequest2 = null;
              var shipmentData = JSON.parse(this.responseText);
              console.log("Individual shipment data:", shipmentData);
              buildPageWithShipmentData(temp, shipmentData, enteredCompany, thisRequestId);
            }
          };
          var url2 = "company_transactions_output.php?" + new URLSearchParams(inputs).toString();
          console.log("Fetching individual shipments:", url2);
          xhttp2.open("GET", url2, true);
          xhttp2.send();
        }
      };
      
      function buildPageWithShipmentData(temp, shipmentData, companyName, requestId) {
          // Check one more time before building
          if (requestId !== currentRequestId) {
            console.log("Ignoring stale page build");
            return;
          }
          
          // Build HTML with tabs
          
          let html = `<h3></br>${companyName}</h3>`;
          
          // Tab navigation
          html += `<ul class="nav nav-tabs mt-3" id="distributorTabs" role="tablist">`;
          html += `  <li class="nav-item">`;
          html += `    <a class="nav-link active" id="overview-tab" data-toggle="tab" href="#overview" role="tab">Overview</a>`;
          html += `  </li>`;
          html += `  <li class="nav-item">`;
          html += `    <a class="nav-link" id="disruptions-tab" data-toggle="tab" href="#disruptions" role="tab">Disruptions</a>`;
          html += `  </li>`;
          html += `  <li class="nav-item">`;
          html += `    <a class="nav-link" id="shipment-status-tab" data-toggle="tab" href="#shipment-status" role="tab">Shipments</a>`;
          html += `  </li>`;
          html += `  <li class="nav-item">`;
            html += `    <a class="nav-link" id="products-tab" data-toggle="tab" href="#products" role="tab">Products Handled</a>`;
            html += `  </li>`;
          html += `</ul>`;
          
          // Helper function for table display
          function makeTableHTML(myArray) {
            if (!myArray || myArray.length === 0) {
              return "None";
            }
            var result = "<div style='max-height: 400px; overflow-y: auto;'><div style='display: grid; grid-template-columns: 1fr 1fr 1fr 1fr; gap: 10px;'>";
            for(var i=0; i<myArray.length; i++) {
                result += "<div>"+myArray[i]+"</div>";
            }
            result += "</div></div>";
            return result;
          }
          
          // Tab content
          html += `<div class="tab-content mt-3" id="distributorTabsContent">`;
          
          // Overview Tab
          html += `  <div class="tab-pane fade show active" id="overview" role="tabpanel">`;
          html += `    <div class="row sparkboxes mt-4">`;
          const ontimeDelivery = temp["on time delivery rate percent:"] && temp["on time delivery rate percent:"][0] ? temp["on time delivery rate percent:"][0] : "None";
          html += `      <div class="col-md-3"><div class="box box1 shadow"><h6>On-Time Delivery Percent</h6>${ontimeDelivery}${ontimeDelivery !== "None" ? "%" : ""}</div></div>`;
          const disruptionExposure = temp["DisruptionExposure:"] && temp["DisruptionExposure:"][0] ? temp["DisruptionExposure:"][0] : "None";
          html += `      <div class="col-md-3"><div class="box box2 shadow"><h6>Disruption Exposure</h6>${disruptionExposure}</div></div>`;
          const outstandingShipments = temp["Outstanding Shipments:"] && temp["Outstanding Shipments:"][0] ? temp["Outstanding Shipments:"][0] : "None";
          html += `      <div class="col-md-3"><div class="box box6 shadow"><h6>Outstanding Shipments</h6>${outstandingShipments}</div></div>`;
          const deliveredShipments = temp["Delievered Shipments:"] && temp["Delievered Shipments:"][0] ? temp["Delievered Shipments:"][0] : "None";
          html += `      <div class="col-md-3"><div class="box box7 shadow"><h6>Delivered Shipments</h6>${deliveredShipments}</div></div>`;
          html += `    </div>`;
          html += `    <h5 class="mt-4 mb-2">Shipment Details</h5>`;
          html += `    <div class="row sparkboxes mt-3">`;
          const totalShipments = temp["Number of Shipments:"] && temp["Number of Shipments:"][0] ? temp["Number of Shipments:"][0] : "None";
          html += `      <div class="col-md-4"><div class="box box3 shadow"><h6>Number of Shipments</h6>${totalShipments}</div></div>`;
          const avgShipmentVolume = temp["Average Shipment Volume:"] && temp["Average Shipment Volume:"][0] ? parseFloat(temp["Average Shipment Volume:"][0]).toFixed(2) : "None";
          html += `      <div class="col-md-4"><div class="box box4 shadow"><h6>Average Shipment Volume</h6>${avgShipmentVolume}</div></div>`;
          const totalShipmentVolume = temp["Total Shipment Volume:"] && temp["Total Shipment Volume:"][0] ? temp["Total Shipment Volume:"][0] : "None";
          html += `      <div class="col-md-4"><div class="box box5 shadow"><h6>Total Shipment Volume</h6>${totalShipmentVolume}</div></div>`;
          html += `    </div>`;
          html += `    <div class="box shadow mt-4">`;
          html += `      <h6>Top 10 Distributors by Shipment Volume</h6>`;
          html += `      <div id="topDistributorsBarChart"></div>`;
          html += `    </div>`;
          html += `  </div>`; // close Overview tab
          
          // Disruptions Tab
          html += `  <div class="tab-pane fade" id="disruptions" role="tabpanel">`;
          html += `    <div class="row mt-4 mb-5">`;
          html += `      <div class="col-md-12">`;
          html += `        <div class="box shadow">`;
          html += `          <h6>Disruption Impact Distribution</h6>`;
          html += `          <div id="disruptionBarChart"></div>`;
          html += `        </div>`;
          html += `      </div>`;
          html += `    </div>`;
          html += `  </div>`; // close Disruptions tab
          
          
          // Shipment Status Tab
          html += `  <div class="tab-pane fade" id="shipment-status" role="tabpanel">`;
          html += `    <div class="row mt-4 mb-5">`;
          html += `      <div class="col-md-12">`;
          html += `        <div class="box shadow">`;
          html += `          <h6>Individual Shipments</h6>`;
          
          // Build individual shipment table using transaction_V2.php data
          // For distributors, show arriving shipments (products they receive from manufacturers)
          const arrivingIds = shipmentData["Arriving ID"] || [];
          const arrivingDates = shipmentData["Arriving Date"] || [];
          const arrivingCompanies = shipmentData["Arriving Company"] || [];
          const arrivingProducts = shipmentData["Arriving Product"] || [];
          const arrivingQuantities = shipmentData["Arriving Quantity"] || [];
          
          if (arrivingIds.length > 0) {
            // Summary info using aggregated data (move to top)
            const outstandingTotal = temp["Outstanding Shipments:"] && temp["Outstanding Shipments:"][0] ? temp["Outstanding Shipments:"][0] : 0;
            const deliveredTotal = temp["Delievered Shipments:"] && temp["Delievered Shipments:"][0] ? temp["Delievered Shipments:"][0] : 0;
            html += `<div class="mb-3" style="padding: 10px; background-color: #f8f9fa; border-radius: 5px;">`;
            html += `<strong>Total Incoming Shipments:</strong> ${arrivingIds.length} listed | `;
            html += `<strong>Overall Summary:</strong> ${deliveredTotal} Delivered, ${outstandingTotal} Outstanding`;
            html += `</div>`;
            html += `<div style="max-height: 250px; overflow-y: auto;">`;
            html += `<table class="table table-striped"><thead><tr>`;
            html += `<th>Shipment ID</th><th>Date</th><th>Source Company</th><th>Product</th><th>Quantity</th>`;
            html += `</tr></thead><tbody>`;
            for (let i = 0; i < arrivingIds.length; i++) {
              html += `<tr>`;
              html += `<td>${arrivingIds[i]}</td>`;
              html += `<td>${arrivingDates[i]}</td>`;
              html += `<td>${arrivingCompanies[i]}</td>`;
              html += `<td>${arrivingProducts[i]}</td>`;
              html += `<td>${arrivingQuantities[i]}</td>`;
              html += `</tr>`;
            }
            html += `</tbody></table></div>`;
          } else {
            html += `<p style="text-align: center; padding: 40px; color: #6c757d;">No shipment data available.</p>`;
          }
          
          html += `        </div>`;
          html += `      </div>`;
          html += `    </div>`;
          html += `  </div>`; // close Shipment Status tab
          
          // Products Handled Tab
          let products = temp["Products Handeled List:"];
          html += `  <div class="tab-pane fade" id="products" role="tabpanel">`;
          html += `    <div class="row mt-4 mb-5">`;
          html += `      <div class="col-md-12">`;
          html += `        <div class="box shadow"><h6>Products Handled</h6>${makeTableHTML(products)}</div>`;
          html += `      </div>`;
          html += `    </div>`;
          html += `  </div>`; // close Products Handled tab
          
          html += `</div>`; // close tab-content
          
          document.getElementById("temp").innerHTML = html;
          
          // Create disruption bar chart using the new modular function
          setTimeout(function() {
            // Verify this is still the current request before rendering charts
            if (requestId !== currentRequestId) {
              console.log("Skipping chart render for stale request");
              return;
            }
            
            if (temp["Impacts(low,medium,high):"] && temp["Impacts(low,medium,high):"].length === 3) {
              createBarChart('disruptionBarChart', {
                labels: ['Low Impact', 'Medium Impact', 'High Impact'],
                values: temp["Impacts(low,medium,high):"],
                colors: ['#00E396', '#FEB019', '#FF4560']
              }, {
                xAxisTitle: 'Impact Type',
                yAxisTitle: 'Count',
                seriesName: 'Count',
                tooltipFormatter: (val) => val + " events"
              });
            }
            
            // Create top distributors bar chart
            const distributorNames = temp["Company Names for Shipment Volume:"] || [];
            const shipmentVolumes = temp["Total Shipment Volume List:"] || [];
            const topNames = distributorNames.slice(0, 10);
            const topVolumes = shipmentVolumes.slice(0, 10);
            if (topNames.length && topVolumes.length) {
              createBarChart('topDistributorsBarChart', {
                labels: topNames,
                values: topVolumes,
                colors: Array(topNames.length).fill('#008FFB')
              }, {
                xAxisTitle: 'Distributor',
                yAxisTitle: 'Total Shipment Volume',
                seriesName: 'Shipment Volume',
                height: 300
              });
            } else {
              document.getElementById('topDistributorsBarChart').innerHTML = '<p style="text-align: center; color: #6c757d;">No distributor shipment volume data available.</p>';
            }
          }, 100);
        }
      
      
      var url = "distributor_transactions_output.php?" + new URLSearchParams(inputs).toString();
      console.log(url);
      xhttp.open("GET", url, true);
      xhttp.send();
    }
  </script>

  <div id="temp"></div>

        </div>
      </div>
    </div>
  </div>

  <!-- Bootstrap JS and dependencies for tab functionality -->
  <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.6/umd/popper.min.js"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/js/bootstrap.min.js"></script>
</body>

</html>
