<?php

declare(strict_types=1);

require_once __DIR__ . '/auth.php';

/**
 * Layout publik bersama untuk halaman di folder pages/.
 * Memanggil public_header(judul, slug-menu-aktif) di awal dan public_footer() di akhir.
 */
function public_header(string $title, string $active = ''): void
{
    $menu = [
        'beranda.php' => 'Beranda',
        'cabang-lomba.php' => 'Lomba',
        'jadwal.php' => 'Jadwal',
        'pengumuman.php' => 'Pengumuman',
        'tentang.php' => 'Tentang',
        'kontak.php' => 'Kontak',
    ];
?>
    <!DOCTYPE html>
    <html lang="id" class="scroll-smooth">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta name="theme-color" content="#0f172a">
        <meta name="description" content="Portal resmi PESOMA 2026 UIN Prof. K.H. Saifuddin Zuhri Purwokerto.">
        <title><?= e($title) ?> - <?= e(APP_NAME) ?></title>
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
                background: var(--primary);
                transform: scaleX(0);
                transition: transform 0.3s ease;
            }

            .menu a:hover:not(.btn),
            .menu .active {
                background: transparent;
                color: var(--primary);
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
                position: relative;
                padding: 42px 0 22px;
            }

            .hero::before {
                content: "";
                position: absolute;
                inset: 12px 0 auto;
                height: 280px;
                background: radial-gradient(circle at 20% 0%, rgba(201, 154, 46, .12), transparent 42%), radial-gradient(circle at 80% 20%, rgba(34, 165, 107, .12), transparent 34%);
                pointer-events: none;
            }

            .hero-shell,
            .hero-grid {
                position: relative;
                overflow: hidden;
                padding: clamp(28px, 4vw, 48px);
                border: 1px solid rgba(255, 255, 255, .14);
                border-radius: 34px;
                color: #fff;
                background:
                    radial-gradient(circle at 92% 10%, rgba(243, 201, 105, .32), transparent 18rem),
                    radial-gradient(circle at 70% 100%, rgba(34, 165, 107, .24), transparent 18rem),
                    linear-gradient(135deg, #07351f 0%, #0d5a37 54%, #14663f 100%);
                box-shadow: 0 32px 78px rgba(7, 53, 31, .22);
            }

            .hero-grid {
                display: grid;
                grid-template-columns: minmax(0, 1.08fr) minmax(280px, .92fr);
                gap: clamp(22px, 4vw, 42px);
                align-items: stretch;
            }

            .hero-shell::before,
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

            .hero-shell::after,
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

            .hero-shell>*,
            .hero-grid>* {
                position: relative;
                z-index: 1;
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
                color: #fff;
                font-size: 16px;
                line-height: 1.4;
            }

            .hero-panel-card span:not(.hero-panel-label) {
                color: rgba(255, 255, 255, .72);
                font-size: 12.5px;
                font-weight: 700;
                line-height: 1.65;
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
                line-height: 1.04;
                letter-spacing: -.05em;
                text-wrap: balance;
                font-weight: 900;
                background: linear-gradient(180deg, #fff, rgba(255, 255, 255, .92));
                -webkit-background-clip: text;
                -webkit-text-fill-color: transparent;
                background-clip: text;
            }

            .hero p {
                margin: 16px 0 0;
                max-width: 700px;
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

            .page-highlight {
                display: grid;
                grid-template-columns: repeat(3, minmax(0, 1fr));
                gap: 16px;
                margin-top: -2px;
                margin-bottom: 30px;
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

            .stat:hover {
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

            .section {
                padding: 34px 0;
            }

            .section-head {
                text-align: center;
                max-width: 720px;
                margin: 0 auto 32px;
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
                max-width: 680px;
                font-size: 14.5px;
                line-height: 1.75;
            }

            .grid {
                display: grid;
                grid-template-columns: repeat(3, minmax(0, 1fr));
                gap: 24px;
            }

            .card-grid {
                display: grid;
                grid-template-columns: repeat(3, minmax(0, 1fr));
                gap: 24px;
            }

            .card,
            .stat,
            .empty-state {
                border: 1px solid rgba(15, 81, 50, .08);
                border-radius: 24px;
                background: rgba(255, 255, 255, .96);
                backdrop-filter: blur(10px);
                box-shadow: 0 12px 34px rgba(15, 81, 50, .08);
            }

            .card {
                position: relative;
                display: flex;
                flex-direction: column;
                padding: 26px;
                transition: .3s ease;
                overflow: hidden;
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

            .card::before {
                content: "";
                position: absolute;
                inset: 0;
                background: linear-gradient(180deg, rgba(255, 255, 255, 0), rgba(15, 81, 50, .03));
                opacity: 0;
                transition: .3s ease;
                pointer-events: none;
            }

            .card:hover {
                transform: translateY(-6px);
                box-shadow: 0 24px 48px rgba(15, 81, 50, .14);
                border-color: rgba(15, 81, 50, .14);
            }

            .card:hover::before {
                opacity: 1;
            }

            .card h2,
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
            }

            .card> :last-child {
                margin-bottom: 0;
            }

            .card .btn {
                margin-top: auto;
                align-self: flex-start;
                min-height: auto;
                padding: 9px 22px;
                font-size: 13px;
                font-weight: 700;
                border: 1.5px solid var(--text-primary);
                color: var(--text-primary) !important;
                background: transparent;
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
                white-space: nowrap;
            }

            .badge.winner {
                background: #fff3d9;
                color: #9a6b10;
                border-color: rgba(201, 154, 46, .24);
            }

            .muted {
                color: var(--text-secondary);
            }

            .table-wrap {
                overflow-x: auto;
                border-radius: 20px;
            }

            .table {
                width: 100%;
                border-collapse: collapse;
                min-width: 680px;
            }

            .table th,
            .table td {
                padding: 14px 16px;
                border-bottom: 1px solid var(--border);
                text-align: left;
                vertical-align: top;
            }

            .table th {
                color: var(--primary);
                background: #f8fbf8;
                font-size: 13px;
            }

            .table tbody tr:hover {
                background: rgba(15, 81, 50, .03);
            }

            .empty-state {
                padding: 22px;
                color: var(--text-secondary);
                font-size: 14px;
                font-weight: 750;
                text-align: center;
            }

            .filters {
                display: flex;
                gap: 10px;
                flex-wrap: wrap;
                margin: 0 0 22px;
            }

            .filters .btn {
                background: rgba(15, 81, 50, .08);
                color: var(--primary) !important;
                box-shadow: none;
            }

            .filters .btn.active,
            .filters .btn:hover {
                background: var(--primary);
                color: #fff !important;
            }

            .stack {
                display: grid;
                gap: 18px;
            }

            .detail-shell {
                width: min(920px, calc(100% - 36px));
                margin: 0 auto;
            }

            .detail-actions {
                display: flex;
                gap: 12px;
                flex-wrap: wrap;
                margin-top: 22px;
            }

            .detail-actions .btn.secondary {
                background: transparent;
                color: var(--text-primary) !important;
                border-color: var(--text-primary);
            }

            .detail-actions .btn.secondary:hover {
                background: var(--text-primary);
                color: #fff !important;
            }

            .footer {
                position: relative;
                overflow: hidden;
                padding: 0;
                background: linear-gradient(180deg, #0a3b23, #072f1b);
                color: rgba(255, 255, 255, .82);
                margin-top: 40px;
            }

            .footer::before {
                content: "";
                position: absolute;
                inset: 0;
                background:
                    radial-gradient(circle at top left, rgba(243, 201, 105, .12), transparent 24%),
                    radial-gradient(circle at bottom right, rgba(34, 165, 107, .12), transparent 22%);
                pointer-events: none;
            }

            .footer-inner {
                position: relative;
                z-index: 1;
                display: grid;
                grid-template-columns: minmax(0, 1.2fr) repeat(2, minmax(180px, .6fr));
                gap: 28px;
                padding: 42px 0 22px;
            }

            .footer-brand {
                max-width: 420px;
            }

            .footer-brand .brand {
                color: #fff;
                margin-bottom: 14px;
            }

            .footer-brand .brand-text small {
                color: rgba(255, 255, 255, .72);
            }

            .footer-desc {
                margin: 0;
                color: rgba(255, 255, 255, .72);
                font-size: 14px;
                line-height: 1.85;
            }

            .footer-title {
                margin: 0 0 14px;
                color: #fff;
                font-size: 14px;
                font-weight: 800;
                letter-spacing: .02em;
            }

            .footer-links,
            .footer-meta {
                display: grid;
                gap: 10px;
            }

            .footer-links a,
            .footer-meta span {
                color: rgba(255, 255, 255, .72);
                font-size: 13.5px;
                line-height: 1.7;
                transition: var(--transition);
            }

            .footer-links a:hover {
                color: #fff;
                transform: translateX(2px);
            }

            .footer-bottom {
                position: relative;
                z-index: 1;
                display: flex;
                justify-content: space-between;
                align-items: center;
                gap: 16px;
                padding: 18px 0 26px;
                border-top: 1px solid rgba(255, 255, 255, .08);
                color: rgba(255, 255, 255, .68);
                font-size: 12.5px;
                letter-spacing: .02em;
            }

            @media(max-width:960px) {

                .grid,
                .card-grid,
                .page-highlight {
                    grid-template-columns: repeat(2, 1fr);
                }

                .hero-grid {
                    grid-template-columns: 1fr;
                }

                .hero-panel {
                    grid-template-columns: repeat(2, minmax(0, 1fr));
                }
            }

            @media(max-width:768px) {
                .nav {
                    gap: 10px;
                    padding: 0 8px;
                    min-height: 56px;
                }

                .brand {
                    font-size: 12px;
                    gap: 6px;
                }

                .brand-mark {
                    width: 28px;
                    height: 28px;
                    font-size: 14px;
                }

                .brand-text small {
                    font-size: 9px;
                }

                .menu a:not(.btn) {
                    padding: 6px 10px;
                    font-size: 13px;
                }

                .footer-inner,
                .footer-bottom {
                    grid-template-columns: 1fr;
                    display: grid;
                }
            }

            @media(max-width:640px) {

                .container,
                .detail-shell {
                    width: min(100% - 24px, 1160px);
                }

                .hero {
                    padding-top: 26px;
                }

                .hero-shell {
                    padding: 24px;
                    border-radius: 24px;
                }

                .hero-grid {
                    padding: 24px;
                    gap: 22px;
                    border-radius: 24px;
                }

                .hero-panel,
                .page-highlight,
                .card-grid {
                    grid-template-columns: 1fr;
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

                .menu a:not(.btn),
                .menu .btn,
                .actions,
                .actions .btn,
                .filters .btn,
                .detail-actions .btn {
                    width: 100%;
                    text-align: left;
                }

                .grid {
                    grid-template-columns: 1fr;
                }
            }
        </style>
    </head>

    <body>
        <header class="header">
            <div class="container nav">
                <a class="brand" href="<?= e(APP_URL) ?>/pages/beranda.php" aria-label="Beranda PESOMA">
                    <span class="brand-text">PESOMA<small>Pekan Seni &amp; Olahraga Mahasiswa</small></span>
                </a>
                <button class="menu-toggle" id="menuToggle" type="button" aria-label="Buka menu" aria-controls="mainMenu" aria-expanded="false">☰</button>
                <nav class="menu" id="mainMenu" aria-label="Navigasi utama">
                    <?php foreach ($menu as $href => $label): ?>
                        <a class="<?= $href === $active ? 'active' : '' ?>" <?= $href === $active ? 'aria-current="page"' : '' ?> href="<?= e(APP_URL) ?>/pages/<?= e($href) ?>"><?= e($label) ?></a>
                    <?php endforeach; ?>
                    <?php if (is_logged_in()): ?>
                        <a class="btn" href="<?= e(dashboard_url_by_role($_SESSION['user']['role'])) ?>">Dashboard</a>
                    <?php else: ?>
                        <a class="btn" href="<?= e(APP_URL) ?>/src/auth/login.php">Login</a>
                    <?php endif; ?>
                </nav>
            </div>
        </header>
        <main>
            <script>
                (() => {
                    const menuToggle = document.getElementById('menuToggle');
                    const mainMenu = document.getElementById('mainMenu');

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
                })();
            </script>
        <?php
    }
