CREATE TABLE mucrm_market_characters (
    id UNIQUEIDENTIFIER NOT NULL DEFAULT NEWID() PRIMARY KEY,
    username VARCHAR(10) NOT NULL,
    character_name VARCHAR(10) NOT NULL,
    price INT NOT NULL DEFAULT 0,
    created_at DATETIME DEFAULT GETDATE(),
    updated_at DATETIME DEFAULT GETDATE()
);

CREATE UNIQUE INDEX idx_mucrm_market_char_id ON mucrm_market_characters(id);
CREATE INDEX idx_mucrm_market_char_name ON mucrm_market_characters(character_name);
CREATE INDEX idx_mucrm_market_username ON mucrm_market_characters(username);