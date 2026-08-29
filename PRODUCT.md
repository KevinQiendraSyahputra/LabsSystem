# Product

<!-- impeccable:product-schema 1 -->

## Platform

web

## Users
Students (Siswa), Teachers (Guru), Lab Coordinators (Koordinator Lab), Head of Lab (Kepala Lab), and System Administrators.

## Product Purpose
Streamline school/institutional laboratory management through self-service equipment catalog borrowing, QR-code item lookup, per-unit inventory tracking, maintenance logging, and administrative reporting.

## Positioning
An integrated, role-tailored laboratory management web platform combining self-service equipment loans with end-to-end asset lifecycle and maintenance oversight.

## Operating Context
School science and computer laboratories; multi-role workflows across mobile and desktop web browsers (quick QR scans at lab benches, catalog browsing by students/teachers, and administrative inventory audits).

## Capabilities and Constraints
- Self-service equipment catalog with online loan request submission.
- QR code scanning (`/scan-qr`, `/api/check-qr`) for rapid item lookup and loan processing.
- Granular per-unit equipment condition tracking, unit splitting, and maintenance lifecycle logs.
- Multi-role permission system (Admin, Guru, Siswa, Kepala Lab, Koordinator Lab).
- PDF export and JSON feeds for inventory and maintenance reporting.
- News/announcements module (`/berita`) and live assistant help page (`/bantuan`).
- Built on Laravel, Tailwind CSS, Vite, and Blade views; engineered for compatibility with traditional hosting environments without SSH access.

## Brand Commitments
- Bahasa Indonesia as the primary interface language.
- Clean, responsive, accessible design usable on both smartphones (QR scanning/borrowing) and desktop workstations (admin management).

## Evidence on Hand
- Complete Laravel project structure in `C:\laragon\www\system_peminjaman_lab`.
- Database migrations and initial SQL schema (`db_peminjaman_lab.sql`).
- Controllers and routes handling inventory, loans, users, news, maintenance, and QR code verification.

## Product Principles
1. **Speed & Efficiency**: Instant QR scanning and streamlined loan requests for zero delay during lab sessions.
2. **Accountability & Lifecycle Visibility**: Clear per-unit condition history from acquisition through maintenance to loan return.
3. **Role-Tailored Simplicity**: Simple catalog browsing for students/teachers alongside powerful controls for lab staff.
4. **Resilient Access**: Works seamlessly across mobile devices at lab benches and desktop administrative setups.

## Accessibility & Inclusion
Responsive web layout with high visual clarity, accessible contrast ratios, clear Bahasa Indonesia microcopy, and touch-friendly controls for mobile scanning.
