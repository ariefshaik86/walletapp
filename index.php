<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
  <meta name="theme-color" content="#4a6bff">
  <meta name="description" content="EarnMore - Complete tasks and earn money">
  <meta name="google-site-verification" content="9eUznn8Vfvf11Xk0KIdyoDAlEE2kWVKX1UdGdkI_MD0" />
  <title>WalletApp</title>
  
  <!-- Favicon -->
  <link rel="icon" href="img.png.png" type="image/png">
  <link rel="apple-touch-icon" href="img.png.png">
  
  <!-- Fonts & Icons -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
  
  <!-- Styles -->
  <link rel="stylesheet" href="home.css">
  <style>
    /* Landing Page Specific Styles */
    .landing-header {
      position: fixed;
      top: 0;
      left: 0;
      right: 0;
      z-index: 1000;
      background:#1e1e2f;
      box-shadow: 0 2px 15px rgba(0, 0, 0, 0.05);
      /* padding: 10px 0; */
    }
    
    .landing-nav {
      max-width: 1200px;
      margin: 0 auto;
      padding: 0 20px;
      display: flex;
      align-items: center;
    }
    
    .logo-container {
      display: flex;
      align-items: center;
      text-decoration: none;
      gap:6px
    }
    
    .logo-img {
      height: 40px;
      width: 40px;
      border-radius:50%;
    }
    
    .logo {
      font-size: 22px;
      font-weight: 700;
      color:white;

    
    }
    
    .auth-buttons {
      display: flex;
      gap: 15px;
    }
    
    .btn {
      padding: 10px 20px;
      border-radius: 30px;
      font-weight: 500;
      text-decoration: none;
      transition: all 0.3s ease;
      font-size: 14px;
      display: inline-flex;
      align-items: center;
      justify-content: center;
    }
    
    .btn-outline {
      border: 1px solid #4a6bff;
      color: #4a6bff;
    }
    
    .btn-outline:hover {
      background: rgba(74, 107, 255, 0.1);
    }
    
    .btn-primary {
      background: linear-gradient(135deg, #4a6bff, #6c5ce7);
      color: white;
      box-shadow: 0 4px 15px rgba(74, 107, 255, 0.3);
      border: none;
    }
    
    .btn-primary:hover {
      transform: translateY(-2px);
      box-shadow: 0 6px 20px rgba(74, 107, 255, 0.4);
    }
    
    .hero {
      padding: 180px 20px 100px;
      background: linear-gradient(135deg, #f8faff 0%, #f0f4ff 100%);
      min-height: 100vh;
      display: flex;
      align-items: center;
      position: relative;
      overflow: hidden;
    }
    
    .hero::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: url('data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIxMDAlIiBoZWlnaHQ9IjEwMCUiPjxkZWZzPjxwYXR0ZXJuIGlkPSJwYXR0ZXJuIiB3aWR0aD0iNDAiIGhlaWdodD0iNDAiIHZpZXdCb3g9IjAgMCA0MCA0MCIgcGF0dGVyblVuaXRzPSJ1c2VyU3BhY2VPblVzZSIgcGF0dGVyblRyYW5zZm9ybT0icm90YXRlKDQ1KSI+PHJlY3Qgd2lkdGg9IjIwIiBoZWlnaHQ9IjIwIiBmaWxsPSJyZ2JhKDAwLDAsMCwwLjAxKSIvPjwvcGF0dGVybj48L2RlZnM+PHJlY3Qgd2lkdGg9IjEwMCUiIGhlaWdodD0iMTAwJSIgZmlsbD0idXJsKCNwYXR0ZXJuKSIvPjwvc3ZnPg==');
      opacity: 0.3;
      z-index: 0;
    }
    
    .hero-content {
      max-width: 1200px;
      margin: 0 auto;
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 60px;
      align-items: center;
      position: relative;
      z-index: 1;
    }
    
    .hero-text {
      max-width: 500px;
    }
    
    .hero h1 {
      font-size: 48px;
      font-weight: 700;
      color: #1a1a2e;
      line-height: 1.2;
      margin-bottom: 20px;
    }
    
    .hero p {
      font-size: 18px;
      color: #4a5568;
      margin-bottom: 30px;
      line-height: 1.6;
    }
    
    .hero-buttons {
      display: flex;
      gap: 15px;
      margin-top: 40px;
    }
    
    .hero-image {
      position: relative;
      animation: float 6s ease-in-out infinite;
    }
    
    .hero-image img {
      width: 100%;
      max-width: 550px;
      border-radius: 20px;
      box-shadow: 0 20px 40px rgba(0, 0, 0, 0.1);
      transform: perspective(1000px) rotateY(-10deg);
      border: 10px solid white;
    }
    
    .features {
      padding: 100px 20px;
      background: white;
    }
    
    .features-container {
      max-width: 1200px;
      margin: 0 auto;
    }
    
    .section-title {
      text-align: center;
      margin-bottom: 60px;
    }
    
    .section-title h2 {
      font-size: 36px;
      color: #1a1a2e;
      margin-bottom: 15px;
      font-weight: 700;
    }
    
    .section-title p {
      font-size: 18px;
      color: #4a5568;
      max-width: 600px;
      margin: 0 auto;
    }
    
    .features-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
      gap: 30px;
      margin-top: 50px;
    }
    
    .feature-card {
      background: #fff;
      border-radius: 15px;
      padding: 30px;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.05);
      transition: all 0.3s ease;
      border: 1px solid #e2e8f0;
    }
    
    .feature-card:hover {
      transform: translateY(-10px);
      box-shadow: 0 15px 40px rgba(0, 0, 0, 0.1);
    }
    
    .feature-icon {
      width: 60px;
      height: 60px;
      border-radius: 15px;
      display: flex;
      align-items: center;
      justify-content: center;
      margin-bottom: 25px;
      font-size: 24px;
      color: white;
      background: linear-gradient(135deg, #4a6bff, #6c5ce7);
    }
    
    .feature-card:nth-child(2) .feature-icon {
      background: linear-gradient(135deg, #00b894, #00cec9);
    }
    
    .feature-card:nth-child(3) .feature-icon {
      background: linear-gradient(135deg, #fdcb6e, #ff9f43);
    }
    
    .feature-card h3 {
      font-size: 20px;
      color: #1a1a2e;
      margin-bottom: 15px;
      font-weight: 600;
    }
    
    .feature-card p {
      color: #4a5568;
      line-height: 1.6;
      margin-bottom: 0;
    }
    
    .cta {
      padding: 100px 20px;
      background: linear-gradient(135deg, #4a6bff, #6c5ce7);
      color: white;
      text-align: center;
      position: relative;
      overflow: hidden;
    }
    
    .cta::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background: url('data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIxMDAlIiBoZWlnaHQ9IjEwMCUiPjxkZWZzPjxwYXR0ZXJuIGlkPSJwYXR0ZXJuIiB3aWR0aD0iNDAiIGhlaWdodD0iNDAiIHZpZXdCb3g9IjAgMCA0MCA0MCIgcGF0dGVyblVuaXRzPSJ1c2VyU3BhY2VPblVzZSIgcGF0dGVyblRyYW5zZm9ybT0icm90YXRlKDQ1KSI+PHJlY3Qgd2lkdGg9IjIwIiBoZWlnaHQ9IjIwIiBmaWxsPSJyZ2JhKDI1NSwyNTUsMjU1LDAuMDUpIi8+PC9wYXR0ZXJuPjwvZGVmcz48cmVjdCB3aWR0aD0iMTAwJSIgaGVpZ2h0PSIxMDAlIiBmaWxsPSJ1cmwoI3BhdHRlcm4pIi8+PC9zdmc+');
      opacity: 0.5;
      z-index: 0;
    }
    
    .cta-content {
      max-width: 700px;
      margin: 0 auto;
      position: relative;
      z-index: 1;
    }
    
    .cta h2 {
      font-size: 36px;
      margin-bottom: 20px;
      font-weight: 700;
    }
    
    .cta p {
      font-size: 18px;
      margin-bottom: 30px;
      opacity: 0.9;
      max-width: 600px;
      margin-left: auto;
      margin-right: auto;
    }
    
    .cta .btn {
      background: white;
      color: #4a6bff;
      font-weight: 600;
      padding: 15px 40px;
      font-size: 16px;
    }
    
    .cta .btn:hover {
      transform: translateY(-3px);
      box-shadow: 0 10px 25px rgba(0, 0, 0, 0.2);
    }
    
    footer {
      background: #1a1a2e;
      color: #a0aec0;
      padding: 50px 20px 20px;
      font-size: 14px;
    }
    
    .footer-content {
      max-width: 1200px;
      margin: 0 auto;
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 40px;
      margin-bottom: 40px;
    }
    
    .footer-logo {
      margin-bottom: 20px;
      display: flex;
      align-items: center;
    }
    
    .footer-logo  {
      font-size: 24px;
      background: linear-gradient(135deg, #4a6bff, #6c5ce7);
      -webkit-background-clip: text;
      background-clip: text;
      -webkit-text-fill-color: transparent;
      color: #4a6bff;
    }
    
    .footer-about p {
      margin-bottom: 20px;
      line-height: 1.6;
    }
    
    .social-links {
      display: flex;
      gap: 15px;
    }
    
    .social-link {
      width: 36px;
      height: 36px;
      border-radius: 50%;
      background: rgba(255, 255, 255, 0.1);
      display: flex;
      align-items: center;
      justify-content: center;
      color: white;
      text-decoration: none;
      transition: all 0.3s ease;
    }
    
    .social-link:hover {
      background: #4a6bff;
      transform: translateY(-3px);
    }
    
    .footer-links h3 {
      color: white;
      font-size: 18px;
      margin-bottom: 20px;
      font-weight: 600;
    }
    
    .footer-links ul {
      list-style: none;
      padding: 0;
      margin: 0;
    }
    
    .footer-links li {
      margin-bottom: 10px;
    }
    
    .footer-links a {
      color: #a0aec0;
      text-decoration: none;
      transition: all 0.3s ease;
    }
    
    .footer-links a:hover {
      color: white;
      padding-left: 5px;
    }
    
    .footer-bottom {
      max-width: 1200px;
      margin: 0 auto;
      padding-top: 20px;
      border-top: 1px solid rgba(255, 255, 255, 0.1);
      text-align: center;
    }
    
    @keyframes float {
      0% { transform: translateY(0px); }
      50% { transform: translateY(-20px); }
      100% { transform: translateY(0px); }
    }
    
    @media (max-width: 1024px) {
      .hero-content {
        grid-template-columns: 1fr;
        text-align: center;
        gap: 40px;
      }
      
      .hero-text {
        max-width: 100%;
        margin: 0 auto;
      }
      
      .hero-buttons {
        justify-content: center;
      }
      
      .hero-image {
        max-width: 80%;
        margin: 0 auto;
      }
      
      .hero h1 {
        font-size: 40px;
    
      }
    }
    
    @media (max-width: 768px) {
        #l,#s{
            font-size: 13px;
            height:36px;
            width: 70px;
        }
        
      .hero {
        padding: 150px 20px 80px;
      }
      
      .hero h1 {
        font-size: 32px;
      }
      
      .hero p {
        font-size: 16px;
      }
      
      .hero-buttons {
        flex-direction: column;
        gap: 15px;
      }
      
      .btn {
        width: 100%;
      }
      
      .section-title h2 {
        font-size: 28px;
      }
      
      .section-title p {
        font-size: 16px;
      }
      
      .features {
        padding: 60px 20px;
      }
      
      .cta h2 {
        font-size: 28px;
      }
      
      .cta p {
        font-size: 16px;
      }
    
    }
    
    @media (max-width: 480px) {
      .hero h1 {
        font-size: 28px;
      }
      
      .hero-image img {
        transform: none;
      }
      
      .feature-card {
        padding: 20px;
      }
      
      .footer-content {
        grid-template-columns: 1fr;
        text-align: center;
      }
      
      .social-links {
        justify-content: center;
      }
      
    }
  </style>
</head>
<body>
  <!-- Header -->
  <header class="landing-header">
    <div class="landing-nav">
      <a href="#" class="logo-container">
        <img src="image.png" alt="EarnMore Logo" class="logo-img">
        <span class="logo">WalletApp</span>
      </a>
      
    </div>
  </header>

  <!-- Hero Section -->
  <section class="hero">
    <div class="hero-content">
      <div class="hero-text">
        <h1>Earn Money by Completing Simple Tasks</h1>
        <p>Join thousands of users who are already earning money in their free time. Complete tasks, refer friends, and withdraw your earnings easily.</p>
        <div class="hero-buttons">
          <a href="register.php" class="btn btn-primary">Get Started for Free</a>
          <a href="#how-it-works" class="btn btn-outline">How It Works</a>
        </div>
      </div>
      <div class="hero-image">
        <img src="https://illustrations.popsy.co/white/earning-money.svg" alt="Earn Money Online">
      </div>
    </div>
  </section>

  <!-- Features Section -->
  <section class="features" id="how-it-works">
    <div class="features-container">
      <div class="section-title">
        <h2>How It Works</h2>
        <p>Start earning in just a few simple steps</p>
      </div>
      <div class="features-grid">
        <div class="feature-card">
          <div class="feature-icon">
            <i class="fas fa-user-plus"></i>
          </div>
          <h3>Create an Account</h3>
          <p>Sign up for free in less than a minute and verify your account to get started.</p>
        </div>
        <div class="feature-card">
          <div class="feature-icon">
            <i class="fas fa-tasks"></i>
          </div>
          <h3>Complete Tasks</h3>
          <p>Browse and complete various tasks like watching videos, taking surveys, or downloading apps.</p>
        </div>
        <div class="feature-card">
          <div class="feature-icon">
            <i class="fas fa-wallet"></i>
          </div>
          <h3>Earn & Withdraw</h3>
          <p>Get paid for every completed task and withdraw your earnings directly to your bank account or e-wallet.</p>
        </div>
      </div>
    </div>
  </section>

  <!-- CTA Section -->
  <section class="cta">
    <div class="cta-content">
      <h2>Ready to Start Earning?</h2>
      <p>Join WalletApp today and start making money in your free time. No experience required!</p>
      <a href="login.php" class="btn">Sign Up Now</a>
    </div>
  </section>

  <!-- Footer -->
  <footer>
    <div class="footer-content">
      <div class="footer-about">
        <div class="footer-logo">
          <span class="logo">WalletApp</span>
        </div>
        <p>EarnMore helps you make money by completing simple tasks in your free time. Join our community today!</p>
        <div class="social-links">
          <a href="#" class="social-link"><i class="fab fa-facebook-f"></i></a>
          <a href="#" class="social-link"><i class="fab fa-twitter"></i></a>
          <a href="#" class="social-link"><i class="fab fa-instagram"></i></a>
          <a href="#" class="social-link"><i class="fab fa-linkedin-in"></i></a>
        </div>
      </div>
      <div class="footer-links">
        <h3>Quick Links</h3>
        <ul>
          <li><a href="index.php">Home</a></li>
          <li><a href="#how-it-works">How It Works</a></li>
          <li><a href="login.php">Login</a></li>
          <li><a href="register.php">Register</a></li>
          <li><a href="admin_login.php">admin login</a></li>

        </ul>
      </div>
      <div class="footer-links">
        <h3>Support</h3>
        <ul>
          <li><a href="#">FAQ</a></li>
          <li><a href="#">Contact Us</a></li>
          <li><a href="#">Privacy Policy</a></li>
          <li><a href="#">Terms of Service</a></li>
        </ul>
      </div>
    </div>
    <div class="footer-bottom">
      <p>&copy; 2025 WalletApp. All rights reserved.</p>
    </div>
  </footer>

  <script>
    // Smooth scrolling for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
      anchor.addEventListener('click', function (e) {
        e.preventDefault();
        
        const targetId = this.getAttribute('href');
        if (targetId === '#') return;
        
        const targetElement = document.querySelector(targetId);
        if (targetElement) {
          window.scrollTo({
            top: targetElement.offsetTop - 100,
            behavior: 'smooth'
          });
        }
      });
    });
    
    // Check if user is logged in and update navigation
    document.addEventListener('DOMContentLoaded', function() {
      const currentUser = JSON.parse(localStorage.getItem('currentUser'));
      const authButtons = document.querySelector('.auth-buttons');
      
      if (currentUser) {
        // User is logged in, redirect to dashboard
        window.location.href = 'home.html';
      }
    });
  </script>
</body>
</html>
