-- Active: 1746616556453@@127.0.0.1@3306@smp_bina_informatika
-- Fix Payment Status Values
-- SMP Bina Informatika
-- This script updates existing payment status values to match the new schema

USE smp_bina_informatika;

-- Update payment status values to match the new ENUM
-- Convert any invalid status values to 'unpaid'

UPDATE students 
SET payment_status = 'unpaid' 
WHERE payment_status NOT IN ('unpaid', 'pending', 'paid', 'verified', 'confirmed', 'rejected');

-- Set default payment status for students without payment proof
UPDATE students 
SET payment_status = 'unpaid' 
WHERE payment_status IS NULL;

-- Update students with payment proof but no payment status to 'pending'
UPDATE students 
SET payment_status = 'pending' 
WHERE payment_proof IS NOT NULL AND payment_status = 'unpaid';

-- Log the changes
INSERT INTO activity_logs (action, description, ip_address, user_agent) 
VALUES ('payment_status_fix', 'Payment status values updated to match new schema', 'SYSTEM', 'Migration Script');

-- Show summary of current payment statuses
SELECT 
    payment_status,
    COUNT(*) as count
FROM students 
GROUP BY payment_status 
ORDER BY payment_status; 