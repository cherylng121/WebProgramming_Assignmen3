<?php
require_once("config.php");

// Initialize variables
$contact_message = "";
$feedback_entries = [];
$error_message = "";

// Check if connection was successful
if ($conn->connect_error) {
    $error_message = "<div class='error-message'>Connection failed: " . $conn->connect_error . "</div>";
} else {
    // Handle contact form submission
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['action']) && $_POST['action'] == 'contact') {
        // Validate input
        $name = filter_input(INPUT_POST, 'name', FILTER_SANITIZE_STRING);
        $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
        $phoneNum = filter_input(INPUT_POST, 'phoneNum', FILTER_SANITIZE_STRING);
        $message = filter_input(INPUT_POST, 'message', FILTER_SANITIZE_STRING);

        // Validate required fields
        if (empty($name) || empty($email) || empty($phoneNum) || empty($message)) {
            $contact_message = "<div class='error-message'>All fields are required.</div>";
        } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $contact_message = "<div class='error-message'>Invalid email format.</div>";
        } else {
            // Prepare and execute the query
            $stmt = $conn->prepare("INSERT INTO contact_submissions (name, email, phoneNum, message) VALUES (?, ?, ?, ?)");
            if (!$stmt) {
                $contact_message = "<div class='error-message'>Error preparing statement: " . $conn->error . "</div>";
            } else {
                $stmt->bind_param("ssss", $name, $email, $phoneNum, $message);
                
                if ($stmt->execute()) {
                    $contact_message = "<div class='success-message'>Thank you! Your feedback has been submitted successfully.</div>";
                } else {
                    $contact_message = "<div class='error-message'>Error: " . $stmt->error . "</div>";
                }
                $stmt->close();
            }
        }
    }

    // Fetch all feedback entries
    $sql = "SELECT name, email, phoneNum, message, submission_date FROM contact_submissions ORDER BY submission_date DESC";
    $result = $conn->query($sql);

    if ($result) {
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                $feedback_entries[] = $row;
            }
        } else {
            $contact_message = "<div class='info-message'>No feedback entries found. Be the first to share your thoughts!</div>";
        }
    } else {
        $error_message = "<div class='error-message'>Error fetching feedback entries: " . $conn->error . "</div>";
    }
}

