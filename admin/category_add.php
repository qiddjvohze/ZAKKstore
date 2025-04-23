<?php
    include 'includes/session.php';
    // Assuming pdo connection setup is in session.php or a separate db connect file included by session.php
    // If not, you might need: include 'includes/conn.php'; // Or wherever $pdo is initialized

    // The createSlug function is NO LONGER NEEDED for this requirement,
    // so it has been removed or can be commented out/deleted.
    /*
    function createSlug($str, $delimiter = '-') {
        // ... function code ...
    }
    */

    if(isset($_POST['add'])){
        $name = $_POST['name']; // Keep original variable name

        // --- Set cat_slug to be EXACTLY the same as name ---
        $cat_slug = $name; // Assign the raw name to cat_slug

        $conn = $pdo->open(); // Open database connection

        // --- Check if category NAME already exists ---
        // Since name and cat_slug are identical, checking name is sufficient
        // if name has a UNIQUE constraint. If cat_slug also had a separate
        // UNIQUE constraint, it would be redundant but harmless here.
        $stmt_check_name = $conn->prepare("SELECT COUNT(*) AS numrows FROM category WHERE name=:name");
        $stmt_check_name->execute(['name'=>$name]);
        $row_name = $stmt_check_name->fetch();

        if($row_name['numrows'] > 0){
            $_SESSION['error'] = 'Category name already exists';
        }
        else{
            // --- Try inserting the new category with identical name and slug ---
            try{
                // The INSERT statement still includes both columns
                $stmt_insert = $conn->prepare("INSERT INTO category (name, cat_slug) VALUES (:name, :cat_slug)");
                // Execute with both parameters, where :cat_slug value is identical to :name
                $stmt_insert->execute(['name'=>$name, 'cat_slug'=>$cat_slug]);
                $_SESSION['success'] = 'Category added successfully';
            }
            catch(PDOException $e){
                // This will catch database errors (e.g., if 'name' has a UNIQUE constraint violated)
                $_SESSION['error'] = "Database Error: " . $e->getMessage();
            }
        }

        $pdo->close(); // Close database connection
    }
    else{
        $_SESSION['error'] = 'Fill up category form first';
    }

    // Redirect back to the category page
    header('location: category.php');
    exit(); // Ensure script stops execution after redirect

?>