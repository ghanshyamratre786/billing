<?php
   include('adminsession.php');
$page_title = 'Category';
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

if(isset($_GET['Category_id'])){
  $keyvalue = $_GET['Category_id'];
}else{
  $keyvalue = 0;
}

if(isset($_POST['submit']))
{
  $Category_name = $_POST['Category_name'];
  $Category_img = $_FILES['Category_img'];
    
  if($keyvalue==0)
  {
      $images = $Category_img['name'];
      $tm="DOC";
      $tm.=microtime(true)*1000;
      $ext = pathinfo($images, PATHINFO_EXTENSION);
      $images=$tm.".".$ext;
      move_uploaded_file($Category_img['tmp_name'],"uploaded/".$images);
      
      mysqli_query($conn,"INSERT INTO Category SET Category_img='$images',Category_name='$Category_name',createdate='$createdate',ipaddress='$ipaddress',loginid='$loginid'");
      $action =1;
  }else
  {
        mysqli_query($conn,"UPDATE Category set Category_name='$Category_name' WHERE Category_id='$keyvalue'");
      if($Category_img['name']!=''){
        $prev_img = mysqli_query($conn,"SELECT Category_img FROM Category WHERE Category_id='$keyvalue'");
            $prev_img = mysqli_fetch_assoc($prev_img);
            $prev_img = $prev_img['Category_img'];
            if($prev_img != ''){
                unlink("uploaded/".$prev_img);
            }
        $images = $Category_img['name'];
        $tm="DOC";
        $tm.=microtime(true)*1000;
        $ext = pathinfo($images, PATHINFO_EXTENSION);
        $images=$tm.".".$ext;
        move_uploaded_file($Category_img['tmp_name'],"uploaded/".$images);
        mysqli_query($conn,"UPDATE Category set Category_img='$images' WHERE Category_id='$keyvalue'");
      }
      $action = 2;
  }
  echo "<script>location='Category?action=$action';</script>";
}
if($_GET['dCategory_id']!=''){
    $prev_img = mysqli_query($conn,"SELECT Category_img FROM Category WHERE Category_id='$_GET[dCategory_id]'");
        $prev_img = mysqli_fetch_assoc($prev_img);
        $prev_img = $prev_img['Category_img'];
        if($prev_img != ''){
            unlink("uploaded/".$prev_img);
        }
    mysqli_query($conn,"DELETE  FROM Category WHERE Category_id='$_GET[dCategory_id]'");
    $action = 3;
    echo "<script>location='Category?action=$action';</script>";
  }
if($_GET['Category_id']!=''){
    $btn_name ='Update';
    $btn_color = 'success';
    $sql = mysqli_query($conn,"SELECT * FROM Category WHERE Category_id='$_GET[Category_id]'");
    $rowedit = mysqli_fetch_array($sql);
    $Category_img = $rowedit['Category_img'];
    $Category_name = $rowedit['Category_name'];
   
  }else{
    $Category_img = '';
    $Category_name = '';
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
         <h1>Category</h1>
         <nav>
            <ol class="breadcrumb">
               <li class="breadcrumb-item"><a href="dashboard">Home</a></li>
               <li class="breadcrumb-item ">Category</li>
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
                        Add Category
                     </h5>
                     <form action="" method='POST' enctype="multipart/form-data">
                        <div class="row md-3">
                            <input type="hidden" name='Category_id' value="<?php echo $keyvalue;?>" >
                            <h4 class="card-title ml-2">Category Name</h4>
                            <div class="col-sm-10">
                                <input type="text" name="Category_name" Placeholder="Name" class="form-control" value="<?php echo $Category_name; ?>">
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
                        Show Category
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
                                    Action
                                </th>
                            </tr>
                            </thead>
                            <tbody>
                                <?php
                                $sn=1;
                                $sql = mysqli_query($conn,"SELECT * FROM Category order by Category_id desc");
                                while($row = mysqli_fetch_array($sql)){
                                ?>
                                <tr>
                                    <td><?php echo $sn++;?></td>
                                    <td><?php echo $row['Category_name'];?></td>
                                    
                                    <td>
                                    <a  href="Category?Category_id=<?php echo $row['Category_id']; ?>"  class="btn btn-success editbtn">Edit</a>
                                    <a href="Category?dCategory_id=<?php echo $row['Category_id']; ?>"  class="btn btn-danger">Delete</a>
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