- Migration 005: pemisahan data struktural per kabinet

ALTER TABLE organization_leaders ADD COLUMN IF NOT EXISTS kabinet_id INT NULL;
ALTER TABLE leaders ADD COLUMN IF NOT EXISTS kabinet_id INT NULL;
ALTER TABLE programs ADD COLUMN IF NOT EXISTS kabinet_id INT NULL;
ALTER TABLE divisions ADD COLUMN IF NOT EXISTS kabinet_id INT NULL;

SET @sql = (SELECT IF(COUNT(*) > 0,
  'ALTER TABLE divisions DROP INDEX uniq_division_slug',
  'SELECT 1') FROM information_schema.statistics
  WHERE table_schema = DATABASE() AND table_name = 'divisions' AND index_name = 'uniq_division_slug');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql = (SELECT IF(COUNT(*) = 0,
  'ALTER TABLE divisions ADD UNIQUE INDEX uniq_division_kabinet_slug (department_id, kabinet_id, slug)',
  'SELECT 1') FROM information_schema.statistics
  WHERE table_schema = DATABASE() AND table_name = 'divisions' AND index_name = 'uniq_division_kabinet_slug');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql = (SELECT IF(COUNT(*) = 0,
  'ALTER TABLE organization_leaders ADD INDEX idx_organization_leaders_kabinet (kabinet_id)',
  'SELECT 1') FROM information_schema.statistics
  WHERE table_schema = DATABASE() AND table_name = 'organization_leaders' AND index_name = 'idx_organization_leaders_kabinet');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql = (SELECT IF(COUNT(*) = 0,
  'ALTER TABLE organization_leaders ADD CONSTRAINT fk_organization_leaders_kabinet FOREIGN KEY (kabinet_id) REFERENCES kabinet(id) ON DELETE SET NULL ON UPDATE CASCADE',
  'SELECT 1') FROM information_schema.table_constraints
  WHERE table_schema = DATABASE() AND table_name = 'organization_leaders' AND constraint_name = 'fk_organization_leaders_kabinet');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql = (SELECT IF(COUNT(*) = 0,
  'ALTER TABLE leaders ADD INDEX idx_leaders_kabinet (kabinet_id)',
  'SELECT 1') FROM information_schema.statistics
  WHERE table_schema = DATABASE() AND table_name = 'leaders' AND index_name = 'idx_leaders_kabinet');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql = (SELECT IF(COUNT(*) = 0,
  'ALTER TABLE leaders ADD CONSTRAINT fk_leaders_kabinet FOREIGN KEY (kabinet_id) REFERENCES kabinet(id) ON DELETE SET NULL ON UPDATE CASCADE',
  'SELECT 1') FROM information_schema.table_constraints
  WHERE table_schema = DATABASE() AND table_name = 'leaders' AND constraint_name = 'fk_leaders_kabinet');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql = (SELECT IF(COUNT(*) = 0,
  'ALTER TABLE programs ADD INDEX idx_programs_kabinet (kabinet_id)',
  'SELECT 1') FROM information_schema.statistics
  WHERE table_schema = DATABASE() AND table_name = 'programs' AND index_name = 'idx_programs_kabinet');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql = (SELECT IF(COUNT(*) = 0,
  'ALTER TABLE programs ADD CONSTRAINT fk_programs_kabinet FOREIGN KEY (kabinet_id) REFERENCES kabinet(id) ON DELETE SET NULL ON UPDATE CASCADE',
  'SELECT 1') FROM information_schema.table_constraints
  WHERE table_schema = DATABASE() AND table_name = 'programs' AND constraint_name = 'fk_programs_kabinet');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql = (SELECT IF(COUNT(*) = 0,
  'ALTER TABLE divisions ADD INDEX idx_divisions_kabinet (kabinet_id)',
  'SELECT 1') FROM information_schema.statistics
  WHERE table_schema = DATABASE() AND table_name = 'divisions' AND index_name = 'idx_divisions_kabinet');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

SET @sql = (SELECT IF(COUNT(*) = 0,
  'ALTER TABLE divisions ADD CONSTRAINT fk_divisions_kabinet FOREIGN KEY (kabinet_id) REFERENCES kabinet(id) ON DELETE SET NULL ON UPDATE CASCADE',
  'SELECT 1') FROM information_schema.table_constraints
  WHERE table_schema = DATABASE() AND table_name = 'divisions' AND constraint_name = 'fk_divisions_kabinet');
PREPARE stmt FROM @sql; EXECUTE stmt; DEALLOCATE PREPARE stmt;

