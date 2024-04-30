function showSearchBar(searchType) {
  // Hide all search bars
  var searchBars = document.querySelectorAll(".searchBar");
  searchBars.forEach(function (bar) {
    bar.style.display = "none";
  });

  // Show the selected search bar
  var searchBarId = searchType + "Search";
  document.getElementById(searchBarId).style.display = "block";

  // Move the "search by..." button to the right
  var dropbtn = document.querySelector(".dropbtn");
  dropbtn.classList.add("searchActive");

  // Hide the dropdown menu
  var dropdownContent = document.querySelector(".dropdown-content");
  dropdownContent.classList.remove("show"); // hide dropdown

  dropdownContent.style.top = "calc(3% + 50px)";
  dropdownContent.style.left = "500px";
}

const dropbtn = document.querySelector(".dropbtn");
const dropdownContent = document.querySelector(".dropdown-content");

dropbtn.addEventListener("click", function () {
  dropdownContent.classList.toggle("show");
});

function searcRecipe() {
  var recipeName = document.getElementById("recipeNameInput").value;

  // Make a POST request to your PHP page
  fetch("../php/search-by-recipe.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/x-www-form-urlencoded",
    },
    body: "recipeName=" + encodeURIComponent(recipeName),
  })
    .then((response) => response.text())
    .then((data) => {
      // Update the searchResult div with the response from the PHP page
      document.getElementById("searchResult").innerHTML = data;
    })
    .catch((error) => {
      // Handle errors here
      console.error("Error:", error);
    });
}

function searchTag() {
  var tagName = document
    .getElementById("tagSearch")
    .querySelector("input").value;
  // Call PHP function for searching recipes by tag with tagName parameter
}

function searchCuisine() {
  var cuisineName = document
    .getElementById("cuisineSearch")
    .querySelector("input").value;
  // Call PHP function for searching recipes by cuisine with cuisineName parameter
}

function searchChef() {
  var chefName = document
    .getElementById("chefSearch")
    .querySelector("input").value;
  // Call PHP function for searching recipes by chef with chefName parameter
}
