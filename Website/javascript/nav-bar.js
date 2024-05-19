var dropbtn = document.querySelector(".dropbtn");

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

  dropbtn.classList.add("searchActive");

  // Hide the dropdown menu
  var dropdownContent = document.querySelector(".dropdown-content");
  dropdownContent.classList.remove("show"); // hide dropdown

  dropdownContent.style.top = "calc(3% + 50px)";
  dropdownContent.style.left = "500px";
}

dropbtn.addEventListener("click", function () {
  var dropdownContent = document.querySelector(".dropdown-content");
  var dropbtnRect = dropbtn.getBoundingClientRect();
  var dropdownContentRect = dropdownContent.getBoundingClientRect();

  var newLeft =
    dropbtnRect.left - dropdownContentRect.width + dropbtnRect.width;

  dropdownContent.style.left = newLeft + "px";
});

const dropdownContent = document.querySelector(".dropdown-content");

dropbtn.addEventListener("click", function () {
  dropdownContent.classList.toggle("show");
});

function searchRecipe() {
  var recipeName = document.getElementById("recipeNameInput").value;

  fetch("../php/search-by-recipe.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/x-www-form-urlencoded",
    },
    body: "recipeName=" + encodeURIComponent(recipeName),
  })
    .then((response) => response.text())
    .then((data) => {
      var parentContainer = document.querySelector(".second-row-favourites");
      parentContainer.innerHTML = data;
    })
    .catch((error) => {
      console.error("Error:", error);
    });
}
function searchCuisine() {
  var cuisineName = document.getElementById("CuisineNameInput").value;

  fetch("../php/get_popular_cuisines.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/x-www-form-urlencoded",
    },
    body: "cuisine=" + encodeURIComponent(cuisineName),
  })
    .then((response) => response.text())
    .then((data) => {
      var parentContainer = document.querySelector(".second-row-favourites");
      parentContainer.innerHTML = data;
    })
    .catch((error) => {
      console.error("Error:", error);
    });
}

function searchTag() {
  var tagName = document.getElementById("TagNameInput").value;

  fetch("../php/search_by_tags.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/x-www-form-urlencoded",
    },
    body: "tag=" + encodeURIComponent(tagName),
  })
    .then((response) => response.text())
    .then((data) => {
      var parentContainer = document.querySelector(".second-row-favourites");
      parentContainer.innerHTML = data;
    })
    .catch((error) => {
      console.error("Error:", error);
    });
}

function searchChef() {
  var chefName = document.getElementById("ChefNameInput").value;

  fetch("../php/search_by_chef.php", {
    method: "POST",
    headers: {
      "Content-Type": "application/x-www-form-urlencoded",
    },
    body: "chef=" + encodeURIComponent(chefName),
  })
    .then((response) => response.text())
    .then((data) => {
      var parentContainer = document.querySelector(".second-row-favourites");
      parentContainer.innerHTML = data;
      attachClickEvent(); // Attach the click event after loading the new data
    })
    .catch((error) => {
      console.error("Error:", error);
    });
}

function attachClickEvent() {
  $(".usernamelink").click(function (e) {
    e.preventDefault();
    var username = $(this).data("username");

    $.ajax({
      type: "POST",
      url: "../php/check-user-profile.php",
      data: { username: username },
      success: function (response) {
        if (typeof response === "string") {
          response = JSON.parse(response);
        }
        if (response.status === "match") {
          window.location.href = "profile-page.php";
        } else if (response.status === "no-match") {
          window.location.href =
            "view-user-profile.php?username=" +
            encodeURIComponent(response.username);
        } else {
          alert(response.message || "An unknown error occurred.");
        }
      },
      error: function () {
        alert("An error occurred while processing your request.");
      },
    });
  });
}

$(document).ready(function () {
  attachClickEvent(); // Attach the click event on page load for any existing elements
});
