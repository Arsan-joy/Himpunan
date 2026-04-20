-- Migration 002: Tambah index untuk optimasi query performa
-- Jalankan sekali saat deployment
-- Gunakan IF NOT EXISTS atau cek dulu sebelum ALTER

-- ============================================================
-- Tabel events
-- ============================================================
ALTER TABLE events
    ADD INDEX IF NOT EXISTS idx_start_date   (start_date),
    ADD INDEX IF NOT EXISTS idx_active_start (active, start_date);

-- ============================================================
-- Tabel members
-- ============================================================
ALTER TABLE members
    ADD INDEX IF NOT EXISTS idx_kabinet_id     (kabinet_id),
    ADD INDEX IF NOT EXISTS idx_department_id  (department_id),
    ADD INDEX IF NOT EXISTS idx_active         (active),
    ADD INDEX IF NOT EXISTS idx_active_kabinet (active, kabinet_id);
