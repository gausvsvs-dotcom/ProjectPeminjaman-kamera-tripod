<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>ProjectPeminjaman</title>
    <!-- Bootstrap -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
        rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link
        rel="stylesheet"
        href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    <style>
        /* =========================
           GLOBAL
        ========================= */
        body {
            background-color: #FFFBEB;
            font-family: Arial, sans-serif;
            margin: 0;
        }
        /* =========================
           CONTENT
        ========================= */
        .main-content {
            margin-left: 260px;
            padding: 30px;
            min-height: 100vh;
        }
        .container-fluid {
            max-width: 1400px;
        }
        /* =========================
           CARD
        ========================= */
        .card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            overflow: hidden;
}
        .card-header {
            background-color: #8174A0;
            color: white;
            border: none;
        }
        /* =========================
           TABLE
        ========================= */
        .table {
            margin-bottom: 0;
        }
        .table thead th {
            background-color: #EFB6C8;
            color: #4d405f;
            vertical-align: middle;
        }
        .table tbody tr {
            transition: 0.2s;
        }
        .table tbody tr:hover {
            background-color: #fff3f6;
        }
        /* =========================
           BUTTON
        ========================= */
        .btn-primary-custom {
            background-color: #A888B5;
            color: white;
            border: none;
        }
        .btn-primary-custom:hover {
            background-color: #8174A0;
            color: white;
        }
        /* =========================
           SIDEBAR
        ========================= */
        .sidebar {
            width: 260px;
            height: 100vh;
            position: fixed;
            left: 0;
            top: 0;

            background-color: #8174A0;
            color: white;

            z-index: 1000;

            overflow-y: auto;
        }
        /* =========================
           BRAND
        ======================== */
        .sidebar-brand {
            height: 65px;
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 0 20px;
            font-size: 17px;
            font-weight: bold;
            background-color: #A888B5;
        }
        .sidebar-brand i {
            font-size: 22px;
        }
        /* =========================
           PROFILE
        ========================= */
        .profile-box {
            text-align: center;
            padding: 15px 10px;
            border-bottom:
                1px solid rgba(255, 255, 255, 0.2);
        }
        .profile-photo {
            width: 65px;
            height: 65px;
            object-fit: cover;
            border-radius: 50%;
            border: 3px solid #EFB6C8;
            margin-bottom: 6px;
        }
        .profile-name {
            color: white;
            font-size: 15px;
            font-weight: bold;
            margin-bottom: 2px;
        }
        .profile-username {
            color: white;
            opacity: 0.75;
            font-size: 11px;
        }
        .profile-role {
            display: inline-block;
            margin-top: 5px;
            padding: 3px 10px;
            border-radius: 15px;
            background-color: #EFB6C8;
            color: #8174A0;
            font-size: 10px;
            font-weight: bold;
            text-transform: uppercase;
        }
        /* =========================
           SIDEBAR MENU
        ========================= */
        .sidebar-menu {
            padding: 10px 12px;
        }
        .menu-title {
            font-size: 10px;
            font-weight: bold;
            opacity: 0.65;
            margin: 15px 8px 6px;
            letter-spacing: 1px;
        }
        .menu-item {
            display: flex;
            align-items: center;
            gap: 11px;
            padding: 9px 12px;
            margin-bottom: 3px;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            transition: 0.2s;
            font-size: 14px;
        }

        .menu-item:hover {
            background-color: #A888B5;
            color: white;
            transform: translateX(2px);
        }
        .menu-item i {
            font-size: 16px;
            width: 20px;
        }

        /* =========================
           LOGOUT
        ========================= */
        .sidebar-bottom {
            margin-top: 15px;
            border-top:
                1px solid rgba(255, 255, 255, 0.2);
            padding-top: 8px;
        }

        .logout:hover {
            background-color: #EFB6C8;
            color: #8174A0;
        }
    </style>
</head>

<body>