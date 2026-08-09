CREATE TABLE transactions (
    id VARCHAR(32) PRIMARY KEY,
    merchant_id VARCHAR(32) NOT NULL,
    payment_provider VARCHAR(50) NOT NULL,
    provider_transaction_id VARCHAR(255),
    amount_cents INTEGER NOT NULL,
    currency VARCHAR(3) NOT NULL DEFAULT 'EUR',
    description VARCHAR(255) NOT NULL,
    card_last_four VARCHAR(4) NOT NULL,
    status VARCHAR(20) NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT transactions_merchant_id_foreign
        FOREIGN KEY (merchant_id)
        REFERENCES merchants(id)
        ON DELETE CASCADE,

    CONSTRAINT transactions_amount_positive
        CHECK (amount_cents > 0)
);

CREATE INDEX transactions_merchant_id_index
    ON transactions(merchant_id);

CREATE INDEX transactions_created_at_index
    ON transactions(created_at);