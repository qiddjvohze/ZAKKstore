<?php include 'includes/session.php'; ?>
<?php include 'includes/header.php'; ?>
<body class="hold-transition skin-blue sidebar-mini">
<div class="wrapper">

  <?php include 'includes/navbar.php'; ?>
  <?php include 'includes/menubar.php'; ?>

  <div class="content-wrapper">
    <section class="content-header">
      <h1>
        Category
      </h1>
      <ol class="breadcrumb">
        <li><a href="home.php"><i class="fa fa-dashboard"></i> Home</a></li>
        <li>Products</li>
        <li class="active">Category</li>
      </ol>
    </section>

    <section class="content">
      <?php
        // Display success or error messages from session
        if(isset($_SESSION['error'])){
          echo "
            <div class='alert alert-danger alert-dismissible'>
              <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>
              <h4><i class='icon fa fa-warning'></i> Error!</h4>
              ".$_SESSION['error']."
            </div>
          ";
          unset($_SESSION['error']); // Clear message after displaying
        }
        if(isset($_SESSION['success'])){
          echo "
            <div class='alert alert-success alert-dismissible'>
              <button type='button' class='close' data-dismiss='alert' aria-hidden='true'>&times;</button>
              <h4><i class='icon fa fa-check'></i> Success!</h4>
              ".$_SESSION['success']."
            </div>
          ";
          unset($_SESSION['success']); // Clear message after displaying
        }
      ?>
      <div class="row">
        <div class="col-xs-12">
          <div class="box">
            <div class="box-header with-border">
              <a href="#addnew" data-toggle="modal" class="btn btn-primary btn-sm btn-flat"><i class="fa fa-plus"></i> New</a>
            </div>
            <div class="box-body">
              <table id="example1" class="table table-bordered">
                <thead>
                  <th>Category Name</th>
                  <th>Category Slug</th> <th>Tools</th>
                </thead>
                <tbody>
                  <?php
                    $conn = $pdo->open(); // Open database connection

                    try{
                      // Select all columns, including the new cat_slug
                      $stmt = $conn->prepare("SELECT * FROM category");
                      $stmt->execute();
                      foreach($stmt as $row){ // Loop through each category row
                        echo "
                          <tr>
                            <td>".$row['name']."</td>
                            <td>".$row['cat_slug']."</td> <td>
                              <button class='btn btn-success btn-sm edit btn-flat' data-id='".$row['id']."'><i class='fa fa-edit'></i> Edit</button>
                              <button class='btn btn-danger btn-sm delete btn-flat' data-id='".$row['id']."'><i class='fa fa-trash'></i> Delete</button>
                            </td>
                          </tr>
                        ";
                      }
                    }
                    catch(PDOException $e){
                      // Display error if database fetch fails
                      echo "<tr><td colspan='3'>Database Error: " . $e->getMessage() . "</td></tr>";
                    }

                    $pdo->close(); // Close database connection
                  ?>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
    </section>

  </div>
    <?php include 'includes/footer.php'; ?>
    <?php include 'includes/category_modal.php'; // Contains modals for add/edit/delete ?>

</div>
<?php include 'includes/scripts.php'; ?>
<script>
// Keep existing JavaScript for Edit/Delete modals
$(function(){
  $(document).on('click', '.edit', function(e){
    e.preventDefault();
    $('#edit').modal('show');
    var id = $(this).data('id');
    getRow(id);
  });

  $(document).on('click', '.delete', function(e){
    e.preventDefault();
    $('#delete').modal('show');
    var id = $(this).data('id');
    getRow(id);
  });

});

// This function fetches data for edit/delete modals.
// IMPORTANT: It currently only fetches 'id' and 'name'.
// You will need to modify 'category_row.php' and this function
// if you want to display or edit the 'cat_slug' in the edit modal.
function getRow(id){
  $.ajax({
    type: 'POST',
    url: 'category_row.php', // The PHP script that fetches category data by ID
    data: {id:id},
    dataType: 'json',
    success: function(response){
      // Populate fields in the modals
      $('.catid').val(response.id);         // Hidden input for ID in modals
      $('#edit_name').val(response.name);   // Input field for name in edit modal
      // $('#edit_slug').val(response.cat_slug); // Add this line if you add a slug field to the edit modal
      $('.catname').html(response.name);    // Display name in delete modal confirmation
    },
    error: function(xhr, status, error) {
        // Optional: Add better error handling for the AJAX request
        console.error("AJAX Error: " + status + " - " + error);
        alert("Failed to fetch category details. Please check the console for errors.");
    }
  });
}
</script>
</body>
</html>