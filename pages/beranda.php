<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/auth.php';

$totalLomba = 14;
$cabangLomba = 14;
$pesertaTerdaftar = (int) (db_fetch('SELECT COUNT(*) AS total FROM users WHERE role = ?', [ROLE_PESERTA])['total'] ?? 0);
$finalisTerpilih = (int) (db_fetch('SELECT COUNT(*) AS total FROM finalists')['total'] ?? 0);
$competitions = db_fetch_all('SELECT id, nama_lomba, jenis, deskripsi FROM competitions WHERE is_active = 1 ORDER BY id ASC LIMIT 6');
$schedules = db_fetch_all('SELECT event_name, event_date, event_time, location FROM schedules WHERE is_public = 1 ORDER BY event_date ASC, event_time ASC LIMIT 4');
$announcements = db_fetch_all('SELECT id, title, type, published_at FROM announcements WHERE is_published = 1 ORDER BY published_at DESC LIMIT 3');
?>
<!DOCTYPE html>
<html lang="id" class="scroll-smooth">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="theme-color" content="#0f172a">
    <title>PESOMA 2026 - <?= e(APP_NAME) ?></title>
    <meta name="description" content="Portal resmi PESOMA 2026 UIN Prof. K.H. Saifuddin Zuhri Purwokerto untuk pendaftaran, upload karya, penjurian, jadwal, finalis, dan pemenang.">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600&family=Plus+Jakarta+Sans:wght@500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary: #1a9d6e;
            --primary-dark: #0f7a52;
            --primary-light: #2fb87f;
            --accent: #c99a2e;
            --accent-light: #f3c969;
            --bg-primary: #f5f8f6;
            --bg-secondary: #fbfdfb;
            --text-primary: #132019;
            --text-secondary: #647268;
            --border: #dfe8e2;
            --shadow-sm: 0 2px 8px rgba(15, 81, 50, .08);
            --shadow-md: 0 8px 24px rgba(15, 81, 50, .12);
            --shadow-lg: 0 24px 70px rgba(15, 81, 50, .14);
            --shadow-xl: 0 34px 90px rgba(7, 53, 31, .24);
            --transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }

        * {
            box-sizing: border-box;
        }

        html {
            scroll-behavior: smooth;
        }

        body {
            margin: 0;
            font-family: "Plus Jakarta Sans", system-ui, -apple-system, "Segoe UI", Arial, sans-serif;
            font-size: 15px;
            color: var(--text-primary);
            background:
                radial-gradient(circle at top left, rgba(243, 201, 105, .18), transparent 24rem),
                linear-gradient(180deg, #fcfdfc 0%, #f4f8f6 52%, #eef5f1 100%);
            line-height: 1.65;
        }

        body::before {
            content: "";
            position: fixed;
            inset: 0;
            pointer-events: none;
            background: linear-gradient(180deg, rgba(255, 255, 255, .22), transparent 28%);
        }

        a {
            color: inherit;
            text-decoration: none;
        }

        .container {
            width: min(1160px, calc(100% - 36px));
            margin: auto;
        }

        .header {
            position: sticky;
            top: 0;
            z-index: 20;
            background: rgba(255, 255, 255, .86);
            border-bottom: 1px solid rgba(15, 81, 50, .08);
            box-shadow: 0 10px 26px rgba(15, 81, 50, .05);
            backdrop-filter: blur(14px);
        }

        .nav {
            min-height: 70px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 40px;
            padding: 0 24px;
            position: relative;
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 12px;
            color: var(--primary);
            font-weight: 900;
            font-size: 15px;
            letter-spacing: -.01em;
            white-space: nowrap;
            flex-shrink: 0;
        }

        .brand-mark {
            width: 48px;
            height: 48px;
            border-radius: 12px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            background: linear-gradient(135deg, #0f5132, #1a7c52);
            color: #fff;
            font-size: 24px;
            box-shadow: 0 8px 18px rgba(15, 81, 50, .18);
            flex-shrink: 0;
        }

        .brand-text {
            display: flex;
            flex-direction: column;
            line-height: 1.2;
        }

        .brand-text small {
            color: var(--text-secondary);
            font-size: 11px;
            font-weight: 700;
            letter-spacing: .02em;
        }

        .menu {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 32px;
            flex-wrap: nowrap;
            flex: 1;
        }

        .menu-toggle {
            display: none;
            width: 40px;
            height: 40px;
            border: 0;
            border-radius: 10px;
            background: transparent;
            color: var(--primary);
            font-size: 22px;
            font-weight: 900;
            cursor: pointer;
            transition: var(--transition);
        }

        .menu-toggle:hover {
            background: #e6f4ec;
        }

        .menu a:not(.btn) {
            padding: 8px 0;
            border-radius: 0;
            color: #111827;
            font-size: 15px;
            font-weight: 500;
            transition: var(--transition);
            white-space: nowrap;
            background: transparent;
            position: relative;
        }

        .menu a:not(.btn)::after {
            content: "";
            position: absolute;
            bottom: -4px;
            left: 0;
            right: 0;
            height: 2px;
            background: #2563eb;
            transform: scaleX(0);
            transition: transform 0.3s ease;
        }

        .menu a:hover:not(.btn),
        .menu .active {
            background: transparent;
            color: #2563eb;
        }

        .menu a:hover:not(.btn)::after,
        .menu .active::after {
            transform: scaleX(1);
        }

        .btn {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            min-height: 40px;
            padding: 10px 24px;
            border-radius: 999px;
            background: transparent;
            color: #111827 !important;
            font-weight: 700;
            font-size: 14px;
            box-shadow: none;
            transition: var(--transition);
            border: 2px solid #111827;
            cursor: pointer;
        }

        .btn:hover {
            background: #111827;
            color: #fff !important;
            transform: none;
            box-shadow: none;
        }

        .btn:active {
            transform: none;
        }

        .btn.secondary {
            background: #111827;
            color: #fff !important;
            border: 2px solid #111827;
            box-shadow: none;
            padding: 10px 24px;
        }

        .btn.secondary:hover {
            background: #1f2937;
            color: #fff !important;
            border-color: #1f2937;
            box-shadow: none;
        }

        .hero {
            padding: 56px 0 30px;
        }

        .hero {
            position: relative;
        }

        .hero::before {
            content: "";
            position: absolute;
            inset: 12px 0 auto;
            height: 280px;
            background: radial-gradient(circle at 20% 0%, rgba(201, 154, 46, .12), transparent 42%), radial-gradient(circle at 80% 20%, rgba(34, 165, 107, .12), transparent 34%);
            pointer-events: none;
        }

        .hero-grid {
            position: relative;
            overflow: hidden;
            display: grid;
            grid-template-columns: minmax(0, 1.2fr) minmax(280px, .8fr);
            gap: 34px;
            align-items: stretch;
            min-height: 0;
            padding: clamp(34px, 4.4vw, 60px);
            border: 1px solid rgba(255, 255, 255, .14);
            border-radius: 38px;
            color: #fff;
            background:
                radial-gradient(circle at 92% 10%, rgba(243, 201, 105, .32), transparent 18rem),
                radial-gradient(circle at 70% 100%, rgba(34, 165, 107, .24), transparent 18rem),
                linear-gradient(135deg, #07351f 0%, #0d5a37 54%, #14663f 100%);
            box-shadow: 0 32px 78px rgba(7, 53, 31, .22);
        }

        .hero-grid::before {
            content: "";
            position: absolute;
            right: -70px;
            top: -70px;
            width: 240px;
            height: 240px;
            border-radius: 50%;
            background: rgba(255, 255, 255, .08);
        }

        .hero-grid::after {
            content: "PESOMA";
            position: absolute;
            right: -8px;
            bottom: -12px;
            color: rgba(255, 255, 255, .045);
            font-size: clamp(68px, 12vw, 150px);
            font-weight: 1000;
            line-height: .82;
            letter-spacing: -.08em;
        }

        .hero-content {
            display: flex;
            flex-direction: column;
            justify-content: center;
        }

        .hero-panel {
            display: grid;
            gap: 14px;
            align-content: start;
            padding: 24px;
            border-radius: 28px;
            background: rgba(255, 255, 255, .1);
            border: 1px solid rgba(255, 255, 255, .14);
            box-shadow: inset 0 1px 0 rgba(255, 255, 255, .08);
            backdrop-filter: blur(10px);
        }

        .hero-panel-card {
            padding: 16px 16px 15px;
            border-radius: 20px;
            background: rgba(7, 53, 31, .2);
            border: 1px solid rgba(255, 255, 255, .1);
        }

        .hero-panel-label {
            display: inline-flex;
            margin-bottom: 10px;
            padding: 6px 10px;
            border-radius: 999px;
            background: rgba(255, 255, 255, .12);
            border: 1px solid rgba(255, 255, 255, .14);
            color: rgba(255, 255, 255, .86);
            font-size: 10.5px;
            font-weight: 900;
            letter-spacing: .08em;
            text-transform: uppercase;
        }

        .hero-panel-card strong {
            display: block;
            margin-bottom: 6px;
            font-size: 16px;
            line-height: 1.4;
        }

        .hero-panel-card span {
            color: rgba(255, 255, 255, .72);
            font-size: 12.5px;
            font-weight: 700;
            line-height: 1.65;
        }

        .hero-grid>* {
            position: relative;
            z-index: 1;
        }

        .eyebrow {
            display: inline-flex;
            margin: 0 0 14px;
            padding: 7px 12px;
            border: 1px solid rgba(255, 255, 255, .22);
            border-radius: 999px;
            background: rgba(255, 255, 255, .1);
            font-size: 11px;
            font-weight: 900;
            letter-spacing: .14em;
            text-transform: uppercase;
            color: rgba(255, 255, 255, .88);
            box-shadow: inset 0 0 30px rgba(255, 255, 255, .04);
        }

        .hero h1 {
            margin: 0;
            font-size: clamp(22px, 3vw, 32px);
            line-height: 1.02;
            letter-spacing: -.05em;
            text-wrap: balance;
            font-weight: 900;
            background: linear-gradient(180deg, #fff, rgba(255, 255, 255, .92));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .hero p {
            margin: 18px 0 0;
            max-width: 640px;
            color: rgba(255, 255, 255, .84);
            font-size: clamp(13px, 1.3vw, 15px);
            line-height: 1.82;
        }

        .actions {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin-top: 24px;
        }

        .hero .actions .btn {
            box-shadow: 0 14px 30px rgba(0, 0, 0, .18);
        }

        .hero .actions .btn:hover {
            transform: translateY(-3px) scale(1.01);
            box-shadow: 0 18px 38px rgba(0, 0, 0, .24), 0 0 0 4px rgba(255, 255, 255, .12);
        }

        .hero .actions .btn:not(.secondary):hover {
            background: #ffffff;
            color: var(--primary-dark) !important;
        }

        .hero .actions .btn.secondary {
            background: rgba(255, 255, 255, .08);
            color: #fff !important;
            border-color: rgba(255, 255, 255, .42);
            backdrop-filter: blur(8px);
        }

        .hero .actions .btn.secondary:hover {
            background: #ffffff;
            color: var(--primary-dark) !important;
            border-color: #ffffff;
            box-shadow: 0 18px 38px rgba(0, 0, 0, .24), 0 0 0 4px rgba(255, 255, 255, .12);
        }

        .hero-note {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
            margin-top: 22px;
            color: rgba(255, 255, 255, .84);
            font-size: 13px;
            font-weight: 750;
        }

        .hero-note span {
            padding: 7px 10px;
            border-radius: 999px;
            background: rgba(255, 255, 255, .09);
            border: 1px solid rgba(255, 255, 255, .12);
        }

        .stats-grid,
        .card-grid,
        .preview-grid {
            display: grid;
            gap: 16px;
        }

        section {
            padding: 34px 0;
        }

        .stats-grid {
            grid-template-columns: repeat(4, 1fr);
            margin-top: 8px;
            gap: 18px;
        }

        .stat,
        .card,
        .schedule,
        .announcement,
        .empty-state {
            border: 1px solid rgba(15, 81, 50, .08);
            border-radius: 24px;
            background: rgba(255, 255, 255, .96);
            backdrop-filter: blur(10px);
            box-shadow: 0 12px 34px rgba(15, 81, 50, .08);
        }

        .stat {
            position: relative;
            padding: 26px 24px;
            overflow: hidden;
            transition: var(--transition);
        }

        .stat::after {
            content: "";
            position: absolute;
            right: -18px;
            top: -18px;
            width: 88px;
            height: 88px;
            border-radius: 999px;
            background: rgba(201, 154, 46, .14);
            transition: transform 0.5s cubic-bezier(0.34, 1.56, 0.64, 1);
        }

        .stat:hover::after {
            transform: scale(1.2) rotate(15deg);
        }

        .stat:hover,
        .announcement:hover,
        .schedule:hover {
            transform: translateY(-3px);
            box-shadow: var(--shadow-lg);
        }

        .stat strong {
            display: block;
            color: var(--primary);
            font-size: 30px;
            line-height: 1;
            letter-spacing: -.03em;
        }

        .stat span {
            display: block;
            margin-top: 6px;
            color: var(--text-secondary);
            font-size: 13px;
            font-weight: 850;
        }

        .section-head {
            text-align: center;
            max-width: 720px;
            margin: 0 auto 46px;
        }

        .section-tag {
            display: inline-block;
            background: #edf7f1;
            color: var(--primary);
            padding: 5px 13px;
            border-radius: 999px;
            font-size: 11.5px;
            font-weight: 800;
            margin-bottom: 14px;
            letter-spacing: .04em;
            text-transform: uppercase;
        }

        .section-title {
            margin: 0;
            color: var(--primary);
            font-size: clamp(18px, 2.5vw, 24px);
            line-height: 1.2;
            letter-spacing: -.03em;
        }

        .section-desc {
            margin: 10px auto 0;
            color: var(--text-secondary);
            max-width: 620px;
            font-size: 14.5px;
            line-height: 1.75;
        }

        .link-more {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            color: var(--primary);
            font-size: 14px;
            font-weight: 800;
            white-space: nowrap;
        }

        .showcase-head-action {
            margin-top: 20px;
        }

        .card-grid {
            grid-template-columns: repeat(3, 1fr);
            gap: 24px;
        }

        .card {
            position: relative;
            display: flex;
            flex-direction: column;
            min-height: 100%;
            padding: 26px;
            transition: .3s ease;
            overflow: hidden;
        }

        .card::before {
            content: "";
            position: absolute;
            inset: 0;
            background: linear-gradient(180deg, rgba(255, 255, 255, 0), rgba(15, 81, 50, .03));
            opacity: 0;
            transition: .3s ease;
            pointer-events: none;
        }

        .card-top {
            display: flex;
            align-items: flex-start;
            justify-content: space-between;
            gap: 16px;
            margin-bottom: 14px;
        }

        .card-icon {
            width: 54px;
            height: 54px;
            flex-shrink: 0;
            border-radius: 18px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            color: #fff;
            font-size: 21px;
            background: linear-gradient(135deg, #0f5132, #1a7c52);
            box-shadow: 0 12px 24px rgba(15, 81, 50, .18);
        }

        .reveal {
            opacity: 0;
            transform: translateY(22px);
            transition: opacity .7s ease, transform .7s ease;
        }

        .reveal.is-visible {
            opacity: 1;
            transform: translateY(0);
        }

        .card:hover {
            transform: translateY(-6px);
            box-shadow: 0 24px 48px rgba(15, 81, 50, .14);
            border-color: rgba(15, 81, 50, .14);
        }

        .card:hover::before {
            opacity: 1;
        }

        .card h3 {
            margin: 0 0 10px;
            font-size: 20px;
            line-height: 1.3;
            font-weight: 800;
            color: var(--text-primary);
            letter-spacing: -.02em;
        }

        .card p {
            margin: 0 0 18px;
            color: var(--text-secondary);
            font-size: 13.5px;
            line-height: 1.7;
            flex-grow: 1;
        }

        .card .btn {
            margin-top: auto;
            align-self: flex-start;
            min-height: auto;
            padding: 9px 22px;
            font-size: 13px;
            font-weight: 700;
            border-radius: 999px;
            background: transparent;
            color: var(--text-primary) !important;
            border: 1.5px solid var(--text-primary);
            box-shadow: none;
        }

        .card .btn:hover {
            background: var(--text-primary);
            color: #fff !important;
            transform: none;
            box-shadow: none;
        }

        .badge {
            display: inline-flex;
            width: fit-content;
            padding: 7px 12px;
            border-radius: 999px;
            background: #eef7f2;
            color: var(--primary);
            font-size: 11px;
            font-weight: 900;
            text-transform: capitalize;
            border: 1px solid rgba(15, 81, 50, .08);
            margin-bottom: 0;
            white-space: nowrap;
        }

        .card-accent {
            display: inline-flex;
            align-items: center;
            gap: 7px;
            margin-bottom: 14px;
            color: var(--primary);
            font-size: 12px;
            font-weight: 800;
        }

        .card-accent i {
            color: var(--accent);
        }

        .preview-grid {
            grid-template-columns: 1fr 1fr;
            gap: 28px;
            align-items: start;
        }

        .preview-column .section-head {
            text-align: left;
            max-width: none;
            margin: 0 0 22px;
        }

        .preview-column .section-desc {
            margin: 10px 0 0;
            max-width: none;
        }

        .preview-list {
            display: grid;
            gap: 16px;
        }

        .schedule,
        .announcement {
            position: relative;
            margin-bottom: 0;
            padding: 24px 24px;
            min-height: 136px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            transition: var(--transition);
            overflow: hidden;
        }

        .schedule::before,
        .announcement::before {
            content: "";
            position: absolute;
            right: -18px;
            top: -18px;
            width: 92px;
            height: 92px;
            border-radius: 999px;
            background: rgba(15, 81, 50, .05);
        }

        .announcement {
            display: block;
        }

        .schedule strong,
        .announcement strong {
            display: block;
            margin-bottom: 8px;
            font-size: 16px;
            line-height: 1.4;
        }

        .meta {
            display: block;
            margin-top: 5px;
            color: var(--text-secondary);
            font-size: 12px;
            font-weight: 700;
            line-height: 1.6;
        }

        .empty-state {
            padding: 22px;
            color: var(--text-secondary);
            font-size: 14px;
            font-weight: 750;
            text-align: center;
        }

        .cta {
            padding-top: 20px;
            padding-bottom: 56px;
        }

        .cta-panel {
            display: flex;
            justify-content: space-between;
            align-items: center;
            gap: 20px;
            padding: clamp(26px, 3.8vw, 38px);
            border-radius: 32px;
            background:
                radial-gradient(circle at 90% 0%, rgba(243, 201, 105, .18), transparent 16rem),
                linear-gradient(135deg, #ffffff, #f1f8f4);
            border: 1px solid rgba(15, 81, 50, .08);
            box-shadow: 0 22px 56px rgba(15, 81, 50, .11);
        }

        .cta h2 {
            margin: 0;
            color: var(--primary);
            font-size: clamp(20px, 2.4vw, 26px);
            line-height: 1.2;
        }

        .cta p {
            margin: 6px 0 0;
            max-width: 540px;
            color: var(--text-secondary);
            font-size: 14px;
            line-height: 1.75;
        }


        .back-to-top {
            position: fixed;
            right: 16px;
            bottom: 16px;
            z-index: 30;
            width: 44px;
            height: 44px;
            border: 0;
            border-radius: 14px;
            background: var(--primary);
            color: #fff;
            box-shadow: 0 14px 30px rgba(15, 81, 50, .28);
            cursor: pointer;
            opacity: 0;
            pointer-events: none;
            transform: translateY(12px);
            transition: var(--transition);
        }

        .back-to-top.is-visible {
            opacity: 1;
            pointer-events: auto;
            transform: translateY(0);
        }

        @media(max-width:960px) {

            .hero-grid,
            .preview-grid {
                grid-template-columns: 1fr;
            }

            .hero-panel {
                grid-template-columns: repeat(2, minmax(0, 1fr));
            }

            .stats-grid,
            .card-grid {
                grid-template-columns: repeat(2, 1fr);
                gap: 24px;
            }
        }

        @media(max-width:768px) {
            .nav {
                gap: 12px;
                padding: 0 8px;
            }

            .brand {
                font-size: 14px;
                gap: 8px;
            }

            .menu a:not(.btn) {
                padding: 6px 10px;
                font-size: 13px;
            }

            .card-grid {
                grid-template-columns: repeat(2, 1fr);
            }
        }

        @media(max-width:640px) {
            .container {
                width: min(100% - 24px, 1160px);
            }

            .hero {
                padding-top: 26px;
            }

            .hero-grid {
                min-height: 0;
                padding: 24px;
                gap: 22px;
                border-radius: 24px;
            }

            .hero-panel {
                grid-template-columns: 1fr;
                padding: 18px;
                border-radius: 22px;
            }

            .nav {
                min-height: 56px;
                gap: 8px;
                padding: 0 8px;
            }

            .brand {
                font-size: 13px;
                gap: 6px;
            }

            .menu a:not(.btn) {
                padding: 6px 10px;
                font-size: 12px;
            }

            .menu-toggle {
                display: inline-grid;
                place-items: center;
                width: 36px;
                height: 36px;
                font-size: 20px;
            }

            .menu {
                display: none;
                position: absolute;
                top: 56px;
                left: 0;
                right: 0;
                width: 100%;
                padding: 12px;
                border: none;
                border-top: 1px solid var(--border);
                border-radius: 0;
                background: #fff;
                flex-direction: column;
                align-items: stretch;
                gap: 4px;
                box-shadow: var(--shadow-md);
            }

            .menu.is-open {
                display: flex;
            }

            .menu a:not(.btn) {
                width: 100%;
                text-align: left;
            }

            .menu .btn {
                width: 100%;
            }

            .menu .btn:hover,
            .menu .btn:active,
            .menu .btn:focus-visible {
                background: var(--primary-dark);
                color: #fff !important;
                border-color: transparent;
                box-shadow: 0 10px 24px rgba(15, 81, 50, .18);
            }

            .stats-grid,
            .card-grid {
                grid-template-columns: 1fr;
            }

            .cta-panel {
                align-items: flex-start;
                flex-direction: column;
                gap: 10px;
            }

            .footer-inner,
            .footer-bottom {
                grid-template-columns: 1fr;
                display: grid;
            }

            .actions,
            .actions .btn,
            .cta .btn {
                width: 100%;
            }
        }
    </style>
</head>

<body>
    <header class="header">
        <div class="container nav">
            <a class="brand" href="<?= e(APP_URL) ?>/pages/beranda.php" aria-label="Beranda PESOMA"><span class="brand-text">PESOMA<small>Pekan Seni &amp; Olahraga Mahasiswa</small></span></a>
            <button class="menu-toggle" id="menuToggle" type="button" aria-label="Buka menu" aria-controls="mainMenu" aria-expanded="false">☰</button>
            <nav class="menu" id="mainMenu" aria-label="Navigasi utama">
                <a class="active" href="<?= e(APP_URL) ?>/pages/beranda.php" aria-current="page">Beranda</a>
                <a href="<?= e(APP_URL) ?>/pages/cabang-lomba.php">Lomba</a>
                <a href="<?= e(APP_URL) ?>/pages/jadwal.php">Jadwal</a>
                <a href="<?= e(APP_URL) ?>/pages/pengumuman.php">Pengumuman</a>
                <a href="<?= e(APP_URL) ?>/pages/tentang.php">Tentang</a>
                <a class="btn" href="<?= e(APP_URL) ?>/src/auth/login.php">Login</a>
            </nav>
        </div>
    </header>
    <main>
        <section class="hero">
            <div class="container hero-grid reveal">
                <div class="hero-content">
                    <span class="eyebrow">Pekan Seni & Olahraga Mahasiswa</span>
                    <h1>Unjuk Aksi, Raih Prestasi.</h1>
                    <p>Portal resmi PESOMA 2026 UIN Prof. K.H. Saifuddin Zuhri Purwokerto untuk pendaftaran, pengumpulan karya, penjurian, jadwal, finalis, dan pemenang.</p>
                    <div class="actions"><a class="btn" href="<?= e(APP_URL) ?>/src/auth/register.php">Daftar Sekarang</a><a class="btn secondary" href="<?= e(APP_URL) ?>/pages/cabang-lomba.php">Lihat Cabang Lomba</a></div>
                    <div class="hero-note"><span>✓ 14 cabang lomba</span><span>✓ Upload karya online</span><span>✓ Pengumuman terpusat</span></div>
                </div>
                <div class="hero-panel" aria-label="Highlight PESOMA 2026">
                    <div class="hero-panel-card">
                        <span class="hero-panel-label">Agenda</span>
                        <strong>Pendaftaran dibuka sampai akhir April 2026</strong>
                        <span>Pantau jadwal resmi, technical meeting, dan deadline unggah karya secara terpusat.</span>
                    </div>
                    <div class="hero-panel-card">
                        <span class="hero-panel-label">Partisipasi</span>
                        <strong>Kompetisi seni, olahraga, riset, dan inovasi</strong>
                        <span>Pilih cabang lomba terbaik sesuai minat dan siapkan performa terbaikmu di PESOMA.</span>
                    </div>
                    <div class="hero-panel-card">
                        <span class="hero-panel-label">Sistem</span>
                        <strong>Portal terintegrasi untuk peserta dan panitia</strong>
                        <span>Mulai dari registrasi, validasi, upload progres, hingga pengumuman finalis dalam satu platform.</span>
                    </div>
                </div>
            </div>
        </section>
        <section aria-label="Statistik PESOMA 2026">
            <div class="container stats-grid">
                <div class="stat reveal"><strong data-count="<?= $totalLomba ?>">0</strong><span>Total Lomba</span></div>
                <div class="stat reveal"><strong data-count="<?= $cabangLomba ?>">0</strong><span>Cabang Lomba</span></div>
                <div class="stat reveal"><strong data-count="<?= $pesertaTerdaftar ?>">0</strong><span>Peserta Terdaftar</span></div>
                <div class="stat reveal"><strong data-count="<?= $finalisTerpilih ?>">0</strong><span>Finalis Terpilih</span></div>
            </div>
        </section>
        <section aria-labelledby="preview-lomba-title">
            <div class="container">
                <div class="section-head reveal">
                    <div class="section-tag">Cabang Lomba</div>
                    <h2 class="section-title" id="preview-lomba-title">Eksplorasi Lomba Unggulan PESOMA.</h2>
                    <p class="section-desc">Pilih cabang lomba seni, olahraga, riset, dan inovasi sesuai minat, bakat, serta ketentuan resmi PESOMA 2026.</p>
                    <div class="showcase-head-action">
                        <a class="link-more" href="<?= e(APP_URL) ?>/pages/cabang-lomba.php">Lihat semua cabang →</a>
                    </div>
                </div>
                <?php if ($competitions): ?><div class="card-grid"><?php foreach ($competitions as $competition): ?><article class="card reveal">
                                <div class="card-top">
                                    <span class="card-icon"><?php if (stripos((string) $competition['jenis'], 'seni') !== false): ?><i class="fas fa-music"></i><?php elseif (stripos((string) $competition['jenis'], 'olahraga') !== false): ?><i class="fas fa-dumbbell"></i><?php elseif (stripos((string) $competition['jenis'], 'riset') !== false): ?><i class="fas fa-flask"></i><?php else: ?><i class="fas fa-trophy"></i><?php endif; ?></span>
                                    <span class="badge"><?= e($competition['jenis']) ?></span>
                                </div>
                                <div class="card-accent"><i class="fas fa-sparkles"></i><span>Pilihan unggulan PESOMA 2026</span></div>
                                <h3><?= e($competition['nama_lomba']) ?></h3>
                                <p><?= e($competition['deskripsi'] ?: 'Informasi cabang lomba PESOMA 2026.') ?></p><a class="btn" href="<?= e(APP_URL) ?>/pages/detail-lomba.php?id=<?= (int) $competition['id'] ?>">Selengkapnya</a>
                            </article><?php endforeach; ?></div><?php else: ?><div class="empty-state">Data cabang lomba belum tersedia.</div><?php endif; ?>
            </div>
        </section>
        <section aria-label="Jadwal dan pengumuman terbaru">
            <div class="container preview-grid">
                <div class="preview-column">
                    <div class="section-head reveal">
                        <div class="section-tag">Agenda</div>
                        <h2 class="section-title">Jadwal Kegiatan</h2>
                        <p class="section-desc">Pantau agenda penting PESOMA 2026 mulai dari pembukaan, perlombaan, hingga pengumuman.</p>
                        <div class="showcase-head-action"><a class="link-more" href="<?= e(APP_URL) ?>/pages/jadwal.php">Lihat semua jadwal →</a></div>
                    </div>
                    <?php if ($schedules): ?><div class="preview-list"><?php foreach ($schedules as $schedule): ?><div class="schedule reveal"><strong><?= e($schedule['event_name']) ?></strong><span class="meta"><?= e(date('d M Y', strtotime($schedule['event_date']))) ?> · <?= e(substr((string) $schedule['event_time'], 0, 5)) ?> WIB · <?= e($schedule['location']) ?></span></div><?php endforeach; ?></div><?php else: ?><div class="empty-state">Jadwal publik belum tersedia.</div><?php endif; ?>
                </div>
                <div class="preview-column">
                    <div class="section-head reveal">
                        <div class="section-tag">Informasi</div>
                        <h2 class="section-title">Pengumuman Terbaru</h2>
                        <p class="section-desc">Informasi penting seputar finalis, pemenang, perubahan jadwal, dan pengumuman resmi lainnya.</p>
                        <div class="showcase-head-action"><a class="link-more" href="<?= e(APP_URL) ?>/pages/pengumuman.php">Lihat semua pengumuman →</a></div>
                    </div>
                    <?php if ($announcements): ?><div class="preview-list"><?php foreach ($announcements as $announcement): ?><a class="announcement reveal" href="<?= e(APP_URL) ?>/pages/detail-pengumuman.php?id=<?= (int) $announcement['id'] ?>"><span class="badge"><?= e($announcement['type']) ?></span><strong><?= e($announcement['title']) ?></strong><span class="meta"><?= e(date('d M Y H:i', strtotime($announcement['published_at']))) ?> WIB</span></a><?php endforeach; ?></div><?php else: ?><div class="empty-state">Belum ada pengumuman publik.</div><?php endif; ?>
                </div>
            </div>
        </section>
        <section class="cta reveal">
            <div class="container">
                <div class="cta-panel">
                    <div>
                        <h2>Siap menjadi bagian PESOMA 2026?</h2>
                        <p>Daftar akun peserta, pilih lomba, lalu unggah karya sesuai jadwal resmi.</p>
                    </div><a class="btn" href="<?= e(APP_URL) ?>/src/auth/register.php">Mulai Pendaftaran</a>
                </div>
            </div>
        </section>
    </main>
    <footer class="footer">
        <div class="container footer-inner">
            <div class="footer-brand">
                <a class="brand" href="<?= e(APP_URL) ?>/pages/beranda.php" aria-label="Beranda PESOMA 2026">
                    <span class="brand-text">PESOMA 2026<small>Pekan Seni &amp; Olahraga Mahasiswa</small></span>
                </a>
                <p class="footer-desc">Platform resmi PESOMA 2026 untuk informasi lomba, jadwal kegiatan, publikasi pengumuman, dan pendaftaran peserta secara terintegrasi.</p>
            </div>
            <div>
                <h3 class="footer-title">Navigasi</h3>
                <div class="footer-links">
                    <a href="<?= e(APP_URL) ?>/pages/cabang-lomba.php">Cabang Lomba</a>
                    <a href="<?= e(APP_URL) ?>/pages/jadwal.php">Jadwal Kegiatan</a>
                    <a href="<?= e(APP_URL) ?>/pages/pengumuman.php">Pengumuman</a>
                    <a href="<?= e(APP_URL) ?>/pages/tentang.php">Tentang PESOMA</a>
                </div>
            </div>
            <div>
                <h3 class="footer-title">Informasi</h3>
                <div class="footer-meta">
                    <span>UIN Prof. K.H. Saifuddin Zuhri Purwokerto</span>
                    <span>Portal lomba mahasiswa terpusat</span>
                    <span>Pendaftaran, jadwal, dan pengumuman resmi</span>
                </div>
            </div>
        </div>
        <div class="container footer-bottom">
            <span>© 2026 PESOMA UIN Prof. K.H. Saifuddin Zuhri Purwokerto</span>
            <span>Didesain untuk pengalaman yang lebih modern dan informatif</span>
        </div>
    </footer>
    <button class="back-to-top" id="backToTop" type="button" aria-label="Kembali ke atas">↑</button>
    <script>
        (() => {
            'use strict';

            const menuToggle = document.getElementById('menuToggle');
            const mainMenu = document.getElementById('mainMenu');
            const backToTop = document.getElementById('backToTop');

            const formatNumber = (number) => new Intl.NumberFormat('id-ID').format(number);

            const updateScrollUI = () => {
                if (backToTop) backToTop.classList.toggle('is-visible', window.scrollY > 360);
            };

            const animateCounter = (element) => {
                const target = Number(element.dataset.count || 0);
                const duration = 900;
                const start = performance.now();

                const frame = (now) => {
                    const progress = Math.min((now - start) / duration, 1);
                    const eased = 1 - Math.pow(1 - progress, 3);
                    element.textContent = formatNumber(Math.round(target * eased));
                    if (progress < 1) requestAnimationFrame(frame);
                };

                requestAnimationFrame(frame);
            };

            const revealObserver = new IntersectionObserver((entries, observer) => {
                entries.forEach((entry) => {
                    if (!entry.isIntersecting) return;
                    entry.target.classList.add('is-visible');

                    const counter = entry.target.querySelector('[data-count]');
                    if (counter && !counter.dataset.animated) {
                        counter.dataset.animated = 'true';
                        animateCounter(counter);
                    }

                    observer.unobserve(entry.target);
                });
            }, {
                threshold: 0.16
            });

            document.querySelectorAll('.reveal').forEach((element, index) => {
                element.style.transitionDelay = `${Math.min(index * 45, 220)}ms`;
                revealObserver.observe(element);
            });

            menuToggle?.addEventListener('click', () => {
                mainMenu?.classList.toggle('is-open');
                menuToggle.setAttribute('aria-expanded', mainMenu?.classList.contains('is-open') ? 'true' : 'false');
            });

            mainMenu?.querySelectorAll('a').forEach((link) => {
                link.addEventListener('click', () => {
                    mainMenu.classList.remove('is-open');
                    menuToggle?.setAttribute('aria-expanded', 'false');
                });
            });

            backToTop?.addEventListener('click', () => {
                window.scrollTo({
                    top: 0,
                    behavior: 'smooth'
                });
            });

            updateScrollUI();
            window.addEventListener('scroll', updateScrollUI, {
                passive: true
            });
            window.addEventListener('resize', updateScrollUI);
        })();
    </script>
</body>

</html>