// Ensure $feedback_entries is always an array
if (!is_array($feedback_entries)) {
    $feedback_entries = [];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Personal Page - Group BlaBlaBla</title>
  <style>
    /* ========== GENERAL STYLING ========== */
    /* Base styling for the entire document */
    html {
        scroll-behavior: smooth;
    }
    
    body { 
        margin: 0; 
        font-family: Arial, sans-serif; 
        color: #333; 
    }

    /* ========== GRID LAYOUT STRUCTURE ========== */
    /* CSS Grid layout for the main page structure with 5 distinct areas */
    .grid-container {
      display: grid;
      grid-template-areas:
        "header header header"
        "left-menu content right-menu"
        "footer footer footer";
      grid-template-columns: 0.6fr 2fr 0.6fr;
      grid-template-rows: auto 1fr auto;
      min-height: 100vh;
    }

    /* ========== HEADER STYLING ========== */
    /* Main header area with background image */
    .header { 
      grid-area: header;
      position: relative;
      background: url('images/Background.jpeg') center/cover no-repeat; 
      height: 400px;
      padding-top: 50px; /* Add padding to account for the navigation bar */
    }
    
    /* Container for centering header content */
    .header-content {
      display: flex;
      height: 100%;
      align-items: center;
      justify-content: center;
      text-align: center;
    }
    
    /* Header title styling */
    .header h1 {
      color: white;
      font-family: 'Times New Roman', Times, serif; 
      font-size: 48px;
      text-shadow: 2px 2px 4px rgba(0,0,0,0.5);
      z-index: 1;
    }
    
    /* ========== NAVIGATION BAR STYLING ========== */
    /* Top navigation bar with semi-transparent background */
    .top-nav {
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      background-color: rgba(120, 15, 64, 0.7); /* Maroon with transparency */
      padding: 15px 0;
      z-index: 10;
      box-shadow: 0 2px 5px rgba(0,0,0,0.2);
    }
    
    /* Navigation menu container */
    .top-nav ul {
      display: flex;
      list-style-type: none;
      margin: 0;
      padding: 0 40px;
      justify-content: flex-end; /* Align items to the right */
      gap: 40px;
    }
    
    /* Navigation link styling */
    .top-nav a {
      text-decoration: none;
      color: white;
      font-weight: bold;
      transition: all 0.3s;
      padding: 8px 15px;
      border-radius: 4px;
    }
    
    /* Hover effect for navigation links */
    .top-nav a:hover {
      background-color: rgba(255, 255, 255, 0.2);
      color: white;
      text-decoration: none;
    }

    /* ========== LEFT MENU STYLING ========== */
    /* Left sidebar area */
    .left-menu {
      grid-area: left-menu;
      background-color: #f8f8f8;
      padding: 10px;
      border-right: 1px solid #eee;
    }
    
    /* ========== RIGHT MENU STYLING ========== */
    /* Right sidebar area */
    .right-menu {
      grid-area: right-menu;
      background-color: #f8f8f8;
      padding: 10px;
      border-left: 1px solid #eee;
    }
    
    /* ========== MENU SECTION STYLING (shared by both menus) ========== */
    /* Individual sections within menus */
    .menu-section {
      margin-bottom: 25px;
    }
    
    /* Menu section headings */
    .menu-section h3 {
      color: #780f40;
      border-bottom: 2px solid #780f40;
      padding-bottom: 10px;
    }
    
    /* Menu lists */
    .menu-section ul {
      list-style-type: none;
      padding-left: 10px;
    }
    
    /* Menu list items */
    .menu-section li {
      margin-bottom: 10px;
    }
    
    /* Menu links */
    .menu-section a {
      text-decoration: none;
      color: #333;
      transition: color 0.3s;
    }
    
    /* Menu link hover effect */
    .menu-section a:hover {
      color: #0056b3;
    }

    /* ========== MAIN CONTENT STYLING ========== */
    /* Main content area */
    .content {
      padding: 50px;
      grid-area: content;
    }

    /* Profile section layout */
    .profile-section {
      display: grid;
      grid-template-columns: 2fr 1fr;
      gap: 30px;
      margin-bottom: 50px;
      align-items: center;
    }
    
    /* Profile image styling */
    .profile-image {
      width: 100%;
      max-width: 250px;
      height: auto;
      border-radius: 10px;
      box-shadow: 0 4px 8px rgba(0,0,0,0.1);
      justify-self: end;
    }
    
    /* Profile text content */
    .profile-text {
      text-align: right;
      font-size: 19px;
      line-height: 1.6;
    }
    
    /* Emphasized name in profile */
    .profile-text strong {
      color: #0056b3;
      font-size: 24px;
    }

    /* ========== INTERESTS SECTION STYLING ========== */
    /* Container for shared interests */
    .interests-section {
      background: linear-gradient(to right, #fefcea, #f1da36);
      padding: 40px;
      margin-top: 30px;
      border-radius: 8px;
    }
    
    /* Interests section heading */
    .interests-section h2 {
      text-align: center;
      font-size: 35px;
      color: #222;
      margin-bottom: 30px;
    }
    
    /* Individual interest category */
    .interest-category {
      margin-bottom: 40px;
    }
    
    /* Interest category heading */
    .interest-category h3 {
      text-align: center;
      font-size: 29px;
      color: #444;
      margin-bottom: 20px;
    }
    
    /* Grid for interest images */
    .interest-images {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
      gap: 20px;
      justify-items: center;
    }
    
    /* Interest image styling */
    .interest-images img {
      width: 100%;
      max-width: 300px;
      height: auto;
      border-radius: 10px;
      box-shadow: 0 4px 8px rgba(0,0,0,0.1);
      transition: transform 0.3s;
    }
    
    /* Interest image hover effect */
    .interest-images img:hover {
      transform: scale(1.05);
    }
    
    /* Divider between interest categories */
    .divider {
      width: 60%;
      margin: 40px auto;
      border: 1px solid #aaa;
    }

    /* ========== DYNAMIC SECTIONS STYLING ========== */
    /* Contact form and guestbook sections */
    .dynamic-section {
      background-color: #f9f9f9;
      padding: 40px;
      margin: 30px 0;
      border-radius: 8px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
    }
    
    .dynamic-section h2 {
      color: #780f40;
      text-align: center;
      margin-bottom: 30px;
      font-size: 28px;
    }
    
    /* Form styling */
    .form-group {
      margin-bottom: 20px;
    }
    
    .form-group label {
      display: block;
      margin-bottom: 5px;
      font-weight: bold;
      color: #333;
    }
    
    .form-group input,
    .form-group textarea {
      width: 100%;
      padding: 12px;
      border: 1px solid #ddd;
      border-radius: 4px;
      font-size: 16px;
      box-sizing: border-box;
    }
    
    .form-group textarea {
      height: 120px;
      resize: vertical;
    }
    
    .submit-btn {
      background-color: #780f40;
      color: white;
      padding: 12px 30px;
      border: none;
      border-radius: 4px;
      cursor: pointer;
      font-size: 16px;
      transition: background-color 0.3s;
    }
    
    .submit-btn:hover {
      background-color: #5a0b30;
    }
    
    /* Message styling */
    .success-message {
      background-color: #d4edda;
      color: #155724;
      padding: 12px;
      border: 1px solid #c3e6cb;
      border-radius: 4px;
      margin-bottom: 20px;
    }
    
    .error-message {
      background-color: #f8d7da;
      color: #721c24;
      padding: 12px;
      border: 1px solid #f5c6cb;
      border-radius: 4px;
      margin-bottom: 20px;
    }
    
    /* Guestbook entries */
    .guestbook-entries {
      margin-top: 30px;
    }
    
    .guestbook-entry {
      background-color: white;
      padding: 20px;
      margin-bottom: 15px;
      border-radius: 6px;
      border-left: 4px solid #780f40;
    }
    
    .guestbook-entry .entry-header {
      font-weight: bold;
      color: #780f40;
      margin-bottom: 8px;
    }
    
    .guestbook-entry .entry-date {
      font-size: 12px;
      color: #666;
      float: right;
    }
    
    .guestbook-entry .entry-message {
      clear: both;
      line-height: 1.6;
    }

    /* ========== FOOTER STYLING ========== */
    /* Main footer area */
    .footer {
      grid-area: footer;
      display: flex; 
      flex-direction: column;
      background-color: #780f40; /* Maroon color matching the nav */
      color: white; 
      padding: 80px 50px 0px;
      align-items: center;
      font-family: 'Times New Roman', Times, serif;
      font-size: 18px;
      width: 100%;
      box-sizing: border-box;
    }
    
    /* Footer content container */
    .footer-content {
      display: flex;
      justify-content: center;
      align-items: center;
      gap: 40px;
      flex-wrap: wrap;
      margin-bottom: 40px;
    }
    
    /* University logo in footer */
    .university-logo {
      max-width: 300px;
      height: auto;
      margin-right: 40px;
    }
    
    /* Contact information styling */
    .contact {
      text-align: center;
    }
    
    /* Contact name emphasis */
    .contact strong {
      font-size: 19px;
      margin-bottom: 15px;
      display: block;
    }
    
    /* Bottom copyright footer */
    .bottom-footer {
      background-color: #333;
      color: white;
      padding: 10px 50px;
      font-size: 12px;
      text-align: center;
      width: 100%;
    }

    /* ========== RESPONSIVE DESIGN ========== */
    /* Media query for smaller screens */
    @media (max-width: 992px) {
      /* Adjust grid layout for mobile */
      .grid-container {
        grid-template-areas:
          "header"
          "left-menu"
          "content"
          "right-menu"
          "footer";
        grid-template-columns: 1fr;
      }
      
      /* Center profile sections on mobile */
      .profile-section {
        grid-template-columns: 1fr;
        text-align: center;
      }
      
      /* Center profile images on mobile */
      .profile-image {
        margin: 0 auto;
      }
      
      /* Adjust navigation for mobile */
      .top-nav {
        position: static;
        width: 100%;
        padding: 10px 0;
      }
      
      /* Center and wrap navigation on mobile */
      .top-nav ul {
        flex-wrap: wrap;
        justify-content: center;
        gap: 10px;
      }
      
      /* Smaller navigation links on mobile */
      .top-nav a {
        padding: 5px 10px;
        font-size: 14px;
      }

      /* Adjust footer for mobile */
      .footer {
        flex-direction: column;
        padding: 50px 20px;
      }

      /* Center university logo on mobile */
      .university-logo {
        margin-right: 0;
        margin-bottom: 20px;
      }
      
      /* Adjust dynamic sections for mobile */
      .dynamic-section {
        padding: 20px;
      }
    }

    /* Contact Section Styling */
    .contact-section {
      background-color: #f8f9fa;
      padding: 40px;
      border-radius: 10px;
      margin-top: 30px;
    }

    .contact-info {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
      gap: 20px;
      margin-bottom: 30px;
    }

    .contact-item {
      display: flex;
      align-items: center;
      gap: 15px;
    }

    .contact-item i {
      font-size: 24px;
      color: #780f40;
    }

    .contact-item span {
      font-size: 18px;
    }

    .contact-form {
      max-width: 600px;
      margin: 0 auto;
    }

    .form-group {
      margin-bottom: 20px;
    }

    .form-group label {
      display: block;
      margin-bottom: 5px;
      color: #333;
    }

    .form-group input,
    .form-group textarea {
      width: 100%;
      padding: 10px;
      border: 1px solid #ddd;
      border-radius: 5px;
      font-size: 16px;
    }

    .form-group textarea {
      height: 150px;
      resize: vertical;
    }

    .submit-btn {
      background-color: #780f40;
      color: white;
      padding: 12px 30px;
      border: none;
      border-radius: 5px;
      cursor: pointer;
      font-size: 16px;
      transition: background-color 0.3s;
    }

    .submit-btn:hover {
      background-color: #5a0b2f;
    }

    .success-message {
      background-color: #d4edda;
      color: #155724;
      padding: 15px;
      border-radius: 5px;
      margin-bottom: 20px;
    }

    .error-message {
      background-color: #f8d7da;
      color: #721c24;
      padding: 15px;
      border-radius: 5px;
      margin-bottom: 20px;
    }

    /* Quick Links Styling */
    .quick-links {
      background-color: #f8f9fa;
      padding: 20px;
      border-radius: 10px;
      margin: 20px 0;
    }

    .quick-links h3 {
      color: #780f40;
      margin-bottom: 15px;
    }

    .quick-links ul {
      list-style: none;
      padding: 0;
    }

    .quick-links li {
      margin-bottom: 10px;
    }

    .quick-links a {
      color: #333;
      text-decoration: none;
      display: flex;
      align-items: center;
      gap: 10px;
      transition: color 0.3s;
    }

    .quick-links a:hover {
      color: #780f40;
    }

    .quick-links i {
      color: #780f40;
    }

    /* ========== FEEDBACK SECTION STYLING ========== */
    .feedback-section {
        padding: 40px;
        background: #fff;
        border-radius: 15px;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        margin: 30px 0;
        transition: transform 0.3s ease;
    }

    .feedback-section:hover {
        transform: translateY(-5px);
    }

    .feedback-section h2 {
        color: #780f40;
        text-align: center;
        margin-bottom: 30px;
        font-size: 2.2em;
        position: relative;
        padding-bottom: 15px;
    }

    .feedback-section h2:after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 50%;
        transform: translateX(-50%);
        width: 100px;
        height: 3px;
        background: #780f40;
    }

    .feedback-form {
        background: #f8f9fa;
        padding: 30px;
        border-radius: 10px;
        margin-bottom: 40px;
    }

    .feedback-form input,
    .feedback-form textarea {
        width: 100%;
        padding: 12px;
        margin-bottom: 20px;
        border: 2px solid #e9ecef;
        border-radius: 8px;
        transition: border-color 0.3s ease;
        font-size: 16px;
    }

    .feedback-form input:focus,
    .feedback-form textarea:focus {
        border-color: #780f40;
        outline: none;
        box-shadow: 0 0 0 3px rgba(120, 15, 64, 0.1);
    }

    .feedback-form button {
        background: #780f40;
        color: white;
        padding: 12px 30px;
        border: none;
        border-radius: 8px;
        cursor: pointer;
        font-size: 16px;
        font-weight: bold;
        transition: all 0.3s ease;
        display: block;
        margin: 0 auto;
    }

    .feedback-form button:hover {
        background: #5a0b2f;
        transform: translateY(-2px);
        box-shadow: 0 5px 15px rgba(120, 15, 64, 0.2);
    }

    .feedback-list {
        margin-top: 40px;
    }

    .feedback-list h3 {
        color: #780f40;
        text-align: center;
        margin-bottom: 25px;
        font-size: 1.8em;
    }

    .feedback-item {
        background: #fff;
        padding: 20px;
        margin-bottom: 20px;
        border-radius: 10px;
        border-left: 4px solid #780f40;
        box-shadow: 0 3px 10px rgba(0,0,0,0.05);
        transition: transform 0.3s ease;
    }

    .feedback-item:hover {
        transform: translateX(10px);
    }

    .feedback-item .name {
        font-weight: bold;
        color: #780f40;
        font-size: 1.2em;
        margin-bottom: 5px;
    }

    .feedback-item .date {
        color: #6c757d;
        font-size: 0.9em;
        margin-bottom: 10px;
    }

    .feedback-item .message {
        color: #495057;
        line-height: 1.6;
        font-size: 1.1em;
    }

    .success-message {
        background: #d4edda;
        color: #155724;
        padding: 15px;
        border-radius: 8px;
        margin-bottom: 20px;
        text-align: center;
        font-weight: bold;
    }

    .error-message {
        background: #f8d7da;
        color: #721c24;
        padding: 15px;
        border-radius: 8px;
        margin-bottom: 20px;
        text-align: center;
        font-weight: bold;
    }

    .info-message {
        background: #cce5ff;
        color: #004085;
        padding: 15px;
        border-radius: 8px;
        margin-bottom: 20px;
        text-align: center;
        font-weight: bold;
    }

    /* Quick Links Styling */
    .menu-section ul li a {
        display: block;
        padding: 10px 15px;
        color: #333;
        text-decoration: none;
        transition: all 0.3s ease;
        border-radius: 5px;
    }

    .menu-section ul li a:hover {
        background: #780f40;
        color: white;
        transform: translateX(5px);
    }

    /* Debug Information */
    .debug-info {
      background-color: #f8f9fa;
      padding: 10px;
      margin: 10px 0;
      border: 1px solid #ddd;
      border-radius: 5px;
      font-family: monospace;
    }
  </style>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>
  <!-- Main grid container for the entire page layout -->
  <div class="grid-container">
    <!-- Header Section with Top Navigation -->
    <header class="header">
      <!-- Top navigation bar with transparent background -->
      <nav class="top-nav">
        <ul>
          <li><a href="#profile-led">Lim En Dhong</a></li>
          <li><a href="#profile-je">Ng Jin En</a></li>
          <li><a href="#profile-wm">Yeo Wern Min</a></li>
          <li><a href="#interests">Our Interests</a></li>
          <li><a href="#contact-form">Contact Us</a></li>
          <li><a href="#guestbook">Guestbook</a></li>
        </ul>
      </nav>
      
      <!-- Header title and welcome message -->
      <div class="header-content">
        <h1>Welcome to Group BlaBlaBla's Personal Page</h1>
      </div>
    </header>
    
    <!-- Left Menu Sidebar with academic and social links -->
    <aside class="left-menu">
      <!-- Academic resources section -->
      <div class="menu-section">
        <h3>Academic Links</h3>
        <ul>
          <li><a href="https://www.utm.my">UTM Website</a></li>
          <li><a href="https://comp.utm.my/">Faculty of Computing</a></li>
          <li><a href="https://studentportal.utm.my/login">UTM Student Portal</a></li>
          <li><a href="https://amd.utm.my/academic-calendar/">Academic Calendar</a></li>
        </ul>
      </div>
      
      <!-- Social connection section -->
      <div class="menu-section">
        <h3>Connect With Us</h3>
        <h4 style="color: lightsalmon;">Github</h4>
        <ul>
          <li><a href="https://github.com/Endhong0929">Lim En Dhong</a></li>
          <li><a href="https://github.com/cherylng121">Ng Jin En</a></li>
          <li><a href="https://github.com/WernMinYeo">Yeo Wern Min</a></li>
        </ul>
      </div>
    </aside>

    <!-- Right Menu Sidebar with quick links -->
    <aside class="right-menu">
      <div class="menu-section">
        <h3>Quick Links</h3>
        <ul>
          <li><a href="https://drive.google.com/file/d/1Rbr4nH8136GdZd3spSyDGpCvY3a8JpLW/view?usp=sharing">Assignment 1</a></li>
          <li><a href="#feedback-section">Comment</a></li>
          <li><a href="#feedback-list">Feedback</a></li>
        </ul>
      </div>
    </aside>
    
    <!-- Main Content Area with profiles and interests -->
    <main class="content">
      <!-- First Profile Section - Lim En Dhong -->
      <section id="profile-led" class="profile-section">
        <div class="profile-text">
          <p><strong>Lim En Dhong</strong>, also known as <strong>LED</strong>, is a Computer Science student at Universiti Teknologi Malaysia. She is from Kedah and is majoring in Computer Networks and Security. She is passionate about networking, cybersecurity and web development. Recently, she started learning web design to improve her frontend skills. Besides tech, she also enjoys listening to music and reading novel in her free time.</p>
        </div>
        <img src="images/EnDhong.jpg" alt="Lim En Dhong" class="profile-image">
      </section>
      
      <!-- Divider between profiles -->
      <hr style="width: 80%; margin: 30px auto; border: 1px solid #ccc;">
      
      <!-- Second Profile Section - Ng Jin En -->
      <section id="profile-je" class="profile-section">
        <div class="profile-text">
          <p><strong>Ng Jin En</strong>, can called as <strong>Cheryl</strong>, is a second-year Computer Science student at Universiti Teknologi Malaysia, specializing in Network and Security. She is from Kulai, Johor. She has a strong interest in cybersecurity, system hardening, and network analysis. She is a person who enjoy learning new things and taking on challenges.</p>
        </div>
        <img src="images/JinEn.jpg" alt="Ng Jin En" class="profile-image">
      </section>
      
      <!-- Divider between profiles -->
      <hr style="width: 80%; margin: 30px auto; border: 1px solid #ccc;">
      
      <!-- Third Profile Section - Yeo Wern Min -->
      <section id="profile-wm" class="profile-section">
        <div class="profile-text">
          <p><strong>Yeo Wern Min</strong> also known as <strong>Min</strong>, is a passionate Computer and Network Security student at Universiti Teknologi Malaysia (UTM). She is deeply interested in mobile app development and is always eager to explore new frameworks. She always welcomes challenging projects that push her skills and fuel her growth as a developer.</p>
        </div>
        <img src="images/WernMin.jpg" alt="Yeo Wern Min" class="profile-image">
      </section>
      
      <!-- Shared Interests Section -->
      <section id="interests" class="interests-section">
        <h2>Our Common Interests</h2>
        
        <!-- Food Category -->
        <div class="interest-category">
          <h3>Food</h3>
          <div class="interest-images">
            <img src="images/Food1.jpg" alt="Favorite Food 1">
            <img src="images/Food2.jpg" alt="Favorite Food 2">
          </div>
        </div>
        
        <!-- Divider between interest categories -->
        <hr class="divider">
        
        <!-- Books Category -->
        <div class="interest-category">
          <h3>Books</h3>
          <div class="interest-images">
            <img src="images/Book1.jpg" alt="Favorite Book 1">
            <img src="images/Book2.jpg" alt="Favorite Book 2">
          </div>
        </div>
        
        <!-- Divider between interest categories -->
        <hr class="divider">
        
        <!-- Movies Category -->
        <div class="interest-category">
          <h3>Movies</h3>
          <div class="interest-images">
            <img src="images/Movie1.jpg" alt="Favorite Movie 1">
            <img src="images/Movie2.jpg" alt="Favorite Movie 2">
          </div>
        </div>
        
        <!-- Divider between interest categories -->
        <hr class="divider">
        
        <!-- Hobbies Category -->
        <div class="interest-category">
          <h3>Hobbies</h3>
          <div class="interest-images">
            <img src="images/Hobby1.jpg" alt="Favorite Hobby 1">
            <img src="images/Hobby2.png" alt="Favorite Hobby 2">
          </div>
        </div>
      </section>
      
      <!-- Feedback Section -->
      <section id="feedback-section" class="feedback-section">
        <h2>Share Your Feedback</h2>
        <?php if (!empty($error_message)): ?>
            <?php echo $error_message; ?>
        <?php endif; ?>

        <?php if (!empty($contact_message)): ?>
            <?php echo $contact_message; ?>
        <?php endif; ?>

        <div class="feedback-form">
            <form method="POST" action="">
                <input type="hidden" name="action" value="contact">
                <input type="text" name="name" placeholder="Your Name" required>
                <input type="email" name="email" placeholder="Your Email" required>
                <input type="tel" name="phoneNum" placeholder="Your Phone Number" required>
                <textarea name="message" placeholder="Share your thoughts and feedback..." rows="4" required></textarea>
                <button type="submit">Submit Feedback</button>
            </form>
        </div>

        <div id="feedback-list" class="feedback-list">
            <h3>Recent Feedback (<?php echo count($feedback_entries); ?>)</h3>
            <?php if (empty($feedback_entries)): ?>
                <div class="info-message">No feedback entries yet. Be the first to share your thoughts!</div>
            <?php else: ?>
                <?php foreach ($feedback_entries as $entry): ?>
                    <div class="feedback-item">
                        <div class="name"><?php echo htmlspecialchars($entry['name']); ?></div>
                        <div class="date"><?php echo date('F j, Y, g:i a', strtotime($entry['submission_date'])); ?></div>
                        <div class="message"><?php echo htmlspecialchars($entry['message']); ?></div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
      </section>
    </main>
    
    <!-- Footer Section with contact information -->
    <footer id="contact" class="footer">
      <div class="footer-content">
        <!-- University logo -->
        <img src="images/UniLogo.png" class="university-logo" alt="University Logo">
        
        <!-- Contact info for first team member -->
        <div class="contact">
          <p><strong>LIM EN DHONG</strong><br><br>
          A23CS0239<br>
          Year 2 Network & Security<br>
          Faculty of Computing UTM<br>
          limdhong@graduate.utm.my</p>
        </div>
        
        <!-- Contact info for second team member -->
        <div class="contact">
          <p><strong>NG JIN EN</strong><br><br>
          A23CS0146<br>
          Year 2 Network & Security<br>
          Faculty of Computing UTM<br>
          ngjinen@graduate.utm.my</p>
        </div>
        
        <!-- Contact info for third team member -->
        <div class="contact">
          <p><strong>YEO WERN MIN</strong><br><br>
          A23CS0285<br>
          Year 2 Network & Security<br>
          Faculty of Computing UTM<br>
          yeomin@graduate.utm.my</p>
        </div>
      </div>  
      
      <!-- Bottom Footer with copyright -->
      <div class="bottom-footer">
        <div>&copy; Web Prog Assignment 3: Enhanced Personal Page with PHP + MySQL by Group BlaBlaBla</div>
      </div>
    </footer>
  </div>
</body>
</html>