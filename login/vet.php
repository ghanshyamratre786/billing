<?php
   include('adminsession.php');
   $page_title = 'Veterinarian';
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

if(isset($_GET['vet_id'])){
  $keyvalue = $_GET['vet_id'];
}else{
  $keyvalue = 0;
}

if(isset($_POST['submit']))
{
  $vet_name = $_POST['vet_name'];
  $vet_designation = $_POST['vet_designation'];
  $vet_desc = $_POST['vet_desc'];
  $vet_logo = $_FILES['vet_logo'];

    
  if($keyvalue==0)
  {
      $images = $vet_logo['name'];
      $tm="DOC";
      $tm.=microtime(true)*1000;
      $ext = pathinfo($images, PATHINFO_EXTENSION);
      $images=$tm.".".$ext;
      move_uploaded_file($vet_logo['tmp_name'],"uploaded/".$images);
      
      mysqli_query($conn,"INSERT INTO veterinarian SET vet_name='$vet_name',vet_designation='$vet_designation',vet_desc='$vet_desc',vet_logo='$images',createdate='$createdate',ipaddress='$ipaddress',loginid='$loginid'");
      $action =1;
  }else
  {
      mysqli_query($conn,"UPDATE veterinarian SET vet_name='$vet_name',vet_designation='$vet_designation',vet_desc='$vet_desc',createdate='$createdate',ipaddress='$ipaddress',loginid='$loginid' where vet_id='$keyvalue'");

      if($gallary_img['name']!=''){
        $prev_img = mysqli_query($conn,"SELECT vet_logo FROM veterinarian WHERE vet_id='$keyvalue'");
            $prev_img = mysqli_fetch_assoc($prev_img);
            $prev_img = $prev_img['vet_logo'];
            if($prev_img != ''){
                unlink("uploaded/".$prev_img);
            }
        $images = $vet_logo['name'];
        $tm="DOC";
        $tm.=microtime(true)*1000;
        $ext = pathinfo($images, PATHINFO_EXTENSION);
        $images=$tm.".".$ext;
        move_uploaded_file($vet_logo['tmp_name'],"uploaded/".$images);
        mysqli_query($conn,"UPDATE veterinarian set vet_logo='$images' WHERE vet_id='$keyvalue'");
      }
      $action = 2;
  }
  echo "<script>location='vet?action=$action';</script>";
}
if($_GET['dvet_id']!=''){
    $prev_img = mysqli_query($conn,"SELECT vet_logo FROM veterinarian WHERE vet_id='$_GET[dvet_id]'");
        $prev_img = mysqli_fetch_assoc($prev_img);
        $prev_img = $prev_img['vet_logo'];
        if($prev_img != ''){
            unlink("uploaded/".$prev_img);
        }
    mysqli_query($conn,"DELETE  FROM veterinarian WHERE vet_id='$_GET[dvet_id]'");
    $action = 3;
    echo "<script>location='vet?action=$action';</script>";
  }
if($_GET['vet_id']!=''){
    $btn_name ='Update';
    $btn_color = 'success';
    $sql = mysqli_query($conn,"SELECT * FROM veterinarian WHERE vet_id='$_GET[vet_id]'");
    $rowedit = mysqli_fetch_array($sql);
    $vet_name = $rowedit['vet_name'];
    $vet_designation = $rowedit['vet_designation'];
    $vet_desc = $rowedit['vet_desc'];
    $vet_logo = $rowedit['vet_logo'];
   
  }else{
   $vet_name = "";
   $vet_designation = "";
   $vet_desc = "";
   $vet_logo = "";
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
         <h1>Veterinarian</h1>
         <nav>
            <ol class="breadcrumb">
               <li class="breadcrumb-item"><a href="dashboard">Home</a></li>
               <li class="breadcrumb-item ">Veterinarian</li>
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
                        Add Veterinarian
                     </h5>
                     <form action="" method='POST' enctype="multipart/form-data">
                        <div class="row md-3">
                            <input type="hidden" name='vet_id' value="<?php echo $keyvalue;?>" >
                            <h4 class="card-title ml-2">Name</h4>
                            <div class="col-sm-8">
                                <input type="text" name="vet_name" Placeholder="Name" class="form-control" value="<?php echo $vet_name; ?>">
                            </div>
                            <h4 class="card-title ml-2">Designation</h4>
                            <div class="col-sm-8">
                                <input type="text" name="vet_designation" Placeholder="Designation" class="form-control" value="<?php echo $vet_designation; ?>">
                            </div>
                            <h4 class="card-title ml-2">Description</h4>
                            <div class="col-sm-8">
                                <input type="text" name="vet_desc" Placeholder="Description" class="form-control" value="<?php echo $vet_desc; ?>">
                            </div>
                            <h4 class="card-title ml-2">Logo</h4>
                            <div class="col-sm-8 mt-3">
                                <input type="file" name="vet_logo" class="form-control">
                                <span><img src="uploaded/<?php echo $vet_logo; ?>" alt="" width="30px;"></span>
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
                        Show Veterinarian
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
                                    Designation
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
                                $sql = mysqli_query($conn,"SELECT * FROM veterinarian order by vet_id desc");
                                while($row = mysqli_fetch_array($sql)){
                                ?>
                                <tr>
                                    <td><?php echo $sn++;?></td>
                                    <td>
                                        <?php echo $row['vet_name']; ?>
                                    </td>
                                    <td>
                                    <?php echo $row['vet_designation']; ?>
                                    </td>
                                    <td>
                                    <?php echo $row['vet_desc']; ?>
                                    </td>
                                    <td>
                                        <img src="uploaded/<?php echo $row['vet_logo'];?>" alt="" width="100px;" height="auto">
                                    </td>
                                    <td>
                                    <a  href="vet?vet_id=<?php echo $row['vet_id']; ?>"  class="btn btn-success editbtn">Edit</a>
                                    <a href="vet?dvet_id=<?php echo $row['vet_id']; ?>"  class="btn btn-danger">Delete</a>
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