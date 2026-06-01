# DESIGN.md – PESOMA 2026

## Home (PESOMA)

### Mission
Create implementation-ready, token-driven UI guidance for **PESOMA 2026 (Pekan Seni dan Olahraga Mahasiswa UIN SAIZU Purwokerto)** that is optimized for consistency, accessibility, and fast delivery across event website.

---

## Brand
- Product/brand: PESOMA 2026
- URL: https://pesoma.uin-saizu.ac.id
- Audience: Mahasiswa aktif UIN SAIZU Purwokerto, panitia, juri, admin
- Product surface: Event website (pendaftaran, upload karya, penjurian, pengumuman)

---

## Style Foundations
- Visual style: structured, tokenized, content-first, sporty yet academic
- Main font style: `font.family.primary=Plus Jakarta Sans`, `font.family.stack=Plus Jakarta Sans, system-ui, sans-serif`, `font.size.base=10.5px`, `font.weight.base=400`, `font.lineHeight.base=15px`
- Typography scale: `font.size.xs=9px`, `font.size.sm=10.5px`, `font.size.md=12px`, `font.size.lg=15px`, `font.size.xl=18px`, `font.size.2xl=22.5px`, `font.size.3xl=27px`
- Color palette: `color.text.primary=#64748b`, `color.surface.base=#000000`, `color.text.tertiary=#ffffff`, `color.text.inverse=#1e293b`, `color.surface.muted=#0b2f9f`, `color.surface.strong=#f1f5f9`, `color.border.default=#e5e7eb`, `color.border.muted=#475569`
- Spacing scale: `space.1=6px`, `space.2=9px`, `space.3=12px`, `space.4=15px`, `space.5=18px`, `space.6=24px`, `space.7=30px`, `space.8=60px`
- Radius/shadow/motion tokens: `radius.xs=6px`, `radius.sm=9999px` | `shadow.1=rgba(0, 0, 0, 0) 0px 0px 0px 0px, rgba(0, 0, 0, 0) 0px 0px 0px 0px, rgba(0, 0, 0, 0.1) 0px 1px 3px 0px, rgba(0, 0, 0, 0.1) 0px 1px 2px -1px`, `shadow.2=rgb(255, 255, 255) 0px 0px 0px 0px, rgba(59, 130, 246, 0.5) 0px 0px 0px 0px, rgba(0, 0, 0, 0) 0px 0px 0px 0px` | `motion.duration.instant=100ms`, `motion.duration.fast=150ms`, `motion.duration.normal=200ms`, `motion.duration.slow=300ms`

---

## Accessibility
- Target: WCAG 2.2 AA
- Keyboard-first interactions required.
- Focus-visible rules required.
- Contrast constraints required.

---

## Writing Tone
Concise, confident, implementation-focused, academic yet energetic (sesuai event mahasiswa).

---

## Rules: Do
- Use semantic tokens, not raw hex values, in component guidance.
- Every component must define states for default, hover, focus-visible, active, disabled, loading, and error.
- Component behavior should specify responsive and edge-case handling.
- Interactive components must document keyboard, pointer, and touch behavior.
- Accessibility acceptance criteria must be testable in implementation.

---

## Rules: Don't
- Do not allow low-contrast text or hidden focus indicators.
- Do not introduce one-off spacing or typography exceptions.
- Do not use ambiguous labels or non-descriptive actions.
- Do not ship component guidance without explicit state rules.

---

## Guideline Authoring Workflow
1. Restate design intent in one sentence.
2. Define foundations and semantic tokens.
3. Define component anatomy, variants, interactions, and state behavior.
4. Add accessibility acceptance criteria with pass/fail checks.
5. Add anti-patterns, migration notes, and edge-case handling.
6. End with a QA checklist.

---

## Required Output Structure
- Context and goals.
- Design tokens and foundations.
- Component-level rules (anatomy, variants, states, responsive behavior).
- Accessibility requirements and testable acceptance criteria.
- Content and tone standards with examples.
- Anti-patterns and prohibited implementations.
- QA checklist.

---

## Component Rule Expectations
- Include keyboard, pointer, and touch behavior.
- Include spacing and typography token requirements.
- Include long-content, overflow, and empty-state handling.
- Include known page component density: links (164), lists (26), buttons (15), cards (9), navigation (2), inputs (1).

---

## Quality Gates
- Every non-negotiable rule must use "must".
- Every recommendation should use "should".
- Every accessibility rule must be testable in implementation.
- Teams should prefer system consistency over local visual exceptions.

---

## PESOMA 2026 Component Tokens (Tambahan Khusus)

### Navigation (Header)
- Background: `color.surface.base=#000000`
- Link default: `color.text.tertiary=#ffffff`
- Link hover: `color.surface.muted=#0b2f9f`
- Active link indicator: border-bottom `2px` solid `color.surface.muted`
- Mobile menu: breakpoint `768px`, drawer dari kanan

### Button
- Primary button background: `color.surface.muted=#0b2f9f`
- Primary button text: `color.text.tertiary=#ffffff`
- Primary button hover: opacity `0.85`
- Secondary button background: transparent
- Secondary button border: `1px` solid `color.border.muted=#475569`
- Secondary button text: `color.text.inverse=#1e293b`

### Card
- Background: `color.surface.strong=#f1f5f9`
- Border radius: `radius.xs=6px`
- Shadow: `shadow.1`
- Padding: `space.5=18px`
- Title font: `font.size.lg=15px`, weight `600`
- Description font: `font.size.sm=10.5px`, color `color.text.primary=#64748b`

### Form Input
- Border: `1px` solid `color.border.default=#e5e7eb`
- Border radius: `radius.xs=6px`
- Padding: `space.3=12px`
- Focus state: border `color.surface.muted=#0b2f9f`, shadow `0 0 0 2px rgba(11,47,159,0.2)`
- Label font: `font.size.sm=10.5px`, weight `500`

### Badge / Status
- Pending: background `#fef3c7`, text `#92400e`
- Diterima: background `#d1fae5`, text `#065f46`
- Finalis: background `#dbeafe`, text `#1e40af`
- Juara: background `#fef08a`, text `#854d0e`

---

## QA Checklist PESOMA 2026
- [ ] Semua warna menggunakan token, bukan raw hex
- [ ] Setiap komponen memiliki state default, hover, focus, active, disabled
- [ ] Navigasi keyboard (Tab, Enter, Space) berfungsi di semua interaksi
- [ ] Focus indicator terlihat jelas (tidak di-hidden)
- [ ] Kontras teks memenuhi WCAG 2.2 AA (minimal 4.5:1 untuk teks normal)
- [ ] Responsif di breakpoint 768px, 1024px, 1280px
- [ ] Tidak ada spacing atau typography yang one-off
- [ ] Semua tombol memiliki label deskriptif (bukan "klik di sini")
- [ ] Long content handling (teks panjang tidak overflow)
- [ ] Empty state ditangani dengan pesan yang jelas

---

## Anti-patterns (Dilarang)
- ❌ Menggunakan warna merah untuk tombol sukses
- ❌ Menghilangkan focus outline
- ❌ Teks dengan kontras rendah (abu-abu muda di background putih)
- ❌ Tombol tanpa cursor pointer
- ❌ Form tanpa validasi atau error message
- ❌ Link dengan label "KLIK" tanpa konteks
- ❌ Mengabaikan padding/margin di mobile
