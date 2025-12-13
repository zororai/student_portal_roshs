-- Clear existing fee types first
TRUNCATE TABLE fee_types;

-- Core / Mandatory Fees
INSERT INTO fee_types (name, description, is_active, created_at, updated_at) VALUES
('Tuition Fee', 'Core / Mandatory Fees - Tuition Fee', 1, NOW(), NOW()),
('Levy (SDC / Development Levy)', 'Core / Mandatory Fees - School Development Committee Levy', 1, NOW(), NOW()),
('Examination Fee', 'Core / Mandatory Fees - Examination Fee', 1, NOW(), NOW()),
('Capital / Building Fee', 'Core / Mandatory Fees - Capital / Building Fee', 1, NOW(), NOW());

-- Academic & Learning Fees
INSERT INTO fee_types (name, description, is_active, created_at, updated_at) VALUES
('Practical Subjects Fee', 'Academic & Learning Fees - Practical Subjects Fee', 1, NOW(), NOW()),
('ICT / Computer Fee', 'Academic & Learning Fees - ICT / Computer Fee', 1, NOW(), NOW()),
('Library Fee', 'Academic & Learning Fees - Library Fee', 1, NOW(), NOW());

-- Sports & Activities
INSERT INTO fee_types (name, description, is_active, created_at, updated_at) VALUES
('Sports Fee', 'Sports & Activities - Sports Fee', 1, NOW(), NOW()),
('Clubs & Societies Fee', 'Sports & Activities - Clubs & Societies Fee', 1, NOW(), NOW());

-- Boarding Fees (if applicable)
INSERT INTO fee_types (name, description, is_active, created_at, updated_at) VALUES
('Boarding Fee', 'Boarding Fees - Boarding Fee', 1, NOW(), NOW()),
('Boarding Maintenance Fee', 'Boarding Fees - Boarding Maintenance Fee', 1, NOW(), NOW()),
('Laundry Fee', 'Boarding Fees - Laundry Fee', 1, NOW(), NOW());

-- Transport & Services
INSERT INTO fee_types (name, description, is_active, created_at, updated_at) VALUES
('Transport Fee', 'Transport & Services - Transport Fee', 1, NOW(), NOW()),
('Boarding Meals Fee', 'Transport & Services - Boarding Meals Fee', 1, NOW(), NOW());

-- Government & External Fees
INSERT INTO fee_types (name, description, is_active, created_at, updated_at) VALUES
('ZIMSEC Examination Fee', 'Government & External Fees - ZIMSEC Examination Fee', 1, NOW(), NOW()),
('Ministry Registration / Approval Fee', 'Government & External Fees - Ministry Registration / Approval Fee', 1, NOW(), NOW());

-- Optional / Pay-as-You-Use Fees
INSERT INTO fee_types (name, description, is_active, created_at, updated_at) VALUES
('Uniform Fee', 'Optional / Pay-as-You-Use Fees - Uniform Fee', 1, NOW(), NOW()),
('Textbook Fee', 'Optional / Pay-as-You-Use Fees - Textbook Fee', 1, NOW(), NOW()),
('Extra Lessons / Tutorials Fee', 'Optional / Pay-as-You-Use Fees - Extra Lessons / Tutorials Fee', 1, NOW(), NOW()),
('School Trip / Educational Tour Fee', 'Optional / Pay-as-You-Use Fees - School Trip / Educational Tour Fee', 1, NOW(), NOW());

-- Penalties & Adjustments
INSERT INTO fee_types (name, description, is_active, created_at, updated_at) VALUES
('Late Payment Penalty', 'Penalties & Adjustments - Late Payment Penalty', 1, NOW(), NOW()),
('Damage / Breakage Fee', 'Penalties & Adjustments - Damage / Breakage Fee', 1, NOW(), NOW()),
('Refund / Credit Adjustment', 'Penalties & Adjustments - Refund / Credit Adjustment', 1, NOW(), NOW());
