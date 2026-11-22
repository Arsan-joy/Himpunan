-- Migration 005: scope structural tables by kabinet and seed structural data.

ALTER TABLE divisions ADD COLUMN IF NOT EXISTS kabinet_id INT DEFAULT NULL;
ALTER TABLE leaders ADD COLUMN IF NOT EXISTS kabinet_id INT DEFAULT NULL;
ALTER TABLE organization_leaders ADD COLUMN IF NOT EXISTS kabinet_id INT DEFAULT NULL;
ALTER TABLE programs ADD COLUMN IF NOT EXISTS kabinet_id INT DEFAULT NULL;

SET @has_divisions_kabinet_fk = (
  SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS
  WHERE CONSTRAINT_SCHEMA = DATABASE() AND TABLE_NAME = 'divisions'
    AND CONSTRAINT_TYPE = 'FOREIGN KEY' AND CONSTRAINT_NAME = 'fk_divisions_kabinet'
);
SET @sql = IF(@has_divisions_kabinet_fk = 0,
  'ALTER TABLE divisions ADD CONSTRAINT fk_divisions_kabinet FOREIGN KEY (kabinet_id) REFERENCES kabinet(id) ON DELETE SET NULL ON UPDATE CASCADE',
  'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @has_leaders_kabinet_fk = (
  SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS
  WHERE CONSTRAINT_SCHEMA = DATABASE() AND TABLE_NAME = 'leaders'
    AND CONSTRAINT_TYPE = 'FOREIGN KEY' AND CONSTRAINT_NAME = 'fk_leaders_kabinet'
);
SET @sql = IF(@has_leaders_kabinet_fk = 0,
  'ALTER TABLE leaders ADD CONSTRAINT fk_leaders_kabinet FOREIGN KEY (kabinet_id) REFERENCES kabinet(id) ON DELETE SET NULL ON UPDATE CASCADE',
  'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @has_org_kabinet_fk = (
  SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS
  WHERE CONSTRAINT_SCHEMA = DATABASE() AND TABLE_NAME = 'organization_leaders'
    AND CONSTRAINT_TYPE = 'FOREIGN KEY' AND CONSTRAINT_NAME = 'fk_org_leaders_kabinet'
);
SET @sql = IF(@has_org_kabinet_fk = 0,
  'ALTER TABLE organization_leaders ADD CONSTRAINT fk_org_leaders_kabinet FOREIGN KEY (kabinet_id) REFERENCES kabinet(id) ON DELETE SET NULL ON UPDATE CASCADE',
  'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

SET @has_programs_kabinet_fk = (
  SELECT COUNT(*) FROM information_schema.TABLE_CONSTRAINTS
  WHERE CONSTRAINT_SCHEMA = DATABASE() AND TABLE_NAME = 'programs'
    AND CONSTRAINT_TYPE = 'FOREIGN KEY' AND CONSTRAINT_NAME = 'fk_programs_kabinet'
);
SET @sql = IF(@has_programs_kabinet_fk = 0,
  'ALTER TABLE programs ADD CONSTRAINT fk_programs_kabinet FOREIGN KEY (kabinet_id) REFERENCES kabinet(id) ON DELETE SET NULL ON UPDATE CASCADE',
  'SELECT 1');
PREPARE stmt FROM @sql;
EXECUTE stmt;
DEALLOCATE PREPARE stmt;

UPDATE kabinet SET period = '2026/2027' WHERE name = 'METANOIA';

INSERT INTO departments (slug, name)
SELECT 'sekretaris-umum', 'Sekretaris Umum'
WHERE NOT EXISTS (SELECT 1 FROM departments WHERE slug = 'sekretaris-umum');
INSERT INTO departments (slug, name)
SELECT 'bendahara-umum', 'Bendahara Umum'
WHERE NOT EXISTS (SELECT 1 FROM departments WHERE slug = 'bendahara-umum');
INSERT INTO departments (slug, name)
SELECT 'kewirausahaan', 'Kewirausahaan'
WHERE NOT EXISTS (SELECT 1 FROM departments WHERE slug = 'kewirausahaan');
INSERT INTO departments (slug, name)
SELECT 'manajemen-mutu-anggota', 'Manajemen Mutu Anggota'
WHERE NOT EXISTS (SELECT 1 FROM departments WHERE slug = 'manajemen-mutu-anggota');
INSERT INTO departments (slug, name)
SELECT 'big-data', 'Big Data'
WHERE NOT EXISTS (SELECT 1 FROM departments WHERE slug = 'big-data');

-- Backfill the known roster rows without deleting or overwriting unrelated data.
UPDATE organization_leaders
SET kabinet_id = (SELECT id FROM kabinet WHERE name = 'METANOIA' ORDER BY id DESC LIMIT 1)
WHERE LOWER(TRIM(name)) = LOWER('Abdurrahman Zidan Rizki');

UPDATE organization_leaders
SET kabinet_id = (SELECT id FROM kabinet WHERE name = 'Samagri' ORDER BY id ASC LIMIT 1)
WHERE LOWER(TRIM(name)) = LOWER('Johan Hezekiel Marolop Ambarita');

-- Seed the verified Metanoia chair if missing; all other roster names remain untouched.
INSERT INTO organization_leaders (kabinet_id, name, role, sort_order)
SELECT k.id, 'Abdurrahman Zidan Rizki', 'Ketua Himpunan', 1
FROM kabinet k
WHERE k.name = 'METANOIA'
  AND NOT EXISTS (
    SELECT 1 FROM organization_leaders o
    WHERE o.kabinet_id = k.id AND LOWER(TRIM(o.name)) = LOWER('Abdurrahman Zidan Rizki')
  );

-- Seed only the cabinet-specific divisions identified in the structural source.
INSERT INTO divisions (department_id, kabinet_id, name, slug)
SELECT d.id, k.id, 'PIM', 'pim'
FROM departments d JOIN kabinet k ON k.name = 'METANOIA'
WHERE LOWER(d.slug) IN ('keilmuan', 'kaderisasi')
  AND NOT EXISTS (
    SELECT 1 FROM divisions v
    WHERE v.department_id = d.id AND v.kabinet_id = k.id AND v.slug = 'pim'
  )
LIMIT 1;

INSERT INTO divisions (department_id, kabinet_id, name, slug)
SELECT d.id, k.id, 'Perlombaan', 'perlombaan'
FROM departments d JOIN kabinet k ON k.name = 'METANOIA'
WHERE LOWER(d.slug) IN ('keilmuan', 'kaderisasi')
  AND NOT EXISTS (
    SELECT 1 FROM divisions v
    WHERE v.department_id = d.id AND v.kabinet_id = k.id AND v.slug = 'perlombaan'
  )
LIMIT 1;

INSERT INTO divisions (department_id, kabinet_id, name, slug)
SELECT d.id, k.id, 'Publikasi dan Dokumentasi', 'publikasi-dokumentasi'
FROM departments d JOIN kabinet k ON k.name = 'Samagri'
WHERE LOWER(d.slug) IN ('medkominfo', 'medkom', 'media-komunikasi')
  AND NOT EXISTS (
    SELECT 1 FROM divisions v
    WHERE v.department_id = d.id AND v.kabinet_id = k.id AND v.slug = 'publikasi-dokumentasi'
  )
LIMIT 1;

INSERT INTO divisions (department_id, kabinet_id, name, slug)
SELECT d.id, k.id, 'Content Creator', 'content-creator'
FROM departments d JOIN kabinet k ON k.name = 'Samagri'
WHERE LOWER(d.slug) IN ('medkominfo', 'medkom', 'media-komunikasi')
  AND NOT EXISTS (
    SELECT 1 FROM divisions v
    WHERE v.department_id = d.id AND v.kabinet_id = k.id AND v.slug = 'content-creator'
  )
LIMIT 1;

INSERT INTO divisions (department_id, kabinet_id, name, slug)
SELECT d.id, k.id, 'Desain', 'desain'
FROM departments d JOIN kabinet k ON k.name = 'Samagri'
WHERE LOWER(d.slug) IN ('medkominfo', 'medkom', 'media-komunikasi')
  AND NOT EXISTS (
    SELECT 1 FROM divisions v
    WHERE v.department_id = d.id AND v.kabinet_id = k.id AND v.slug = 'desain'
  )
LIMIT 1;
