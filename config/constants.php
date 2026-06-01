<?php

declare(strict_types=1);

const ROLE_ADMIN = 'admin';
const ROLE_PANITIA = 'panitia';
const ROLE_JURI = 'juri';
const ROLE_PESERTA = 'peserta';

const REG_STATUS_PENDING = 'pending';
const REG_STATUS_DITERIMA = 'diterima';
const REG_STATUS_DITOLAK = 'ditolak';

const ANNOUNCEMENT_UMUM = 'umum';
const ANNOUNCEMENT_FINALIS = 'finalis';
const ANNOUNCEMENT_WINNER = 'winner';

const ALLOWED_FAKULTAS = [
    'FTIK',
    'FAKDA',
    'FASYA',
    'FEBI',
    'FUAH',
    'FST',
];

const ALLOWED_UPLOAD_EXTENSIONS = [
    'doc',
    'docx',
    'ppt',
    'pptx',
    'pdf',
    'jpg',
    'jpeg',
    'png',
    'mp4',
    'zip',
    'rar',
];

// Design tokens - sesuai dengan beranda.php
const DESIGN_PRIMARY = '#1a9d6e';
const DESIGN_PRIMARY_DARK = '#0f7a52';
const DESIGN_PRIMARY_LIGHT = '#2fb87f';
const DESIGN_ACCENT = '#c99a2e';
const DESIGN_ACCENT_LIGHT = '#f3c969';
const DESIGN_BG_PRIMARY = '#f5f8f6';
const DESIGN_BG_SECONDARY = '#fbfdfb';
const DESIGN_TEXT_PRIMARY = '#132019';
const DESIGN_TEXT_SECONDARY = '#647268';
const DESIGN_BORDER = '#dfe8e2';
const DESIGN_DANGER = '#dc2626';
const DESIGN_WARN = '#d97706';
const DESIGN_OK = '#059669';