UPDATE kabinet
SET period = '2026-2027'
WHERE name = 'METANOIA';

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

-- Pengurus inti Metanoia
UPDATE organization_leaders ol
JOIN kabinet k ON k.name = 'METANOIA'
SET ol.kabinet_id = k.id
WHERE ol.name IN (
  'Abdurrahman Zidan Rizki',
  'Rizqi Ramadhan Putra',
  'Aniskurlillah Putri Santoso',
  'Erga Wiradinati',
  'Aeryn Lidya Sihaloho',
  'Syakila Rahmadhina',
  'Zetro Pangihutan Sinaga',
  'Muhammad Sanjaya Ardy',
  'Fikri Muzzaki A'
);

INSERT INTO organization_leaders (kabinet_id, name, role, sort_order)
SELECT k.id, r.name, r.role, r.sort_order
FROM kabinet k
JOIN (
  SELECT 'Abdurrahman Zidan Rizki' name, 'Ketua Himpunan' role, 1 sort_order
  UNION ALL SELECT 'Rizqi Ramadhan Putra', 'Sekretaris Jenderal', 2
  UNION ALL SELECT 'Aniskurlillah Putri Santoso', 'Sekretaris 1', 3
  UNION ALL SELECT 'Erga Wiradinati', 'Sekretaris 2', 4
  UNION ALL SELECT 'Aeryn Lidya Sihaloho', 'Bendahara 1', 5
  UNION ALL SELECT 'Syakila Rahmadhina', 'Bendahara 2', 6
  UNION ALL SELECT 'Zetro Pangihutan Sinaga', 'Manajemen Mutu Anggota', 7
  UNION ALL SELECT 'Muhammad Sanjaya Ardy', 'Big Data', 8
  UNION ALL SELECT 'Fikri Muzzaki A', 'Kewirausahaan', 9
  UNION ALL SELECT 'Alessandro Delpiero', 'Senat', 10
  UNION ALL SELECT 'Adrian Reyvaldi', 'Senat', 11
  UNION ALL SELECT 'Adib Ramadhani', 'Senat', 12
  UNION ALL SELECT 'Helvran Angely', 'Senat', 13
  UNION ALL SELECT 'Jonathan Christofel Siburian', 'Senat', 14
  UNION ALL SELECT 'Kuntum Khaira Ummah', 'Senat', 15
  UNION ALL SELECT 'Johan Hezekiel Marolop Ambarita', 'Badan Penasihat Anggota (BPO)', 16
  UNION ALL SELECT 'Muhammad Wildan Arrasyidi', 'Badan Penasihat Anggota (BPO)', 17
) r
WHERE k.name = 'METANOIA'
  AND NOT EXISTS (
    SELECT 1 FROM organization_leaders ol
    WHERE ol.kabinet_id = k.id AND ol.name = r.name AND ol.role = r.role
  );

-- Leader departemen Metanoia
UPDATE leaders l
JOIN kabinet k ON k.name = 'METANOIA'
JOIN departments d ON d.id = l.department_id
SET l.kabinet_id = k.id
WHERE (l.name = 'Rafi Arya Antawah' AND d.slug = 'internal')
   OR (l.name = 'Marcha Evelynia' AND d.slug = 'internal')
   OR (l.name = 'Malka Ananda' AND d.slug = 'ekster')
   OR (l.name = 'Feriska Haura Meisalwa' AND d.slug = 'ekster')
   OR (l.name = 'Willy Alvares Lumban Gaol' AND d.slug = 'kaderisasi')
   OR (l.name = 'Meilani Cyntia' AND d.slug = 'kaderisasi')
   OR (l.name = 'Simon Petrus Hia' AND d.slug = 'psda')
   OR (l.name = 'Alya Faizah' AND d.slug = 'psda')
   OR (l.name = 'Allysia Arta Risqani Manurung' AND d.slug = 'medkominfo')
   OR (l.name = 'Nova Mei Rahayu' AND d.slug = 'medkominfo')
   OR (l.name = 'Vawaz Zuhri Wavi' AND d.slug = 'keilmuan')
   OR (l.name = "Nasywa Rihhadatul 'Aisy" AND d.slug = 'keilmuan');

