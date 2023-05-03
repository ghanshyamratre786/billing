<?php
   include('adminsession.php');
$page_title = 'Gallary';
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

if(isset($_GET['gallary_id'])){
  $keyvalue = $_GET['gallary_id'];
}else{
  $keyvalue = 0;
}

if(isset($_POST['submit']))
{
  $gallary_name = $_POST['gallary_name'];
  $gallary_img = $_FILES['gallary_img'];
    
  if($keyvalue==0)
  {
      $images = $gallary_img['name'];
      $tm="DOC";
      $tm.=microtime(true)*1000;
      $ext = pathinfo($images, PATHINFO_EXTENSION);
      $images=$tm.".".$ext;
      move_uploaded_file($gallary_img['tmp_name'],"uploaded/".$images);
      
      mysqli_query($conn,"INSERT INTO gallary SET gallary_img='$images',gallary_name='$gallary_name',createdate='$createdate',ipaddress='$ipaddress',loginid='$loginid'");
      $action =1;
  }else
  {
        mysqli_query($conn,"UPDATE gallary set gallary_name='$gallary_name' WHERE gallary_id='$keyvalue'");
      if($gallary_img['name']!=''){
        $prev_img = mysqli_query($conn,"SELECT gallary_img FROM gallary WHERE gallary_id='$keyvalue'");
            $prev_img = mysqli_fetch_assoc($prev_img);
            $prev_img = $prev_img['gallary_img'];
            if($prev_img != ''){
                unlink("uploaded/".$prev_img);
            }
        $images = $gallary_img['name'];
        $tm="DOC";
        $tm.=microtime(true)*1000;
        $ext = pathinfo($images, PATHINFO_EXTENSION);
        $images=$tm.".".$ext;
        move_uploaded_file($gallary_img['tmp_name'],"uploaded/".$images);
        mysqli_query($conn,"UPDATE gallary set gallary_img='$images' WHERE gallary_id='$keyvalue'");
      }
      $action = 2;
  }
  echo "<script>location='gallary?action=$action';</script>";
}
if($_GET['dgallary_id']!=''){
    $prev_img = mysqli_query($conn,"SELECT gallary_img FROM gallary WHERE gallary_id='$_GET[dgallary_id]'");
        $prev_img = mysqli_fetch_assoc($prev_img);
        $prev_img = $prev_img['gallary_img'];
        if($prev_img != ''){
            unlink("uploaded/".$prev_img);
        }
    mysqli_query($conn,"DELETE  FROM gallary WHERE gallary_id='$_GET[dgallary_id]'");
    $action = 3;
    echo "<script>location='gallary?action=$action';</script>";
  }
if($_GET['gallary_id']!=''){
    $btn_name ='Update';
    $btn_color = 'success';
    $sql = mysqli_query($conn,"SELECT * FROM gallary WHERE gallary_id='$_GET[gallary_id]'");
    $rowedit = mysqli_fetch_array($sql);
    $gallary_img = $rowedit['gallary_img'];
    $gallary_name = $rowedit['gallary_name'];
   
  }else{
    $gallary_img = '';
    $gallary_name = '';
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
         <h1>Gallary</h1>
         <nav>
            <ol class="breadcrumb">
               <li class="breadcrumb-item"><a href="dashboard">Home</a></li>
               <li class="breadcrumb-item ">Gallary</li>
            </ol>
         </nav>
      </div>
      <center> <span style="color:red;"><?php echo $msg; ?></span></center>
      <br>
      <!-- End Page Title -->
      <section class="section dashboard">
         <div class="row">
            <div class="col-lg-6">
               <div class="card">
                  <div class="card-body">
                     <h5 class="class card-title">
                        Add Gallary
                     </h5>
                     <form action="" method='POST' enctype="multipart/form-data">
                        <div class="row md-3">
                            <input type="hidden" name='gallary_id' value="<?php echo $keyvalue;?>" >
                            <h4 class="card-title ml-2">Gallary Name</h4>
                            <div class="col-sm-10">
                                <input type="text" name="gallary_name" Placeholder="Name" class="form-control" value="<?php echo $gallary_name; ?>">
                            </div>
                            <h4 class="card-title ml-2">Gallary Image</h4>
                            <div class="col-sm-10">
                                <input type="file" name="gallary_img" class="form-control">
                                <span><img src="uploaded/<?php echo $gallary_img; ?>" alt="" width="30px;"></span>
                            </div>
                        </div>
                       <br>
                        <button type="submit" name="submit" class="btn btn-<?php echo $btn_color; ?>"><?php echo $btn_name; ?></button>
                     </form>
                  </div>
               </div>
            </div>
            <div class="col-lg-6">
               <div class="card">
                  <div class="card-body">
                     <h5 class="class card-title">
                        Show Gallary
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
                                    Image
                                </th>
                                <th scope="col">
                                    Action
                                </th>
                            </tr>
                            </thead>
                            <tbody>
                                <?php
                                $sn=1;
                                $sql = mysqli_query($conn,"SELECT * FROM gallary order by gallary_id desc");
                                while($row = mysqli_fetch_array($sql)){
                                ?>
                                <tr>
                                    <td><?php echo $sn++;?></td>
                                    <td><?php echo $row['gallary_name'];?></td>
                                    <td>
                                        <img src="uploaded/<?php echo $row['gallary_img'];?>" alt="" width="100px;" height="auto">
                                    </td>
                                    <td>
                                    <a  href="gallary?gallary_id=<?php echo $row['gallary_id']; ?>"  class="btn btn-success editbtn">Edit</a>
                                    <a href="gallary?dgallary_id=<?php echo $row['gallary_id']; ?>"  class="btn btn-danger">Delete</a>
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