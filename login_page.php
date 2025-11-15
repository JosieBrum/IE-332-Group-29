<?php
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);
session_start();

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim(filter_input(INPUT_POST, 'username', FILTER_SANITIZE_STRING) ?? '');
    $password = trim(filter_input(INPUT_POST, 'password', FILTER_SANITIZE_STRING) ?? '');

    // TODO: replace this with real authentication (DB, LDAP, etc.)
    // Example: accept any user whose password equals "password"
    if ($username !== '' && $password === 'password') {
        $_SESSION['user'] = $username;
        header('Location: protected.php'); // change to your target page
        exit;
    } else {
        $error = 'Invalid username or password';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Group 29 — ERP Assignment</title>
  <style>
    :root { font-family: system-ui, -apple-system, Segoe UI, Roboto, Arial, sans-serif; }
    body { margin: 0; background: #0f172a; color: #e5e7eb; }
    .wrap { max-width: 1000px; margin: 0 auto; padding: 48px 20px 64px; }
    h1 { margin: 0 0 8px; font-size: clamp(2rem, 4.5vw, 3rem); text-align: center; }
    .subtitle { text-align: center; color: #9ca3af; margin-bottom: 28px; line-height: 1.5; }
    .card {
      background: #111827; border: 1px solid #1f2937; border-radius: 18px; padding: 20px;
      box-shadow: 0 10px 30px rgba(0,0,0,.25);
    }
    form { display: grid; gap: 12px; margin-bottom: 26px; }
    input[type="text"], input[type="password"]{
      width: 100%; padding: 12px 14px; border-radius: 10px; border: 1px solid #374151;
      background: #0b1220; color: #e5e7eb;
    }
    input[type="submit"]{
      padding: 12px 14px; border-radius: 10px; border: 0; cursor: pointer;
      background: #2563eb; color: white; font-weight: 600;
    }
    input[type="submit"]:hover { filter: brightness(1.05); }
    .grid {
      display: grid; gap: 16px;
      grid-template-columns: repeat(3, minmax(0, 1fr));
    }
    .person {
      border: 1px solid #1f2937; border-radius: 14px; overflow: hidden; background: #0b1220;
    }
    .person header {
      padding: 10px 14px; font-weight: 600; color: #cbd5e1; background: #0f172a;
      border-bottom: 1px solid #1f2937; text-align: center;
    }
    .person .img-wrap { aspect-ratio: 4/3; display: grid; place-items: center; }
    .person img { width: 100%; height: 100%; object-fit: cover; display: block; }
    .placeholder {
      color: #64748b; font-size: .95rem; text-align: center; padding: 28px 10px;
    }
    .error { color: #ff6b6b; text-align:center; margin-bottom:8px; }
    @media (max-width: 820px) { .grid { grid-template-columns: repeat(2, 1fr); } }
    @media (max-width: 520px) { .grid { grid-template-columns: 1fr; } }
  </style>
</head>
<body>
  <div class="wrap">
    <h1>Group 9</h1>
    <p class="subtitle">This assignment reviews ERP systems .....</p>

    <div class="card">
      <!-- form posts to this same page -->
      <?php if ($error): ?>
        <div class="error"><?= htmlspecialchars($error) ?></div>
      <?php endif; ?>

      <form method="post" action="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>">
        <input type="text" name="username" placeholder="Enter Username" required value="<?= isset($username) ? htmlspecialchars($username) : '' ?>">
        <input type="password" name="password" placeholder="Enter Password" required>
        <input type="submit" name="submit-btn" value="submit">
      </form>

      <div class="grid">
        <!-- Row 1 -->
        <figure class="person">
          <header>Joaquin</header>
          <div class="img-wrap">
            <img src="images/joaquin.png" alt="Joaquin headshot placeholder" onerror="this.replaceWith(makePh());">
          </div>
        </figure>

        <figure class="person">
          <header>Ongshu</header>
          <div class="img-wrap">
            <img src="images/ongshu.png" alt="Ongshu headshot placeholder" onerror="this.replaceWith(makePh());">
          </div>
        </figure>

        <figure class="person">
          <header>Ibrahim</header>
          <div class="img-wrap">
            <img src="images/ibrahim.png" alt="Ibrahim headshot placeholder" onerror="this.replaceWith(makePh());">
          </div>
        </figure>

        <!-- Row 2 -->
        <figure class="person">
          <header>Sumnima</header>
          <div class="img-wrap">
            <img src="images/sumnima.png" alt="Sumnima headshot placeholder" onerror="this.replaceWith(makePh());">
          </div>
        </figure>

        <figure class="person">
          <header>Josephine</header>
          <div class="img-wrap">
            <img src="images/josephine.png" alt="Josephine headshot placeholder" onerror="this.replaceWith(makePh());">
          </div>
        </figure>

        <figure class="person">
          <header>Jeffrey</header>
          <div class="img-wrap">
            <img src="images/jeffrey.png" alt="Jeffrey headshot placeholder" onerror="this.replaceWith(makePh());">
          </div>
        </figure>
      </div>
    </div>
  </div>

  <script>
    // Lightweight placeholder if a .png is missing
    function makePh(){
      const ph = document.createElement('div');
      ph.className = 'placeholder';
      ph.textContent = 'PNG placeholder (add your image in /images)';
      return ph;
    }
  </script>
</body>
</html>
