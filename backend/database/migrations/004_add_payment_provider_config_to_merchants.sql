ALTER TABLE merchants
ADD COLUMN payment_provider_config JSONB NOT NULL DEFAULT '{}';