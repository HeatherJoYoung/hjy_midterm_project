# INF653 Midterm Project

This is a simple PHP REST API built from scratch with no framework. It includes a simple router that makes use of the Apache rewrite module. 

## Project Home Page

[Home Page](www.tbd.com)

## Documentation

If you want to clone this repo and run it locally as is, you need to be using an Apache server and you need to create an .htaccess file and add the following three lines which enable the Router to work.  

> RewriteEngine On<br>
> RewriteCond %{REQUEST_FILENAME}% !-f<br>
> RewriteRule ^(.*)$ index.php<br>

There are SQL statements available along with CSV files containing sample data in the Database directory, which you can use to set up a database.

If you are using a Postgres database, you can use the following CLI command to populata a table from a CSV file:

> psql -h \[database-host-name] -d \[database-name] -U \[username] -c "\copy \[tablename] (\[columns]) from 'path-to-csv-file' with delimiter as ','"


## App Info

The following endpoints are available.

### GET
| endpoint | description |
| -------- | ----------- |
| /quotes/ | GET all quotes |
| /quotes/?author_id=x | GET all quotes by an author |
| /quotes/?category_id=y | GET all quotes for a category |
| /quotes/?author_id=x&category_id=y | GET all quotes in a cateogory by an author |
| /authors/ | GET all authors |
| /authors/?id=x | GET author by id |
| /categories/ | GET all categories |
| /categories/?id=x | GET category by id |

### POST
| endpoint | description |
| -------- | ----------- |
| /quotes/ | create quote requires params (quote, author_id, category_id) |
| /authors/ | create author requires params (author) |
| /categories/ | create category requires params (category) |

### PUT
| endpoint | description |
| -------- | ----------- |
| /quotes/ | update quote requires params (id, quote, author_id, category_id) |
| /authors/ | update author requires params (id, author) |
| /categories/ | update category requires params (id, category) |

### DELETE
| endpoint | description |
| -------- | ----------- |
| /quotes/ | update quote requires params (id) |
| /authors/ | update author requires params (id) |
| /categories/ | update category requires params (id) |

### Author

Heather Jo Young