INSERT INTO leaders (kabinet_id, department_id, name, role, sort_order)
SELECT k.id, d.id, r.name, r.role, r.sort_order
FROM kabinet k
JOIN departments d
JOIN (
  SELECT 'internal' slug, 'Rafi Arya Antawah' name, 'Kepala Departemen' role, 1 sort_order
  UNION ALL SELECT 'internal', 'Marcha Evelynia', 'Sekretaris Departemen', 2
  UNION ALL SELECT 'ekster', 'Malka Ananda', 'Kepala Departemen', 1
  UNION ALL SELECT 'ekster', 'Feriska Haura Meisalwa', 'Sekretaris Departemen', 2
  UNION ALL SELECT 'kaderisasi', 'Willy Alvares Lumban Gaol', 'Kepala Departemen', 1
  UNION ALL SELECT 'kaderisasi', 'Meilani Cyntia', 'Sekretaris Departemen', 2
  UNION ALL SELECT 'psda', 'Simon Petrus Hia', 'Kepala Departemen', 1
  UNION ALL SELECT 'psda', 'Alya Faizah', 'Sekretaris Departemen', 2
  UNION ALL SELECT 'medkominfo', 'Allysia Arta Risqani Manurung', 'Kepala Departemen', 1
  UNION ALL SELECT 'medkominfo', 'Nova Mei Rahayu', 'Sekretaris Departemen', 2
  UNION ALL SELECT 'keilmuan', 'Vawaz Zuhri Wavi', 'Kepala Departemen', 1
  UNION ALL SELECT 'keilmuan', "Nasywa Rihhadatul 'Aisy", 'Sekretaris Departemen', 2
  UNION ALL SELECT 'sekretaris-umum', 'Rizqi Ramadhan Putra', 'Kepala Departemen', 1
  UNION ALL SELECT 'bendahara-umum', 'Aeryn Lidya Sihaloho', 'Kepala Departemen', 1
  UNION ALL SELECT 'kewirausahaan', 'Fikri Muzzaki A', 'Kepala Departemen', 1
  UNION ALL SELECT 'manajemen-mutu-anggota', 'Zetro Pangihutan Sinaga', 'Kepala Departemen', 1
  UNION ALL SELECT 'big-data', 'Muhammad Sanjaya Ardy', 'Kepala Departemen', 1
) r ON r.slug = d.slug
WHERE k.name = 'METANOIA'
  AND NOT EXISTS (
    SELECT 1 FROM leaders l
    WHERE l.kabinet_id = k.id AND l.department_id = d.id
      AND l.name = r.name AND l.role = r.role
  );

-- Divisi Metanoia dan kepala divisi
UPDATE divisions
SET kabinet_id = (SELECT id FROM kabinet WHERE name = 'Samagri' ORDER BY id ASC LIMIT 1)
WHERE kabinet_id IS NULL;

INSERT INTO divisions (kabinet_id, department_id, name, slug, sort_order)
SELECT k.id, d.id, r.name, r.slug, r.sort_order
FROM kabinet k
JOIN departments d
JOIN (
  SELECT 'internal' dept_slug, 'Harmonisasi' name, 'harmonisasi' slug, 1 sort_order
  UNION ALL SELECT 'internal', 'Kesejahteraan', 'kesejahteraan', 2
  UNION ALL SELECT 'internal', 'Operasional', 'operasional', 3
  UNION ALL SELECT 'ekster', 'Intra Kampus', 'intra-kampus', 1
  UNION ALL SELECT 'ekster', 'Ekstra Kampus', 'ekstra-kampus', 2
  UNION ALL SELECT 'ekster', 'Pengabdian Masyarakat', 'pengabdian-masyarakat', 3
  UNION ALL SELECT 'kaderisasi', 'Eksekutor', 'eksekutor', 1
  UNION ALL SELECT 'kaderisasi', 'Konseptor', 'konseptor', 2
  UNION ALL SELECT 'psda', 'Pengembangan Potensi Olahraga', 'pengembangan-potensi-olahraga', 1
  UNION ALL SELECT 'psda', 'Minat dan Bakat', 'minat-dan-bakat', 2
  UNION ALL SELECT 'medkominfo', 'Pengembangan Informasi Media', 'pengembangan-informasi-media', 1
  UNION ALL SELECT 'medkominfo', 'Desain Grafis', 'desain-grafis', 2
  UNION ALL SELECT 'keilmuan', 'Akademik', 'akademik', 1
  UNION ALL SELECT 'keilmuan', 'Keprofesian', 'keprofesian', 2
  UNION ALL SELECT 'keilmuan', 'Kajian Strategis', 'kajian-strategis', 3
  UNION ALL SELECT 'keilmuan', 'Perlombaan', 'perlombaan', 4
) r ON r.dept_slug = d.slug
WHERE k.name = 'METANOIA'
  AND NOT EXISTS (
    SELECT 1 FROM divisions dv
    WHERE dv.department_id = d.id
      AND dv.kabinet_id = k.id
      AND dv.slug = r.slug
  );

