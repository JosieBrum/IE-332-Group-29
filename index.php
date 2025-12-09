<!DOCTYPE html>
<html>
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/@picocss/pico@2/css/pico.min.css">
  <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/css/bootstrap.min.css"/>
  <link href="https://fonts.googleapis.com/css?family=Montserrat" rel="stylesheet"/>
  <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css"/>
  <link rel="stylesheet" href="styles.css?v=2" />

  <style>
    #wrapper {
      padding-bottom: 50px;
    }
    
    .login-container {
      max-width: 450px;
      margin: 60px auto;
    }
    
    .login-box {
      background-color: #fff;
      padding: 40px;
      box-shadow: 0px 1px 15px 1px rgba(69, 65, 78, 0.08);
      border-radius: 5px;
    }
    
    .login-box h4 {
      margin-bottom: 30px;
      text-align: center;
    }
    
    .team-section {
      margin-top: 80px;
      margin-bottom: 60px;
    }
    
    .team-section h3 {
      text-align: center;
      margin-bottom: 50px;
    }
    
    .team-member {
      text-align: center;
      margin-bottom: 30px;
      padding: 0 5px;
    }
    
    .team-member-photo {
      width: 120px;
      height: 120px;
      border-radius: 0;
      object-fit: cover;
      margin: 0 auto 15px;
      display: block;
      background-color: #e0e0e0;
      border: 4px solid #999;
    }
    
    .team-member h5 {
      margin-bottom: 5px;
      font-weight: 600;
      font-size: 15px;
    }
    
    .team-member p {
      color: #999;
      font-size: 14px;
      margin-bottom: 0;
    }
  </style>

  <title>Login Page</title>
</head>

<body>
  <div id="wrapper">
    <div class="content-area">
      <div class="container-fluid">

        <!-- Navbar -->
        <nav class="navbar navbar-light" style="display: flex; justify-content: space-between; align-items: center;">
          <!-- Logo -->
          <div class="navbar">
            <img src="logo.png" alt="INSERT LOGO HERE" style="max-height: 50px; max-width: 200px; height: auto;" />
          </div>
        </nav>

        <!-- Login Form and Team Section Side by Side -->
        <div class="row mt-5">
          <!-- Left Side: Login Form -->
          <div class="col-md-5 pl-5">
            <div class="login-box box shadow">
              <h4>Welcome Back</h4>
              <form onsubmit="handleLogin()">
                <div class="form-group">
                  <label for="username">Username</label>
                  <input type="text" class="form-control" id="username" placeholder="Enter username" required>
                </div>
                <div class="form-group">
                  <label for="password">Password</label>
                  <input type="password" class="form-control" id="password" placeholder="Enter password" required>
                </div>
                <button type="submit" class="btn btn-primary btn-block" style="background-color: #007bff; border-color: #007bff;">Login</button>
                <div id="loginMessage" class="text-center mt-3" style="color: red;"></div>
              </form>
            </div>
          </div>

          <!-- Right Side: Team Photos in 3 Columns -->
          <div class="col-md-7">
            <h4 class="text-center mb-4">Meet the Team</h4>
            <div class="row">
              <!-- Team Member 1 -->
              <div class="col-md-4">
                <div class="team-member">
                                    <!--<img src="https://via.placeholder.com/120" alt="Team Member 6" class="team-member-photo">-->
                  <h5>Josephine Brumfield</h5>
                </div>
              </div>

              <!-- Team Member 2 -->
              <div class="col-md-4">
                <div class="team-member">
                                    <!--<img src="https://via.placeholder.com/120" alt="Team Member 6" class="team-member-photo">-->
                  <h5>Ongshu Dutta</h5>
                </div>
              </div>

              <!-- Team Member 3 -->
              <div class="col-md-4">
                <div class="team-member">
                                    <!--<img src="https://via.placeholder.com/120" alt="Team Member 6" class="team-member-photo">-->
                  <h5>Sumnima Bhandari</h5>
                </div>
              </div>

              <!-- Team Member 4 -->
              <div class="col-md-4">
                <div class="team-member">
                                      <!--<img src="https://via.placeholder.com/120" alt="Team Member 6" class="team-member-photo">-->
                  <h5>Joaquin Garza</h5>
                </div>
              </div>

              <!-- Team Member 5 -->
              <div class="col-md-4">
                <div class="team-member">
                  <!--<img src="https://via.placeholder.com/120" alt="Team Member 6" class="team-member-photo">-->
                  <h5>Ibrahim Abdeen</h5>
                </div>
              </div>

              <!-- Team Member 6 -->
              <div class="col-md-4">
                <div class="team-member">
                  <!--<img src="https://via.placeholder.com/120" alt="Team Member 6" class="team-member-photo">-->
                  <h5>Jeffery Michael Baur</h5>
                </div>
              </div>
            </div>
          </div>
        </div>

        <script>
          function handleLogin() {
            event.preventDefault();
            
            const username = document.getElementById("username").value.trim();
            const password = document.getElementById("password").value;
            const loginMessage = document.getElementById("loginMessage");
            
            // Clear previous message
            loginMessage.textContent = "";
            
            // Create the query string
            const queryString = username + "|" + password;
            console.log(queryString);
            // Make the request to PHP
            var xhttp = new XMLHttpRequest();
            xhttp.onload = function() {
              if (this.readyState == 4 && this.status == 200) {
                var response = JSON.parse(this.responseText);
                console.log(response);
                
                if (response === "Login successful!") {
                  // Redirect to company info page on success
                  window.location.href = "company_info_page_josie_v5.php";
                } else {
                  // Display error message
                  loginMessage.textContent = response;
                  loginMessage.style.color = "red";
                }
              }
            };
            
            var url = "login_page_check.php?l=" + encodeURIComponent(queryString);
            xhttp.open("GET", url, true);
            xhttp.send();
          }
        </script>

      </div>
    </div>
  </div>

  <!-- Bootstrap JS -->
  <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.6/umd/popper.min.js"></script>
  <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.2.1/js/bootstrap.min.js"></script>
</body>
</html>
