DROP TABLE authors;

CREATE TABLE authors(
    id SERIAL PRIMARY KEY,
    author VARCHAR(40) NOT NULL
);

SELECT * FROM authors;