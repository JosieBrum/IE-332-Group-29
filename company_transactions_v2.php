<?php
session_start(); // Start the session
// Check if the user is logged in
if (!isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
    // Redirect to login page if not logged in
    header("Location: index.php"); // Change this to your login page
    exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@picocss/pico@2/css/pico.min.css"> <!-- input form style -->
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/css/bootstrap.min.css"/>
  <link href="https://fonts.googleapis.com/css?family=Montserrat" rel="stylesheet"/>
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css"/>

  <link rel="stylesheet" href="styles.css?v=3" />  <!-- general css style -->

  <script src="https://cdn.plot.ly/plotly-3.3.0.min.js" charset="utf-8"></script>
  <script src="https://cdn.jsdelivr.net/npm/chart.js@latest/dist/chart.umd.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
  <script src="chart-utils.js?v=7"></script>
  
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
      min-height: 100vh;
    }
  </style>

  <title>Company Transactions</title>

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
      });
  </script>

  <!-- Main Content -->
  <div class="main-content">
    <div id="wrapper">
      <div class="content-area">
        <div class="container-fluid">

     <!-- Page Title --> 
    <h3>Company Transactions</h3>

  <div class="box shadow">
    <!-- This form is used to search for company transactions -->
    <form action="" onsubmit="showCustomer()">
        <div style="display: grid; grid-template-columns: 1fr; gap: 15px;">
          <select name="searchType" id="searchType" onchange="updateSearchFields()" required>
            <option value="">Select search type</option>
            <option value="company">Company</option>
            <option value="continent">Continent</option>
            <option value="country">Country</option>
            <option value="city">City</option>
          </select>
          
          <div id="searchFields"></div>
          
          <div style="display: grid; grid-template-columns: 1fr 1fr auto; gap: 10px; align-items: end;">
            <input type="date" name="date1" id="date1" placeholder="Enter starting date">
            <input type="date" name="date2" id="date2" placeholder="Enter ending date">
            <input type="submit" name="submit-btn" value="Search">
          </div>
        </div>
        <div id="errorMessage" class="mt-3" style="color: red; display: none;"></div>
    </form>
  </div>

  <!-- Tabs for different transaction views (hidden until search is performed) -->
  <div class="mt-4" id="transactionsTabsContainer" style="display: none;">
    <ul class="nav nav-tabs" id="transactionTabs" role="tablist">
      <li class="nav-item">
        <a class="nav-link active" id="arriving-tab" data-toggle="tab" href="#arriving" role="tab">Arriving</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" id="leaving-tab" data-toggle="tab" href="#leaving" role="tab">Leaving</a>
      </li>
      <li class="nav-item">
        <a class="nav-link" id="adjustments-tab" data-toggle="tab" href="#adjustments" role="tab">Adjustments</a>
      </li>
    </ul>

    <div class="tab-content mt-3" id="transactionTabsContent">
      <div class="tab-pane fade show active" id="arriving" role="tabpanel">
        <h5 class="mt-3">Arriving Transactions</h5>
        <div class="box shadow">
          <div id="arrivingTotalDisplay" style="text-align: center; font-size: 18px; font-weight: bold; margin-bottom: 15px; padding: 10px; background-color: #f8f9fa; border-radius: 5px;">Loading...</div>
          <div id="arrivingTable" style="max-height: 300px; overflow-y: auto;">
            <p style="text-align: center; padding: 20px; color: #6c757d;">Loading...</p>
          </div>
        </div>
        <div class="box shadow mt-3">
          <h6>Top 10 Products Arriving by Quantity</h6>
          <div id="arrivingChart"></div>
        </div>
      </div>
      
      <div class="tab-pane fade" id="leaving" role="tabpanel">
        <h5 class="mt-3">Leaving Transactions</h5>
        <div class="box shadow">
          <div id="leavingTotalDisplay" style="text-align: center; font-size: 18px; font-weight: bold; margin-bottom: 15px; padding: 10px; background-color: #f8f9fa; border-radius: 5px;">Loading...</div>
          <div id="leavingTable" style="max-height: 300px; overflow-y: auto;">
            <p style="text-align: center; padding: 20px; color: #6c757d;">Loading...</p>
          </div>
        </div>
        <div class="box shadow mt-3">
          <h6>Top 10 Products Leaving by Quantity</h6>
          <div id="leavingChart"></div>
        </div>
      </div>
      
      <div class="tab-pane fade" id="adjustments" role="tabpanel">
        <h5 class="mt-3">Adjustment Transactions</h5>
        <div class="box shadow">
          <div id="adjustmentsTotalDisplay" style="text-align: center; font-size: 18px; font-weight: bold; margin-bottom: 15px; padding: 10px; background-color: #f8f9fa; border-radius: 5px;">Loading...</div>
          <div id="adjustmentsTable" style="max-height: 400px; overflow-y: auto;">
            <p style="text-align: center; padding: 20px; color: #6c757d;">Loading...</p>
          </div>
        </div>
      </div>
    </div>
  </div>

        </div>
      </div>
    </div>
  </div>

  <!-- Bootstrap JS and dependencies for tab functionality -->
  <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.6/umd/popper.min.js"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/js/bootstrap.min.js"></script>

  <script>
    function updateSearchFields() {
      const searchType = document.getElementById("searchType").value;
      const searchFieldsDiv = document.getElementById("searchFields");
      
      let html = '';
      
      switch(searchType) {
        case 'company':
          html = '<input type="text" name="company" id="company" placeholder="Enter company" required>';
          break;
        case 'continent':
          html = `<select name="continent" id="continent" required>
            <option value="">Select continent</option>
            <option value="Africa">Africa</option>
            <option value="Asia">Asia</option>
            <option value="Europe">Europe</option>
            <option value="North America">North America</option>
            <option value="Oceania">Oceania</option>
            <option value="South America">South America</option>
          </select>`;
          break;
        case 'country':
          html = '<input type="text" name="country" id="country" placeholder="Enter country" required>';
          break;
        case 'city':
          html = '<input type="text" name="city" id="city" placeholder="Enter city" required>';
          break;
        default:
          html = '';
      }
      
      searchFieldsDiv.innerHTML = html;
    }

    // ISO-3 codes → Country Names
    const countryToISO3 = {
      "Algeria":"DZA","Angola":"AGO","Benin":"BEN","Botswana":"BWA","Burkina Faso":"BFA",
      "Burundi":"BDI","Cabo Verde":"CPV","Cameroon":"CMR","Central African Republic":"CAF","Chad":"TCD",
      "Comoros":"COM","Congo":"COG","Democratic Republic of the Congo":"COD","Côte d'Ivoire":"CIV","Djibouti":"DJI",
      "Egypt":"EGY","Equatorial Guinea":"GNQ","Eritrea":"ERI","Eswatini":"SWZ","Ethiopia":"ETH",
      "Gabon":"GAB","Gambia":"GMB","Ghana":"GHA","Guinea":"GIN","Guinea-Bissau":"GNB",
      "Kenya":"KEN","Lesotho":"LSO","Liberia":"LBR","Libya":"LBY","Madagascar":"MDG",
      "Malawi":"MWI","Mali":"MLI","Mauritania":"MRT","Mauritius":"MUS","Mayotte":"MYT",
      "Morocco":"MAR","Mozambique":"MOZ","Namibia":"NAM","Niger":"NER","Nigeria":"NGA",
      "Réunion":"REU","Rwanda":"RWA","Saint Helena":"SHN","Sao Tome and Principe":"STP","Senegal":"SEN",
      "Seychelles":"SYC","Sierra Leone":"SLE","Somalia":"SOM","South Africa":"ZAF","South Sudan":"SSD",
      "Sudan":"SDN","Tanzania":"TZA","Togo":"TGO","Tunisia":"TUN","Uganda":"UGA",
      "Western Sahara":"ESH","Zambia":"ZMB","Zimbabwe":"ZWE",
      "Åland Islands":"ALA","Albania":"ALB","Andorra":"AND","Austria":"AUT","Belarus":"BLR",
      "Belgium":"BEL","Bosnia and Herzegovina":"BIH","Bulgaria":"BGR","Croatia":"HRV","Czechia":"CZE",
      "Denmark":"DNK","Estonia":"EST","Faroe Islands":"FRO","Finland":"FIN","France":"FRA",
      "Germany":"DEU","Gibraltar":"GIB","Greece":"GRC","Guernsey":"GGY","Holy See":"VAT",
      "Hungary":"HUN","Iceland":"ISL","Ireland":"IRL","Isle of Man":"IMN","Italy":"ITA",
      "Jersey":"JEY","Latvia":"LVA","Liechtenstein":"LIE","Lithuania":"LTU","Luxembourg":"LUX",
      "Malta":"MLT","Moldova":"MDA","Monaco":"MCO","Montenegro":"MNE","Netherlands":"NLD",
      "North Macedonia":"MKD","Norway":"NOR","Poland":"POL","Portugal":"PRT","Romania":"ROU",
      "Russia":"RUS","San Marino":"SMR","Serbia":"SRB","Slovakia":"SVK","Slovenia":"SVN",
      "Spain":"ESP","Svalbard and Jan Mayen":"SJM","Sweden":"SWE","Switzerland":"CHE","Ukraine":"UKR",
      "United Kingdom":"GBR",
      "Afghanistan":"AFG","Armenia":"ARM","Azerbaijan":"AZE","Bahrain":"BHR","Bangladesh":"BGD",
      "Bhutan":"BTN","Brunei":"BRN","Cambodia":"KHM","China":"CHN","Cyprus":"CYP",
      "Georgia":"GEO","Hong Kong":"HKG","India":"IND","Indonesia":"IDN","Iran":"IRN",
      "Iraq":"IRQ","Israel":"ISR","Japan":"JPN","Jordan":"JOR","Kazakhstan":"KAZ",
      "North Korea":"PRK","South Korea":"KOR","Kuwait":"KWT","Kyrgyzstan":"KGZ","Laos":"LAO",
      "Lebanon":"LBN","Macau":"MAC","Malaysia":"MYS","Maldives":"MDV","Mongolia":"MNG",
      "Myanmar":"MMR","Nepal":"NPL","Oman":"OMN","Pakistan":"PAK","Palestine":"PSE",
      "Philippines":"PHL","Qatar":"QAT","Saudi Arabia":"SAU","Singapore":"SGP","Sri Lanka":"LKA",
      "Syria":"SYR","Tajikistan":"TJK","Thailand":"THA","Timor-Leste":"TLS","Turkey":"TUR",
      "Turkmenistan":"TKM","United Arab Emirates":"ARE","Uzbekistan":"UZB","Vietnam":"VNM","Yemen":"YEM",
      "Anguilla":"AIA","Antigua and Barbuda":"ATG","Aruba":"ABW","Bahamas":"BHS","Barbados":"BRB",
      "Belize":"BLZ","Bermuda":"BMU","Bonaire, Sint Eustatius and Saba":"BES","Canada":"CAN","Cayman Islands":"CYM",
      "Costa Rica":"CRI","Cuba":"CUB","Curaçao":"CUW","Dominica":"DMA","Dominican Republic":"DOM",
      "El Salvador":"SLV","Greenland":"GRL","Grenada":"GRD","Guadeloupe":"GLP","Guatemala":"GTM",
      "Haiti":"HTI","Honduras":"HND","Jamaica":"JAM","Martinique":"MTQ","Mexico":"MEX",
      "Montserrat":"MSR","Nicaragua":"NIC","Panama":"PAN","Puerto Rico":"PRI","Saint Barthélemy":"BLM",
      "Saint Kitts and Nevis":"KNA","Saint Lucia":"LCA","Saint Martin":"MAF","Saint Pierre and Miquelon":"SPM","Saint Vincent and the Grenadines":"VCT",
      "Sint Maarten":"SXM","Trinidad and Tobago":"TTO","Turks and Caicos Islands":"TCA","United States":"USA","British Virgin Islands":"VGB","U.S. Virgin Islands":"VIR",
      "Argentina":"ARG","Bolivia":"BOL","Bouvet Island":"BVT","Brazil":"BRA","Chile":"CHL",
      "Colombia":"COL","Ecuador":"ECU","Falkland Islands":"FLK","French Guiana":"GUF","Guyana":"GUY",
      "Paraguay":"PRY","Peru":"PER","South Georgia and the South Sandwich Islands":"SGS","Suriname":"SUR","Uruguay":"URY","Venezuela":"VEN",
      "American Samoa":"ASM","Australia":"AUS","Christmas Island":"CXR","Cocos (Keeling) Islands":"CCK","Cook Islands":"COK",
      "Fiji":"FJI","French Polynesia":"PYF","Guam":"GUM","Heard Island and McDonald Islands":"HMD","Kiribati":"KIR",
      "Marshall Islands":"MHL","Micronesia":"FSM","Nauru":"NRU","New Caledonia":"NCL","New Zealand":"NZL",
      "Niue":"NIU","Norfolk Island":"NFK","Northern Mariana Islands":"MNP","Palau":"PLW","Papua New Guinea":"PNG",
      "Pitcairn Islands":"PCN","Samoa":"WSM","Solomon Islands":"SLB","Tokelau":"TKL","Tonga":"TON",
      "Tuvalu":"TUV","U.S. Minor Outlying Islands":"UMI","Vanuatu":"VUT","Wallis and Futuna":"WLF"
    };

    // Continent → ISO-3 country lists
    const continentCountries = {
          "Africa": [
      "DZA", "AGO", "BEN", "BWA", "IOT", "BFA", "BDI", "CPV", "CMR", "CAF",
      "TCD", "COM", "COG", "COD", "CIV", "DJI", "EGY", "GNQ", "ERI", "SWZ",
      "ETH", "ATF", "GAB", "GMB", "GHA", "GIN", "GNB", "KEN", "LSO", "LBR",
      "LBY", "MDG", "MWI", "MLI", "MRT", "MUS", "MYT", "MAR", "MOZ", "NAM",
      "NER", "NGA", "REU", "RWA", "SHN", "STP", "SEN", "SYC", "SLE", "SOM",
      "ZAF", "SSD", "SDN", "TZA", "TGO", "TUN", "UGA", "ESH", "ZMB", "ZWE"],
 
    "Europe": [
      "ALA", "ALB", "AND", "AUT", "BLR", "BEL", "BIH", "BGR", "HRV", "CZE",
      "DNK", "EST", "FRO", "FIN", "FRA", "DEU", "GIB", "GRC", "GGY", "VAT",
      "HUN", "ISL", "IRL", "IMN", "ITA", "JEY", "LVA", "LIE", "LTU", "LUX",
      "MLT", "MDA", "MCO", "MNE", "NLD", "MKD", "NOR", "POL", "PRT", "ROU",
      "RUS", "SMR", "SRB", "SVK", "SVN", "ESP", "SJM", "SWE", "CHE", "UKR",
      "GBR"],
 
    "Asia": [
      "AFG", "ARM", "AZE", "BHR", "BGD", "BTN", "BRN", "KHM", "CHN", "CYP",
      "GEO", "HKG", "IND", "IDN", "IRN", "IRQ", "ISR", "JPN", "JOR", "KAZ",
      "PRK", "KOR", "KWT", "KGZ", "LAO", "LBN", "MAC", "MYS", "MDV", "MNG",
      "MMR", "NPL", "OMN", "PAK", "PSE", "PHL", "QAT", "SAU", "SGP", "LKA",
      "SYR", "TJK", "THA", "TLS", "TUR", "TKM", "ARE", "UZB", "VNM", "YEM"],
     
    "North America": [
      "AIA", "ATG", "ABW", "BHS", "BRB", "BLZ", "BMU", "BES", "CAN", "CYM",
      "CRI", "CUB", "CUW", "DMA", "DOM", "SLV", "GRL", "GRD", "GLP", "GTM",
      "HTI", "HND", "JAM", "MTQ", "MEX", "MSR", "NIC", "PAN", "PRI", "BLM",
      "KNA", "LCA", "MAF", "SPM", "VCT", "SXM", "TTO", "TCA", "USA", "VGB",
      "VIR"],
     
    "South America": [
      "ARG", "BOL", "BVT", "BRA", "CHL", "COL", "ECU", "FLK", "GUF", "GUY",
      "PRY", "PER", "SGS", "SUR", "URY", "VEN"],
       
    "Oceania": [
      "ASM", "AUS", "CXR", "CCK", "COK", "FJI", "PYF", "GUM", "HMD", "KIR",
      "MHL", "FSM", "NRU", "NCL", "NZL", "NIU", "NFK", "MNP", "PLW", "PNG",
      "PCN", "WSM", "SLB", "TKL", "TON", "TUV", "UMI", "VUT", "WLF"]
    };

    function showCustomer() {
      // Clear previous error message
      const errorMessageDiv = document.getElementById("errorMessage");
      errorMessageDiv.style.display = "none";
      errorMessageDiv.textContent = "";
      
      event.preventDefault();

      // Build query parameters
      var fields = ["company", "country", "continent", "city", "date1", "date2"];
      var inputs = {};
      for (var key = 0; key < fields.length; key++) {
        if (document.getElementById(fields[key])) {
          if (document.getElementById(fields[key]).value != "") {
            inputs[fields[key]] = document.getElementById(fields[key]).value;
          }
        }
      }
      
      // Set default dates if not provided
      const today = new Date();
      today.setHours(0, 0, 0, 0); // Reset time to midnight for accurate comparison
      
      // If no end date provided, use today
      if (!inputs["date2"]) {
        const todayStr = today.toISOString().split('T')[0];
        inputs["date2"] = todayStr;
        document.getElementById("date2").value = todayStr;
      }
      
      // If no start date provided, use 2019-01-01 (start of database)
      if (!inputs["date1"]) {
        const dbStartDate = '2019-01-01';
        inputs["date1"] = dbStartDate;
        document.getElementById("date1").value = dbStartDate;
      }
      
      // Validate dates
      const startDate = new Date(inputs["date1"]);
      const endDate = new Date(inputs["date2"]);

      // Check if end date is before start date
      if (endDate < startDate) {
        errorMessageDiv.textContent = "The ending date cannot be before the starting date.";
        errorMessageDiv.style.display = "block";
        return;
      }

      // Check if either date is after today
      if (startDate > today) {
        errorMessageDiv.textContent = "The starting date cannot be after today's date.";
        errorMessageDiv.style.display = "block";
        return;
      }

      if (endDate > today) {
        errorMessageDiv.textContent = "The ending date cannot be after today's date.";
        errorMessageDiv.style.display = "block";
        return;
      }
      
      console.log(inputs);

      const url = "transaction_V2.php?" + new URLSearchParams(inputs).toString();
      console.log(url);

      const xhttp = new XMLHttpRequest();
      xhttp.onload = function() {
        if(this.readyState === 4 && this.status === 200) {
          const temp = JSON.parse(this.responseText);
          console.log(temp);

          // Reset all tabs and content to loading state
          document.getElementById("transactionsTabsContainer").style.display = "none";
          document.getElementById("arrivingTotalDisplay").textContent = "Loading... Check if there are arriving transactions.";
          document.getElementById("arrivingTable").innerHTML = '<p style="text-align: center; padding: 20px; color: #6c757d;">Loading... Check if there are arriving transactions.</p>';
          document.getElementById("arrivingChart").innerHTML = '';
          document.getElementById("leavingTotalDisplay").textContent = "Loading... Check if there are leaving transactions.";
          document.getElementById("leavingTable").innerHTML = '<p style="text-align: center; padding: 20px; color: #6c757d;">Loading... Check if there are leaving transactions.</p>';
          document.getElementById("leavingChart").innerHTML = '';
          document.getElementById("adjustmentsTotalDisplay").textContent = "Loading... Check if there are adjustments.";
          document.getElementById("adjustmentsTable").innerHTML = '<p style="text-align: center; padding: 20px; color: #6c757d;">Loading... Check if there are adjustments.</p>';
          
          // Reset to first tab (Arriving)
          const arrivingTab = document.getElementById('arriving-tab');
          const leavingTab = document.getElementById('leaving-tab');
          const adjustmentsTab = document.getElementById('adjustments-tab');
          const arrivingPane = document.getElementById('arriving');
          const leavingPane = document.getElementById('leaving');
          const adjustmentsPane = document.getElementById('adjustments');
          
          arrivingTab.classList.add('active');
          leavingTab.classList.remove('active');
          adjustmentsTab.classList.remove('active');
          arrivingPane.classList.add('show', 'active');
          leavingPane.classList.remove('show', 'active');
          adjustmentsPane.classList.remove('show', 'active');

          // Validate company name if it was provided
          if (inputs["company"]) {
            const companyListAll = temp["Company Name List All: "];
            const enteredCompany = inputs["company"].trim();
            
            if (companyListAll && companyListAll.length > 0 && !companyListAll.includes(enteredCompany)) {
              errorMessageDiv.textContent = "Company '" + enteredCompany + "' not found in database. Please check the spelling and try again.";
              errorMessageDiv.style.display = "block";
              return;
            }
          }
          
          // Validate country name if it was provided
          if (inputs["country"]) {
            const countryListAll = temp["Country Name List All: "];
            const enteredCountry = inputs["country"].trim();
            
            if (countryListAll && countryListAll.length > 0 && !countryListAll.includes(enteredCountry)) {
              errorMessageDiv.textContent = "Country '" + enteredCountry + "' not found in database. Please check the spelling and try again.";
              errorMessageDiv.style.display = "block";
              return;
            }
          }

          // Show the tabs container after successful search
          document.getElementById("transactionsTabsContainer").style.display = "block";

          // Process Arriving Transactions
          if(temp["Arriving ID"] && temp["Arriving ID"].length > 0) {
            const arrivingCount = temp["Arriving ID"].length;
            document.getElementById("arrivingTotalDisplay").textContent = `Total Arriving Transactions: ${arrivingCount}`;
            
            // Create arriving table
            let arrivingHTML = '<div style="display: grid; grid-template-columns: 0.8fr 1.2fr 1.8fr 1.8fr 0.6fr; gap: 10px; font-weight: bold; border-bottom: 2px solid #ddd; padding-bottom: 10px; margin-bottom: 10px;">';
            arrivingHTML += '<div>Shipment ID</div><div>Date</div><div>Company</div><div>Product</div><div>Quantity</div></div>';
            
            for(let i = 0; i < arrivingCount; i++) {
              arrivingHTML += '<div style="display: grid; grid-template-columns: 0.8fr 1.2fr 1.8fr 1.8fr 0.6fr; gap: 10px; padding: 8px 10px; border-bottom: 1px solid #eee;">';
              arrivingHTML += `<div>${temp["Arriving ID"][i]}</div>`;
              arrivingHTML += `<div>${temp["Arriving Date"][i]}</div>`;
              arrivingHTML += `<div>${temp["Arriving Company"][i]}</div>`;
              arrivingHTML += `<div>${temp["Arriving Product"][i]}</div>`;
              arrivingHTML += `<div>${temp["Arriving Quantity"][i]}</div>`;
              arrivingHTML += '</div>';
            }
            document.getElementById("arrivingTable").innerHTML = arrivingHTML;
            
            // Create arriving chart - aggregate by product
            const arrivingProducts = {};
            for(let i = 0; i < arrivingCount; i++) {
              const product = temp["Arriving Product"][i];
              const quantity = parseFloat(temp["Arriving Quantity"][i]);
              arrivingProducts[product] = (arrivingProducts[product] || 0) + quantity;
            }
            
            // Sort by quantity and get top 10
            const arrivingSorted = Object.entries(arrivingProducts)
              .sort((a, b) => b[1] - a[1])
              .slice(0, 10);
            const arrivingLabels = arrivingSorted.map(item => item[0]);
            const arrivingValues = arrivingSorted.map(item => item[1]);
            
            createBarChart("arrivingChart", {
              labels: arrivingLabels,
              values: arrivingValues
            }, {
              xAxisTitle: "Product",
              yAxisTitle: "Total Quantity"
            });
          }

          // Process Leaving Transactions
          if(temp["Leaving ID"] && temp["Leaving ID"].length > 0) {
            const leavingCount = temp["Leaving ID"].length;
            document.getElementById("leavingTotalDisplay").textContent = `Total Leaving Transactions: ${leavingCount}`;
            
            // Create leaving table
            let leavingHTML = '<div style="display: grid; grid-template-columns: 0.8fr 1.2fr 1.8fr 1.8fr 0.6fr; gap: 10px; font-weight: bold; border-bottom: 2px solid #ddd; padding-bottom: 10px; margin-bottom: 10px;">';
            leavingHTML += '<div>Shipment ID</div><div>Date</div><div>Company</div><div>Product</div><div>Quantity</div></div>';
            
            for(let i = 0; i < leavingCount; i++) {
              leavingHTML += '<div style="display: grid; grid-template-columns: 0.8fr 1.2fr 1.8fr 1.8fr 0.6fr; gap: 10px; padding: 8px 10px; border-bottom: 1px solid #eee;">';
              leavingHTML += `<div>${temp["Leaving ID"][i]}</div>`;
              leavingHTML += `<div>${temp["Leaving Date"][i]}</div>`;
              leavingHTML += `<div>${temp["Leaving Company"][i]}</div>`;
              leavingHTML += `<div>${temp["Leaving Product"][i]}</div>`;
              leavingHTML += `<div>${temp["Leaving Quantity"][i]}</div>`;
              leavingHTML += '</div>';
            }
            document.getElementById("leavingTable").innerHTML = leavingHTML;
            
            // Create leaving chart - aggregate by product
            const leavingProducts = {};
            for(let i = 0; i < leavingCount; i++) {
              const product = temp["Leaving Product"][i];
              const quantity = parseFloat(temp["Leaving Quantity"][i]);
              leavingProducts[product] = (leavingProducts[product] || 0) + quantity;
            }
            
            // Sort by quantity and get top 10
            const leavingSorted = Object.entries(leavingProducts)
              .sort((a, b) => b[1] - a[1])
              .slice(0, 10);
            const leavingLabels = leavingSorted.map(item => item[0]);
            const leavingValues = leavingSorted.map(item => item[1]);
            
            createBarChart("leavingChart", {
              labels: leavingLabels,
              values: leavingValues
            }, {
              xAxisTitle: "Product",
              yAxisTitle: "Total Quantity"
            });
          }

          // Process Adjustment Transactions
          if(temp["Adjustment ID"] && temp["Adjustment ID"].length > 0) {
            const adjustmentCount = temp["Adjustment ID"].length;
            document.getElementById("adjustmentsTotalDisplay").textContent = `Total Adjustment Transactions: ${adjustmentCount}`;
            
            // Create adjustments table
            let adjustmentHTML = '<div style="display: grid; grid-template-columns: 0.8fr 1fr 1.5fr 1.5fr 1fr 1.5fr; gap: 10px; font-weight: bold; border-bottom: 2px solid #ddd; padding-bottom: 10px; margin-bottom: 10px;">';
            adjustmentHTML += '<div>Shipment ID</div><div>Date</div><div>Company</div><div>Product</div><div>Quantity</div><div>Reason</div></div>';
            
            for(let i = 0; i < adjustmentCount; i++) {
              adjustmentHTML += '<div style="display: grid; grid-template-columns: 0.8fr 1fr 1.5fr 1.5fr 1fr 1.5fr; gap: 10px; padding: 8px 10px; border-bottom: 1px solid #eee;">';
              adjustmentHTML += `<div>${temp["Adjustment ID"][i]}</div>`;
              adjustmentHTML += `<div>${temp["Adjustment Date"][i]}</div>`;
              adjustmentHTML += `<div>${temp["Adjustment Company"][i]}</div>`;
              adjustmentHTML += `<div>${temp["Adjustment Product"][i]}</div>`;
              adjustmentHTML += `<div>${temp["Adjustment Quantity"][i]}</div>`;
              adjustmentHTML += `<div>${temp["Adjustment Reason"][i]}</div>`;
              adjustmentHTML += '</div>';
            }
            document.getElementById("adjustmentsTable").innerHTML = adjustmentHTML;
          }
        }
      };

      xhttp.open("GET", url, true);
      xhttp.send();
    }
  </script>

</body>
</html>
