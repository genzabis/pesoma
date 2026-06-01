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

// Design tokens — single source of truth (selaras assets/css/pesoma-public.css)
const DESIGN_PRIMARY = '#0b2f9f';
const DESIGN_PRIMARY_DARK = '#0a2580';
const DESIGN_PRIMARY_LIGHT = '#3b82f6';
const DESIGN_ACCENT = '#10b981';
const DESIGN_BG_PRIMARY = '#f7f8fa';
const DESIGN_BG_SECONDARY = '#ffffff';
const DESIGN_TEXT_PRIMARY = '#0f172a';
const DESIGN_TEXT_SECONDARY = '#475569';
const DESIGN_BORDER = '#e8ebf0';
const DESIGN_DANGER = '#b91c1c';
const DESIGN_WARN = '#92400e';
const DESIGN_OK = '#065f46';
