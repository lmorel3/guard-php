CREATE TABLE IF NOT EXISTS users(
  username text NOT NULL UNIQUE,
  password text NOT NULL,
  role     text NOT NULL
);