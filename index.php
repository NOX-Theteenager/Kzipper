<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <!-- Favicon -->
  <link rel="icon" href="Images/Logo_zipper.png">
  <title>Keyce Zipper</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <style>
    /* Animation de fade-in */
    @keyframes fadeIn {
      0% {
        opacity: 0;
        transform: translateY(20px);
      }
      100% {
        opacity: 1;
        transform: translateY(0);
      }
    }

    /* Application de l'animation fade-in */
    .fade-in {
      animation: fadeIn 1s ease-out;
      animation-fill-mode: both;
    }

    .fade-in-logo {
      animation-delay: 0.2s;
    }
    .fade-in-title {
      animation-delay: 0.4s;
    }
    .fade-in-text {
      animation-delay: 0.6s;
    }
    .fade-in-button {
      animation-delay: 0.8s;
    }

    body {
      background: #f5f7fa;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      margin: 0;
      padding: 0;
    }

    .jumbotron {
      background: linear-gradient(135deg, #007bff, #09c052);
      color: white;
      text-align: center;
      padding: 8rem 3rem;
      display: flex;
      flex-direction: column;
      justify-content: center;
      align-items: center;
      height: 100vh;
    }

    .jumbotron img {
      max-width: 300px;
      height: auto;
    }

    h1 {
      font-size: 2.5rem;
      font-weight: bold;
      margin: 1rem 0;
      text-transform: uppercase;
    }

    p {
      font-size: 1.2rem;
      line-height: 1.6;
      max-width: 800px;
      margin: 0 auto 1.5rem;
    }

    .header-btn {
      background: white;
      color: #007bff;
      border: none;
      border-radius: 30px;
      padding: 0.75rem 1.5rem;
      font-size: 1.2rem;
      font-weight: bold;
      text-transform: uppercase;
      transition: background-color 0.3s ease, color 0.3s ease;
    }

    .header-btn:hover {
      background: #0056b3;
      color: white;
    }

    .footer {
      background: #007bff;
      color: white;
      text-align: center;
      padding: 1rem 0;
      font-size: 0.9rem;
    }

    /* Responsiveness */
    @media (max-width: 768px) {
      .jumbotron {
        padding: 4rem 2rem;
      }

      h1 {
        font-size: 2rem;
      }

      p {
        font-size: 1rem;
      }

      .header-btn {
        font-size: 1rem;
        padding: 0.6rem 1.2rem;
      }
    }

    @media (max-width: 480px) {
      .jumbotron {
        padding: 3rem 1rem;
      }

      h1 {
        font-size: 1.8rem;
      }

      p {
        font-size: 0.9rem;
      }

      .header-btn {
        font-size: 0.9rem;
      }
    }
  </style>
</head>
<body>
  <header class="jumbotron">
    <div>
      <img src="Images/Logo_zipper.png" alt="Logo" class="img-fluid fade-in fade-in-logo">
      <h1 class="fade-in fade-in-title">Keyce Zipper</h1>
      <p class="fade-in fade-in-text">
        Une application totalement gratuite conçue par des ingénieurs expérimentés pour répondre à vos besoins 
        en matière de compression et de décompression de fichiers en toute sécurité. 
        Bienvenue sur notre plateforme !
      </p>
      <a href="Zipper_page.php" class="btn header-btn fade-in fade-in-button">Commencer</a>
    </div>
  </header>

  <footer class="footer fade-in">
    <p>&copy; 2024 Nox_Theteenager. Tous droits réservés.</p>
  </footer>

  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
