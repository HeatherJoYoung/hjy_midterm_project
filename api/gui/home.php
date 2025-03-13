<?php 
	// Purpose: This file is the home page of the website. It allows the user to search for quotes by author, category, or randomly. The user can also view all quotes in the database.
?>

<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Midterm Project</title>
	<style><?php include 'gui/style.css' ?></style>
</head>

<body>
	<main>
		<header>
			<h3>Midterm Project</h3>
			<p class="sub-title">2025S_INF653_VB</p>
			<p>Heather Jo Young</p>
		</header>
		<div class="content">
			<div id="description">
				<h3>Description</h3>
				<p>This API returns information from a database of quotes. There are three endpoints against which a client can make GET, POST, PUT, and DELETE requests. The endpoints are 'authors', 'categories' and 'quotes'. </p>
				<p>For the 'quotes' endpoint, the client can GET all quotes, GET a quote by ID, POST a new quote, PUT a quote by ID, and DELETE a quote by ID. The client can also GET all quotes filtered by author and/or category or request a random quote with or without author and category filters.</p>
				<p>Below is a form that performs GET requests for quotes and displays the results.</p>
			</div>
			<div id="form-container">
				<form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]);?>" method="GET">
					<h3>Find a Quote</h3>
					<div class="filters">
						<label>Filter by:</label>
						<select name="author" id="authorSelect">
							<option value="" selected>All Authors</option>
							<?php 
								$query = 'SELECT * FROM authors ORDER BY author';
								$statement = $db->prepare($query);
								$statement->execute();
								$authors = $statement->fetchAll(PDO::FETCH_ASSOC);
								$statement->closeCursor();
								foreach ($authors as $author) {
									echo "<option value={$author['id']}>{$author['author']}</option>";
								}
							?>
						</select>
						<select name="category" id="categorySelect">
							<option value="" selected>All Categories</option>
							<?php 
								$query = 'SELECT * FROM categories ORDER BY category';
								$statement = $db->prepare($query);
								$statement->execute();
								$categories = $statement->fetchAll(PDO::FETCH_ASSOC);
								$statement->closeCursor();
								foreach ($categories as $category) {
									echo "<option value='{$category['id']}'>{$category['category']}</option>";
								}
							?>
						</select>
						<label for="random">Random</label>
						<input type="checkbox" name="random" id="random">
					</div>
				</form>
				<button type="submit" id="submit-button">Submit Query</button>
			</div>
			<div id="results">
				<div id="table-container">
					<table>
						<thead>
							<tr>
								<th class="quote-cell">Quote</th>
								<th class= "category-cell">Category</th>
								<th class="author-cell">Author</th>
							</tr>
						</thead>
						<tbody id="table-body">
						</tbody>
					</table>
				</div>
			</div>
		</div>
	</main>
	
  <script type="text/javascript" src="./gui/home.js"></script> 
</body>

</html>