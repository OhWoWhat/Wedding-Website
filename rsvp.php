<?php
// --- PHP Logic ---
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "wedding_rsvp";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
  die("Connection failed: " . $conn->connect_error);
}

$success = "";
$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $name = $_POST['name'];
  $email = $_POST['email'];
  $attendance = $_POST['attendance'];
  $message = $_POST['message'];

  $check_sql = "SELECT id FROM rsvp WHERE email = ?";
  $stmt = $conn->prepare($check_sql);
  $stmt->bind_param("s", $email);
  $stmt->execute();
  $stmt->store_result();

  if ($stmt->num_rows > 0) {
    $error = "You've already RSVP'd with this email address.";
  } else {
    $sql = "INSERT INTO rsvp (name, email, attendance, message) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $name, $email, $attendance, $message);

    if ($stmt->execute()) {
      $success = "Thank you for your RSVP!";
    } else {
      $error = "Error: " . $stmt->error;
    }
  }

  $stmt->close();
  $conn->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>RSVP | Ferdinand and Winniechris</title>
  
  <link href="https://cdn.jsdelivr.net/npm/tailwindcss@2.2.19/dist/tailwind.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Great+Vibes&family=Open+Sans&display=swap" rel="stylesheet">

  <style>
    h1, h2, h3 {
      font-family: 'Great Vibes', cursive;
      color: #5e2129;
    }
    body {
      font-family: 'Open Sans', sans-serif;
      background-color: #fff8f2;
      color: #5e2129;
    }
    .content-font {
      font-family: 'Cinzel', serif;
      font-weight: 700;
      text-transform: uppercase;
      letter-spacing: 0.5px;
    }
    .bg-primary {
      background-color: #fdf2ec;
    }
    .text-primary {
      color: #5e2129;
    }
    .btn-primary {
      background-color: #b85763;
      color: white;
    }
    .btn-primary:hover {
      background-color: #9b3f4d;
    }
  </style>
  <link rel="icon" href="images/favicon.png" type="image/png">
</head>

<body class="pt-16">

<!-- Header -->
<header class="bg-white shadow-md fixed w-full z-50 top-0 left-0">
  <div class="max-w-7xl mx-auto px-4 py-4 flex justify-between items-center">
    <h1 class="text-xl">F&W Wedding</h1>
    <div class="md:hidden">
      <button id="menu-button" class="text-primary focus:outline-none">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2"
             viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round">
          <path d="M4 6h16M4 12h16M4 18h16" />
        </svg>
      </button>
    </div>
    <nav class="hidden md:flex space-x-4 text-sm sm:text-base font-medium">
      <a href="index.html" class="text-gray-700 hover:text-primary">Home</a>
      <a href="details.html" class="text-gray-700 hover:text-primary">Wedding Details</a>
      <a href="entourage.html" class="text-gray-700 hover:text-primary">Entourage</a>
      <a href="faqs.html" class="text-gray-700 hover:text-primary">FAQs</a>
      <a href="gallery.html" class="text-gray-700 hover:text-primary">Gallery</a>
      <a href="rsvp.php" class="text-primary font-semibold">RSVP</a>
    </nav>
  </div>

  <div id="mobile-menu" class="md:hidden hidden px-4 pb-4 font-medium text-sm sm:text-base">
    <a href="index.html" class="block py-2 text-gray-700 hover:text-primary">Home</a>
    <a href="details.html" class="block py-2 text-gray-700 hover:text-primary">Wedding Details</a>
    <a href="entourage.html" class="block py-2 text-gray-700 hover:text-primary">Entourage</a>
    <a href="faqs.html" class="block py-2 text-gray-700 hover:text-primary">FAQs</a>
    <a href="gallery.html" class="block py-2 text-gray-700 hover:text-primary">Gallery</a>
    <a href="rsvp.php" class="block py-2 text-primary font-semibold">RSVP</a>
  </div>
</header>

<!-- RSVP Form Section -->
<section class="min-h-screen flex items-center justify-center bg-primary py-12 px-6">
  <div class="max-w-2xl w-full bg-white rounded-2xl shadow-xl p-8">
    <div class="text-center mb-6">
      <h2 class="text-4xl mb-2">RSVP</h2>
      <p class="text-lg text-primary">Please let us know if you can celebrate with us!</p>
    </div>

    <?php if (!empty($error)): ?>
      <p class="text-red-600 text-center font-semibold mb-4"><?php echo $error; ?></p>
    <?php endif; ?>

    <?php if (empty($success)): ?>
    <form action="rsvp.php" method="POST" class="space-y-4">
      <input type="text" name="name" placeholder="Full Name" required
        class="w-full px-5 py-3 border border-gray-300 rounded-full focus:outline-none focus:ring-2 focus:ring-pink-300"/>

      <input type="email" name="email" placeholder="Email Address" required
        class="w-full px-5 py-3 border border-gray-300 rounded-full focus:outline-none focus:ring-2 focus:ring-pink-300"/>

      <select name="attendance" required
        class="w-full px-5 py-3 border border-gray-300 rounded-full focus:outline-none focus:ring-2 focus:ring-pink-300">
        <option value="">Will you attend?</option>
        <option value="Will Attend">Will Attend</option>
        <option value="Will Not Attend">Will Not Attend</option>
      </select>

      <textarea name="message" rows="4" placeholder="Your Message (Optional)"
        class="w-full px-5 py-3 border border-gray-300 rounded-2xl focus:outline-none focus:ring-2 focus:ring-pink-300"></textarea>

      <button type="submit" class="btn-primary w-full py-3 rounded-full text-lg font-semibold transition duration-300 hover:shadow-lg hover:scale-105">
        Submit RSVP
      </button>
    </form>
    <?php endif; ?>
  </div>
</section>

<!-- Footer -->
<footer class="bg-primary text-center py-6">
  <p class="text-primary content-font">&copy; 2025 Ferdinand & Winniechris | All Rights Reserved</p>
</footer>

<!-- Mobile Menu Script -->
<script>
  const menuBtn = document.getElementById('menu-button');
  const mobileMenu = document.getElementById('mobile-menu');

  menuBtn.addEventListener('click', () => {
    mobileMenu.classList.toggle('hidden');
  });
</script>

<!-- Success Modal Script & Animation -->
<?php if (!empty($success)): ?>
<style>
@keyframes fade-in {
  from { opacity: 0; transform: scale(0.95); }
  to { opacity: 1; transform: scale(1); }
}
.animate-fade-in {
  animation: fade-in 0.3s ease-out;
}
</style>

<div id="successModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
  <div class="bg-white p-6 rounded-xl shadow-xl text-center max-w-sm w-full animate-fade-in">
    <h2 class="text-2xl font-bold mb-4 text-primary">🎉 RSVP Successful!</h2>
    <p class="mb-4"><?php echo $success; ?></p>
    <button onclick="document.getElementById('successModal').style.display='none'"
      class="mt-2 px-4 py-2 rounded-full bg-pink-600 text-white hover:bg-pink-700 transition">
      Close
    </button>
  </div>
</div>
<?php endif; ?>

</body>
</html>
