CREATE TABLE api_tokens (
    id VARCHAR(32) PRIMARY KEY,
    merchant_id VARCHAR(32) NOT NULL,
    token_hash VARCHAR(64) UNIQUE NOT NULL,
    expires_at TIMESTAMP NOT NULL,
    created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,

    CONSTRAINT api_tokens_merchant_id_foreign
        FOREIGN KEY (merchant_id)
        REFERENCES merchants(id)
        ON DELETE CASCADE
);

CREATE INDEX api_tokens_merchant_id_index
    ON api_tokens(merchant_id);