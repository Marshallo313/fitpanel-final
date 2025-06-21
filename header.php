<!DOCTYPE html>
<html lang="pl">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title><?= $pageTitle ?? 'Twoja aplikacja' ?></title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <style>
    .card, .btn {
      cursor: pointer;
      transition: all 0.3s ease;
    }

    .card:hover {
      transform: translateY(-6px);
      box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
    }

    .btn:hover {
      transform: scale(1.05);
    }

    .container {
      opacity: 0;
      animation: fadeIn 0.7s forwards;
    }

    @keyframes fadeIn {
      to {
        opacity: 1;
      }
    }

    .list-group-item:hover {
      background-color: #f0f8ff;
      transition: background-color 0.3s ease;
    }
  </style>
</head>
<body class="bg-light">
<?php include "navbar.php"; ?>
<div class="container py-5">
