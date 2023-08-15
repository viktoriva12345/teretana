<?php 
    require_once('config.php');
    echo "Admin Dashboard!";


    if(!isset($_SESSION['admin_id'])) {
        header('location: index.php');
        exit();
    }

?>

<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@4.3.1/dist/css/bootstrap.min.css" integrity="sha384-ggOyR0iXCbMQv3Xipma34MD+dH/1fQ784/j6cY/iJTQUOhcWr7x9JvoRxT2MZw1T" crossorigin="anonymous">
    <link rel="stylesheet" href="https://unpkg.com/dropzone@5/dist/min/dropzone.min.css" type="text/css" />
    <title>Admin Dashboard</title>
  </head>
  <body>

  <?php 
    if(isset($_SESSION['success_message'])) :
?>
    <div class="alert alert-success" role="alert">
        <?php  
            echo $_SESSION['success_message'];
            unset($_SESSION['success_message']);
        ?>
    </div>
<?php 
    endif; 
?>


    <div class="container">
        <div class="row">
            <div class="col-md-12">
                <h2>Members List</h2>

                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>First Name</th>
                            <th>Last Name</th>
                            <th>Email</th>
                            <th>Phone number</th>
                            <th>Trainer</th>
                            <th>Photo</th>
                            <th>Training Plan</th>
                            <th>Access Card</th>
                            <th>Creatred At</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php 
                            $sql = "SELECT members.* ,
                            training_plans.name AS training_plan_name,
                            trainers.first_name AS trainer_first_name,
                            trainers.last_name AS trainer_last_name
                            FROM `members` LEFT JOIN `training_plans` 
                            ON members.training_plan_id = training_plans.plan_id
                            LEFT JOIN `trainers` ON members.trainer_id = trainers.trainer_id;
                            ";
                            $run = $conn->query($sql);
                        
                            $results = $run->fetch_all(MYSQLI_ASSOC);
                            foreach ($results as $result) : ?>
                                    <tr>
                                        <td><?php echo $result['first_name']; ?></td>
                                        <td><?php echo $result['last_name']; ?></td>
                                        <td><?php echo $result['email']; ?></td>
                                        <td><?php echo $result['phone_number']; ?></td>
                                        <td><?php
                                            if($result['trainer_first_name']) {
                                                echo $result['trainer_first_name'] . " " . $result['trainer_last_name'];
                                            }else {
                                                echo "Nema dodjeljenog trenera";
                                            }
                                        
                                        ?></td>
                                        <td><img style="width:60px ;" src="<?php echo $result['photo_path']; ?>" /></td>
                                        <td><?php 
                                            if($result['training_plan_name']) {
                                                echo $result['training_plan_name'];
                                            }else {
                                                echo "Nema plana";
                                            }
                                        ?></td>
                                        <td><a target="blank" href="<?php echo $result['access_card_pdf_path']; ?>">ACCESS CARD</a></td>
                                        <td><?php 
                                        
                                        $created_at = strtotime($result['created_at']); 
                                        $new_date = date("d/m/y", $created_at);
                                        echo $new_date;
                                        ?></td>
                                       
                                        <td>
                                            <form action="delete_member.php" method="POST">
                                                <input type="hidden" name="member_id" value="<?php echo $result['member_id']; ?>"/>
                                                <button type="submit">DELETE</button> 
                                            </form> 
                                            
                                        </td>
                                    </tr>
                            <?php endforeach; ?>
                        
                    </tbody>
                </table>
            </div>
        </div>


    <div class="row mb-5">
            <div class="col-md-6">
                <h2>Register member</h2>
                <form action="register_member.php" method="post" enctype="multipart/form-data">
                    First Name: <input class="form-control" type="text" name="first_name"/>
                    <br />
                    Last Name: <input class="form-control" type="text" name="last_name"/>
                    <br />
                    Email: <input class="form-control" type="email" name="email"/>
                    <br />
                    Phone Number: <input class="form-control" type="text" name="phone_number"/>
                    <br />
                    Training Plan:
                    <select class="form-control" name="training_plan_id">
                        <option value="" disabled selected>Training plan</option>
                        
                        <?php
                            $sql = "SELECT * FROM training_plans"; 
                            $run = $conn->query($sql);
                            
                            while ($result = $run->fetch_assoc()) {
                                echo "<option value='" . $result['plan_id'] . "'>" . $result['name'] . "</option>";
                            }

                        ?>


                    </select><br />
                    <input type="hidden" name="photo_path" id="photoPathInput"/>

                    <div id="dropzone-upload" class="dropzone"></div>

                    <input class="btn btn-primary mt-3" type="submit" value="Register Member"/>
                </form>
            </div>

            <div class="col-md-6">
                <h2>Register trainer</h2>
                <form action="register_trainer.php" method="POST">
                    First Name : <input class="form-control" type="text" name="first_name"/><br />
                    Last Name : <input class="form-control" type="text" name="last_name"/><br />
                    Email : <input class="form-control" type="email" name="email"/><br />
                    Phone Number : <input class="form-control" type="text" name="phone_number"/><br />
                    <input class="btn btn-primary" type="submit" value="Register Trainer "/>
                </form>
            </div>
        </div>
    </div>
    <?php $conn->close();?>

    <script src="https://unpkg.com/dropzone@5/dist/min/dropzone.min.js"></script>
    <script>
        window.Dropzone.options.dropzoneUpload = {
            url: "upload_photo.php",
            paramName: "photo",
            maxFilesize: 20,
            acceptedFiles: "image/*",
            init: function() {
                this.on('success', function(file, response) {
                    const jsonResponse = JSON.parse(response);
                    if(jsonResponse.success) {
                        document.getElementById('photoPathInput').value=jsonResponse.photo_path;
                    }else {
                        console.error(jsonResponse.error);
                    }
                });
            }
        };
    </script>

</body>
</html>

