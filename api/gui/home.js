// Purpose: This file is responsible for the functionality of the home page. It will make a request to the QuoteController to get a list of quotes based on the user's input. It will then populate the table with the results.

let results;

function getParams () {
	let author = document.getElementById('authorSelect').value;
	let category = document.getElementById('categorySelect').value;
	let random = document.getElementById('random').checked;
	let params = new URLSearchParams();

	console.log('random: ', random);

	if (author) {
		params.append('author_id', author);
	}

	if (category) {
		params.append('category_id', category);
	}

	if (random) {
		params.append('random', random);
	}

	console.log('params: ', params.toString());
	return params.toString();
}

function createRow(quote) {

	let row = document.createElement('tr');
	let quoteCell = document.createElement('td');
	let categoryCell = document.createElement('td');
	let authorCell = document.createElement('td');

	quoteCell.innerText = quote.quote;
	quoteCell.className = "quote-cell";
	categoryCell.innerText = quote.category;
	categoryCell.className = "category-cell";
	authorCell.innerText = quote.author;
	authorCell.className = "author-cell";

	row.appendChild(quoteCell);
	row.appendChild(categoryCell);
	row.appendChild(authorCell);

	return row;
}

function populateResults(results) {

	let tableBody = document.getElementById('table-body');
	tableBody.innerHTML = '';

	results.forEach(quote => {
		let row = createRow(quote);
		tableBody.appendChild(row);
	});

	const count = results.length;

	if (count < 10) {
		const diff = 10 - count;

		for (let i = 0; i < diff; i++) {
			let row = createRow({quote: '', category: '', author: ''});
			tableBody.appendChild(row);
		}
	}
}

document.getElementById('submit-button').addEventListener('click', async function() {
	let params = getParams();
	await fetch('controllers/QuoteController.php/?' + params, {
		method: 'GET',
		headers: {
			'Content-Type': 'application/json'
		}
	}).then(response => {
		if (!response.ok) {
			throw new Error('There was a problem with the request');
		}
		return response.json();
	}).then( result => {
		populateResults(result);
	});
});	
