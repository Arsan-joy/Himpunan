-- Normalize legacy material categories before tightening the ENUM.
-- This migration is intentionally repeatable: only legacy values are updated,
-- and MODIFY COLUMN on the resulting 4-value ENUM is safe to run again.
UPDATE materials
SET category = 'kuliah'
WHERE category IN ('Semester_3', 'Semester_4', 'Semester_5', 'Semester_6', 'Semester_7', '');

ALTER TABLE materials
  MODIFY COLUMN category ENUM('kuliah', 'praktikum', 'laporan_kp', 'laporan_ta')
  NOT NULL DEFAULT 'kuliah';
