CREATE TABLE categories(
    id SERIAL PRIMARY KEY,
    category VARCHAR(20) NOT NULL
);

INSERT INTO categories (category)
VALUES 
    ('Inspiration'),
    ('Humor'),
    ('Literature'),
    ('Movies'),
    ('Technology');

SELECT * FROM categories;