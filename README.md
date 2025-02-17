# Smart Meal
<img src='./SmartMeal-optimize.gif' title='Video Walkthrough' width='' alt='Video Walkthrough' />

Welcome to the Smart Meal Site project! This web application helps users find, rate, and manage recipes based on their preferences and pantry ingredients. It offers a simple interface to search for meals, view meal details and ratings, and manage user account settings.

## Project Overview

- **User Authentication:**  
  Users can sign up, log in, and log out. Sessions are managed to provide a personalized experience.

- **Dashboard:**  
  After logging in, users access a dashboard that displays past recipes, settings, and personalized recommendations.

- **Meal Search and Ratings:**  
  Users can search for meals based on criteria such as meal type, keyword, calories, pantry ingredients, and allergens. They can also view popular meals and rate recipes.

- **Pantry and Allergens:**  
  Users can add or remove ingredients in their pantry, and modify allergen preferences to tailor recipe recommendations.

- **Settings and History:**  
  Update personal details, change passwords, and view a history of viewed meals.

## File Structure

- **`home.php`** - Main landing page that acts as the hub for navigation.  
- **`dashboard.php`** - User dashboard displaying personalized content and navigation links.  
- **`phongUtil.php`** - Contains functions for user login, sign up, pantry management, and recipe recommendations.  
- **`benUtil.php`** - Implements meal search forms, popular meal listings, and search functionalities.  
- **`rakoanUtil.php`** - Handles user settings, password changes, viewing meal history, and ratings.  
- **`db_connect.php`** - Database connection details for the MySQL server.  
- **`navBar.php`** - Navigation bar integrated into multiple pages for seamless navigation.  
- **`styles.css`** - Stylesheet providing the visual design for the website.  
- **`schema.sql`** - SQL schema to set up the database tables and structure for the project.

## How to Run

1. Import the database schema (`schema.sql`) into your MySQL server.
2. Adjust the database connection parameters in [db_connect.php](db_connect.php).
3. Deploy the PHP files on a suitable web server (e.g., Apache, Nginx with PHP-FPM).
4. Navigate to `home.php` to start using the application.

## Technologies Used

- **PHP:** Backend scripting for server-side logic.
- **MySQL:** Database management using PDO for secure connections.
- **HTML & CSS:** Frontend design and layout.
- **JavaScript:** Simple client-side scripting for form validations.

## Credits
- **Andry Rakotonjanabelo:** Developed user settings, history, and meal rating functions in [rakoanUtil.php](rakoanUtil.php).
- **Philip Pham:** Authored login, sign-up, and recipe recommendation functionalities in [phongUtil.php](phongUtil.php).  
- **Ben (and Phong):** Implemented search functionalities and popular meal features in [benUtil.php](benUtil.php).  


We hope you enjoy using and contributing to the Smart Meal Site project!
