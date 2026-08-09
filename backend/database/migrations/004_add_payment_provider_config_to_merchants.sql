ALTER TABLE merchants
ADD COLUMN payment_provider_config VARCHAR(2000) NOT NULL DEFAULT '{}';