INSERT INTO divisions (kabinet_id, department_id, name, slug, sort_order)
SELECT k.id, d.id, r.name, r.slug, r.sort_order
FROM kabinet k
JOIN departments d
JOIN (
  SELECT 'internal' dept_slug, 'Harmonisasi' name, 'harmonisasi' slug, 1 sort_order
  UNION ALL SELECT 'internal', 'Kesejahteraan', 'kesejahteraan', 2
  UNION ALL SELECT 'internal', 'Operasional', 'operasional', 3
  UNION ALL SELECT 'ekster', 'Intra Kampus', 'intra-kampus', 1
  UNION ALL SELECT 'ekster', 'Ekstra Kampus', 'ekstra-kampus', 2
  UNION ALL SELECT 'ekster', 'Pengabdian Masyarakat', 'pengabdian-masyarakat', 3
  UNION ALL SELECT 'kaderisasi', 'Eksekutor', 'eksekutor', 1
  UNION ALL SELECT 'kaderisasi', 'Konseptor', 'konseptor', 2
  UNION ALL SELECT 'psda', 'PPO', 'ppo', 1
  UNION ALL SELECT 'psda', 'Minat dan Bakat', 'minat-dan-bakat', 2
  UNION ALL SELECT 'medkominfo', 'Publikasi dan Dokumentasi', 'publikasi-dan-dokumentasi', 1
  UNION ALL SELECT 'medkominfo', 'Content Creator', 'content-creator', 2
  UNION ALL SELECT 'medkominfo', 'Desain', 'desain', 3
  UNION ALL SELECT 'keilmuan', 'Akademik', 'akademik', 1
  UNION ALL SELECT 'keilmuan', 'Keprofesian', 'keprofesian', 2
  UNION ALL SELECT 'keilmuan', 'Kajian Strategis dan Propaganda', 'kajian-strategis-dan-propaganda', 3
) r ON r.dept_slug = d.slug
WHERE k.name = 'SAMAGRI'
  AND NOT EXISTS (
    SELECT 1 FROM divisions dv
    WHERE dv.department_id = d.id
      AND dv.kabinet_id = k.id
      AND dv.slug = r.slug
  );

INSERT INTO division_members (division_id, name, role, sort_order)
SELECT dv.id, r.name, 'Kepala Divisi', r.sort_order
FROM divisions dv
JOIN kabinet k ON k.name = 'METANOIA'
JOIN (
  SELECT 'harmonisasi' division_slug, 'Tio Berta Br Sirait' name, 1 sort_order
  UNION ALL SELECT 'kesejahteraan', 'Silsi Ramadhani', 1
  UNION ALL SELECT 'operasional', 'Mario Siwomigo', 1
  UNION ALL SELECT 'intra-kampus', 'Hanif Nur Alfian', 1
  UNION ALL SELECT 'ekstra-kampus', 'M Devana Marlon Adirangga', 1
  UNION ALL SELECT 'pengabdian-masyarakat', 'Asti Pricillia Safitri', 1
  UNION ALL SELECT 'eksekutor', 'Dwy Hanna Manalu', 1
  UNION ALL SELECT 'konseptor', 'Masta Hendri', 1
  UNION ALL SELECT 'pengembangan-potensi-olahraga', 'Martin Delon Nainggolan', 1
  UNION ALL SELECT 'minat-dan-bakat', 'Rafli Nanang Firmansyah', 1
  UNION ALL SELECT 'pengembangan-informasi-media', 'Jihan Mutia A', 1
  UNION ALL SELECT 'desain-grafis', 'Yulia Mardiani Putri', 1
  UNION ALL SELECT 'akademik', 'Moses Benediktus Sitompul', 1
  UNION ALL SELECT 'keprofesian', 'Yosia Cendanawangi Putri Dwibiyanto', 1
  UNION ALL SELECT 'kajian-strategis', 'Virgoes Yehezkiel', 1
  UNION ALL SELECT 'perlombaan', 'Mhd Rasyid Asshiddiqie', 1
) r ON r.division_slug = dv.slug
WHERE dv.kabinet_id = k.id
  AND NOT EXISTS (
    SELECT 1 FROM division_members dm
    WHERE dm.division_id = dv.id
      AND dm.name = r.name
      AND dm.role = 'Kepala Divisi'
  );
