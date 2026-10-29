CREATE TABLE IF NOT EXISTS admin_action_usage (
    user_id INT UNSIGNED NOT NULL,
    page VARCHAR(60) NOT NULL,
    visits INT UNSIGNED NOT NULL DEFAULT 0,
    PRIMARY KEY (user_id, page),
    CONSTRAINT fk_admin_action_user FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
