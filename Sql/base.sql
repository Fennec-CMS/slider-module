CREATE TABLE banners (
  title VARCHAR(128) NOT NULL,
  image VARCHAR(48) NOT NULL,
  startdate TIMESTAMP DEFAULT NOW() NOT NULL,
  enddate TIMESTAMP,
  position INT,
  url VARCHAR(255),
  id SERIAL
);
