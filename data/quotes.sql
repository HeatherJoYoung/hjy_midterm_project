CREATE TABLE quotes (
    id SERIAL PRIMARY KEY,
    quote VARCHAR(255) NOT NULL,
    author_id INT NOT NULL REFERENCES authors(id),
    category_id INT NOT NULL REFERENCES categories(id)
);

SELECT * FROM quotes;