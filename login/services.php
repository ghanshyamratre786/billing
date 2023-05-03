<?php
   include('adminsession.php');
$page_title = 'Services';
   include('inc/head.php');
$btn_name = 'Submit';
$btn_color = 'primary';
if($_GET['action']==1){
$msg = "Data Has been Inserted Successfully";
}

if($_GET['action']==2){
$msg = "Data Has been Updated Successfully";
}

if($_GET['action']==3){
$msg = "Data Has been Deleted Successfully";
}

if(isset($_GET['ser_id'])){
  $keyvalue = $_GET['ser_id'];
}else{
  $keyvalue = 0;
}

if(isset($_POST['submit']))
{
  $ser_name = $_POST['ser_name'];
  $ser_desc = $_POST['ser_desc'];
  $ser_logo = $_FILES['ser_logo'];

    
  if($keyvalue==0)
  {
      $images = $ser_logo['name'];
      $tm="DOC";
      $tm.=microtime(true)*1000;
      $ext = pathinfo($images, PATHINFO_EXTENSION);
      $images=$tm.".".$ext;
      move_uploaded_file($ser_logo['tmp_name'],"uploaded/".$images);
      
      mysqli_query($conn,"INSERT INTO services SET ser_name='$ser_name',ser_desc='$ser_desc',ser_logo='$images',createdate='$createdate',ipaddress='$ipaddress',loginid='$loginid'");
      $action =1;
  }else
  {
      mysqli_query($conn,"UPDATE services SET ser_name='$ser_name',ser_desc='$ser_desc',createdate='$createdate',ipaddress='$ipaddress',loginid='$loginid' where ser_id='$keyvalue'");

      if($gallary_img['name']!=''){
        $prev_img = mysqli_query($conn,"SELECT ser_logo FROM services WHERE ser_id='$keyvalue'");
            $prev_img = mysqli_fetch_assoc($prev_img);
            $prev_img = $prev_img['ser_logo'];
            if($prev_img != ''){
                unlink("uploaded/".$prev_img);
            }
        $images = $ser_logo['name'];
        $tm="DOC";
        $tm.=microtime(true)*1000;
        $ext = pathinfo($images, PATHINFO_EXTENSION);
        $images=$tm.".".$ext;
        move_uploaded_file($ser_logo['tmp_name'],"uploaded/".$images);
        mysqli_query($conn,"UPDATE Services set ser_logo='$images' WHERE ser_id='$keyvalue'");
      }
      $action = 2;
  }
  echo "<script>location='services?action=$action';</script>";
}
if($_GET['dser_id']!=''){
    $prev_img = mysqli_query($conn,"SELECT ser_logo FROM services WHERE ser_id='$_GET[dser_id]'");
        $prev_img = mysqli_fetch_assoc($prev_img);
        $prev_img = $prev_img['ser_logo'];
        if($prev_img != ''){
            unlink("uploaded/".$prev_img);
        }
    mysqli_query($conn,"DELETE  FROM services WHERE ser_id='$_GET[dser_id]'");
    $action = 3;
    echo "<script>location='services?action=$action';</script>";
  }
if($_GET['ser_id']!=''){
    $btn_name ='Update';
    $btn_color = 'success';
    $sql = mysqli_query($conn,"SELECT * FROM services WHERE ser_id='$_GET[ser_id]'");
    $rowedit = mysqli_fetch_array($sql);
    $ser_name = $rowedit['ser_name'];
    $ser_desc = $rowedit['ser_desc'];
    $ser_logo = $rowedit['ser_logo'];
   
  }else{
   $ser_name = "";
   $ser_desc = "";
   $ser_logo = "";
  }
?>
<body>
   <!-- ======= Header ======= -->
   <?php 
      include('inc/header.php');
       ?>
   <!-- ======= Sidebar ======= -->
   <?php 
      include('inc/menu.php');
       ?>
   <main id="main" class="main">
      <div class="pagetitle">
         <h1>Services</h1>
         <nav>
            <ol class="breadcrumb">
               <li class="breadcrumb-item"><a href="dashboard">Home</a></li>
               <li class="breadcrumb-item ">Services</li>
            </ol>
         </nav>
      </div>
      <center> <span style="color:red;"><?php echo $msg; ?></span></center>
      <br>
      <!-- End Page Title -->
      <section class="section dashboard">
         <div class="row">
            <div class="col-lg-12">
               <div class="card">
                  <div class="card-body">
                     <h5 class="class card-title">
                        Add Services
                     </h5>
                     <form action="" method='POST' enctype="multipart/form-data">
                        <div class="row md-3">
                            <input type="hidden" name='ser_id' value="<?php echo $keyvalue;?>" >
                            <h4 class="card-title ml-2">Name</h4>
                            <div class="col-sm-8">
                                <input type="text" name="ser_name" Placeholder="Name" class="form-control" value="<?php echo $ser_name; ?>">
                            </div>
                            
                            <h4 class="card-title ml-2">Description</h4>
                            <div class="col-sm-8">
                                <input type="text" name="ser_desc" Placeholder="Description" class="form-control" value="<?php echo $ser_desc; ?>">
                            </div>
                            <h4 class="card-title ml-2">Logo</h4>
                            <div class="col-sm-8 mt-3">
                                <input type="file" name="ser_logo" class="form-control">
                                <span><img src="uploaded/<?php echo $ser_logo; ?>" alt="" width="30px;"></span>
                            </div>
                        </div>
                       <br>
                        <button type="submit" name="submit" class="btn btn-<?php echo $btn_color; ?>"><?php echo $btn_name; ?></button>
                     </form>
                  </div>
               </div>
            </div>
</div>
<div class="row">
            <div class="col-lg-12">
               <div class="card">
                  <div class="card-body">
                     <h5 class="class card-title">
                        Show Services
                     </h5>
                    <table class="table datatable">
                        <thead>
                            <tr>
                                <th scope="col">
                                S.No.
                                </th>
                                <th scope="col">
                                    Name
                                </th>
                               
                                <th scope="col">
                                    Description
                                </th>
                                <th scope="col">
                                    Logo
                                </th>
                                <th scope="col">
                                    Action
                                </th>
                            </tr>
                            </thead>
                            <tbody>
                                <?php
                                $sn=1;
                                $sql = mysqli_query($conn,"SELECT * FROM services order by ser_id desc");
                                while($row = mysqli_fetch_array($sql)){
                                ?>
                                <tr>
                                    <td><?php echo $sn++;?></td>
                                    <td>
                                        <?php echo $row['ser_name']; ?>
                                    </td>
                                   
                                    <td>
                                    <?php echo $row['ser_desc']; ?>
                                    </td>
                                    <td>
                                        <img src="uploaded/<?php echo $row['ser_logo'];?>" alt="" width="100px;" height="auto">
                                    </td>
                                    <td>
                                    <a  href="services?ser_id=<?php echo $row['ser_id']; ?>"  class="btn btn-success editbtn">Edit</a>
                                    <a href="services?dser_id=<?php echo $row['ser_id']; ?>"  class="btn btn-danger">Delete</a>
                                    </td>
                                </tr>
                                <?php }?>
                            </tbody>
                    </table>
                  </div>
               </div>
            </div>
         </div>
      </section>
   </main>
   <!-- End #main -->
   <!-- ======= Footer ======= -->
   <?php
      include('inc/footer.php');
       ?>