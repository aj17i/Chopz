<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recipe Page</title>
    <link rel="stylesheet" href="../css/recipe-view.css">
</head>

<body>
    <nav>
        <ul>
            <li><a href="#">Home</a></li>

            <li><a href="#">profile</a></li>
            <!-- Add more navigation links as needed -->
        </ul>
    </nav>

    <div class="container">
        <div class="recipe">
            <!-- Recipe content goes here -->
            <h1>Recipe Name</h1>
            <img src="recipe-image.jpg" alt="Recipe Image">
            <p>Ingredients:</p>
            <ul>
                <li>Ingredient 1</li>
                <li>Ingredient 2</li>
                <!-- Add more ingredients as needed -->
            </ul>
            <p>Instructions:</p>
            <ol>
                <li>Step 1</li>
                <li>Step 2</li>
                <!-- Add more steps as needed -->
            </ol>
        </div>

        <div class="creator-info">
            <h2>Creator Information</h2>
            <!-- Creator information and buttons -->
            <p>Creator Name</p>
            <button id="followBtn">Follow</button>
            <div class="rating">
                <!-- Dynamic star rating -->    
                <p>Rating:</p>
                <div class="stars">
                    <span class="star">&#9733;</span>
                    <span class="star">&#9733;</span>
                    <span class="star">&#9733;</span>
                    <span class="star">&#9733;</span>
                    <span class="star">&#9733;</span>
                </div>

            </div>
            <button id="saveBtn">Save</button>
            <!-- Comment section -->
            <div class="comments">
                <h3>Comments</h3>
                <div class="comment">
                    <p>User123: This recipe is amazing!</p>
                </div>
                <!-- Add more comments dynamically -->
            </div>
            <!-- Add a form for adding new comments if needed -->
        </div>
    </div>

    <script>
        // script.js

        document.getElementById('saveBtn').addEventListener('click', function () {
            this.classList.toggle('saved');
            if (this.classList.contains('saved')) {
                this.textContent = 'Saved';
            } else {
                this.textContent = 'Save';
            }
        });

        document.getElementById('followBtn').addEventListener('click', function () {
            this.classList.toggle('followed');
            if (this.classList.contains('followed')) {
                this.textContent = 'Following';
            } else {
                this.textContent = 'Follow';
            }
        });
        // script.js

        // Function to handle star rating
        function handleRatingClick(event) {
            if (event.target.classList.contains('star')) {
                const stars = document.querySelectorAll('.star');
                const clickedStarIndex = Array.from(stars).indexOf(event.target) + 1;

                // Highlight clicked star and unhighlight others
                stars.forEach((star, index) => {
                    if (index < clickedStarIndex) {
                        star.classList.add('rated');
                    } else {
                        star.classList.remove('rated');
                    }
                });
            }
        }

        // Event listener for rating stars
        document.querySelector('.stars').addEventListener('click', handleRatingClick);

    </script>
</body>

</html>