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
<!-- https://apexcharts.com/javascript-chart-demos/dashboards/modern/ -->
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

  <title>Company Information</title>

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

 <!-- https://www.wrappixel.com/templates/materialm-free-bootstrap-admin/ -->
        
     <!-- Page Title --> 
    <h3>Company Information</h3>

  <div class="box shadow">
    <!-- This form is used to get user input for the company name and date range -->
    <form action= "" onsubmit="showCustomer()">
        <div style="display: grid; grid-template-columns: 3fr 1fr 1fr auto; gap: 10px; align-items: end;">
          <input type= "text" name= "Company_name" id= "company" placeholder= "Enter a company name" >
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
      document.getElementById("company").value = "Haynes-Long";
      
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
    
    // This function is in a script tag, indicating javascript, it is called when we
    // click the submit button on the form above.
    function showCustomer() {
      // These 3 strings are retrieved from the table above and joined into one string
      // separated by commas in the format the php file expects to find after the ?q=
      // part. 

      // Clear previous error message
      const errorMessageDiv = document.getElementById("errorMessage");
      errorMessageDiv.style.display = "none";
      errorMessageDiv.textContent = "";

      // This part gets the strings from the table.
      str = document.getElementById("company").value.trim();
      xt = document.getElementById("date1").value;
      yt = document.getElementById("date2").value;

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

      // This is how you join the strings into one big one.
      str = xt + "|" + yt + "|" + str;
      console.log(str);
      


      // This thing is weird and you don't always need it, but I think you need it specifically
      // for submit button based pages.
      event.preventDefault();

      // Here is where we start defining the XHTTP request that JS uses to call the php file 
      // with the variables.
      var xhttp;
      if (str == "") {
        // If the table variable/query variable is empty, make sure we're not displaying anything in temp. 
        // This isn't necessary in this example, but could be useful for particular configurations.
        document.getElementById("temp").innerHTML = "";
        return;
      }
      // this is where we define the actual request.
      xhttp = new XMLHttpRequest();

      // This function is called when the request loads from the php file.
      xhttp.onload = function () {
        // This if statement checks if we're ready and the php file returned a "working"
        // result (i.e. html status code 200, look this up if curious or confused.)
        if (this.readyState == 4 && this.status == 200) {

          // Since we have our php file returning the code in JSON, we need to parse the string as JSON.
          // this produces a list of Key-Value-Pairs, which are indexed by the key names, which we need to grab from
          // xt and yt variable from before. We do this, in this section of the code eval(`item.${xt}`) } below.
          //nsole.log(this.responseText);
          var temp = JSON.parse(this.responseText);
          console.log(temp);
          
          // Validate company name against database list
          const companyListAll = temp["Company List All:"];
          const enteredCompany = document.getElementById("company").value.trim();
          
          if (companyListAll && companyListAll.length > 0 && !companyListAll.includes(enteredCompany)) {
            const errorMessageDiv = document.getElementById("errorMessage");
            errorMessageDiv.textContent = "Company '" + enteredCompany + "' not found in database. Please check the spelling and try again.";
            errorMessageDiv.style.display = "block";
            document.getElementById("temp").innerHTML = "";
            return;
          }
          // https://stackoverflow.com/questions/14643617/create-table-using-javascript
          function makeTableHTML(myArray) {
            if (!myArray || myArray.length === 0) {
              return "None";
            }
            var result = "<div style='max-height: 400px; overflow-y: auto;'><div style='display: grid; grid-template-columns: 1fr 1fr; gap: 10px;'>";
            for(var i=0; i<myArray.length; i++) {
                result += "<div>"+myArray[i]+"</div>";
            }
            result += "</div></div>";
            return result;
          }
          //https://stackoverflow.com/questions/14643617/create-table-using-javascript
          function makeTableHTMLColoumns(myArray) {
            if (!myArray || myArray.length === 0) {
              return "None";
            }
            var result = "<div style='max-height: 400px; overflow-y: auto;'><div style='display: grid; grid-template-columns: 1fr; gap: 10px;'>";
            for(var i=0; i<myArray.length; i++) {
                result += "<div>" + myArray[i] + "</div>";
            }
            result +="</div></div>";
            return result;
          }

          const companyName = temp["Company Name"] || "None";
          const tier = temp["Tier"] || "None";
          const type = temp["Type"] || "None";
          const city = temp["City"] || "";
          const country = temp["Country"] || "";
          const continent = temp["Continent"] || "";
          const locationText = [city, country, continent].filter(x => x).join(", ") || "None";

          let html = `<h3></br>${companyName}</h3>`;
          
          // Tab navigation
          html += `<ul class="nav nav-tabs mt-3" id="companyTabs" role="tablist">`;
          html += `  <li class="nav-item">`;
          html += `    <a class="nav-link active" id="info-tab" data-toggle="tab" href="#info" role="tab">Information</a>`;
          html += `  </li>`;
          html += `  <li class="nav-item">`;
          html += `    <a class="nav-link" id="financial-tab" data-toggle="tab" href="#financial" role="tab">Financial Health</a>`;
          html += `  </li>`;
          html += `  <li class="nav-item">`;
          html += `    <a class="nav-link" id="dependencies-tab" data-toggle="tab" href="#dependencies" role="tab">Dependencies</a>`;
          html += `  </li>`;
          html += `  <li class="nav-item">`;
          html += `    <a class="nav-link" id="disruptions-tab" data-toggle="tab" href="#disruptions" role="tab">Disruptions</a>`;
          html += `  </li>`;
          html += `  <li class="nav-item">`;
          html += `    <a class="nav-link" id="transactions-tab" data-toggle="tab" href="#transactions" role="tab">Transactions</a>`;
          html += `  </li>`;
          
          if (type == "Manufacturer") {
            html += `  <li class="nav-item">`;
            html += `    <a class="nav-link" id="products-tab" data-toggle="tab" href="#products" role="tab">Products</a>`;
            html += `  </li>`;
          } else if (type == "Distributor") {
            html += `  <li class="nav-item">`;
            html += `    <a class="nav-link" id="routes-tab" data-toggle="tab" href="#routes" role="tab">Routes</a>`;
            html += `  </li>`;
          }
          
          html += `</ul>`;
          
          // Tab content
          html += `<div class="tab-content mt-3" id="companyTabsContent">`;
          
          // Company Information Tab
          html += `  <div class="tab-pane fade show active" id="info" role="tabpanel">`;
          
          // Get most recent financial health score
          const recentHealthScore = temp["Most Recent Financial Health Score"] && temp["Most Recent Financial Health Score"][0] ? temp["Most Recent Financial Health Score"][0] : "None";
          
          // Determine layout based on company type
          if (type == "Manufacturer") {
            // Manufacturer: 2 rows of 4
            html += `    <div class="row sparkboxes mt-4">`;
            html += `      <div class="col-md-3"><div class="box box1 shadow"><h6>Tier</h6>${tier || "None"}</div></div>`;
            html += `      <div class="col-md-3"><div class="box box2 shadow"><h6>Type</h6>${type || "None"}</div></div>`;
            html += `      <div class="col-md-3"><div class="box box3 shadow"><h6>Location</h6>${locationText}</div></div>`;
            const capacity = temp["Factory Capacity"] && temp["Factory Capacity"][0] ? temp["Factory Capacity"][0] : "None";
            html += `      <div class="col-md-3"><div class="box box4 shadow"><h6>Capacity</h6>${capacity}</div></div>`;
            html += `    </div>`; // close first row
            
            // Second row for delivery metrics
            const ontimeDelivery = temp["On Time Delivery Rate Percent"] && temp["On Time Delivery Rate Percent"][0] ? temp["On Time Delivery Rate Percent"][0] : "None";
            const avgDelay = temp["Average of Delay"] && temp["Average of Delay"][0] ? parseFloat(temp["Average of Delay"][0]).toFixed(2) : "None";
            const stdDevDelay = temp["Standard Deviation of Delay"] && temp["Standard Deviation of Delay"][0] ? parseFloat(temp["Standard Deviation of Delay"][0]).toFixed(2) : "None";
            
            html += `    <div class="row sparkboxes mt-3">`;
            html += `      <div class="col-md-3"><div class="box box5 shadow"><h6>On-Time Delivery</h6>${ontimeDelivery}${ontimeDelivery !== "None" ? "%" : ""}</div></div>`;
            html += `      <div class="col-md-3"><div class="box box6 shadow"><h6>Average Delay</h6>${avgDelay}${avgDelay !== "None" ? " days" : ""}</div></div>`;
            html += `      <div class="col-md-3"><div class="box box7 shadow"><h6>Std Dev of Delay</h6>${stdDevDelay}${stdDevDelay !== "None" ? " days" : ""}</div></div>`;
            html += `      <div class="col-md-3"><div class="box box1 shadow"><h6>Financial Score</h6>${recentHealthScore}</div></div>`;
            html += `    </div>`; // close second row
          } else if (type == "Distributor") {
            // Distributor: 2 rows of 4 (same as Manufacturer but with Routes instead of Capacity)
            html += `    <div class="row sparkboxes mt-4">`;
            html += `      <div class="col-md-3"><div class="box box1 shadow"><h6>Tier</h6>${tier || "None"}</div></div>`;
            html += `      <div class="col-md-3"><div class="box box2 shadow"><h6>Type</h6>${type || "None"}</div></div>`;
            html += `      <div class="col-md-3"><div class="box box3 shadow"><h6>Location</h6>${locationText}</div></div>`;
            let Distributors = temp["Routes"];
            const routesCount = Array.isArray(Distributors) && Distributors.length > 0 ? Distributors.length : "None";
            html += `      <div class="col-md-3"><div class="box box4 shadow"><h6>Routes</h6>${routesCount}</div></div>`;
            html += `    </div>`; // close first row
            
            // Second row for delivery metrics (same as Manufacturer)
            const ontimeDeliveryDist = temp["On Time Delivery Rate Percent"] && temp["On Time Delivery Rate Percent"][0] ? temp["On Time Delivery Rate Percent"][0] : "None";
            const avgDelayDist = temp["Average of Delay"] && temp["Average of Delay"][0] ? parseFloat(temp["Average of Delay"][0]).toFixed(2) : "None";
            const stdDevDelayDist = temp["Standard Deviation of Delay"] && temp["Standard Deviation of Delay"][0] ? parseFloat(temp["Standard Deviation of Delay"][0]).toFixed(2) : "None";
            
            html += `    <div class="row sparkboxes mt-3">`;
            html += `      <div class="col-md-3"><div class="box box5 shadow"><h6>On-Time Delivery</h6>${ontimeDeliveryDist}${ontimeDeliveryDist !== "None" ? "%" : ""}</div></div>`;
            html += `      <div class="col-md-3"><div class="box box6 shadow"><h6>Average Delay</h6>${avgDelayDist}${avgDelayDist !== "None" ? " days" : ""}</div></div>`;
            html += `      <div class="col-md-3"><div class="box box7 shadow"><h6>Std Dev of Delay</h6>${stdDevDelayDist}${stdDevDelayDist !== "None" ? " days" : ""}</div></div>`;
            html += `      <div class="col-md-3"><div class="box box1 shadow"><h6>Financial Score</h6>${recentHealthScore}</div></div>`;
            html += `    </div>`; // close second row
          } else {
            // Retailer: 1 row of 4
            html += `    <div class="row sparkboxes mt-4">`;
            html += `      <div class="col-md-3"><div class="box box1 shadow"><h6>Tier</h6>${tier || "None"}</div></div>`;
            html += `      <div class="col-md-3"><div class="box box2 shadow"><h6>Type</h6>${type || "None"}</div></div>`;
            html += `      <div class="col-md-3"><div class="box box3 shadow"><h6>Location</h6>${locationText}</div></div>`;
            html += `      <div class="col-md-3"><div class="box box4 shadow"><h6>Financial Score</h6>${recentHealthScore}</div></div>`;
            html += `    </div>`; // close row
          }
          
          html += `  </div>`;       // close Company Information tab
          
          // Financial Health Tab
          html += `  <div class="tab-pane fade" id="financial" role="tabpanel">`;
          html += `    <div class="row mt-4 mb-5">`;
          html += `      <div class="col-md-12">`;
          html += `        <div class="box shadow">`;
          html += `          <h6>Financial Health Status</h6> <div id="myChartLine"></div>`;
          html += `        </div>`;
          html += `      </div>`;
          html += `    </div>`;
          html += `  </div>`;       // close Financial Health tab
          
          // Dependencies Tab
          html += `  <div class="tab-pane fade" id="dependencies" role="tabpanel">`;
          html += `    <div class="row mt-4 mb-5">`;
          
          let whoDependsOnThem = temp["Who depends on them"];
          let whoTheyDependOn = temp["Who they depend on"];
          html += `      <div class="col-md-6"><div class="box shadow"><h6>Who Depends on Them</h6>${makeTableHTMLColoumns(whoDependsOnThem)}</div></div>`;
          html += `      <div class="col-md-6"><div class="box shadow"><h6>Who They Depend On</h6>${makeTableHTMLColoumns(whoTheyDependOn)}</div></div>`;
          
          html += `    </div>`;
          html += `  </div>`;       // close Dependencies tab
          
          // Disruptions Tab
          html += `  <div class="tab-pane fade" id="disruptions" role="tabpanel">`;
          html += `    <div class="row mt-4 mb-5">`;
          
          let disruptionCategories = temp["Disruption Category"];
          let disruptionPercentages = temp["Disruption Percentage"];
          let disruptionList = temp["Disruption Category List"];
          let disruptionDates = temp["Disruption Event Date"];
          
          // Left box: Disruption Categories with percentages (pie chart)
          html += `      <div class="col-md-6"><div class="box shadow" style="min-height: 550px; padding-bottom: 20px;"><h6>Disruption Categories</h6>`;
          if (disruptionCategories && disruptionCategories.length > 0) {
            html += `<div id="disruptionchart"></div>`;
          } else {
            html += "None";
          }
          html += `</div></div>`;
          
          // Right box: Disruption Events by date
          html += `      <div class="col-md-6"><div class="box shadow"><h6>Disruption Events</h6>`;
          if (disruptionList && disruptionList.length > 0) {
            // Create array of objects to sort by date
            let events = [];
            for (let i = 0; i < disruptionList.length; i++) {
              events.push({
                date: disruptionDates[i],
                event: disruptionList[i]
              });
            }
            // Sort by date descending (most recent first)
            events.sort((a, b) => new Date(b.date) - new Date(a.date));
            
            html += `<div style="max-height: 400px; overflow-y: auto;">`;
            html += `<table class="table table-striped"><thead><tr>`;
            html += `<th>Date</th><th>Event</th>`;
            html += `</tr></thead><tbody>`;
            for (let i = 0; i < events.length; i++) {
              html += `<tr><td>${events[i].date}</td><td>${events[i].event}</td></tr>`;
            }
            html += `</tbody></table></div>`;
          } else {
            html += "None";
          }
          html += `</div></div>`;
          
          html += `    </div>`;
          html += `  </div>`;       // close Disruptions tab
          
          // Transactions Tab (All company types with nested tabs)
          html += `  <div class="tab-pane fade" id="transactions" role="tabpanel">`;
          html += `    <div class="mt-4">`;
          
          // Nested tabs for transaction types
          html += `      <ul class="nav nav-tabs" id="transactionTabs" role="tablist">`;
          
          if (type == "Manufacturer") {
            html += `        <li class="nav-item">`;
            html += `          <a class="nav-link active" id="shipping-tab" data-toggle="tab" href="#shipping" role="tab">Shipping</a>`;
            html += `        </li>`;
            html += `        <li class="nav-item">`;
            html += `          <a class="nav-link" id="receiving-tab" data-toggle="tab" href="#receiving" role="tab">Receiving</a>`;
            html += `        </li>`;
            html += `        <li class="nav-item">`;
            html += `          <a class="nav-link" id="adjustment-tab" data-toggle="tab" href="#adjustment" role="tab">Adjustment</a>`;
            html += `        </li>`;
          } else if (type == "Retailer") {
            html += `        <li class="nav-item">`;
            html += `          <a class="nav-link active" id="receiving-tab" data-toggle="tab" href="#receiving" role="tab">Receiving</a>`;
            html += `        </li>`;
            html += `        <li class="nav-item">`;
            html += `          <a class="nav-link" id="adjustment-tab" data-toggle="tab" href="#adjustment" role="tab">Adjustment</a>`;
            html += `        </li>`;
          } else if (type == "Distributor") {
            html += `        <li class="nav-item">`;
            html += `          <a class="nav-link active" id="adjustment-tab" data-toggle="tab" href="#adjustment" role="tab">Adjustment</a>`;
            html += `        </li>`;
          }
          
          html += `      </ul>`;
          
          // Nested tab content
          html += `      <div class="tab-content mt-3" id="transactionTabsContent">`;
          
          if (type == "Manufacturer") {
            // Shipping tab
            html += `        <div class="tab-pane fade show active" id="shipping" role="tabpanel">`;
            html += `          <div class="row mb-5">`;
            html += `            <div class="col-md-12">`;
            html += `              <div class="box shadow"><h6>Shipping Transactions</h6>`;
            
            // Build shipping table
            if (temp["Shipping: Date"] && temp["Shipping: Date"].length > 0) {
              html += `<div style="max-height: 400px; overflow-y: auto;"><table class="table table-striped"><thead><tr>`;
              html += `<th>Shipping Date</th><th>Product Name</th><th>Source Company</th><th>Destination Company</th><th>Quantity</th>`;
              html += `</tr></thead><tbody>`;
              for (let i = 0; i < temp["Shipping: Date"].length; i++) {
                html += `<tr>`;
                html += `<td>${temp["Shipping: Date"][i]}</td>`;
                html += `<td>${temp["Shipping: Product Name"][i]}</td>`;
                html += `<td>${temp["Shipping: Soruce Company Name"][i]}</td>`;
                html += `<td>${temp["Shipping: Destination Company Name"][i]}</td>`;
                html += `<td>${temp["Shipping: Quantity"][i]}</td>`;
                html += `</tr>`;
              }
              html += `</tbody></table></div>`;
            } else {
              html += `<p style="text-align: center; padding: 20px; color: #6c757d;">No shipping transactions found.</p>`;
            }
            
            html += `              </div>`;
            html += `            </div>`;
            html += `          </div>`;
            html += `        </div>`;
            
            // Receiving tab
            html += `        <div class="tab-pane fade" id="receiving" role="tabpanel">`;
            html += `          <div class="row mb-5">`;
            html += `            <div class="col-md-12">`;
            html += `              <div class="box shadow"><h6>Receiving Transactions</h6>`;
            
            // Build receiving table
            if (temp["Receiving: Date"] && temp["Receiving: Date"].length > 0) {
              html += `<div style="max-height: 400px; overflow-y: auto;"><table class="table table-striped"><thead><tr>`;
              html += `<th>Receiving Date</th><th>Product Name</th><th>Source Company</th><th>Destination Company</th><th>Quantity</th>`;
              html += `</tr></thead><tbody>`;
              for (let i = 0; i < temp["Receiving: Date"].length; i++) {
                html += `<tr>`;
                html += `<td>${temp["Receiving: Date"][i]}</td>`;
                html += `<td>${temp["Receiving: Product Name"][i]}</td>`;
                html += `<td>${temp["Receiving: Soruce Company Name"][i]}</td>`;
                html += `<td>${temp["Receiving: Destination Company Name"][i]}</td>`;
                html += `<td>${temp["Receiving: Quantity"][i]}</td>`;
                html += `</tr>`;
              }
              html += `</tbody></table></div>`;
            } else {
              html += `<p style="text-align: center; padding: 20px; color: #6c757d;">No receiving transactions found.</p>`;
            }
            
            html += `              </div>`;
            html += `            </div>`;
            html += `          </div>`;
            html += `        </div>`;
            
            // Adjustment tab
            html += `        <div class="tab-pane fade" id="adjustment" role="tabpanel">`;
            html += `          <div class="row mb-5">`;
            html += `            <div class="col-md-12">`;
            html += `              <div class="box shadow"><h6>Adjustment Transactions</h6>`;
            
            // Build adjustment table
            if (temp["Adjustment: Date"] && temp["Adjustment: Date"].length > 0) {
              html += `<div style="max-height: 400px; overflow-y: auto;"><table class="table table-striped"><thead><tr>`;
              html += `<th>Date</th><th>Product Name</th><th>Source Company</th><th>Quantity</th><th>Reason</th>`;
              html += `</tr></thead><tbody>`;
              for (let i = 0; i < temp["Adjustment: Date"].length; i++) {
                html += `<tr>`;
                html += `<td>${temp["Adjustment: Date"][i]}</td>`;
                html += `<td>${temp["Adjustment: Product Name"][i]}</td>`;
                html += `<td>${temp["Adjustment: Soruce Company Name"][i]}</td>`;
                html += `<td>${temp["Adjustment: Quantity"][i]}</td>`;
                html += `<td>${temp["Adjustment: Reason"][i]}</td>`;
                html += `</tr>`;
              }
              html += `</tbody></table></div>`;
            } else {
              html += `<p style="text-align: center; padding: 20px; color: #6c757d;">No adjustment transactions found.</p>`;
            }
            
            html += `              </div>`;
            html += `            </div>`;
            html += `          </div>`;
            html += `        </div>`;
          } else if (type == "Retailer") {
            // Receiving tab
            html += `        <div class="tab-pane fade show active" id="receiving" role="tabpanel">`;
            html += `          <div class="row mb-5">`;
            html += `            <div class="col-md-12">`;
            html += `              <div class="box shadow"><h6>Receiving Transactions</h6>`;
            
            // Build receiving table
            if (temp["Receiving: Date"] && temp["Receiving: Date"].length > 0) {
              html += `<div style="max-height: 400px; overflow-y: auto;"><table class="table table-striped"><thead><tr>`;
              html += `<th>Receiving Date</th><th>Product Name</th><th>Source Company</th><th>Destination Company</th><th>Quantity</th>`;
              html += `</tr></thead><tbody>`;
              for (let i = 0; i < temp["Receiving: Date"].length; i++) {
                html += `<tr>`;
                html += `<td>${temp["Receiving: Date"][i]}</td>`;
                html += `<td>${temp["Receiving: Product Name"][i]}</td>`;
                html += `<td>${temp["Receiving: Soruce Company Name"][i]}</td>`;
                html += `<td>${temp["Receiving: Destination Company Name"][i]}</td>`;
                html += `<td>${temp["Receiving: Quantity"][i]}</td>`;
                html += `</tr>`;
              }
              html += `</tbody></table>`;
              html += `</tbody></table></div>`;
            } else {
              html += `<p style="text-align: center; padding: 20px; color: #6c757d;">No receiving transactions found.</p>`;
            }
            
            html += `              </div>`;
            html += `            </div>`;
            html += `          </div>`;
            html += `        </div>`;
            
            // Adjustment tab
            html += `        <div class="tab-pane fade" id="adjustment" role="tabpanel">`;
            html += `          <div class="row mb-5">`;
            html += `            <div class="col-md-12">`;
            html += `              <div class="box shadow"><h6>Adjustment Transactions</h6>`;
            
            // Build adjustment table
            if (temp["Adjustment: Date"] && temp["Adjustment: Date"].length > 0) {
              html += `<div style="max-height: 400px; overflow-y: auto;"><table class="table table-striped"><thead><tr>`;
              html += `<th>Date</th><th>Product Name</th><th>Source Company</th><th>Quantity</th><th>Reason</th>`;
              html += `</tr></thead><tbody>`;
              for (let i = 0; i < temp["Adjustment: Date"].length; i++) {
                html += `<tr>`;
                html += `<td>${temp["Adjustment: Date"][i]}</td>`;
                html += `<td>${temp["Adjustment: Product Name"][i]}</td>`;
                html += `<td>${temp["Adjustment: Soruce Company Name"][i]}</td>`;
                html += `<td>${temp["Adjustment: Quantity"][i]}</td>`;
                html += `<td>${temp["Adjustment: Reason"][i]}</td>`;
                html += `</tr>`;
              }
              html += `</tbody></table></div>`;
            } else {
              html += `<p style="text-align: center; padding: 20px; color: #6c757d;">No adjustment transactions found.</p>`;
            }
            
            html += `              </div>`;
            html += `            </div>`;
            html += `          </div>`;
            html += `        </div>`;
          } else if (type == "Distributor") {
            // Adjustment tab only
            html += `        <div class="tab-pane fade show active" id="adjustment" role="tabpanel">`;
            html += `          <div class="row mb-5">`;
            html += `            <div class="col-md-12">`;
            html += `              <div class="box shadow"><h6>Adjustment Transactions</h6>`;
            
            // Build adjustment table
            if (temp["Adjustment: Date"] && temp["Adjustment: Date"].length > 0) {
              html += `<div style="max-height: 400px; overflow-y: auto;"><table class="table table-striped"><thead><tr>`;
              html += `<th>Date</th><th>Product Name</th><th>Source Company</th><th>Quantity</th><th>Reason</th>`;
              html += `</tr></thead><tbody>`;
              for (let i = 0; i < temp["Adjustment: Date"].length; i++) {
                html += `<tr>`;
                html += `<td>${temp["Adjustment: Date"][i]}</td>`;
                html += `<td>${temp["Adjustment: Product Name"][i]}</td>`;
                html += `<td>${temp["Adjustment: Soruce Company Name"][i]}</td>`;
                html += `<td>${temp["Adjustment: Quantity"][i]}</td>`;
                html += `<td>${temp["Adjustment: Reason"][i]}</td>`;
                html += `</tr>`;
              }
              html += `</tbody></table></div>`;
            } else {
              html += `<p style="text-align: center; padding: 20px; color: #6c757d;">No adjustment transactions found.</p>`;
            }
            
            html += `              </div>`;
            html += `            </div>`;
            html += `          </div>`;
            html += `        </div>`;
          }
          
          html += `      </div>`;   // close nested tab-content
          html += `    </div>`;
          html += `  </div>`;       // close Transactions tab
          
          // Products Tab (Manufacturer only)
          if (type == "Manufacturer") {
            let products = temp["Products"];
            html += `  <div class="tab-pane fade" id="products" role="tabpanel">`;
            html += `    <div class="row mt-4 mb-5">`;
            html += `      <div class="col-md-7">`;
            html += `        <div class="box shadow"><h6>Products</h6>${makeTableHTML(products)}</div>`;
            html += `      </div>`;
            html += `      <div class="col-md-5">`;
            html += `        <div class="box shadow" style="min-height: 450px; padding-bottom: 40px;">`;
            html += `          <h6>Categories</h6> <div id="myChart"></div>`;
            html += `        </div>`;
            html += `      </div>`;
            html += `    </div>`;
            html += `  </div>`;     // close Products tab
            
            // Clear pie chart for non-manufacturers
            if (window.myPieChart) {
              window.myPieChart.destroy();
              window.myPieChart = null;
            }
          } else if (type == "Distributor") {
            // Routes Tab (Distributor only)
            let Distributors = temp["Routes"];
            html += `  <div class="tab-pane fade" id="routes" role="tabpanel">`;
            html += `    <div class="row mt-4 mb-5">`;
            html += `      <div class="col-md-12">`;
            html += `        <div class="box shadow"><h6>Routes</h6>${makeTableHTML(Distributors)}</div>`;
            html += `      </div>`;
            html += `    </div>`;
            html += `  </div>`;     // close Routes tab
            
            // Clear pie chart for non-manufacturers
            if (window.myPieChart) {
              window.myPieChart.destroy();
              window.myPieChart = null;
            }
          } else {
            // Clear pie chart for non-manufacturers
            if (window.myPieChart) {
              window.myPieChart.destroy();
              window.myPieChart = null;
            }
          }
          
          html += `</div>`;         // close tab-content

          var tempEl = document.getElementById("temp");
          tempEl.innerHTML = html;
          
          // Create charts AFTER HTML is inserted into DOM using new modular functions
          setTimeout(function() {
            if (type == "Manufacturer") {
              createPieChart("myChart", {
                labels: temp["Categories Products"],
                values: temp["Categories Products Percentages"]
              }, {
                monochrome: true
              });
            }
            
            createLineChart("myChartLine", {
              labels: temp["Quarters"],
              values: temp["Scores"]
            }, {
              seriesName: 'Financial Health',
              lineColor: 'rgb(75, 192, 192)',
              xAxisTitle: 'Year (Quarter)',
              yAxisTitle: 'Financial Health Score'
            });
            
            // Create disruption pie chart
            if (temp["Disruption Category"] && temp["Disruption Category"].length > 0 &&
                temp["Disruption Percentage"] && temp["Disruption Percentage"].length > 0) {
              createColorfulPieChart("disruptionchart", {
                labels: temp["Disruption Category"],
                values: temp["Disruption Percentage"]
              }, {
                height: 480,
                legendPosition: 'bottom',
                legendHeight: 80
              });
            }
          }, 100);
        }
      };

      

      // This portion actually runs the HTTP request to the php file using the variables set in the table
      // and concatenated on line 49 into the str variable which contains x_variable,y_variable,table
      // but replace the placeholders with whatever variables/table  you're trying to query.
      var url = "company_info_output.php?q=" + encodeURIComponent(str);
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
  </div>
    
    <!-- Bootstrap JS and dependencies for tab functionality -->
    <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.6/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/js/bootstrap.min.js"></script>
  </body><!-- https://web.ics.purdue.edu/~brumfij/company_info_page_josie_v5 -->

</html>